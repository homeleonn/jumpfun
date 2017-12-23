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
postSlug = '<?=$options['slug'];?>',
contents = ['content', 'description'],
text,
editor;

contents.forEach(function(item){
	var item = 'textarea#' + item;
	if($(item).length){
		text = $(item).val();
		$(item).val('');
		return false;
	}
});

tinymce.init({ 
	selector:'textarea:not(.nonEditor)',
	plugins : "image imagetools fullscreen hr anchor autoresize autolink autosave link lists table",
	relative_urls: false,
	remove_script_host: false,
	height : "600px"
});

</script>

</body>
</html>