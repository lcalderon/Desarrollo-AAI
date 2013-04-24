#!/usr/local/bin/php -q 
<?
function conectadb($server,$user,$pass,$db){
	$link = mysql_connect($server, $user, $pass);
	mysql_select_db($db, $link); return $link;
}

function setCall($link,$canal,$callid,$exten,$uniqueid,$suniqueid){
	$fecha = date('Y-m-d H:i:s');
	$sql = "INSERT INTO monitor_llamada_queue (call_fecha,call_schannel,call_suniqueid,call_callid,call_exten,call_uniqueid) VALUES ('".$fecha."','".$canal."','".$suniqueid."','".$callid."','".$exten."','".$uniqueid."')";
	mysql_query($sql, $link);
	$sql_up = "UPDATE monitor_llamada SET EXTENSION = '".$exten."' WHERE UNIQUEID = '".$uniqueid."'";
	mysql_query($sql_up,$link);

}

function setCall2($link,$callid,$channel,$piloto,$uniqueid,$db2)
{
	$fecha = date('Y-m-d H:i:s');
	//expedientes en proceso
	$sql_exped_proc="
	SELECT 
		PT.NUMEROTELEFONO,
		PT.IDPERSONA 
	FROM
		expediente_persona_telefono PT 
	INNER JOIN expediente_persona EP ON PT.IDPERSONA = EP.IDPERSONA 
	INNER JOIN expediente E ON EP.IDEXPEDIENTE = E.IDEXPEDIENTE
    WHERE 
 	   PT.NUMEROTELEFONO = RIGHT('$callid',LENGTH('$callid')-1) 
       AND E.ARRSTATUSEXPEDIENTE = 'PRO'
    ORDER BY E.FECHAMOD DESC LIMIT 1
    ";

	$exec_exped_proc = mysql_query($sql_exped_proc,$link);
	$nreg_exped_proc = mysql_num_rows($exec_exped_proc);
	
	if($nreg_exped_proc==0)
	{
		//AFILIADOS
		$sql_afil="
		SELECT 
			NUMEROTELEFONO,
			IDAFILIADO 
		FROM 
			$db2.catalogo_afiliado_persona_telefono
        WHERE 
        	NUMEROTELEFONO = RIGHT('$callid',LENGTH('$callid')-1) 
        ORDER BY FECHAMOD DESC LIMIT 1
        ";
		$exec_afil = mysql_query($sql_afil,$link);
		$nreg_afil = mysql_num_rows($exec_afil);
		if($nreg_afil==0){
			//CONTACTOS DE PROVEEDORES
			$sql_contacto="
				SELECT 
					NUMEROTELEFONO,
					IDCONTACTO 
				FROM 
					$db2.catalogo_proveedor_contacto_telefono
	            WHERE 
	            	NUMEROTELEFONO = RIGHT('$callid',LENGTH('$callid')-1) 
	            ORDER BY FECHAMOD DESC LIMIT 1
	            ";
			$exec_contacto =mysql_query($sql_contacto,$link);
			$nreg_contacto = mysql_num_rows($exec_contacto);
			if($nreg_contacto==0){
				//PROVEEDOR
				$sql_proveedor="
					SELECT 
						NUMEROTELEFONO,IDPROVEEDOR 
					FROM 
						$db2.catalogo_proveedor_telefono
		            WHERE 
		            	NUMEROTELEFONO = RIGHT('$callid',LENGTH('$callid')-1) ORDER BY FECHAMOD DESC LIMIT 1
		            	";
				$exec_proveedor = mysql_query($sql_proveedor,$link);
				$nreg_proveedor = mysql_num_rows($exec_proveedor);
				if($nrge_proveedor==0){
					$idpersona = 0;
					$origen = '';
				}
				else{
					if($rset_proveedor=mysql_fetch_array($exec_proveedor))
					{
						$idpersona = $rset_proveedor['IDPROVEEDOR'];
						$origen = 'PROVEEDOR';
					}
				}
			}
			else{
				if($rset_contacto=mysql_fetch_array($exec_contacto))
				{
					$idpersona = $rset_contacto['IDCONTACTO'];
					$origen = 'CONTACTO';
				}
			}
		}else
		{
			if($rset_afil=mysql_fetch_array($exec_afil)){
				$idpersona = $rset_afil['IDAFILIADO'];
				$origen = 'AFILIADO';
			}
		}
	}
	else
	{
		if($rset_exped_proc=mysql_fetch_array($exec_exped_proc))
		{
			$idpersona = $rset_exped_proc['IDPERSONA'];
			$origen = 'EXPEDIENTE';
		}
	}

	$sql = "INSERT INTO monitor_llamada (TELEFONO,CHANNEL,DNID,STATUS,UNIQUEID,IDPERSONA,ORIGEN) VALUES ('".$callid."','".$channel."','".$piloto."','WAIT','".$uniqueid."','".$idpersona."','".$origen."')";
	mysql_query($sql, $link);
}


function hangup($link,$id,$status,$uniqueid)
{
	$sql = "UPDATE monitor_llamada_queue SET call_estado = '".$status."' WHERE call_suniqueid='".$uniqueid."'";
	mysql_query($sql,$link);
	if($status=='ANSWER')
	{
		$sql_update_answer="UPDATE monitor_llamada SET STATUS='".$status."' WHERE UNIQUEID='".$id."'";
		mysql_query($sql_update_answer,$link);
	}
	if($id==$uniqueid)
	{
		$sql_verifica="SELECT STATUS FROM monitor_llamada WHERE UNIQUEID ='".$id."' AND STATUS='ANSWER'";
		$exec_verifica = mysql_query($sql_verifica);
		$cont = mysql_num_rows($exec_verifica);
		if($cont==0)
		{
			$sql_update = "UPDATE monitor_llamada SET STATUS = 'HANGUP' WHERE UNIQUEID = '".$id."'";
			mysql_query($sql_update,$link);
		}
	}
}
 ?>
