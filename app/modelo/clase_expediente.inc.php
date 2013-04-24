<?
class expediente extends DB_mysqli  {
	var $idexpediente;
	var $idafiliado;
	var $titular_afiliado;
	var $titular_persona;
	var $beneficiario;
	var $contacto;
	var $cuenta;
	var $programa;
	var $arrstatusexpediente;
	// ubigeo
	var $cvepais;
	var $cveentidad1;
	var $cveentidad2;
	var $cveentidad3;
	var $cveentidad4;
	var $cveentidad5;
	var $cveentidad6;
	var $cveentidad7;
	var $cvetipovia;
	var $descripcion;
	var $direccion;
	var $codpostal;
	var $calle;
	var $numero;
	var $latitud;
	var $longitud;
	var $referencia1;
	var $referencia2;

	var $observaciones;
	var $descripcionfisica;
	var $arrtipoautorizacion;
	var $numautorizacion;
	var $nomautorizacion;
	var $ocurrio;
	var $ani;
	var $piloto;

	function carga_datos($idexpediente){
		$sql="
			SELECT 
				*
			FROM
			 $this->temporal.expediente
			WHERE
				IDEXPEDIENTE = '$idexpediente'
			";

		$result = $this->query($sql);
		while ($reg= $result->fetch_object()) {
			$persona = new persona();
			$this->idexpediente = $reg->IDEXPEDIENTE;
			$this->cveafiliado = $reg->CVEAFILIADO;
			$this->idafiliado = $reg->IDAFILIADO;
			$this->cuenta = new cuenta();
			$this->cuenta->carga_datos($reg->IDCUENTA);

			$this->programa = new programa();
			$this->programa->carga_datos($reg->IDPROGRAMA);

			$this->arrstatusexpediente = $reg->ARRSTATUSEXPEDIENTE;


			$this->observaciones = $reg->OBSERVACIONES;
			$this->descripcionfisica = $reg->DESCRIPCIONFISICA;
			$this->arrtipoautorizacion = $reg->ARRTIPOAUTORIZACION;
			$this->numautorizacion = $reg->NUMAUTORIZACION;
			$this->nomautorizacion = $reg->NOMAUTORIZACION;
			$this->ocurrio = $reg->OCURRIO;
			$this->ani = $reg->ANI;
			$this->piloto = $reg->PILOTO;



			//  leer datos de ubigeo
			$sql="
			SELECT *
			FROM
				$this->temporal.expediente_ubigeo
			WHERE 
				IDEXPEDIENTE ='$idexpediente'	
			";
			$resul=$this->query($sql);
			while ($reg=$resul->fetch_object()) {
				$this->cvepais = $reg->CVEPAIS;
				$this->cveentidad1 = $reg->CVEENTIDAD1;
				$this->cveentidad2 = $reg->CVEENTIDAD2;
				$this->cveentidad3 = $reg->CVEENTIDAD3;
				$this->cveentidad4 = $reg->CVEENTIDAD4;
				$this->cveentidad5 = $reg->CVEENTIDAD5;
				$this->cveentidad6 = $reg->CVEENTIDAD6;
				$this->cveentidad7 = $reg->CVEENTIDAD7;
				$this->cvetipovia = $reg->CVETIPOVIA;
				$this->descripcion = $reg->DESCRIPCION;
				$this->direccion = $reg->DIRECCION;
				$this->codpostal = $reg->CODPOSTAL;
				$this->numero = $reg->NUMERO;
				$this->latitud = $reg->LATITUD;
				$this->longitud = $reg->LONGITUD;
				$this->referencia1 = $reg->REFERENCIA1;
				$this->referencia2 = $reg->REFERENCIA2;
			}
			// leer datos de personas
			$sql="
			SELECT * FROM $this->temporal.expediente_persona WHERE IDEXPEDIENTE='$idexpediente'
			";
			$result=$this->query($sql);
			while($reg=$result->fetch_object()){
				$this->personas[$reg->ARRTIPOPERSONA]= array(
				'NOMBRE'=>$reg->NOMBRE,
				'APPATERNO'=>$reg->APPATERNO,
				'APMATERNO'=>$reg->APMATERNO,
				'NOMBRE'=>$reg->NOMBRE,
				'IDTIPODOCUMENTO'=>$reg->IDTIPODOCUMENTO,
				'IDDOCUMENTO'=>$reg->IDDOCUMENTO,
				'IDPERSONA'=>$reg->IDPERSONA
				);
			}


		}
		return;
	}


	function leer_telf_persona($idpersona){
		$sql="
		SELECT 
			* 
		FROM
		 	$this->temporal.expediente_persona_telefono
		WHERE 
		 IDPERSONA='$idpersona'
		";
		$result=$this->query($sql);
		while ($reg = $result->fetch_object()) {
			$this->telefonos[$reg->PRIORIDAD-1]= array(
			'IDTIPOTELEFONO'=>$reg->IDTIPOTELEFONO,
			'CODIGOAREA' => $reg->CODIGOAREA,
			'NUMEROTELEFONO' => $reg->NUMEROTELEFONO,
			'EXTENSION' => $reg->EXTENSION,
			'IDTSP' => $reg->IDTSP);
		}

		return;
	}

}// fin de la clase



?>