<?php
session_start();

include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/functions.php');
include_once("../../../vista/login/Auth.class.php");
include_once("../../includes/arreglos.php");

$con= new DB_mysqli();
if($con->Errno){
	printf("Fallo de conexion: %s\n", $con->Error);
	exit();
}

$var_cod_afiliado= $_REQUEST["va_cod_afiliado"];

$sql_titular= "SELECT
			   catalogo_afiliado.IDCUENTA,
			   catalogo_afiliado.IDPROGRAMA,
			   catalogo_afiliado.IDAFILIADO,
			   catalogo_afiliado.CVEAFILIADO,
			   catalogo_afiliado.STATUSASISTENCIA,
			   catalogo_afiliado_persona.IDTIPODOCUMENTO,
			   catalogo_afiliado_persona.IDDOCUMENTO,
			   catalogo_afiliado_persona.NOMBRE,
			   catalogo_afiliado_persona.APMATERNO,
			   catalogo_afiliado_persona.APPATERNO,
			   catalogo_afiliado_persona.DIGITOVERIFICADOR,
			   expediente.IDEXPEDIENTE,
			   expediente.ANI
			   FROM $con->catalogo.catalogo_afiliado
				INNER JOIN $con->catalogo.catalogo_afiliado_persona
				  ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado.IDAFILIADO
				LEFT JOIN $con->temporal.expediente
				  ON expediente.IDAFILIADO = catalogo_afiliado.IDAFILIADO
			   WHERE catalogo_afiliado.CVEAFILIADO ='".$var_cod_afiliado."' 
			   GROUP BY catalogo_afiliado.IDAFILIADO";

$result= $con->query($sql_titular);
$row= $result->fetch_object();

$datocuenta=$row->IDCUENTA;
$datoplan=$row->IDPROGRAMA;
$idafiliado=$row->IDAFILIADO;

list($allcuentas,$ver_cuentas,$ids,$validoCta)=accesos_cuentas($_SESSION["user"],$datocuenta);

if($validoCta ==1)	$Sql_programa="SELECT IDPROGRAMA,NOMBRE FROM $con->catalogo.catalogo_programa where IDCUENTA='".$datocuenta."' ORDER BY NOMBRE"; else $Sql_programa="SELECT IDPROGRAMA,NOMBRE FROM $con->catalogo.catalogo_programa where IDCUENTA='' ORDER BY NOMBRE";

//OBTENER EL TELEFONO 1
$sql_telefono_1= "SELECT
				 	catalogo_afiliado_persona_telefono.CODIGOAREA,
					catalogo_afiliado_persona_telefono.IDTIPOTELEFONO,
					catalogo_afiliado_persona_telefono.NUMEROTELEFONO,
					catalogo_afiliado_persona_telefono.EXTENSION,
					catalogo_afiliado_persona_telefono.IDTSP
				 FROM $con->catalogo.catalogo_afiliado_persona_telefono
					INNER JOIN $con->catalogo.catalogo_afiliado
					  ON catalogo_afiliado.IDAFILIADO = catalogo_afiliado_persona_telefono.IDAFILIADO						
				 WHERE catalogo_afiliado.IDAFILIADO = '".$idafiliado."'
				 ORDER BY catalogo_afiliado_persona_telefono.PRIORIDAD
				 LIMIT 1";
$rs_telefono_1= $con->query($sql_telefono_1);
$row_tel_1= $rs_telefono_1->fetch_object();
$var_afi_telefono_1= $row_tel_1->NUMEROTELEFONO;
?>

<table class="style_table" border="0" width="100%" bordercolor="#0c298f" cellspacing="0" cellpadding="2" align="center">
<tr>
        <td colspan="2">
        <fieldset style="width:97%">
        <legend style="color:#333">Datos afiliado:</legend>
          <table class="style_table" border="0" width="100%" bordercolor="#0c298f" cellspacing="0" cellpadding="2" align="center">
          <tr>
            <td width="12%"><div align="right"><strong><?=_("Cuenta:");?></strong></div></td>
            <td width="21%">
             <?
			  if(!$row->IDCUENTA)	$status=" WHERE ACTIVO =1";
			  if($allcuentas==1)	$Sql_cuenta="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta $status ORDER BY NOMBRE"; else $Sql_cuenta=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";
			  
			  $con->cmbselectdata($Sql_cuenta,"cmbcuentatitular",$row->IDCUENTA,"style='width:150'; class='txt'","1");
			  ?>
            
            </td>
            <td width="13%">&nbsp;</td>
            <td width="22%">&nbsp;</td>
            <td width="12%"><div align="right"><strong><?=_("Plan:");?></strong></div></td>
            <td width="20%">
            <? $con->cmbselectdata($Sql_programa,"cmbprogramatitular",$row->IDPROGRAMA,"style='width:150'; class='txt'","1"); 
			?>
            </td>
          </tr>
          <tr>
            <td><div align="right"><strong><?=_("AP. Paterno:");?></strong></div></td>
            <td><input class="txt" type="text" name="txt_afi_ap_paterno"  maxlength="200" value="<?=$row->APPATERNO; ?>" style="text-transform:uppercase; width:150"></td>
            <td><div align="right"><strong><?=_("AP. Materno:");?></strong></div></td>
            <td><input class="txt" type="text" name="txt_afi_ap_materno"  maxlength="200" value="<?=$row->APMATERNO; ?>" style="text-transform:uppercase; width:150"></td>
            <td><div align="right"><strong><?=_("Nombres:");?></strong></div>

            </td>
            <td><input class="txt" type="text" name="txt_afi_nombres"  maxlength="200" value="<?=$row->NOMBRE; ?>" style="text-transform:uppercase; width:150"></td>
          </tr>
          <tr>
            <td><div align="right"><strong><?=_("Telefono 1:");?></strong></div></td>
            <td><input class="txt" type="text" name="txt_afi_telefono_1"  maxlength="200" style="width:100" value="<?=$var_afi_telefono_1;?>"></td>
            <td><div align="right"><strong><?=_("Telefono 2:");?></strong></div></td>
            <td><input class="txt" type="text" name="txt_afi_telefono_2"  maxlength="200" style="width:100"></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          </table>
        </fieldset>
        </td>
</tr>
      <tr>
        <td colspan="2">
         <fieldset style="width:97%">
         <legend style="color:#333">Datos contacto:</legend>
          <table class="style_table" border="0" width="100%" bordercolor="#0c298f" cellspacing="0" cellpadding="2" align="center">
          <tr>
            <td width="12%"><div align="right"><strong><?=_("AP. Paterno:");?></strong></div></td>
            <td width="21%"><input class="txt" type="text" name="txt_con_ap_paterno"  maxlength="200" style="text-transform:uppercase;width:150"></td>
            <td width="13%"><div align="right"><strong><?=_("AP. Materno:");?></strong></div></td>
            <td width="22%"><input class="txt" type="text" name="txt_con_ap_materno"  maxlength="200" style="text-transform:uppercase;width:150"></td>
            <td width="12%"><div align="right"><strong><?=_("Nombres:");?></strong></div>

            </td>
            <td width="20%"><input class="txt" type="text" name="txt_con_nombres"  maxlength="200" style="text-transform:uppercase;width:150"></td>
          </tr>
          <tr>
            <td><div align="right"><strong><?=_("Telefono 1:");?></strong></div></td>
            <td><input class="txt" type="text" name="txt_con_telefono_1"  maxlength="200" style="width:100"></td>
            <td><div align="right"><strong><?=_("Telefono 2:");?></strong></div></td>
            <td><input class="txt" type="text" name="txt_con_telefono_2"  maxlength="200" style="width:100"></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
        </fieldset>
        </td>
</tr>
</table>