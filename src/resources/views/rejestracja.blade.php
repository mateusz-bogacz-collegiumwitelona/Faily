<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rejestracja</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: rgb(224, 223, 220);">
  <header>
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: rgb(0, 140, 255);">
      <div class="container-fluid">
        <a class="navbar-brand" href="index.html" style="color: rgb(0, 0, 0);">>Faily</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropstart">
              <a class="nav-link dropdown-toggle" href="#" id="kontoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: rgb(0, 0, 0);">
                Konto
              </a>
              <ul class="dropdown-menu" aria-labelledby="kontoDropdown">
                <li><a class="dropdown-item" href="konto.html">Przejdź do konta</a></li>
                <li><a class="dropdown-item" href="logowanie.html">Logowanie</a></li>
                <li><a class="dropdown-item" href="wyloguj.html">Wyloguj</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: rgb(0, 0, 0);">
                ...
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="moreDropdown">
                <li><a class="dropdown-item" href="ustawienia.html">Ustawienia</a></li>
                <li><a class="dropdown-item" href="dodawanie_wydarzen.html">Dodaj wydarzenie</a></li>
                <li><a class="dropdown-item" href="lista_wydarzen.html">Przejdź do listy wydarzeń</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Rejestracja</h3>
            <form action="rejestracja.php" method="post" enctype="multipart/form-data">
              <div class="mb-3">
                <label for="email" class="form-label">Adres email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Wprowadź email" required>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="password" class="form-label">Hasło</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Wprowadź hasło" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="confirm_password" class="form-label">Powtórz hasło</label>
                  <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Powtórz hasło" required>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="first_name" class="form-label">Imię</label>
                  <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Opcjonalnie">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="last_name" class="form-label">Nazwisko</label>
                  <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Opcjonalnie">
                </div>
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="age" class="form-label">Wiek</label>
                  <input type="number" class="form-control" id="age" name="age" placeholder="Opcjonalnie">
                </div>
                <div class="col-md-8 mb-3">
                  <label for="phone" class="form-label">Numer telefonu</label>
                  <input type="tel" class="form-control" id="phone" name="phone" placeholder="Opcjonalnie">
                </div>
              </div>
              <div class="mb-3">
                <label for="description" class="form-label">Opis</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Opcjonalnie"></textarea>
              </div>
              <div class="mb-3">
                <label for="photo" class="form-label">Wybierz zdjęcie</label>
                <input class="form-control" type="file" id="photo" name="photo" accept="image/*">
              </div>
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Zarejestruj się</button>
              </div>
            </form>
            <hr>
            <div class="text-center">
              <p>Masz już konto? <a href="logowanie.html">Zaloguj się</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
