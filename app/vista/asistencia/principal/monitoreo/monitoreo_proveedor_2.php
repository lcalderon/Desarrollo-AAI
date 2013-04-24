<?php
session_start();
//require_once('/var/www/soaang_preproduccion/app/vista/asistencia/asignacion/xajax/xajax.inc.php');
include_once('../../../../librerias/xajax/xajax.inc.php');
include_once('../../../modelo/clase_lang.inc.php');
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../../modelo/clase_usuario.inc.php');
$idusuario = $_SESSION[user];
$idasistencia=$_GET[idasistencia];
//$idasistencia=21;
$varetapa='MONPROV';
$con = new DB_mysqli();
//$catalogo=$con->catalogo;

$con->select_db($con->catalogo);
$temporal=$con->temporal;
$usuario = new usuario();
//$user=$_GET['user'];
$extension = $usuario->extension_usada($_SESSION[user]);
$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');

function fgrabar($idasistencia){
$con = new DB_mysqli();
$con->select_db($con->temporal);
if($xetapa<4){
	  $rows[IDETAPA]=4;
	//actualiza los datos
 	$resultado=$con->update("asistencia",$rows,"WHERE IDASISTENCIA=".$idasistencia);
}

}


function agregarFilabitacora($formulario,$idasistencia,$comentario){
$con = new DB_mysqli();
$con->select_db($con->temporal);


$catalogo=$con->catalogo;


$bitacora[IDMONITOREOASISTENCIA]="";
$bitacora[IDUSUARIOMOD]=$_SESSION['user'];
$bitacora[IDETAPA]="3";
$bitacora[IDASISTENCIA]=$idasistencia;
$bitacora[COMENTARIO]=$comentario;

$con->insert_reg('asistencia_monitoreo_proveedor',$bitacora);
 $sql_verifica_etapa="SELECT IDETAPA FROM asistencia WHERE IDASISTENCIA = $idasistencia";
  $exec_verifica_etapa = $con->query($sql_verifica_etapa);
if($rset_verifica_etapa = $exec_verifica_etapa->fetch_object()){
    $xetapa=$rset_verifica_etapa->IDETAPA;
}


	$respuestab = new xajaxResponse();  
      
	extract($formulario);	

	$id_camposb = $cant_camposb = $num_camposb+1;
	//echo $id_campos;
   
	if($num_campos2 == 0){ // creamos un encabezado de lo contrario solo agragamos la fila
	$respuestab->addCreate("tbDetalleb", "tr", "rowDetalleb_0");
        $respuestab->addCreate("rowDetalleb_0", "th", "tdDetalleb_01");    //creamos los campos
	$respuestab->addCreate("rowDetalleb_0", "th", "tdDetalleb_02");
	$respuestab->addCreate("rowDetalleb_0", "th", "tdDetalleb_03");

	  $respuestab->addAssign("tdDetalleb_01", "innerHTML", "FECHAHORA");   //asignamos el contenido
	$respuestab->addAssign("tdDetalleb_02", "innerHTML", "USUARIO");   //asignamos el contenido
	$respuestab->addAssign("tdDetalleb_03", "innerHTML", "COMENTARIO");   //asignamos el contenido
	}

    $idRowb = "rowDetalleb_$id_camposb";
    $idTdb = "tdDetalleb_$id_camposb";
            
    $respuestab->addCreate("tbDetalleb", "tr", $idRowb);
    if($num_campos2%2==0){ $estilo = '#D5D190'; }else{ $estilo = ''; } 
$num_campos2=$num_campos2+1; 
      $respuestab->addAssign($idRowb, "style.background",$estilo);
    //$respuestab->addAssign("tbDetalleb", "class",'impar');
      $respuestab->addCreate($idRowb, "td", $idTdb."1");     //creamos los campos
      $respuestab->addCreate($idRowb, "td", $idTdb."2");
      $respuestab->addCreate($idRowb, "td", $idTdb."3");
    
    $sql_bitacora="SELECT COMENTARIO, FECHAMOD,IDUSUARIOMOD 
		    FROM asistencia_monitoreo_proveedor
		WHERE IDASISTENCIA = $idasistencia
		ORDER BY FECHAMOD DESC LIMIT 1";
//echo $sql_prov;
    $exec_bitacora = $con->query($sql_bitacora);

    if($rset_bitacora=$exec_bitacora->fetch_object())
    {

      $respuestab->addAssign($idTdb."1", "align","center");
      $respuestab->addAssign($idTdb."1", "innerHTML", $rset_bitacora->FECHAMOD);   //asignamos el contenido
      $respuestab->addAssign($idTdb."2", "align","left");
      $respuestab->addAssign($idTdb."2", "innerHTML", $rset_bitacora->IDUSUARIOMOD);
      $respuestab->addAssign($idTdb."3", "align","LEFT");
      $respuestab->addAssign($idTdb."3", "width", "80%");
      $respuestab->addAssign($idTdb."3", "innerHTML","<pre>".$rset_bitacora->COMENTARIO."</pre>");

    }



	$respuestab->addAssign("num_camposb","value", $id_camposb);
	$respuestab->addAssign("cant_camposb" ,"value", $id_camposb);
	$respuestab->addAssign("num_campos2" ,"value", $num_campos2);
	$respuestab->addAssign("comentario" ,"value", ""); 
	
	return $respuestab;

}

function agregarFila($formulario,$idasistencia){
$con = new DB_mysqli();
$usuario = new usuario();
$extension = $usuario->extension_usada($_SESSION[user]);

$prefijo = $con->lee_parametro('PREFIJO_LLAMADAS_SALIENTES');

$con->select_db($con->temporal);
$db=$con->catalogo;

	$respuesta2 = new xajaxResponse();  
      
	extract($formulario);	
	

	 $num_camposb=0;
	$id_campob=0;

	$respuesta2->addRemove("tbDetalle2");
	$respuesta2->addCreate("tblDetalle2", "tbody", "tbDetalle2");
      


       //$id_campos1 = $cant_campos1 = $num_campos1+1;
	if($num_campos2 == 0){ // creamos un encabezado de lo contrario solo agragamos la fila
	$respuesta2->addCreate("tbDetalleb", "tr", "rowDetalleb_0");
        $respuesta2->addCreate("rowDetalleb_0", "th", "tdDetalleb_01");    //creamos los campos
	$respuesta2->addCreate("rowDetalleb_0", "th", "tdDetalleb_02");
	$respuesta2->addCreate("rowDetalleb_0", "th", "tdDetalleb_03");

	  $respuesta2->addAssign("tdDetalleb_01", "innerHTML", "FECHAHORA");   //asignamos el contenido
	$respuesta2->addAssign("tdDetalleb_02", "innerHTML", "USUARIO");   //asignamos el contenido
	$respuesta2->addAssign("tdDetalleb_03", "innerHTML", "COMENTARIO");   //asignamos el contenido
       
	}

    


    $sql_prov2="SELECT COMENTARIO, FECHAMOD,IDUSUARIOMOD 
		    FROM asistencia_monitoreo_proveedor
		WHERE IDASISTENCIA = $idasistencia
		ORDER BY FECHAMOD DESC ";
//echo $sql_prov;
    $exec_prov2 = $con->query($sql_prov2);
$nreg_prov2= $exec_prov2->num_rows;
 
if($nreg_prov2==0){
}else{
    while($rset_prov2=$exec_prov2->fetch_object())
    {
    

    $idRow2 = "rowDetalle2_$id_campos2";
    $idTd2 = "tdDetalle2_$id_campos2";
     
      $respuesta2->addCreate("tbDetalleb", "tr", $idRow2);
    if($id_campos2%2==0){ $estilo = '#D5D190'; }else{ $estilo = ''; }
      $respuesta2->addAssign($idRow2, "style.background",$estilo);
    //$respuestab->addAssign("tbDetalleb", "class",'impar');
    $respuesta2->addCreate($idRow2, "td", $idTd2."1");     //creamos los campos
    $respuesta2->addCreate($idRow2, "td", $idTd2."2");
    $respuesta2->addCreate($idRow2, "td", $idTd2."3");

		//echo $rset_prov->IDPROVEEDOR;

     
      $respuesta2->addAssign($idTd2."1", "align","center");
      $respuesta2->addAssign($idTd2."1", "innerHTML", $rset_prov2->FECHAMOD);   //asignamos el contenido
      $respuesta2->addAssign($idTd2."2", "align","left");
      $respuesta2->addAssign($idTd2."2", "innerHTML", $rset_prov2->IDUSUARIOMOD);
      $respuesta2->addAssign($idTd2."3", "align","LEFT");
      $respuesta2->addAssign($idTd2."3", "width", "80%");
      $respuesta2->addAssign($idTd2."3", "innerHTML","<pre>".$rset_prov2->COMENTARIO."</pre>");
	 //echo $id_campos2;
	$id_campos2++;
    }
}	
     
	$respuesta2->addAssign("num_campos2","value", $id_campos2);
	$respuesta2->addAssign("cant_campos2" ,"value", $id_campos2);
	
	return $respuesta2;

}

$xajax=new xajax();   

    // Crea un nuevo objeto xajax
$xajax->setCharEncoding("iso-8859-1"); // le indica la codificación que debe utilizar
//$xajax->setCharEncoding("UTF-8");
$xajax->decodeUTF8InputOn();            // decodifica los caracteres extraños

$xajax->registerFunction("agregarFilabitacora");
$xajax->registerFunction("agregarFila");
$xajax->registerFunction("fgrabar");

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
<body onLoad="xajax_agregarFila(xajax.getFormValues('proyecto'),<?=$idasistencia?>)">
<!--<body>-->
<?
$sql_prov_asignado="SELECT P.IDPROVEEDOR,P.NOMBRECOMERCIAL,PC.IDPERSONA,CONCAT(PE.APMATERNO,' ',PE.APPATERNO,' ',PE.NOMBRE) CONTACTO,
   IF(P.INTERNO=1,'INTERNO','EXTERNO') INTERNO,
   (SELECT NUMEROTELEFONO FROM catalogo_telefono CPT INNER JOIN catalogo_persona_telefono CPET
 ON CPT.IDTELEFONO = CPET.IDTELEFONO WHERE CPET.IDPERSONA = PC.IDPERSONA AND CPET.PRIORIDAD = 1) TELEFONOCONTACTO,
IF(MAX(CASE(PT.PRIORIDAD) WHEN 1 THEN CT.NUMEROTELEFONO END) IS NULL,0,MAX(CASE(PT.PRIORIDAD) WHEN 1 THEN CT.NUMEROTELEFONO END)) AS TELEFONO1,
IF(MAX(CASE(PT.PRIORIDAD) WHEN 2 THEN CT.NUMEROTELEFONO END) IS NULL,0,MAX(CASE(PT.PRIORIDAD) WHEN 2 THEN CT.NUMEROTELEFONO END)) AS TELEFONO2,
IF(MAX(CASE(PT.PRIORIDAD) WHEN 3 THEN CT.NUMEROTELEFONO END) IS NULL,0,MAX(CASE(PT.PRIORIDAD) WHEN 3 THEN CT.NUMEROTELEFONO END))  AS TELEFONO3,
AP.TEATCP,AP.TEAMCP
 FROM catalogo_proveedor P INNER JOIN $temporal.asistencia_asig_proveedor AP
 ON P.IDPROVEEDOR = AP.IDPROVEEDOR
 LEFT JOIN catalogo_proveedor_contacto PC
  ON P.IDPROVEEDOR = PC.IDPROVEEDOR  AND PC.RESPONSABLE = 1
 LEFT JOIN catalogo_persona PE
 ON PC.IDPERSONA = PE.IDPERSONA
LEFT JOIN catalogo_proveedor_telefono PT ON P.IDPROVEEDOR = PT.IDPROVEEDOR
LEFT JOIN catalogo_telefono CT ON PT.IDTELEFONO = CT.IDTELEFONO 
 WHERE AP.IDASISTENCIA = $idasistencia AND AP.STATUSPROVEEDOR = 'AC'
 GROUP BY 1";
//echo $sql_prov_asignado;
$exec_prov_asignado = $con->query($sql_prov_asignado);
while($rset_prov_asignado = $exec_prov_asignado->fetch_object())
{
    $idproveedor = $rset_prov_asignado->IDPROVEEDOR;
  //echo $idprovedor;
    $provcomercial = $rset_prov_asignado->NOMBRECOMERCIAL;
    $contacto = $rset_prov_asignado->CONTACTO;
    $telefonocontacto = $rset_prov_asignado->TELEFONOCONTACTO;
    $telefono1 = $rset_prov_asignado->TELEFONO1;
    $telefono2 = $rset_prov_asignado->TELEFONO2;
    $telefono3 = $rset_prov_asignado->TELEFONO3;
    $teat = $rset_prov_asignado->TEATCP;
    $team = $rset_prov_asignado->TEAMCP;
}
?>



<form name="proyecto" id="proyecto"  method="post"  style="width:auto">
<div id='bitacora'>
<?
include_once('../asignacion/vista_bitacora.php');
?>
</div>
<input type="hidden" id="num_camposb" name="num_camposb" value="0"/>
    <input type="hidden" id="cant_camposb" name="cant_camposb" value="0"/>
<input type="hidden" id="num_campos2" name="num_campos2" value="0"/>
    <input type="hidden" id="cant_campos2" name="cant_campos2" value="0"/>
</form>
<iframe id='ifr_accion' name="ifr_accion" frameborder=0></iframe>


</body>

<script type="text/javascript">
function llamada(extension,pre,numero){
	
			new Ajax.Request('../../../controlador/ajax/ajax_llamada.php',
			{	method : 'get',
				parameters: {ext:extension, prefijo :pre, num: numero }
			 
			}
			);
	   }
</script>
</html>