@extends('layouts.main')

@section('content')
<div id="page-inner">
    <!-- /. ROW  -->
    <div class="row">
        <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="board">
                <div class="panel panel-primary">
                    <div class="number">
                        <h3>
                            <h3>{{ $data['total_users'] }}</h3>
                            <small>Total App Users</small>
                        </h3>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users fa-5x blue"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="board">
                <div class="panel panel-primary">
                    <div class="number">
                        <h3>
                            <h3>{{ $data['total_drivers'] }}</h3>
                            <small>Driver And Trucks</small>
                        </h3>
                    </div>
                    <div class="icon">
                        <i class="fa fa-user fa-5x yellow"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="board">
                <div class="panel panel-primary">
                    <div class="number">
                        <h3>
                            <h3>{{ $data['total_trip_completed'] }}</h3>
                            <small>Total Completed Trips</small>
                        </h3>
                    </div>
                    <div class="icon">
                        <i class="fa fa-smile-o fa-5x green"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="board">
                <div class="panel panel-primary">
                    <div class="number">
                        <h3>
                            <h3>{{ $data['total_trip_rejected'] }}</h3>
                            <small>Total Rejected Trips</small>
                        </h3>
                    </div>
                    <div class="icon">
                        <i class="fa fa-frown-o fa-5x red"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="board">
                <div class="panel panel-primary">
                    <div class="number">
                        <h3>
                            <h3>{{ $data['total_trip_canceled'] }}</h3>
                            <small>Total Canceled  Trips</small>
                        </h3>
                    </div>
                    <div class="icon">
                        <i class="fa fa-calendar fa-5x red"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="board">
                <div class="panel panel-primary">
                    <div class="number">
                        <h3>
                            <h3>{{ $data['total_trip_ongoing'] }}</h3>
                            <small>Number Of Users on trip</small>
                        </h3>
                    </div>
                    <div class="icon">
                        <i class="fa fa-tachometer fa-5x blue"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="board">
                <div class="panel panel-primary">
                    <div class="number">
                        <h3>
                            <h3>{{ $data['total_driver_free'] }}</h3>
                            <small>Current Free Drivers</small>
                        </h3>
                    </div>
                    <div class="icon">
                        <i class="fa fa-gamepad fa-5x green"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="board">
                <div class="panel panel-primary">
                    <div class="number">
                        <h3>
                            <h3>{{ $data['total_driver_offline'] }}</h3>
                            <small>Current Offline Drivers</small>
                        </h3>
                    </div>
                    <div class="icon">
                        <i class="fa fa-coffee fa-5x yellow"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="board">
                <div class="panel panel-primary">
                    <div class="number">
                        <h3 style="font-size:21px;">Current Vehicle On Trip</h3> <a href="" style="background:#ff0000 !important; color:#fff; margin-top:-16px; padding:2px 8px 2px 8px; float:right;">Refresh Map</a>
                        <img src="assets/img/assigned_location1.png" width="100%" height="477"> </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /. PAGE INNER  -->
@stop

<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('#content_header').html('{{ trans('dashboard.content header') }}');
    });
</script>