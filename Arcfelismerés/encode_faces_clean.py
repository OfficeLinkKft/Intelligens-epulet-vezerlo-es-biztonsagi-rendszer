# Haszálat
# python encode_faces.py --dataset dataset --encodings encodings.pickle

# A kód egy arcfelismerő projekt alapjaira épül
# https://www.pyimagesearch.com/2018/06/18/face-recognition-with-opencv-python-and-deep-learning/

# A szükséges csomagok beimportálása
from imutils import paths
import face_recognition
import argparse
import pickle
import cv2
import os
import datetime



# A paraméterek feldolgozásához létre kell hozni a megfelelő paraméter parszert
ap = argparse.ArgumentParser()

# A rendszámok feldolgozott encodingjait tartalmazó állomány
ap.add_argument("-e", "--encodings", required=True,
    help="A felismerendő arcok szerializált adatbázisát tartalmazó állomány teljes elérési útja")

# A feldolgozott video kimenet
ap.add_argument("-i", "--dataset", required=True,
    help="Az arcokat tartalmazó képek elérési útja (könyvtár+képek)")

# Felismerési módszer
# A hog kevésbé erőforrásigényes és GPU nélküli környezetben is alkalmazható, míg a cnn pontosabb lehet, de 
# annak futtatásához GPU-ra van szükség
ap.add_argument("-d", "--detection-method", type=str, default="cnn",
    help="A használandó rendszámfelismerési módszer: `hog` vagy `cnn`")

# A megadott paraméterek begyűjtése
args = vars(ap.parse_args())

# grab the paths to the input images in our dataset
print("[INFO - {}]\tAz arcokat tartalmazó képek felsorolása...".format(datetime.datetime.now()))
imagePaths = list(paths.list_images(args["dataset"]))

# Az ismert encoding-ok és nevek listájának inicializálása
knownEncodings = []
knownNames = []
starttime = datetime.datetime.now()
print("[INFO - {}]\tA szükséges teljes idő: {}".format(datetime.datetime.now(), datetime.datetime.now()-starttime))

# Végigiterálunk a képek elérési útjain
for (i, imagePath) in enumerate(imagePaths):
    # A tanítandó személy nevét a kép elérési útjából vesszük
    print("[INFO - {}]\tA(z) {}/{} kép feldolgozása folyamatban - képfile: {}".format(datetime.datetime.now(), i + 1,
        len(imagePaths), imagePath))
    name = imagePath.split(os.path.sep)[-2]

    # Betöltjük a képet és elvégezzük a szükséges transzformációkat
    image = cv2.imread(imagePath)
    rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)

    # Megkeressük az arcokat tartalmazó befoglaló téglalapokat
    boxes = face_recognition.face_locations(rgb,
        model=args["detection_method"], number_of_times_to_upsample=1)
    print("[INFO - {}]\tAz arcokat befoglaló téglalapok: {}".format(datetime.datetime.now(), boxes))

    # Kiszámítjuk az embeddingeket az arcokra
    encodings = face_recognition.face_encodings(rgb, boxes)

    # Végigiterálunk az embeddingeken
    for encoding in encodings:
        # Minden egyes encodingot és nevet hozzáadjuk az ismert encodingok és 
        # nevek listájához
        knownEncodings.append(encoding)
        knownNames.append(name)

# Az adatokat kimentjük egy pickle file-ba (encodingok + nevek)
print("[INFO - {}]\tAz adatok kimentése...".format(datetime.datetime.now()))
data = {"encodings": knownEncodings, "names": knownNames}
f = open(args["encodings"], "wb")
f.write(pickle.dumps(data))
print("[INFO - {}]\tA szükséges teljes idő: {}".format(datetime.datetime.now(), datetime.datetime.now()-starttime))
f.close()