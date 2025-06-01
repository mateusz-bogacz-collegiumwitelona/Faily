<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.title.eventDetails') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-main">

@include('includes.navbar')

<main class="container mt-5 mb-5 text-color">
    <div class="row">
        <div class="col-md-8">
            @if($event->photos->count() > 0)
                <div id="eventCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($event->photos as $key => $photo)
                            <button type="button" data-bs-target="#eventCarousel" data-bs-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($event->photos as $key => $photo)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                @if(isset($event->photos) && count($event->photos) > 0)
                                    <img src="{{ asset('storage/' . $photo->path) }}"
                                         alt="{{ $event->title }}"
                                         class="card-img-top"
                                         style="height: 250px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('images/includes/brak_zdjecia.jpg') }}"
                                         alt="{{ __('messages.showevent.noPhoto') }}"
                                         class="card-img-top w-100"
                                         style="height: 250px; object-fit: cover;">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#eventCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#eventCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            @else
                <img src="{{ asset('images/includes/brak_zdjecia.jpg') }}"
                     alt="{{ __('messages.showevent.noPhoto') }}"
                     class="card-img-top w-100"
                     style="height: 250px; object-fit: cover;">
            @endif

            <div class="card text-color  mb-4 shadow-sm">
                <div class="card-header flex-column">
                    <h4>{{ __('messages.showevent.myEvents') }}</h4>
                </div>
                <div class="card-body shadow-sm">
                    <p class="text-color"><strong>{{ __('messages.showevent.eventTitle') }}</strong> {{ $event->title }}</p>
                    <p class="text-color"><strong>{{ __('messages.showevent.eventLocation') }}</strong> {{ $event->location_name }}</p>
                    <p class="text-color"><strong>{{ __('messages.showevent.eventDesc') }}</strong> {{ $event->description}}</p>
                </div>
            </div>

            <div class="card text-color shadow-sm mb-4">
                <div class="card-header">
                    <h4>{{ __('messages.showevent.eventInfo') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.showevent.eventDate') }}</strong> {{ \Carbon\Carbon::parse($event->date)->format('d.m.Y H:i') }}</p>
                            <p><strong>{{ __('messages.showevent.eventLocation') }}</strong> {{ $event->location_name }}</p>
                            <p><strong>{{ __('messages.showevent.eventOrganizer') }}</strong> {{ $event->user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            @php
                                $totalAttendees = $event->acceptedAttendees()->sum('attendees_count');
                                $availableSpots = max(0, $event->people_count - $totalAttendees);
                            @endphp
                            <p><strong>{{ __('messages.showevent.participantsLimit') }}</strong> {{ $event->people_count }}</p>
                            <p><strong>{{ __('messages.showevent.registeredPeople') }}</strong> {{ $totalAttendees }}</p>
                            <p><strong>{{ __('messages.showevent.freeSpots') }}</strong> {{ $availableSpots }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($event->has_ride_sharing && $event->rides->count() > 0)
                <div class="card shadow-lg mb-4 shadow-sm">
                    <div class="card-header py-3 border-bottom border-secondary d-flex justify-content-between align-items-center">
                        <h4 class="text-color_2 m-0 font-weight-bold"><i class="fas fa-car me-2"></i>{{ __('messages.showevent.availableRides') }}</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead>
                                <tr>
                                    <th class="py-3 px-4 border-0">{{ __('messages.showevent.driver') }}</th>
                                    <th class="py-3 px-4 border-0">{{ __('messages.showevent.vehicle') }}</th>
                                    <th class="py-3 px-4 border-0">{{ __('messages.showevent.seats') }}</th>
                                    <th class="py-3 px-4 border-0">{{ __('messages.showevent.location') }}</th>
                                    <th class="py-3 px-4 border-0">{{ __('messages.showevent.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($event->rides as $ride)
                                    <tr class="border-top border-secondary">
                                        <td class="py-3 px-4">
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('user.profile', $ride->driver) }}" class="text-decoration-none">
                                                    @if($ride->driver->photo_path)
                                                        <img src="{{ asset('storage/' . $ride->driver->photo_path) }}"
                                                             class="rounded-circle border border-2 border-light hover-lift"
                                                             alt="{{ __('messages.eventlist.profilePhotoLabel') }}"
                                                             width="70" height="70"
                                                             style="object-fit: cover; transition: transform 0.2s ease;">
                                                    @else
                                                        <img src="{{ asset('images/includes/default-avatar.png') }}"
                                                             class="rounded-circle border border-2 border-light hover-lift"
                                                             alt="{{ __('messages.eventlist.profilePhotoLabel') }}"
                                                             width="70" height="70"
                                                             style="object-fit: cover; transition: transform 0.2s ease;">
                                                    @endif
                                                </a>
                                                <a href="{{ route('user.profile', $ride->driver) }}" class="text-decoration-none ms-3">
                                                    <span class="fw-bold text-color_2 hover-underline">{{ $ride->driver->name }}</span>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-car-side me-2 text-info"></i>
                                                <span>{{ $ride->vehicle_description }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            @php
                                                $takenSeats = $ride->requests()->where('status', 'accepted')->count();
                                                $availableSeats = max(0, $ride->available_seats - $takenSeats);
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                    <span class="badge {{ $availableSeats > 0 ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                        {{ $availableSeats }} / {{ $ride->available_seats }}
                                    </span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                                <span>{{ $ride->meeting_location_name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            @auth
                                                @if(Auth::id() !== $ride->driver_id)
                                                    @php
                                                        $userRequest = $ride->requests()->where('passenger_id', Auth::id())->first();
                                                    @endphp

                                                    @if(!$userRequest)
                                                        @if($availableSeats > 0)
                                                            <a href="{{ route('ride-requests.create', ['ride_id' => $ride->id]) }}"
                                                               class="btn btn-sm text-color_2 btn-gradient">
                                                                <i class="fas fa-hand-point-up me-1"></i>{{ __('messages.showevent.signUp') }}
                                                            </a>
                                                        @else
                                                            <span class="badge rounded-pill px-3 py-2 btn-gradient-danger" >
                                                    <i class="fas fa-times-circle me-1 text-color_2"></i> {{ __('messages.showevent.noSpots') }}
                                                </span>
                                                        @endif
                                                    @elseif($userRequest->status == 'pending')
                                                        <div class="d-flex flex-column align-items-start gap-2">
                                                <span class="badge rounded-pill px-3 py-2 btn-gradient-pending">
                                                    <i class="fas fa-clock me-1 text-color_2"></i>{{ __('messages.showevent.pending') }}
                                                </span>
                                                            <form action="{{ route('ride-requests.destroy', $userRequest) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                        class="btn btn-sm text-color_2 btn-gradient-danger">
                                                                    <i class="fas fa-times me-1"></i>{{ __('messages.showevent.cancel') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @elseif($userRequest->status == 'accepted')
                                                        <div class="d-flex flex-column align-items-start gap-2">
                                                <span class="badge rounded-pill px-3 py-2 text-color_2 btn-gradient-check">
                                                    <i class="fas fa-check-circle me-1"></i>{{ __('messages.showevent.accepted') }}
                                                </span>
                                                            <form action="{{ route('ride-requests.destroy', $userRequest) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm text-color_2 btn-gradient-danger">
                                                                    <i class="fas fa-sign-out-alt me-1"></i>{{ __('messages.showevent.resign') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @elseif($userRequest->status == 'rejected')
                                                        <span class="badge rounded-pill px-3 py-2 btn-gradient-danger">
                                                <i class="fas fa-ban me-1 text-color_2"></i>{{ __('messages.showevent.rejected') }}
                                            </span>
                                                    @endif
                                                @else
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('ride-requests.index', ['ride_id' => $ride->id]) }}"
                                                           class="btn btn-sm text-color_2 btn-gradient-info"
                                                           style=" border-radius: 20px 0 0 20px; padding: 5px 10px;">
                                                            <i class="fas fa-users me-1"></i>{{ __('messages.showevent.applications') }}
                                                        </a>
                                                        <a href="{{ route('rides.show', $ride) }}"
                                                           class="btn btn-sm text-color_2 btn-gradient-check"
                                                           style=" padding: 5px 10px;">
                                                            <i class="fas fa-edit me-1"></i>{{ __('messages.showevent.showRide') }}
                                                        </a>
                                                        <a href="{{ route('rides.edit', $ride) }}"
                                                           class="btn btn-sm text-color_2 btn-gradient-secondary"
                                                           style="padding: 5px 10px;">
                                                            <i class="fas fa-edit me-1"></i>{{ __('messages.showevent.edit') }}
                                                        </a>
                                                        <form action="{{ route('rides.destroy', $ride) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm text-color_2 btn-gradient-danger"
                                                                    style=" border-radius: 0 20px 20px 0; padding: 5px 10px;"
                                                                    onclick="return confirm('{{ __('messages.showevent.deleteInfo') }}')">
                                                                <i class="fas fa-trash-alt me-1"></i>{{ __('messages.showevent.delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endauth
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm text-color mb-4">
                <div class="card-header">
                    <h4>{{ __('messages.showevent.actions') }}</h4>
                </div>
                <div class="card-body shadow-sm">
                    @auth
                        @if(Auth::id() === $event->user_id)
                            {{-- Akcje dla organizatora wydarzenia --}}
                            <div class="d-grid gap-2 mb-3">
                                <a href="{{ route('events.edit', $event) }}"  class="btn text-color_2 btn-gradient">{{ __('messages.showevent.editEvent') }}</a>
                            </div>
                            <div class="d-grid gap-2 mb-3">
                                <a href="{{ route('events.attendees.index', $event) }}"  class="btn text-color_2 btn-gradient">{{ __('messages.showevent.manageApplications') }}</a>
                            </div>
                            @if($event->has_ride_sharing)
                                <div class="d-grid gap-2 mb-3">
                                    <a href="{{ route('rides.create', ['event_id' => $event->id]) }}" class="btn text-color_2 btn-gradient">{{ __('messages.showevent.addRide') }}</a>
                                </div>
                            @endif
                            <div class="d-grid gap-2">
                                <form action="{{ route('events.destroy', $event) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn w-100 btn-gradient-danger btn-sm rounded-pill mt-2 text-color_2" onclick="return confirm('{{ __('messages.showevent.deleteEventInfo') }}')">{{ __('messages.showevent.deleteEvent') }}</button>
                                </form>
                            </div>
                        @else
                            {{-- Akcje dla zwykłego użytkownika --}}
                            @php
                                $attendee = $event->attendees()->where('user_id', Auth::id())->first();
                            @endphp

                            @if(!$attendee)
                                <div class="d-grid gap-2 mb-3">
                                    <a href="{{ route('events.attendees.create', $event) }}" class="btn text-color_2 btn-gradient ">{{ __('messages.showevent.signUpForEvent') }}</a>
                                </div>
                            @else
                                <div class="alert text-color_2 shadow-sm">
                                    <p>
                                        <strong>
                                            @if($attendee->status == 'pending') {{ __('messages.showevent.awaitingAcceptance') }}
                                            @elseif($attendee->status == 'accepted') {{ __('messages.showevent.acceptedAcceptance') }}
                                            @elseif($attendee->status == 'rejected'){{ __('messages.showevent.applicationRejected') }}
                                            @endif
                                        </strong>
                                    </p>
                                    <p>{{ __('messages.showevent.numberOfPeople') }} {{ $attendee->attendees_count }}</p>
                                    @if($attendee->message)
                                        <p>{{ __('messages.showevent.yourMessage') }} {{ $attendee->message }}</p>
                                    @endif

                                    <form action="{{ route('events.attendees.destroy', ['event' => $event, 'attendee' => $attendee]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn text-color_2 btn-gradient-danger">
                                            @if($attendee->status == 'accepted') {{ __('messages.showevent.unsubscribe') }}
                                            @else {{ __('messages.showevent.cancelApplication') }}
                                            @endif
                                        </button>
                                    </form>
                                </div>

                                @if($attendee->status == 'accepted' && $event->has_ride_sharing)
                                    <div class="card mb-3 text-color shadow-sm">
                                        <div class="card-header">
                                            <h5>{{ __('messages.showevent.rideOptions') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $userRide = $event->rides()->where('driver_id', Auth::id())->first();
                                            @endphp

                                            @if(!$userRide)
                                                <div class="mb-3">
                                                    <a href="{{ route('rides.create', ['event_id' => $event->id]) }}" class="btn w-100 text-color shadow-sm">{{ __('messages.showevent.offerRide') }}</a>
                                                </div>
                                            @else
                                                <div class="alert text-color_2 shadow-sm">
                                                    <p><strong>{{ __('messages.showevent.rideOffered') }}</strong></p>
                                                    <p>{{ __('messages.showevent.vehicleDetails') }} {{ $userRide->vehicle_description }}</p>
                                                    <p>{{ __('messages.showevent.availableSeats') }} {{ $userRide->available_seats }}</p>
                                                    <div class="mt-2">
                                                        <a href="{{ route('ride-requests.index', ['ride_id' => $userRide->id]) }}" class="btn btn-sm text-color_2 btn-gradient-secondary">{{ __('messages.showevent.manageApplications') }}</a>
                                                        <a href="{{ route('rides.edit', $userRide) }}" class="btn btn-sm text-color_2 btn-gradient">{{ __('messages.showevent.edit') }}</a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    @endauth
                </div>
            </div>

            <div class="card text-color shadow-sm">
                <div class="card-header">
                    <h4>{{ __('messages.showevent.organizer') }}</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('user.profile', $event->user) }}" class="text-decoration-none">
                            @if($event->user->photo_path)
                                <img src="{{ asset('storage/' . $event->user->photo_path) }}"
                                     class="rounded-circle me-3 border border-2 border-light hover-lift"
                                     alt="{{ __('messages.showevent.profilePhoto') }}"
                                     width="80" height="80"
                                     style="object-fit: cover; transition: transform 0.2s ease;">
                            @else
                                <img src="{{ asset('images/includes/default-avatar.png') }}"
                                     class="rounded-circle me-3 border border-2 border-light hover-lift"
                                     alt="{{ __('messages.showevent.profilePhoto') }}"
                                     width="80" height="80"
                                     style="object-fit: cover; transition: transform 0.2s ease;">
                            @endif
                        </a>
                        <div>
                            <a href="{{ route('user.profile', $event->user) }}" class="text-decoration-none">
                                <h5 class="mb-1 fw-bold text-color_2 hover-underline">{{ $event->user->name }}</h5>
                            </a>
                            @if($event->user->description)
                                <p class="mb-0 small text-color">{{ $event->user->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @auth
                    <div class="card-footer text-danger">
                        @if(Auth::id() !== $event->user_id && $event->user->role !== 'admin')
                            @include('includes.report-user-modal', ['userId' => $event->user->id, 'userName' => $event->user->name])
                        @endif
                    </div>
                @endauth
            </div>

        </div>
    </div>
</main>
@include('includes.footer')
</body>
</html>
