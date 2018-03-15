	</div>
	<footer>
	</footer>
</div>
<?php //ju_footer();?>
<script src="<?=SITE_URL?>admin/view/js/common.js"></script>
<script src="<?=SITE_URL?>admin/view/js/upload.js"></script>
<script src="<?=SITE_URL?>admin/view/js/add.js"></script>
<script src="<?=SITE_URL?>admin/view/js/translit.js"></script>
<script src="<?=SITE_URL?>admin/view/js/comments.js"></script>
<script src="<?=SITE_URL?>Jump/components/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?=SITE_URL?>Jump/components/js/tinymce/tinymce.min.js"></script>
<script>

var root = '<?=ROOT_URI?>', 
ajaxUrl = root + "admin/ajax/",
postType = '<?=isset($options['type']) ? $options['type'] : 'false';?>',
contents = ['content', 'description'],
text, editor, tinymceInit = false,
urlPattern = /^<?=URL_PATTERN?>$/;

contents.forEach(function(item){
	var item = 'textarea#' + item;
	if($(item).length){
		text = $(item).val();
		$(item).val('');
		return false;
	}
});



</script>


<script src="<?=SITE_URL?>admin/view/js/jquery.nestable.js"></script>
<script src="<?=SITE_URL?>admin/view/js/edit-menu.js"></script>
</body>
</html>