(function( $ ) {
	'use strict';

	$(document).ready(function(){
		$('.wb_mlr_view').on('click', function(){

			var popup_element = $('.wb_mlr_detail_popup');
			var popup_inner = popup_element.find('.wb_mlr_popup_inner');
			var popup_width = popup_element.outerWidth();
			var window_height = jQuery(window).height();
			var popup_height = window_height-150;
			
			popup_element.css({'margin-left':((popup_width/2)*-1),'display':'block','top':'50px', 'opacity':0}).animate({'opacity':1});
			popup_inner.css({'max-height':popup_height+'px','overflow':'auto'});

			popup_inner.html('');
			popup_element.addClass('wb_mlr_ajax_loader');
			var wb_mlr_id = $(this).attr('data-id');
			$.ajax({
				url:wb_mlr_params.ajax_url,
				type:'post',
				data:{'action':'wb_mlr_detail_view', 'security':wb_mlr_params.nonce, 'wb_mlr_id':wb_mlr_id},
				success:function(data){
					popup_element.removeClass('wb_mlr_ajax_loader');
					popup_inner.html(data);
				},
				error:function(){
					popup_element.removeClass('wb_mlr_ajax_loader');
					popup_inner.html(wb_mlr_params.labels.unabletoload);
				}
			});
		});

		$('.wb_mlr_detail_popup_close').on('click', function(){
			$('.wb_mlr_detail_popup').hide();
		});

		$('.wb_mlr_delete').on('click', function(){
			if(typeof $(this).attr('data-id')=='undefined'){
				return false;
			}
			if(confirm(wb_mlr_params.labels.areusure)){
				window.location.href = wb_mlr_params.delete_url + $(this).attr('data-id');
			}
		});

		$('.wb_mlr_bulk_check_main').on('click', function(){
			if($(this).is(':checked')){
				$('.wb_mlr_bulk_check_sub').prop('checked', true);
			}else{
				$('.wb_mlr_bulk_check_sub').prop('checked', false);
			}
		});

		$('.wb_mlr_bulk_check_sub').on('click', function(){
			if($('.wb_mlr_bulk_check_sub:checked').length!=$('.wb_mlr_bulk_check_sub').length){
				$('.wb_mlr_bulk_check_main').prop('checked', false);
			}else{
				$('.wb_mlr_bulk_check_main').prop('checked', true);
			}
		});

		$('.wb_mlr_delete_bulk').on('click', function(){
			if($('.wb_mlr_bulk_check_sub:checked').length==0){
				alert(wb_mlr_params.labels.chooseforbulk);
				return false;
			}
			var id_list = new Array();
			$('.wb_mlr_bulk_check_sub:checked').each(function(){
				if(typeof $(this).attr('data-id')!='undefined'){
					id_list.push($(this).attr('data-id'));
				}
			});

			if(id_list.length==0){
				alert(wb_mlr_params.labels.chooseforbulk);
				return false;
			}

			if(confirm(wb_mlr_params.labels.areusure)){
				window.location.href = wb_mlr_params.delete_url + id_list.join(",");
			}
		});

	});

})( jQuery );
