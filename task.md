Chat API fejlesztése Laravel keretrendszerrel
Készíts egy REST alapú chat API-t a Laravel keretrendszer (legalább v11) felhasználásával.
Az API célja, hogy lehetővé tegye a felhasználók számára a regisztrációt, ismerősök
hozzáadását és üzenetküldést egymás között. A projekt során törekedj a moduláris felépítésre,
tiszta kódszerkezetre és tesztelhető megoldásra.

Funkcionális követelmények
1. Felhasználói regisztráció
   o A felhasználók email-címmel regisztrálhatnak.
   o A regisztrációhoz emailes megerősítés szükséges (email-verifikáció).
2. Ismerősök kezelése
   o Regisztrált és aktív felhasználók ismerősnek jelölhetik egymást.
   o Ismerősnek jelölés csak akkor lehetséges, ha a másik fél is aktív.
   o Az ismerős kapcsolatok kölcsönösek legyenek (kétirányú kapcsolat).
3. Felhasználók listázása
   o Lehessen kilistázni az aktív felhasználókat ismerősnek jelölés céljából.
   o A lista legyen lapozható (pagination) és szűrhető (pl. név szerint).
4. Üzenetküldés
   o Két ismerős felhasználó tudjon egymásnak szöveges üzeneteket küldeni.
   o A rendszer tárolja az üzeneteket és biztosítson végpontokat azok lekérdezésére.

Technikai követelmények
• Nyelv: PHP 8.3 vagy újabb
• Keretrendszer: Laravel 11 vagy újabb
• Adatbázis: MySQL vagy MariaDB
• Autentikáció: Laravel beépített email-verifikációs rendszere
• API struktúra: RESTful elvek szerint
• Tesztelés: Legalább egy feature test elkészítése a legfontosabb funkcionalitásra (pl.
regisztráció, ismerős jelölés, üzenetküldés)

Elvárt kimenet
• A teljes API kódja egy Laravel projektben
• Adatbázismigrációk, modellek, kontrollerek, validációk stb.
• Példák a tesztekre (Feature Test) a tests/Feature mappában
• README.md fájl, amely tartalmazza a telepítési és használati útmutatót (opcionális,
de javasolt)
