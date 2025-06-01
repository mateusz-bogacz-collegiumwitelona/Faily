<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.title.aboutDevelopers') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-main">
@include('includes.navbar')
<div class="page-container">
    <main class="container py-5">
        <div class="creator-section py-5 px-3 px-md-5 my-5">

            <div class="team-stats">
                <div class="stat-item">
                    <div class="stat-number">3</div>
                    <div class="stat-label">{{ __('messages.developers.developersNumber') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">∞</div>
                    <div class="stat-label">{{ __('messages.developers.ideasNumber') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">{{ __('messages.developers.linesOfCodeNumber') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">{{ __('messages.developers.engagement') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">{{ __('messages.developers.hamstersNumber') }}</div>
                </div>
            </div>

            <div class="team-gallery">
                <!-- Wódz -->
                <div class="team-member floating">
                    <div class="member-card">
                        <div class="member-front">
                            <img src="{{ asset('images/includes/wodz-faily.png') }}" alt="Wódz" class="member-img">
                            <h3 class="member-name">Mateusz Bogacz-Drewniak</h3>
                            <div class="member-role">{{ __('messages.developers.backendDeveloper') }}</div>
                        </div>
                        <div class="member-back">
                            <p class="member-description">
                                {{ __('messages.developers.wodzShortDesc') }}
                            </p>
                            <div class="member-social">
                                <a href="https://github.com/mateusz-bogacz-collegiumwitelona" class="social-icon" title="Github" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('images/includes/github-mark-white.png') }}" alt="Github Logo" class="social-logo" width="20" height="20">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gabi -->
                <div class="team-member floating delay-1">
                    <div class="member-card">
                        <div class="member-front">
                            <img src="{{ asset('images/includes/gabi-faily.png') }}" alt="Gabi" class="member-img">
                            <h3 class="member-name">Gabriela Grabarska</h3>
                            <div class="member-role">{{ __('messages.developers.frontendDeveloper') }}</div>
                        </div>
                        <div class="member-back">
                            <p class="member-description">
                                {{ __('messages.developers.gabiShortDesc') }}
                            </p>
                            <div class="member-social">
                                <a href="https://github.com/GrabarskaGabriela" class="social-icon" title="Github" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('images/includes/github-mark-white.png') }}" alt="Github Logo" class="social-logo" width="20" height="20">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chimek -->
                <div class="team-member floating delay-2">
                    <div class="member-card">
                        <div class="member-front">
                            <img src="{{ asset('images/includes/chimek-faily.png') }}" alt="Chimek" class="member-img">
                            <h3 class="member-name">Mateusz Chimkowski</h3>
                            <div class="member-role">{{ __('messages.developers.uxuiDesigner') }}</div>
                        </div>
                        <div class="member-back">
                            <p class="member-description">
                                {{ __('messages.developers.chimekShortDesc') }}
                            </p>
                            <div class="member-social">
                                <a href="https://github.com/Chimek006" class="social-icon" title="Github" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('images/includes/github-mark-white.png') }}" alt="Github Logo" class="social-logo" width="20" height="20">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="creator-full-descriptions mt-5 p-4 text-color rounded-4 shadow-lg">
            <h2 class="text-center mb-4 display-6 fw-semibold text-color">
                {{ __('messages.developers.getMeetUs') }}
            </h2>

            <div class="text-center my-4">
                <a href="{{ asset('Faily-book.pdf') }}" target="_blank" class="btn btn-gradient text-color_2 px-5 py-2 shadow">
                    {{ __('messages.developers.book') }}
                </a>
            </div>

            <div class="creator-tabs">
                <ul class="nav nav-tabs justify-content-center border-0 mb-3" id="creatorTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="btn-gradient-nav text-color_2 dev-desc active" id="wodz-tab" data-bs-toggle="tab" data-bs-target="#wodz" type="button" role="tab" aria-controls="wodz" aria-selected="true">
                            {{ __('messages.developers.wodz') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="btn-gradient-nav text-color_2 dev-desc" id="gabi-tab" data-bs-toggle="tab" data-bs-target="#gabi" type="button" role="tab" aria-controls="gabi" aria-selected="false">
                            {{ __('messages.developers.gabi') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="btn-gradient-nav text-color_2 dev-desc" id="chimek-tab" data-bs-toggle="tab" data-bs-target="#chimek" type="button" role="tab" aria-controls="chimek" aria-selected="false">
                            {{ __('messages.developers.chimek') }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-4 rounded-bottom-3 dev-desc" id="creatorTabContent">
                    <div class="tab-pane fade show active" id="wodz" role="tabpanel" aria-labelledby="wodz-tab">
                        <p class="lead">{{ __('messages.developers.wodzDesc') }}</p>
                    </div>
                    <div class="tab-pane fade" id="gabi" role="tabpanel" aria-labelledby="gabi-tab">
                        <p class="lead">{{ __('messages.developers.gabiDesc') }}</p>
                    </div>
                    <div class="tab-pane fade" id="chimek" role="tabpanel" aria-labelledby="chimek-tab">
                        <p class="lead">{{ __('messages.developers.chimekDesc') }}</p>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center gap-3">
                <button type="submit" class="btn-gradient-nav text-color_2" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
                    <i class="bi bi-person-arms-up"></i>
                </button>
            </div>
            <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmUserDeletionLabel">{{ __('messages.developers.joke') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <video width="100%" height="auto" controls id="modalVideo">
                                <source src="{{ asset('video/dziwny_pomysl_wodza.mp4') }}" type="video/mp4">
                            </video>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.profilepartialsdelete.closeButton') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@include('includes.footer')

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.getElementById('wodz-tab').classList.add('active');
        document.getElementById('wodz').classList.add('show', 'active');
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('[data-bs-toggle="tab"]').forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                const targetId = this.getAttribute('data-bs-target').substring(1);
                const targetPane = document.getElementById(targetId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            });
        });
        document.querySelectorAll('.team-member').forEach(member => {
            member.addEventListener('click', function() {
                const memberName = this.querySelector('.member-name').textContent;
                let tabId;

                if (memberName.includes('Bogacz')) {
                    tabId = 'wodz-tab';
                } else if (memberName.includes('Grabarska')) {
                    tabId = 'gabi-tab';
                } else {
                    tabId = 'chimek-tab';
                }
                const tab = document.getElementById(tabId);
                if (tab) {
                    tab.click();
                    document.querySelector('.creator-full-descriptions').scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    });
</script>
<script>
    document.getElementById('confirmUserDeletionModal').addEventListener('hidden.bs.modal', function () {
        const video = document.getElementById('modalVideo');
        if (video) {
            video.pause();
            video.currentTime = 0;
        }
    });
    document.getElementById('confirmUserDeletionModal').addEventListener('shown.bs.modal', function () {
        const video = document.getElementById('modalVideo');
        if (video) {
            video.play();
        }
    });
</script>
</body>
</html>
