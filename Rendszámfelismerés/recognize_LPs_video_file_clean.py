# Több rendszámfelismeréis modellt kiprobáltunk.
# Ez a megközelítés kombinálja a Jupyter notebookban végigvitt rendszámtábla felismerést, illetve a gépi tanulás
# területén ismert mély metrika tanulást (deep metric learning).
# A felismerést megvalósító kód az alábbi tutorialra épül:
# https://www.pyimagesearch.com/2018/06/18/face-recognition-with-opencv-python-and-deep-learning/
# A kód arcfelismrésre mutatja meg, hogyan használható hatékonyan a mély metrika tanulás.
# Az arcfelismerést lecseréltük rendszámtábla képre, illetve a felismerőt rendszámtábla képekkel tanítottuk
# A rendszer jól demonstrálja az algoritmus általánosíthatóságát és hatékonyságát. 

# Használat:
# python recognize_LPs_video_file_clean.py --output ./videowithLPs.avi --input IMG_4823.MOV --encodings encodingsLPs.pickle -d hog

# A szükséges csomagok importálása
import face_recognition
import argparse
import imutils
import pickle
import cv2
from skimage.color import rgb2gray

# A rendszámtábla felismerő függvények egy külön állományba kerültek
from helperfunctions_clean import find_LP_boxes


# A paraméterek feldolgozásához létre kell hozni a megfelelő paraméter parszert
ap = argparse.ArgumentParser()

# A video file neve elérési úttal
ap.add_argument("-i", "--input", required=True,
    help="A feldolgozandó video teljes elérési útja")

# A feldolgozott video kimenet
ap.add_argument("-o", "--output", type=str,
    help="A feldolgozott video teljes elérési útja")

# A feldogozás alatt megjelenjen-e a video a képernyőn
ap.add_argument("-y", "--display", type=int, default=1,
    help="A feldogozás alatt megjelenjen-e a video a képernyőn")

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

# Az ismert rendszámok és encodingjainak betöltése
print("[INFO] encodingok betöltése...")
data = pickle.loads(open(args["encodings"], "rb").read())

# A video file és a kimeneti video író kezdeti pointereinek inicializálása
print("[INFO] A video feldolgozása...")

# A video streamet a megadott fileból vesszük
stream = cv2.VideoCapture(args["input"])
writer = None
frame_counter = 0

# A megjelenítéshez megnyitunk egy ablakot
cv2.namedWindow('Output ablak', cv2.WINDOW_NORMAL)

# A megfelelő feldolgozási sebesség miatt a streamet egy 500x500-as ablakba tesszük
cv2.resizeWindow('Output ablak', 500,500)

# A video állomány képkockáin végig iterálunk
while True:
    frame_counter = frame_counter + 1
    # begrabbeljük a következő képkockát
    (grabbed, frame) = stream.read()
    # ha nem sikerült begrabbelni, akkor elértük a video file végét
    if not grabbed:
        break    

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

    # Amennyiben a rendelkezésre álló számítási kapacitás szűkös, lehetőségünk 
    # van rá, hogy csak minden nth képkockát dolgozzunk fel. Jelenleg megfelelő
    # erőforrás áll rendelkezésünkre, hogy minden egyes képkockát feldolgozzunk.
    nth = 1
    if (frame_counter % nth == 0):
    

    
        # A segédfüggvényünkkel megkeressük a lehetséges rendszámtábla
        # befoglaló téglalapjának a koordinátáit a képkockában, majd kiszámítjuk
        # rá az embeddingeket minden egyes rendszámtáblára (a példában egyetlen
        # rendszám van csak a képen)
        # A segédfüggvényünk visszaadja a megtalált rendszámtábla befoglaló 
        # téglalapjának a koordinátáit, magát a rendszámtábla képét, illetve
        # hogy hány rendszám karaktert talált a képben
        box, lp, found_chars = find_LP_boxes(image)
        boxes = list()
        boxes.append(box)
        
        print("")
        print("A megtalált lehetséges rendszámtábla befoglaló koordinátái: ", boxes)
        if(boxes[0] and found_chars > 3):

            # Ha találtunk rendszámtáblát és legalább 3 karaktert felismertünk,
            # akkor kiszámítjuk rá az embeddinget, hogy megnézzük, van-e
            # illeszkedés a betanított adatbázissal
            # Az arcfelismerő könyvtárat használjuk
            encodings = face_recognition.face_encodings(rgb4recognition, boxes)
            lpns = []
        
            # végig iterálunk a rendszám embeddingeken
            for encoding in encodings:
                # Megpróbáljuk illeszteni a képen lévő minden egyes rendszámtáblát
                # a betanított adatbázisban található rendszámokra
                matches = face_recognition.compare_faces(data["encodings"],
                    encoding, tolerance=0.25)
                lpn = "Ismeretlen"
        
                # Ellenőrizzük, hogy találtunk-e illeszkedést
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
                lpns.append(lpn)
        
            # Végigiterálunk a megtalált rendszámtáblákon
            for ((top, left, bottom, right), lpn) in zip(boxes, lpns):
                # Visszaskálázzuk a rendszámtábla koordinátáka
                top = int(top * r)
                right = int(right * r)
                bottom = int(bottom * r)
                left = int(left * r)
                
                print("Berajzoljuk a téglalapot: ", top, right, bottom, left)
        
                # Berajzoljuk a prediktált rendszámtáblát és kiírjuk a rendszámot a képre
                cv2.rectangle(rgb, (left, top), (right, bottom),
                    (0, 255, 0), 2)
                y = top - 15 if top - 15 > 15 else top + 15
                cv2.putText(rgb, lpn, (left, y), cv2.FONT_HERSHEY_SIMPLEX,
                    0.75, (0, 255, 0), 2)
                print("A megtalált rendszám: ", lpn)
    
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
        cv2.imshow("Output ablak", rgb)
        key = cv2.waitKey(1) & 0xFF

        # A ciklusból a k billentyű leütésével léphetünk ki: (k)ilépés
        if key == ord("k"):
            break

# A video file pointereit standard módon felszabadítjuk
stream.release()

# Amennyiben írtunk video kimenetet, akkor felszabadítjuk a hozzá kapcsolódó pointereket is
if writer is not None:
    writer.release()

# Végtelen ciklusban figyeljük a billentyűzet leütést
while True:
    key = cv2.waitKey(1) & 0xFF
 
    # A ciklusból a k billentyű leütésével léphetünk ki: (k)ilépés
    if key == ord("k"):
        break
    
# Az erőforrások konzerválása miatt takarítunk
# Bezárjuk a megjelenítésre használt ablakot, ezzel felszabadítjuk a handle-t
cv2.destroyAllWindows()
    