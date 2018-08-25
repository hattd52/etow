@extends('layouts.account')

@section('content')
    <div id="wrapper">
        <div class="login_logo">{{--<img src="assets/logo.png" width="124" height="124">--}}</div>
        <div class="login_wrapper">
            <div class="content">
                @include('partials.notifications')

                <form class="login-form" action="{{ route('forgot') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-title">
                        <h3><i class="fa fa-lock fa-4x"></i></h3>
                        <h2 class="text-center">Forgot Password?</h2>
                        <p>You can reset your password here.</p>
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                        <label class="control-label visible-ie8 visible-ie9">{{ trans('auth.login.form.label.email') }}</label>
                        <input type="email" class="form-control form-control-solid" autofocus
                               name="email" placeholder="{{ trans('auth.login.form.placeholder.email') }}" value="{{ old('email')}}" required>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-lg btn-primary btn-block">Reset Password</button>
                    </div>
                </form>
            </div>
        </div> <!--login_wrapper -->
        <div class="login_copyright">Copyright <?= date('Y') ?> eTow</div>
    </div><!--wrapper -->
@stop