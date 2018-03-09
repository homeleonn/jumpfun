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
	
}


	$(function(){
		slider = new Slider('slider');
		menuFix = false;
		$(window)
			.keyup(function(e){
				//console.log(e.which);
				switch(e.which){
					case 37: slider.next('right');break;
					case 39: slider.next('left');break;
					case 27: note.hide();break;
				}
			})
			/*.scroll(function(){
				if($(window).scrollTop() > 40){
					if(!menuFix){
						menuFix = true;
						$('nav').addClass('fix');
					}
				}else{
					if(menuFix){
						menuFix = false;
						$('nav').removeClass('fix');
					}
				}
			});*/
		
		$('#hide-nav').click(function(){
			$('nav, nav ul').toggleClass('block width');
		});
		
		$('.btn-tr').click(function(){
			$("html, body").animate({ scrollTop: $('#about').offset().top - 80 }, 1000);
		});
		
		$('.thumbs img').click(function(){
			slider.identNext($(this));
		});
		
		servicesHide = true;
		setInterval(function(){
			var active = $('#head div.active');
			var next = active.next();
			if(!next.length) next = $('#head div:first');
			active.removeClass('active');
			next.addClass('active');
			//$('.arr-right').click();
		}, 5000);
		
		$('button#services').click(function(){
			var list = $(this).parent().parent().find('div:first');
			if(servicesHide){
				$(this).html('Скрыть');
			}else{
				$(this).html('Еще');
			}
			servicesHide = !servicesHide;
			list.toggle('normal');
		});
		
		$('#up').click(function(){
			$('html, body').animate({scrollTop: 0}, 'normal');
		});
		
		
		$('div#order, .getform').click(function(){
			if($('#order-form').length){
				$('html, body').animate({scrollTop: $('#order-form').offset().top - 90}, 1000, function(){
					if($('html, body').offset().top != $('#order-form').offset().top - 90)
						$('html, body').animate({scrollTop: $('#order-form').offset().top - 90}, 500);
				});
			}else{
				note.get('Оформить заказ', 2);
			}
		});
		
		
		
		
		/*NOTIFICATION*/
		
		
		
		var validmail = true;
		$('#mail').blur(function(){
			var mail = $("#mail").val();
			var color = 'red';
			var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,10}\.)?[a-z]{2,10}$/i;
			if(pattern.test(mail)){
				validmail 	= true;
				color 		= 'green';
			}
			
			$('#mail').css('border', '2px '+ color +' solid');
		});
		
		
		
		
		$('#orderform').submit(function(e){
			e.preventDefault();
			
			var title = 'Оформление заказа';
			if(!validmail){
				note.get(title, 'Почтовый адрес введен некорректно. Перепроверьте введенные данные');
				return false;
			}
			
			var name = $("#name").val();
			var tel  = $("#tel").val();
			var mail = $("#mail").val();
			var message = $("#message").val();
			
			
			if(!checkMail(mail)){
				alert('Почта введена некорректно. Проверьте данные');
				return;
			}
			
			
			$.post('j.php', {order:1,name:name,tel:tel,mail:mail, text:message}, function(msg){
				note.get(title, msg);
			});
		});
		
		
		
		
		$('#head-call, #call-set').click(function(){
			var tel = $(this.id == 'head-call' ? "#call" : "#call-tel").val();
			if(!checkTel(tel)) return;
			$.post('j.php', {needCall:1,tel:tel}, function(msg){
				note.get('Заказать звонок', msg);
			});
		});
		
		$('.slide-description > .button7').click(function(){
			chooseModel = $(this).parent().find('.model').html();
			note.get('Заказать авто', 2);
		});
		
		
		$('#q-set').click(function(){
			//console.log($('#qq').html(), cont + (chooseModel ? '(При написании заказа пользователь выбрал автомобиль марки: '+chooseModel+')' : ''));
			var title = $('#note-title').html();
			//console.log(title);
			writeOrder(title == 'Задать вопрос менеджеру' || title == 'Заголовок' ? 3 : (title == 'Заказать авто' ? 4 : (title == 'Оформить заказ' ? 5 : 6)));
		});
		
		
		
		$("img.lazy").lazyload({threshold: 100, effect: "fadeIn"});
		
		if($(window).width() < 1000){
			$('img.lazy').each(function(){
				$(this).attr({'src':$(this).attr('data-original')});
			});
		}
		
	});
	
	function checkTel(tel){
		var telPattern = /^(\+\d+(-|\s)?)?\(?\d{3}\)?(-|\s)?\d{3}(-|\s)?\d{2}(-|\s)?\d{2}$/;
		if(!telPattern.test(tel)){
			note.get('Ошибка', 'Неверный формат номера телефона. Необходимый вид формата (код оператора) 000 00 00');
			return false;
		}
		
		return true;
	}
	

	function writeOrder(type){
		var name = $("#qname").val();
		var tel  = $("#qtel").val();
		var mail = $("#qmail").val();
		var text = $("#qq").val();
		var title = $('#note-title').html();
		
		if(!checkMail(mail)){
			alert('Почта введена некорректно. Проверьте данные');
			return;
		}
		
		$.post('j.php', {orderType:type,name:name,tel:tel,mail:mail,text:text}, function(msg){
			note.get(title, msg);
		});
	}
	
	function callMe(){
		
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
		
		
	
	
	
	
	var chooseModel = '';
	
	
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
			}else{
				$('#note-content').html(content);
			}
			
			if(submit) $('#note-submit').addClass('block');
			
			$('#note-wrap').addClass('block');
			$('#note')[0].offsetWidth;
			$('#note').css({marginTop: ($(window).height() < 470 ? '0' : '20%')});
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
		
var shower = new Shower();

function Shower(cl)
{
	if(!$('#shower').length){
		$('body').after('<div id="shower"><span></span><div id="img"><img src="" alt="Просмотр изображения"><div  id="showerTools"> <div></div><div></div> </div> <div id="counter">0 / 0</div> <div id="close">x</div></div></div>');
	}
	var cl = cl || '.shower';
	var $imgs = $('img'+cl);
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
	
	this.getIndex = function(src){
		var index = 0;
		$imgs.each(function(i, img){
			if($(img).attr('src') == src){
				index = i;
				return false;
			}
		});
		
		return index;
	}
	
	this.setCounter = function(){
		$imgWrap.children('#counter').html((self.index+1)+' / '+$imgs.length);
	}
	$(function(){
		$('body').on('click', 'img'+cl, function(){
			self.index = self.getIndex($(this).attr('src'));
			self.setCounter();
			self.get(this);
		});
		// $imgs.click(function(){
			// self.index = self.getIndex($(this).attr('src'));
			// self.setCounter();
			// self.get(this);
		// });
		
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
	});
}


$(function(){
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
				
				if($sub.find('td.sub-comments').length){
					$sub.append(data.comment);
				}else{
					$parent.append('<tr><td colspan="3" class="sub-comments">' + data.comment + '</td></tr>');
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
		$('#comment-text').val(item.data('author') + ', ' + $('#comment-text').val()).focus();
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
