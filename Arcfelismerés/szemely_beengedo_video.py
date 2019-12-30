# Több arcfelismerési modellt kiprobáltunk.
# A felismerést megvalósító kód az alábbi tutorialra épül:
# https://www.pyimagesearch.com/2018/06/18/face-recognition-with-opencv-python-and-deep-learning/
# A kód arcfelismrésre mutatja meg, hogyan használható hatékonyan a mély metrika tanulás.

# Használat:
# python szemely_beengedo_video_clean.py --output ./videowithPersons.avi --encodings encodingsLPs.pickle -d hog
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

http = urllib3.PoolManager()



# A paraméterek feldolgozásához létre kell hozni a megfelelő paraméter parszert
ap = argparse.ArgumentParser()

# A rendszámok feldolgozott encodingjait tartalmazó állomány
ap.add_argument("-e", "--encodings", required=True,
    help="A felismerendő arcok szerializált adatbázisát tartalmazó állomány teljes elérési útja")

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
recognition_threshold = 30
unknown = "Ismeretlen"
lock_open = False
lock_file = Path("lock_file.txt")
blocking_period = 5
frame_counter = 0

# A rendszerben regisztált személyek
registered_persons = ['Gyuszi', 'Gabi', 'Agi', 'Gyula', 'Geri', 'Gyuri', 'Roli', 'Zsombor', 'Rezso', 'Rezsi', 'Toncsi']

# Amennyiben szűkösek az erőforrások, minden n-ik képkockát dolgozzuk csak fel
# Az 1 érték azt jelenti, hogy mindegyiket feldolgozzuk
nth = 1

# Beengedendő személyek
# Bemenetként a függvény megkapja a felismert személyeket, és ellenőrzi, hogy
# benne van-e a regisztrált személyek listájában. Ha igen, visszaadja a beengedendő
# személyt    
def authorized_person_recognised(names):
    print("[INFO - {}]\tEllenőrizendő személyek: {}".format(datetime.datetime.now(), names))
    name_list = [x for x in names if (x != unknown) and x in registered_persons]
    print("[INFO - {}]\tA beengedhető személyek: {}".format(datetime.datetime.now(), name_list))
    return name_list

def carry_out_action(authorized_person):
    print("--------------------------------------------------------------------------------")
    print("[INFO - {}]\tBeengedési esemény".format(datetime.datetime.now()))
    #url="http://109.61.31.18/act/gar.php?name=p_oli_k7&data=1"
    #url = "https://intelligens.officelink.hu/cam2light.php"
    #response = http.request('GET', url)
    #soup = BeautifulSoup(response.data)

    print("[INFO - {}]\tAjtó nyitás".format(datetime.datetime.now()))
    print("[INFO - {}]\tA {} nevű személy beengedésre került.".format(datetime.datetime.now(), authorized_person))    
    print("--------------------------------------------------------------------------------")
    
    if (not(lock_file.exists())):
        lock_open = True
        # From transactional point of view this is not correct
        print("Authorizd person: ", authorized_person[0])
        lock = open("lock_file.txt", "w") 
        lock.write(authorized_person[0]) 
        lock.close() 
        
# A video file és a kimeneti video író kezdeti pointereinek inicializálása
print("[INFO - {}]\tA video stream feldolgozása indul...".format(datetime.datetime.now()))
#vs = VideoStream(src=0).start()
#vs = VideoStream(src="http://admin:4932f8ef@dcs5000.euronetrt.hu:65432/video.cgi").start()
vs = VideoStream(src="http://admin:4932f8ef@192.168.0.103:65432/video.cgi").start()
#vs = VideoStream(src="http://kepecske:jelszavacska@109.61.54.170/mjpg/video.mjpg").start()

print("[INFO - {}]\tA video stream objektum: {}".format(datetime.datetime.now(), vs))
writer = None
time.sleep(2.0)
print("[INFO - {}]\tA video stream él...".format(datetime.datetime.now()))

# A video stream képkockáin végig iterálunk
while True:
    # beolvassuk a következő képkockát
    frame = vs.read()
    frame_counter = frame_counter + 1

    print("")
    print("[INFO - {}]\tKépkocka feldolgozás - {}. képkocka...".format(datetime.datetime.now(), frame_counter))

    # Amennyiben a rendelkezésre álló számítási kapacitás szűkös, lehetőségünk 
    # van rá, hogy csak minden nth képkockát dolgozzunk fel. Jelenleg megfelelő
    # erőforrás áll rendelkezésünkre, hogy minden egyes képkockát feldolgozzunk.
    if (frame_counter % nth == 0):
    
        
        # A bemenetet RGB-re alakítjuk (tanítás is RGB-ben volt), illetve
        # a hatékonyab feldolgozás érdekében leskálázzuk 750px szélességre
        rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        rgb = imutils.resize(frame, width=750)
        r = frame.shape[1] / float(rgb.shape[1])
    
        # Megkeressük a lehetséges arcokat
        # befoglaló téglalapjának a koordinátáit a képkockában, majd kiszámítjuk
        # rá az embeddingeket minden egyes arcra
        boxes = face_recognition.face_locations(rgb,
            model=args["detection_method"])

        print("[INFO - {}]\tEmbedding feldolgozás...".format(datetime.datetime.now()))

        encodings = face_recognition.face_encodings(rgb, boxes)
        names = []
    
        face_count = 0
        # végig iterálunk az arc embeddingeken
        for encoding in encodings:
            # Megpróbáljuk illeszteni a képen lévő minden egyes rendszámtáblát
            # a betanított adatbázisban található rendszámokra
            
            face_count = face_count + 1
            
            matches = face_recognition.compare_faces(data["encodings"],
                encoding)
            name = unknown
    
            # Ellenőrizzük, hogy találtunk-e illeszkedést
            if True in matches:
                # Megkeressük az összes megtalált arc indexét
                # és inicializálunk egy dictionary-t, hogy megszámoljuk
                # az összes találatot
                matchedIdxs = [i for (i, b) in enumerate(matches) if b]
                counts = {}
    
                # Végigiterálunk a találati indexeken és frissítjük
                # a találati számot minden egyes rendszámhoz
                for i in matchedIdxs:
                    name = data["names"][i]
                    counts[name] = counts.get(name, 0) + 1
    
                # Meghatározzuk a legnagyobb találati számmal rendelkező
                # arcot (amelyik a legtöbb szavazatot kapta)
                # Amennyiben több arc azonos szavazatot kap, akkor
                # az első előfordulás kerül kiválasztásra
                name = max(counts, key=counts.get)
                max_count = int(counts.get(name))
                name = name.split('.')[1]
            
            # Csak akkor frissítjük a nevek listáját, ha a maximum nagyobb,
            # mint a meghatározott felismerési küszöbérték
            if max_count < recognition_threshold:
                name = "Ismeretlen"
    
            names.append(name)

            if names:
                print("[INFO - {}]\tArcot találtunk: {}".format(datetime.datetime.now(), names))
            
            print("[INFO - {}]\t\tMegtalált arcok száma: {}".format(datetime.datetime.now(), face_count))
            print("[INFO - {}]\t\tNév: {}".format(datetime.datetime.now(), name))
            print("[INFO - {}]\t\tTalálati lista: {}".format(datetime.datetime.now(), counts))
    
        # Végigiterálunk a megtalált rendszámtáblákon
        for ((top, right, bottom, left), name) in zip(boxes, names):

            # Visszaskálázzuk a rendszámtábla koordinátáka
            top = int(top * r)
            right = int(right * r)
            bottom = int(bottom * r)
            left = int(left * r)
    
            # Berajzoljuk a prediktált rendszámtáblát és kiírjuk a rendszámot a képre
            cv2.rectangle(frame, (left, top), (right, bottom),
                (0, 255, 0), 2)
            y = top - 15 if top - 15 > 15 else top + 15
            cv2.putText(frame, name, (left, y), cv2.FONT_HERSHEY_SIMPLEX,
                0.75, (0, 255, 0), 2)
    
        # Amennyiben még nem inicializáltuk a video writert és kell video kimenetet
        # írnunk, inicializáljuk a video writert
        if writer is None and args["output"] is not None:
            fourcc = cv2.VideoWriter_fourcc(*"MJPG")
            writer = cv2.VideoWriter(args["output"], fourcc, 20,
                (frame.shape[1], frame.shape[0]), True)
    
        # Amennyiben a writer inicializálva van, kiírjuk a képkockát a video fileba
        # a megtalált bekeretezett rendszámtáblával és fölötte a megtalált rendszámmal
        if writer is not None:
            writer.write(frame)
    
        # Amennyiben meg kell jelenítenünk a video streamet a képernyőn, akkor
        # kiírjuk az aktuális képkockát a megnyitott Output ablak-ba
        if args["display"] > 0:
            cv2.namedWindow('Output ablak',cv2.WINDOW_NORMAL)
            cv2.resizeWindow('Output ablak', 750,750)
            cv2.imshow("Output ablak", frame)
            key = cv2.waitKey(1) & 0xFF
    
            # A ciklusból a k billentyű leütésével léphetünk ki: (k)ilépés
            if key == ord("k"):
                break

        print("[INFO - {}]\tFelismert személy: {}".format(datetime.datetime.now(), name))
                
        authorized_person = authorized_person_recognised(names)
        print("[INFO - {}]\tAutorizált személy: {}".format(datetime.datetime.now(), authorized_person))

        if (authorized_person):
            carry_out_action(authorized_person)
        
# Az erőforrások konzerválása miatt takarítunk
# Bezárjuk a megjelenítésre használt ablakot, ezzel felszabadítjuk a handle-t
cv2.destroyAllWindows()
# Leállítjuk a video streamet
vs.stop()

# Amennyiben írtunk video kimenetet, akkor felszabadítjuk a hozzá kapcsolódó pointereket is
if writer is not None:
    writer.release()
        


