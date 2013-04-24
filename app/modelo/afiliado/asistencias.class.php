<?php

	class asistencias
	{
        public $idasistencia;
        public $db;

        public function __construct($idasistencia){
            
			$this->idasistencia= $idasistencia;
			$this->db = new DB_mysqli();
        }
		 
        public function informacionAsistencia(){
		
			$Sql_asistencia="SELECT
								expediente.IDEXPEDIENTE,
								asistencia.IDASISTENCIA,
								(
									SELECT
										CONCAT(
											expediente_usuario.FECHAHORA,
											expediente_usuario.IDUSUARIO
										)
									FROM
										".$this->db->temporal.".expediente_usuario
									WHERE
										expediente_usuario.IDEXPEDIENTE = asistencia.IDEXPEDIENTE
									AND expediente_usuario.ARRTIPOMOVEXP = 'REG'
								) AS FECHAUSUARIOEXP,
								catalogo_cuenta.NOMBRE AS NOMBRECUENTA,
								catalogo_programa.NOMBRE AS NOMBREPLAN,
								asistencia.ARRSTATUSASISTENCIA,
								asistencia.ARRAMBITO,
								asistencia.STATUSCALIDAD,
								asistencia.ARRCONDICIONSERVICIO,
								asistencia.ARRPRIORIDADATENCION,
								asistencia.ARRSTATUSENCUESTA,
								asistencia.EVALAUDITORIA,
								expediente.ARRSTATUSEXPEDIENTE,
								(
									SELECT
										GROUP_CONCAT(
											expediente_persona_telefono.NUMEROTELEFONO
										)
									FROM
										".$this->db->temporal.".expediente_persona
									INNER JOIN ".$this->db->temporal.".expediente_persona_telefono ON expediente_persona_telefono.IDPERSONA = expediente_persona.IDPERSONA
									WHERE
										expediente_persona.IDEXPEDIENTE = asistencia.IDEXPEDIENTE
									AND expediente_persona.ARRTIPOPERSONA = 'TITULAR'
								) AS TELEFONOS,
								IF(asistencia.IDSERVICIO >0,catalogo_servicio.DESCRIPCION,catalogo_programa_servicio.ETIQUETA) AS SERVICIO,
							(SELECT  CONCAT(expediente_persona.APPATERNO,' ',expediente_persona.APMATERNO,', ',expediente_persona.NOMBRE) FROM ".$this->db->temporal.".expediente_persona WHERE expediente_persona.IDEXPEDIENTE=asistencia.IDEXPEDIENTE AND expediente_persona.ARRTIPOPERSONA='TITULAR') AS NOBREAFILIADO,
								asistencia.IDETAPA
							FROM
								".$this->db->temporal.".asistencia
							INNER JOIN ".$this->db->catalogo.".catalogo_servicio ON catalogo_servicio.IDSERVICIO = asistencia.IDSERVICIO
							INNER JOIN ".$this->db->temporal.".expediente ON expediente.IDEXPEDIENTE = asistencia.IDEXPEDIENTE
							INNER JOIN ".$this->db->catalogo.".catalogo_cuenta ON catalogo_cuenta.IDCUENTA = asistencia.IDCUENTA
							INNER JOIN ".$this->db->catalogo.".catalogo_programa ON catalogo_programa.IDPROGRAMA = asistencia.IDPROGRAMA
							LEFT JOIN ".$this->db->catalogo.".catalogo_programa_servicio ON catalogo_programa_servicio.IDPROGRAMASERVICIO = asistencia.IDPROGRAMASERVICIO
							WHERE
								asistencia.IDASISTENCIA = '".$this->idasistencia."'";
 
		 	$result= $this->db->query($Sql_asistencia);
			while($reg= $result->fetch_object()){
				
				$datosAsistencia["IDEXPEDIENTE"]=$reg->IDEXPEDIENTE;
				$datosAsistencia["IDASISTENCIA"]=$reg->IDASISTENCIA;
				$datosAsistencia["FECHAUSUARIOEXP"]=$reg->FECHAUSUARIOEXP;
				$datosAsistencia["NOMBRECUENTA"]=$reg->NOMBRECUENTA;
				$datosAsistencia["NOMBREPLAN"]=$reg->NOMBREPLAN;
				$datosAsistencia["ARRSTATUSASISTENCIA"]=$reg->ARRSTATUSASISTENCIA;
				$datosAsistencia["ARRAMBITO"]=$reg->ARRAMBITO;
				$datosAsistencia["ARRCONDICIONSERVICIO"]=$reg->ARRCONDICIONSERVICIO;
				$datosAsistencia["ARRPRIORIDADATENCION"]=$reg->ARRPRIORIDADATENCION;
				$datosAsistencia["ARRSTATUSENCUESTA"]=$reg->ARRSTATUSENCUESTA;
				$datosAsistencia["EVALAUDITORIA"]=$reg->EVALAUDITORIA;
				$datosAsistencia["ARRSTATUSEXPEDIENTE"]=$reg->ARRSTATUSEXPEDIENTE;
				$datosAsistencia["TELEFONOS"]=$reg->TELEFONOS;			
				$datosAsistencia["SERVICIO"]=$reg->SERVICIO;			
				$datosAsistencia["NOBREAFILIADO"]=$reg->NOBREAFILIADO;			
				$datosAsistencia["STATUSCALIDAD"]=$reg->STATUSCALIDAD;			
				$datosAsistencia["IDETAPA"]=$reg->IDETAPA;			
			}
			
			return $datosAsistencia; 
        }
	} 		 

// fin  class asistencias
?>
