<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Admin Sidebar (optional) -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Public Panel</a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pages.index') }}">Pages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('media.index') }}">Media</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('team.index') }}">Team</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="mt-3">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
