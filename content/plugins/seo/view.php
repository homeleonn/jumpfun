<link rel="stylesheet" href="<?=PLUGINS?>seo/style.css">
<div id="post-seo" class="side-block">
	<div class="block-title">SEO</div>
	<div class="block-content">
		<b>Описание(Мета-тег: description):</b><br>
		<input type="text" class="w100" name="_seo_description" value="<?=$descr?>"><br><br>
		<b>Ключевые слова(Мета-тег: keywords):</b><br>
		<input type="text" class="w100" name="_seo_keywords" value="<?=$keys?>">
		<div id="seo-recommended"></div>
	</div>
</div>
<script>

if(!window.jQuery)
	document.write('<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></scr'+'ipt>');

(function(){
	var $desc 		= $('[name="_seo_description"]');
	var $keys 		= $('[name="_seo_keywords"]');
	var $recBlock 	= $('#seo-recommended');
	
	var content;
	
	
	
	function a(item, type, text){
		if(!$recBlock.find('#'+item).length){
			$recBlock.append('<div id="'+item+'"></div>');
		}
		$('#'+item).attr('class', 'seo-'+type).html(text);
	}
	
	function b(){
		var length = $desc.val().length;
		if(length > 160)
			a('seo-desc', 'red', ' <b>Мета-описание</b> слишком длинное, '+length+' символов, оптимальная длинна 150-160 символов!');
		else if(length < 100)
			a('seo-desc', 'red', ' <b>Мета-описание</b> слишком короткое, '+length+' символов, оптимальная длинна 150-160 символов!');
		else
			a('seo-desc', 'green', ' <b>Мета-описание</b> '+length+' символов. Отлично!');
	}
	
	function c(){
		// del tags
		content = content.replace(/<[^\s]*?>/g, '').trim();
		var spaceLength = content.match(/\s+/g).length;
		var length = spaceLength + 1;
		console.log(content, length);
		
		if(length < 300)
			a('seo-content', 'red', ' Слишком мало текста, '+length+' слов, оптимальное количество слов 300!');
		else
			a('seo-content', 'green', ' С количеством слов в тексте все впорядке, '+length+' слов. Отлично!');
	}
	
	$(function(){
		setTimeout(function(){
			content = getContent();
			c();
			$('textarea').on('blur', function(){
				content = getContent();
				c();
			});
		}, 1);
		
		b();
		$desc.on('blur', b);
	});
	
	
}());
</script>