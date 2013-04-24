<?php

function fechahora(){

	$db = new DB_mysqli();
	$rs = $db->consultation("SELECT NOW() AS fechahora,LEFT(NOW(),10) AS fecha,RIGHT(NOW(),8) AS hora");

	return array($rs[0]["fechahora"],$rs[0]["fecha"],$rs[0]["hora"]);

}


function sql_evalue($value){

	if(get_magic_quotes_gpc())

	$value = stripslashes($value);

	//check if this function exists

	if(function_exists("mysql_real_escape_string"))
	$value = mysql_real_escape_string( $value );
	else//for PHP version < 4.3.0 use addslashes
	$value = addslashes( $value );

	return $value;
}

/************************************************************************************************
 *
 *   arrayProv( ) --> array
 *        array : Array de prueba que define el conjunto de proveedores que ingreasran para ser calculados  en el ranking
 *
 *	NOTA: Esta funcion debera ser remplazada por la funcion realizada  por Frank S�nchez para el array de proveedores basados en el ubigeo.
 *   Desarrollo: Pedro  Quispe sanchez
 *
 ************************************************************************************************/

function arrayProv()
{
	return array( array(1, 100,0),
	array(2,120,0),
	array(3,105,0),
	array(4,95,0),
	array(5,98,0),
	array(6,96,1),
	array(12,98,1),
	array(7,100,1) );
}

function diatexto($fechax){
	switch($fechax){
		case 'Monday' : $diatextoactual ='LUNES';break;
		case 'Tuesday' : $diatextoactual ='MARTES';break;
		case 'Wednesday' : $diatextoactual ='MIERCOLES';break;
		case 'Thursday' : $diatextoactual ='JUEVES';break;
		case 'Friday' : $diatextoactual ='VIERNES';break;
		case 'Saturday' : $diatextoactual ='SABADO';break;
		case 'Sunday' : $diatextoactual ='DOMINGO';break;
	}
	return $diatextoactual;
}

/************************************************************************************************
 *   08/27/2009 3:35p.m.
 *
 *   DiaSgteTexto( $arraydia,$diaactual,$mesactual ) --> $diatextoformato
 *		 $arraydia	: Array	con los numeros consecutivos a desplegar en el calendario
 *        $diaactual : Primer dia a mostrar en el calendario
 *        $mesactual : Mes desplegado en el calendario
 *        $diatextoformato : Array que contiene los dias consecutivos a ser desplegados formateado en el calendario, validados por el mes.
 *
 *   Desarrollo: Pedro  Quispe sanchez
 *
 ************************************************************************************************/

function DiaSgteTexto($arraydia,$diaactual,$mesactual)
{

	//echo $diaactual.' '.$mesactual;
	for($a=0;$a<count($arraydia);$a++){
		//echo $arraydia[$a];
		if($diaactual<=22){
			$diatextosgte[] = date( "l", strtotime('2009/'.$mesactual.'/'.$arraydia[$a]));

		}
		elseif($diaactual>22){

			if($arraydia[$a]>22){
				$diatextosgte[] = date( "l", strtotime('2009/'.$mesactual.'/'.$arraydia[$a]));
				//echo $diatextosgte[$a];;
			}
			else
			{
				if($measctual==12){
					$messgte = 1;
				}else{
					$messgte=$mesactual+1;
				}

				$diatextosgte[] = date( "l", strtotime('2009/'.$messgte.'/'.$arraydia[$a]));
				//echo $diatextosgte[$a];
			}
		}
			
		$diatextoformato[] = diatexto($diatextosgte[$a]);
	}
	return $diatextoformato;

}


/************************************************************************************************
 *   20091006
 *
 *   mostrarAnio_valido() --> $aniovalido,$numero_mes
 *		 $fecha	: fecha actual
 *        $fechainicial : fecha actual menos 1
 *   Desarrollo: Luis Calderon C.
 *
 ************************************************************************************************/

function mostrarAnio_valido()
{
	list(,$fecha,)=fechahora();

	$fechainicial=substr($fecha,2,2)-1;

	for($i= 0; $i <= 6; $i ++)
	{
		$fechaini= $fechainicial+$i;
		if(strlen($fechaini)==1)	$fechaini="0".$fechaini;	else $fechaini=$fechaini;
		$aniovalido[$fechaini] =$fechaini;
	}

	for($i= 1; $i <= 12; $i ++)
	{
		if(strlen($i)==1)	$valor="0".$i;	else $valor=$i;
		$numero_mes[$valor] =$valor;
	}

	return array($aniovalido,$numero_mes);

}

function mostrarAnio_Vehiculo()
{
	list(,$fecha,)=fechahora();

	$fechainicial=$fecha-40;

	for($i= 1; $i <= 41; $i ++)
	{
		$fechaini= $fechainicial+$i;
		$aniovehiculo[$fechaini] =$fechaini;
	}

	return $aniovehiculo;
}


function mostrar_Anio($cantidad=5)
{
	list(,$fecha,)=fechahora();

	$fechainicial=substr($fecha,0,4);

	for($i= 0; $i <= $cantidad; $i ++)
	{
		$aniovalido[$fechainicial-$i]=$fechainicial-$i;
	}
	//$aniovalido[]=rsort($aniovalido);
	return $aniovalido;
}

/************************************************************************************************
 *   20091006
 *
 *   filtradoStr() --> $str,$opc

 *   Desarrollo: Luis Calderon C.
 *
 ************************************************************************************************/

function filtradoStr($str,$opc)
{

	$quitar= array(',','  ','%','\'','/','\\');
	$str=trim(str_replace($quitar, "",$str));

	if(strlen(trim($str)) <=3)	$str_valido="";	//else	$str_valido=$str;

	$trozos=explode(" ",$str);
	$trozos=array_unique($trozos);
	$numero=count($trozos);

	for($i=0; $i <= $numero; $i ++)
	{
		if(strlen(trim($trozos[$i])) <=3)
		{
			continue;
		}
		else
		{
			$str_valido= $str_valido."+".$trozos[$i]."* ";
		}
	}

	$quitarlo= array('+','*');
	if($opc)	$str_valido=str_replace($quitarlo, "",trim($str_valido));
	
	if($numero >1){
		$str_valido=trim($str_valido);
		$str_valido=substr($str_valido,0,strlen($str_valido)-1);
	}
	
  //echo $str_valido."-";
	return trim($str_valido);

}

/************************************************************************************************
 *   Funcion para conectarse a la BD
 *
 *   conectadb( $server,$user,$pass,$db ) --> $link
 *		 $server : Ip del servidor de BD a conectarse
 *        $user : Usuario de conexion a la BD
 *        $pass : Password de conexion
 *        $db : Base de datos a conectarse
 *
 *   Desarrollo: Pedro  Quispe sanchez
 *
 ************************************************************************************************/

function conectadb($server,$user,$pass,$db){
	$link = mysql_connect($server, $user, $pass);
	mysql_select_db($db, $link); return $link;
}

/************************************************************************************************
 *   Funcion para loguearse usando el usuario manager
 *
 *   login( $socket,$user,$pass )
 *		 $socket : Socket
 *        $user : Usuario manager
 *        $pass : Password manager
 *
 *   Desarrollo: Pedro  Quispe Sánchez
 *
 ************************************************************************************************/

function login($socket,$user,$pass){
	fwrite($socket,"Action: login\r\n");
	fwrite($socket,"UserName: ".$user."\r\n");
	fwrite($socket,"Secret: ".$pass."\r\n\r\n");
}

/************************************************************************************************
 *   Funcion para desloguearse usando el usuario manager
 *
 *   logoff( $socket )
 *		 $socket : Socket
 *
 *   Desarrollo: Pedro  Quispe Sánchez
 *
 ************************************************************************************************/
function logoff($socket){
	fwrite($socket, "Action: Logoff\r\n\r\n");
}

/************************************************************************************************
 *   Funcion para generar una llamada usando el usuario manager, ademas permite enviar al plan de marcacion
 variables que se toran tomadas para el aramado de la grabacion como son el idasistencia y la etapa.
 Nota: Si la etapa toma un valor de 20 indica que la llamada se genero desde el monitor PBX
 *
 *   originateCmd($socket,$canal,$exten,$contexto,$idetapa,$idasistencia,$extension )
 *		$socket : Socket
 *		$canal : Canal utilizado
 *		$exten : Numero a marcar
 *		$contexto : Contexto utilizado
 *		$idetapa : ID de la etapa desde donde se genera la llamada
 *		$idasistencia : Numero de la asistencia
 *		$extension : Extension asociada desde donde se genera la llamada
 *   Desarrollo: Pedro  Quispe Sánchez
 *
 ************************************************************************************************/

function originateCmd($socket,$canal,$exten,$contexto,$extension){
	fwrite($socket, "Action: Originate\r\n");
	fwrite($socket, "Channel: ".$canal."\r\n");
	fwrite($socket, "Exten: ".$exten."\r\n");
	fwrite($socket, "Context: ".$contexto."\r\n");
	fwrite($socket, "Priority: 1\r\n\r\n");

}
/************************************************************************************************
 *   Funcion que permite redireccionar una llamada a una extension especifica; es utilizada para contestar
 las llamadas desde el monitor de la pbx.
 *
 *   redirectCmd($socket,$canal,$exten,$contexto)
 *		$socket : Socket
 *		$canal : Canal utilizado
 *		$exten : Numero a marcar
 *		$contexto : Contexto utilizado
 *   Desarrollo: Pedro  Quispe Sánchez
 *
 ************************************************************************************************/
function redirectCmd($socket,$canal,$exten,$contexto){
	fwrite($socket, "Action: redirect\r\n");
	fwrite($socket, "Channel: ".$canal."\r\n");
	fwrite($socket, "Exten: ".$exten."\r\n");
	fwrite($socket, "Context: ".$contexto."\r\n");
	fwrite($socket, "Priority: 1\r\n");
}



function originateCall($socket,$user,$pass,$canal,$exten,$contexto,$extension){
	login($socket,$user,$pass);
	originateCmd($socket,$canal,$exten,$contexto,$extension);
	logoff($socket);
}

function redirectCall($socket,$user,$pass,$canal,$exten,$contexto){
	login($socket,$user,$pass);
	redirectCmd($socket,$canal,$exten,$contexto);
	logoff($socket);
}

/************************************************************************************************
 *   Funcion que permite generar la ventana modal de justificacion
 *
 *   justificacion($modulo)
 *		$modulo : Cadena que identifica el modulo asociado al conjunto de justificaciones.
 Ejm: PROV - Asignacion de proveedores
 CM   - Cancelado al momento
 CP   - Cancelado Posterior
 RE   - Reasignar Proveedor, ...
 *   Desarrollo: Pedro  Quispe Sánchez
 *
 ************************************************************************************************/

function justificacion($modulo){
	$con = new DB_mysqli();
	$catalogo=$con->catalogo;
	$sql = "SELECT IDJUSTIFICACION,MOTIVO FROM $catalogo.catalogo_justificacion WHERE ARRJUSTIFICACIONMODULO= '$modulo'";
	$exec_sql = $con->query($sql);
	$combo .= "<select name='JUSTIFICACION' id='JUSTIFICACION'>";
	$combo .= "<option value='SEL'>"._('SELECCIONE')."</option>";
	while($rset=$exec_sql->fetch_object()){

		$combo .= "<option value='$rset->IDJUSTIFICACION'>$rset->MOTIVO</option>";
	}
	$combo .= "</select>";

	$justificacion .= "<form id='frmaddregistro' name='frmaddregistro'  method='POST' >";
	$justificacion .= "<table  align='left' border='0' cellpadding='1' cellspacing='1' width='100%'>";
	$justificacion .= "<tr ><th>"._('MOTIVO JUSTIFICACION')."<input type='hidden' name='hid_idasistencia' value='$idasistencia'><input type='hidden' name='hid_idproveedor' value=''></th>";
	$justificacion .= "<td>$combo</td></tr>";
	$justificacion .= "<tr ><td colspan='2'><textarea  name='OBSERVACION' id='OBSERVACION' rows='3' cols='60' class='classtexto' style='text-transform:uppercase;'></textarea></td></tr>";
	$justificacion .= "</table>";
	$justificacion .= "<span id='startrecord'></span>-<span id='endrecord'></span>";
	$justificacion .= "</form>";

	return $justificacion;
}

function justificacion_proveedor($modulo,$idprov){
	$con = new DB_mysqli();
	$catalogo=$con->catalogo;
	$sql = "SELECT IDJUSTIFICACION,MOTIVO FROM $catalogo.catalogo_justificacion WHERE ARRJUSTIFICACIONMODULO= '$modulo'";
	$exec_sql = $con->query($sql);
	$combo .= "<select name='JUSTIFICACION' id='JUSTIFICACION'>";
	$combo .= "<option value='SEL'>"._('SELECCIONE')."</option>";
	while($rset=$exec_sql->fetch_object()){

		$combo .= "<option value='$rset->IDJUSTIFICACION'>$rset->MOTIVO</option>";
	}
	$combo .= "</select>";

	$justificacion .= "<form id='frmaddregistro' name='frmaddregistro'  method='POST' >";
	$justificacion .= "<table  align='left' border='0' cellpadding='1' cellspacing='1' width='100%'>";
	$justificacion .= "<tr ><th>"._('MOTIVO JUSTIFICACION')."<input type='hidden' name='hid_idasistencia' value='$idasistencia'><input type='text' name='hid_idproveedor' value='$idprov'></th>";
	$justificacion .= "<td>$combo</td></tr>";
	$justificacion .= "<tr ><td colspan='2'><textarea  name='OBSERVACION' id='OBSERVACION' rows='3' cols='60' class='classtexto' style='text-transform:uppercase;'></textarea></td></tr>";
	$justificacion .= "</table>";
	$justificacion .= "<span id='startrecord'></span>-<span id='endrecord'></span>";
	$justificacion .= "</form>";

	return $justificacion;
}
/************************************************************************************************
 *   20100104
 *	calculo_tiemporespuesta

 *   Desarrollo: Luis Calderon C.
 *   Nota: Calculo del tiempo atencion de los casos en sac.
 ************************************************************************************************/

function calculo_tiemporespuesta2($idcaso)
{
	$db = new DB_mysqli();

	$Sql="SELECT
					@dias:=IF((SELECT COUNT(DISTINCT IDGRUPO) AS cantidad FROM $db->temporal.retencion_seguimiento WHERE IDRETENCION='$idcaso') <2,1,3) AS dias,

					@totalarea:=(SELECT COUNT(DISTINCT IDGRUPO) AS cantidad FROM $db->temporal.retencion_seguimiento WHERE IDRETENCION='$idcaso') AS cantidadAREA, 

					@diasf:=DATEDIFF(CURDATE(),LEFT(FECHARETENCION,10)) AS diasdif,
					 
					@sumad:= 
					IF(DAYOFWEEK(FECHARETENCION) =2 AND @totalarea >1,@dias,
					IF(DAYOFWEEK(FECHARETENCION) <6 AND @totalarea <2,@dias, 
					IF(DAYOFWEEK(FECHARETENCION) <6 AND @totalarea >1,@dias+2,

					IF(DAYOFWEEK(FECHARETENCION) >5 AND @totalarea <2,@dias+2,
					IF(DAYOFWEEK(FECHARETENCION) >5 AND @totalarea >1,@dias+2, '0' )) ))) AS diastotal,

					IF(ADDDATE(LEFT(FECHARETENCION,10),@sumad) < CURDATE() AND @totalarea <2 AND @diasf <3 AND STATUS_SEGUIMIENTO!='CON','#FFA851', 

					IF(ADDDATE(LEFT(FECHARETENCION,10),@sumad) < CURDATE() AND @totalarea <2 AND @diasf <5 AND DAYOFWEEK(FECHARETENCION) <5 AND STATUS_SEGUIMIENTO!='CON','#FF2F2F', 

					IF(ADDDATE(LEFT(FECHARETENCION,10),@sumad) < CURDATE() AND @totalarea <2 AND @diasf <5 AND DAYOFWEEK(FECHARETENCION) <6 AND STATUS_SEGUIMIENTO!='CON','#FFA851',

					IF(ADDDATE(LEFT(FECHARETENCION,10),@sumad) >= CURDATE() AND @totalarea >1 AND DAYOFWEEK(FECHARETENCION) <6 AND @diasf <2 AND STATUS_SEGUIMIENTO!='CON','FUERA',

					IF(ADDDATE(LEFT(FECHARETENCION,10),@sumad) >= CURDATE() AND @totalarea >1 AND DAYOFWEEK(FECHARETENCION) <6 AND @diasf <3 AND STATUS_SEGUIMIENTO!='CON','#FFA851',

					IF(ADDDATE(LEFT(FECHARETENCION,10),@sumad) >= CURDATE() AND @totalarea >1 AND DAYOFWEEK(FECHARETENCION) <6 AND @diasf >2 AND STATUS_SEGUIMIENTO!='CON','#FFA851',


					IF(ADDDATE(LEFT(FECHARETENCION,10),@sumad) >= CURDATE() AND @totalarea >1 AND DAYOFWEEK(FECHARETENCION) >5 AND @diasf <3 AND STATUS_SEGUIMIENTO!='CON','#FFA851', 

					IF(ADDDATE(LEFT(FECHARETENCION,10),@sumad) >= CURDATE() AND @totalarea >1 AND DAYOFWEEK(FECHARETENCION) >5 AND STATUS_SEGUIMIENTO!='CON','FUERA',

					IF(ADDDATE(LEFT(FECHARETENCION,10),@sumad) < CURDATE() AND STATUS_SEGUIMIENTO!='CON','#FF2F2F', 'FUERA' )))) ))))) AS RESP2,

					CURDATE(),FECHARETENCION,DAYOFWEEK(FECHARETENCION)
 
				  FROM $db->temporal.retencion WHERE IDRETENCION='$idcaso' ";

	$calculoh=$db->consultation($Sql);
	//echo $Sql."-----------";

	//if($calculoh[0][4]=="#FFA851" and $calculoh[0][5]!="#FF2F2F") $color="#FFAC59";	else if($calculoh[0][5]=="AUNAMBAR")  $color="#FFAC59";	 else if($calculoh[0][5]=="#FF2F2F")  $color="#FF4F4F";	 else $color="";
	if($calculoh[0][4]=="#FFA851") $color="#FFAC59";	else if($calculoh[0][4]=="#FF2F2F") $color="#FF4F4F";	else  $color="";

	return $color;

}

/************************************************************************************************
 *   20100104
 *	acceso_casoseguimiento

 *   Desarrollo: Luis Calderon C.
 *   Nota: Acceso a ejecutar seguiemito de los casos en SAC.
 ************************************************************************************************/

function acceso_casoseguimiento($idretencion)
{
	$db = new DB_mysqli();

	$accesocaso=$db->consultation("SELECT count(*) as cantidad FROM $db->temporal.grupo_usuario WHERE IDUSUARIO='".$_SESSION["user"]."' AND IDGRUPO = (SELECT  IDGRUPO FROM $db->temporal.retencion WHERE IDRETENCION='$idretencion')");

	return $accesocaso[0][0];
}
/*
 function ver_cuentas($usuario)
 {
 $db = new DB_mysqli();
 $allvalor=$db->consultation("SELECT TODOCUENTAS FROM $db->catalogo.catalogo_usuario WHERE IDUSUARIO='$usuario'  ");

 if($allvalor[0][0]==1)
 {
 $respsql="IDCUENTA LIKE '%' ";
 }
 else
 {
 $ver_cuentas=$db->consultation("SELECT GROUP_CONCAT('\'',IDCUENTA,'\'') AS cuentas FROM $db->temporal.seguridad_acceso_cuenta WHERE IDUSUARIO='".$usuario."'");
 if($ver_cuentas[0][0])	$respsql="IDCUENTA IN(".$ver_cuentas[0][0].") "; else $respsql="IDCUENTA IN('') ";
 }

 return array ($allvalor[0][0],$respsql);
 }

 function ver_cuentas2($usuario)
 {
 $db = new DB_mysqli();
 $allvalor=$db->consultation("SELECT TODOCUENTAS FROM $db->catalogo.catalogo_usuario WHERE IDUSUARIO='$usuario'  ");

 if($allvalor[0][0]==1)
 {
 //$respsql="IDCUENTA LIKE '%' ";
 $respsql=" ";
 }
 else
 {
 $ver_cuentas=$db->consultation("SELECT GROUP_CONCAT('\'',IDCUENTA,'\'') AS cuentas FROM $db->temporal.seguridad_acceso_cuenta WHERE IDUSUARIO='".$usuario."'");
 if($ver_cuentas[0][0])	$respsql="catalogo_cuenta.IDCUENTA IN(".$ver_cuentas[0][0].") AND "; else $respsql="catalogo_cuenta.IDCUENTA IN('') AND ";
 }

 return array ($allvalor[0][0],$respsql);
 } */

function accesos_cuentas($usuario,$idcuenta="")
{
	$db = new DB_mysqli();
	$allvalor=$db->consultation("SELECT TODOCUENTAS FROM $db->catalogo.catalogo_usuario WHERE IDUSUARIO='$usuario'  ");

	if($allvalor[0][0]==1)
	{
		$respsql="";
		$existCta=1;
	}
	else
	{
		$ver_cuentas=$db->consultation("SELECT GROUP_CONCAT('\'',IDCUENTA,'\'') AS cuentas FROM $db->temporal.seguridad_acceso_cuenta WHERE IDUSUARIO='".$usuario."'");
		if($ver_cuentas[0][0])	$respsql="catalogo_cuenta.IDCUENTA IN(".$ver_cuentas[0][0].") AND "; else $respsql="catalogo_cuenta.IDCUENTA IN('') AND";
		$ids=$ver_cuentas[0][0];
		
		if($idcuenta){
			
			$existe_cuenta=$db->consultation("SELECT COUNT(*) FROM $db->temporal.seguridad_acceso_cuenta WHERE IDUSUARIO='".$usuario."' AND IDCUENTA='$idcuenta'");
			$existCta=($existe_cuenta[0][0] >0)?"1":"0";
		}
		
	}

	return array ($allvalor[0][0],$respsql,$ids,$existCta);
}


//SUMAR DIAS A UNA FECHA ESPECIFICA

function sumaDia($fecha,$dia)
{
	list($year,$mon,$day) = explode('-',$fecha);
	return date('Y-m-d',mktime(0,0,0,$mon,$day+$dia,$year));
}

//CALCULO DEL SEGUIMIENTO(COLOR) DE LOS RECLAMOS DEL SAC
function calculo_tiemporespuesta($cantidad_area,$fecharet,$dia_semana)
{
	$color="";
	if($cantidad_area >1)
	{

		if($dia_semana ==2)
		{
			if(date("Y-m-d") > sumaDia($fecharet,1) )	$color="#FFA851";
			if(date("Y-m-d") > sumaDia($fecharet,3) )	$color="#FF2F2F";
		}
		else if($dia_semana ==3)
		{
			if(date("Y-m-d") > sumaDia($fecharet,1) )	$color="#FFA851";
			if(date("Y-m-d") > sumaDia($fecharet,5) )	$color="#FF2F2F";
		}
		else if($dia_semana ==4)
		{
			if(date("Y-m-d") > sumaDia($fecharet,1) )	$color="#FFA851";
			if(date("Y-m-d") > sumaDia($fecharet,5) )	$color="#FF2F2F";
		}
		else if($dia_semana ==5)
		{
			if(date("Y-m-d") > sumaDia($fecharet,3) )	$color="#FFA851";
			if(date("Y-m-d") > sumaDia($fecharet,5) )	$color="#FF2F2F";
		}
		else if($dia_semana ==6)
		{
			if(date("Y-m-d") > sumaDia($fecharet,3) )	$color="#FFA851";
			if(date("Y-m-d") > sumaDia($fecharet,5) )	$color="#FF2F2F";
		}
		else if($dia_semana ==7)
		{
			if(date("Y-m-d") > sumaDia($fecharet,2) )	$color="#FFA851";
			if(date("Y-m-d") > sumaDia($fecharet,4) )	$color="#FF2F2F";
		}

	}
	else
	{
		if($dia_semana <5)
		{
			if(date("Y-m-d") > sumaDia($fecharet,1) )	$color="#FFA851";
			if(date("Y-m-d") > sumaDia($fecharet,2) )	$color="#FF2F2F";
		}
		else if($dia_semana ==5)
		{
			if(date("Y-m-d") > sumaDia($fecharet,3) )	$color="#FFA851";
			if(date("Y-m-d") > sumaDia($fecharet,4) )	$color="#FF2F2F";
		}
		else if($dia_semana ==6)
		{
			if(date("Y-m-d") > sumaDia($fecharet,3) )	$color="#FFA851";
			if(date("Y-m-d") > sumaDia($fecharet,4) )	$color="#FF2F2F";
		}
		else if($dia_semana ==7)
		{
			if(date("Y-m-d") > sumaDia($fecharet,2) )	$color="#FFA851";
			if(date("Y-m-d") > sumaDia($fecharet,3) )	$color="#FF2F2F";
		}
	}
		
	return $color;

}

//PERMISOS DE ACEPTAR Y RECHAZAR DELEGACION.

function delegar_aprobacion($accesoadm,$userdelepor,$userdelehacia)
{
	$db = new DB_mysqli();
	$usracceso=$db->consultation("SELECT COUNT(*) FROM $db->temporal.grupo_usuario WHERE IDUSUARIO='$userdelepor' AND IDGRUPO='SUCA' ");

	if($accesoadm and $userdelehacia==$_SESSION["user"])
	{
		$permiso=1;
	}
	else if($accesoadm and $userdelehacia!=$_SESSION["user"])
	{
		$permiso=1;
	}
	else if(!$accesoadm and $usracceso[0][0] ==0)
	{
		$permiso=1;
	}
	else if(!$accesoadm and $usracceso[0][0] ==0)
	{
		$permiso=0;
	}

	return $permiso;

}

// Copyright John Pezullo
// Released under same terms as PHP.
// PHP Port and OO'fying by Paul Meagher

function doCommonMath($q, $i, $j, $b) {
		
	$zz = 1;
	$z  = $zz;
	$k  = $i;
		
		
	while($k <= $j) {
		$zz = $zz * $q * $k / ($k - $b);
		$z  = $z + $zz;
		$k  = $k + 2;
	}
		
	return $z;
		
}

function getStudentT($t, $df) {

	$t  = abs($t);
	$w  = $t  / sqrt($df);
	$th = atan($w);
		
	if ($df == 1) {
		return 1 - $th / (pi() / 2);
	}
		
	$sth = sin($th);
	$cth = cos($th);
		
	if( ($df % 2) ==1 ) {
		return
		1 - ($th + $sth * $cth * doCommonMath($cth * $cth, 2, $df - 3, -1))
		/ (pi()/2);
	} else {
		return 1 - $sth * doCommonMath($cth * $cth, 1, $df - 3, -1);
	}
		
}

function getInverseStudentT($p, $df) {
		
	$v =  0.5;
	$dv = 0.5;
	$t  = 0;
		
	while($dv > 1e-6) {
		$t = (1 / $v) - 1;
		$dv = $dv / 2;
		if ( getStudentT($t, $df) > $p) {
			$v = $v - $dv;
		} else {
			$v = $v + $dv;
		}
	}
	return $t;
}

function standard_deviation($std)
{
	$total;
	while(list($key,$val) = each($std))
	{
		$total += $val;
	}
	reset($std);
	$mean = $total/count($std);
		
	while(list($key,$val) = each($std))
	{
		$sum += pow(($val-$mean),2);
	}
	$var = sqrt($sum/(count($std)-1));
	return $var;
}

function standard_deviation_sample ($a)
{
	//variable and initializations
	$the_standard_deviation = 0.0;
	$the_variance = 0.0;
	$the_mean = 0.0;
	$the_array_sum = array_sum($a); //sum the elements
	$number_elements = count($a); //count the number of elements

	//calculate the mean
	$the_mean = $the_array_sum / $number_elements;

	//calculate the variance
	for ($i = 0; $i < $number_elements; $i++)
	{
		//sum the array
		$the_variance = $the_variance + ($a[$i] - $the_mean) * ($a[$i] - $the_mean);
	}

	$the_variance = $the_variance / ($number_elements - 1.0);

	//calculate the standard deviation
	$the_standard_deviation = pow( $the_variance, 0.5);

	//return the variance
	return $the_standard_deviation;
}

	function validar_url($exp_asis,$codexpd_asis,$mensaje){
	
		if(crypt($exp_asis,"666")!=$codexpd_asis){
			echo "<script>";
			echo "alert('*** $mensaje !!! ***');";
			echo "window.close();";
			echo "</script>";

			die("*** $mensaje !!! ***");
		}
	}

	//verificacion de dias validos de vigencia
	function Numero_Dias($fecha_ini,$fecha_fin){
		//Fecha inicial
		$ano1=date("Y", strtotime($fecha_ini));
		$mes1=date("m", strtotime($fecha_ini));
		$dia1=date("d", strtotime($fecha_ini));
		
		//Fecha final
		$ano2=date("Y", strtotime($fecha_fin));
		$mes2=date("m", strtotime($fecha_fin));
		$dia2=date("d", strtotime($fecha_fin));

		//calcular timestam de las dos fechas
		$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
		$timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);
		
		//restar a una fecha la otra
		$segundos_diferencia = $timestamp2 - $timestamp1;
			
		//convertir segundos en días
		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);
		
		//Redondear el los dias
		$dias_diferencia = round($dias_diferencia,0);
	   
		return $dias_diferencia; 
	}
	
	//verificacion de la licencia
	function verificar_licencia(){
		
		$db = new DB_mysqli();
		$fecha_caducidad=$db->lee_parametro("FECHA_CADUCIDAD_SOAANG");
		$dias_recordatorio=$db->lee_parametro("DIAS_RECORDATORIO_CADUCIDAD");
		
		global $DiaNro, $fecha_res, $Dias_record;
		
		$fecha_caducidad= $fecha_caducidad; //Fecha de Caducidad
		$num_dias_recordatorio= $dias_recordatorio; //Dias de recordatorio
		$fecha_actual= date("Y-m-d"); //Fecha Actual

		$DiaNro= Numero_Dias($fecha_actual, $fecha_caducidad);
		$fecha_res= date("Y-m-d", strtotime("$fecha_caducidad -$num_dias_recordatorio day")); //Dias antes de la caducidad
		
		$Dias_record= Numero_Dias($fecha_actual, $fecha_res); //Resultado fecha de advertencia
		
		$array_licencia["num_dias_res"]= $DiaNro;
		
		if($DiaNro <= 0){
			$array_licencia["id"]= 1;
		} else if($Dias_record <= 0){
			$array_licencia["id"]= -1;
		} else{
			$array_licencia["id"]= 0;
		}
		
		return $array_licencia;
	}	
	
	function getBrowser(){
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}
	   
		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		elseif(preg_match('/Firefox/i',$u_agent))
		{
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		elseif(preg_match('/Chrome/i',$u_agent))
		{
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		elseif(preg_match('/Safari/i',$u_agent))
		{
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		elseif(preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Opera';
			$ub = "Opera";
		}
		elseif(preg_match('/Netscape/i',$u_agent))
		{
			$bname = 'Netscape';
			$ub = "Netscape";
		}
	   
		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}
	   
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version= $matches['version'][0];
			}
			else {
				$version= $matches['version'][1];
			}
		}
		else {
			$version= $matches['version'][0];
		}
	   
		// check if we have a number
		if ($version==null || $version=="") {$version="?";}
	   
		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'    => $pattern
		);
	}

/* 	//obtenciones de urls 
	function getUrl($url=""){
		
		//$url="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		//$url=urlencode(str_replace("&","%",$_SERVER['REQUEST_URI']));
		$url=urlencode($url);
		
		return $url;
		
	} */
	
	 function validar_permiso($nombreaccess,$opc=0,$nombreaccessOpcional=""){
 
		if($nombreaccessOpcional !="") $nombreaccess=$nombreaccessOpcional;
	
		$db = new DB_mysqli();			
		$db->select_db($db->temporal);	
		 
		$result = $db->query("SELECT IDMODULO FROM seguridad_modulosxusuario WHERE IDUSUARIO = '".$_SESSION["user"]."'");
	
		while($row = $result->fetch_object())	$valoracc[$row->IDMODULO]=$row->IDMODULO;
		if($opc==0)
		 {
			if(in_array($nombreaccess, $valoracc))	return	true;
		 }
		else
		 {
			if(!in_array($nombreaccess, $valoracc))	die(_("*** ACCESO NO PERMITIDO. ***"));;
		 
		 }
	}
	 	
/**
 * Reemplaza todos los acentos por sus equivalentes sin ellos
 *
 * @param $string
 *  string la cadena a sanear
 *
 * @return $string
 *  string saneada
 */
	function quitar_caracteresEspeciales($string,$opc){
		 
		$string = utf8_encode(trim($string));
		 
		$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
		);
	 
		$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
		);
	 
		$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
		);
	 
		$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
		);
	 
		$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
		);
	 
		$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
		);
	 
		//Esta parte se encarga de eliminar cualquier caracter extraño
		$string = str_replace(
			array("\\", "¨", "º", "-", "~",
				 "#", "@", "|", "!", "\"",
				 "·", "$", "%", "&", "/",
				 "(", ")", "?", "'", "¡",
				 "¿", "[", "^", "`", "]",
				 "+", "}", "{", "¨", "´",
				 ">", "<", ";", ",", ":",
				 ".", " "),
			'',
			$string
		);
	
		$string = str_replace("*","",$string);
		$string = str_replace(" ","",$string);
		$string = str_replace("/","_",$string);
		$string = str_replace("-","_",$string);
			
		return strtoupper($string);
	}

//reemplazar ñ y tildes a mayusculas
	function remplazar_enes_tildes($string){
		 
		$string = trim($string);

		$string = str_replace("á","Á",$string);
		$string = str_replace("é","É",$string);
		$string = str_replace("í","Í",$string);
		$string = str_replace("ó","Ó",$string);
		$string = str_replace("ú","Ú",$string); 
		$string = str_replace("ñ","Ñ",$string);
		$string = strtoupper(utf8_decode($string));
			
		return $string;
	}	
	
	function campoDescripcion($idasistencia){
		
		$db = new DB_mysqli();			
			
		$Sql_plantilla="SELECT
						   CONCAT(LOWER(catalogo_familia.DESCRIPCION),'_',REPLACE(catalogo_plantilla.VISTA,'.php','')) AS TABLA, 
						   catalogo_plantilla.CAMPOAPRESENTAR
						FROM $db->temporal.asistencia
						  INNER JOIN $db->catalogo.catalogo_servicio
							ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
						  INNER JOIN $db->catalogo.catalogo_familia
							ON catalogo_familia.IDFAMILIA = catalogo_servicio.IDFAMILIA  								
						  INNER JOIN $db->catalogo.catalogo_plantilla
							ON catalogo_plantilla.IDPLANTILLA = catalogo_servicio.IDPLANTILLA
						WHERE asistencia.IDASISTENCIA = '$idasistencia'";

							
			$rs_plantilla=$db->query($Sql_plantilla);							
			$row = $rs_plantilla->fetch_object();
			$ocurrido=$db->consultation("SELECT $row->CAMPOAPRESENTAR FROM $db->temporal.asistencia_$row->TABLA WHERE IDASISTENCIA='$idasistencia'");
	
		return $ocurrido[0][0];
	}
	
?>