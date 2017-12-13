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
		
		
		
		
		$('form:first').submit(function(e){
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
		
