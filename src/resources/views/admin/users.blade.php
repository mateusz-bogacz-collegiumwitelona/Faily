<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.admin.menageUsers') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
@include('includes.navbar')

<main class="container flex-grow-1 my-5">
    <div class="row">
        @include('includes.admin_menu')
        <div class="col-md-9">
            @if (session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-users me-2"></i>{{ __('messages.admin.menageUsers') }}</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-dark">
                            <thead>
                            <tr>
                                <th>{{ __('messages.admin.name') }}</th>
                                <th>{{ __('messages.admin.email') }}</th>
                                <th>{{ __('messages.admin.role') }}</th>
                                <th>{{ __('messages.admin.status') }}</th>
                                <th>{{ __('messages.admin.reports') }}</th>
                                <th>{{ __('messages.admin.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->role === 'admin')
                                            <span class="badge bg-purple">{{ __('messages.admin.admin') }}</span>
                                        @else
                                            <span class="badge bg-primary">{{ __('messages.admin.user') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->status === 'banned')
                                            <span class="badge bg-danger">{{ __('messages.admin.block') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('messages.admin.active') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->reports_count ?? 0 }}</td>
                                    <td>
                                        <div class="d-flex">
                                            @if ($user->status !== 'banned' && $user->role !== 'admin')
                                                <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" class="me-2">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.admin.blockQuestion') }}')">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @elseif ($user->status === 'banned')
                                                <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" class="me-2">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($user->role !== 'admin' && $user->status !== 'banned')
                                                <form action="{{ route('admin.users.make-admin', $user->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('{{ __('messages.admin.adminAddQuestion') }}')">
                                                        <i class="fas fa-user-shield"></i>
                                                    </button>
                                                </form>
                                            @elseif ($user->role === 'admin' && $user->id !== Auth::id())
                                                <form action="{{ route('admin.users.remove-admin', $user->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('{{ __('messages.admin.adminDeleteQuestion') }}')">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('includes.footer')
</body>
</html>
