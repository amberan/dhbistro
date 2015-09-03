<html>
     <head>
          <script type="text/javascript" src="jquery.min.js"></script>
          <script type="text/javascript" src="raphael-min.js"></script>
          <script type="text/javascript" src="json2.js"></script>
          <script type="text/javascript" src="raphael.sketchpad.js"></script>
          <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
          <script type="text/javascript" src="raphael-2.0.1.js"></script>

   <script type="text/javascript">

    $(document).ready(function() {
    var sketchpad = Raphael.sketchpad("editor", {
            height: 260,
            width: 260,
            editing: true

        });

        // When the sketchpad changes, update the input field.
        sketchpad.change(function() {
            $("#data").val(sketchpad.json());
        });

});

</script>
     </head>
    <body>
<input type="hidden" id="data" />
<div id="editor" style="border: 1px solid #aaa; "></div>

<h1> Testing</h1>



    </body>
</html>