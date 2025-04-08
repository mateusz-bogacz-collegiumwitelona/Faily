<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lista wydarzeń</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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

  <div class="container mt-5">
    <section class="mb-5">
      <h3 class="mb-4">Na te wydarzenia się zapisałeś:</h3>
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card h-100" style="background-color: rgb(0, 140, 255);">
            <a href="wydarzenie.html">
              <img src="zdjecie1.png" class="card-img-top" alt="Tytuł wydarzenia" style="height:250px; object-fit:cover;">
            </a>
            <div class="card-body">
              <h5 class="card-title">Tytuł wydarzenia</h5>
              <p class="card-text">Miasto, ulica, nr.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100" style="background-color: rgb(0, 140, 255);">
            <a href="wydarzenie.html">
              <img src="zdjecie2.png" class="card-img-top" alt="Tytuł wydarzenia" style="height:250px; object-fit:cover;">
            </a>
            <div class="card-body">
              <h5 class="card-title">Inne wydarzenie</h5>
              <p class="card-text">Miasto, ulica, nr.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section>
      <h3 class="mb-4">Te wydarzenia dodałeś do listy:</h3>
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card h-100" style="background-color: rgb(0, 140, 255);">
            <a href="wydarzenie.html">
              <img src="zdjecie3.png" class="card-img-top" alt="Tytuł wydarzenia" style="height:250px; object-fit:cover;">
            </a>
            <div class="card-body">
              <h5 class="card-title">Kolejne wydarzenie</h5>
              <p class="card-text">Miasto, ulica, nr.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100" style="background-color: rgb(0, 140, 255);">
            <a href="wydarzenie.html">
              <img src="zdjecie1.png" class="card-img-top" alt="Tytuł wydarzenia" style="height:250px; object-fit:cover;">
            </a>
            <div class="card-body">
              <h5 class="card-title">Jeszcze jedno wydarzenie</h5>
              <p class="card-text">Miasto, ulica, nr.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="bg-dark text-white text-center py-3">
    © 2025 Find an Idiot Like You!
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
