<?
class afiliado extends DB_mysqli {
	var $idafiliado;
	var $cveafiliado;
	var $arrorigentabla;
	var $programa;
	var $cuenta;
	var $idcanalventa;
	var $fechainiciovigencia;
	var $fechafinvigencia;
	var $statusasistencia;
	var $statuscomercial;

	
	// DATOS PERSONALES DEL AFILIADO
	var $appaterno;
	var $apmaterno;
	var $idtipodocumento;
	var $iddocumento;
	var $digitoverificador;
	var $idubigeo;
	var $observaciones;
	var $email1;
	var $email2;
	var $email3;
	var $fechnac;
	var $genero;
	var $estadocivil;
	var $profesion;
	
	var $telefonos;
	var $mes_nac;
	var $anio_nac;
	var $dia_nac;
	
	
	
	var $tabla;







	function carga_datos($idafiliado){
		$this->select_db($this->catalogo);
		$sql="
			SELECT 
				*
			FROM 
			  catalogo_afiliado
			WHERE
			 IDAFILIADO ='$idafiliado'
		";
	
		$result=$this->query($sql);
		while ($reg=$result->fetch_object()) {
			$this->idafiliado = $reg->IDAFILIADO;
			$this->cveafiliado = $reg->CVEAFILIADO;
			$this->arrorigentabla = $reg->ARRORIGENTABLA;
			
			$this->programa = new programa();
			$this->programa->carga_datos($reg->IDPROGRAMA);
			
			$this->cuenta = new cuenta();
			$this->cuenta->carga_datos($reg->IDCUENTA);
			
			$this->fechainiciovigencia = $reg->FECHAINICIOVIGENCIA;
			$this->fechafinvigencia = $reg->FECHAFINVIGENCIA;
			$this->statusasistencia = $reg->STATUSASISTENCIA;
			$this->statuscomercial = $reg->STATUSCOMERCIAL;
		}
		
		switch ($this->arrorigentabla){
			case 'PERSONAS':{
				$this->tabla='catalogo_afiliado_persona';
				break;
			}
		}//fin del switch
		
		// OBTENER DATOS PERSONALES DEL AFILIADO
		$sql="
		SELECT * 
		FROM 
			$this->catalogo.$this->tabla
		WHERE 
			IDAFILIADO = '$idafiliado'		
		";
		
		$result=$this->query($sql);
		while ($reg=$result->fetch_object()) {
			$this->nombre = $reg->NOMBRE;
			$this->appaterno = $reg->APPATERNO;
			$this->apmaterno = $reg->APMATERNO;
			$this->idtipodocumento = $reg->IDTIPODOCUMENTO;
			$this->iddocumento = $reg->IDDOCUMENTO;
			$this->digitoverificador = $reg->DIGITOVERIFICADOR;
			$this->observaciones = $reg->OBSERVACIONES;
			$this->email1= $reg->EMAIL1; 					// Direccion del trabajo
			$this->email2 = $reg->EMAIL2; 					// Direccion personal
			$this->email3 = $reg->EMAIL3; 					// Direccion personal
			$this->fechanac = $reg->FECHANAC;
			$this->genero = $reg->GENERO;
			$this->estadocivil = $reg->ESTADOCIVIL;
			$this->profesion = $reg->PROFESION;
			$this->dia_nac = $reg->DIA_NAC;
			$this->mes_nac = $reg->MES_NAC;
			$this->anio_nac = $reg->ANIO_NAC;
			}

		return;
	}


}  // fin de la clase afilia
?>