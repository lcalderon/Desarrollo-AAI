<?php

	class afiliado
	{
        public $idafiliado;
        public $db;

        public function __construct($idafiliado){
            
			$this->idafiliado= $idafiliado;
			$this->db = new DB_mysqli();
        }
		 
        public function informacionAfiliado(){
		
			$Sqlinfo_afiliado="SELECT
								catalogo_afiliado.IDAFILIADO,
								catalogo_afiliado.CVEAFILIADO,
								catalogo_afiliado.IDCUENTA,
								catalogo_cuenta.NOMBRE AS NOMBRECUENTA,
								catalogo_afiliado.IDPROGRAMA,
								catalogo_programa.NOMBRE AS NOMBREPLAN,
								catalogo_afiliado.FECHAINICIOVIGENCIA,
								catalogo_afiliado.FECHAFINVIGENCIA,
								catalogo_afiliado.FECHACANCELACION,
								catalogo_afiliado.FECHACREACION,
								catalogo_afiliado.STATUSASISTENCIA,
								catalogo_afiliado.STATUSCOMERCIAL,
								catalogo_afiliado.AFILIADO_SISTEMA,
								catalogo_afiliado.ARRMODALIDADPG,
								catalogo_afiliado_persona.NOMBRE,
								catalogo_afiliado_persona.APPATERNO,
								catalogo_afiliado_persona.APMATERNO,
								catalogo_afiliado_persona.EMAIL1,
								catalogo_afiliado_persona.EMAIL2,
								CONCAT(
									catalogo_afiliado_persona.NOMBRE,
									' ',
									catalogo_afiliado_persona.APPATERNO,
									' ',
									catalogo_afiliado_persona.APMATERNO
								) AS NOMBREPERSONA,

							IF (
								catalogo_afiliado_persona.EMAIL1 !='' AND catalogo_afiliado_persona.EMAIL2 !='',
								CONCAT(
									catalogo_afiliado_persona.EMAIL1,
									',',
									catalogo_afiliado_persona.EMAIL2,
									',',
									catalogo_afiliado_persona.EMAIL3
								),
								catalogo_afiliado_persona.EMAIL1 
							) AS EMAILS,
							 catalogo_afiliado_persona.ESTADOCIVIL,
							 catalogo_afiliado_persona.FECHANACIMIENTO,
							 catalogo_afiliado_persona.GENERO,
							 catalogo_afiliado_persona.IDGRUPOSANGUINEO,
							 catalogo_afiliado_persona.IDDOCUMENTO,
							 catalogo_afiliado_persona.IDTIPODOCUMENTO,
							 catalogo_afiliado_persona.ID_SISTEMASALUD,
							 catalogo_afiliado_persona.VIVECONQUIEN,
							 catalogo_afiliado_persona.VIVESOLO,
							 catalogo_afiliado_persona.DIGITOVERIFICADOR,
							 catalogo_afiliado_persona.AFILIADO_VIP,
							 catalogo_afiliado_persona_ubigeo.IDAFILIADO AS IDUBIGEO,
							 catalogo_afiliado_persona_ubigeo.CODPOSTAL,
							 catalogo_afiliado_persona_ubigeo.CVEPAIS,
							 catalogo_afiliado_persona_ubigeo.DIRECCION,
							 catalogo_sucursal.DESCRIPCION   AS SUCURSAL,
							 catalogo_vendedor.NOMBRES AS VENDEDOR,
							 catalogo_canal_venta.DESCRIPCION	AS CANAL
							FROM
								".$this->db->catalogo.".catalogo_afiliado
							INNER JOIN ".$this->db->catalogo.".catalogo_afiliado_persona ON catalogo_afiliado_persona.IDAFILIADO = catalogo_afiliado.IDAFILIADO
							INNER JOIN ".$this->db->catalogo.".catalogo_cuenta ON catalogo_cuenta.IDCUENTA = catalogo_afiliado.IDCUENTA
							INNER JOIN ".$this->db->catalogo.".catalogo_programa ON catalogo_programa.IDPROGRAMA = catalogo_afiliado.IDPROGRAMA
							LEFT JOIN ".$this->db->catalogo.".catalogo_afiliado_persona_ubigeo ON catalogo_afiliado_persona_ubigeo.IDAFILIADO = catalogo_afiliado.IDAFILIADO
							LEFT JOIN ".$this->db->catalogo.".catalogo_afiliado_canalventa ON catalogo_afiliado_canalventa.IDAFILIADO = catalogo_afiliado.IDAFILIADO
							LEFT JOIN ".$this->db->catalogo.".catalogo_canal_venta ON catalogo_canal_venta.IDCANALVENTA = catalogo_afiliado_canalventa.IDCANALVENTA
							LEFT JOIN ".$this->db->catalogo.".catalogo_vendedor ON catalogo_vendedor.IDVENDEDOR = catalogo_afiliado_canalventa.IDVENDEDOR
							LEFT JOIN ".$this->db->catalogo.".catalogo_sucursal ON catalogo_sucursal.IDSUCURSAL = catalogo_afiliado_canalventa.IDSUCURSAL
							WHERE
								catalogo_afiliado.IDAFILIADO = '".$this->idafiliado."'";								
 //echo $Sqlinfo_afiliado;
		 	$result= $this->db->query($Sqlinfo_afiliado);
			while($reg= $result->fetch_object()){
				
				$datosAfiliado["IDAFILIADO"]=$reg->IDAFILIADO;
				$datosAfiliado["CVEAFILIADO"]=$reg->CVEAFILIADO;
				$datosAfiliado["IDCUENTA"]=$reg->IDCUENTA;
				$datosAfiliado["NOMBRECUENTA"]=$reg->NOMBRECUENTA;
				$datosAfiliado["IDPROGRAMA"]=$reg->IDPROGRAMA;
				$datosAfiliado["NOMBREPLAN"]=$reg->NOMBREPLAN;
				$datosAfiliado["FECHAINICIOVIGENCIA"]=$reg->FECHAINICIOVIGENCIA;
				$datosAfiliado["FECHAFINVIGENCIA"]=$reg->FECHAFINVIGENCIA;
				$datosAfiliado["FECHACANCELACION"]=$reg->FECHACANCELACION;
				$datosAfiliado["FECHACREACION"]=$reg->FECHACREACION;
				$datosAfiliado["STATUSASISTENCIA"]=$reg->STATUSASISTENCIA;
				$datosAfiliado["STATUSCOMERCIAL"]=$reg->STATUSCOMERCIAL;
				$datosAfiliado["AFILIADO_SISTEMA"]=$reg->AFILIADO_SISTEMA;
				$datosAfiliado["ARRMODALIDADPG"]=$reg->ARRMODALIDADPG;
				$datosAfiliado["NOMBRE"]=$reg->NOMBRE;			
				$datosAfiliado["APPATERNO"]=$reg->APPATERNO;			
				$datosAfiliado["APMATERNO"]=$reg->APMATERNO;			
				$datosAfiliado["NOMBREPERSONA"]=$reg->NOMBREPERSONA;			
				$datosAfiliado["EMAIL1"]=$reg->EMAIL1;			
				$datosAfiliado["EMAIL2"]=$reg->EMAIL2;
				$datosAfiliado["EMAILS"]=$reg->EMAILS;			
				$datosAfiliado["ESTADOCIVIL"]=$reg->ESTADOCIVIL;			
				$datosAfiliado["FECHANACIMIENTO"]=$reg->FECHANACIMIENTO;			
				$datosAfiliado["GENERO"]=$reg->GENERO;			
				$datosAfiliado["IDGRUPOSANGUINEO"]=$reg->IDGRUPOSANGUINEO;			
				$datosAfiliado["IDDOCUMENTO"]=$reg->IDDOCUMENTO;			
				$datosAfiliado["IDTIPODOCUMENTO"]=$reg->IDTIPODOCUMENTO;			
				$datosAfiliado["ID_SISTEMASALUD"]=$reg->ID_SISTEMASALUD;			
				$datosAfiliado["VIVECONQUIEN"]=$reg->VIVECONQUIEN;			
				$datosAfiliado["VIVESOLO"]=$reg->VIVESOLO;			
				$datosAfiliado["DIGITOVERIFICADOR"]=$reg->DIGITOVERIFICADOR;			
				$datosAfiliado["AFILIADO_VIP"]=$reg->AFILIADO_VIP;	
				$datosAfiliado["IDUBIGEO"]=$reg->IDUBIGEO;	
				$datosAfiliado["CODPOSTAL"]=$reg->CODPOSTAL;	
				$datosAfiliado["CVEPAIS"]=$reg->CVEPAIS;	
				$datosAfiliado["DIRECCION"]=$reg->DIRECCION;	
				$datosAfiliado["SUCURSAL"]=$reg->SUCURSAL;	
				$datosAfiliado["VENDEDOR"]=$reg->VENDEDOR;	
				$datosAfiliado["CANAL"]=$reg->CANAL;	
			}
			
			return $datosAfiliado;
        }
		
        public function personaAfiliadoTelefono(){
			
			$sqlTelefono="SELECT
							catalogo_afiliado_persona_telefono.NUMEROTELEFONO,
							catalogo_afiliado_persona_telefono.IDTIPOTELEFONO,
							catalogo_tipotelefono.DESCRIPCION,
							catalogo_afiliado_persona_telefono.CODIGOAREA,
							catalogo_afiliado_persona_telefono.EXTENSION,
							catalogo_afiliado_persona_telefono.IDTSP,
							catalogo_afiliado_persona_telefono.PRIORIDAD
						FROM
							".$this->db->catalogo.".catalogo_afiliado_persona_telefono
						INNER JOIN ".$this->db->catalogo.".catalogo_tipotelefono ON catalogo_tipotelefono.IDTIPOTELEFONO = catalogo_afiliado_persona_telefono.IDTIPOTELEFONO
						WHERE
							catalogo_afiliado_persona_telefono.IDAFILIADO = '".$this->idafiliado."'
						ORDER BY catalogo_afiliado_persona_telefono.PRIORIDAD";
					
					$i=0; $ii=0; $iii=0; $conta=0;
					$result_tel= $this->db->query($sqlTelefono);
					while($row= $result_tel->fetch_object()){
						
						$telefonosOrden[$conta]=$row->NUMEROTELEFONO."-".$row->CODIGOAREA."-".$row->EXTENSION;
						
						if($row->DESCRIPCION =="CELULAR"){							
							$datosTelefono[$row->DESCRIPCION.$i]=$row->NUMEROTELEFONO;							
							$i++;							
						} else if($row->DESCRIPCION =="FIJO"){
							$datosTelefono[$row->DESCRIPCION.$ii]=$row->NUMEROTELEFONO;							
							$ii++;							
						} else{
							$datosTelefono[$row->DESCRIPCION.$iii]=$row->NUMEROTELEFONO;							
							$iii++;							
						}
						
						$conta++;
					}
			
			return array($datosTelefono,$telefonosOrden);			
		}			
	} 		 

// fin  class asistencias
?>