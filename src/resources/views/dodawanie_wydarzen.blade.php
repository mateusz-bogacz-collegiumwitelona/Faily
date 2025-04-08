<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dodaj wydarzenie</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    function updateFileList() {
      const input = document.getElementById("eventPhotos");
      const list = document.getElementById("fileList");
      list.innerHTML = "";
      for (const file of input.files) {
        const li = document.createElement("li");
        li.textContent = file.name;
        list.appendChild(li);
      }
    }
  </script>
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
    <h2>Dodaj wydarzenie</h2>
    <form>
      <div class="mb-3">
        <label class="form-label">Lokalizacja</label>
        <input type="text" class="form-control" placeholder="Miasto" required>
        <input type="text" class="form-control mt-2" placeholder="Ulica" required>
        <input type="text" class="form-control mt-2" placeholder="Numer budynku" required>
        <input type="text" class="form-control mt-2" placeholder="Numer mieszkania (opcjonalnie)">
      </div>
      <div class="mb-3">
        <label for="eventPhotos" class="form-label">Dodaj zdjęcia</label>
        <input type="file" class="form-control" id="eventPhotos" multiple onchange="updateFileList()">
        <ul id="fileList" class="mt-2"></ul>
      </div>
      <div class="mb-3">
        <label for="peopleCount" class="form-label">Ilość osób</label>
        <input type="number" class="form-control" id="peopleCount" min="1" required>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Opis wydarzenia</label>
        <textarea class="form-control" id="description" rows="4" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-lg">Dodaj ogłoszenie do oferty</button>
    </form>
  </div>
  <footer class="bg-dark text-white text-center py-3 mt-5">© 2025 Find an Idiot Like You!</footer>
</body>
</html>
