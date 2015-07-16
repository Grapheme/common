$(function(){
	
	$('.approve-checkbox').on('change', function(){
		
		var $this = $(this);
		var $value = 0;
		if($(this).is(':checked')){
			$value = 1;
		}
		$.ajax({
			url: $($this).parents('form').attr('action'),
			data: {value: $value},
			type: 'post'
		}).done(function(response){
			showMessage.constructor("Модерация", response.responseText);
			showMessage.smallInfo();
			$($this).parents('tr').fadeOut(500,function(){$(this).remove();});
		});
	});

	$('.banned-post').on('click', function(){
		
		var $this = $(this);
		$.ajax({
			url: $($this).parents('form').attr('action'),
			type: 'post'
		}).done(function(response){
			showMessage.constructor("Модерация", response.responseText);
			showMessage.smallInfo();
			$($this).parents('tr').fadeOut(500,function(){$(this).remove();});
		});
	});


	$('.approve-post').on('click', function(){
		
		var $this = $(this);
		var $value = 1;
		$.ajax({
			url: $($this).parents('form').attr('action'),
			data: {value: $value},
			type: 'post'
		}).done(function(response){
			showMessage.constructor("Модерация", response.responseText);
			showMessage.smallInfo();
			$($this).parents('tr').fadeOut(500,function(){$(this).remove();});
		});
	});


});