<div class="modal fade" id="modal-forgot" tabindex="-1" role="dialog" aria-labelledby="forgot-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="">
                <h4 class="modal-title" id="forgot-title" style="">{{ trans('core.modal.forgot title') }}</h4>
            </div>
            <div class="modal-body" style="">
                <div class="default-message" style="color: #fff;">
                    @include('partials.notifications')
                </div>
                <div class="row">
                    <div class="col-md-2 col-sm-2"></div>
                    <div class="col-md-8 col-sm-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="text-center">
                                    <h3><i class="fa fa-lock fa-4x"></i></h3>
                                    <h2 class="text-center">Forgot Password?</h2>
                                    <p>You can reset your password here.</p>
                                    <div class="panel-body">
                                        <form id="register-form" role="form" action="{{ route('forgot-password') }}" autocomplete="off" class="form" method="post">
                                            {{ csrf_field() }}
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                                                    <input id="email" name="email" placeholder="input email address" class="form-control"  type="email" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input id="btnForgot" class="btn btn-lg btn-primary btn-block" value="Reset Password" type="button">
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-2"></div>
            </div>
            <div class="modal-footer" style="">
                <button type="button" class="btn btn-outline btn-flat"
                        data-dismiss="modal" style="border: 1px solid #fff; background: transparent;">
                    {{ trans('core.button.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function forgotPassword() {
        console.log('Haaaaaaaa');
        $('#modal-forgot').modal('show');
    }

    $(function () {
        $('#btnForgot').on('click', function () {
            var email = $('input:name="email"').val();
        })
    });

</script>
