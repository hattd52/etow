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
            <div class="panel panel-default">
                <div class="page_sort">
                    <div style="float:right; margin-right:8px;">Search:
                        <input name="key" type="search" style="padding-left:8px; height:36px;" placeholder="Enter Name/ Phone">
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="table-users">
                            <thead>
                            <tr>
                                <th class="cl_min-60">{{ trans('user.table.id') }}<div class="fa fa-sign-out fa-sort"></div></th>
                                <th class="cl_min-120" data-sortable="false">{{ trans('user.table.full_name') }}</th>
                                <th class="cl_min-120" data-sortable="false">{{ trans('user.table.email') }}</th>
                                <th class="cl_min-120" data-sortable="false">{{ trans('user.table.phone') }}</th>
                                <th class="cl_min-100" data-sortable="false">{{ trans('user.table.trip_complete') }}</th>
                                <th class="cl_min-100" data-sortable="false">{{ trans('user.table.trip_cancel') }}</th>
                                <th class="cl_min-100" data-sortable="false">{{ trans('user.table.trip_reject') }}</th>
                                <th class="cl_min-100" data-sortable="false">{{ trans('user.table.trip_new') }}</th>
                                <th class="cl_min-100" data-sortable="false">{{ trans('user.table.trip_ongoing') }}</th>
                                <th class="cl_min-120" data-sortable="false">{{ trans('user.table.view') }}</th>
                                <th class="cl_min-120" data-sortable="false">{{ trans('user.table.status') }}</th>
                                <th class="cl_min-100" data-sortable="false">{{ trans('user.table.action') }}</th>
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
@endpush

@push('js-stack')
<script>
    $(document).ready(function () {
        //$('#dataTables-example').dataTable();
    });
</script>

<script type="text/javascript">
    $(function () {
        window.memberTable = $('#table-users').dataTable({
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
                "url": '{{ route('ajax.user.search') }}',
                "type": "POST",
                "data": function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.key = $( "input[name='key']" ).val();
                }
            },
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "language": {
                "url": '{{ asset("assets/js/dataTables/lang/en.json") }}'
            }
        });

        $( "input[name='key']" ).on('keyup', function (){
            $('#table-users').dataTable().fnFilter(this.value);
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

        var database   = firebase.database();
        var accountRef = database.ref('account');
        accountRef.on("child_added", function(snap) {
            memberTable.fnDraw();
        });
        accountRef.on("child_changed", function(snap) {
            memberTable.fnDraw();
        });
        accountRef.on("child_removed", function(snap) {
            memberTable.fnDraw();
        });
    });
</script>
@endpush

