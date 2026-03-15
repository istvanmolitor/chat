# Chat API - Laravel

Ez egy RESTful Chat API projekt, amely a Laravel 12 keretrendszerre épül. Lehetővé teszi a felhasználók regisztrációját, email-verifikációt, ismerősök kezelését és privát üzenetküldést.

## Főbb funkciók

- **Autentikáció**: Regisztráció, bejelentkezés és kijelentkezés Laravel Sanctum segítségével.
- **Email-verifikáció**: Csak hitelesített email címmel rendelkező felhasználók használhatják a rendszert.
- **Felhasználók kezelése**: Aktív felhasználók listázása (keresés és lapozás).
- **Ismerősök kezelése**: Ismerősi felkérések küldése, elfogadása, elutasítása és ismerősök listázása.
- **Üzenetküldés**: Szöveges üzenetek küldése ismerősök között, beszélgetés előzmények lekérdezése, olvasatlan üzenetek száma.

## Technológiai stack

- **PHP**: 8.3+ (a környezetben 8.5.3 fut)
- **Framework**: Laravel 12
- **Adatbázis**: MySQL / MariaDB
- **Docker**: Laravel Sail
- **Autentikáció**: Laravel Sanctum

## Telepítés és beállítás

A projekt futtatásához Docker és Docker Compose szükséges (Laravel Sail használatával).

### Egyszerű telepítés (Szkripttel)

Ha a rendszeren elérhető a `bash`, futtathatod az automatizált telepítő szkriptet:

```bash
git clone git@github.com:istvanmolitor/chat.git
cd chat
chmod +x install.sh
./install.sh
```

### Manuális telepítés

1. **Projekt klónozása**
   ```bash
   git clone git@github.com:istvanmolitor/chat.git
   cd chat
   ```

2. **Függőségek telepítése**
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php84-composer:latest \
       composer install --ignore-platform-reqs
   ```
   *(Megjegyzés: Ha van helyi composer-ed, futtathatod közvetlenül is, de a Sail-es konténer biztosítja a megfelelő környezetet.)*

3. **Környezeti változók beállítása**
   ```bash
   cp .env.example .env
   ```
   *(A `.env` fájlban ellenőrizd az adatbázis és mail beállításokat. Sail esetén az alapértelmezett értékek általában megfelelnek.)*

4. **Sail elindítása**
   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Alkalmazás kulcs generálása**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6. **Adatbázis migrációk és seedelés**
   ```bash
   ./vendor/bin/sail artisan migrate --seed
   ```

7. **Frontend függőségek telepítése és build**
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```

## Használat (API Végpontok)

Az API alapértelmezett URL-je: `http://localhost/api`

### Hitelesítés (Public)
- `POST /api/register` - Regisztráció (név, email, jelszó)
- `POST /api/login` - Bejelentkezés (visszatér a Bearer tokennel)

### Felhasználók (Auth required)
- `GET /api/user` - Aktuális felhasználó adatai
- `GET /api/users/active` - Aktív felhasználók listája (szűrhető: `?search=name`, lapozható)
- `GET /api/users/{id}` - Felhasználó profilja

### Ismerősök (Auth required)
- `GET /api/friends` - Ismerősök listája
- `GET /api/friends/requests` - Függőben lévő felkérések
- `POST /api/friends/request/{userId}` - Ismerősi felkérés küldése
- `POST /api/friends/accept/{friendshipId}` - Felkérés elfogadása
- `POST /api/friends/decline/{friendshipId}` - Felkérés elutasítása
- `DELETE /api/friends/{userId}` - Ismerős eltávolítása

### Üzenetek (Auth required)
- `GET /api/messages/{userId}` - Beszélgetés lekérdezése egy ismerőssel
- `POST /api/messages/{userId}` - Üzenet küldése
- `GET /api/messages/{userId}/unread` - Olvasatlan üzenetek száma

## Tesztelés

A tesztek futtatásához használd a következő parancsot:

```bash
./vendor/bin/sail artisan test
```

A fontosabb funkciók (regisztráció, ismerősök, üzenetküldés) a `tests/Feature` mappában található tesztekkel vannak lefedve.

## Fejlesztés

A kódformázáshoz a Laravel Pint-et használjuk:
```bash
./vendor/bin/sail bin pint
```
