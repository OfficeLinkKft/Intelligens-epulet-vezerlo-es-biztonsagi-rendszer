# Használat
# python encode_LPs_clean.py --dataset dataset --encodings encodings.pickle

# A kód alapja egy arcfelismerő projekt kódbázisára épül
# https://www.pyimagesearch.com/2018/06/18/face-recognition-with-opencv-python-and-deep-learning/

# A szükséges csomagok importálása
from imutils import paths
import face_recognition
import argparse
import pickle
import cv2
import os
import datetime
import imutils
from skimage.io import imread
from skimage.color import rgb2gray

# A rendszámtábla felismerő függvények egy külön állományba kerültek
from helperfunctions_clean import find_LP_boxes

# A paraméterek feldolgozásához létre kell hozni a megfelelő paraméter parszert
ap = argparse.ArgumentParser()

# A video file neve elérési úttal
ap.add_argument("-i", "--dataset", required=True,
    help="A feldolgozandó rendszámokat tartalmazó könyvtár elérési útja")

# A rendszámok feldolgozott encodingjait tartalmazó állomány
ap.add_argument("-e", "--encodings", required=True,
    help="A rendszámok szerializált adatbázisát tartalmazó állomány teljes elérési útja")

# Felismerési módszer
# A hog kevésbé erőforrásigényes és GPU nélküli környezetben is alkalmazható, míg a cnn pontosabb lehet, de 
# annak futtatásához GPU-ra van szükség
ap.add_argument("-d", "--detection-method", type=str, default="cnn",
    help="A használandó rendszámfelismerési módszer: `hog` vagy `cnn`")

# A megadott paraméterek begyűjtése
args = vars(ap.parse_args())

print("[INFO - {}]\tA rendszámok (LPs) feldolgozásának kezdete...".format(datetime.datetime.now()))
print()

# A datasetben található összes kép elérési útvonalát legeneráljuk egy listába
imagePaths = list(paths.list_images(args["dataset"]))

# Az ismert encodingok és LPN-ek listájának inicializálása
knownEncodings = []
knownLPNs = []

# A megtalálandó karakterek minimális száma a rendszámban, ami felett sikeresnek ítéljük
# a megtalálást
min_chars = 0

starttime = datetime.datetime.now()

# A sikeresen feldolgozott minták számának inicializálása
good_samples = 0

# Az összes képen végigiterálunk
for (i, imagePath) in enumerate(imagePaths):
    print("[INFO - {}]\tA(z) {}/{} kép feldolgozása folyamatban...".format(datetime.datetime.now(), i + 1,
        len(imagePaths)))
    print("[INFO - {}]\t\tA file: {}".format(datetime.datetime.now(), imagePath))
    # A rendszám meghatározása az elérési útból
    lpn = imagePath.split(os.path.sep)[-2]
    print("[INFO - {}]\t\tA jelenleg feldolgozott rendszám: {}".format(datetime.datetime.now(), lpn))
    # Betöltjük a képet és RGB-vé konvertáljuk


#    image = cv2.imread(imagePath)
#    rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
#    image = cv2.cvtColor(image, cv2.COLOR_RGB2GRAY)
#    image = imread(imagePath, as_grey=True)
#    image = imutils.resize(image, width=500)

    image = cv2.imread(imagePath)
    rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    rgb = imutils.resize(image, width=500)
    image = rgb2gray(rgb)

    # A find_LP_boxes() segédfügvénnyel megkeressük a rendszámtáblákat
    # A függvény visszaadja a rendszámtábla befoglalő téglalapját, magát a 
    # rendszámtábla képét, illetve a felismert karakterek számát
    box, lp, found_chars = find_LP_boxes(image)

#    rgb = imutils.resize(rgb, width=500)
    
    boxes = list()
    boxes.append(box)
    print("[INFO - {}]\t\tA megtalált rendszámtábla befoglaló téglalapja: {}".format(datetime.datetime.now(), boxes))
    print("[INFO - {}]\t\tA megtalált karakterek száma: {}".format(datetime.datetime.now(), found_chars))
    
#    if found_chars > min_chars:
#        for (top, right, bottom, left) in boxes:
#        # draw the predicted face name on the image
#            cv2.rectangle(rgb, (left, top), (right, bottom), (0, 255, 0), 2)
#
#            # show the output image
#            cv2.imshow("Image", rgb)
#            cv2.waitKey(0)
 

    # Ha rendszámtáblát találtunk, akkor kiszámítjuk a rendszámtábla embeddingjét
    if found_chars > min_chars:
        print("[INFO - {}]\t\tMegfelelő számú karaktert találtunk: {}".format(datetime.datetime.now(), found_chars))
        encodings = face_recognition.face_encodings(rgb, boxes)
        good_samples = good_samples + 1
    else:
        print("[INFO - {}]\t\tNem találtunk megfelelő számú karaktert: {}".format(datetime.datetime.now(), found_chars))

    # loop over the encodings
    if encodings:
        for encoding in encodings:
            # Minden egyes megtalált rendszámtáblára hozzáadjuk az embeddinget,
            # illetve a rendszámot az ismert encodingok, illetve 
            # rendszámok listájához.
            knownEncodings.append(encoding)
            knownLPNs.append(lpn)

print("[INFO - {}]\tSikeresen feldolgoztunk {} mintát".format(datetime.datetime.now(), good_samples))

# A rendszám encodingokat és a rendszámokat elmentjük
print("[INFO - {}]\tencodingok adatbázisba mentése (pickle file) kezdődik...".format(datetime.datetime.now()))
data = {"encodings": knownEncodings, "lpns": knownLPNs}
f = open(args["encodings"], "wb")
f.write(pickle.dumps(data))
print("[INFO - {}]\tencodingok adatbázisba mentése (pickle file) vége...".format(datetime.datetime.now()))
print()
print("[INFO - {}]\tA feldolgozás teljes ideje: {}".format(datetime.datetime.now(), datetime.datetime.now()-starttime))
f.close()

#cv2.destroyWindow('Image')
