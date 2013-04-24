<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();

if (isset($_POST['ID'])) {
	
	$sql ="DELETE FROM $con->catalogo.catalogo_guiacalle WHERE ID='$_POST[ID]'";
	$con->query($sql); 
}

?>