// Mr.Fixit v1.2
// requires jQuery to work !!!	
function FixitLeft (sonny, daddy){ //both parameters must be ID
	// will fix position relatively to left corner
	var sHeightOuter = $("#"+sonny).outerHeight(false);
	var dHeightOuter = $("#"+daddy).outerHeight(false);
	
	var sHeightInner = $("#"+sonny).innerHeight();
	var dHeightInner = $("#"+daddy).innerHeight();
	
	var dBorderLeft = parseInt($("#"+daddy).css("border-left-width").replace("px",""));
	var dBorderRight = parseInt($("#"+daddy).css("border-right-width").replace("px",""));
	var dBorderTop = parseInt($("#"+daddy).css("border-top-width").replace("px",""));
	var dBorderBottom =  parseInt($("#"+daddy).css("border-bottom-width").replace("px",""));
	
	var dPositionTop = $("#"+daddy).offset().top;
	var sPositionTop = $("#"+sonny).offset().top;
	
	var dCssPosition = $("#"+daddy).css("position");
	var sCssPosition = $("#"+sonny).css("position"); 
			
	var dCssLeft = parseInt($("#"+daddy).css("left").replace("px",""));
	if($("#"+sonny).css("left").length){  var sCssLeft = parseInt($("#"+sonny).css("left").replace("px",""));  }else{ var sCssLeft = "auto"; };

	if((dCssPosition=="relative") || (dCssPosition=="absolute")){
		var sLeftPlus = $("#"+daddy).offset().left + dBorderLeft;
	}else{
		var sLeftPlus = 0;
		$("#"+daddy).css({ position: relative });
	};
	
	$(window).resize(function() {
		if((dCssPosition=="relative") || (dCssPosition=="absolute")){
			sLeftPlus = $("#"+daddy).offset().left + dBorderLeft;
		}else{
			$("#"+daddy).css({ position: relative });
		};
		
		if(($(window).scrollTop() > (sPositionTop-buffTop)) && ($(window).scrollTop() < maxScrollTop)){
			$("#"+sonny).css({ left: sCssLeft+sLeftPlus });
		};
	});
	
	var buffTop = sPositionTop - dPositionTop - dBorderTop;
	var buffBottom = buffTop;
	
	var maxScrollTop = dPositionTop + dBorderTop + dHeightInner - sHeightOuter - buffBottom;
	var absInnerPos = dHeightInner - sHeightOuter - buffBottom;

	$(window).scroll(function(e){
		// podminka oblasti posunu
		if(($(window).scrollTop() > (sPositionTop-buffTop)) && ($(window).scrollTop() < maxScrollTop)){
			$("#"+sonny).css({
				position: 'fixed',
				top: buffTop
			});
			$("#"+sonny).css({ left: sCssLeft+sLeftPlus });
		// spodni presah oblast posunu (paticka)
		}else if($(window).scrollTop() >= maxScrollTop){
			$("#"+sonny).css({
				position: 'absolute',
				top: absInnerPos
			});
			$("#"+sonny).css({ left: sCssLeft });
		// vrchni presah oblasti (hlavicka)
		}else {
			$("#"+sonny).css({
				position: sCssPosition,
				top: buffTop
			});
			$("#"+sonny).css({ left: sCssLeft });
		};
	});
}
function FixitRight (sonny, daddy){ //both parameters must be ID
	// will fix position relatively to right corner
	var sHeightOuter = $("#"+sonny).outerHeight(false);
	var dHeightOuter = $("#"+daddy).outerHeight(false);
	
	var sHeightInner = $("#"+sonny).innerHeight();
	var dHeightInner = $("#"+daddy).innerHeight();
	
	var dBorderLeft = parseInt($("#"+daddy).css("border-left-width").replace("px",""));
	var dBorderRight = parseInt($("#"+daddy).css("border-right-width").replace("px",""));
	var dBorderTop = parseInt($("#"+daddy).css("border-top-width").replace("px",""));
	var dBorderBottom =  parseInt($("#"+daddy).css("border-bottom-width").replace("px",""));
	
	var dPositionTop = $("#"+daddy).offset().top;
	var sPositionTop = $("#"+sonny).offset().top;
	
	var dCssPosition = $("#"+daddy).css("position");
	var sCssPosition = $("#"+sonny).css("position"); 
	
	var dCssRight = parseInt($("#"+daddy).css("right").replace("px",""));
	if($("#"+sonny).css("right").length){ var sCssRight = parseInt($("#"+sonny).css("right").replace("px",""));}else{ var sCssRight = "auto"; }; 

	if((dCssPosition=="relative") || (dCssPosition=="absolute")){
		var sRightPlus = $(window).width() - ($("#"+daddy).offset().left + $("#"+daddy).outerWidth()) + dBorderRight;
	}else{
		var sRightPlus = 0;
		$("#"+daddy).css({ position: relative });
	};
	
	$(window).resize(function() {
		if((dCssPosition=="relative") || (dCssPosition=="absolute")){
			sRightPlus = $(window).width() - ($("#"+daddy).offset().left + $("#"+daddy).outerWidth()) + dBorderRight;
		}else{
			$("#"+daddy).css({ position: relative });
		};
		
		if(($(window).scrollTop() > (sPositionTop-buffTop)) && ($(window).scrollTop() < maxScrollTop)){
			$("#"+sonny).css({ right: sCssRight+sRightPlus });
		};
	});
	
	var buffTop = sPositionTop - dPositionTop - dBorderTop;
	var buffBottom = buffTop;
	
	var maxScrollTop = dPositionTop + dBorderTop + dHeightInner - sHeightOuter - buffBottom;
	var absInnerPos = dHeightInner - sHeightOuter - buffBottom;

	$(window).scroll(function(e){
		// podminka oblasti posunu
		if(($(window).scrollTop() > (sPositionTop-buffTop)) && ($(window).scrollTop() < maxScrollTop)){
			$("#"+sonny).css({
				position: 'fixed',
				top: buffTop
			});
			$("#"+sonny).css({ right: sCssRight+sRightPlus });
		// spodni presah oblast posunu (paticka)
		}else if($(window).scrollTop() >= maxScrollTop){
			$("#"+sonny).css({
				position: 'absolute',
				top: absInnerPos
			});
			$("#"+sonny).css({ right: sCssRight });
		// vrchni presah oblasti (hlavicka)
		}else {
			$("#"+sonny).css({
				position: sCssPosition,
				top: buffTop
			});
			$("#"+sonny).css({ right: sCssRight });
		};
	});		
};