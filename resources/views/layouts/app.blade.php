<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('https://172.31.176.49/sfpproject/public') }}">

    <title>SFP IS</title>

    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('img/SFP-LOGO-2024.png') }}" rel="icon">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
        <header id="header" class="header fixed-top d-flex align-items-right">
            <div class="d-flex justify-center">
                <a href="{{ route('child.index') }}" class="logo d-flex align-items-center">
                    <img src="{{ asset('img/DSWD_Logo.png') }}" alt="dswd_logo">

                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="logo d-flex align-items-center">
                    <img src="{{ asset('img/SFP-LOGO-2024.png') }}" alt="sfp_logo">
                    {{-- <img src="{{ asset('img/[PNG] bagong pilipinas (1).png') }}" alt="bagongpilipinas"> --}}
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <i class="bi bi-list toggle-sidebar-btn"></i>
                <span class="header-title d-none d-lg-block">SFP IS</span>
            </div>

            <!-- End Search Bar -->

            <!-- End Logo -->

            {{-- <nav class="header-nav ml-auto align-items-center justify-end">
                <ul class="d-flex list-unstyled mt-2">
                    <li class="nav-item dropdown pe-3">
                        <a href="#" role="button" class="nav-link nav-profile d-flex align-items-center pe-0 dropdown-toggle uppercase"
                            data-bs-toggle="dropdown">
                            @auth
                                @php
                                    $user = Auth::user();
                                    $fullName = trim("{$user->firstname} {$user->middlename} {$user->lastname}");
                                @endphp
                                <span class="d-none d-md-block ps-2">{{ $fullName }}</span>
                            @endauth
                        </a>
                        <ul class="dropdown-menu profile">
                            <li>
                                <form action="{{ route('users.show') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id"  value="{{ auth()->user()->id }}">
                                    <button class="dropdown-item d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="mr-2 size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>

                                        <span class="text-sm">
                                            Profile
                                        </span>
                                    </button>
                                </form>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center"
                                        style="background: none; border: none;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="mr-2 size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                        </svg>

                                        <span class="text-sm">Sign Out</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav><!-- End Icons Navigation --> --}}
            <nav class="header-nav ml-auto flex items-center">
                <ul class="flex list-none mt-2">
                    <li class="nav-item relative" x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            @click.away="open = false"
                            class="d-flex nav-link nav-profile items-center uppercase focus:outline-none"
                        >
                            @auth
                                @php
                                    $user = Auth::user();
                                    $fullName = trim("{$user->firstname} {$user->middlename} {$user->lastname}");
                                @endphp
                                <span class="d-none d-md-block ps-2">{{ $fullName }}</span>
                            @endauth
                            <svg class="ml-2 mr-10 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <ul
                            x-show="open"
                            x-transition
                            class="absolute left-0 mt-2 px-2 w-60 bg-white rounded-md shadow-lg z-50 profile"
                            @click.away="open = false"
                        >
                            <li>
                                <form action="{{ route('users.show') }}" method="POST" class="block w-full">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                    <button class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mt-1 items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="mr-2 w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                        Profile
                                    </button>
                                </form>
                            </li>
                            <li><hr class="border-t my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="block w-full">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-2 py-2 text-sm hover:bg-gray-100 flex rounded-md mb-1 items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="mr-2 w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                        </svg>
                                        Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

        </header>

        <aside id="sidebar" class="sidebar">


            <ul class="sidebar-nav" id="sidebar-nav">
                {{-- @if (!auth()->user()->hasRole('encoder'))
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="{{ route('dashboard') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="#899bbd" class="mr-2 size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                            <span class="text-sm">Dashboard</span>
                        </a>
                    </li>
                @endif --}}

                <li class="nav-item">
                    <a class="nav-link collapsed" href="{{ route('child.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="#899bbd" class="mr-2 size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                        <span class="text-sm">Children</span>
                    </a>
                </li><!-- End Child List Nav -->


                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal') || auth()->user()->hasRole('sfp coordinator'))
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="{{ route('centers.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="#899bbd" class="mr-2 size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>

                            <span class="text-sm">Centers</span>
                        </a>
                    </li><!-- End CDC Page Nav -->
                @endif

                @canany(['add-cycle-implementation', 'edit-cycle-implementation', 'view-cycle-implementation'])
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="{{ route('cycle.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="#899bbd" class="mr-2 size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            <span class="text-sm">Implementations</span>
                        </a>
                    </li><!-- End Cycle Nav -->
                @endcanany

                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('lgu focal') || auth()->user()->hasRole('sfp coordinator'))
                    <li class="nav-heading">Admin Tools</li>

                    <li class="nav-item">
                        <a class="nav-link collapsed" href="{{ route('users.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="#899bbd" class="mr-2 size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>

                            <span class="text-sm">Accounts</span>
                        </a>
                    </li><!-- End Accounts Nav -->
                @endif
                @if (auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a class="nav-link collapsed" href="{{ route('activitylogs.index')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#899bbd" class="mr-2 size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                            </svg>
                            <span class="text-sm">Audit Logs</span>
                        </a>
                    </li><!-- End Audit Logs Page Nav -->
                @endif
            </ul>


        </aside><!-- End Sidebar-->
        <div class="wrapper">
            <main id="main" class="main">
                @yield('content')
            </main>


            <footer id="footer" class="footer">
                <div class="footer-dswd">
                    &copy; 2025 Department of Social Welfare and Development.
                </div>
            </footer><!-- End Footer -->
        </div>

        <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
                class="bi bi-arrow-up-short"></i></a>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                window.submitCancelForm = function () {
                    var cancelForm = document.getElementById('cancel-form');

                    if (cancelForm) {
                        cancelForm.submit();
                    } else {
                        console.error('Form not found!');
                    }
                };
            });
        </script>

        {{-- @vite(['resources/js/app.js']) --}}
</body>

</html>
