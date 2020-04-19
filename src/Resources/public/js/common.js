$(document).on('hidden.bs.modal', '#mediaModal', function () {
    $("#mediaModal").remove();
    $(".modal-dialog").remove();
});

function insertImageUrl(obj, imagePreviewDivClass){
    let modal = $(`<div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-labelledby="mediaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h6 class="modal-title" id="mediaModalLabel">Media</h6>
                </div>
                <div class="modal-body" id="detail"></div>
                <div class="modal-footer" id="footer-detail">
                    <button id="insertMedia" class="btn btn-primary pull-right">Insert</button>
                </div>
            </div>
        </div>
    </div>`);

    let modalBody = modal.find('.modal-body');
    let callbackUrl = "{{ path('fn_site_multimedia_ajax_list') }}";
    $.ajax({
        url: callbackUrl,
        type: 'get',
        success: function (res) {
            modalBody.html(res.template);
            modal.modal('show');
        },
        error: function () {
            $.notify('Oops!!! Something went wrong', "error");
            modal.remove();
        }
    });
}

function changeTab() {
    $('#uploadLi').removeClass('active');
    $('#uploadTab').removeClass('active');
    $('#browseLi').addClass('active');
    $('#browseTab').addClass('active');
}

function refreshForm(id='', fileName='', url='', createdAt='', caption='',title='',altName='',description='') {
    $('input[name="img-id"]').val(id);
    $('#filename-html').text('');
    $('#filename-image-thumb').html('<img src="'+url+'">');
    $('#uploaded-html').text(createdAt);
    $('#url').val(url);
    $('#caption').val(caption);
    $('#title').val(title);
    $('#altname').val(altName);
    $('#description').val(description);
}

function filterMedia(searchValue ='') {
    $.ajax({
        url: "{{ url('fn_site_multimedia_ajax_list',{'isUpdate':'IS_UPDATE'}) }}".replace('IS_UPDATE', 1),
        data: {'value':searchValue},
        success: function (res) {
            $(".image_picker_selector").remove();
            refreshForm();
            $("#media-content")
                .append('<div class="thumbnails image_picker_selector" id="fn-media-list-container">'+res.template+'</div>');
        }
    });
}

function updateMediaListing() {
    $.ajax({
        url: "{{ url('fn_site_multimedia_ajax_list',{'isUpdate':'IS_UPDATE'}) }}".replace('IS_UPDATE', 1),
        success: function (res) {
            $(".image_picker_selector").remove();
            refreshForm();
            $("#media-content")
                .append('<div class="thumbnails image_picker_selector" id="fn-media-list-container">'+res.template+'</div>');
        }
    });
}
