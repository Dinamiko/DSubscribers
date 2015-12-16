jQuery(document).ready(function($) {
	
	jQuery.validator.setDefaults({
	  debug: false,
	  success: "valid"
	});

	var form = $( "#form-validation" );

	form.validate();
	
	$( "#form-validation" ).submit(function(e) {

		e.preventDefault();
		
		if( form.valid() ) {

			var ajaxurl = $(this).attr('action');
			var dsubscribers_action = $(this).find('#dsubscribers_action').val();
			var dsubscribers_email = $(this).find('#dsubscribers_email').val();
			var dsubscribers_nonce = $(this).find('#dsubscribers_nonce').val();

		   	jQuery.ajax({

		         type : 'post',
		         dataType : 'json',
		         url : ajaxurl,
		         data : { 	action: 'dsubscribers_ajax', 
		         			dsubscribers_action:dsubscribers_action, 
		         			dsubscribers_email:dsubscribers_email,
		         			dsubscribers_nonce:dsubscribers_nonce 
		         		},

		         success: function( response ) {

		            if( response.type == 'success' ) {

		            	$('#dsubscribers_msg').html( response.msg );
		            	$('#dsubscribers_email').val('');


		            } else {

		               $('#dsubscribers_msg').html( response.msg );
		               $('#dsubscribers_email').val('');

		            }
		            
		         }

		    });		

		}

	});

	var form_widget = $( "#form-validation-widget" );

	form_widget.validate();
	
	$( "#form-validation-widget" ).submit(function(e) {

		e.preventDefault();
		
		if( form_widget.valid() ) {

			var ajaxurl = $(this).attr('action');
			var dsubscribers_action = $(this).find('#dsubscribers_action').val();
			var dsubscribers_email = $(this).find('#dsubscribers_email').val();
			var dsubscribers_nonce = $(this).find('#dsubscribers_nonce').val();

		   	jQuery.ajax({

		         type : 'post',
		         dataType : 'json',
		         url : ajaxurl,
		         data : { 	action: 'dsubscribers_ajax', 
		         			dsubscribers_action:dsubscribers_action, 
		         			dsubscribers_email:dsubscribers_email,
		         			dsubscribers_nonce:dsubscribers_nonce 
		         		},

		         success: function( response ) {

		            if( response.type == 'success' ) {

		            	$('#dsubscribers_msg_widget').html( response.msg );
		            	$('#dsubscribers_email_widget').val('');


		            } else {

		               $('#dsubscribers_msg_widget').html( response.msg );
		               $('#dsubscribers_email_widget').val('');

		            }
		            
		         }

		    });		

		}

	});

	
});