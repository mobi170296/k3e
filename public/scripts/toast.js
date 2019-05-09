/* Library for toast */
var $Toast = new (function(){
    this.toastbody = null;
    this.createToastBody = function(){
        this.toastbody = $.create('div');
        this.toastbody.id = 'toast-wrapper';
        $(document.body).addEnd(this.toastbody);
    }
    this.makeWarning = function(m,t=2000){
        var b = $.create('div');
        $(b).addClass('toast-box').addClass('warning').html(m);
        if(this.toastbody===null){
            this.createToastBody();
        }
        $(this.toastbody).addEnd(b);
        window.setTimeout(function(){
            window.$Toast.remove();
        }, t);
        $(b).css('transition-duration',t+'ms');
        window.setTimeout(function(){
            $(b).addClass('fadeout');
        }, 2000);
    }
    this.makeError = function(m,t=2000){
        var b = $.create('div');
        $(b).addClass('toast-box').addClass('error').html(m);
        if(this.toastbody==null){
            this.createToastBody();
        }
        $(this.toastbody).addEnd(b);
        window.setTimeout(function(){
            window.$Toast.remove();
        }, t);
        $(b).css('transition-duration', t + 'ms');
        window.setTimeout(function(){
            $(b).addClass('fadeout');
        }, 2000);
    }
    this.makeSuccess = function(m,t=2000){
        var b = $.create('div');
        $(b).addClass('toast-box').addClass('success').html(m);
        if(this.toastbody==null){
            this.createToastBody();
        }
        $(this.toastbody).addEnd(b);
        window.setTimeout(function(){
            window.$Toast.remove();
        }, t);
        $(b).css('transition-duration', t + 'ms');
        window.setTimeout(function(){
            $(b).addClass('fadeout');
        },2000);
    }
    this.remove = function(){
        this.toastbody.firstElementChild.remove();
    }
})();

$LoadingPopup = new (function(){
    this.container = $.create('div');
    this.icon = $.create('div');
    this.container.append(this.icon);
    $(this.container).addClass('loading-popup').addClass('u-hidden');
    $(this.icon).addClass('loading-icon');
    this.show = function(){
        $(document.body).addBegin(this.container);
        $(this.container).removeClass('u-hidden');
        $(document.body).css('overflow', 'hidden');
    }
    this.hide = function(){
        $(this.container).addClass('u-hidden');
        $(document.body).css('overflow', 'auto');
    }
})();

$ConfirmPopup = new (function(){
    this.container = $.create('div');
    this.messagebox = $.create('div');
    this.content = $.create('div');
    this.btngroup = $.create('div');
    this.activebtn = $.create('button');
    this.inactivebtn = $.create('button');
    $(this.activebtn).attr('type', 'button').addClass('activebtn');
    $(this.inactivebtn).attr('type', 'button').addClass('inactivebtn');
    $(this.btngroup).addEnd(this.activebtn).addEnd(this.inactivebtn).addClass('btngroup');
    $(this.messagebox).addEnd(this.content).addEnd(this.btngroup).addClass('messagebox');
    $(this.messagebox).on('mousedown', function(e){
        e.stopPropagation();
    });
    $(this.content).addClass('content');
    $(this.container).addEnd(this.messagebox).addClass('confirm-popup').addClass('u-hidden');//.data('role', 'popup').data('role-type', 'static');
    this.message = function(m){
        $(this.content).html(m);
        return this;
    }
    this.active = function(m, f){
        this.activebtn.innerHTML = m;
        this.activebtn.onclick = f;
        return this;
    }
    this.inactive = function(m, f){
        this.inactivebtn.innerHTML = m;
        this.inactivebtn.onclick = f;
        return this;
    }
    this.show = function(){
        $(this.container).removeClass('u-hidden');
    }
    this.hide = function(e){
        $(this.container).addClass('u-hidden');
    }
})();

$NotificationPopup = new (function(){
    var container = $.create('div');
    var messagebox = $.create('div');
    var content = $.create('div');
    var okbtn = $.create('button');
    $(okbtn).attr('type', 'button').addClass('okbtn');
    $(content).addClass('content');
    $(messagebox).addClass('messagebox').addEnd(content).addEnd(okbtn);
    $(container).addClass('notification-popup').addClass('u-hidden').addEnd(messagebox);
    
    
    okbtn.onclick = function(e){
        $(messagebox).css('transform', 'translate(0, -500px)');
        window.setTimeout(function(e){
            hide();
        }, 200);
    }
    
    message = function(message){
        $(content).html(message);
        return this;
    }
    
    ok = function(title){
        $(okbtn).html(title);
        return this;
    }
    
    show = function(){
        $(messagebox).css('transform', 'translate(0, 0)');
        $(container).removeClass('u-hidden');
        return this;
    }
    
    hide = function(){
        $(container).addClass('u-hidden');
        return this;
    }
    
    return {
        message: message,
        ok: ok,
        show: show,
        hide: hide,
        container: container
    }
});




var $Modal = new (function(){
    this.modalwrapper = $('#modal-wrapper')[0];
    if(this.modalwrapper !== null){
        this.modal = $(this.modalwrapper).$('.modal')[0];
        this.modalheader = $(this.modal).$('.modal-header')[0];
        this.modalheadertitle = $(this.modal).$('.modal-header-title')[0];
        this.modalbody = $(this.modal).$('.modal-body')[0];
        this.modalclosebutton = $(this.modaltitle).$('.modal-header-button')[0];
    }
    
    this.create = function(){
        this.modalwrapper = $.create('div');
        this.modalwrapper.id = 'modal-wrapper';
        $(this.modalwwrapper).addClass('u-hidden');
        this.modal = $.create('div');
        $(this.modal).addClass('modal');
        $(this.modal).on('mousedown', function (e) {
            e.stopPropagation();
        });
        this.modalheader = $.create('div');
        $(this.modalheader).addClass('modal-header').addClass('clearfix');
        this.modalheadertitle = $.create('div');
        $(this.modalheadertitle).addClass('modal-header-title');
        $(this.modalheader).addEnd(this.modalheadertitle);
        
        this.modalclosebutton = $.create('div');
        $(this.modalclosebutton).html('&times;');
        $(this.modalclosebutton).addClass('modal-header-button');
        $(this.modalclosebutton).on('click', function (e) {
            window.$Modal.hide();
        });
        
        $(this.modalheader).addEnd(this.modalclosebutton);
        
        $(this.modal).addEnd(this.modalheader);
        
        this.modalbody = $.create('div');
        $(this.modalbody).addClass('modal-body');
        $(this.modal).addEnd(this.modalbody);
        
        $(this.modalwrapper).addEnd(this.modal);
        
        $(document.body).addEnd(this.modalwrapper);
        return this;
    }
    this.show = function(){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalwrapper).css('display', 'block');
        $(window.document.body).css('overflow', 'hidden');
        return this;
    }
    this.hide = function(){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalwrapper).css('display', 'none');
        $(window.document.body).css('overflow', 'auto');
        return this;
    }
    this.text = function(t){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalbody).text(t);
        return this;
    }
    this.html = function(t){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalbody).html(t);
        return this;
    }
    this.contenttext = function(t){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalbody).text(t);
        return this;
    }
    this.contenthtml = function(t){
        if(this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalbody).html(t);
        return this;
    }
    this.title = function(t){
        if (this.modalwrapper === undefined){
            this.create();
        }
        $(this.modalheadertitle).text(t);
        return this;
    }
    this.waiting = function(){
        if (this.modalwrapper === undefined){
            this.create();
        }
        this.html('<div class="loading-i-wrapper"><div class="loading-i"></div></div>');
        return this;
    }
    this.setEvent = function(t, e){
        $(this.modalwrapper).on(t, e);
    }
})();

document.onmousedown = function(e){
    window.$Modal.hide();
}

$(function(e){
    $(document.body).addBegin($LoadingPopup.container).addBegin($ConfirmPopup.container).addBegin($NotificationPopup.container);
    $('.tabpane .baritem').on('click', function(e){
        var p = $(this).parent('.tabpane');
        $(p).$('.baritem').removeClass('active');
        $(this).addClass('active');
        $(p).$('.tabcontent .tab').removeClass('active');
        $(p).$('.tabcontent .tab[data-tab="' + $(this).data('tab')+ '"]').addClass('active');   
    });
});


$(document).on('mousedown', function (e) {
    if(e.which===1){
        $('[data-role="popup"][data-role-type="static"]').addClass('u-hidden');
        $('[data-role="popup"][data-role-type="dynamic"]').remove();
    }
});