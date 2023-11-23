jQuery(document).ready( function(){

  function message(text, messageclass) {
      jQuery('<div class="message '+ messageclass +'">' + text + '</div>').appendTo(".message_box_club").delay(4000).queue(function() { 
          jQuery(this).remove(); 
      });
  }

	var mediaUploader;
	
	jQuery('#upload-button').on('click',function(e) {
		e.preventDefault();
		if( mediaUploader ){
			mediaUploader.open();
			return;
		}
		
		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: 'بارگزاری تصویر',
			button: {
				text: 'انتخاب'
			},
			multiple: false
		});
		
		mediaUploader.on('select', function(){
			attachment = mediaUploader.state().get('selection').first().toJSON();
			jQuery('#image_gift').val(attachment.url);
      jQuery('.picture-preview').css('display', 'block');
      jQuery('.picture-preview').attr('src', attachment.url);
		});
		
		mediaUploader.open();
		
	});

  jQuery('.picture-preview').on('click',function(){
      jQuery('#image_gift').val('');
      jQuery('.picture-preview').attr('src', '');
      jQuery('.picture-preview').css('display', 'none');
	});

	jQuery(".select_gift").click(function() {
      var select = jQuery(this).attr("value");
      if (select == "0") {
        jQuery(".main_coupon_gift").show();
      }
      if (select == "1") {
        jQuery(".main_coupon_gift").hide();
      }
  });

    
  jQuery('<div class="quantity-button quantity-up">+</div>').insertAfter('.input_number input');
  jQuery('<div class="quantity-button quantity-down">-</div>').insertBefore('.input_number input');
  jQuery('.input_number').each(function() {
    var spinner = jQuery(this),
      input = spinner.find('input[type="number"]'),
      btnUp = spinner.find('.quantity-up'),
      btnDown = spinner.find('.quantity-down'),
      min = input.attr('min'),
      max = input.attr('max'),
      step = input.attr('step');

    btnUp.click(function() {
      var oldValue = parseFloat(input.val());
      if (oldValue >= max) {
        var newVal = oldValue;
      } else {
        var newVal = oldValue + parseInt(step);
      }
      spinner.find("input").val(newVal);
      spinner.find("input").trigger("change");
    });

    btnDown.click(function() {
      var oldValue = parseFloat(input.val());
      if (oldValue <= min) {
        var newVal = oldValue;
      } else {
        var newVal = oldValue - parseInt(step);
      }
      spinner.find("input").val(newVal);
      spinner.find("input").trigger("change");
    });

  });

  
      jQuery(".btn_send_gifts").on("click" ,function() {
          var loader_button = jQuery(this);
          loader_button.addClass("loader_button");
          var data = {
              title : jQuery("#title_gift").val(),
              score : jQuery("#score_gift").val(),
              dec : jQuery("#dec_gift").val(),
              image : jQuery("#image_gift").val(),
              type : jQuery(".select_gift:checked").val(),
              dec_custom : jQuery("#dec_custom_gift").val(),
              type_coupon : jQuery("#type_coupon").val(),
              value_type_coupon : jQuery("#value_type_coupon").val(),
              free_shipping : jQuery("#free_shipping").val(),
              min_cart : jQuery("#min_cart").val(),
              max_cart : jQuery("#max_cart").val(),
              individual_use : jQuery("#individual_use").val(),
              exclude_sale_items : jQuery("#exclude_sale_items").val(),
              product_ids : jQuery("#product_ids").val(),
              exclude_product_ids : jQuery("#exclude_product_ids").val(),
              product_categories : jQuery("#product_categories").val(),
              exclude_product_categories : jQuery("#exclude_product_categories").val(),
              action : 'add_gift_club'
          }
          jQuery.ajax({
              type: "post",
              url: ajaxclubplugin.ajax_url,
              data: data,
              success: function(data) {
                data = JSON.parse( data );
                if(data['error']){
                  message(data['text'] , "error");
                }else{
                  jQuery(".form_add_gift").find("input[type=text], input[type=hidden], textarea, input[type=number]").val(null);
                  jQuery(".form_add_gift").find(".input_number input").val("0");
                  jQuery(".form_add_gift").find("select option[value='percent']").prop('selected', true);
                  jQuery(".form_add_gift").find("input:checkbox").removeAttr('checked');
                  jQuery('.picture-preview').attr('src', '');
                  jQuery('.picture-preview').css('display', 'none');
                  message(data['text'] , "success");
                }
                loader_button.removeClass("loader_button");
              },
              error: function() {}
          });
      });


      jQuery(".btn_update_gifts").on("click" ,function() {
        var loader_button = jQuery(this);
        loader_button.addClass("loader_button");
        var data = {
            id:  jQuery(".btn_update_gifts").attr('id_gifts'),
            title : jQuery("#title_gift").val(),
            score : jQuery("#score_gift").val(),
            dec : jQuery("#dec_gift").val(),
            image : jQuery("#image_gift").val(),
            type : jQuery(".select_gift:checked").val(),
            dec_custom : jQuery("#dec_custom_gift").val(),
            type_coupon : jQuery("#type_coupon").val(),
            value_type_coupon : jQuery("#value_type_coupon").val(),
            free_shipping : jQuery("#free_shipping").val(),
            min_cart : jQuery("#min_cart").val(),
            max_cart : jQuery("#max_cart").val(),
            individual_use : jQuery("#individual_use").val(),
            exclude_sale_items : jQuery("#exclude_sale_items").val(),
            product_ids : jQuery("#product_ids").val(),
            exclude_product_ids : jQuery("#exclude_product_ids").val(),
            product_categories : jQuery("#product_categories").val(),
            exclude_product_categories : jQuery("#exclude_product_categories").val(),
            action : 'update_gift_club'
        }
        jQuery.ajax({
            type: "post",
            url: ajaxclubplugin.ajax_url,
            data: data,
            success: function(data) {
              data = JSON.parse( data );
              if(data['error']){
                message(data['text'] , "error");
              }else{
                message(data['text'] , "success");
              }
              loader_button.removeClass("loader_button");
            },
            error: function() {}
        });
    });

    jQuery(".delete_gift").on("click" ,function() {
        var tr = jQuery( "#table tbody" ).find( "tr" ),
        id = "#"+jQuery(this).attr('id');
        var data = {
            id : jQuery(this).attr('id'),
            action : 'delete_gift_club'
        }
        jQuery.ajax({
            type: "post",
            url: ajaxclubplugin.ajax_url,
            data: data,
            success: function(data) {
              data = JSON.parse( data );
              if(data['error']){
                message(data['text'] , "error");
              }else{
                if(tr.length > 1){
                  jQuery(id).remove();
                }else{
                  jQuery("#table").remove();
                  jQuery(".main_table").append('<p class="empty">هدیه ای وجود ندارد</p>');
                }
                message(data['text'] , "success");
              }
              loader_button.removeClass("loader_button");
            },
            error: function() {}
        });
    });
});