jQuery(document).ready(function($){

	$('body').on('click', '.lb-follow-btn', function(){

		var btn = $(this);
		var store_id = btn.data('store-id');
		var follow = btn.data('follow');

		$.post( siteurl + '/wp-admin/admin-ajax.php?action=lb_follow', { store_id: store_id, follow: follow } ).done(function(response){
			
			btn.find('span').text(response.text);
			btn.find('i').removeClass('fa-heart fa-heart-o');

			if(response.following){
				btn.find('i').addClass('fa-heart');
				btn.data('follow', 'false');
			}else{
				btn.find('i').addClass('fa-heart-o');
				btn.data('follow', 'true');
			}

		});

		return false;

	});

});