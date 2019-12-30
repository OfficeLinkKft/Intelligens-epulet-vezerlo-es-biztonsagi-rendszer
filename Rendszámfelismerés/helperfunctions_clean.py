#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# Rendszámtábla felismerés alapja
# https://github.com/apoorva-dave/LicensePlateDetector/blob/master/

import imutils
from skimage.filters import threshold_otsu
from skimage import measure
from skimage.measure import regionprops
import numpy as np
import matplotlib.patches as patches
from skimage.transform import resize

# A minimálisan megtalálandó karakterek száma a rendszámban, hogy rendszámnak
# tekintsük
min_chars = 2

# Segédfüggvény, hogy felismerjük, ha két régió tartalmazás relációban van, 
# vagyis a nagyobb régió körbeöleli a kisebb régiót
def contained(bbox1, bboxes):
    contains = False
    y00, x00, y01, x01 = bbox1
    
    for i, bbox2 in enumerate(bboxes):
        y10, x10, y11, x11 = bbox2
        #print("bbox1", x00,y00,x01,y01)
        #print("bbox2", x10,y10,x11,y11)
        if (y00 >= y10 and x00 >= x10 and y01 <= y11 and x01 <= x11) and (y00 > y10 or x00 > x10 or y01 < y11 or x01 < x11):
            contains = True
    return contains

# Segédfüggvény
# Megadja a rendszámszerű objektumban található karakterek számát és azok befoglaló téglalap koordinátáit
# A karakterek is adott arányú téglalapok a rendszámtábla objektumon belül. Az arányokat a magyar 
# rendszámtábla karakterei alapján kerültek beállításra
# Ez a keresés logikailag megegyezik a rendszámtábla szerű objektum keresésével a teljes képre vonatkozóan, 
# ahol a teljes kép helyébe a korábban megtalált rendszámtábla szerű objektum lép, míg a rendszámtábla
# helyébe a rendszám egyes karakterei
def characters_in_lp(licence_plate_image, min_height_r=0.2, max_height_r=0.95, min_width_r=0.02, max_width_r=0.35, min_ratio=0, max_ratio=10):

    license_plate = np.invert(licence_plate_image.astype(np.int))

    labelled_plate = measure.label(license_plate)

    character_dimensions = (min_height_r*license_plate.shape[0], max_height_r*license_plate.shape[0], min_width_r*license_plate.shape[1], max_width_r*license_plate.shape[1])
    min_height, max_height, min_width, max_width = character_dimensions

    characters = []
    counter=0
    column_list = []
    borders = []
    areas = []
    heights = []
    filtered_characters = []
    filtered_borders = []
    bboxes = []
    filtered_bboxes = []
    filtered_characters2 = []
    filtered_borders2 = []
    
    for regions in regionprops(labelled_plate):
        y0, x0, y1, x1 = regions.bbox
        region_height = y1 - y0
        region_width = x1 - x0

        if region_height > min_height and region_height < max_height and region_width > min_width and region_width < max_width and region_height/region_width > min_ratio and region_height/region_width < max_ratio:

            bboxes.append(regions.bbox)
            areas.append(regions.area)
            heights.append(region_height)
            
            roi = license_plate[y0:y1, x0:x1] 

            rect_border = patches.Rectangle((x0, y0), x1 - x0, y1 - y0, edgecolor="red",
                                linewidth=2, fill=False)
            borders.append(rect_border)
            resized_char = resize(roi, (20, 20))
            characters.append(resized_char)

            # Karaktersoorend nyomonkövetésére
            column_list.append(x0)

    if heights:
        max_height = max(heights)
        # A megtalált karaktereken tapasztalati szűrést végzünk. A karaktereknek azonos magasságúnak kell 
        # lenniük, így a legnmagasabb karakter 50% magasságmérete alatt eldobjuk
        for i, character in enumerate(characters):
            if (heights[i] > max_height*0.5):
                filtered_characters.append(characters[i])
                filtered_borders.append(borders[i])
                filtered_bboxes.append(bboxes[i])

    # kiszűrjük a nagyobb téglalapokat, amelyek tartalmaznak más téglalapukat (karaktereket)            
    for i, bbox1 in enumerate(filtered_bboxes):
        if (not contained(bbox1, filtered_bboxes)):
            filtered_characters2.append(filtered_characters[i])
            filtered_borders2.append(filtered_borders[i])
            
    #print("Felismert karakterek száma: ", len(filtered_characters2))        
    return len(filtered_characters2), filtered_borders2

# Kulcsfüggvény
# Visszaadja a megtalált rendszám képét, illetve a megtalált rendszám képét, illetve a befoglaló téglalap koordinátáit
def find_LP_boxes(image, min_height_r=0.03, max_height_r=0.4, min_width_r=0.1, max_width_r=0.8, original_image=True):
    
    #print("A megadott kép mérete és alakja: ", image.size, image.shape)
    # A képet le kell skálázni, hogy hatékonyabb legyen a feldolgozás
    car_image = imutils.resize(image, width=500)
    

    
    gray_car_image = car_image * 255
    threshold_value = threshold_otsu(gray_car_image)
    binary_car_image = gray_car_image > threshold_value

    # bináris képek összekapcsolt területeinek megkeresése
    label_image = measure.label(binary_car_image)

    plate_dimensions = (min_height_r*label_image.shape[0], max_height_r*label_image.shape[0], min_width_r*label_image.shape[1], max_width_r*label_image.shape[1])
    min_height, max_height, min_width, max_width = plate_dimensions
    plate_objects_cordinates = []
    plate_like_objects = []
    plate_like_objects_orig_image = []
    identified_chars_nr = []
    identified_chars_nr_max = 0
    identified_chars_nr_max_idx = 0

    flag =0
    # regionprops a címkézett régiók tulajdonság listáit tartalmazza
    
    for region in regionprops(label_image):
    # print(region)
        if region.area < 50:
            # eldobjuk az egy minimális méret alatti régiókat
            continue
            # the bounding box coordinates
        min_row, min_col, max_row, max_col = region.bbox
        region_height = max_row - min_row
        region_width = max_col - min_col
        
        # A tipikus rendszámtábla arányoknak megfelelő objektumokat tartjuk meg
        if region_height >= min_height and region_height <= max_height and region_width >= min_width and region_width <= max_width and region_width > region_height:
            flag = 1
            lp_ci = car_image[min_row:max_row, min_col:max_col]
            lp_bci = binary_car_image[min_row:max_row, min_col:max_col]
            plate_like_objects.append(lp_bci)
            plate_like_objects_orig_image.append(lp_ci)
            
            # Felismeréshez top, right, bottom, left kell
            plate_objects_cordinates.append((min_row, min_col,
                                             max_row, max_col))
            #if original_image:
            #    identified_chars, rect_border = characters_in_lp(lp_ci)
            #else:
            #    identified_chars, rect_border = characters_in_lp(lp_bci)
            
            identified_chars, rect_border = characters_in_lp(lp_bci)
            
            identified_chars_nr.append(identified_chars)
            #print("Identified-chars-in-the process: ", identified_chars)
    
    if identified_chars_nr:
        identified_chars_nr_max = max(identified_chars_nr)
        identified_chars_nr_max_idx = identified_chars_nr.index(identified_chars_nr_max)
    # Ha nem találtunk rendszámszerű objektumot, vagy a megtalált karakterek maximális száma 0,
    # az azt jelenti, hogy nem találtunk rendszámot
    if len(plate_like_objects) == 0 or identified_chars_nr_max < min_chars: 
        #print("PLO len: ", len(plate_like_objects))
        #print("ICNM: ", identified_chars_nr_max)
        lp_image = "Nem találtunk rendszámot!"
        return (), image, 0
    
    if original_image:
        lp_image = plate_like_objects_orig_image[identified_chars_nr_max_idx]    
    else:
        lp_image = plate_like_objects[identified_chars_nr_max_idx]    
    
    coordinates = plate_objects_cordinates[identified_chars_nr_max_idx]
    #print("Megtalált karakterek maximális száma: ", identified_chars_nr_max)
    #print("Koordináták: ", plate_objects_cordinates)

    return coordinates, lp_image, identified_chars_nr_max


# Deszkamodellekben használt segédfüggvények
# A könnyen téveszthető számjegyek (számjegy-karakter)
szamjegy2karakter_dict = {'0':'O', '1':'I'}
# A könnyen téveszthető karakterek (karakter-számjegy)
karakter2szamjegy_dict = {'O':'0', 'I':'1', 'S':'5', 'Z':'2'}
# A könnyen téveszthető karakterek (karakter-karakter)
karakter2karakter_mappings = (('H', 'M'), ('H', 'W'), ('M', 'H'), ('M', 'W'), ('W', 'H'), ('W', 'M'))
# A karakter kontextus magyar rendszámok esetén az első három karakter
karakter_kontextus_rendszamban = (0,2)
# A szám kontextus magyar rendszámok esetén a 4-6 karakter
szamjegy_kontextus_rendszamban = (3,5)
# A rendszámokban alkalmazott számjegyek és karakterek
szamjegyek = ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9')
karakterek = ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y')

# Kontextus függő karakter korrekció
# Magyar rendszámok esetén az első három karakter karakter, míg az utolsó három
# karakter számjegy. Ez a rendszámok jelentős részére igaz. A speciális
# rendszámok kivételt képeznek ez alól.
def karakter_korrekcio(rendszam):
    
    korrigalt_rendszam = list(rendszam)
    if len(rendszam) == 7:
        szamjegy_kontextus_rendszamban = (4,6)
        
    for i in range(len(rendszam)):
        if is_karakterkontext(i) and not is_karakter(rendszam[i]):
            karakter = szamcsere_karakterre(rendszam[i])
            if karakter:
                korrigalt_rendszam[i] = karakter 
                
        if is_szamjegykontext(i) and not is_szamjegy(rendszam[i]):
            karakter = karaktercsere_szamjegyre(rendszam[i])
            if karakter:
                korrigalt_rendszam[i] = karakter                 
            
    return ''.join(korrigalt_rendszam)

# Számjegybők korrigál a hozzá hasonló karakterre    
def szamjegy2karakter(szamjegy):
    return szamjegy2karakter(szamjegy)

# Karakterből korrigál a hozzá hasonló számjegyre
def karakter2szamjegy(karakter):
    return karakter2szamjegy(karakter)

# Visszaadja, hogy jelenleg karakter kontextben van-e a karakter
def is_karakterkontext(index):
    alsoh, felsoh = karakter_kontextus_rendszamban
    
    if (index >= alsoh and index <= felsoh):
        return True
    else:
        return False
    
# Visszaadja, hogy jelenleg számjegy kontextben van-e a karakter    
def is_szamjegykontext(index):
    alsoh, felsoh = szamjegy_kontextus_rendszamban
    
    if (index >= alsoh and index <= felsoh):
        return True
    else:
        return False    

# Igazzal tér vissza, ha a paraméterül kapott karakter megtalálható a felismerés
# szempontjából elfogadott karakterek listájában
def is_karakter(karakter):
    if karakter in karakterek:
        return True
    else:
        return False

# Igazzal tér vissza, ha a paraméterül kapott karakter megtalálható a felismerés
# szempontjából elfogadott számjegyek listájában    
def is_szamjegy(karakter):
    if karakter in szamjegyek:
        return True
    else:
        return False

# Az adott számjegyet a hasonló karakterére cserél
def szamcsere_karakterre(szamjegy):
    return szamjegy2karakter_dict.get(szamjegy, False)

# Az adott karaktert a hasonló számjegyére cserél
def karaktercsere_szamjegyre(karakter):
    return karakter2szamjegy_dict.get(karakter, False)

# Előállítja az alternatív rendszámok listáját, ahol a hasonló karaktereket
# lecseréli a hasonlósági listában lévő karakterekre
def lehetseges_alternativ_rendszamok(rendszam):
    
    alternativ_rendszamok = []
    alternativ_rendszamok.append(list(rendszam))
    for i in range(len(rendszam)):
        print("Alternatív rendszámok a for ciklus elején:", alternativ_rendszamok)
        print("i: ", i)
        for rsz in alternativ_rendszamok.copy():
            print("    Rsz: ", rsz)
            for karakter, alternativ_karakter in karakter2karakter_mappings:
                print("        Karakter:{}, alternatív karakter: {}".format(karakter, alternativ_karakter))
                alternativ_rendszam = rsz.copy()
                if alternativ_rendszam[i] == karakter:
                    alternativ_rendszam[i] = alternativ_karakter
                    alternativ_rendszamok.append(alternativ_rendszam)
                    print("        Alternativ rendszamok: ", alternativ_rendszamok)

    return alternativ_rendszamok
            
# Segédfüggvény - Egy tömbben megadott rendszámból listát konvertál
def rendszamtombbol_rendszamlista(rendszamtomb):
     rendszamlista = []
     for rendszam in rendszamtomb:
         rendszamlista.append("".join(rendszam))
         
     return rendszamlista
