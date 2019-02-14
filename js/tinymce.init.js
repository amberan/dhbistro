tinymce.init({
	selector: "textarea",
	entity_encoding: "raw",
	plugins: [
		"advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
		"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
		"save table contextmenu directionality template paste textcolor"
	],
	toolbar: "undo redo | styleselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor table removeformat",
	menubar: false,
	toolbar_items_size: "small"
});