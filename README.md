# E-bankarstvo – REST API

## Opis aplikacije

E-bankarstvo je **backend REST API aplikacija** razvijena u Laravelu. Namenjena je upravljanju korisnicima, bankovnim računima i transakcijama. Sistem omogućava pregled računa, izvršavanje i pretragu transakcija, filtriranje podataka, konverziju valuta i izvoz podataka.

Aplikacija koristi autentifikaciju putem **Laravel Sanctum** tokena i kontrolu pristupa na osnovu uloga (`user`, `admin`, `super_admin`).

> Projekat trenutno predstavlja backend aplikaciju. API zahtevi mogu se testirati pomoću Postmana ili sličnog HTTP klijenta.

## Tehnologije

- **PHP / Laravel** – REST API i poslovna logika
- **MySQL** – relaciona baza podataka
- **Laravel Sanctum** – autentifikacija
- **Redis** – keširanje odabranih podataka
- **Docker i Docker Compose** – kontejnerizacija i pokretanje servisa
- **NGINX** – reverse proxy, raspodela opterećenja između tri backend instance i HTTPS
- **Composer** – upravljanje PHP zavisnostima
- **CSV / PDF** – uvoz i izvoz podataka
- **Frankfurter API** – kursna konverzija valuta

## Funkcionalnosti

### Korisnik (`user`)

- Registracija, prijavljivanje i odjavljivanje
- Zahtev za resetovanje lozinke i postavljanje nove lozinke
- Pregled dostupnih informacija o korisnicima i računima, uz kontrolu pristupa
- Pregled valuta i kategorija transakcija
- Pregled sopstvenih računa i transakcija
- Izvršavanje transakcija sa sopstvenih računa
- Konverzija valuta
- Pretraga i filtriranje transakcija po nazivu, računu i kategoriji
- Izvoz sopstvene transakcije u PDF formatu

### Administrator (`admin`)

- Sve funkcionalnosti korisnika
- Kreiranje, ažuriranje i brisanje podataka u sistemu
- Uvoz podataka iz CSV datoteka
- Izvoz podataka sistema u CSV formatu

### Super administrator (`super_admin`)

- Sve funkcionalnosti administratora
- Promena uloga korisnika

## Arhitektura

Docker Compose konfiguracija obuhvata:

- **Tri Laravel backend instance** (`backend-server1`, `backend-server2`, `backend-server3`)
- **NGINX**, koji prima HTTPS zahteve i raspoređuje ih algoritmom `least_conn`
- **MySQL**, sa Docker volume-om za čuvanje podataka
- **Redis**, za keširanje
- **Healthcheck** provere i zavisnosti pri pokretanju servisa

Tok zahteva:

```text
Klijent / Postman
       |
       | HTTPS :443
       v
     NGINX
       |
       +------> Laravel backend 1 ----+
       +------> Laravel backend 2 ----+----> MySQL
       +------> Laravel backend 3 ----+----> Redis
```

> Konfiguracija je namenjena lokalnom razvoju i demonstraciji. Koristi lokalni HTTPS sertifikat i razvojni Laravel server; nije produkciono podešavanje.

## Pokretanje aplikacije pomoću Docker Compose-a

### 1. Kloniranje repozitorijuma

```bash
git clone https://github.com/elab-development/serverske-veb-tehnologije-2025-26-ebankarstvovebaplikacija_2023_0041.git
cd serverske-veb-tehnologije-2025-26-ebankarstvovebaplikacija_2023_0041
```

Ako se projekat nalazi u drugom GitHub repozitorijumu, zameniti adresu odgovarajućom adresom.

### 2. Kreiranje `.env` datoteke

U glavnom direktorijumu projekta kopirati `.env.example` u `.env`:

```powershell
Copy-Item .env.example .env
```

Primer ključnih vrednosti za Docker okruženje:

```dotenv
DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=ebanking
DB_USERNAME=root
DB_PASSWORD=promeniti_lozinku

REDIS_HOST=redis
REDIS_PORT=6379
CACHE_STORE=redis
```

Podesiti i ostale Laravel promenljive iz `.env.example` (npr. podešavanja slanja mejlova za resetovanje lozinke). Vrednost `DB_PASSWORD` koristi se i za MySQL root lozinku u trenutnom `compose.yaml` fajlu.

**Važno:** Docker Compose automatski koristi `.env` za zamenu izraza poput `${DB_PASSWORD}`, ali ga **ne prosleđuje automatski** svim kontejnerima. Zato konfiguracija backend servisa mora da obezbedi Laravelu potrebne promenljive okruženja, na primer preko `env_file: .env` i odgovarajućih `environment` vrednosti (`DB_HOST=database`, `REDIS_HOST=redis`). Pre pokretanja proveriti da je to podešeno u lokalnoj Compose konfiguraciji.

### 3. Generisanje aplikacionog ključa

Ako su PHP i Composer instalirani lokalno:

```bash
composer install
php artisan key:generate
```

Komandu izvršiti pre pravljenja Docker image-a ako Dockerfile kopira aplikaciju i njenu `.env` datoteku. `APP_KEY` mora biti dostupan svim backend instancama i imati istu vrednost.

### 4. Lokalni HTTPS sertifikat

Konfiguracija `nginx.conf` očekuje datoteke `certificate.crt` i `certificate_key.key` u glavnom direktorijumu projekta. Za lokalno testiranje moguće ih je napraviti pomoću OpenSSL-a:

```bash
openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout certificate_key.key -out certificate.crt -subj "/CN=localhost" -addext "subjectAltName=DNS:localhost"
```

Sertifikat je samopotpisan, pa će ga pregledač ili HTTP klijent možda označiti kao nepouzdan. **Privatni ključ `certificate_key.key` i `.env` ne postavljati na GitHub.**

### 5. Pokretanje kontejnera

```bash
docker compose up -d --build
```

Provera statusa servisa:

```bash
docker compose ps
```

Ako backend servisi ne prelaze u stanje `healthy`, proveriti logove:

```bash
docker compose logs backend-server1 database redis nginx
```

### 6. Kreiranje tabela i testnih podataka

Kada su backend i baza dostupni:

```bash
docker compose exec backend-server1 php artisan migrate --seed
```

### 7. Pristup aplikaciji

API je dostupan putem NGINX-a na adresi:

```text
https://localhost/api
```

Primer zahteva: `POST https://localhost/api/login`.

> NGINX je podešen da sačeka da sva tri backend servisa prođu healthcheck. Ukoliko API ne može da se pokrene zbog konfiguracije ili migracija, potrebno je prvo otkloniti uzrok prikazan u logovima.

Za zaustavljanje kontejnera:

```bash
docker compose down
```

Ova komanda ne briše podatke iz imenovanih Docker volume-ova. Komanda `docker compose down -v` **briše i volume-ove**, uključujući podatke baze, pa je koristiti samo kada je to namera.

## Alternativno: pokretanje bez Docker-a

Potrebni su PHP, Composer i lokalni MySQL server, sa odgovarajućim PHP ekstenzijama.

```bash
git clone https://github.com/elab-development/serverske-veb-tehnologije-2025-26-ebankarstvovebaplikacija_2023_0041.git
cd serverske-veb-tehnologije-2025-26-ebankarstvovebaplikacija_2023_0041
composer install
```

Kopirati `.env.example` u `.env` i podesiti lokalnu bazu:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ebanking
DB_USERNAME=root
DB_PASSWORD=lokalna_lozinka
```

Ako Redis nije pokrenut lokalno, prilagoditi konfiguraciju keša (`CACHE_STORE=database` ili drugi dostupni driver); ukoliko aplikacija koristi Redis direktno, potrebno je i njega pokrenuti ili prilagoditi kod.

Zatim izvršiti:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Aplikacija će podrazumevano biti dostupna na `http://127.0.0.1:8000`, a API rute na `/api`.

## Testiranje API-ja

Za testiranje može se koristiti **Postman**:

1. Poslati zahtev za registraciju ili prijavljivanje.
2. Preuzeti dobijeni Sanctum token.
3. Za zaštićene rute dodati zaglavlje `Authorization: Bearer <token>`.
4. Testirati operacije u skladu sa korisničkom ulogom.

Na primer:

```http
POST /api/login
GET /api/my-accounts
GET /api/currencies
GET /api/categories
```

## Napomene o bezbednosti

- Pristup podacima ograničen je kroz autentifikaciju, uloge i provere ovlašćenja.
- Stanje računa i transakcije zahtevaju ažurne podatke; keširanje se mora pažljivo primenjivati uz invalidaciju.
- `.env`, privatni sertifikacioni ključ i ostale tajne vrednosti ne smeju biti uključeni u Git istoriju.
- Pre javnog postavljanja servisa potrebno je dodatno podesiti produkcioni web server, HTTPS sertifikate i mrežnu bezbednost.
