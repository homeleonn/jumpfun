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
		//clearTimeout(frontSliderTimer);	
		this.parent(self);
		var active;
		if(!(active = this.oport())) return false;
		var type = type || 'left';
		var mode = type == 'left' ? 'next' : 'prev';
		var next = type == 'left' ? $(active).next() : $(active).prev();
		if(!next.length){
			next = $(this.element).find(' .item:' + (type == 'left' ? 'first' : 'last'));
		}
		var nextImg = next.children('img');
		var src = nextImg.attr('data-src');
		if(src){ 
			nextImg.removeAttr('data-src');
			nextImg.attr('src', src);
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
	
	this.goProgress = function(width, $progressbar){
		return false;
		if(yout) return;
		var width = width || 0;
		if(!width) clearTimeout(sliderProgressBarTimer);
		sliderProgressBarTimer = setInterval(function(){
			if((width += 0.85) >= 100) {
				clearTimeout(sliderProgressBarTimer);
			}
			$progressbar.css('width', width + '%');
		}, 40);
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
		
		$('.controls > .arr-right, .controls > .arr-left').click(function(){
			clearTimeout(frontSliderTimer);	
			clearTimeout(sliderProgressBarTimer);
			if($(window).width() < 480) return;
			var self = this;
			var width = 0;
			var $progressbar = $(self).closest('.slider-wrapper').find('.progressbar');
			$progressbar.css('width', width + '%');
			yout = false;
			runFrontSlider();
			slider.goProgress(width, $progressbar);
		});
		slider.goProgress(0, $('.main .slider-wrapper .progressbar'));
		
		$('.main .slider-wrapper .item').hover(
			function() {
				clearTimeout(frontSliderTimer);	
				clearTimeout(sliderProgressBarTimer);
			}, function() {
				clearTimeout(frontSliderTimer);	
				clearTimeout(sliderProgressBarTimer);
				if(yout) return;
				var currentWidth = $('.main .slider-wrapper').find('.progressbar').width() / $('.main .slider-wrapper').find('.ss').width() * 100;
				runFrontSlider(5000 - 5000 * (currentWidth / 100));
				slider.goProgress(currentWidth, $('.main .slider-wrapper').find('.progressbar'));
			}
		);
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


;(function($){
	$(function(){
		if(!isFrontPage()){
			var load = false;
			$('.heroes-catalog .item').hover(function(){
				if(!load){
					load = true;
					$('.heroes-catalog img').each(function(){
						var data = $(this).data('src');
						if(data){
							this.src = data;
						}
					});
				}
			});
		}else{
			var activeImg, w, h, d1, d2;
			$('.girl-left').hover(
				function(){
					activeImg = this;
					w = $(this).width();
					h = $(this).height();
					$(this).css('transform', 'rotateY(60deg)');
				},
				function(){
					$(this).css({'transform': 'rotateX(0deg) rotateY(0deg)', 'left' : 50 +'px', 'top' : 30+'px'});
				},
			)
			
			$('.girl-left').mousemove(function(e){
				// console.log(e.offsetX, e.offsetY);
				// d1 = e.offsetX >= h ? '-' : '';
				// d2 = e.offsetY >= w ? '-' : '';
				
				// if(e.offsetX >= w){
					// d1 = '';
					// d2 = e.offsetY >= h ? '-' : '-';
				// }else{
					// d1 = '-';
					// d2 = e.offsetY >= h ? '-' : '';
				// }
				// $(this).css('transform', 'rotateX('+d2+'30deg) rotateY('+d1+'30deg)');
				
				d1 = e.offsetX / w * 30;
				d2 = e.offsetY / h * 30;
				if(e.offsetX > (w / 2)) d2 = - d2;
				if(e.offsetY > (h / 2)) d1 = - d1;
				//$(this).css({'transform':'rotateX('+d1+'deg) rotateY('+d2+'deg)', 'left' : d1 +'px', 'top' : d2+'px'});
				$(this).css({'transform':'rotateX('+d1+'deg)'});
			})
		}
	});
	
})(jQuery);




function isFrontPage(){
	return document.location.pathname == root;
}

var frontSliderTimer, sliderProgressBarTimer;
function runFrontSlider(once){
	var once = once || 5000;
	// var once = 1000;
	frontSliderTimer = setTimeout(function(){
		$('.arr-right').click();
	}, once);
}

;(function($){
	var state = 0;
	var prev = state;
	var down = true;
	
	function addClass(prevState, currentState){
		$('.main > .header > .top-sky > .air-balloons-left,\
			.main > .header > .top-sky > .air-balloons-right').removeClass('animate' + prevState).addClass('animate' + currentState);
	}
	
	$(function(){
		addClass(0, 0);
		setInterval(function(){
			var prev = state;
			state = down ? state + 1 : state - 1;
			
			addClass(prev, state);
			
			if(state == 3) down = false;
			else if(!state) down = true;
		}, 5 * 1000);
	});
})//(jQuery);


var yout = false;
$(function(){
	slider = new Slider('slider');
	shower = new Shower('.shower');
	
	$('body').addClass('loaded');
	if(isFrontPage()){
		runFrontSlider();
		$('.wrapper.main').toggleClass('animate');
		setInterval(function(){
			$('.wrapper.main').toggleClass('animate');
		}, 20 * 1000);
	}
	
	
	
	setInterval(function(){
		$('.phone').toggleClass('red');
	}, 1000);
	
	if(document.location.host){
		$('img[alt="www.000webhost.com"]').remove();
	}
	
	
	$('.slider-wrapper .yout').click(function(){
		yout = true;
		var img = $(this).find('img');
		if(!img.length) return;
		$(this).closest('.item').html('<iframe width="1120" height="385" src="https://www.youtube.com/embed/'+img.data('youtube')+'/?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
		if($(this).closest('.wrapper.main').length){
			clearTimeout(frontSliderTimer);
			clearTimeout(sliderProgressBarTimer);
		}
	});
	
	$('.yout1').click(function(){
		var img = $(this).find('img');
		if(!img.length) return;
		$(this).html('<iframe width="1120" height="385" src="https://www.youtube.com/embed/'+img.data('youtube')+'/?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>');
	}); 
	
	
	$(".front-menu").on("click","a", function (event) {
		event.preventDefault();
		var id  = $(this).attr('href'),
			top = $(id).offset().top;
		$('body,html').animate({scrollTop: top - 50}, 500);
	});
	
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
	
	$('.phone, .phone-top, .bottom-phone .icon-phone').click(function(e){
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
	
	
	$('#callme .send').click(function(){
		requestSet(3, this);
	});
	
	$('#review-form .send').click(function(){
		requestSet(4, this);
	});
	
});

var menuFixed = false;

function requestSet(type, el){
	var type = type || 2;
	var data = {};
	var title = 'Сообщение';
	var $wrapper = $(el).closest('.request-wrapper');
	
	var $captcha = $wrapper.find(".captcha-wrapper");
	
	if(!$captcha.hasClass('none')){
		var captcha = $captcha.find('.captcha-code').val();
		if(!captcha.length){
			fillIErrorInput($captcha.find('.captcha-code'));
			$captcha.find('.captcha-reload').click();
			return;
		}
	}
	switch(type){
		case 3:{
			var $tel = $wrapper.find('.tel');
			if(!$tel.val()) {fillIErrorInput($tel);return;}
			if(checkTel($tel.val())){
				data.type 	= type;
				data.tel 	= $tel.val();
				data.captcha= captcha;
				title = 'Обратный звонок';
			}else{
				fillIErrorInput($tel);
				return;
			}
		}break;
		case 4:{
			var $name = $wrapper.find('.name');
			var $text = $wrapper.find('textarea.text');
			if(!$name.val()) {fillIErrorInput($name);return;}
			if(!$text.val()) {fillIErrorInput($text);return;}
			data.type 	= type;
			data.name 	= $name.val();
			data.text 	= $text.val();
			data.captcha= captcha;
			title = 'Отзыв';
		}break;
	}
	
	$.post(root + 'reviews/mail/', data, function(msg){
		if(msg == 999){
			note.get('Ошибка', 'Неопределенная ошибка');
		}else if(msg == 0){
			fillIErrorInput($captcha.find('.captcha-code'));
			$captcha.find('.captcha-reload').click();
		}else if(msg == 1){
			$captcha.removeClass('none');
			$captcha.find('img.captcha').prop('src', root + 'get-captcha-for-comment/');
			return;
		}else{
			note.get(title, msg);
		}
	});
}

function fillIErrorInput($input){console.log($input);
	$input.css('border', '2px red solid');
}

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
		
		if(content == 3 || content == 4){
			$('#captcha-wrapper').clone().addClass('none').insertBefore($('#note-content .send'));
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
	
	var $imgs = $('a' + cl);
	var $wrapper = $('#shower');
	var $imgWrap = $('#shower > #img');
	var $img = $imgWrap.children('img');
	var $title = $imgWrap.children('#shower-title');
	var self = this;
	
	this.index = 0;
	
	$imgWrap.children('#counter').html((this.index+1)+' / '+$imgs.length);
	
	this.get = function(src){
		//var newSrc = $(img).attr('data-large-img') ? 'data-large-img' : 'src';
		//$img.attr({'src':$(img).attr(src)});
		var ww = $(window).width();
		var wh = $(window).height();
		
		var showImg = new Image();
		showImg.onload = function(){
			$img.attr({'src':src.href});
			
			if(!$wrapper.hasClass('block')){
				$wrapper.addClass('block');
				$wrapper.animate({'opacity': 1}, 500);
			}
			
			var nw = $img[0].naturalWidth;
			var nh = $img[0].naturalHeight;
			
			//$img.css({'width': nw, 'height': nh,'max-height': wh - 100});
			$img.css({'width': 'auto', 'height': 'auto','max-height': wh - 100});
			//$img.css({'max-height': wh - 100});
			var h = $img.height();
			if(h != nh){
				var newWidth = nw * (h / nh);
				if(newWidth > ww){
					$img.css('height', nw * (newWidth / nw));
				}
				$img.css('width', newWidth);
			}
			
			$imgWrap.css({
				'margin-left': ((-$imgWrap.width()/2-20)+'px'), 
				'margin-top': (($imgWrap.height() > wh ? 0 : 20)+'px'), 
				'visibility':'visible'
			});
			
			$img.animate({'opacity': 1}, 300);
		}
		showImg.src = src.href;
		$title.text(src.title);
		self.setCounter();
	}
	
	this.hide = function(){
		self.index = 0;
		$wrapper.animate({'opacity': 0}, 500, function(){
			$wrapper.removeClass('block');
			$imgWrap.css({'visibility':'hidden'});
			self.setCounter();
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
	
	// $imgs.click(function(){
		// self.index = self.getIndex(this);
		// self.setCounter();
		// self.get(this);
	// });
	
	// $('.shower .img').click(function(){
		// var img = $(this).find('img');
		// self.index = self.getIndex(img);
		// self.setCounter();
		// self.get(img);
	// });
	
	$('#shower #close, #shower > span').click(function(){
		self.hide();
	});
	
	$('#shower-prev').click(function(){
		self.index--;
		if(self.index < 0){
			self.hide();
			return false;
		}else
			$img.animate({'opacity': 0}, 300, function(){self.get($imgs[self.index])});
			
		
		self.setCounter();
	});
	
	$('#shower-next').click(function(){
		self.index++;
		if(self.index >= $imgs.length){
			self.index = 0;
			self.hide();
			return false;
		}else
			$img.animate({'opacity': 0}, 300, function(){self.get($imgs[self.index])});
	});
	
	$('a.shower').click(function(e){
		e.preventDefault();
		self.index = self.getIndex(this);
		self.get(this);
	});
}
	
$(function(){
	$('.nav a[href="'+document.location.pathname+'"]').addClass('active');
	if(document.location.pathname.toString == root){
		$('.menu a[href="'+root+'"]').addClass('active');
	}else{
		var parts = document.location.pathname.split(root)[1].split('/');
		$('.menu a[href="'+root+parts[0]+'/"]').addClass('active');
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
					$parent.append('<tr><td colspan="3" class="sub-comments"><div>Ответы (1)</div>' + data.comment + '</td></tr>');
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
	
	$('body').on('click', '.captcha-reload', function(){
		captchaReload(this);
	});
});

function captchaReload(el){
	if(typeof captcha1 != 'undefined' && captcha1) return false;
	captcha1 = true; 
	console.log($(el), el);
	var $captcha = $(el).closest('.captcha-wrapper').find('.captcha');
	$captcha.addClass('rotate1').attr('src', root+'get-captcha-for-comment/?async&'+Math.random())
	setTimeout(function(){ 
		$captcha.removeClass('rotate1');
		captcha1 = false; 
	}, 1000);
}

;(function($){
	$(function(){
		$('#order-question #q-set').click(function(e){
			e.preventDefault();
			var $parent = $(this).closest('#order-question');
			var name = $parent.find("#qname").val();
			var tel  = $parent.find("#qtel").val();
			var mail = $parent.find("#qmail").val();
			var message = $parent.find("#qq").val();
			var captcha = '';
			
			if(!message) message = '';
			if(!name.length){
				note.get('Ошибка', 'Введите Ваше имя');
				return;
			}
			
			
			if(!checkMail(mail)){
				note.get('Ошибка', 'Почта введена некорректно. Проверьте данные');
				return;
			}
			
			if(!checkTel(tel)){
				return;
			}
			
			
			var $captcha = $parent.find(".captcha-wrapper");
			
			if(!$captcha.hasClass('none')){
				var captcha = $captcha.find('.captcha-code').val();
				if(!captcha.length){
					note.get('Ошибка', 'Введите защитный код');
					return;
				}
			}
			
			$.post(root + 'reviews/mail/', {type:2,name:name,tel:tel,mail:mail,text:message,captcha:captcha}, function(msg){
				if(msg == 0){
					note.get('Сообщение', 'Неверно введен защитный код');
					$captcha.find('.captcha-reload').click();
				}else if(msg == 1){
					$captcha.removeClass('none');
					$captcha.find('img.captcha').prop('src', root + 'get-captcha-for-comment/');
					return;
				}else{
					$parent.find("#qname, #qtel, #qmail, #qq, #captcha-code").val('');
					if(captcha.length)
						$captcha.find('.captcha-reload').click();
					note.get('Сообщение', msg);
				}
			});
		});
	});
})(jQuery);



function checkTel(tel){
	var telPattern = /^(\+\d+(-|\s)?)?\(?\d{3}\)?(-|\s)?\d{3}(-|\s)?\d{2}(-|\s)?\d{2}$/;
	if(!telPattern.test(tel)){
		note.get('Ошибка', 'Неверный формат номера телефона. Необходимый вид формата (код оператора) 000 00 00');
		return false;
	}
	
	return true;
}

function checkBlurMail(self){
	var mail = $(self).val();
	var color = 'red';
	
	if(checkMail(mail)){
		validmail 	= true;
		color 		= 'green';
	}
	
	$(self).css('border', '2px '+ color +' solid');
}


function checkMail(mail){
	var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,10}\.)?[a-z]{2,10}$/i;
	if(!pattern.test(mail)){
		return false;
	}
	
	return true;
}
	
	
