<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('inspinia/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('inspinia/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('inspinia/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('inspinia/css/style.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,500,700" rel="stylesheet">
    <!-- Leaflet CSS -->
<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  integrity="sha256-oEgtU8rH3L3DW2ZqYoRfCtLJxGPf9TrX0zQKB6lX2DA="
  crossorigin=""
/>

</head>

<body class="canvas-menu">
    <div id="wrapper">
        <!-- Sidebar -->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <a class="close-canvas-menu"><i class="fa fa-times"></i></a>
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <img alt="image" class="rounded-circle" src="{{ asset('inspinia/img/profile_small.jpg') }}"/>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="block m-t-xs font-bold">{{ Auth::user()->name ?? 'User' }}</span>
                                <span class="text-muted text-xs block">{{ session('user_role_name', 'Role') }} <b class="caret"></b></span>
                            </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="logo-element">IN+</div>
                    </li>

<!-- Menu Items -->
<li class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
    <a href="{{ route('admin.dashboard') }}">
        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('wardens.index') }}">
        <i class="fas fa-user-shield"></i> Wardens
    </a>
</li>

<li class="{{ request()->routeIs('incident-reports.index') ? 'active' : '' }}">
    <a href="{{ route('incident-reports.index') }}">
        <i class="fa fa-flag"></i> <span>Incident Reports</span>
    </a>
</li>

<li class="{{ request()->routeIs('shifts.index') ? 'active' : '' }}">
    <a href="{{ route('shifts.index') }}">
        <i class="fas fa-clock"></i></i> <span>Shift Management</span>
    </a>
</li>



<li class="{{ request()->routeIs('teams.index') ? 'active' : '' }}">
    <a href="{{ route('teams.index') }}">
        <i class="fa fa-users"></i> <span>Teams</span>
    </a>
</li>

<li class="{{ request()->routeIs('roles.index') ? 'active' : '' }}">
    <a href="{{ route('roles.index') }}">
        <i class="fa fa-id-badge"></i> <span>Roles</span>
    </a>
</li>

<li class="{{ request()->routeIs('settings.index') ? 'active' : '' }}">
    <a href="{{ route('settings.index') }}">
        <i class="fa fa-cogs"></i> <span>Settings</span>
    </a>
</li>

                </ul>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0; background: #2f4050;">
                   <div class="navbar-header">
    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#"><i class="fa fa-bars"></i></a>
</div>

                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i> Log out
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

{{-- Content Sections --}}
@yield('content-heading')
@yield('content')
@yield('footer')

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="{{ asset('inspinia/js/popper.min.js') }}"></script>
    <script src="{{ asset('inspinia/js/bootstrap.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/metismenu@3.0.7/dist/metisMenu.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
    <script src="{{ asset('inspinia/js/inspinia.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/pace-js@1.2.4/pace.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot@4.2.2/dist/es5/jquery.flot.js"></script>
    <script src="{{ asset('inspinia/js/jquery.flot.tooltip.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot/jquery.flot.resize.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        $('body.canvas-menu .sidebar-collapse').slimScroll({
            height: '100%',
            railOpacity: 0.9
        });

        (function () {
            const originalFetch = window.fetch;
            window.fetch = async function (url, options = {}) {
                try {
                    const response = await originalFetch(url, options);
                    if (response.status === 413) {
                        Swal.fire({
                            title: 'File Too Large',
                            text: 'The file you are trying to upload exceeds the maximum size limit.',
                            icon: 'error',
                        });
                        return;
                    }
                    return response;
                } catch (error) {
                    console.error('Fetch error: ', error);
                }
            };
        })();
    </script>
    <!-- Leaflet JS -->
<script
  src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
  integrity="sha256-o9N1jKw1jM0xP8jLu7AG9Ndy3P4dNSnsi9Nnb5OguTY="
  crossorigin=""
></script>
@stack('scripts')

</body>
</html>
