//Скрипт для запоминания и воспроизведения вертикального сскролинга страници.
$(function(){
	function getCookie(name){
	  	var matches = document.cookie.match(new RegExp(
	    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	  	));
	  	return matches ? decodeURIComponent(matches[1]) : undefined;
	}
	
	$(".module").click(function(){
  		var scrt = $(document).scrollTop();
  		document.cookie = 'scr_doc' + '=' + scrt;
	});
	
	$(document).ready(function(){
		scrt = getCookie('scr_doc');
		if(scrt){
			$(document).scrollTop(scrt);
  		}
	});
});


















/*
$(function(){
  $(".MEDICAL_REGISTRY_Item").click(function(){
  	  var scrt = $(document).scrollTop();
	  cookie = ("scroll", scrt);
  });
});


$(function(){
	 var scroll = cookie('scroll')
	if(scroll) {
	  	alert('Скролинг ' + scroll);
		var scrt = $.cookie("scroll");
});
*/

		
		
		
