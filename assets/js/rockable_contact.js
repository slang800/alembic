jQuery(function(){

	jQuery('.contact').submit(function(){
		
		var $this = jQuery(this);
		
		
		var form_name = document.getElementById('contact_name');
		var form_email = document.getElementById('contact_email');
		var form_message = document.getElementById('contact_message');
		
		if(form_name && form_name.value == form_name.defaultValue)
			form_name.value = '';
		
		if(form_email && form_email.value == form_email.defaultValue)
			form_email.value = '';		
		
		if(form_message && form_message.value == form_message.defaultValue)
			form_message.value = '';
			

		jQuery('.alert', $this).html('');		
		jQuery('.button', $this).attr('disabled', true);
		
		var data = $this.serialize();
		
		jQuery.post(ajaxurl, data, function(response){
		
			jQuery('.alert', $this).html(response);
			jQuery('.button', $this).attr('disabled', false);
			
			if(form_name && form_name.value == '')
				form_name.value = form_name.defaultValue;
			
			if(form_email && form_email.value == '')
				form_email.value = form_email.defaultValue;		
			
			if(form_message && form_message.value == '')
				form_message.value = form_message.defaultValue;
			
		});
		
		return false;
	});
	
});