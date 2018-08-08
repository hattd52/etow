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
            <div class="panel panel-default">
                <div class="page_sort"></div>
                <div class="search_n">Search: <input name="key" type="search" class="input_320" placeholder="Search Name/ Phone"></div>
                <div class="clearfix"></div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="table-feedbacks">
                            <thead>
                                <tr>
                                    <th>{{ trans('feedback.table.stt') }}<div class="fa fa-sign-out fa-sort"></div></th>
                                    <th class="cl_min-100">{{ trans('feedback.table.user_name') }}<div class="fa fa-sign-out fa-sort"></div></th>
                                    <th class="cl_min-100">{{ trans('feedback.table.mobile_no') }}<div class="fa fa-sign-out fa-sort"></div></th>
                                    <th class="cl_min-100">{{ trans('feedback.table.comment') }}</th>
                                    <th class="cl_min-140">{{ trans('feedback.table.action') }}</th>
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
        window.memberTable = $('#table-feedbacks').dataTable({
            "processing": true,
            "serverSide": true,
            "paginate": true,
            "lengthChange": true,
            "filter": true,
            "info": true,
            "autoWidth": true,
            "order": [[ 0, "desc" ]],
            "columnDefs": [
                { orderable: false, targets: [3, 4]},
                { orderable: true, targets: [0, 1, 2]}
            ],
            "ajax": {
                "url": '{{ route('ajax.feedback.search') }}',
                "type": "POST",
                "data": function (d) {
                    d._token = "{{ csrf_token() }}";
                    d.key  = $( "input[name='key']" ).val();
                }
            },
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "language": {
                "url": '{{ asset("assets/js/dataTables/lang/en.json") }}'
            }
        });

        $( "input[name='key']" ).on('keyup', function (){
            $('#table-feedbacks').dataTable().fnFilter(this.value);
        });

        var database = firebase.database();
        var feedbackRef = database.ref('feedback');
        feedbackRef.on("child_added", function(snap) {
            memberTable.fnDraw();
        });
        feedbackRef.on("child_changed", function(snap) {
            memberTable.fnDraw();
        });
        feedbackRef.on("child_removed", function(snap) {
            memberTable.fnDraw();
        });
    });
</script>
@endpush

