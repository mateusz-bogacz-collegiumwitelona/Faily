<header>
    <nav class="navbar navbar-expand-lg " style="background-color: #4a588b;">
        <div class="container-fluid">
            <!-- Logo + napis -->
            <a class="navbar-brand fs-3 text-white" href="#">
                <img src="{{ Vite::asset('resources/images/includes/logo.png') }}" alt="Logo" width="50" height="50" class="d-inline-block align-text-top">
                Faily
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <ul class="navbar-nav justify-content-center align-items-center fs-4 flex-grow-1 pe-3">
                    <li class="nav-item mx-2">
                        <button class="btn me-2 text-white" type="button">Kupuję</button>
                    </li>
                    <li class="nav-item mx-2">
                        <button class="btn me-2 text-white" type="button">Wynajmuję</button>
                    </li>
                    <li class="nav-item mx-2">
                        <button class="btn me-2 text-white" type="button" onclick="location.href='wystawianie_oferty.php'">Sprzedaję</button>
                    </li>
                </ul>
                <div class="d-flex flex-lg-row justify-content-center align-items-center gap-3">
                    <div class="dropdown">
                        <a class="btn dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Moje konto
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logowanie.php">Zaloguj się</a></li>
                            <li><a class="dropdown-item" href="konto.php">Przejdź do konta</a></li>
                            <li><a class="dropdown-item" href="ogloszenia.php">Moje ogłoszenia</a></li>
                            <li><a class="dropdown-item" href="pomoc.php">Pomoc</a></li>
                            <li><a class="dropdown-item" href="ustawienia.php">Ustawienia</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item">Wyloguj się</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
