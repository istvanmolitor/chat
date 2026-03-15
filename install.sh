#!/bin/bash

# Chat API Telepítő Szkript

set -e

echo "🚀 Chat API telepítése elindul..."

# 1. Függőségek telepítése Docker-rel (ha még nincs vendor mappa)
if [ ! -d "vendor" ]; then
    echo "📦 Composer függőségek telepítése (Docker)..."
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install --ignore-platform-reqs
else
    echo "✅ Vendor mappa már létezik, kihagyás."
fi

# 2. .env fájl létrehozása
if [ ! -f ".env" ]; then
    echo "📄 .env fájl létrehozása..."
    cp .env.example .env
else
    echo "✅ .env fájl már létezik."
fi

# 3. Sail elindítása
echo "🐳 Docker konténerek elindítása (Sail)..."
./vendor/bin/sail up -d

# Várakozás, amíg az adatbázis feláll (egyszerűbb verzió)
echo "⏳ Várakozás a konténerekre..."
sleep 5

# 4. Alkalmazás kulcs generálása
echo "🔑 Alkalmazás kulcs generálása..."
./vendor/bin/sail artisan key:generate

# 5. Migrációk és seedelés
echo "🗄️ Adatbázis migrációk és seedelés..."
./vendor/bin/sail artisan migrate --seed

# 6. Frontend build (Vite manifest hiba elkerülése)
echo "📦 Frontend függőségek telepítése és build..."
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

echo "✅ Telepítés sikeresen befejeződött!"
echo "🌐 Az alkalmazás elérhető: http://localhost"
echo "🌐 A mailpit elérhető: http://localhost:8025"
