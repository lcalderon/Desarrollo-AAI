<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();


if ($_POST['ID']==''){
	unset($_POST['ID']);
	
	$con->insert_reg("$con->catalogo.catalogo_guiacalle",$_POST);
	}
else {
	$con->update("$con->catalogo.catalogo_guiacalle",$_POST," where ID= '$_POST[ID]'");
	}


?>