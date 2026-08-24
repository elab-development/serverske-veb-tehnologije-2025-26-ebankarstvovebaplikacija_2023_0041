## Opis aplikacije
Veb aplikacija za e-bankarstvo namenjena je upravljanju računima i obavljanju transakcija. Sistem omogućava korisnicima pregled računa i transakcija, izvršavanje transakcija, pretragu i filtriranje podataka, kao i konverziju valuta. Pored osnovnih korisničkih funkcionalnosti, sistem omogućava administratorima upravljanje podacima.

## Funkcionalnosti
Aplikacija omogucava korisniku:
- Registraciju u sistem.
- Prijavljivanje u sistem
- Odjavljivanje sa sistema
- Resetovanje lozinke
- Pregled odredjenih informacija o drugim korisnicima
- Pregled kategorija
- Pregled valuta
- Pregled svojih racuna
- Pretrazivanje racuna po korisniku
- Pregled odredjenih informacija o racunima drugih korisnika
- Pregled svojih transakcija
- Konverziju valuta
- Izvoz svojih transakcija u PDF formatu
- Pretrazivanje i filtriranje transakcija prema racunu, nazivu i kategoriji

Aplikacija omogucava administratoru:
- Sve funkcionalnosti koje su omogucene korisniku
- Kreiranje, azuriranje i brisanje podataka u sistemu
- Uvoz podataka u sistem iz CSV datoteka
- Izvoz podataka sistema u CSV formatu

Aplikacija omogucava super administratoru:
- Sve funkcionalnosti koje su omogucene administratoru
- Promenu uloga korisnika sistema

## Pokretanje aplikacije na lokalnoj masini

### 1. Kloniranje repozitorijuma
Projekat je potrebno klonirati sa GitHub repozitorijuma:
```bash
git clone https://github.com/elab-development/serverske-veb-tehnologije-2025-26-ebankarstvovebaplikacija_2023_0041
```

Nakon kloniranja potrebno je pozicionirati se u direktorijum projekta:
```bash
cd serverske-veb-tehnologije-2025-26-ebankarstvovebaplikacija_2023_0041
```

### 2. Instalacija PHP paketa
```bash
composer install
```

### 3. Kreirati `.env` datoteku na osnovu `.env.example` datoteke
```bash
copy .env.example .env
```

### 4. Generisanje aplikacionog kljuca
```bash
php artisan key:generate
```

### 5. Podesavanje baze podataka pomocu .env datoteke
Potrebno je pokrenuti MySQL server i kreirati bazu podataka sa nazivom ebanking.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=---
DB_DATABASE=ebanking
DB_USERNAME=---
DB_PASSWORD=---
```

### 6. Kreiranje tabela sa testnim podacima
```bash
php artisan migrate --seed
```

### 7. Pokretanje aplikacije
```bash
php artisan serve
```
Nakon pokretanja aplikacije ona je dostupna na http://127.0.0.1:8000/



