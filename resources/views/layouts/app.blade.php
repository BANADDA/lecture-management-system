<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Lecture Management System') }}</title>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Fonts --}}
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Common Styles --}}
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #f8f9fa;
            font-size: 0.9rem;
        }

        #app {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
    </style>

    @auth
    <style>
        /* Fixed Navbar and Footer with slightly smaller heights */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50px;
            z-index: 999;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 35px;
            z-index: 999;
        }

        /* Main container offset by navbar & footer */
        .page-body {
            display: flex;
            flex: 1 1 auto;
            margin-top: 50px;   /* matches .navbar height */
            margin-bottom: 35px;/* matches .footer height */
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 50px;      /* below navbar */
            bottom: 35px;   /* above footer */
            left: 0;
            width: 200px;
            background-color: #343a40;
            color: #fff;
            overflow-y: auto;
            z-index: 998;
            padding: 0.75rem;
        }
        .sidebar .nav-link {
            color: #ccc;
            padding: 0.5rem;
        }
        .sidebar .nav-link.active {
            background-color: #495057;
            color: #fff;
        }

        /* Main content: minimal left padding & no extra centering */
        .main-content {
            flex: 1 1 auto;
            margin-left: 100px;  /* matches sidebar width */
            overflow-y: auto;
            padding: 0.5rem;     /* minimal padding so text isn't jammed against edge */
            background: #fff;
        }
    </style>
    @endauth

    @guest
    <style>
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
    </style>
    @endguest
</head>
<body>
    <div id="app">
        @auth
        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-2">
            <div class="container-fluid">
                <span class="navbar-brand mb-0">
                    {{ config('app.name', 'Lecture Management System') }}
                </span>
                <!-- other navbar items -->
            </div>
        </nav>

        <!-- BODY (SIDEBAR + MAIN CONTENT) -->
        <div class="page-body">
            <aside class="sidebar">
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white">
                            Dashboard
                        </a>
                    </li>
                    <!-- More links here -->
                </ul>
            </aside>

            <main class="main-content">
                @yield('content')
            </main>
        </div>

        <!-- FOOTER -->
        <footer class="footer bg-dark text-light d-flex align-items-center justify-content-center">
            <div style="font-size: 0.8rem;">
                <small>
                    &copy; {{ date('Y') }} - {{ config('app.name', 'Lecture Management System') }}
                </small>
            </div>
        </footer>
        @endauth

        @guest
        <div class="auth-container">
            <div class="container">
                @yield('content')
            </div>
        </div>
        @endguest
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
