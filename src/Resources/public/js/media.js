(function ($) {

    $.fn.fnMediaModal = function (options) {
        return this.each(function () {
            let defaults = $.extend({
                title: 'Media',
                url: "{{ path('fn_site_multimedia_ajax_list') }}",
                content: '',
                onSuccess: function () {
                },
                onGetSuccess: function () {
                },
            }, options);

            let _self = $(this);
            let _selfHtml = _self.html();
            let modalTitle = _self.data('title') || defaults.title;
            let callbackUrl = _self.data('url') || defaults.url;
            let onSuccessCallback = _self.data('success-callback') || defaults.onSuccess;
            let onGetSuccessCallback = _self.data('get-success-callback') || defaults.onGetSuccess;
            let modal = $(`
                <div class="modal fade custom-modal" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog  modal-lg ">
                        <div class="modal-content">
                            <div class="modal-header bg-indigo">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                </button>
                                <h6 class="modal-title" id="mediaModalLabel">${modalTitle}</h6>
                            </div>
                            <div class="modal-body" id="detail"></div>
                            <div class="modal-footer" id="footer-detail">
                        </div>
                    </div>
                </div>`);

            let modalBody = modal.find('.modal-body');
            let modalFooter = modal.find('.modal-footer')
            modalFooter.html('<button id="insertMedia" class="btn btn-primary pull-right">Insert</button>')

            let loaderTemplate = $(`<i class="fa fa-spinner fa-spin"></i>`);
            if (!defaults.content) {
                modalBody.append(loaderTemplate);
            }
            let disableSelf = () => {
                _self.html('');
                _self.html('<i class="fa fa-spinner fa-spin"></i> Processing ...');
                _self.prop('disable', true);
            };

            let enableSelf = () => {
                _self.html('');
                _self.html(_selfHtml);
                _self.prop('disable', false);
            };

            let loadForm = () => {

                disableSelf();

                if (!callbackUrl) {
                    modal.modal('show');
                    return;
                }

                $.ajax({
                    url: callbackUrl,
                    type: 'get',
                    success: function (res) {
                        if (res.status !== undefined && res.status === false) {
                            modal.modal('hide');
                            fnNotify(res.message, 'error');
                        } else {
                            modalBody.html(res.template);
                            onGetSuccessCallback(modal, res, _self);
                            modal.modal('show');
                        }

                        enableSelf();
                    },
                    error: function () {
                        fnNotify('Oops!!! Something went wrong', "error");
                        modal.remove();
                        enableSelf();
                    }
                });
            };

            _self.on('click', function (e) {
                e.preventDefault();
                if (_self.data('disabled') === true) {
                    return;
                }
                loadForm();
            });
            let postSuccess = () => {
                let url = $('#url').val();
                let fileType = $('#filetype').val();
                let filetypeName = fileType.split("/");
                let res;
                if(filetypeName[0] =='image'){
                    let size = $('#ys-media-desc-size').children("option:selected").text();
                    let imageStyle = getImageStyle(size);
                    res= {'mediaType': filetypeName[0],'mediaUrl': url, height: imageStyle[0], width: imageStyle[1]};
                }else {
                    res = {'mediaType': filetypeName[0],'mediaUrl': url }
                }
                //file type could be audio video image or other(applications)
                onSuccessCallback(res, _self);
                // fnNotify('success', "success");
                modal.modal('hide');
            };

            modal.on('click', '#insertMedia', function (e) {
                e.preventDefault();
                postSuccess();
            });
        });
    }

}(jQuery));
