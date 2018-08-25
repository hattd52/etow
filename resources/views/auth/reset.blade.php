@extends('layouts.account')

@section('content')
    <div id="wrapper">
        <div class="login_logo">{{--<img src="assets/logo.png" width="124" height="124">--}}</div>
        <div class="login_wrapper">
            <div class="content">
                @include('partials.notifications')

                <form class="login-form" action="{{ route('reset') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-title">
                        {{--<h3><i class="fa fa-lock fa-4x"></i></h3>--}}
                        <h2 class="text-center">Reset Password?</h2>
                    </div>
                    <div class="form-group">
                        <label>Token</label>
                        <input type="text" name="token" class="form-control" placeholder="Input token" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Input new password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Input confirm password" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-lg btn-primary btn-block">Change Password</button>
                    </div>
                </form>
            </div>
        </div> <!--login_wrapper -->
        <div class="login_copyright">Copyright <?= date('Y') ?> eTow</div>
    </div><!--wrapper -->
@stop