$(document).on('hidden.bs.modal', '#mediaModal', function () {
    $("#mediaModal").remove();
    $(".modal-dialog").remove();
});

function insertCkeditorImage(obj, textArea) {
    insertImageUrl(obj, textArea, true)
}
function getImageStyle(size){

    size = size.split("-");
    var newDimension = size[1].split("x");
    var imageHeight = parseInt(newDimension[1])+'px;',
        imageWidth = parseInt(newDimension[0])+'px;';

    var imageStyle = "height:"+imageHeight+" width:"+imageWidth;
    var style = [imageHeight , imageWidth];
    return style;
}

function insertImageUrl(obj, imagePreviewDivClass, isCkeditor = false) {
    let modal = $(`<div class="modal fade" id="mediaModal" tabindex="-1" role="dialog" aria-labelledby="mediaModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg ">
            <div class="modal-content">
                <div class="modal-header bg-indigo">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h6 class="modal-title" id="mediaModalLabel">Media</h6>
                </div>
                <div class="modal-body" id="detail"></div>
                <div class="modal-footer" id="footer-detail">
                </div>
            </div>
        </div>
    </div>`);
    let modalFooter = modal.find('.modal-footer')
    if(!isCkeditor){
        modalFooter.html('<button id="insertMedia" class="btn btn-primary pull-right">Insert</button>')
    }else {
        modalFooter.html('<button id="insertMediaCkeditor" class="btn btn-primary pull-right">Insert</button>')
    }
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
    var counter = 0;
    $(document).on('click', ' #insertMedia',function () {
        if(counter ==0 ){
            let imageUrl = '/'+$('#url').val();
            let fileType = $('#filetype').val();
            $(obj).val(imageUrl);
            counter++;
            let filetypeName = fileType.split("/");
            if(filetypeName[0]=='image'){
                previewImage(imageUrl, obj, imagePreviewDivClass);
            }
        }
        $("#mediaModal").modal('hide');
    });

    $(document).on('click', '#insertMediaCkeditor',function (e) {
        if(counter == 0){
            if (imagePreviewDivClass != null && imagePreviewDivClass !== undefined) {
                var hostname = window.location.hostname;
                var domain = document.URL;
                var parts = domain.split('//');
                var alttext = $('#altname').val();
                var fileName = $('#title').val();
                var imagePath = '/'+$('#url').val();
                var filetype = $('#filetype').val();
                var size = $('#ys-media-desc-size').children("option:selected").text();

                let filetypeName = filetype.split("/");
                let appendFileTag = '';

                if(hostname != 'localhost'){
                    imagePath = parts[0] +'//'+hostname+ imagePath;
                }

                if(filetypeName[0]=='image'){
                    var imageStyle = getImageStyle(size);
                    appendFileTag = "<img alt='"+alttext+"' style='"+ imageStyle+"'/>";
                    appendFileTag = new CKEDITOR.dom.element('img');
                    appendFileTag.setAttribute( 'src', imagePath );
                    appendFileTag.setAttribute( 'type', filetypeName[0] );
                    appendFileTag.setAttribute( 'title', fileName );
                    appendFileTag.setAttribute( 'alt', alttext );
                    appendFileTag.setAttribute('height', imageStyle[0] );
                    appendFileTag.setAttribute( 'width', imageStyle[1] );
                }else if (filetypeName[0]=='audio'){
                    appendFileTag = new CKEDITOR.dom.element('video');
                    appendFileTag.setAttribute('controls', 'controls');
                    appendFileTag.setAttribute( 'src', imagePath );
                    appendFileTag.setAttribute( 'type', filetypeName[0] );
                    appendFileTag.setAttribute( 'title', fileName );
                    appendFileTag.setAttribute( 'alt', alttext );
                }else if (filetypeName[0]=='video'){
                    appendFileTag = new CKEDITOR.dom.element('video');
                    appendFileTag.setAttribute('controls', 'controls');
                    appendFileTag.setAttribute( 'src', imagePath );
                    appendFileTag.setAttribute( 'type', filetypeName[0] );
                    appendFileTag.setAttribute( 'title', fileName );
                    appendFileTag.setAttribute( 'alt', alttext );
                }else{
                    appendFileTag = '<a href="'+imagePath+'" target="_blank" title="'+alttext+'">'+fileName+'</a>';
                }
                if(filetypeName[0] == 'image' || filetypeName[0] == 'audio' || filetypeName[0] == 'video'){
                    CKEDITOR.instances[imagePreviewDivClass].insertElement(appendFileTag);
                }else{
                    $.fn.insertAtCaret = function (myValue) {
                        myValue = myValue.trim();
                        CKEDITOR.instances[imagePreviewDivClass].insertHtml(myValue);
                    };

                    $.fn.insertAtCaret(appendFileTag);
                }

                $('.'+imagePreviewDivClass).data('textarea', null);
                $("#mediaModal").modal('hide');
            }
            counter++;
        }
        return false;
    });

}

function previewImage(imageUrl, obj, imagePreviewDivClass) {
    let previewImageDiv = $(obj).parent().parent().parent();
    // var size = $('#ys-media-desc-size').children("option:selected").text();
    // var imageStyle = getImageStyle(size);

    let image = "<div class='removeMedia'><img style='height: auto;width:100%;' src='"+imageUrl+"'>" +
        "<a class=\"removeImage pull-right\" title=\"Remove Image\"><i class=\"fa fa-trash\"></i></a></div><div class=\"clearfix\"></div>";
    $(previewImageDiv).find('.'+imagePreviewDivClass+'Holder').html(image);

    $('.removeImage').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.removeMedia').remove();
        // $(this).closest('.'+imagePreviewDivClass).val('');
        $('.'+imagePreviewDivClass).val('');
    });

}

function changeTab() {
    $('#uploadLi').removeClass('active');
    $('#uploadTab').removeClass('active');
    $('#browseLi').addClass('active');
    $('#browseTab').addClass('active');
}

function refreshForm(id='', fileName='', filetype='',url='', createdAt='', caption='',title='',altName='',description='') {
    $('input[name="img-id"]').val(id);
    $('#filename-html').text('');
    $('#filename-image-thumb').html('<img src="'+url+'">');
    $('#uploaded-html').text(createdAt);
    $('#url').val(url);
    $('#caption').val(caption);
    $('#title').val(title);
    $('#altname').val(altName);
    $('#description').val(description);
    $('#ys-media-desc-size').html('');

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
