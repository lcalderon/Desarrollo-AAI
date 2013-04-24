<?
	session_start(); 
	
 	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/functions.php');
	include_once('../../includes/arreglos.php');
	
	$con= new DB_mysqli();
	
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	$Sql_pcs="SELECT
			  MARCA,
			  MODELO,
			  NUMEROSERIE,
			  FECHADECOMPRA,
			  SISTEMAOPERATIVO
			FROM $con->temporal.asistencia_pc
			WHERE ID= '".$_POST["idpc"]."' ";
			 
	$result=$con->query($Sql_pcs);	
	$rowpc = $result->fetch_object();
?>
	<table>
		<tr>
			<td><?=_('MARCA *')?></td>
			<td><input type='text' name='MARCA' id='marca' value="<?=($_POST["idpc"])?$rowpc->MARCA:$asis->asistencia_familia->MARCA?>" size="50"><? if(!$asis->idasistencia) {?><img src="../../../imagenes/iconos/copy_data.gif" onClick="ventana_pcs()" style="cursor:pointer" border="0" width="18" height="18" title="<?=_('BUSCAR PCS')?>" alt="Vehiculo"><? } ?></td>
		</tr>
		<tr>
			<td><?=_('MODELO *')?></td>
			<td><input type='text' name='MODELO' id='modelo' value="<?=($_POST["idpc"])?$rowpc->MODELO:$asis->asistencia_familia->MODELO?>" size="50"></td>
		</tr>
		<tr>
			<td><?=_('NUMERO DE SERIE')?></td>
			<td><input type='text' name='NUMEROSERIE' id='numeroserie' value="<?=($_POST["idpc"])?$rowpc->NUMEROSERIE:$asis->asistencia_familia->NUMEROSERIE?>" size="50"></td>
		</tr>
		<tr>
			<td><?=_('FECHA DE COMPRA')?></td>
			<td>
			<input type='text' name='DIA' id="dia"  value="<?=($_POST["idpc"])?substr($rowpc->FECHADECOMPRA,8,2):substr($asis->asistencia_familia->FECHADECOMPRA,8,2);?>" size='2'  onKeyPress="return validarnumero(event)" >
			-	<?
			$con->cmbselect_ar('MES',$mes_del_anio,($_POST["idpc"])?substr($rowpc->FECHADECOMPRA,5,2):substr($asis->asistencia_familia->FECHADECOMPRA,5,2),"id='mes'",'','Mes');
				?>
			- 	<?
			$anio_inicial = getdate();
			$con->cmbselect_anio('ANIO',$anio_inicial[year],10,($_POST["idpc"])?substr($rowpc->FECHADECOMPRA,0,4):substr($asis->asistencia_familia->FECHADECOMPRA,0,4),"id='anio'",'');
				?>
		</tr>
		<tr>
			<td><?=_('SISTEMA OPERATIVO ')?></td>
			<td><input type='text' name='SISTEMAOPERATIVO' id='sistemaoperativo' value="<?=($_POST["idpc"])?$rowpc->SISTEMAOPERATIVO:$asis->asistencia_familia->SISTEMAOPERATIVO?>" size="50"></td>
		</tr>
		<!-- <tr>
			<td><input type='button' value="<?=_('BITACORA')?>" id='bitacora' onclick="ver_bitacora()" class="normal"  <?=(isset($asis)?'':'disabled')?>></td>
		</tr>-->
	</table>
