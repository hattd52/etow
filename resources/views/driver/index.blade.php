<?php
    //dd($users);
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
                    <a class="trips_but_margin" id="btnAll" style="text-decoration: none; cursor: pointer"> <span class="trips_but">All Drivers ({{ $total }})</span></a>
                    <a id="btnOnline" style="cursor: pointer;"><div class="trips_but_margin"> <span class="trips_but_completed" id="text_online">Online Drivers</span></div></a>
                    <a id="btnOffline" style="cursor: pointer;"><div class="trips_but_margin"> <span class="trips_but_canceled" id="text_offline">Offline Drivers</span></div></a>
                    <a id="btnFree" style="cursor: pointer;"><div class="trips_but_margin"> <span class="trips_but_scheduled" id="text_free">Free Drivers</span></div></a>
                    <a id="btnTrip" style="cursor: pointer;"><div class="trips_but_margin"> <span class="trips_but_ongoing" id="text_on_trip">Drivers On Trip</span></div></a>
                    <input type="hidden" id="type_search" value="" />

                    <div class="add_business"><a href="{{ route('driver.create') }}" target="_blank" style="text-decoration: none;"><i class="fa fa-plus"></i>Add a Driver</a></div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="page_sort"></div>
                <div class="search_n">Search: <input name="key" type="search" class="input_320" placeholder="Enter Driver Name/ ID / Company Name"></div>
                <div class="clearfix"></div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="table-drivers">
                            <thead>
                            <tr>
                                <th class="cl_min-60">{{ trans('driver.table.stt') }}<div class="fa fa-sign-out fa-sort"></div></th>
                                <th class="cl_min-120" data-sortable="false">{{ trans('driver.table.driver_code') }}</th>
                                <th class="cl_min-200" data-sortable="false">{{ trans('driver.table.full_name') }}</th>
                                <th class="cl_min-60" data-sortable="false">{{ trans('driver.table.avatar') }}</th>
                                <th class="cl_min-200" data-sortable="false">{{ trans('driver.table.email') }}</th>
                                <th class="cl_min-100" data-sortable="false">{{ trans('driver.table.vehicle_type') }}</th>
                                <th class="cl_min-100" data-sortable="false">{{ trans('driver.table.vehicle_number') }}</th>
                                <th class="cl_min-200" data-sortable="false">{{ trans('driver.table.company') }}</th>
                                <th class="cl_min-60" data-sortable="false">{{ trans('driver.table.emirate_id') }}</th>
                                <th class="cl_min-60" data-sortable="false">{{ trans('driver.table.driving_license') }}</th>
                                <th class="cl_min-60" data-sortable="false">{{ trans('driver.table.mulkiya') }}</th>
                                <th class="cl_min-60" data-sortable="false">{{ trans('driver.table.is_online') }}</th>
                                <th class="cl_min-60" data-sortable="false">{{ trans('driver.table.trip_complete') }}</th>
                                <th class="cl_min-60" data-sortable="false">{{ trans('driver.table.trip_cancel') }}</th>
                                <th class="cl_min-60" data-sortable="false">{{ trans('driver.table.trip_reject') }}</th>
                                <th class="cl_min-60" data-sortable="false">{{ trans('driver.table.trip_new') }}</th>
                                <th class="cl_min-60" data-sortable="false">{{ trans('driver.table.trip_ongoing') }}</th>
                                <th class="cl_min-100" data-sortable="false">{{ trans('driver.table.rate') }}</th>
                                <th class="cl_min-100" data-sortable="false">{{ trans('driver.table.status') }}</th>
                                <th class="cl_min-140" data-sortable="false">{{ trans('driver.table.action') }}</th>
                            </tr>
                            </thead>
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
</style>
@endpush

@push('js-stack')
<script>
    $(document).ready(function () {
        //
    });
</script>

<script type="text/javascript">
    $(function () {
        window.memberTable = $('#table-drivers').dataTable({
            "processing": true,
            "serverSide": true,
            "paginate": true,
            "lengthChange": true,
            "filter": true,
            "info": true,
            "autoWidth": true,
            "order": [[ 0, "desc" ]],
            "columnDefs": [
                //{ orderable: false, targets: '_all'},
                { orderable: true, targets: [0]}
            ],
            "ajax": {
                "url": '{{ route('ajax.driver.search') }}',
                "type": "POST",
                "data": function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.key  = $( "input[name='key']" ).val();
                    d.type = $( "#type_search" ).val();
                }
            },
            "drawCallback": function(settings) {
                var result = settings.json;
                $('#text_all').html('All Drivers ('+ result.total_all +')');
                //var type   = $( "#type_search" ).val();
                //if(type == '<?= DRIVER_ONLINE ?>') {
                    $('#text_online').html('Online Drivers ('+ result.total_online +')');
                //} else if(type == '<?= DRIVER_OFFLINE ?>') {
                    $('#text_offline').html('Offline Drivers ('+ result.total_offline +')');
                //} else if(type == '<?= FREE_DRIVER ?>') {
                    $('#text_free').html('Free Drivers ('+ result.total_free +')');
                //} else if(type == '<?= DRIVER_ON_TRIP ?>') {
                    $('#text_on_trip').html('Drivers On Trip ('+ result.total_on_trip +')');
                //}
            },
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "language": {
                "url": '{{ asset("assets/js/dataTables/lang/en.json") }}'
            }
        });
        memberTable.dataTable.ext.errMode = 'throw';

        $( "input[name='key']" ).on('keyup', function (){
            $('#table-drivers').dataTable().fnFilter(this.value);
        });

        window.changeStatus = function (uid, status) {
            $('#btnStatus').attr('disabled',true);
            $.ajax({
                type: "POST",
                url: '{{ route('ajax.user.update_status') }}',
                data:{
                    "_token": "{{ csrf_token() }}",
                    "uid": uid,
                    "status": status
                },
                // dataType: "text",
                success: function(resultData){
                    //console.log(resultData);return;
                    $('#btnStatus').attr('disabled',false);

                    if(resultData.status == 0){
                        alert('<?= trans('driver.message.update status fail') ?>');
                    } else {
                        alert('<?= trans('driver.message.update status success') ?>');
                        memberTable.fnDraw();
                    }
                }
            });
        }

        $('#btnOnline').on('click', function () {
            $('#type_search').val('<?= DRIVER_ONLINE ?>');
            $('#btnAll span').removeClass('trips_but').addClass('trips_but_all');
            $('#menu_select a span').removeClass('btn-selected');
            $(this).find('span').addClass('btn-selected');
            memberTable.fnDraw();
        });

        $('#btnOffline').on('click', function () {
            $('#type_search').val('<?= DRIVER_OFFLINE ?>');
            $('#btnAll span').removeClass('trips_but').addClass('trips_but_all');
            $('#menu_select a span').removeClass('btn-selected');
            $(this).find('span').addClass('btn-selected');
            memberTable.fnDraw();
        });

        $('#btnFree').on('click', function () {
            $('#type_search').val('<?= FREE_DRIVER ?>');
            $('#btnAll span').removeClass('trips_but').addClass('trips_but_all');
            $('#menu_select a span').removeClass('btn-selected');
            $(this).find('span').addClass('btn-selected');
            memberTable.fnDraw();
        });

        $('#btnTrip').on('click', function () {
            $('#type_search').val('<?= DRIVER_ON_TRIP ?>');
            $('#btnAll span').removeClass('trips_but').addClass('trips_but_all');
            $('#menu_select a span').removeClass('btn-selected');
            $(this).find('span').addClass('btn-selected');
            memberTable.fnDraw();
        });

        $('#btnAll').on('click', function () {
            $('#type_search').val('');
            $('#btnAll span').removeClass('trips_but_all').addClass('trips_but');
            $('#menu_select a span').removeClass('btn-selected');
            memberTable.fnDraw();
        });

        var database = firebase.database();
        var driverRef = database.ref('driver');
        driverRef.on("child_added", function(snap) {
            memberTable.fnDraw();
        });
        driverRef.on("child_changed", function(snap) {
            memberTable.fnDraw();
        });
        driverRef.on("child_removed", function(snap) {
            memberTable.fnDraw();
        });
    });
</script>
@endpush

