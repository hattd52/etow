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
        <div class="col-sm-12">
            <div class="sub_nav">
                <div>
                    <a href="{{ route('setting.update_price') }}"><div class="trips_but_margin"> <span class="trips_but_completed">Update Price</span></div></a>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="table-trips">
                            <thead>
                                <tr>
                                    <th>{{ trans('setting.table.km') }}<div class="fa fa-sign-out fa-sort"></div></th>
                                    <th class="cl_min-100">{{ trans('setting.table.price') }}</th>
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
        window.memberTable = $('#table-trips').dataTable({
            "processing": true,
            "serverSide": true,
            "paginate": false,
            "lengthChange": true,
            "filter": false,
            "info": false,
            "autoWidth": true,
            "order": [[ 0, "asc" ]],
            "columnDefs": [
                //{ orderable: false, targets: '_all'},
                { orderable: true, targets: [0]}
            ],
            "ajax": {
                "url": '{{ route('ajax.setting.search') }}',
                "type": "POST",
                "data": function (d) {
                    d._token = "{{ csrf_token() }}";
                }
            },
            //"lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "language": {
                "url": '{{ asset("assets/js/dataTables/lang/en.json") }}'
            }
        });
    });
</script>
@endpush

