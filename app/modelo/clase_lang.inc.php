<?
include_once('clase_mysqli.inc.php');

function gettext_simply($lang='', $mo_file='messages', $path_LC='')
{
	setlocale(LC_ALL,$lang);
	bindtextdomain($mo_file, $path_LC.'/locale');
	return;
}

function getlocale($category) {
	return setlocale($category, NULL);
}

//echo  getlocale(LC_ALL);
$con = new DB_mysqli();
gettext_simply($con->lee_parametro("IDLOCALE"),'messages',$con->lee_parametro('RUTA_DIRECTORIO'));


/* si no funciona bajar y levantar el server */
//echo  getlocale(LC_ALL);  determina el locale por default

?>