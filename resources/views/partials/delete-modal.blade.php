<div class="modal fade modal-danger" id="modal-delete-confirmation" tabindex="-1" role="dialog" aria-labelledby="delete-confirmation-title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="delete-confirmation-title" style="color: #fff;">{{ trans('core.modal.title') }}</h4>
            </div>
            <div class="modal-body" style="">
                <div class="default-message" style="">
                    @isset($message)
                        {!! $message !!}
                    @else
                        {{ trans('core.modal.confirmation-message') }}
                    @endisset
                </div>
                <div class="custom-message"></div>
            </div>
            <div class="modal-footer" style="">
                <button type="button" class="btn btn-outline btn-flat"
                        data-dismiss="modal" style="">
                    {{ trans('core.button.cancel') }}
                </button>
                <form method="POST" accept-charset="UTF-8" class="pull-left">
                    <input name="_method" type="hidden" value="DELETE">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-outline btn-flat"
                            style="">
                        <i class="fa fa-trash"></i>
                        {{ trans('core.button.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js-stack')
<script>
    $( document ).ready(function() {
        $('#modal-delete-confirmation').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var actionTarget = button.data('action-target');
            var modal = $(this);
            modal.find('form').attr('action', actionTarget);

            if (button.data('message') === undefined) {
            } else if (button.data('message') != '') {
                modal.find('.custom-message').show().empty().append(button.data('message'));
                modal.find('.default-message').hide();
            } else {
                modal.find('.default-message').show();
                modal.find('.custom-message').hide();
            }

            if (button.data('remove-submit-button') === true) {
                modal.find('button[type=submit]').hide();
            } else {
                modal.find('button[type=submit]').show();
            }
        });
    });
</script>
@endpush
