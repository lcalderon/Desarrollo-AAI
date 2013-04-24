<?
class persona extends DB_mysqli {
	var $idpersona;
	var $idnombre;
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
	

	function carga_datos($idpersona){
		$bd=$this->catalogo;
		$this->select_db($bd);
		$sql=
		"select
				IDPERSONA,NOMBRE,APPATERNO,APMATERNO,IDTIPODOCUMENTO,
				IDDOCUMENTO, DIGITOVERIFICADOR,IDUBIGEO,OBSERVACIONES,
				EMAIL1,EMAIL2,EMAIL3, FECHANAC,GENERO,ESTADOCIVIL,PROFESION,
				YEAR(FECHANAC) ANIO_NAC, MONTH(FECHANAC) MES_NAC, DAY(FECHANAC) DIA_NAC
				
		 from 
			catalogo_persona
		 where 
			IDPERSONA = '$idpersona'
		";
		$result = $this->query($sql);
		while ($reg = $result->fetch_object()){
			$this->idpersona = $reg->IDPERSONA;
			$this->nombre = $reg->NOMBRE;
			$this->appaterno = $reg->APPATERNO;
			$this->apmaterno = $reg->APMATERNO;
			$this->idtipodocumento = $reg->IDTIPODOCUMENTO;
			$this->iddocumento = $reg->IDDOCUMENTO;
			$this->digitoverificador = $reg->DIGITOVERIFICADOR;
			$this->idubigeo = $reg->IDUBIGEO;
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
		
			// 	   CARGA DE LOS TELEFONOS DE LA PERSONA //
		$sql="
		SELECT 
			cpt.IDTELEFONO, cpt.PRIORIDAD,
			ct.IDTIPOTELEFONO,ct.CODIGOAREA,ct.NUMEROTELEFONO,ct.EXTENSION,ct.IDTSP
		FROM
			catalogo_persona_telefono cpt,
			catalogo_telefono ct
		WHERE
			cpt.IDPERSONA ='$idpersona'
			AND cpt.IDTELEFONO = ct.IDTELEFONO;
		";
		$resul=$this->query($sql);
		while ($reg = $resul->fetch_object()) {
			$this->telefonos[$reg->PRIORIDAD-1]= array( 'IDTIPOTELEFONO'=>$reg->IDTIPOTELEFONO,
			'CODIGOAREA' => $reg->CODIGOAREA,
			'NUMEROTELEFONO' => $reg->NUMEROTELEFONO,
			'EXTENSION' => $reg->EXTENSION,
			'IDTSP' => $reg->IDTSP);

		}

		return;
		
	} //fin de la funcion carga_datos


	function grabar($datos){
		$bd=$this->catalogo;
		$this->select_db($bd);
		
		$pers->NOMBRE = $datos[NOMBRE];
		$pers->APPATERNO = $datos[APPATERNO];
		$pers->APMATERNO = $datos[APMATERNO];
		$pers->CVEUSUARIO_MOD = $_SESSION[user];          //usuario que esta modificando;
		$pers->CVETIPODOCUMENTO = $datos[CVETIPODOCUMENTO];
		$pers->IDDOCUMENTO = $datos[IDDOCUMENTO];
		$pers->DIGITOVERIFICADOR = $datos[DIGITOVERIFICADOR];
		$pers->CVEUBIGEO = $datos[CVEUBIGEO];
		$pers->OBSERVACIONES = $datos[OBSERVACIONES];
		$pers->EMAIL1 = $datos[EMAIL1]; 					// Direccion del trabajo
		$pers->EMAIL2 = $datos[EMAIL2]; 					// Direccion personal
		$pers->EMAIL3 = $datos[EMAIL3]; 					// Direccion personal
		$pers->FECHANAC = $datos[FECHANAC];
		$pers->GENERO = $datos[GENERO];
		$pers->ESTADOCIVIL = $datos[ESTADOCIVIL];
		$pers->PROFESION = $datos[PROFESION];

		if ($datos[CVEPERSONA]==''){
			$xwhere=" where NOMBRE = '$pers->NOMBRE' and APPATERNO = '$pers->APPATERNO' and APMATERNO = '$pers->APMATERNO' and IDDOCUMENTO = '$pers->IDDOCUMENTO' ";
			if (!$this->exist('catalogo_persona','*',$xwhere))
			{
				$this->insert_reg('catalogo_persona',$pers);
				$datos[CVEPERSONA]=$this->con->insert_id;
			}
			else
			$this->update('catalogo_persona',$pers, $xwhere);
		}
		else
		$this->update('catalogo_persona',$pers, " Where CVEPERSONA = $datos[CVEPERSONA]");
	} // fin del metodo grabar
	
	
	
	
	
	

} // fin de la clase






?>