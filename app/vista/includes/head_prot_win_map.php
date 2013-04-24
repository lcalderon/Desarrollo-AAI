<?
$con = new DB_mysqli( );
if ($con->Errno) {
	printf ( "Fallo de conexion: %s\n", $con->Error );
	exit ();
}

// Coordenadas por default del mapa de la oficina
$lat = $con->lee_parametro('UBICACION_PRIMARIA_LATITUD');
$lng = $con->lee_parametro('UBICACION_PRIMARIA_LONGITUD');
$clave = $con->lee_parametro($con->url());
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" >
 <head>
	<title>American Assist</title>
	<script type="text/javascript"	src="http://maps.google.com/maps?file=api&v=2&key=<?=$clave?>"></script>	
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="/librerias/scriptaculous/scriptaculous.js"></script>
	
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/effects.js"></script>
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/window.js"></script>
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/window_effects.js"></script>
	<script type="text/javascript" src="/librerias/windows_js_1.3/javascripts/debug.js"></script>

	<link href="/librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link>
	<link href="/librerias/windows_js_1.3/themes/spread.css" rel="stylesheet" type="text/css" ></link>
	<link href="/librerias/windows_js_1.3/themes/alert.css" rel="stylesheet" type="text/css" ></link>
	<link href="/librerias/windows_js_1.3/themes/alert_lite.css" rel="stylesheet" type="text/css" ></link>
	<link href="/librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" ></link>
	<link href="/librerias/windows_js_1.3/themes/debug.css" rel="stylesheet" type="text/css" ></link>
	
	
	<script type="text/javascript" src="/estilos/functionjs/validator.js"></script>
	<link href="/estilos/plantillas/form.css" rel="stylesheet" type="text/css" />
	<link href="/estilos/suggest/ubigeo.css" rel="stylesheet" type="text/css" />
	
	<link rel="stylesheet" href="/librerias/tinytablev3.0/style.css" />

	<link rel="stylesheet" href="/librerias/calendarview-1.2/stylesheets/calendarview.css">
	<script src="/librerias/calendarview-1.2/javascripts/calendarview.js"></script>

	

</head>