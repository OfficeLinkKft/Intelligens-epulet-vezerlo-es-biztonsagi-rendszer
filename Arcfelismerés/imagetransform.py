#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# Használat
#python imagetransform.py

## @package imagetransform
#  A modul alap képtranszformációkat tartalmaz.
#
#  Két egyszerű transzformációt definiálunk:
#  - eltolás
#  - forgatás.
 
# A szükséges importok
# A transzformáció az opencv könyvtár megfelelő metódusaira épít
import cv2
import os
import glob
import numpy as np
import argparse

# A parancsori paraméterek feldolgozása
ap = argparse.ArgumentParser()
ap.add_argument("-d", "--directory", type=str, default=".",
    help="A képeket tartalmazó könyvtár elérési útja")
ap.add_argument("-ss", "--shift_start", type=int, default=20,
    help="eltolás kezdőértéke")
ap.add_argument("-se", "--shift_end", type=int, default=100,
    help="eltolás végértéke")
ap.add_argument("-sd", "--shift_difference", type=int, default=20,
    help="eltolás léptetési értéke")
ap.add_argument("-rs", "--rotation_start", type=int, default=45,
    help="elforgatás kezdőértéke")
ap.add_argument("-re", "--rotation_end", type=int, default=360,
    help="elforgatás kezdőértéke")
ap.add_argument("-rd", "--rotation_difference", type=int, default=45,
    help="elforgatás léptetési értéke")
ap.add_argument("-sp", "--shift_prefix", type=str, default="Shifted",
    help="Az eltolással transzformált képek prefixe")
ap.add_argument("-rp", "--rotation_prefix", type=str, default="Rotated",
    help="Az elforgatással transzformált képek prefixe")
ap.add_argument("-m", "--max_size", type=int, default=500,
    help="a skálázás maximális mérete")
args = vars(ap.parse_args())

directory = args["directory"]
shift_start = args["shift_start"]
shift_end = args["shift_end"]
shift_difference = args["shift_difference"]
shift_prefix = args["shift_prefix"]
rotation_start = args["rotation_start"]
rotation_end = args["rotation_end"]
rotation_difference = args["rotation_difference"]
rotation_prefix = args["shift_prefix"]
max_size = args["max_size"]

## Parametrizálható eltolás
#
#  A paraméterként kapott képett vízszintesen és függőlegesen eltolja az adott paraméter szerint.
def shift_image(img, horizontal_shift, vertical_shit):
    rows,cols,colors = img.shape
    M = np.float32([[1,0,horizontal_shift],[0,1,vertical_shit]])
    dst = cv2.warpAffine(img,M,(cols,rows))
    
    return dst

## Parametrizálható elforgatás
#
#  A paraméterként kapott képett elforgatja az adott paraméter szerint.
def rotate_image(img, angle):
    rows,cols,colors = img.shape

    # cols-1 and rows-1 are the coordinate limits.
    M = cv2.getRotationMatrix2D(((cols-1)/2.0,(rows-1)/2.0),angle,1)
    dst = cv2.warpAffine(img,M,(cols,rows))
    
    return dst

## Parametrizálható átméretezés arányos skálázással
#
#  A paraméterként kapott képett arányosan átskálázza az adott paraméter szerint.
def resize_image_scale(img, scale):
    rows, cols, colors = img.shape
    dst = cv2.resize(img,(scale*rows, scale*cols), interpolation = cv2.INTER_AREA)
    
    return dst

## Parametrizálható aránytartó átméretezés maximális méret megadásával
#
#  A paraméterként kapott képett aránytartóan átskálázza a megadott maximális méretre.
def resize_image_maxdim(img, maxdim):
    rows, cols, colors = img.shape
    aspect_ratio = cols/rows
    if (rows < cols):
        dims = (maxdim, round(maxdim / aspect_ratio))
    else:
        dims = (round(maxdim * aspect_ratio), maxdim)
    dst = cv2.resize(img, dims, interpolation = cv2.INTER_AREA)
    
    return dst

# A képeket tartalmazó könyvtár megadása
#img_dir = "face-recognition-opencv/dataset/012.Tamas"
img_dir = directory
data_path = os.path.join(img_dir,'*')
files = glob.glob(data_path)
data = []
for f1 in files:
    print("File elérési út:\t{}".format(f1))
    img = cv2.imread(f1)
    print(f1.split('/'))
    # A könyvtár komponens mindig az utolsó előtti komponens
    directory = '/'.join(f1.split('/')[:-1])
    print("Directory: ", directory)
    # A filenév a struktúra függvénye, itt a könnyebség kedvéért be van drótozva [3]
    #file_name = f1.split('/')[3].split('.')[0]
    path_length = len(f1.split('/'))
    file_name = f1.split('/')[path_length-1].split('.')[0]
    print("Filenév: ", file_name)
    extension = f1.split('/')[path_length-1].split('.')[1]
    print("Kiterjesztés: ", extension)
    # Ha újra ráfuttatjuk egy már feldolgozott könyvtárra, akkor lánc transzformációt kaphatunk
    # ezért a saját hozzáfűzött nevet keressük és ha megtaláljuk, nem hajtjuk végre a transzformációt.
    if (file_name.find("H_" + shift_prefix + "_") != -1):
        break
    if (file_name.find("V" + shift_prefix + "_") != -1):
        break
    if (file_name.find(rotation_prefix + "_") != -1):
        break
    
    # Mivel adott esetben egy könyvtár sok képet is tartalmazhat, a biztoonság kevéért megszakíthatóvá
    # tesszük a feldolgozást (q lenyomásával ki lehet lépni)
    if cv2.waitKey(0) & 0xFF == ord('q'):
            break
    
    # A képet leskálázzuk, hogy a legnyagyobb mérete maximum 500 pixel legyen
    new_img = resize_image_maxdim(img, max_size)
    
    # A képből készítünk vízszintesen és függőlegesen adott léptékkel eltolt változatokat
    # Az eltolást bedrótoztuk, 20-100-as tartományban 20 pixelenként léptetünk
    for shift in range(shift_start,shift_end,shift_difference):
        #cv2.imshow('new_img', img)
        #if cv2.waitKey(0) & 0xFF == ord('q'):
        #    break
        h_shifted = shift_image(new_img, shift, 0)
        v_shifted = shift_image(new_img, 0, shift)
        #cv2.imshow('img', new_img)
        #if cv2.waitKey(0) & 0xFF == ord('q'):
        #    break
        cv2.imwrite(directory + "/" + "H_" + shift_prefix + "_" + str(shift) + "_" + file_name + "." + extension, h_shifted)        
        cv2.imwrite(directory + "/" + "V_" + shift_prefix + "_" + str(shift) + "_" + file_name + "." + extension, v_shifted)        

    # A képből készítünk adott szöggel elforgatott változatokat
    # Az elforgatást bedrótoztuk, 45-360-as tartományban 45 fokonként forgatunk        
    for rotate in range(rotation_start,rotation_end,rotation_difference):
        #cv2.imshow('new_img', img)
        #if cv2.waitKey(0) & 0xFF == ord('q'):
        #    break
        rotated = rotate_image(new_img, rotate)
        cv2.imwrite(directory + "/" + rotation_prefix + "_" + str(rotate) + "_" + file_name + "." + extension, rotated)        
        
    #cv2.destroyWindow('img')
    #print(directory + "/" + "Shifted_" + file_name + "." + extension)
    #cv2.imwrite(directory + "/" + "Shifted_" + file_name + "." + extension, img)