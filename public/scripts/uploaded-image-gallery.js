function UploadedImageGallery() {
    this.callback = null;
    this.gallerypopup = $.create('div');
    $(this.gallerypopup).addClass('gallery-popup');
    $(this.gallerypopup).data('role', 'popup').data('role-type', 'dynamic');

    this.uploadedimagebody = $.create('div');
    $(this.uploadedimagebody).addClass('uploaded-image-body');
    $(this.uploadedimagebody).on('click', function (e) {
        e.stopPropagation();
    });

    this.uploadbox = $.create('div');
    $(this.uploadbox).addClass('upload-box');

    this.form = $.create('form');
    this.imageinput = $.create('input');
    this.imageinput.type = "file";
    this.imageinput.name = "image";

    this.uploadbutton = $.create('button');
    this.uploadbutton.type = "button";
    $(this.uploadbutton).html('Upload');

    $(this.form).addEnd(this.imageinput);
    $(this.form).addEnd(this.uploadbutton);

    $(this.uploadbox).addEnd(this.form);

    this.uploadedimagegallery = $.create('div');
    $(this.uploadedimagegallery).addClass('uploaded-image-gallery');

    this.uploadedimagegallerywrapper = $.create('div');
    $(this.uploadedimagegallerywrapper).addClass('uploaded-image-gallery-wrapper').addClass('clearfix');

    $(this.uploadedimagegallery).addEnd(this.uploadedimagegallerywrapper);

    $(this.uploadedimagebody).addEnd(this.uploadbox).addEnd(this.uploadedimagegallery);

    $(this.gallerypopup).addEnd(this.uploadedimagebody);

    this.addImage = function (p = {}) {
        var item = $.create('div');
        $(item).addClass('uploaded-image-gallery-item').data('id', p.id);
        var img = $.create('img');
        img.src = p.src;
        var selectbutton = $.create('span');
        selectbutton.__container = this;
        $(selectbutton).addClass('select-image-button').data('id', p.id);
        $(selectbutton).on('click', function (e) {
            e.__id = $(this).data('id');
            this.__container.selectButtonClick(e);
        });
        var deletebutton = $.create('span');
        deletebutton.__container = this;
        $(deletebutton).addClass('delete-image-button').data('id', p.id);
        $(deletebutton).on('click', function (e) {
            e.__id = $(this).data('id');
            this.__container.deleteButtonClick(e);
        });
        $(item).addEnd(img).addEnd(selectbutton).addEnd(deletebutton);
        $(this.uploadedimagegallerywrapper).addEnd(item);
    }

    this.removeAllImage = function () {
        $(this.uploadedimagegallerywrapper).$('div.uploaded-image-gallery-item').remove();
    }

    this.removeImage = function (p = {}) {
        $(this.uploadedimagegallerywrapper).$('div.uploaded-image-gallery-item[data-id="' + p.id + '"]').remove();
    }

    this.getImageTotal = function () {
        return $(this.uploadedimagegallerywrapper).$('div.uploaded-image-gallery-item[data-id="' + p.id + '"]').length;
    }

    this.show = function () {
        $(document.body).addEnd(this.gallerypopup);
        $(this.gallerypopup).removeClass('u-hidden');
    }

    this.hide = function () {
        $(this.gallerypopup).addClass('u-hidden');
    }

    this.selectButtonClick = function (e) {
        if (this.callback != null) {
            this.callback.selectButtonClick(e);
        }
    }

    this.deleteButtonClick = function (e) {
        if (this.callback != null) {
            this.callback.deleteButtonClick(e);
        }
    }

    this.setEventCallBack = function(cbo){
        this.callback = cbo;
    }
}