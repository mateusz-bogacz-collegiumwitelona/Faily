<div class="col-md-3">
    <div class="card shadow-sm mb-4 fade-in-up">
        <div class="card-header text-color_2">
            <h5>{{ __('messages.eventlist.filterTitle') }}</h5>
        </div>
        <div class="card-body shadow-sm text-color ">
            <form action="{{ route('events.feed') }}" method="GET">
                <div class="mb-3">
                    <label for="search" class="form-label">{{ __('messages.eventlist.searchButton') }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}">
                </div>
                <div class="mb-3">
                    <label for="date_from" class="form-label">{{ __('messages.eventlist.startDateLabel') }}</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="mb-3">
                    <label for="date_to" class="form-label">{{ __('messages.eventlist.endDateLabel') }}</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input chimkowy-switch" type="checkbox" id="has_ride_sharing" name="has_ride_sharing" value="1" {{ request('has_ride_sharing') ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_ride_sharing">{{ __('messages.eventlist.withTransportCheckbox') }}</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input chimkowy-switch" type="checkbox" id="has_available_spots" name="has_available_spots" value="1" {{ request('has_available_spots') ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_available_spots">{{ __('messages.eventlist.withFreeSpotsCheckbox') }}</label>
                </div>
                <button type="submit" class="btn btn-gradient text-color_2">{{ __('messages.eventlist.applyFiltersButton') }}</button>
                <a href="{{ route('events.feed') }}" class="btn btn-gradient-secondary text-color_2">
                    {{ __('messages.eventlist.resetFiltersButton') }}
                </a>
            </form>
        </div>
    </div>

    <div class="card shadow-sm fade-in-up">
        <div class="card-header text-color_2">
            <h5>{{ __('messages.eventlist.quickLinksLabel') }}</h5>
        </div>
        <div class="card-body d-grid gap-2 text-color">
            <a href="{{ route('events.create') }}" class="btn text-color_2 btn-gradient" >{{ __('messages.eventlist.createEventButton') }}</a>
            <a href="{{ route('my_events') }}" class="btn text-color_2 btn-gradient">{{ __('messages.eventlist.myEventsButton') }}</a>
            <a href="{{ route('all_events') }}" class="btn text-color_2 btn-gradient">{{ __('messages.eventlist.allEventsButton') }}</a>
        </div>
    </div>
</div>
