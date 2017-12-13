function Upload() {
	if(Upload.created) return false;
	Upload.created = true;
	var progressWidth = 100;
	var p;
	var self = this;
	
	this.log = function(text){
		$('#loading-progress').html(text);
	}
	
	this.reset = function(){
		if(self.uploadFileName){
			$.post(self.uploadPath + 'delete/', {del:self.uploadFileName});
		}
		Upload.created = false;
		$('#upload-img')[0].value = '';
		$('#upload-img').show();
		$('#file-response > input').hide();
		$('#uploadimgname').val('');
		
	}
	
	this.success = function(responseText){
		Upload.created = false;
		//console.log(responseText);//return;
		$('#loading').hide();
		if(responseText.error){
			self.log(responseText.error);
			self.reset();
		}else{
			var img = responseText.imgsrc;
			$('#upload-img').hide();
			$('#file-response > input').show();
			self.uploadFileName = responseText.imgsrc;
			$('#uploadimgname').val(img.substr(img.lastIndexOf('/')+1));
			self.log('<img style="width: 100px;" src="'+img+'">');
		}
	}
	
	this.upload = function(file, uploadPath){
		var formData = new FormData();
		self.file = file;
		self.uploadPath = uploadPath;
		self.uploadFileName = false;
		formData.append('image', self.file);
		formData.append('to', to);
		var xhr = new XMLHttpRequest();
		$('#loading').show();
		
		xhr.onload = xhr.onerror = function() {
			if (this.status == 200) {
				//console.log(this.responseText);return;
				self.success(JSON.parse(this.responseText));
			} else {
				self.log("error " + this.status);
			}
		};

		// обработчик для закачки
		xhr.upload.onprogress = function(event) {
			p = event.loaded/event.total*100;
			$('#loading img').css('width', p + 'px');
			self.log(Math.round(p) + '%');
		}
		
		xhr.open("POST", self.uploadPath, true);
		xhr.setRequestHeader("X-Requested-With", 'XMLHttpRequest');
		xhr.send(formData);
	}
}
Upload.created = false;

function checkImg(w, h, file, now, imgIndex){
	var now = now || false;
	var reader = new FileReader();
	//var file = el.files[0];
	
	
	reader.onload = function(e) {
	  var img = document.createElement('img');
	  
	  img.onload = function() {
		var err = '';
		
		/*if(!file.name.match(/[a-zA-Z0-9-_]+\.(jpg|jpeg|png)/i)){
			err = (++i)+') Недопустимое имя. допускаются латинские буквы, цифры от 0 до 9 и знаки -_. Так же поддерживаются только такое форматы: jpg, jpeg, png<br>';
		}
		
		if(this.width != w && this.height != h){
			err += (++i)+') Размеры изображения не подходят: '
			+this.width+'x'+this.height+'. <br>Необходимые размеры: '+w+'x'+h;
		}
		
		if(err){
			$('#loading-progress').html('<div style="color: indianred;">'+err+'</div>');
			$('#error').show();
			return false;
		}*/
		
		
		if(!checkFileSize(file)) err = true;
		
		if(err) goToError();
		
		if(now){
			if(typeof(uploading) == "undefined"){
				window.uploading = new Upload();
			}
			uploading.upload(file, root+"admin/upload/");
		}else{
			
			$('#morephotosContainer').append('<div class="imgIndex" id="imgIndex'+imgIndex+'">');
			$('#imgIndex'+imgIndex).append(this);
			$('#imgIndex'+imgIndex).append('<img src="'+root+'admin/view/img/del.png" class="del" onclick="delFile(this,'+imgIndex+')">');
		}
	 };
	  
		
	  
	 img.src = e.target.result;
	  
	}

	reader.readAsDataURL(file);
}


function checkFileSize(file){
	var kb = (file.size / 1024).toFixed(3);
	if(kb > 1048){
		$('#error').append('<div>Размер файла "'+file.name+'" превышает 1MB</div>');
		return false;
	}
	return true;
}

function goToError(){
	$('#error').addClass('block');
	$('body, html').scrollTop($('#error').offset().top);
}


var imgIndex = 0;
$(function(){
	$('#upload-img').change(function(){
		var file = $('#upload-img')[0].files[0];
		checkImg(200, 300, file, true);
	});
	
	$('#morephotos').change(function(e){
		if(filesUpload.length >= 20){
			alert('Больше 20 изображений загружать нельзя');
			return false;
		}
		
		var files = $('#morephotos')[0].files;
		for(var i=0;i<files.length;i++){
			filesUpload.push(files[i]);
			checkImg(200, 300, files[i], false, imgIndex++);
		}
	});
	
	$('#file-response > input').click(function(){
		uploading.reset();
	});
});



function moreFiles(){
	var f = $('#morephotos')[0].files;
	for(var i=0;i<f.length;i++)
		filesUpload.push(f[i]);
}

function delFile(el, i){
	$(el).parent().remove();
	delete(filesUpload[i]);
}