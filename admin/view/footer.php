	</div>
	<footer>
	</footer>
</div>
<?php //ju_footer();?>
<script src="<?=SITE_URL?>admin/view/js/common.js"></script>
<script src="<?=SITE_URL?>admin/view/js/upload.js"></script>
<script src="<?=SITE_URL?>admin/view/js/add.js"></script>
<script src="<?=SITE_URL?>admin/view/js/translit.js"></script>
<script src="<?=SITE_URL?>Jump/components/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?=SITE_URL?>Jump/components/js/tinymce/tinymce.min.js"></script>
<script>

var root = '<?=ROOT_URI?>', 
ajaxUrl = root + "admin/ajax/",
postSlug = '<?=$options['rewrite']['slug'];?>',
postType = '<?=$options['type'];?>',
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

</body>
</html>