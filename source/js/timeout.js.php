
$(document).ready(function() {
	setTimeout(
		function() {
			$("#submitbutton").addClass("timeout-triggered");
		}	
	, 3000);
});

$("#submitbutton").on("click", function(event) {
	if($(this).hasClass('timeout-triggered')) {			
  	event.preventDefault();
		$('#js-popup').addClass('popup-opened');
	}
});

$(".close-button").on("click", function() {
	$('#js-popup').removeClass('popup-opened');
});