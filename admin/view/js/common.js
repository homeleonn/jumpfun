$(function(){
	$('#menu > li.top').click(function(e){
		if($(e.target).data('menu')){
			var menu = $('#menu > li.top');
			var flag = $(this).hasClass('slowShow');
			
			menu.removeClass('slowShow');
			if(!flag) $(this).addClass('slowShow');
			$(this).children('[class ^= "submenu"]').toggle('fast');
			menu.not('.slowShow').children('[class ^= "submenu"]').hide('fast');
		}
		
	});
	
	
	
	// select active menu element
	var activeTemp = document.URL;
	var $active = activeTemp;
	if(!($active = menuSelector(activeTemp)).length){
		var i = 3;
		while(i--){
			if(($active = menuSelector(activeTemp = activeTemp.replace(/(.*)\/.*\/$/gi, '$1/'))).length)
				break;
		}
	}
	$active.closest('li.top').find('div > a').click();
	if(!$active.parent('li').length){
		$active.closest('li.top').addClass('active');
	}else{
		$active.parent().addClass('active').closest('li.top').addClass('active')
	}
});

function menuSelector(href){
	return $('#menu a[href="'+href+'"]');
}

function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
	"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]).replace(/\+/g, ' ') : undefined;
}

function setCookie(name, value, hours){
	var date = new Date( new Date().getTime() + 3600*hours*1000 );
	var exp = (!hours) ? '' : "expires="+date.toUTCString();
	document.cookie=name+'='+value+";"+exp;
}

function delCookie(name){
	var date = new Date(0);
	document.cookie= name+"=; expires="+date.toUTCString();
}

function gebi(id){
	return document.getElementById(id);
}


function drawLoadingWait($el, del){
	var del = del || false;
	var loadClass = 'loading-wait';
	
	$el.addClass('rel');
	if(del) $el.children('.'+loadClass).remove();
	else	$el.append('<div class="'+loadClass+'"></div>');
}


function ExtraFiled(){
	this.state = 0;
	this.counter = 0;
	this.setNew = function(e){
		e.preventDefault();
		if(!this.state){
			$('#select_extra_name').addClass('none');
			$('#input_extra_name').removeClass('none');
			$('#init_new_extra').text('Отмена');
			this.state = !this.state;
		}else{
			extraFiled.cancel();
		}
	}
	
	this.set = function(){
		if($('#select_extra_name')[0].selectedIndex == 1){
			alert('Выберите имя поля или введите новое');return;
		}else{
			var nameEl 	= $('#input_extra_name');
			var valueEl = $('#extra_value_editor');
			var name 	= nameEl.val();
			var value 	= valueEl.val();
			if(name == ''){
				alert('Выберите имя поля или введите новое');return;
			}else if(value == ''){
				alert('Введите значение');return;
			}else if($('[name="'+name+'"]').length || $('[name="extra_fileds['+name+']"]').length){
				alert('Поле с данным именем уже существует');return;
			}else if(name[0] == '_'){
				alert('Недопустимое имя поля! Не может начинаться с "_"');return;
			}
		}
		
		if(!$('.new_extra_fields > .field').length){
			$('.fields_caption').removeClass('none');
		}
		
		
		var field = $('.field.prototype').clone();
		field.find('.extra_name').val(name);
		field.find('textarea').val(value).attr({'name' : 'extra_fileds['+name+']'});
		field.find('.extra_field_delete, .extra_field_update').attr({'data-extra_index' : extraFiled.counter});
		nameEl.val(''); valueEl.val('');
		$('.new_extra_fields').append(field.removeClass('prototype none'));
		extraFiled.counter++;
		extraFiled.cancel();
	}
	
	
	
	this.cancel = function(){
		$('#select_extra_name').removeClass('none');
		$('#input_extra_name').addClass('none');
		$('#init_new_extra').text('Введите новое');
		this.state = !this.state;
	}
	
	$(function(){
		$('#add_new_extra_name').click(extraFiled.set);
		$('#init_new_extra').click(function(e){
			extraFiled.setNew(e);
		});
		
		$('.new_extra_fields').click(function(e){
			var className = '.' + e.target.className;
			var field = $('[data-extra_index="'+$(e.target).data('extra_index')+'"]').closest('.field');
			switch(className){
				case '.extra_field_delete':{
					field.remove();
					if(!$('.new_extra_fields > .field').length){
						$('.fields_caption').addClass('none');
					}
				}break;
				case '.extra_field_update':{
					field.find('textarea').attr('name', 'extra_fileds['+field.find('.extra_name').val()+']');
				}break;
			}
		});
	}); 
}

extraFiled = new ExtraFiled();

