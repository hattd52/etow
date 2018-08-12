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
                <div class="panel-body">
                    <form action="{{ route('setting.update') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="exampleInputEmail1">Update Price (*)</label>
                            <input style="width:340px; " type="file" maxlength="3" class="form-control" name="filePrice"
                                   placeholder="Input file csv or excel" required="true" accept=".xls,.xlsx,.csv">
                        </div>
                        <div class="btn btn-success btn_spcing popup" onclick="myFunction()">Update Price
                            <div class="popuptext" id="myPopup">
                                <h1> Are you sure you want to update price ? </h1><br/><br/>
                                <button type="button" class="btn btn-danger btn_spcing"> No</button>
                                <button type="submit" class="btn btn-success btn_spcing"> Yes</button>
                                <br/><br/>
                            </div>
                        </div>
                        <a style="margin-left: 5px" id="btnDownload" class="btn btn-primary">Download File Example</a>
                    </form>
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

<script>
    function myFunction1() {
        confirm("Are you sure you want to update price ?");
    }
</script>

<script>
    function myFunction1() {
        var close = document.getElementsByClassName("closebtn");
        var i;
        for (i = 0; i < close.length; i++) {
            close[i].onclick = function(){
                var div = this.parentElement;
                div.style.opacity = "0";
                setTimeout(function(){ div.style.display = "none"; }, 600);
            }
        }
    }
</script>

<script>
    // When the user clicks on div, open the popup
    function myFunction() {
        var popup = document.getElementById("myPopup");
        popup.classList.toggle("show");
    }

    $(function () {
        $('#btnDownload').on('click', function () {
            window.open('{{ asset('assets/excel/price_example.xlsx') }}','_blank');
        });
    });
</script>
@endpush

