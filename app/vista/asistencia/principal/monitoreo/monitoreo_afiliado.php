<?php
session_start();
//require_once('/var/www/soaang_preproduccion/app/vista/asistencia/asignacion/xajax/xajax.inc.php');
include_once('../../../../librerias/xajax/xajax.inc.php');
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_moneda.inc.php');
include_once('../../../modelo/clase_ubigeo.inc.php');
include_once('../../../modelo/clase_plantilla.inc.php');
include_once('../../../modelo/clase_persona.inc.php');
include_once('../../../modelo/clase_telefono.inc.php');
include_once('../../../modelo/clase_cuenta.inc.php');
include_once('../../../modelo/clase_familia.inc.php');
include_once('../../../modelo/clase_servicio.inc.php');
include_once('../../../modelo/clase_programa_servicio.inc.php');
include_once('../../../modelo/clase_programa.inc.php');
include_once('../../../modelo/clase_afiliado.inc.php');
include_once('../../../modelo/clase_etapa.inc.php');
include_once('../../../modelo/clase_expediente.inc.php');
include_once('../../../modelo/clase_asistencia.inc.php');
include_once('../../../modelo/clase_usuario.inc.php');
include_once('../../includes/arreglos.php');
$idusuario = $_SESSION[user];
$idasistencia=$_GET[idasistencia];
$idexpediente = $_GET[idexpediente];
//$idasistencia=21;
$varetapa='MONAFIL';
$con = new DB_mysqli();
//$catalogo=$con->catalogo;

$con->select_db($con->catalogo);
$temporal=$con->temporal;
$usuario = new usuario();
//$user=$_GET['user'];
$extension = $usuario->extension_usada($_SESSION[user]);
$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');

$exp= new expediente();
$exp->carga_datos($idexpediente);

$asis = new asistencia();
$asis->carga_datos($idasistencia);

if ($asis->arrstatusasistencia=='PRO') $disabledgral='';
else $disabledgral='disabled';

/* VARIABLES */
$afiliado = $exp->titular_persona->nombre.' '.$exp->titular_persona->appaterno.' '.$exp->titular_persona->apmaterno;
$contactante = $exp->contacto->nombre.' '.$exp->contacto->appaterno.' '.$exp->contacto->apmaterno;
$idcontacto = $exp->contacto->idpersona;
$idtitular = $exp->titular_persona->idpersona;
$telefono = $exp->contacto->telefonos;
$cuenta = $exp->cuenta->nombre;
$plan = $exp->programa->nombre;
$etapa = new etapa();
//$etapa->carga_datos(1); // inicia en la etapa 1

$ubigeo = $exp->ubigeo;
$idprograma = $exp->programa->idprograma;
//echo $idcontacto;
//echo $idtitular;
//if($idcontacto==''){ $idpersona =$idtitular; }else { $idpersona=$idcontacto; };
$sql_persona = "SELECT IDPERSONA,APPATERNO,APMATERNO,NOMBRE FROM $temporal.expediente_persona WHERE IDEXPEDIENTE = $idexpediente AND ARRTIPOPERSONA='CONTACTO'";
//echo $sql_persona;
$exec_persona = $con->query($sql_persona);
$nreg_persona = $exec_persona->num_rows;
if($nreg_persona==0){
	$sql_persona_tit = "SELECT IDPERSONA,APPATERNO,APMATERNO,NOMBRE FROM $temporal.expediente_persona WHERE IDEXPEDIENTE = $idexpediente AND ARRTIPOPERSONA='TITULAR'";
	$exec_persona_tit = $con->query($sql_persona_tit);
		while($rset_persona_tit= $exec_persona_tit->fetch_object())
	{
		$idpersona = $rset_persona_tit->IDPERSONA;
		$nombre =$rset_persona_tit->NOMBRE;
		$paterno =$rset_persona_tit->APPATERNO;
		$materno =$rset_persona_tit->APMATERNO;
	}
	}
{
	while($rset_persona= $exec_persona->fetch_object())
	{
		$idpersona = $rset_persona->IDPERSONA;
		$nombre =$rset_persona->NOMBRE;
		$paterno =$rset_persona->APPATERNO;
		$materno =$rset_persona->APMATERNO;
	}
}


/*
foreach($telefono as $numerotelefono=>$datos){
   echo $datos[NUMEROTELEFONO]; 
}*/
function fgrabar($idasistencia){
$con = new DB_mysqli();
$con->select_db($con->temporal);

$asis = new asistencia();
$asis->carga_datos($idasistencia);



if($asis->idetapa<5){
	  $rows[IDETAPA]=5;
	//actualiza los datos
 	$resultado=$con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);
 $asisuser[IDASISTENCIA]=$idasistencia;
	    $asisuser[IDUSUARIO]=$_SESSION['user'];
	    $asisuser[IDETAPA]=4;
	   $con->insert_reg('asistencia_usuario',$asisuser);
}

$respuesta = new xajaxResponse();
$respuesta->addScript("parent.top.document.location.href = '../../plantillas/etapa5.php?idasistencia=$idasistencia';");
 return $respuesta;
}

function cancelar(){  //elimina todo el contenido de la tabla y vuelve a cero los contadores
    
    $respuesta = new xajaxResponse();
 
    $respuesta->addRemove("tbDetalle"); //vuelve a crear la tabla vacia
    //$respuesta->addCreate("tblDetalle", "tbody", "tbDetalle");
    $respuesta->addAssign("num_campos", "value", "0");
    $respuesta->addAssign("cant_campos", "value", "0");
    return $respuesta;
}



function agregarFila($formulario,$idasistencia,$comentario,$origen){
$con = new DB_mysqli();
$usuario = new usuario();
$extension = $usuario->extension_usada($_SESSION[user]);

$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');

$con->select_db($con->temporal);
$db=$con->catalogo;

if($origen=='ADD'){
$bitacora[IDMONITOREOASISTENCIA]="";
$bitacora[IDUSUARIOMOD]=$_SESSION['user'];
$bitacora[IDETAPA]="4";
$bitacora[IDASISTENCIA]=$idasistencia;
$bitacora[COMENTARIO]=$comentario;

$con->insert_reg('asistencia_monitoreo_afiliado',$bitacora);
}
 $sql_verifica_etapa="SELECT IDETAPA FROM asistencia WHERE IDASISTENCIA = $idasistencia";
  $exec_verifica_etapa = $con->query($sql_verifica_etapa);
if($rset_verifica_etapa = $exec_verifica_etapa->fetch_object()){
    $xetapa=$rset_verifica_etapa->IDETAPA;
}

	$respuesta = new xajaxResponse();  
      
	extract($formulario);	
	

	 $num_campos=0;
	$id_campo=0;

	$respuesta->addRemove("tbDetalle");
	$respuesta->addCreate("tblDetalle", "tbody", "tbDetalle");
      


       //$id_campos1 = $cant_campos1 = $num_campos1+1;
	if($num_campos == 0){ // creamos un encabezado de lo contrario solo agragamos la fila
	$respuesta->addCreate("tbDetalle", "tr", "rowDetalle_0");
        $respuesta->addCreate("rowDetalle_0", "th", "tdDetalle_01");    //creamos los campos
	$respuesta->addCreate("rowDetalle_0", "th", "tdDetalle_02");
	$respuesta->addCreate("rowDetalle_0", "th", "tdDetalle_03");

	  $respuesta->addAssign("tdDetalle_01", "innerHTML", "FECHAHORA");   //asignamos el contenido
	$respuesta->addAssign("tdDetalle_02", "innerHTML", "USUARIO");   //asignamos el contenido
	$respuesta->addAssign("tdDetalle_03", "innerHTML", "COMENTARIO");   //asignamos el contenido
       
	}

    


    $sql_prov="SELECT COMENTARIO, FECHAMOD,IDUSUARIOMOD 
		    FROM asistencia_monitoreo_afiliado
		WHERE IDASISTENCIA = $idasistencia
		ORDER BY FECHAMOD DESC ";
//echo $sql_prov;
    $exec_prov = $con->query($sql_prov);
$nreg_prov= $exec_prov->num_rows;
 
if($nreg_prov==0){
}else{
    while($rset_prov=$exec_prov->fetch_object())
    {
    

   $idRow = "rowDetalle_$id_campos";
    $idTd = "tdDetalle_$id_campos";
     
      $respuesta->addCreate("tbDetalle", "tr", $idRow);
    if($id_campos%2==0){ $estilo = '#BBE0FF'; }else{ $estilo = ''; }
      $respuesta->addAssign($idRow, "style.background",$estilo);
    //$respuestab->addAssign("tbDetalleb", "class",'impar');
    $respuesta->addCreate($idRow, "td", $idTd."1");     //creamos los campos
    $respuesta->addCreate($idRow, "td", $idTd."2");
    $respuesta->addCreate($idRow, "td", $idTd."3");

		//echo $rset_prov->IDPROVEEDOR;

     
      $respuesta->addAssign($idTd."1", "align","center");
      $respuesta->addAssign($idTd."1", "innerHTML", $rset_prov->FECHAMOD);   //asignamos el contenido
      $respuesta->addAssign($idTd."2", "align","left");
      $respuesta->addAssign($idTd."2", "innerHTML", $rset_prov->IDUSUARIOMOD);
      $respuesta->addAssign($idTd."3", "align","LEFT");
      $respuesta->addAssign($idTd."3", "width", "80%");
      $respuesta->addAssign($idTd."3", "innerHTML","<pre>".$rset_prov->COMENTARIO."</pre>");
	 //echo $id_campos2;
	$id_campos++;
    }
}	
     
	$respuesta->addAssign("num_campos","value", $id_campos);
	$respuesta->addAssign("cant_campos" ,"value", $id_campos);
	$respuesta->addAssign("comentario" ,"value", ""); 
	//$respuesta2->addAssign("num_campos2","value", '');
	//$respuesta2->addAssign("cant_campos2" ,"value", '');
	return $respuesta;

}


$xajax=new xajax();   

    // Crea un nuevo objeto xajax
$xajax->setCharEncoding("iso-8859-1"); // le indica la codificación que debe utilizar
//$xajax->setCharEncoding("UTF-8");
$xajax->decodeUTF8InputOn();            // decodifica los caracteres extraños


$xajax->registerFunction("agregarFila");
$xajax->registerFunction("fgrabar");
$xajax->registerFunction("cancelar");
$xajax->processRequests();

//
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Pragma"content="no-cache">
<meta http-equiv="expires"content="0">
<head>
<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
<?php $xajax->printJavascript("xajax");  //imprime el codigo javascript necesario para que funcione todo. ?>
<link href="../asignacion/CSS/style.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="xajax_agregarFila(xajax.getFormValues('proyecto'),<?=$idasistencia?>,'LOAD')">

<form name="proyecto" id="proyecto"  method="post"  style="width:auto">
<div id='bitacora'>
<?
include_once('../asignacion/vista_bitacora.php');
?>
</div>
<input type="hidden" id="num_campos" name="num_campos" value="0"/>
    <input type="hidden" id="cant_campos" name="cant_campos" value="0"/>
<input type="hidden" id="num_campos2" name="num_campos2" value="0"/>
    <input type="hidden" id="cant_campos2" name="cant_campos2" value="0"/>
</form>
<iframe id='ifr_accion' name="ifr_accion" frameborder=0></iframe>

</body>
<script type="text/javascript">
function llamada(codigoarea,numero){
alert(codigoarea+numero);
	numero = codigoarea+numero;
	new Ajax.Request('/app/controlador/ajax/ajax_llamada.php',
	{
		method : 'get',
		parameters: {
			prefijo: "",
			num: numero,
			ext: '<?=$extension?>'
		},
		onSuccess: function(t){
			//		alert(t.responseText);

		}
	}
	);
	return;
}

function GrabarMonAfil(){
    

if(document.getElementById('comentario').value==''){
  xajax_fgrabar(<?=$idasistencia?>);
}else{
 xajax_fgrabar(<?=$idasistencia?>);
 xajax_agregarFila(xajax.getFormValues('proyecto'),<?=$idasistencia?>,document.getElementById('comentario').value,'ADD');
	
}


}
</script>
</html>