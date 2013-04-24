<?php

	session_start();

	include_once("../../modelo/clase_mysqli.inc.php");
	include_once("../../vista/login/Auth.class.php");

	Auth::required();

	$idasistencia=$_GET["asistencia"];
	$cvedeficiencia= $_GET["cuenta"];
	$idetapa = $_GET["plan"];
	$idprincipal = $_GET["idprincipal"];
	$expediente = $_GET["expediente"];
	$accion="nuevo";


	$con = new DB_mysqli();
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}

		$color="#82A8C8";
		if($_GET["asistencia"] =="valida" || $_GET["asistencia"] =="retira"){

			if($_GET["asistencia"] =="valida")	$color="#F40000"; else $color="#007500";

			$Sql_deficiencia="SELECT IDDEFICIENCIA_CORRELATIVO,CVEDEFICIENCIA,IDETAPA,IDEXPEDIENTE,IDASISTENCIA,IDCOORDINADOR,IDPROVEEDOR FROM $con->temporal.expediente_deficiencia WHERE IDDEFICIENCIA_CORRELATIVO = '".$_GET["expediente"]."'";
			$valorsdefic=$con->consultation($Sql_deficiencia);

			$idprincipal=$valorsdefic[0][0];
			$cvedeficiencia=$valorsdefic[0][1];
			$idetapa=$valorsdefic[0][2];
			$expediente=$valorsdefic[0][3];
			$idasistencia=$valorsdefic[0][4];
			$coordinador=$valorsdefic[0][5];
			$proveedor=$valorsdefic[0][6];
			$accion=$_GET["asistencia"];
		}

	//comprobar si cve es interno
		$Sql_interno="SELECT ORIGEN FROM $con->catalogo.catalogo_deficiencia WHERE CVEDEFICIENCIA = '$cvedeficiencia'";
		$cveinterno=$con->consultation($Sql_interno);

		$Sql_coordinador="SELECT
			catalogo_usuario.IDUSUARIO,
			CONCAT(
				catalogo_usuario.APELLIDOS,
				', ',
				catalogo_usuario.NOMBRES
			) AS NOMBRES
		FROM
			$con->catalogo.catalogo_usuario
		INNER JOIN $con->temporal.grupo_usuario ON grupo_usuario.IDUSUARIO = catalogo_usuario.IDUSUARIO
		WHERE grupo_usuario.IDGRUPO='CORD' ";

		$Sql_proveedores="(SELECT
						  catalogo_proveedor.IDPROVEEDOR,
						  catalogo_proveedor.NOMBRECOMERCIAL
						FROM $con->catalogo.catalogo_proveedor
						  INNER JOIN $con->temporal.asistencia_asig_proveedor
							ON asistencia_asig_proveedor.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
						WHERE asistencia_asig_proveedor.IDASISTENCIA = '$idasistencia'
						ORDER BY catalogo_proveedor.NOMBRECOMERCIAL
						 )
						UNION
						(
						SELECT DISTINCT
						  catalogo_proveedor.IDPROVEEDOR,
						  catalogo_proveedor.NOMBRECOMERCIAL
						FROM $con->catalogo.catalogo_proveedor
						  INNER JOIN $con->temporal.expediente_deficiencia
							ON expediente_deficiencia.IDPROVEEDOR = catalogo_proveedor.IDPROVEEDOR
						WHERE expediente_deficiencia.IDASISTENCIA = '$idasistencia'
						ORDER BY catalogo_proveedor.NOMBRECOMERCIAL
						)";
?>
<html>
<head>
	<title><?=_("AgregarDeficiencia");?></title>
	<script src="../asistencia/principal/mmenu.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../estilos/functionjs/func_global.js"></script>
	<link href="../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" ></link>
	<link href="../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" ></link>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/effects.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/window_effects.js"></script>
	<script type="text/javascript" src="../../../librerias/windows_js_1.3/javascripts/debug.js"></script>
</head>
<body>

<form name='frmdef' id='frmdef' method='POST'>
    <table border ="0" cellpadding="1" cellspacing="1"  bgcolor="#ADC6DE" style="width:100%;border:2px solid <?=$color?>;font-size:10px">
<?
    if($cveinterno[0][0] =="EXTERNO"){
?>
		<tr>
            <td>
				<div id="titulo2" style="display:none" ><?=_('PROVEEDOR')?>&nbsp;&nbsp;
					<input type="hidden" name="cmb_proveedor" id="cmb_proveedor"/><input type="text" name="txtproveed" id="txtproveed" readonly size="45" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto'/>
				</div>
				<div id="titulo"><?=_('PROVEEDOR')?>&nbsp;&nbsp;
				<? $con->cmbselectdata($Sql_proveedores,"cmbproveedor",$proveedor,"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ",($proveedor)?"1":"2");?>  <? if($proveedor ==""){?><input type='button' name='btnverprov' id='btnverprov' title="Buscar Proveedor" value="..." onclick="document.getElementById('titulo2').style.display='block';document.getElementById('titulo').style.display='none';document.getElementById('buscarProveedor').style.display='block' "/><?}?>
				</div>
			</td>
        </tr>
<? 	} else{?>
        <tr>
            <td><strong><?=_("COORDINADOR")?></strong>&nbsp;
				<? $con->cmbselectdata($Sql_coordinador,"cmbcoordinador",$coordinador,"onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto'",($coordinador)?"1":"2");?>
			</td>
        </tr>
<? 	} ?>
        <tr>
            <td align='center'><textarea name='txtaobservacion' id='observacion' cols="60" rows='3' style="text-transform:uppercase;" onfocus='coloronFocus(this);' class='classtexto' onBlur='colorOffFocus(this)'></textarea></td>
        </tr>
        <tr>
            <td align = center><input type='button' name='btndef' id='btndef' value="<?=_('GRABAR DEFICIENCIA')?>" class='guardar' onclick="GrabarDeficiencia()" <?//=($proveedores_exit[0][0] =="" and $origen =="EXTERNO")?"disabled":""?> ></td>
        </tr>
    </table>

	<div id="buscarProveedor" style="display:none">
		 <table width="100%" border="0" cellpadding="1" cellspacing="1" style="width:100%">
			<tr>
				<td>
					<input name="txtproveedor" type="text" id="txtproveedor" size="49" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' />
				</td>
				<td>
					<input type="button" name="button" class='normal' id="button" value="Buscar Proveedor" onclick="mostrarDiv('div-resultado','consultaProveedores.php',$F('txtproveedor'))"/>
				</td>
			</tr>
		</table>
		<div id="div-resultado"></div>
	</div>

    <input type='hidden' name='idprincipal' VALUE='<?=$idprincipal?>'>
    <input type='hidden' name='hid_idetapa' VALUE='<?=$idetapa?>'>
    <input type='hidden' name='hid_cvedefeficiencia' VALUE='<?=$cvedeficiencia?>'>
    <input type='hidden' name='hid_idasistencia' VALUE='<?=$idasistencia?>'>
    <input type='hidden' name='hid_idexpediente' VALUE='<?=$expediente?>'>
    <input type='hidden' name='hid_accion' VALUE='<?=$accion?>'>

</form>
</body>
</html>

<script type="text/javascript">

	function GrabarDeficiencia(){

		if(document.getElementById('observacion').value==''){

			alert("<?=_("DEBE INGRESAR UN COMENTARIO!!!")?>");
			document.getElementById('observacion').focus();

		} else if(document.getElementById('titulo2') && document.getElementById('titulo2').style.display =='block' && document.getElementById('txtproveed').value=='' ){

			alert("<?=_("SELECCION AL PROVEEDOR")?>");

		} else if(document.getElementById('titulo') && document.getElementById('titulo').style.display !='none' && document.getElementById('cmbproveedor').value=='' ){

			alert("<?=_("SELECCION AL PROVEEDOR")?>");

		} else if(confirm("<?=_("ESTA SEGURO QUE DESEA GRABAR LA DEFICIENCIA?")?>")){
			document.getElementById('frmdef').action='grabardeficiencia.php'

			document.getElementById('frmdef').submit();
		}
	}

	function asignar_proveedor(idvalor,desc){

		document.getElementById('cmb_proveedor').value=idvalor;
		document.getElementById('txtproveed').value=desc;

	}

</script>


