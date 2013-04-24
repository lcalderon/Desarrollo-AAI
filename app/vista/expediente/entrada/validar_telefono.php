<?php
	
	$url="http://208.70.190.4:9091/DaVinci/servlet/clubterra/queryaa?movil=".$_POST["telefono"]."&operador=MOVISTAR&producto=HOGAR";
 
	if(function_exists('curl_init')){ // Comprobamos si hay soporte para cURL

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$resultado = curl_exec ($ch);
	 
		print_r($resultado);
	} else{
		echo "No hay soporte para cURL";
	}
?>