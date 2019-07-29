jQuery(document).ready(function(){

jQuery('.sbtn').click(function(){

let id = jQuery(this).attr('data-id');
let fr = jQuery(this).parents('form').serialize();

iss_formhandler(fr, id);

return false;

});

jQuery('.delete_setting').click(function(){

	let id = jQuery(this).attr('data-id');
	
	let checkDel = confirm('Are you sure ?');
	
	if(checkDel){
		
		jQuery.ajax({

			url : aj.ajax_url,
			type : 'POST',
			data : {action : 'iss_data_delete', id : id },
			success : function(response){
				console.log(response);
				if(response == 1){
					alert('Setting deleted!');
					location.reload();
				}else{
					alert('Some error occured please refresh page and try again');
				}
			}
		})

	}

	return false;
	
	});


let selectids = ssettings_ids.split(',');

if( selectids != '' && selectids.length > 0){

	selectids.forEach(element => {
		
		jQuery('.select_'+element).select2();

	});
}

jQuery('section h4').click(function(){

setTimeout(function(){

	selectids.forEach(element => {
		
		jQuery('.select_'+element).select2();

			});

		},200);

});

jQuery('.shipcheck').click(function(){

	let ship_id = jQuery(this).attr('data-id');
	let th = jQuery(this);

	if(th.prop('checked')){

		let ship_check = confirm('This will overwrite previous shipping setting');

		if(ship_check === true){
			
			jQuery.ajax({

				url : aj.ajax_url,
				type : 'POST',
				data : {action : 'iss_shipping_update', ship_id : ship_id },
				success : function(response){
					console.log(response);
					if(response == 1){
						alert('shipping setting updated !');
					}else{
						alert('Some error occured please refresh page and try again');
					}
				}
			})

		}else{

			th.prop('checked',false);
		}

		}else{
			
	}

});

});


function iss_formhandler(form,id) {

jQuery.ajax({

	url : aj.ajax_url,
	type : 'POST',
	data : {action : 'iss_data_update', data : form, id : id},
	success : function(response){
		console.log(response);
		if(response == 1){
			alert('Data updated !');
		}else{
			alert('Some error occured please refresh page and try again');
		}
	}
})

}

// Create new setting function

function NewStripe(){

	var person = prompt("Please enter setting name");
	if ( person != null && person.length > 0  ) {
		NewStripeAjax(person);
	}else{
		alert("Setting name can't be empty");
	}

}

function NewStripeAjax(name){

	jQuery.ajax({

		url : aj.ajax_url,
		type : 'POST',
		data : {action : 'NewStripeAjax', name : name},
		success : function(response){
			console.log(response);
			if(response == 1){
				alert('Setting Created Successfully!');
				location.reload();
			}else{
				alert('Some error occured please refresh page and try again');
			}
		}
	});	

}


// Tabs code

jQuery(document).ready(function(){

jQuery('section h4').click(function(event) {

	event.preventDefault();
	jQuery(this).addClass('active');
	jQuery(this).siblings().removeClass('active');
  
	var ph = jQuery(this).parent().height();
	var ch = jQuery(this).next().height();
  
	if (ch > ph) {
	  jQuery(this).parent().css({
		'min-height': ch + 'px'
	  });
	} else {
	  jQuery(this).parent().css({
		'height': 'auto'
	  });
	}
  });
  
  jQuery(window).resize(function() {
	tabParentHeight();
  });
  
  jQuery(document).resize(function() {
	tabParentHeight();
  });
  tabParentHeight();

  function tabParentHeight() {
	var ph = jQuery('section').height();
	var ch = jQuery('section ul').height();
	if (ch > ph) {
	  jQuery('section').css({
		'height': ch + 'px'
	  });
	} else {
	  jQuery(this).parent().css({
		'height': 'auto'
	  });
	}
  }
});