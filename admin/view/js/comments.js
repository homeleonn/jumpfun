$(function(){
	;(function($){
		var $show 	= $('#admin-comment-block-show');
		var $cancel = $('#admin-cancel-comment');
		var $add 	= $('#admin-add-comment');
		var $commentText = $('#comment-text');
		
		function close(){
			$show.removeClass('none');
			$("#admin-comments-block").addClass('none');
			$commentText.val('');
		}
		
		$show.click(function(){
			if($(this).hasClass('none')) return;
			$(this).addClass('none');
			$("#admin-comments-block").removeClass('none');
		});
		
		$cancel.click(close);
		
		$add.click(function(){
			$.post(root + 'user/comments/add/' + $('#post_id').val() + '/', {comment: $commentText.val()}, function(){
				close();
			});
		});
		
		
		$('body').on('click', '#comment-delete', function(){
			var self = this;
			$.post(root + 'admin/delComment/' + $(this).data('id') + '/', function(data){
				if(data.response == 1) $(self).closest('table').remove();
			}, 'json');
		});
		
		
	})(window.jQuery);
});

