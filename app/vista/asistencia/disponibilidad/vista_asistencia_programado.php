<?
$con = new DB_mysqli();
$con->select_db($con->temporal);
$sql_disponibilidad ="select * from asistencia_disponibilidad_afiliado WHERE idasistencia = $idasistencia";
$exec_disponibilidad = $con->query($sql_disponibilidad);
if($rset_disponibilidad=$exec_disponibilidad->fetch_object()){
    $fecdispo = $rset_disponibilidad->FECHAHORA;
}
?>
<fieldset style="background: #ECE9D8">
<legend><?=_('ASISTENCIA PROGRAMADA -- > ').$idasistencia?></legend>
<table align="left">
		<tbody>
			<tr>
				<td><?=_('Disponibilidad de Afiliado')?><br>
				<input type="text" value="<?=$fecdispo?>" size="30" disabled></td>
				
			</tr>
		
			
		</tbody>
	</table>
</fieldset>