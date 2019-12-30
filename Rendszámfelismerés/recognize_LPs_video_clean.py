# Több rendszámfelismeréis modellt kiprobáltunk.
# Ez a megközelítés kombinálja a Jupyter notebookban végigvitt rendszámtábla felismerést, illetve a gépi tanulás
# területén ismert mély metrika tanulást (deep metric learning).
# A felismerést megvalósító kód az alábbi tutorialra épül:
# https://www.pyimagesearch.com/2018/06/18/face-recognition-with-opencv-python-and-deep-learning/
# A kód arcfelismrésre mutatja meg, hogyan használható hatékonyan a mély metrika tanulás.
# Az arcfelismerést lecseréltük rendszámtábla képre, illetve a felismerőt rendszámtábla képekkel tanítottuk
# A rendszer jól demonstrálja az algoritmus általánosíthatóságát és hatékonyságát. 

# Használat:
# python recognize_LPs_video_clean.py --output ./videowithLPs.avi --encodings encodingsLPs.pickle -d hog
# import the necessary packages
from imutils.video import VideoStream
import face_recognition
import argparse
import imutils
import pickle
import time
import cv2
from pathlib import Path
import urllib3
from bs4 import BeautifulSoup
import datetime


# A rendszámtábla felismerő függvények egy külön állományba kerültek
from helperfunctions_clean import find_LP_boxes
from helperfunctions_clean import karakter_korrekcio

from imutils import paths
from skimage.color import rgb2gray

import pytesseract
from fuzzywuzzy import process

http = urllib3.PoolManager()


# A paraméterek feldolgozásához létre kell hozni a megfelelő paraméter parszert
ap = argparse.ArgumentParser()

# A rendszámok feldolgozott encodingjait tartalmazó állomány
ap.add_argument("-e", "--encodings", required=True,
    help="A rendszámok szerializált adatbázisát tartalmazó állomány teljes elérési útja")

# A feldolgozott video kimenet
ap.add_argument("-o", "--output", type=str,
    help="A feldolgozott video teljes elérési útja")

# A feldogozás alatt megjelenjen-e a video a képernyőn
ap.add_argument("-y", "--display", type=int, default=1,
    help="A feldogozás alatt megjelenjen-e a video a képernyőn")


# Felismerési módszer
# A hog kevésbé erőforrásigényes és GPU nélküli környezetben is alkalmazható, míg a cnn pontosabb lehet, de 
# annak futtatásához GPU-ra van szükség
ap.add_argument("-d", "--detection-method", type=str, default="cnn",
    help="A használandó rendszámfelismerési módszer: `hog` vagy `cnn`")

# A megadott paraméterek begyűjtése
args = vars(ap.parse_args())

# Az ismert rendszámok és encodingjainak betöltése
print("[INFO - {}]\tEncodingok betöltése indul...".format(datetime.datetime.now()))
data = pickle.loads(open(args["encodings"], "rb").read())
print("[INFO - {}]\tEncodingok betöltése kész...".format(datetime.datetime.now()))

# Felismerési küszöb
# Ezen küszöb feletti találatokat tekintjük sikeres találatnak
recognition_threshold = 5
# A fel nem ismert rendszám megnevezése
unknown = "Ismeretlen"
lock_open = False
lock_file = Path("lock_file.txt")
blocking_period = 5
# Amennyiben szűkösek az erőforrások, minden n-ik képkockát dolgozzuk csak fel
# Az 1 érték azt jelenti, hogy mindegyiket feldolgozzuk
nth = 1

# A megtalÃ¡landÃ³ karakterek minimÃ¡lis szÃ¡ma a rendszÃ¡mban, ami felett sikeresnek Ã­tÃ©ljÃ¼k
# a megtalÃ¡lÃ¡st
min_chars = 3

frame_counter = 0
# A rendszerben regisztált rendszámok
registered_plates = ["MOJ-595", "XXX-123", "YYY-123", "MakettMOJ595"]

# Létrehozunk egy ablakot, amelyben megjelenítjük a feldolgozott video streamet
cv2.namedWindow('Output ablak', cv2.WINDOW_NORMAL)
# 600x600-as méretre állítjuk
cv2.resizeWindow('Output ablak', 600,600)

# Beengedendő rendszámok
# Bemenetként a függvény megkapja a felismert rendszámokat, és ellenőrzi, hogy
# benne van-e a regisztrált rendszámok listájában. Ha igen, visszaadja a beengedendő
# rendszámot
def authorized_car_recognised(lpns):
    print("[INFO - {}]\tEllenőrizendő rendszámok: {}".format(datetime.datetime.now(), lpns))
    car_list = [x for x in lpns if (x != unknown) and x in registered_plates]
    print("[INFO - {}]\tA beengedhető rendszámok: {}".format(datetime.datetime.now(), car_list))
    return car_list

# A beengedési esemény hatására végrehajtandó akció
def carry_out_action(authorized_car):
    print("------------------------------------------------------------------")
    print("[INFO - {}]\tBeengedési esemény".format(datetime.datetime.now()))
#    url="http://action_url?with_parameters"
#    response = http.request('GET', url)
#    soup = BeautifulSoup(response.data)

    print("[INFO - {}]\tGarázsajtó nyitás".format(datetime.datetime.now()))
    print("[INFO - {}]\tA {} rendszámú autó beengedésre került.".format(datetime.datetime.now(), authorized_car))    
    print("------------------------------------------------------------------")
    
    if (not(lock_file.exists())):
        lock_open = True
        # From transactional point of view this is not correct
        #print("Felismert rendszám: ", authorized_car[0])
        lock = open("lock_file.txt", "w") 
        lock.write(authorized_car[0]) 
        lock.close() 

# A video file és a kimeneti video író kezdeti pointereinek inicializálása
print("[INFO - {}]\tA video stream feldolgozása indul...".format(datetime.datetime.now()))
#vs = VideoStream(src=0).start() # Local camera
vs = VideoStream(src="http://user:pass@url:port/video.cgi").start()
print("[INFO - {}]\tA video stream objektum: {}".format(datetime.datetime.now(), vs))
writer = None
time.sleep(2.0)
print("[INFO - {}]\tA video stream él...".format(datetime.datetime.now()))

# A video stream képkockáin végig iterálunk
while True:
    # beolvassuk a következő képkockát
    frame = vs.read()
    frame_counter = frame_counter + 1

    # Amennyiben a rendelkezésre álló számítási kapacitás szűkös, lehetőségünk 
    # van rá, hogy csak minden nth képkockát dolgozzunk fel. Jelenleg megfelelő
    # erőforrás áll rendelkezésünkre, hogy minden egyes képkockát feldolgozzunk.
    if (frame_counter % nth == 0):
    
        # A bemenetet RGB-re alakítjuk (tanítás is RGB-ben volt), illetve
        # a hatékonyab feldolgozás érdekében leskálázzuk 500px szélességre
        rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        rgb = imutils.resize(frame, width= 500)
        rgb4recognition = imutils.resize(frame, width=500)
        
        #print(rgb.shape[1])
        # A képkocka szélessége alapján kiszámítjuk a skálázó faktort
        r = frame.shape[1] / float(rgb.shape[1])
        r = 500 / float(rgb.shape[1])
        image = rgb2gray(rgb)

        # A segédfüggvényünkkel megkeressük a lehetséges rendszámtábla
        # befoglaló téglalapjának a koordinátáit a képkockában, majd kiszámítjuk
        # rá az embeddingeket minden egyes rendszámtáblára 
        # A segédfüggvényünk visszaadja a megtalált rendszámtábla befoglaló 
        # téglalapjának a koordinátáit, magát a rendszámtábla képét, illetve
        # hogy hány rendszám karaktert talált a képben
        box, lp, found_chars = find_LP_boxes(image, min_height_r=0.03, max_height_r=0.4, min_width_r=0.1, max_width_r=0.8, original_image=True)
        boxes = list()
        boxes.append(box)
        
        print("")
        print("[INFO - {}]\tKépkocka feldolgozás - {}. képkocka...".format(datetime.datetime.now(), frame_counter))
        print("")
        print("[INFO - {}]\tA megtalált lehetséges rendszámtábla befoglaló koordinátái: {}".format(datetime.datetime.now(), boxes))
        if(boxes[0] and found_chars > min_chars):
            top, left, bottom, right = boxes[0]
            tes_top = top+min(bottom-top, 10)
            tes_bottom = max(tes_top, bottom-min(bottom-top,10))
            tes_left = left+min(right-left,10)
            tes_right = max(tes_left, right-min(right-left,10))
            # A tesseract ocr-rel felismertetjük a rendszámot
            print("[INFO - {}]\tTesseract (OCR) feldolgozás...".format(datetime.datetime.now()))
            lptext = pytesseract.image_to_string(rgb[tes_top:tes_bottom,tes_left:tes_right])
            print("[INFO - {}]\tTesseract (OCR) által talált rendszám: {}".format(datetime.datetime.now(), lptext))
            #cv2.imwrite("lp{}.jpg".format(frame_counter), rgb[top+10:bottom-10,left+10:right-10])
            #cv2.imwrite("lp{}.jpg".format(frame_counter), rgb[top:bottom,left:right])
            # Fuzzy illeszkedést számolunk a regisztrált rendszámokra
            Ratios = process.extract(lptext, registered_plates)        
            print("[INFO - {}]\tIlleszkedési % a regisztrált rendszámokra: ".format(datetime.datetime.now()))
            for plate, ratio in Ratios:
                print("[INFO - {}]\t\tRendszám: {}\t\tTalálati %: {}".format(datetime.datetime.now(), plate, ratio))
            # A könnyen téveszthető karaktereket korrigáljuk
            print("[INFO - {}]\tA korrigált rendszámok: {}".format(datetime.datetime.now(), karakter_korrekcio(lptext)))
#            encodings = face_recognition.face_encodings(rgb4recognition, boxes)
            # Ha találtunk rendszámtáblát és legalább 3 karaktert felismertünk,
            # akkor kiszámítjuk rá az embeddinget, hogy megnézzük, van-e
            # illeszkedés a betanított adatbázissal
            # Az arcfelismerő könyvtárat használjuk
            print("[INFO - {}]\tEmbedding feldolgozás...".format(datetime.datetime.now()))
            encodings = face_recognition.face_encodings(rgb4recognition, boxes)
            lpns = []
            
            # végig iterálunk a rendszám embeddingeken
            for encoding in encodings:
                # Megpróbáljuk illeszteni a képen lévő minden egyes rendszámtáblát
                # a betanított adatbázisban található rendszámokra
                matches = face_recognition.compare_faces(data["encodings"],
                    encoding, tolerance=0.35)
                lpn = unknown
                
                # check to see if we have found a match
                if True in matches:
                    # Megkeressük az összes megtalált rendszám indexét
                    # és inicializálunk egy dictionary-t, hogy megszámoljuk
                    # az összes találatot
                    matchedIdxs = [i for (i, b) in enumerate(matches) if b]
                    counts = {}
        
                    # Végigiterálunk a találati indexeken és frissítjük
                    # a találati számot minden egyes rendszámhoz
                    for i in matchedIdxs:
                        lpn = data["lpns"][i]
                        counts[lpn] = counts.get(lpn, 0) + 1
        
                    # Meghatározzuk a legnagyobb találati számmal rendelkező
                    # rendszámot (amelyik a legtöbb szavazatot kapta)
                    # Amennyiben több rendszám azonos szavazatot kap, akkor
                    # az első előfordulás kerül kiválasztásra
                    lpn = max(counts, key=counts.get)
                
                # Frissítjük a rendszámtáblák listáját - lpns (licence plate numbers)
                #print("Maximális találati szám: ", max(counts))
                lpns.append(lpn)
        
            if lpns:
                print("[INFO - {}]\tRendszámot találtunk: {}".format(datetime.datetime.now(), lpns))
            
            # Végigiterálunk a megtalált rendszámtáblákon
            for ((top, left, bottom, right), lpn) in zip(boxes, lpns):
                # Visszaskálázzuk a rendszámtábla koordinátáka
                top = int(top * r)
                right = int(right * r)
                bottom = int(bottom * r)
                left = int(left * r)
                
                #print("Berajzoljuk a téglalapot: ", top, right, bottom, left)
        
                # Berajzoljuk a prediktált rendszámtáblát és kiírjuk a rendszámot a képre
                cv2.rectangle(rgb, (left, top), (right, bottom),
                    (0, 255, 0), 2)
                y = top - 50 if top - 50 > 50 else top + 50
                # F: Felismert rendszám
                cv2.putText(rgb, "F: " + lpn, (left, y), cv2.FONT_HERSHEY_SIMPLEX,
                    0.75, (0, 255, 0), 2)
                y = top - 15 if top - 15 > 15 else top + 15
                # T: Tesseract rendszám
                cv2.putText(rgb, "T: " + lptext, (left, y), cv2.FONT_HERSHEY_SIMPLEX,
                    0.75, (0, 255, 0), 2)
                print("[INFO - {}]\tFelismert rendszám: {}".format(datetime.datetime.now(), lpn))
                print("[INFO - {}]\tTesseract felismert rendszám: {}".format(datetime.datetime.now(), lptext))
                
                authorized_car = authorized_car_recognised(lpns)
                print("[INFO - {}]\tAutorizált rendszám: {}".format(datetime.datetime.now(), authorized_car))
                if (authorized_car):
                    carry_out_action(authorized_car)
        
    
    # Amennyiben még nem inicializáltuk a video writert és kell video kimenetet
    # írnunk, inicializáljuk a video writert
    if writer is None and args["output"] is not None:
        fourcc = cv2.VideoWriter_fourcc(*"MJPG")
        writer = cv2.VideoWriter(args["output"], fourcc, 24,
            (rgb.shape[1], rgb.shape[0]), True)

    # Amennyiben a writer inicializálva van, kiírjuk a képkockát a video fileba
    # a megtalált bekeretezett rendszámtáblával és fölötte a megtalált rendszámmal
    if writer is not None:
        writer.write(rgb)

    # Amennyiben meg kell jelenítenünk a video streamet a képernyőn, akkor
    # kiírjuk az aktuális képkockát a megnyitott Output ablak-ba
    if args["display"] > 0:
        cv2.namedWindow('Output ablak',cv2.WINDOW_NORMAL)
        cv2.resizeWindow('Output ablak', 500, 500)
        cv2.imshow("Output ablak", rgb)
        key = cv2.waitKey(1) & 0xFF

        # A ciklusból a k billentyű leütésével léphetünk ki: (k)ilépés
        if key == ord("k"):
            break

# Az erőforrások konzerválása miatt takarítunk
# Bezárjuk a megjelenítésre használt ablakot, ezzel felszabadítjuk a handle-t
cv2.destroyAllWindows()
# Leállítjuk a video streamet
vs.stop()

# Amennyiben írtunk video kimenetet, akkor felszabadítjuk a hozzá kapcsolódó pointereket is
if writer is not None:
    writer.release()
    


