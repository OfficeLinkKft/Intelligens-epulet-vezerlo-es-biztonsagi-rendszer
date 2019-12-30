# Intelligens-epulet-vezerlo-es-biztonsagi-rendszer
Intelligens épület vezérlő és biztonsági rendszer

## Célok megfogalmazása
Célunk olyan épület vezérlőközpont és hozzá tartozóan olyan telepítési és üzemeltetési megoldás fejlesztése, amely gyors elterjedést tesz lehetővé.
A nemzetközi előrejelzés szerint a következő 6 évben az intelligens épületek száma 10-szeresére fog növekedni. Jelenleg Magyarországon a
valamilyen intelligenciával felszerelt otthonok száma 0,18%, így a növekedés akár ennek a többszöröse is lehet. Technikai megoldás,
összehangolás és az optimalizálás a fő fókusz. Jelenleg nincs olyan ötlet, amely kiválthatja a megvalósítást, tehát nem az ötlet, hanem a megvalósító
szervezet felépítése és motivációja számít egy projekt sikerre vitelekor.

A repository tartalmazza az OfficeLink Kft. által fejlesztett rendszer főbb komponenseit, illetve az alap kutatási implementációkat.

## Főbb modulok:
### InVR Felügyeleti rendszer
A felügyeleti rendszer egy korszerű webes felügyeleti központot valósít meg, amelyen keresztül a rendszer teelepítői, illetve a rendszer birtokosai a megfelelő jogosultságokkal megtekinthetik, illetve menedzselhetik a hozzájuk tartozó eszközöket.

### InVR vezérlőegység
A rendszer tartalmazza a mai korszerű vezérlési típusokat megvalósító egységeket, többek közt egy központi vezérlő szofvert, illetve modul szoftvereket. Az alap modulok többek közt: hőmérő, fénymérő, világítás szabályozó, sötétítés szabályozó, kazán vezérlő, stb.

### Arcfelismerő rendszer
Egy open-source alapra építő arcfelismerő rendszer deszkamodell, amely lehetővé teszi kiterjesztett beléptetés megvalósítását az automatizált rendszerbe. 
A rendszer a video (pl. kaputelefon) alapú felismerést (személy beléptetés) demonstrálja.

### Rendszám felismerő rendszer
Egy open-source alapra építő rendszámfelismerő rendszer deszkamodell, amely lehetővé teszi kiterjesztett beléptetés megvalósítását az automatizált rendszerbe. 
A rendszer a video (pl. garázskamera) alapú rendszmfelismerést (autó beléptetés) demonstrálja.

### Log elemzés
A log elemzés több Jupyter notebookban történik.
A logok havi bontásban csv állományokban találhatóak. A logok előfeldolgozással egy állományba kerültek.



 


