<?php
include THEME_DIR . 'header.php';
?>

<style>
	
	.slider-new{
		border: 2px black solid;
		overflow: hidden;
		background: linear-gradient(-30deg, #000515, #6fd0da);
	}
	
	#s{
		cursor: grab;
		width: 9999px;
	}
	
	#s:active{
		cursor: grabbing;
	}
	
	.item{
		display: inline-block;
		~border: 1px red dashed;
		width: 70%;
	}
	
	.item img{
		width: 100%;
		transform: scale(.85);
		transition: .8s;
		border-radius: 30px;
		border: 5px transparent solid;
	}
	
	.item.active img{
		transform: scale(1);
		border: 5px #a7dcdc solid;
		box-shadow: 0 0 100px #ffffff;
	}
	
	
	.item, #s1{
		position: relative;
		padding: 30px 0 ;
	}
	
	span{
		position: relative;
		left: 50%;
	}
</style>

<script>
	$$(() => {
		var ww = $(window).width();
		var wcenter = ww / 2;
		var t0;
		var move, index = 0;
		
		build();
		var slideNew = document.getElementById('s1');

		slideNew.onmousedown = function(e) {
			t0 = performance.now();
			$('#s1').stop();

		  var coords = getCoords(slideNew);
		  var shiftX = e.pageX - coords.left;
		  
		  move = coords.left;

		  slideNew.style.zIndex = 1000;

		  function moveAt(e) {
			slideNew.style.left = e.pageX - shiftX + 'px';
		  }

		  document.onmousemove = function(e) {
			moveAt(e);
		  };

		  document.onmouseup = function() {
			document.onmousemove = null;
			slideNew.onmouseup = null;
			
			move -= getCoords(slideNew).left;
			
			if (performance.now() - t0 < 200) {
				index = move > 0 ? index + 1 : index - 1;
				
				if (index < 0) index = 0;
				else if (index + 1 > $('.item').length) index = $('.item').length - 1;
			} else {
				var center = false;
				var coords;
				$('.item').each(function(i){
					coords = $(this).offset().left + ($(this).width() / 2) - wcenter;
					
					
					if (center === false || Math.abs(coords) < Math.abs(center)) {
						center = coords;
						index = i;
					}
				});
			}
			
			makeActive(index);
		  };
		}

		slideNew.ondragstart = function() {
		  return false;
		};

		function getCoords(elem) {   // кроме IE8-
		  var box = elem.getBoundingClientRect();
		  return {
			left: box.left + pageXOffset
		  };
		}
		
		function build(){
			$('.item').css('width', ww * 0.7 + 'px');
			makeActive(0);
		}
		
		function makeActive(index){
			var parentPos = $('.item').parent().offset();
			var $activeItem = $('.item:nth-child('+(index + 1)+')');
			var left = wcenter - $activeItem.width() / 2;
			left -= $activeItem.offset().left - parentPos.left;
			
			setTimeout(() => {$('.item').removeClass('active');}, 0);
			setTimeout(() => {$activeItem.addClass('active');}, 0);
			$('#s1').stop().animate({'left': left}, 1000);
			setTimeout(() => {$('#s1')}, 1000);
			//go(left)();
			
		}
		
		function go(left){
			var currentPos = +$('#s1').css('left').split('px')[0];
			var stepCount = 40;
			var step = left / stepCount;
			var up = currentPos < left;
			
			
			return function(){
				if (--stepCount) {
					currentPos = up ? currentPos + step : currentPos - step;
					setTimeout(() => {$('#s1').css({'left': currentPos});}, 25);
					console.log(1);
				}
			}
		}
		
		function fastScroll(){
			
		}
	});
</script>

<div class="slider-new clearfix" id="slider-new">
	<span>©</span>
	<div id="s">
	<div id="s1">
		<div class="item"><img src="<?=THEME . 'img/002.jpg'?>"></div>
		<div class="item"><img src="<?=THEME . 'img/002.jpg'?>"></div>
		<div class="item"><img src="<?=THEME . 'img/002.jpg'?>"></div>
		<div class="item"><img src="<?=THEME . 'img/002.jpg'?>"></div>
	</div>
	</div>
</div>
<?php
include THEME_DIR . 'footer.php';
?>