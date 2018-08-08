<?php
    //dd($users);
?>
@extends('layouts.main')

@section('content')
<div id="page-inner">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="col-sm-12">
                        @include('notifications')
                    </div>
                    <div class="col-sm-12">
                        @include('form_error')
                    </div>
                </div>
                <div class="panel-body">
                    <form action="{{ route('driver.update', $driver->id) }}" enctype="multipart/form-data" method="POST">
                        {{ csrf_field() }}
                        @include('driver.form')
                        <button type="submit" class="btn btn-success btn_spcing">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /. ROW  -->
</div>
<!-- /. PAGE INNER  -->
@stop

@push('css-stack')
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" >
    <link href="{{ asset('assets/css/checkbox3.min.css') }}" rel="stylesheet" >
@endpush

@push('js-stack')
<!-- Custom Js -->
{{--<script src="assets/js/morris/morris.js"></script>--}}
{{--<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>--}}
{{--<script src="assets/js/custom-scripts.js"></script>--}}

<script src="{{ asset('assets/js/select2.full.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".selectbox").select2();
    });
</script>

<script type="text/javascript">
    $(function () {
    });
</script>
@endpush

