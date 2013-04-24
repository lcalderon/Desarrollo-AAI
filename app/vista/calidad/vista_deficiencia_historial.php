<?
   	session_start();

	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");

	Auth::required();

	$idasistencia=$_GET["idasistencia"];
	
	$con = new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

	$resp = $con->query("SELECT FECHAMOD,IDUSUARIOMOD,COMENTARIO,ORIGEN FROM $con->temporal.expediente_deficiencia_bitacora WHERE IDDEFICIENCIA_CORRELATIVO='".$_GET["expediente"]."' ORDER BY FECHAMOD");

?>
	<table cellpadding="0" cellspacing="0" border="1" id="table" width="100%" style="font-family: Arial,Helvetica,sans-serif;border-collapse:collapse;border:1px solid #336699;font-size:11px">
		<thead>
			<tr bgcolor="#4B708D">
				<th><?=_("FECHAHORA")?></th>
				<th><?=_("USUARIO")?></th>
				<th><?=_("ACCION")?></th>
				<th><?=_("COMENTARIO")?></th>
			</tr>
		</thead>
		<tbody>
		<?
			$linea=0;
			while($row = $resp->fetch_object()){
				$colorbg = ($linea%2)?"#ECF2F6":"#FFFFFF";
		?>
			<tr bgcolor="<?=$colorbg?>">
				<td align="center" width="130px"><?=$row->FECHAMOD?></td>
				<td align="center"><?=$row->IDUSUARIOMOD?></td>
				<td align = "center" width="70"><?=$row->ORIGEN?></td>
				<td><?=$row->COMENTARIO?></td>			
			</tr>
		<?
				$linea++;
			}
		?>
		</tbody>
	</table>
  