<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .active::after {
            content: '';
            display: block;
            margin: 0 auto;
            width: 30%;
            padding-top: 3px;
            border-bottom: 3px solid currentColor;
        }

        .menu li a::after {
            transition: width 0.3s ease-in-out;
        }

        .menu li a.active::after {
            width: 100%;
        }

        #sortable tr {
            cursor: move;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <div class="navbar bg-base-100">
            <div class="navbar-start">
                <div class="dropdown">
                    <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h8m-8 6h16" />
                        </svg>
                    </div>
                    <ul tabindex="0"
                        class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow">
                        <li class="{{ request()->is('/') ? 'active' : '' }}"><a href="{{ url('/') }}">To Do</a>
                        </li>
                        <li class="{{ request()->is('complete') ? 'active' : '' }}"><a
                                href="{{ url('/complete') }}">Completed</a></li>
                    </ul>
                </div>
                <a class="btn btn-ghost text-xl">To Do App</a>
            </div>
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1">
                    <li class="{{ request()->is('/') ? 'active' : '' }}"><a href="{{ url('/') }}">To Do</a></li>
                    <li class="{{ request()->is('complete') ? 'active' : '' }}"><a
                            href="{{ url('/complete') }}">Completed</a></li>
                </ul>
            </div>
            <div class="navbar-end">
            </div>
        </div>
        <main>
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    @yield('js')
</body>

</html>
