<?php
#	echo "lang.php - ".$_SESSION['lang'];

	# HU,EN,DE,SI,HR

	switch ($_SESSION['lang']) {
		case "HU":
			$n_lang=0;
			break;
		case "EN":
			$n_lang=1;
			break;
		case "DE":
			$n_lang=2;
			break;
		case "SI":
			$n_lang=3;
			break;
		case "HR":
			$n_lang=4;
			break;
		default:
			$n_lang=0;		
	}



	/*
	directory structure
		index.php		-	login
		common_files
		manual
		flow
		clients
		inventory
		privileges
	*/

	# index.php
	$lang_login = array (
		array ("InVR &#9832; login","InVR &#9832; login","InVR &#9832; login","InVR &#9832; login","InVR &#9832; login"), # 0
		array ("Intelligens Vezérlési Rendszer","Intelligent Control System","Intelligent Control System","Intelligent Control System","Intelligent Control System"),
		array ("InVR &#9832;  belépés","InVR &#9832;  login","InVR &#9832;  anmelden","InVR &#9832;  login","InVR &#9832;  login"),
		array ("Név","name","Name","name","name"),
		array ("Jelszó","password","Kennwort","password","password"),
		array ("mehet","ok","ok","ok","ok"),
	);

	# manual/index.php
	$lang_manual = array (
		array ("InVR &#9832; Manuál","InVR &#9832; Manual","InVR &#9832; Manual","InVR &#9832; Manual","InVR &#9832; Manual"), # 0
		array ("Intelligens Vezérlési Rendszer","Intelligent Control System","Intelligent Control System","Intelligent Control System","Intelligent Control System"),
		array ("kényelem, takarékosság, presztízs","comfort, thrift, prestige","Komfort, Sparsamkeit, Prestige","comfort, thrift, prestige","comfort, thrift, prestige"),
		array ("kilépés","logout","Abmelden","logout","logout"),
		array ("I. OfficeLink Intelligens Vezérlési Rendszer | InVR &#9832;","I. Intelligent Control System | InVR &#9832;","I. Intelligent Control System | InVR &#9832;","I. Intelligent Control System | InVR &#9832;","I. Intelligent Control System | InVR &#9832;"),
		array ("Felügyeleti rendszer","Client control system","Client control system","Client control system","Client control system"), # 5
		array ("A felügyeleti rendszer nyilvántartja a vezérlési rendszereket és azok felépítését, naplózza a rendszerek elérhetőségét.","The client controls system access and log all system and parts","","",""),
		array ("Nyilvántartja a felhasználókat, rendszerelérési jogosultságaikat, az értesítési rendszeren keresztül az eseményekről tájékoztatja őket.","","","",""),
		array ("Vezérlési rendszer","Controlling the IOT parts","","",""),
		array ("A felhasználó számára a vezérlőprogram nyújta a rendszerhez kapcsolt eszközök közötti logikai kapcsolatokon keresztül a rendszer alapvető feladatait: <b>Kényelem, takarékosság, presztízs.","","","",""),

		array ("Vezérlőmodulok","Modules, IOT parts","","",""), # 10
		array ("A rendszer fizikai részegységei a vezérlőmodulok, amelyek elérhetővé teszik a rendszerhez kapcsolt környezeti eszközöket.","","","",""),
		array ("II. Felügyeleti rendszer","Client control system","","",""),
		array ("Kliens lista","Clients list","","",""),
		array ("<b>Kliens lista: </b>A kliens lista elemei a szerverek, a vezérlési rendszerek és a felhasználók.","Clients list: list of servers, control systems and users","","",""),
		array ("Egy adott rendszer adatlapja <a href='../clients/ds_form.php?id=mcs001' target='_blank'>itt elérhető</a>.","","","",""), # 15
		array ("Rendszerfolyamatok - flow","System map - flows","","",""),
		array ("<b>rendszerfolyamatok: </b>Ez a <a href='../flow/flow_list.php' target='_blank'>rendszerfolyamatok és eljárások listája</a>, amelyek a szerveren vagy a klienseken futnak.","","","",""),
		array ("A rendszerfolyamatok <a href='../flow/index.php' target='_blank'>térképe</a>.","Map of system1s flows","","",""),
		array ("Leltár","Inventory","","",""),

		array ("<b>Leltár: </b>A vezérlési rendszerek alkatrészei, hardver és szoftver komponensei, amelyek a működést biztosítják. <a href='../inventory' target='_blank'>Teljes elemlista</a>, naplószerűen rögzítve, azaz elemek és rendszer életciklusa követhető, a gyártástól vagy vásárlástól kezdve a beállításon, összeszerelésen keresztül,  a rendszertelepítésig, valamint a javításig és a selejtezésig.","","","",""), # 20
		array ("Az elemek azonosítása a part numberrel (PN) és a serial numberrel (SN) együttesen történik.","","","",""),
		array ("Vezérlési rendszer alkatrészek, elemek <a href='../inventory/act_inv.php' target='_blank'>hozzáadása, szerkesztése, törlése, állapotváltása</a>. Elemek listája elérhető <a href='../inventory/products.php' target='_blank'>itt</a>.","","","",""),
		array ("Felhasználók, jogosultságok","Users, privileges","","",""),
		array ("<b>felhasználók, jogosultságok: </b>A vezérlőrendszerek tulajdonosai, felhasználói hozzáféréssel rendelkeznek a rendszerekhez: értesítések fogadása, elérés, beállítás.</a>","","","",""),
		array ("Felügyeleti rendszer tevékenységek","","","",""), # 25
		array ("Rendszerfelügyelet","","","",""),
		array ("A rendszerfelügyeletet a rendszerfolyamatok (flow) végzik, amely a <a href='../flow/flow_list.php' target='_blank'>rendszerfolyamatok listájában</a> és a <a href='../flow/index.php' target='_blank'>rendszertérképen</a> követhető.","","","",""),
		array ("Rendszer elem tevékenységek","","","",""),
		array ("gyártás / vásárlás","","","",""),

		array ("beállítás / rendszerbe kapcsolás / értékesítés / javítás / selejtezés","","","",""), # 30
		array ("Rendszer összeállítás, üzemeltetés","","","",""),
		array ("új rendszer","","","",""),
		array ("elem rendszerhez adása","","","",""),
		array ("rendszer karbantartás","","","",""),
		array ("rendszerelem cseréje","","","",""), # 35
		array ("rendszer értékesítése","","","",""),
		array ("rendszer bérbeadása","","","",""),
		array ("rendszer szüneteltetése, leállítása","","","",""),
		array ("Adatlista","Datalist","","",""),

		array ("<b>adatlista: </b>Alapadatok felügyeleti rendszerhez:","<b>datalist: </b>Datasheets for clients control system:","","",""), # 40
		array ("név","name","","",""),
		array ("Felügyeleti rendszer fejlesztési napló","Development log","","",""),
		array ("","","","",""),
		array ("","","","",""),
		array ("","","","",""), # 45
		array ("","","","",""),
		array ("","","","",""),
		array ("","","","",""),
		array ("","","","",""),
	);



	# lists switches
	$lang_sw = array (
		array ("","","","",""), # 0
		array ("Kliens felügyeleti rendszer lista","Client's list","","",""),
		array ("Rendszerfolyamat lista","flow list","","",""),
		array ("Rendszerfolyamat térkép","flow map","","",""),
		array ("Térkép","open map","","",""),

		array ("Diszpécser képernyő","dispatcher screens","","",""), # 5
		array ("Felhasználók","VPN users","","",""),
		array ("Szerverek","servers","","",""),
		array ("Alaprendszerek","development - base systems","","",""),
		array ("InVR rendszerek","InVR systems","","",""),

		array ("OFFline rendszerek","OFFline systems","","",""), # 10
		array ("Csak a hiányzó rendszerek megjelenítése","only broken systems of the owner","","",""),
		array ("Kamera képek","camera pics ON / OFF","","",""),
		array ("Kliens bejelentkezési napló","clients log ON / OFF","","",""),
		array ("Minden státusz log megjelenítése","all status log ON","","",""),
		array ("Minden státusz log elrejtése","all status log OFF","","",""), # 15
		array ("lost (24hour) | status=0 -> ON","lost (24hour) | status=0 -> ON","","",""),
		array ("lost (24hour) | status=0 -> OFF","lost (24hour) | status=0 -> OFF","","",""),
		array ("OK | status=1 -> ON","OK | status=1 -> ON","","",""),
		array ("OK | status=1 -> OFF","OK | status=1 -> OFF","","",""),

		array ("lost (15min) | status=2 -> ON","lost (15min) | status=2 -> ON","","",""), #20
		array ("lost (15min) | status=2 -> OFF","lost (15min) | status=2 -> OFF","","",""),
		array ("no power | status=3 -> ON","no power | status=3 -> ON","","",""),
		array ("no power | status=3 -> OFF","no power | status=3 -> OFF","","",""),
		array ("lost vpn user | status=40 -> ON","lost vpn user | status=40 -> ON","","",""),
		array ("lost vpn user | status=40 -> OFF","lost vpn user | status=40 -> OFF","","",""), #25
		array ("lost vpn user | status=41 -> ON","lost vpn user | status=41 -> ON","","",""),
		array ("lost vpn user | status=41 -> OFF","lost vpn user | status=41 -> OFF","","",""),
		array ("lost vpn user | status=42 -> ON","lost vpn user | status=42 -> ON","","",""),
		array ("lost vpn user | status=42 -> OFF","lost vpn user | status=42 -> OFF","","",""),

		array ("lost vpn user | status=43 -> ON","lost vpn user | status=43 -> ON","","",""), #30
		array ("lost vpn user | status=43 -> OFF","lost vpn user | status=43 -> OFF","","",""),
		array ("idl","idl","","",""),
		array ("calendar ON/OFF","calendar ON/OFF","","",""),
		array ("Új rendszer felvétele","new client","","",""),
		array ("Új rendszer","new client","","",""), #35
		array ("Kilépés","logout","","",""),
		array ("Manuál","manual","","",""),
		array ("","","","",""),
		array ("","","","",""),

	);

	# flow/flow_list.php
	$lang_flow_list = array (
		array ("InVR &#9832; Rendszerfolyamatok","InVR &#9832; Systems flows","InVR &#9832; Systems flows","InVR &#9832; Systems flows","InVR &#9832; Systems flows"), # 0
		array ("OFF","OFF","","",""),
		array ("rendben","ok","","",""),
		array ("meleg","warm","","",""),
		array ("figyelj","warning","","",""),

		array ("forró","hot","","",""), # 5
		array ("nincs","no","","",""),
		array ("selejt","scrap","","",""),
		array ("","","","",""),
		array ("","","","",""),

		array ("","","","",""), # 10

	);



	# flow/index.php
	$lang_flow = array (
		array ("InVR &#9832; Rendszertérkép","InVR &#9832; Systems map","InVR &#9832; System map","InVR &#9832; System map","InVR &#9832; System map"), # 0
		array ("Rendszerfolyamatok","Systems flows","","",""),

		array ("","","","",""), # 5
		array ("","","","",""),

		array ("","","","",""), # 10

	);


	# clients/index.php
	$lang_clients = array (
		array ("InVR &#9832; Kliens lista","InVR &#9832; Clients list","InVR &#9832; Clients list","InVR &#9832; Clients list","InVR &#9832; System's map"), # 0
		array ("CPU","CPU","","",""),
		array ("rendezés id szerint","order by id - last will be first","","",""),
		array ("rendezés id szerint","order by id","","",""),
		array ("rendezés név szerint","order by name - last will be first","","",""),
		array ("Név","name","","",""), # 5
		array ("rendezés név szerint","order by name","","",""),
		array ("Utolsó belépés","last login","","",""),
		array ("rendezés utolsó belépés szerint","order by last login - last will be first","","",""),
		array ("rendezés utolsó belépés szerint","order by last login","","",""),

		array ("rendezés megbízhatóság szerint","order by SLA - last will be first","","",""), # 10
		array ("rendezés megbízhatóság szerint","order by SLA","","",""),
		array ("rendezés vpn ip szerint","order by vpn ip","","",""),
		array ("rendezés vpn ip szerint","order by vpn ip - last will be first","","",""),
		array ("rendezés elérési idő szerint","order by response time - last will be first","","",""),
		array ("rendezés elérési idő szerint","order by response time","","",""), # 15
		array ("rendezés sebesség alapján","order by velocity - last will be first","","",""),
		array ("rendezés sebesség alapján","order by velocity","","",""),
		array ("Ugrás a klienshez","jump to client","","",""),
		array ("Kliens bejelentkezési napló","log of the client","","",""),

		array ("Terminál kinyitása","open terminal","","",""), # 20
		array ("","","","",""),
		array ("","","","",""),
		array ("","","","",""),
		array ("","","","",""),
		array ("","","","",""), # 25
		array ("","","","",""),
		array ("","","","",""),
		array ("","","","",""),
		array ("","","","",""),

	);




#	echo "<br>text1: ".$lang_login[0][$n_lang]; # test

?>
