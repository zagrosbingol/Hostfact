var useStylesheet = function(){

    var currentWidth = document.getElementsByTagName('body')[0].clientWidth;

	document.getElementById('stylesheet-a').setAttribute('media');
	document.getElementById('stylesheet-b').setAttribute('media');
	document.getElementById('stylesheet-c').setAttribute('media');
	
	if(currentWidth <= 1530){
		document.getElementById('stylesheet-a').removeAttribute('media');
	}else if(currentWidth > 1530){
		document.getElementById('stylesheet-b').removeAttribute('media');
	}
		
    if(currentWidth > 1762){
		document.getElementById('stylesheet-c').removeAttribute('media');
	}          

}

window.attachEvent('onload', useStylesheet);
window.attachEvent('onresize', useStylesheet);