@extends('layouts.account')

@section('content')
    <div id="wrapper">
        <div class="login_logo"><img src="assets/logo.png" width="124" height="124"></div>
        <div class="login_wrapper">
            <div class="content">
                @include('partials.notifications')

                <form class="login-form" action="{{ route('login') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-title">{{ trans('auth.login.title') }}</div>
                    <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                        <label class="control-label visible-ie8 visible-ie9">{{ trans('auth.login.form.label.email') }}</label>
                        <input type="email" class="form-control form-control-solid" autofocus
                               name="email" placeholder="{{ trans('auth.login.form.placeholder.email') }}" value="{{ old('email')}}">
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="control-label visible-ie8 visible-ie9">{{ trans('auth.login.form.label.password') }}</label>
                        <input type="password" class="form-control form-control-solid"
                               name="password" placeholder="{{ trans('auth.login.form.placeholder.password') }}" value="{{ old('password')}}">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        {!! $errors->first('password', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btnsubmit uppercase">{{ trans('auth.login.form.button.login') }}</button>
                    </div>
                    </form>

                    <a href="{{ route('reset')}}">{{ trans('auth.login.form.button.forgot') }}</a><br>
            </div>
        </div> <!--login_wrapper -->
        <div class="login_copyright">Copyright <?= date('Y') ?> eTow</div>
    </div><!--wrapper -->
@stop
