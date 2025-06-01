# Instrukcja uruchomienia projektu Faily

## Wymagania wstępne
- Docker i Docker Compose
- Git

## Krok 1: Klonowanie repozytorium
```bash
git clone https://github.com/mateusz-bogacz-collegiumwitelona/Faily/
cd Faily
```

## Krok 2: Przygotowanie środowiska
1. Upewnij się, że porty 63851, 63852, 63853, 63854 i 5173 są wolne na Twoim komputerze

2. Uruchom kontenery Docker:
```bash
docker-compose build 
docker-compose up 
```

## Krok 3: Konfiguracja projektu
Po uruchomieniu kontenerów należy otworzyć terminal i wykonać poniższe polecenia w celu uruchominia specjalnie przygotowane skryptu ułatwiającego przygotowanie środowiska:

```shell
# Wejdź do kontenera aplikacji
docker exec -it faily-app-dev bash

# Nadanie skryptowi uprawnień do uruchamiania
chmod +x deploy.sh

# Uruchomienie skryptu
./deploy.sh 

```

Można też wykonać polecenia ze skryptu ręcznie:

```bash
# Wejdź do kontenera aplikacji
docker exec -it faily-app-dev bash

# Zainstaluj zależności Composer
composer install

# Zainstaluj zależności npm
npm install

# Skompiluj zasoby (dla produkcji)
npm run build

# Włącz migracje w celu utworzenia tabel relacyjnej bazy danych
php artisan migrate

# Wygeneruj klucz aplikacji 
php artisan key:generate

# Wtłocz testowe dane do aplikacji
php artisan db:seed

# Stwórz link symboliczny dla zdjęć profilowych
php artisan storage:link
```

## Krok 5: Dostęp do aplikacji
- Aplikacja Laravel: http://localhost:63851
- Panel Mailpit (do testowania e-maili): http://localhost:63854

## Rozwiązywanie typowych problemów

### Problem z uprawnieniami do katalogów
```bash
docker exec -it faily-app-dev bash
cd /application
chmod -R 777 storage bootstrap/cache
```

### Problemy z kompilacją zasobów
```bash
docker exec -it faily-app-dev bash
cd /application
npm run build
php artisan view:clear
```

### Problemy z bazą danych
```bash
docker exec -it faily-app-dev bash
cd /application
php artisan migrate:fresh
```

### Remedium na całe złp
Stworzyłem skrypt który rozwiązuje znaczącą większość problemów po stronie Laravela
```shell
docker exec -it faily-app-dev bash
chmod +x deploy.sh
./deploy.sh 
```

## Uruchamianie projektu

### Pierwsze uruchomienie
Wykonaj kroki 1-4 z powyższej instrukcji. Pierwsze uruchomienie wymaga pełnej konfiguracji środowiska.

### Zatrzymywanie środowiska
```bash
docker-compose down
```

### Całkowite usunięcie środowiska wraz z danymi
```bash
docker-compose down -v
```

## Struktura projektu
- `environment/dev/app/` - pliki konfiguracyjne Docker
- `src/` - kod źródłowy Laravel
- `src/resources/js/components/` - komponenty Vue.js
- `src/resources/css/` - pliki CSS i SCSS

## Nadanie uprawnień admian użytkownikowi 
```shell
docker exec -it faily-app-dev bash
php artisan user:make-admin 'email_użytkownika'

```

Ta instrukcja zawiera wszystkie niezbędne kroki do uruchomienia i pracy z projektem. W razie dodatkowych pytań, warto zajrzeć do oficjalnej dokumentacji Laravel i Vue.js.