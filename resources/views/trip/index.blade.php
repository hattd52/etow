<?php
$user_id = isset($user_id) ? $user_id : '';
$type    = isset($type) ? $type : '';
?>

@extends('layouts.main')

@section('content')
<div id="page-inner">
    <div class="row">
        <div class="col-sm-12">
            @include('notifications')
        </div>
        <div class="col-md-12">
            <!-- Advanced Tables -->
            <div class="sub_nav">
                <div id="menu_select">
                    <div class="trips_but_margin" id="btnAll" style="text-decoration: none; cursor: pointer"> <span class="trips_but">Total Trips ({{ $total }})</span></div>
                    <a id="btnOngoing" style="cursor: pointer;"><div class="trips_but_margin"> <span class="trips_but_ongoing" id="text_ongoing">Ongoing Trips</span></div></a>
                    <a id="btnSchedule" style="cursor: pointer;"><div class="trips_but_margin"> <span class="trips_but_scheduled" id="text_schedule">Scheduled Trips</span></div></a>
                    <a id="btnComplete" style="cursor: pointer;"><div class="trips_but_margin"> <span class="trips_but_completed" id="text_complete">Completed Trips</span></div></a>
                    <a id="btnReject" style="cursor: pointer;"><div class="trips_but_margin"> <span class="trips_but_rejected" id="text_reject">Rejected Trips</span></div></a>
                    <a id="btnCancel" style="cursor: pointer;"><div class="trips_but_margin"> <span class="trips_but_canceled" id="text_cancel">Canceled Trips</span></div></a>
                    <input type="hidden" id="type_search" value="" />
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="page_sort">
                    <div class="search_n">Search: <input name="key" type="search" class="input_320" placeholder="Enter Trip No/ Driver Name / ID / Company Name"></div>
                    <div class="date_n"><a id="btnSort" class="btn btn-success">Sort</a></div>
                    <div class="date_n">to: <input name="end_date" type="date" class="input_150" ></div>
                    <div class="date_n">Sort: <input name="start_date" type="date" class="input_150" ></div>
                </div>
                <div class="clearfix"></div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="table-trips">
                            <thead>
                                <tr>
                                    <th class="cl_min-80">{{ trans('trip.table.stt') }}<div class="fa fa-sign-out fa-sort"></div></th>
                                    <th class="cl_min-100">{{ trans('trip.table.date') }}<div class="fa fa-sign-out fa-sort"></div></th>
                                    <th class="cl_min-120" data-sortable="false">{{ trans('trip.table.trip_no') }}</th>
                                    <th class="cl_min-120" data-sortable="false">{{ trans('trip.table.driver_id') }}</th>
                                    <th class="cl_min-200" data-sortable="false">{{ trans('trip.table.driver_name') }}</th>
                                    <th class="cl_min-140" data-sortable="false">{{ trans('trip.table.driver_number') }}</th>
                                    <th class="cl_min-90" data-sortable="false">{{ trans('trip.table.vehicle_number') }}</th>
                                    <th class="cl_min-200" data-sortable="false">{{ trans('trip.table.company_name') }}</th>
                                    <th class="cl_min-120" data-sortable="false">{{ trans('trip.table.customer_name') }}</th>
                                    <th class="cl_min-120" data-sortable="false">{{ trans('trip.table.customer_number') }}</th>
                                    <th class="cl_min-200" data-sortable="false">{{ trans('trip.table.pick_up') }}</th>
                                    <th class="cl_min-200" data-sortable="false">{{ trans('trip.table.drop_off') }}</th>
                                    <th class="cl_min-100" data-sortable="false">{{ trans('trip.table.total_amount') }}</th>
                                    <th class="cl_min-100" data-sortable="false">{{ trans('trip.table.trip_type') }}</th>
                                    <th class="cl_min-100" data-sortable="false">{{ trans('trip.table.schedule_time') }}</th>
                                    <th class="cl_min-100" data-sortable="false">{{ trans('trip.table.trip_status') }}</th>
                                    <th class="cl_min-160" data-sortable="false">{{ trans('trip.table.reason_cancel') }}</th>
                                    <th class="cl_min-100" data-sortable="false">{{ trans('trip.table.paid_cash') }}</th>
                                    <th class="cl_min-100" data-sortable="false">{{ trans('trip.table.paid_card') }}</th>
                                    <th class="cl_min-100" data-sortable="false">{{ trans('trip.table.payment_status') }}</th>
                                    <th class="cl_min-100" data-sortable="false">{{ trans('trip.table.rating') }}</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--End Advanced Tables -->
        </div>
    </div>
    <!-- /. ROW  -->
</div>
<!-- /. PAGE INNER  -->
@include('partials.delete-modal')
@stop

@push('css-stack')
<!-- TABLE STYLES-->
<link href="{{ asset('assets/js/dataTables/dataTables.bootstrap.css') }}" rel="stylesheet" />
<style>
    .btn-selected {
        background: #f5f5f5 !important;
        color: #d0d0d0 !important;
    }
    .th_hide {
        display: none;
    }

    .th_show {
        display: table-cell;
    }
</style>
@endpush

@push('js-stack')
<script>
    $(document).ready(function () {
        $( "#type_search" ).val('<?= $type ?>');

        var type = $( "#type_search" ).val();
        if(type) {
            $('#btnAll span').removeClass('trips_but').addClass('trips_but_all');
            $('#menu_select a span').removeClass('btn-selected');
            if(type == '<?= TRIP_ON_GOING ?>') {
                $('#btnOngoing').find('span').addClass('btn-selected');
            } else if(type == '<?= TRIP_SCHEDULE ?>') {
                $('#btnSchedule').find('span').addClass('btn-selected');
            } else if(type == '<?= TRIP_COMPLETE ?>') {
                $('#btnComplete').find('span').addClass('btn-selected');
            } else if(type == '<?= TRIP_REJECT ?>') {
                $('#btnReject').find('span').addClass('btn-selected');
            } else if(type == '<?= TRIP_CANCEL ?>') {
                $('#btnCancel').find('span').addClass('btn-selected');
            }
        }
    });
</script>

<script type="text/javascript">
    $(function () {
        window.memberTable = $('#table-trips').dataTable({
            "processing": true,
            "serverSide": true,
            "paginate": true,
            "lengthChange": true,
            "filter": true,
            "info": true,
            "autoWidth": true,
            "order": [[ 1, "desc" ]],
            "columnDefs": [
                //{ orderable: false, targets: []},
                { orderable: true, targets: [0, 1]}
            ],
            "ajax": {
                "url": '{{ route('ajax.trip.search') }}',
                "type": "POST",
                "data": function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.key  = $( "input[name='key']" ).val();
                    d.type = $( "#type_search" ).val();
                    d.start_date = $( "input[name='start_date']" ).val();
                    d.end_date = $( "input[name='end_date']" ).val();
                    d.user_id = '<?= $user_id ?>';
                }
            },
            "drawCallback": function(settings) {
                var result = settings.json;
                var type   = $( "#type_search" ).val();
                if(type == '<?= TRIP_ON_GOING ?>') {
                    $('#text_ongoing').html('Ongoing Trips ('+ result.recordsTotal +')');
                } else if(type == '<?= TRIP_SCHEDULE ?>') {
                    $('#text_schedule').html('Scheduled Trips ('+ result.recordsTotal +')');
                } else if(type == '<?= TRIP_COMPLETE ?>') {
                    $('#text_complete').html('Completed Trips ('+ result.recordsTotal +')');
                } else if(type == '<?= TRIP_REJECT ?>') {
                    $('#text_reject').html('Rejected Trips ('+ result.recordsTotal +')');
                } else if(type == '<?= TRIP_CANCEL ?>') {
                    $('#text_cancel').html('Canceled Trips ('+ result.recordsTotal +')');
                }
            },
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "language": {
                "url": '{{ asset("assets/js/dataTables/lang/en.json") }}'
            }
        });

        $( "input[name='key']" ).on('keyup', function (){
            $('#table-trips').dataTable().fnFilter(this.value);
        });

        $('#btnSort').on('click', function () {
            memberTable.fnDraw();
        });

        $('#btnOngoing').on('click', function () {
            memberTable.api().columns( [16, 17,18, 19, 20] ).visible( false );
            $('#type_search').val('<?= TRIP_ON_GOING ?>');
            $('#btnAll span').removeClass('trips_but').addClass('trips_but_all');
            $('#menu_select a span').removeClass('btn-selected');
            $(this).find('span').addClass('btn-selected');
            memberTable.fnDraw();
        });

        $('#btnSchedule').on('click', function () {
            memberTable.api().columns( [17,18, 19, 20] ).visible( true );
            memberTable.api().columns( [16] ).visible( false );
            $('#type_search').val('<?= TRIP_SCHEDULE ?>');
            $('#btnAll span').removeClass('trips_but').addClass('trips_but_all');
            $('#menu_select a span').removeClass('btn-selected');
            $(this).find('span').addClass('btn-selected');
            memberTable.fnDraw();
        });

        $('#btnComplete').on('click', function () {
            memberTable.api().columns( [17, 18, 19, 20] ).visible( true );
            memberTable.api().columns( [16] ).visible( false );
            $('#type_search').val('<?= TRIP_COMPLETE ?>');
            $('#btnAll span').removeClass('trips_but').addClass('trips_but_all');
            $('#menu_select a span').removeClass('btn-selected');
            $(this).find('span').addClass('btn-selected');
            $('#table-trips').find('.th_hide').removeClass('th_hide').addClass('th_show');
            memberTable.fnDraw();
        });

        $('#btnReject').on('click', function () {
            memberTable.api().columns( [17, 18, 19, 20] ).visible( false );
            memberTable.api().columns( [16] ).visible( true );
            $('#type_search').val('<?= TRIP_REJECT ?>');
            $('#btnAll span').removeClass('trips_but').addClass('trips_but_all');
            $('#menu_select a span').removeClass('btn-selected');
            $(this).find('span').addClass('btn-selected');
            memberTable.fnDraw();
        });

        $('#btnCancel').on('click', function () {
            memberTable.api().columns( [17, 18, 19, 20] ).visible( false );
            memberTable.api().columns( [16] ).visible( true );
            $('#type_search').val('<?= TRIP_CANCEL ?>');
            $('#btnAll span').removeClass('trips_but').addClass('trips_but_all');
            $('#menu_select a span').removeClass('btn-selected');
            $(this).find('span').addClass('btn-selected');
            memberTable.fnDraw();
        });

        $('#btnAll').on('click', function () {
            memberTable.api().columns( [16, 17,18, 19, 20] ).visible( true );
            $('#type_search').val('');
            $('#btnAll span').removeClass('trips_but_all').addClass('trips_but');
            $('#menu_select a span').removeClass('btn-selected');
            memberTable.fnDraw();
        });

        var database = firebase.database();
        var tripRef = database.ref('trip');
        tripRef.once("child_added", function(snap) {
            console.log('add');
            memberTable.fnDraw();
        });
        tripRef.once("child_changed", function(snap) {
            console.log('change');
            memberTable.fnDraw();
        });
        tripRef.once("child_removed", function(snap) {
            console.log('remove');
            memberTable.fnDraw();
        });
    });
</script>
@endpush

