<?php
	
	session_start();  
	
	include_once('../../../modelo/clase_mysqli.inc.php');

	$con= new DB_mysqli();
 
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
 
 
// Ruta del proceso en el desarrollo

/var/www/html/tc_tmk/app/tablero_control/procesos/ tc_tmk_cargar_data.php



//Librería de funciones

/var/www/html/tc_tmk/app/tablero_control/librerías/ funciones.php



function Conexion_SFTP($ftp_ip, $ftp_puerto, $ftp_usuario, $ftp_clave)
{
                //Verificar libreria SSH2
                if (!function_exists('ssh2_connect')) {
                               echo "     ERROR: " . _('This module requires the PHP ssh2 extension.')."\n";
                               return false;
                }
                
                if ($ftp_ip!='' && $ftp_puerto!='' && $ftp_usuario!='') {
                               // Creando conexión a servidor SSH, puerto 22
                               $conexion= @ssh2_connect($ftp_ip, $ftp_puerto);
                               // Autenticandose en el servidor
                               @ssh2_auth_password($conexion, $ftp_usuario, $ftp_clave);
                               // Solicitando subsistema SFTP
                               $sftp= @ssh2_sftp($conexion);
                               
                               if (!$sftp) {
                                               echo "Error al conectar al servidor SFTP\n";
                               } else {
                                               //echo "Conectado con $ftp_ip, para usuario $ftp_usuario\n";
                               }
                }
                
                return $sftp;
}

function Cargar_Campaign_File($cod_callcenter, $nom_archivo, $conex_ftp, $ruta_ftp, $conex_tablero) {
                $var_ruta_destino= $ruta_ftp;
                $var_archivo_destino= "ssh2.sftp://$conex_ftp".$var_ruta_destino.$nom_archivo;
                
                $fp= @fopen($var_archivo_destino,"r");
                if ($fp) {
                               while (($data= fgetcsv($fp,1000 ,"|"))!== FALSE ){
                                               $i= 0;
                                               unset($array_dato);
                                               $var_num_cols= count($data);
                                               foreach($data as $row){
                                                               $i++;
                                                               if (!($i==$var_num_cols && trim($row)=='')) {
                                                                              $array_dato[]= "'".trim($row)."'";           
                                                               }
                                               }
                                               $var_registro= implode(",",$array_dato);
                                               
                                               
                                               //Verificar si el registro existe
                                               $sql_veri= "SELECT * FROM aai_campaigns WHERE IDCAMPANIA=".$array_dato[0]." AND IDCALLCENTER='".$cod_callcenter."'";
                                               $rs_veri= mysql_query($sql_veri, $conex_tablero);
                                               if (mysql_num_rows($rs_veri)==0) {
                                                               //Insertar informacion en la tabla de campaigns del tablero
                                                               $sql_ins= "INSERT INTO aai_campaigns (IDCAMPANIA,NOMCAMPANIA,IDPAIS,ACTIVE,IDCALLCENTER) VALUES(".$var_registro.",'$cod_callcenter')";
                                                               $rs_ins= mysql_query($sql_ins, $conex_tablero);
                                               }
                               }
                               
                               echo "     Carga de campaigns terminada.\n";
                               fclose($fp);
                } else {
                               echo "     Error al cargar campaigns.\n";
                }
}

 
 
 ?>
