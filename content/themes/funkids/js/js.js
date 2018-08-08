function Slider(element){
	var list   = [];
	[].forEach.call($('.'+element+' .item'), function(l1){
		list.push(l1);
	});
	
	this.element = $('.'+element+' .ss');
	this.count = list.length;
	this.list = list;
	this.op = true;
	
	delete(list);
	
	this.go = function(active, next, mode, type){
		$(next).addClass(mode);
		$(next)[0].offsetWidth;
		$(active).addClass(type);
		$(next).addClass(type);
		setTimeout(function(){
			$(active)
				.removeClass('active')
				.removeClass(type);
			$(next)
				.removeClass(type)
				.removeClass(mode)
				.addClass('active');
				
			slider.opTrue();
		}, 500);
	}
	
	
	this.next = function(type, self){
		this.parent(self);
		var active;
		if(!(active = this.oport())) return false;
		var type = type || 'left';
		var mode = type == 'left' ? 'next' : 'prev';
		var next = type == 'left' ? $(active).next() : $(active).prev();
		if(!next.length){
			next = $(this.element).find(' .item:' + (type == 'left' ? 'first' : 'last'));
		}
		$(this.element).next().find('img').removeClass().eq(next.index()).addClass('active');
		this.go(active, next, mode, type);
	}
	
	this.identNext = function(elem){
		this.element = elem.closest('.thumbs').prev().children('.ss');
		if(elem.hasClass('active')) return;
		var active;
		if(!(active = this.oport())) return false;
		$(this.element).parent().next().find('img').removeClass();
		elem.addClass('active');
		var 
			mode = 'next',
			type = 'left';
			
		
		this.go(active, $(this.element).children('div').eq(elem.index()), mode, type);
	}
	
	this.parent = function(self){
		this.element = $(self).closest('.slider').eq(0);
	}
	
	this.active = function(){
		return $(this.element).find('.active');
	}
	
	this.opTrue = function(){
		this.op = true;
	}
	
	this.oport = function(){
		if(!this.op) return false;
		this.op = false;
		return this.active();
	}
		
	this.nextItem = function(){
		var current = this.count + 1;
		return this.list[current < this.count ? current : 1];
	}
	
	this.previous = function(){
		var current = this.count - 1;
		return this.list[current ? current : this.count];
	}
	
	this.getList = function(){
		return this.list;
	}
	
	this.getActive = function(){
		return $('.slider .ss').find('.item.active').next().css('display','block');
	}
	
	$(function(){
		$('.thumbs img').click(function(){
			slider.identNext($(this));
		});
		
		$('.controls > .arr-left').click(function(){
			slider.next('right', $(this));
		});
		
		$('.controls > .arr-right').click(function(){
			slider.next('left', $(this));
		});
	});
	
	
}

(function($){
	widgetSlide = false;
	$(function(){
		$('.carousel-widget .controls').click(function(e){
			if(widgetSlide) return;
			var $widget 		= $(this).closest('.carousel-widget');
			var $content 		= $widget.find('.widget-content');
			var $insideContent 	= $widget.find('.inside-content');
			var columns 		= $widget.data('carousel-widget-column-mobile') ?   $widget.data('carousel-widget-column-mobile'):
																					$widget.data('carousel-widget-column');
			var items 			= $insideContent.find('.item').length;
			if(items < columns) columns = items;
			var currentMargin 	= parseFloat($insideContent.css('margin-left'), 10);
			var right 			= e.target.className == 'rightside';
			var offset 			= ($content.width() + 30) / columns;
			var marginLimit 	= items * offset - (offset * columns);
			
			if(!right && !parseInt(currentMargin, 10))
				var margin = -marginLimit;
			else if(right && parseInt(currentMargin, 10) == -parseInt(marginLimit, 10)) 
				var margin = 0;
			else
				var margin = currentMargin + (right ? -offset : offset)
			//console.log(margin);
			widgetSlide = true;
			$insideContent.animate({'margin-left': margin + 'px'}, 200, function(){
				widgetSlide = false;
			});
		});
		
		$('.carousel-widget').each(function(i){
			carouselWidgetColumns(this);
		});
		
		widgetLevel = 0;
		widgetOffset = false;
		carouselWidgetResize();
		$(window).resize(function(){
			carouselWidgetResize();
		});
	});
})(jQuery);



$(function(){
	slider = new Slider('slider');
	shower = new Shower('.shower');
	
	if(document.location.host){
		$('img[alt="www.000webhost.com"]').remove();
	}
	
	setInterval(function(){
		$('.arr-right').click();
	}, 5000);
	
	$('.up').click(function(){
		$('html, body').animate({
			scrollTop: 0
		}, 'fast');
	});
	
	$(window).scroll(function(e){
		if(!menuFixed && $(window).scrollTop() >= 100){
			$('nav.menu').addClass('fixed');
			menuFixed = true;
		}
		
		if(menuFixed && $(window).scrollTop() < 100){
			$('nav.menu').removeClass('fixed');
			menuFixed = false;
		}
	});
	
	$('nav .top-menu').click(function(){
		$(this).toggleClass('visible');
	});
	
	$('.get-review-form').click(function(e){
		e.preventDefault();
		note.get('Оставить отзыв', 4);
		$('#captcha').prop('src', root + 'get-captcha-for-comment/');
	});
	
	$('.phone').click(function(e){
		e.preventDefault();
		note.get('Заказать обратный звонок', 3);
	});
	
	
	$('textarea.limit-symbols').keyup(function(){
		var limit = $(this).data('limit');
		if(!limit) return;
		if($(this).next()[0].className != 'limit'){
			$(this).after('<p class="limit"></p>');
		}
		$(this).next().text('Осталось: '+ (parseInt($(this).data('limit'), 10) - $(this).val().length) + ' символов');
	});
	
	$('.review-set').click(function(){
		var $name = $(this).parent().children('#review-name');
		var name = $name.val();
		if(!name){$name.css('border', '2px red solid'); return;}
		
		var $text = $(this).parent().children('#review-text');
		var text = $(this).parent().children('#review-text').val();
		if(!text){$text.css('border', '2px red solid');return;}
		
		var $captcha = $(this).parent().find('#captcha-code');
		if(!$captcha.val()){$captcha.css('border', '2px red solid');return;}
		
		var self = this;
		$.post(root + 'reviews/add/', {name:name,text:text, captcha:$captcha.val()}, function(msg){
			if(msg == 5){
				$captcha.css('border', '2px red solid');
				$(self).parent().find('.captcha-reload').click();
			}else{
				note.get('Отзыв', msg);
			}
		});
	});
	
});
var menuFixed = false;

var note = new Note();
var question;
function Note(){
	this.vis = true;
	
	this.get = function(title, content, submit){
		var title 		= title   || 'Уведомление';
		var content 	= content || 'Текст';
		var submit 	= submit  || false;
		
		
		$('#note-title').html(title);
		if(content == 2){
			this.clone('#order-question');
		}else if(content == 3){
			this.clone('#callme');
		}else if(content == 4){
			this.clone('#review-form');
		}else{
			$('#note-content').html(content);
		}
		
		if(submit) $('#note-submit').addClass('block');
		
		$('#note-wrap').addClass('block');
		$('#note')[0].offsetWidth;
		$('#note').css({marginTop: ($(window).height() < 470 ? '0' : '10%')});
		$('body').css('overflow', 'hidden');
		this.vis = false;
	}
	
	this.hide = function(){
		if(this.vis) return;
		$('#note').css({marginTop: '-400px'});
		setTimeout(function(){
			$('#note-wrap, #note-submit').removeClass('block');
			$('body').css('overflow', 'auto');
		}, 500);
	}
	
	this.clone = function(from){
		$('#note-content').html('');
		$(from).clone(true).addClass('block').appendTo('#note-content');
	}
}

var goZeroMargin = false;
function carouselWidgetColumns(elem, columns){
	var columns = columns || $(elem).data('carousel-widget-column');
	var items 	= $(elem).find('.item').length;
	if(items < columns) {
		columns = items;
	}
	$(elem).find('.item').css('width', ($(elem).find('.widget-content').width()) / columns - (columns > 1 ? 5 + columns * 5 : 0));
	var $inside = $(elem).find('.inside-content');
	if(parseFloat($inside.css('margin-left'), 10) && !goZeroMargin){
		goZeroMargin = true;
		$inside.animate({'margin-left': 0 + 'px'}, function(){
			goZeroMargin = false;
		});
	}
		
}

function carouselWidgetResize(){
	if($(window).width() > 620 && $(window).width() < 990 && widgetLevel != 1){
		widgetOffset = true;
	}else if($(window).width() > 996 && $(window).width() < 1050 && widgetLevel != 2){
		widgetOffset = true;
		widgetLevel = 2;
	}else if($(window).width() > 1060 && $(window).width() < 1200 && widgetLevel != 3){
		widgetOffset = true;
		widgetLevel = 3;
	}else if($(window).width() > 1202 && $(window).width() < 2000 && widgetLevel != 4){
		widgetOffset = true;
		widgetLevel = 4;
	}else if($(window).width() < 600){
		widgetOffset = true;
		widgetLevel = 6;
	}
	
	if(widgetOffset){
		var columns;
		if($(window).width() < 600){
			columns = 1;
			widgetLevel = 0;
		}else{
			columns = false;
		}
		
		$('.carousel-widget').data('carousel-widget-column-mobile', columns);
		$('.carousel-widget').each(function(i){
			carouselWidgetColumns(this, columns);
		});
		widgetOffset = false;
	}
}


function Shower(cl)
{
	var cl = cl || '.shower';
	
	//var $imgs = $('img'+cl);
	var $imgs = $('.shower img, img.shower');
	var $wrapper = $('#shower');
	var $imgWrap = $('#shower > #img');
	var $img = $imgWrap.children('img');
	var self = this;
	
	this.index = 0;
	
	$imgWrap.children('#counter').html((this.index+1)+' / '+$imgs.length);
	
	this.get = function(img){
		var newSrc = $(img).attr('data-large-img') ? 'data-large-img' : 'src';
		$img.attr({'src':$(img).attr(newSrc)});
		
		if(!$wrapper.hasClass('block')){
			$wrapper.addClass('block');
			$wrapper.animate({'opacity': 1}, 500);
		}
		if($imgWrap.height() > $(window).height() - 30) {
			$img.css('max-height', $(window).height() - 150);
			$img.css('min-width', 'auto');
		}
		$imgWrap.css({
			'margin-left': ((-$imgWrap.width()/2-20)+'px'), 
			'margin-top': (($imgWrap.height() > $(window).height() ? 0 : 20)+'px'), 
			'visibility':'visible'
		});
		
		$img.animate({'opacity': 1}, 300);
	}
	
	this.hide = function(){
		self.index = 0;
		self.setCounter();
		$wrapper.animate({'opacity': 0}, 500, function(){
			$wrapper.removeClass('block');
			$imgWrap.css({'visibility':'hidden'});
		})
	}
	
	this.getIndex = function(img1){
		var index = 0;
		$imgs.each(function(i, img){
			if($(img)[0] == $(img1)[0]){
				index = i;
				return false;
			}
		});
		
		return index;
	}
	
	this.setCounter = function(){
		$imgWrap.children('#counter').html((self.index+1)+' / '+$imgs.length);
	}
	
	$imgs.click(function(){
		self.index = self.getIndex(this);
		self.setCounter();
		self.get(this);
	});
	
	$('.shower .img').click(function(){
		var img = $(this).children('img');
		self.index = self.getIndex(img);
		self.setCounter();
		self.get(img);
	});
	
	$('#shower #close, #shower > span').click(function(){
		self.hide();
	});
	
	$('#showerTools div:first').click(function(){
		
		self.index--;
		if(self.index < 0){
			self.hide();
			return false;
		}else
			$img.animate({'opacity': 0}, 300, function(){self.get($imgs[self.index])});
			
		
		self.setCounter();
	});
	
	$('#showerTools div:last').click(function(){
		self.index++;
		if(self.index >= $imgs.length){
			self.index = 0;
			self.hide();
			return false;
		}else
			$img.animate({'opacity': 0}, 300, function(){self.get($imgs[self.index])});
		
		self.setCounter();
	});
}
	
$(function(){
	$('.nav a[href="'+document.location.pathname+'"]').addClass('active');
	if(document.location.pathname.toString == root){
		$('.menu a[href="'+root+'"]').addClass('active');
	}else{
		var parts = document.location.pathname.split(root)[1].split('/');
		$('.menu a[href="'+root+parts[1]+'/"]').addClass('active');
	}
	var $active = $('.menu a[href="'+document.location.pathname+'"]');
	if(!$active.parent('li').length){
		$active.closest('li.top-menu').addClass('active');
	}else{
		$active.parent().addClass('active').closest('li.top-menu').addClass('active')
	}
	
	$('.heroes-catalog a[href="'+document.URL+'"]').addClass('active');
	
	$('#comments-block-form').submit(function(e){
		e.preventDefault();
		var self = this;
		var comment = $(this.elements.content).val().replace(/\s+/g, ' ');
		var login = $(this.elements.login).val().replace(/\s+/g, ' ');
		if(comment == '' || comment == ' ')	{alert('Введите сообщение!');return;}
		if(login == '' || login == ' ')		{alert('Введите ваше имя!');return;}

		var comment_count = $('#comment-count').text();
		$.post(root + 'user/comments/add/' + $(this.elements.post_id).val() + '/', {
			login: login,
			comment: comment,
			comment_parent: $(this.elements.parent).val(),
			comment_count: comment_count,
			captcha_code: $(this.elements.captcha_code).val(),
		}, function(data){
			if (typeof data.error !== 'undefined') {
				alert(data.error);
				return;
			}
			if(data.comment_parent){
				var $parent = $('[data-id="'+data.comment_parent+'"]');
				var $sub = $parent.find('td.sub-comments');
				
				if($sub.length){
					//$sub.append(data.comment);
					$(data.comment).insertBefore($('#comments-block-form'))
				}else{
					$parent.append('<tr><td colspan="3" class="sub-comments"><div style="">Ответы (1)</div>' + data.comment + '</td></tr>');
				}
				
			}else{
				$('#post-comments .block-content').prepend(data.comment);
			}
			$('#comment-count').text((+comment_count+1));
			$('#comment-text, #captcha-code').val('');
			$('#comment-parent').val('0');
			captchaReload();
		}, 'json');
		
	});
	
	$('table .icon-comment, table .comment-author').click(function(){
		var item = $(this).closest('table');
		//console.log($(item));
		$(item).after($('#comments-block-form'));
		$('#comment-text').val((!item.hasClass('general') ? '+' : '')+item.data('author') + ', ' + $('#comment-text').val()).focus();
		$('#comment-parent').val(item.hasClass('general') ? item.data('id') : item.closest('table.general').data('id'));
		window.scrollTo(0, $('#comment-text').offset().top - 300);
	});
	
	$('.captcha-reload').on('click', function(){
		captchaReload();
	});
});

function captchaReload(){
	if(typeof captcha1 != 'undefined' && captcha1) return false;
	captcha1 = true; 
	$('#captcha').addClass('rotate1').attr('src', root+'get-captcha-for-comment/?async&'+Math.random())
	setTimeout(function(){ 
		$('#captcha').removeClass('rotate1');
		captcha1 = false; 
	}, 1000)
}
