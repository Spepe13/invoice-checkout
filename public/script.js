(function ($) {
	$(document).ready(function(){
		var $timologio = $('#billing_timologio');

		function checkTimologioFieldsVisibility() {
		   var required = '<abbr class="required" title="<?php echo $required; ?>">*</abbr>';   
		   var timologio = $timologio.val() === 'yes';
		   $('#billing_timologio_field label > .optional').hide(); 
			if (timologio) {
								my_callback()
				$('.timologio-hide').slideDown('fast');
			$('#billing_vat_field label > .optional').remove();
			$('#billing_vat_field').find('abbr').remove(); 
			$('#billing_vat_field'+' label').append(required);
			$('#billing_doy_field label > .optional').remove();
			$('#billing_doy_field').find('abbr').remove(); 
			$('#billing_doy_field'+' label').append(required);
			$('#billing_epag_field label > .optional').remove();
			$('#billing_epag_field').find('abbr').remove(); 
			$('#billing_epag_field'+' label').append(required);
			$('#billing_addr_field label > .optional').remove();
			$('#billing_addr_field').find('abbr').remove(); 
			$('#billing_addr_field'+' label').append(required);
			} else {
				$('#billing_timologio_field label > .optional').show();
		   
								my_callback()
				$('.timologio-hide').slideUp('fast');
			}
		}

		$timologio.change(checkTimologioFieldsVisibility);

		checkTimologioFieldsVisibility();
			   
		function my_callback() {
		jQuery('body').trigger('update_checkout');
	}
										  })
})(jQuery);