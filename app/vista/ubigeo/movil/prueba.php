<script type="text/javascript" src="/librerias/jquery/jquery-1.5.js"></script>
<script type="text/javascript">
var auto_refresh = setInterval(
function ()
{
	$('#load_tweets').load('prueba2.php',
	{ nombrePost: 'frank'},
	function(xml){
		$(xml).find('data').each(function(){
			$(this).find('proveedor').each(function()
			{
				var cadena = $(this).find('id').text() + ' ';
				cadena += $(this).find('nc').text() + ' ';
				cadena += $(this).find('telf').text() + ' ';
				cadena += $(this).find('lat').text() + ' ';
				cadena += $(this).find('lng').text() + '';
				alert(cadena);
			});
		})
	}
	).fadeIn("slow");
}, 1000); // refresh every 10000 milliseconds

</script>
<body>
<div id="load_tweets"> </div>
</body>
