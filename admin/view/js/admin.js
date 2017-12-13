//

if(typeof $ == "undefined" && typeof jQuery == "undefined"){
	var script = document.createElement('script');
	script.src = path+'admin/view/js/jq3.js';
	script.async = false;
	document.head.appendChild(script);
}

function cacheClear()
{
	$.post(path+'admin/cache/clear/all/', function(data){
		alert('Успешно!');
	});
}

//pricelist
function pricelistUppdate(){
	$.post(root + 'admin/cache/clear/pricelist/', function(msg){
		//pageRefresh();
		alert('Успешно!');
	});
}



//---

$(function(){
	$('#replace-price').submit(function(e){
		e.preventDefault();
		var fd = new FormData(this);
		$.ajax({
			url : root + 'admin/upload/',
			type: "POST",processData: false,contentType: false,data: fd, 
			success: function(data){
				alert(data);
				activeInput.value = "";
				delete(file);
			}
		});
	});

	$('#thisprice, #fullprice').change(function(){
		activeInput = this;
		file = this.files[0];
		$('#replace-price').submit();
	});
	
	$('#adminpanel > div').click(function(){
		$('#adminpanel > ul').toggleClass('showadmin');
	});
});