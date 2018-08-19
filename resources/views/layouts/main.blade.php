<?php
    $router = Route::currentRouteName();
?>

<!DOCTYPE html>
<html>
<head>
    <meta id="token" name="token" value="{{ csrf_token() }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="" name="description" />
    <title>eTow</title>

    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" />
    {{--<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" />--}}
    <!-- FontAwesome Styles-->
    <link href="{{ asset('assets/css/font-awesome.css') }}" rel="stylesheet" />
    <!-- Morris Chart Styles-->
    {{--<link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />--}}
    <!-- Custom Styles-->
    <link href="{{ asset('assets/css/custom-styles.css') }}" rel="stylesheet" />
    <!-- Google Fonts-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    {{--<link rel="stylesheet" href="assets/js/Lightweight-Chart/cssCharts.css">--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    {{--<script src="{{ asset('assets/js/jquery-1.10.2.js') }}"></script>--}}
    @stack('css-stack')
</head>

<body>
<div id="wrapper">
    <nav class="navbar navbar-default top-navbar" role="navigation">
        <div class="navbar-header">
            <div id="content_header" class="business_name">{{ trans('core.header.'.$router) }}</div>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('dashboard') }}"><img src="{{ asset('assets/logo1.png') }}" width="70" alt="eTow" title="eTow Logo"></a>
        </div> <!-- navbar-header-->

        <ul class="nav navbar-top-links navbar-right">
            <!-- /.dropdown -->
            <li class="dropdown">
                <a class="dropdown-toggle logout_icon" data-toggle="dropdown" href="#" aria-expanded="false"> <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i></a>
                <div class="driver_name_r"><span> Welcome Admin</span> </div>

                <ul class="dropdown-menu dropdown-user">
                    <li><a href="{{ route('logout') }}"> <i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
    </nav>
    <!--/. NAV TOP  -->

    <nav class="navbar-default navbar-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="main-menu">
                <li><a class="{{ $router === 'dashboard' ? 'active-menu' : '' }}"  href="{{ route('dashboard') }}"><i class="fa fa-calendar"></i> Dashboard</a></li>
                <li><a class="{{ $router === 'user.index' ? 'active-menu' : '' }}" href="{{ route('user.index') }}"><i class="fa fa-users"></i> Users</a></li>
                <li><a class="{{ in_array($router, ['driver.index', 'driver.create', 'driver.edit']) ? 'active-menu' : '' }}" href="{{ route('driver.index') }}"><i class="fa fa-star"></i> Drivers</a></li>
                <li><a class="{{ in_array($router, ['trip.index', 'trip.by_user', 'trip.by_user_type', 'trip.by_driver_type']) ? 'active-menu' : '' }}" href="{{ route('trip.index') }}"><i class="fa fa-truck"> </i> Trips</a></li>
                <li><a class="{{ in_array($router, ['setting.index', 'setting.update_price']) ? 'active-menu' : '' }}" href="{{ route('setting.index') }}"><i class="fa fa-cogs"></i> Settings</a></li>
                <li> <a class="{{ $router === 'feedback.index' ? 'active-menu' : '' }}" href="{{ route('feedback.index') }}"><i class="fa fa-atfa fa-envelope"></i> Feedback</a></li>
            </ul>
        </div> <!-- sidebar-collapse-->
    </nav>
    <!-- /. NAV SIDE  -->

    <div id="page-wrapper">
        @yield('content')
        <!-- /. PAGE INNER  -->
        <footer><p>All right reserved. Developed by: <a href="http://bcwebco.com" target="_blank">bcwebco.com</a></p></footer>
    </div>
    <!-- /. PAGE WRAPPER  -->
</div>

<script src="{{ asset('assets/js/jquery-1.10.2.js') }}"></script>
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- Bootstrap Js -->
{{--<script src="assets/js/bootstrap.min.js"></script>--}}
{{--
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
--}}
{{--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>--}}
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

<!-- Metis Menu Js -->
<script src="{{ asset('assets/js/jquery.metisMenu.js') }}"></script>
<!-- Morris Chart Js -->
{{--<script src="assets/js/morris/raphael-2.1.0.min.js"></script>
<script src="assets/js/morris/morris.js"></script>--}}

{{--<script src="assets/js/easypiechart.js"></script>--}}
{{--<script src="assets/js/easypiechart-data.js"></script>--}}

{{--<script src="assets/js/Lightweight-Chart/jquery.chart.js"></script>--}}

<!-- Custom Js -->
{{--<script src="assets/js/custom-scripts.js"></script>--}}

<!-- Chart Js -->
{{--<script type="text/javascript" src="assets/js/Chart.min.js"></script>--}}
{{--<script type="text/javascript" src="assets/js/chartjs.js"></script>--}}

<!-- DATA TABLE SCRIPTS -->
<script src="{{ asset('assets/js/dataTables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/js/dataTables/dataTables.bootstrap.js') }}"></script>

<script src="https://www.gstatic.com/firebasejs/5.3.1/firebase.js"></script>
<script>
    // Initialize Firebase
    var config = {
        apiKey: "{{ config('services.firebase.api_key') }}",
        authDomain: "{{ config('services.firebase.auth_domain') }}",
        databaseURL: "{{ config('services.firebase.database_url') }}",
        storageBucket: "{{ config('services.firebase.storage_bucket') }}",
    };
    firebase.initializeApp(config);
</script>

@stack('js-stack')

</body>
</html>