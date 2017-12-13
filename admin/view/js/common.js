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
	var $active = $('#menu a[href="'+document.URL.split('?')[0]+'"]');
	$active.closest('li.top').find('div > a').click();
	if(!$active.parent('li').length){
		$active.closest('li.top').addClass('active');
	}else{
		$active.parent().addClass('active').closest('li.top').addClass('active')
	}
});

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
