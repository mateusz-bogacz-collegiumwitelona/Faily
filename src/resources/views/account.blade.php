<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @if(isset($user) && Auth::check() && Auth::id() !== $user->id)
            Profil użytkownika - {{ $user->name }}
        @else
            {{ __('messages.title.account') }}
        @endif
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-main">
@include('includes.navbar')

<div>
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="profile-header position-relative mb-4"></div>

                <div class="text-center text-color_2 fade-in-up">
                    <div class="position-relative d-inline-block">
                        @php
                            $currentUser = isset($user) ? $user : Auth::user();
                        @endphp

                        @if($currentUser->photo_path)
                            <img src="{{ asset('storage/' . $currentUser->photo_path) }}"
                                 class="rounded-circle profile-pic" alt="Profile photo" width="350" height="350">
                        @else
                            <img src="{{ asset('images/includes/default-avatar.png') }}"
                                 class="rounded-circle profile-pic" alt="Profile photo" width="350" height="350">
                        @endif
                    </div>
                    <h3 class="mt-3 mb-1">{{ $currentUser->name }}</h3>
                    <p class="mb-3">{{ $currentUser->age }} {{ __('messages.account.years') }}</p>

                    <div class="row">
                        <div class="col-12 mb-4 lift-card">
                            <div class="about-me-highlight p-3 rounded-4 shadow-lg fade-in-up delay-1">
                                <h3 class="text-color_2 mb-3">{{ __('messages.account.aboutMe') }}</h3>
                                <p class="about-me-text lead m-0">„{{ $currentUser->description ?: '-' }}"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-4 ">
                <div class="col-md-8">
                    <div class="card shadow-sm mb-5 fade-in-up delay-2 ">
                        <div class="card-header bg-white">
                            <h4 class="mb-0">{{ __('messages.account.info') }}</h4>
                        </div>
                        <div class="card-body text-color">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">{{ __('messages.account.fullName') }}:</div>
                                <div class="col-md-8">{{ $currentUser->first_name }} {{ $currentUser->last_name }}
                                    <small class="text-color">ps. '{{ $currentUser->name }}'</small>
                                </div>
                            </div>

                            @if(!isset($user) || Auth::id() === $user->id)
                                {{-- Pokaż email i telefon tylko dla własnego profilu --}}
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">{{ __('messages.account.email') }}:</div>
                                    <div class="col-md-8">{{ $currentUser->email }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">{{ __('messages.account.phone') }}:</div>
                                    <div class="col-md-8">{{ $currentUser->phone }}</div>
                                </div>
                            @else
                                {{-- Dla obcego profilu pokaż datę dołączenia --}}
                                <div class="row mb-3">
                                    <div class="col-md-4 fw-bold">Dołączył:</div>
                                    <div class="col-md-8">{{ $currentUser->created_at->format('d.m.Y') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(isset($user) && Auth::check() && Auth::id() !== $user->id)
                        {{-- Przycisk powrotu dla obcego profilu --}}
                        <div class="text-center">
                            <a href="{{ url()->previous() }}" class="btn btn-gradient">
                                <i class="bi bi-arrow-left"></i> Powrót
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@include('includes.footer')
</body>
</html>
