$(document).ready(function(){
	
    /*$('.find-article1').click(function(){
		var form = $(this);
		$.ajax({
			url: '/item/show/'+$('#create-order #article').val()+'/'+$('#create-order #group').val(),
			success: function(data){
				console.error(data);
				var message = $($(data).find('.alert-danger')[0]).clone().wrap('<p>').parent().html();
				if(typeof message != 'undefined'){
					data = message;
				}
				$('#item-result').html(data);
			},
			error: function(data){
				console.error(data);
				alert('Произошла ошибка');
			}
		});
		return false;
	});*/

	$('.report-rate').on('keyup', function(){
		var input = $(this).val();
		$('.userSum, .totalSum').each(function(i, e){
			var ru = $(e).find('.ru')[0];
			if(ru != undefined){
				$($(ru).parent().find('.by')[0]).text((input*$(ru).text()).toFixed(2) + ' BYN');
				
			}
		})
	});

	$('.order-edit').on('click', function(e){
		var elem = $(this);
		$.ajax({
			url: '/order/edit/'+$(this).data('id'),
			success: function(data){
				elem.parent().after(data);
				elem.detach();
			},
			error: function(data){
				console.log(data);
				alert('Произошла ошибка');
			}
		});
		/*var parent = $(this).parent(); 
		var qty = parent.find('span.qty').text();
		parent.parent().find('input[name=qty]').val(qty);
		parent.parent().find('input[name=userId]').val($(this).data('user'));*/
        return false;
	});
	
	jQuery.fn.outerHTML = function(s) {
        return s
            ? this.before(s).remove()
            : jQuery("<p>").append(this.eq(0).clone()).html();
    };
	
});


