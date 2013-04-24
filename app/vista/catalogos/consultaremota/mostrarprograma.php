<?php

	session_start();
	include_once("../../../modelo/clase_mysqli.inc.php");
		
	$con = new DB_mysqli();		
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	
	if($_POST["idcodigo"]!="")	$sql="select IDPROGRAMA,NOMBRE from $con->catalogo.catalogo_programa where ACTIVO=1 AND IDCUENTA='".$_POST["idcodigo"]."' ";
	if($_POST["opc2"]=="TODOS") $nombreplan=$_POST["opc2"]." >>>"; else $nombreplan=_("SELECCIONE");
	 
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width='17%' colspan="3"  >
		<?
			
			if($_POST["idcodigo"]!=""){			
				$con->cmbselectdata($sql,"cmbprogramatitular",$_POST["opc1"],"onchange='verifica_cuenta()'; onFocus=coloronFocus(this); onBlur=colorOffFocus(this); class='classtexto'","",$nombreplan); 
			} else{
				echo "<select name='cmbprogramatitular'  id='cmbprogramatitular' class='classtexto' onfocus='coloronFocus(this);' onblur='colorOffFocus(this);'>
						<option value=''>$nombreplan</option>
					  </select>";
			}
		?>
		</td>
	</tr>
</table>