<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.titles.applyEvent') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-main">

@include('includes.navbar')

<main class="container py-5 text-color">
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('events.show', $event) }}" class="btn btn-gradient">
            {{ __('messages.eventattendeescreate.backToEventButton') }}
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card text-color shadow-lg border-0 rounded shadow-sm">
                <div class="card-header border-bottom-0 bg-transparent">
                    <h4 class="mb-0">{{ __('messages.eventattendeescreate.titleLabel') }} {{ $event->title }}</h4>
                </div>
                <div class="card-body shadow-sm">
                    <form action="{{ route('events.attendees.store', $event) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="attendees_count" class="form-label">{{ __('messages.eventattendeescreate.currentParticipantsLabel') }}</label>
                            <input type="number" class="form-control " id="attendees_count" name="attendees_count"
                                   min="1" max="100" value="1" required>
                            <small class="form-text text-color">{{ __('messages.eventattendeescreate.maxParticipantsLabel') }}</small>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">{{ __('messages.eventattendeescreate.messageToOrganizerLabel') }}</label>
                            <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-gradient text-color_2">
                                {{ __('messages.eventattendeescreate.submitButton') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

@include('includes.footer')
</body>
</html>
