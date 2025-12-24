<!doctype html>
<html lang="bn">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Roman Electronic & Furnitures @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.maateen.me/solaiman-lipi/font.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <style>
        body {
            font-family: 'solaimanlipi', sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }

        /* Tailwind-like Bootstrap color overrides */
        :root {
            --bs-primary: #222222;
            --bs-primary-hover: #000;
            --bs-success: #10b981;
            --bs-success-hover: #059669;
            --bs-info: #0ea5e9;
            --bs-info-hover: #0284c7;
            --bs-warning: #f59e0b;
            --bs-warning-hover: #d97706;
            --bs-danger: #ef4444;
            --bs-danger-hover: #dc2626;
        }

        .btn {
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        /* Scrolling Notices */
        .scrolling-notices {
            background: linear-gradient(to right, #f59e0b, #facc15);
            overflow: hidden;
            white-space: nowrap;
            padding: 0.5rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .scrolling-notices h4 {
            display: inline-block;
            margin-right: 3rem;
            font-weight: 600;
            animation: scrollText 20s linear infinite;
        }

        .scrolling-notices:hover h4 {
            animation-play-state: paused; /* pause on hover */
        }

        @keyframes scrollText {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        footer {
            background-color: #fff;
            color: #555;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">

        <!-- Logo -->
        <a class="navbar-brand fw-bold text-primary" href="{{ route('dashboard') }}">
            MyAdmin
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar" aria-controls="mainNavbar"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold text-primary' : '' }}">
                        Dashboard
                    </a>
                </li>

                @can('customer-list')
                <li class="nav-item">
                    <a href="{{ route('customers.index') }}"
                       class="nav-link {{ request()->routeIs('customers.*') ? 'active fw-semibold text-primary' : '' }}">
                        Customers
                    </a>
                </li>
                @endcan

                @can('location-list')
                <li class="nav-item">
                    <a href="{{ route('locations.index') }}"
                       class="nav-link {{ request()->routeIs('locations.*') ? 'active fw-semibold text-primary' : '' }}">
                        Locations
                    </a>
                </li>
                @endcan

                @can('product-list')
                <li class="nav-item">
                    <a href="{{ route('products.index') }}"
                       class="nav-link {{ request()->routeIs('products.*') ? 'active fw-semibold text-primary' : '' }}">
                        Products
                    </a>
                </li>
                @endcan

                @can('product-model-list')
                <li class="nav-item">
                    <a href="{{ route('products.model') }}"
                       class="nav-link {{ request()->routeIs('products.model') ? 'active fw-semibold text-primary' : '' }}">
                        Product Models
                    </a>
                </li>
                @endcan

                @can('user-list')
                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                       class="nav-link {{ request()->routeIs('users.*') ? 'active fw-semibold text-primary' : '' }}">
                        Users
                    </a>
                </li>
                @endcan

                @can('role-list')
                <li class="nav-item">
                    <a href="{{ route('roles.index') }}"
                       class="nav-link {{ request()->routeIs('roles.*') ? 'active fw-semibold text-primary' : '' }}">
                        User Roles
                    </a>
                </li>
                @endcan

            </ul>
        </div>
    </div>
</nav>



    <!-- Scrolling Notices -->
    <div class="scrolling-notices">
        <div class="container">
            @foreach ($notices as $notice)

                <h4>{{ $notice->name }}</h4>
            @endforeach
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mt-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-center py-3 mt-5">
        <small>© {{ date('Y') }} রোমান ইলেকট্রনিক্স ও ফার্নিচার</small>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    @yield('scripts')

</body>
</html>
