jQuery(document).ready(function(){
    
    function message(text, messageclass) {
        jQuery('<div class="message '+ messageclass +'">' + text + '</div>').appendTo(".message_box_club").delay(3000).queue(function() { 
            jQuery(this).remove(); 
        });
    }
    function popup(text) {
        jQuery('<div class="popup"><div class="popup_header_club flex_club justify_between_club"><h3>جزئیات هدیه</h3><span class="btn_close_popup_club">بستن</span></div><p>' + text + '</p></div>').appendTo(".popup_box_club").siblings().remove();
        jQuery('.popup_box_club').fadeIn();
        jQuery('.btn_close_popup_club').click(function(){
            jQuery('.popup_box_club').fadeOut();
        })
    }
    
    jQuery(".btn_add_code_club").on("click" ,function() {
        var data = {
            code : jQuery(".inputcodeclub").val(),
            action : 'render_form_code_club'
        }
        jQuery.ajax({
            type: "post",
            url: ajaxclubplugin.ajax_url,
            data: data,
            success: function(data) {
                data = JSON.parse( data );
                if(data['error']){
                    message(data['text'] , "error");
                }
                else{
                    message(data['text'] , "success");
                    jQuery(".main-club").find(".form-club").remove();
                    jQuery(".main-club").append("<p>"+data['successtext']+"</p>");
                }
            },
            error: function() {}
        })
    })
    
    jQuery(".btn_club").on("click" ,function() {
        var loader_button = jQuery(this);
        loader_button.addClass("loader_button");
        var data = {
            id : jQuery(this).attr('id'),
            action : 'set_gift'
        }
        jQuery.ajax({
            type: "post",
            url: ajaxclubplugin.ajax_url,
            data: data,
            success: function(data) {
                data = JSON.parse( data );
                if(data['text']){
                    message(data['text'] , "error");
                }
                if(data['popup']){
                    popup(data['popup']);
                }
                loader_button.removeClass("loader_button");
            },
            error: function() {}
        });
    });
})