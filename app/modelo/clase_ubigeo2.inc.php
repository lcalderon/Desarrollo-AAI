<?
class ubigeo extends DB_mysqli{

	
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
	var $numero;
	var $latitud;
	var $longitud;
	
	
	function grabar_ubigeo($form,$tabla,$campo)
	{
 
		$reg[CVEPAIS] = $form[CVEPAIS];
		$reg[CVETIPOVIA] = $form[CVETIPOVIA];
		$reg[CVEENTIDAD1] = ($form[CVEENTIDAD1]=='')?'0':$form[CVEENTIDAD1];
		$reg[CVEENTIDAD2] = ($form[CVEENTIDAD2]=='')?'0':$form[CVEENTIDAD2];
		$reg[CVEENTIDAD3] = ($form[CVEENTIDAD3]=='')?'0':$form[CVEENTIDAD3];
		$reg[CVEENTIDAD4] = ($form[CVEENTIDAD4]=='')?'0':$form[CVEENTIDAD4];
		$reg[CVEENTIDAD5] = ($form[CVEENTIDAD5]=='')?'0':$form[CVEENTIDAD5];
		$reg[CVEENTIDAD6] = ($form[CVEENTIDAD6]=='')?'0':$form[CVEENTIDAD6];
		$reg[CVEENTIDAD7] = ($form[CVEENTIDAD7]=='')?'0':$form[CVEENTIDAD7];
		$reg[DESCRIPCION] = $form[DESCRIPCION];
		$reg[DIRECCION] = $form[DIRECCION];
		$reg[CODPOSTAL] = $form[CODPOSTAL];
		$reg[CALLE] = $form[CALLE];
		$reg[NUMERO] = $form[NUMERO];
		$reg[CUADRA] = $form[CUADRA];
		$reg[LATITUD] = $form[LATITUD];
		$reg[LONGITUD] = $form[LONGITUD];
		$reg[REFERENCIA1] = $form[REFERENCIA1];
		$reg[REFERENCIA2] = $form[REFERENCIA2];

		
			if ($this->exist($tabla,$campo," where $campo ='$form[$campo]'"))
			 {
			
				$this->update($tabla,$reg," where $campo ='$form[$campo]'");
				return $form[$campo];
			 }
			else 
			 {
				$reg[$campo] = $form[$campo];
				$this->insert_reg($tabla,$reg);
				return $this->con->insert_id;			

			 }
	}
	
	function grabar($campo,$bd,$tabla,$form)
	{
		$reg[$campo]=$form[$campo];
		$reg[CVEPAIS] = $form[CVEPAIS];
		$reg[CVETIPOVIA] = $form[CVETIPOVIA];
		$reg[CVEENTIDAD1] = ($form[CVEENTIDAD1]=='')?'0':$form[CVEENTIDAD1];
		$reg[CVEENTIDAD2] = ($form[CVEENTIDAD2]=='')?'0':$form[CVEENTIDAD2];
		$reg[CVEENTIDAD3] = ($form[CVEENTIDAD3]=='')?'0':$form[CVEENTIDAD3];
		$reg[CVEENTIDAD4] = ($form[CVEENTIDAD4]=='')?'0':$form[CVEENTIDAD4];
		$reg[CVEENTIDAD5] = ($form[CVEENTIDAD5]=='')?'0':$form[CVEENTIDAD5];
		$reg[CVEENTIDAD6] = ($form[CVEENTIDAD6]=='')?'0':$form[CVEENTIDAD6];
		$reg[CVEENTIDAD7] = ($form[CVEENTIDAD7]=='')?'0':$form[CVEENTIDAD7];
		$reg[DESCRIPCION] = $form[DESCRIPCION];
		$reg[DIRECCION] = $form[DIRECCION];
		$reg[CODPOSTAL] = $form[CODPOSTAL];
		$reg[CALLE] = $form[CALLE];
		$reg[NUMERO] = $form[NUMERO];
		$reg[CUADRA] = $form[CUADRA];
		$reg[LATITUD] = $form[LATITUD];
		$reg[LONGITUD] = $form[LONGITUD];
		$reg[REFERENCIA1] = $form[REFERENCIA1];
		$reg[REFERENCIA2] = $form[REFERENCIA2];
		
		if ($this->exist($bd.'.'.$campo,$campo," where $campo ='$form[$campo]'")){
			
				$this->update($bd.'.'.$campo,$reg," where $campo ='$form[$campo]'");
		}
		else {
				$this->insert_reg($bd.'.'.$campo,$reg);
			
		
		}
//
//		if ($form[IDUBIGEO]=='')
//		{
//			$this->con->insert_reg('catalogo_ubigeo',$reg);
//			return $this->con->insert_id;
//		}
//		else {
//			$this->con->update('catalogo_ubigeo',$reg," where IDUBIGEO ='$form[IDUBIGEO]'");
//			return $form[IDUBIGEO];
//		}

	}

	function leer($campo,$bd,$tabla,$valor){
		$sql="
		SELECT
			*
		FROM
			$bd.$tabla
		WHERE
			$campo ='$valor'
		";
 
		$resul=$this->query($sql);
		while ($reg=$resul->fetch_object()) {
			//$this->{$campo} = $reg->{$campo};
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
		return;
	}





	function nombre_entidades($idubigeo)
	{
		$sql="
		select
			cu.CVEPAIS,cu.CVEENTIDAD1,cu.CVEENTIDAD2,cu.CVEENTIDAD3,cu.CVEENTIDAD4,cu.CVEENTIDAD5,CVEENTIDAD6,CVEENTIDAD7
		from
			catalogo_ubigeo cu
		where
			cu.IDUBIGEO=$idubigeo
		";

		$result = $this->con->query($sql);
		while ($reg = $result->fetch_object())
		{
			$array_ubicacion[0]=$reg->CVEPAIS;
			$array_ubicacion[1]=$reg->CVEENTIDAD1;
			$array_ubicacion[2]=$reg->CVEENTIDAD2;
			$array_ubicacion[3]=$reg->CVEENTIDAD3;
			$array_ubicacion[4]=$reg->CVEENTIDAD4;
			$array_ubicacion[5]=$reg->CVEENTIDAD5;
			$array_ubicacion[6]=$reg->CVEENTIDAD6;
			$array_ubicacion[7]=$reg->CVEENTIDAD7;
		}


		$sql="
		select
			if ($array_ubicacion[1] ='0','',DESCRIPCION) DESCRIPCION
		from
			catalogo_guiacalle
		where
			CVEENTIDAD1 = $array_ubicacion[1]
			AND CVEENTIDAD2 ='0'";

		$result = $this->con->query($sql);
		while ($reg = $result->fetch_object()) {
			$ubi[1] =  array ("$array_ubicacion[1]"=>utf8_encode($reg->DESCRIPCION));
		}


		$sql="
		select
			if ($array_ubicacion[2] ='0','',DESCRIPCION) DESCRIPCION
		from
			catalogo_guiacalle
		where
			CVEENTIDAD1 = $array_ubicacion[1]
			AND CVEENTIDAD2 = $array_ubicacion[2]
			AND CVEENTIDAD3 ='0'";
		$result = $this->con->query($sql);
		while ($reg= $result->fetch_object()) {
			$ubi[2] =  array ("$array_ubicacion[2]"=>utf8_encode($reg->DESCRIPCION));
		}

		$sql="
		select
			 if ($array_ubicacion[3] ='0','',DESCRIPCION) DESCRIPCION
		from
			catalogo_guiacalle
		where
			CVEENTIDAD1 = $array_ubicacion[1]
			AND CVEENTIDAD2 = $array_ubicacion[2]
			AND CVEENTIDAD3 = $array_ubicacion[3]
			AND CVEENTIDAD4 ='0'";
		$result = $this->con->query($sql);
		while ($reg= $result->fetch_object()) {
			$ubi[3] =  array ("$array_ubicacion[3]"=>utf8_encode($reg->DESCRIPCION));
		}

		$sql="
		select
		 if ($array_ubicacion[4] ='0','',DESCRIPCION) DESCRIPCION
		from
			catalogo_guiacalle
		where
			CVEENTIDAD1 = $array_ubicacion[1]
			AND CVEENTIDAD2 = $array_ubicacion[2]
			AND CVEENTIDAD3 = $array_ubicacion[3]
			AND CVEENTIDAD4 = $array_ubicacion[4]
			AND CVEENTIDAD5 ='0'";

		$result = $this->con->query($sql);
		while ($reg= $result->fetch_object()) {
			$ubi[4] =  array ("$array_ubicacion[4]"=>utf8_encode($reg->DESCRIPCION));
		}

		$sql="
		select
			if ($array_ubicacion[5] ='0','',DESCRIPCION) DESCRIPCION
		from
			catalogo_guiacalle
		where
			CVEENTIDAD1 = $array_ubicacion[1]
			AND CVEENTIDAD2 = $array_ubicacion[2]
			AND CVEENTIDAD3 = $array_ubicacion[3]
			AND CVEENTIDAD4 = $array_ubicacion[4]
			AND CVEENTIDAD5 = $array_ubicacion[5]
			AND CVEENTIDAD6 ='0'";
		$result = $this->con->query($sql);
		while ($reg= $result->fetch_object()) {
			$ubi[5] =  array ("$array_ubicacion[5]"=>utf8_encode($reg->DESCRIPCION));
		}

		$sql="
		select
			if ($array_ubicacion[6] ='0','',DESCRIPCION) DESCRIPCION
		from
			catalogo_guiacalle
		where
			CVEENTIDAD1 = $array_ubicacion[1]
			AND CVEENTIDAD2 = $array_ubicacion[2]
			AND CVEENTIDAD3 = $array_ubicacion[3]
			AND CVEENTIDAD4 = $array_ubicacion[4]
			AND CVEENTIDAD5 = $array_ubicacion[5]
			AND CVEENTIDAD6 = $array_ubicacion[6]
			AND CVEENTIDAD7 ='0'";
		$result = $this->con->query($sql);
		while ($reg= $result->fetch_object()) {
			$ubi[6] =  array ("$array_ubicacion[6]"=>utf8_encode($reg->DESCRIPCION));
		}

		$sql="
		select
			if ($array_ubicacion[7] ='0','',DESCRIPCION) DESCRIPCION
		from
			catalogo_guiacalle
		where
			CVEENTIDAD1 = $array_ubicacion[1]
			AND CVEENTIDAD2 = $array_ubicacion[2]
			AND CVEENTIDAD3 = $array_ubicacion[3]
			AND CVEENTIDAD4 = $array_ubicacion[4]
			AND CVEENTIDAD5 = $array_ubicacion[5]
			AND CVEENTIDAD6 = $array_ubicacion[6]
			AND CVEENTIDAD7 = $array_ubicacion[7]";

		$result = $this->con->query($sql);
		while ($reg= $result->fetch_object()) {
			$ubi[7] =  array ("$array_ubicacion[7]"=>utf8_encode($reg->DESCRIPCION));
		}


		$sql="
		SELECT
			NOMBRE
		FROM
			catalogo_pais
		WHERE
			IDPAIS = '$array_ubicacion[0]'
		";

		$result = $this->con->query($sql);
		while ($reg= $result->fetch_object()) {
			$ubi[0] =  array ("$array_ubicacion[0]"=>utf8_encode($reg->NOMBRE));
		}

		$sql="
		select
		  DIRECCION,CODPOSTAL
		from
			catalogo_ubigeo
		where
		IDUBIGEO ='$idubigeo'
			";
		$result = $this->con->query($sql);
		while ($reg= $result->fetch_object()) {
			$ubi[8] = array("DIRECCION"=>utf8_encode($reg->DIRECCION));
			$ubi[9] = array("CODPOSTAL"=>utf8_encode($reg->CODPOSTAL));
		}




		return $ubi;
	}

} // fin de la clase ubigeo

?>