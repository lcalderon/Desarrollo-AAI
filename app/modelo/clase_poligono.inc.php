<?
class poligono {
	var $idpoligono;
	var $nombre;
	var $zoommap;
	var $idusuariomod;
	var $vertices;



	var $con;

	function __construct() {
		$this->con = new DB_mysqli;
		
		$this->con->select_db($this->con->catalogo);
		return;
	}

	function leer($idpoligono){
		$sql="
		SELECT
			cp.IDPOLIGONO,cp.NOMBRE,cp.IDUSUARIOMOD,cp.ZOOMMAP,
			cvp.ORDEN,cvp.LATITUD,cvp.LONGITUD
		FROM
			catalogo_poligono cp,
			catalogo_vertices_poligono cvp
		WHERE
			cp.IDPOLIGONO=cvp.IDPOLIGONO
			AND cp.IDPOLIGONO ='$idpoligono'
			ORDER BY cvp.ORDEN ASC
		";
		$result= $this->con->query($sql);

		while($reg= $result->fetch_object()){
			$this->idpoligono= $reg->IDPOLIGONO;
			$this->nombre= trim($reg->NOMBRE);
			$this->zoommap= $reg->ZOOMMAP;
			$this->idusuariomod= $reg->IDUSUARIOMOD;
			$this->vertices[lat][$reg->ORDEN] = $reg->LATITUD;
			$this->vertices[lng][$reg->ORDEN] = $reg->LONGITUD;
		}
		return;
	}

	function grabar($form)
	{
		
		// DATOS PARA LA TABLA catalogo_poligono
		$poligono[IDPOLIGONO] = $form[IDPOLIGONO];
		$poligono[NOMBRE] =strtoupper(utf8_encode(trim($form[NOMBRE])));
		$poligono[ZOOMMAP] = $form[ZOOMMAP];
		$poligono[IDUSUARIOMOD] = $form[IDUSUARIOMOD];

		// INSERTA O ACTUALIZA EL POLIGONO
		if ($poligono[IDPOLIGONO]!='')
		{
			$this->con->update('catalogo_poligono',$poligono," where IDPOLIGONO ='$poligono[IDPOLIGONO]'");
			$this->idpoligono=$poligono[IDPOLIGONO];
		}
		else
		{
			$this->con->insert_reg('catalogo_poligono',$poligono);
			$this->idpoligono = $this->con->insert_id;
		}

		// BORRA LOS VERTICES DEL POLIGONO
		$this->borrar_vertices($this->idpoligono);

		// DATOS PARA LA TABLA 		catalogo_vertices_poligono
		$vertices[IDPOLIGONO]=$this->idpoligono;
		$vertices[IDUSUARIOMOD]=$form[IDUSUARIOMOD];
		for($i=0 ;$i<count($form[LATITUD]);$i++ )
		{
			$vertices[ORDEN] = $i+1;
			$vertices[LATITUD] = $form[LATITUD][$i];
			$vertices[LONGITUD] = $form[LONGITUD][$i];
			$this->con->insert_reg('catalogo_vertices_poligono',$vertices);
		}
		return;
	}

	function borrar_vertices($idpoligono)
	{
		$sql="
		 DELETE FROM catalogo_vertices_poligono
		 WHERE
		 IDPOLIGONO = '$idpoligono'
		";
		$this->con->query($sql);
		return;
	}


	function borrar_poligono($idpoligono){
		$sql="
		 DELETE FROM catalogo_poligono
		 WHERE
		 IDPOLIGONO = '$idpoligono'
		";
		//		echo $sql;
		$this->con->query($sql);
		$this->borrar_vertices($idpoligono);
		return;
	}


	function poligonos_predefinidos(){
		$sql="
		SELECT
			*
		FROM
			catalogo_poligono
		WHERE
			 NOMBRE<>''
		";
		$result=$this->con->query($sql);
		while ($reg=$result->fetch_object()){
			$poli_predefinidos[$reg->IDPOLIGONO] =$reg->NOMBRE;
		}
		return $poli_predefinidos;
	}


	//*********************** VERIFICA SI EL PUNTO ESTA DENTRO DEL POLIGONO ************************************//
	/*
	$point = array('lat'=>  ,'lng'=>);
	$vertices = array(
	array('lat'=>  ,'lng'=>),
	array('lat'=>  ,'lng'=>),
	array('lat'=>  ,'lng'=>)
	);



	*/

	

	function lee_vertices($idpoligono)
	{
		$sql="
		SELECT
			ORDEN,LATITUD,LONGITUD
		FROM
			catalogo_vertices_poligono
		WHERE
		IDPOLIGONO='$idpoligono'
		";
		
		$res = $this->con->query($sql);
		while ($reg=$res->fetch_object())
		{
			$arr[$reg->ORDEN]=array('lat'=>$reg->LATITUD,'lng'=>$reg->LONGITUD);
		}
		return $arr;

	}


}


function geoDistancia($lat1,$lon1,$lat2,$lon2,$unit='MIL') {

		// calculate miles
		$M =  69.09 * rad2deg(acos(sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lon1 - $lon2))));

		switch(strtoupper($unit))
		{
			case 'KM':
				// kilometers
				return $M * 1.609344;
				break;
			case 'MIL':
			default:
				// miles
				return $M;
				break;
		}

}


function pointInPolygon($point, $vertices, $pointOnVertex = true)
	{
		if ($pointOnVertex == true and pointOnVertex($point, $vertices) == true) 	return 1;
		$intersections = 0;
		$vertices_count = count($vertices);
		for ($i=1; $i < $vertices_count; $i++)
		{
			$vertex1 = $vertices[$i-1];
			$vertex2 = $vertices[$i];
			if ($vertex1['lng'] == $vertex2['lng'] and $vertex1['lng'] == $point['lng'] and $point['lat'] > min($vertex1['lat'], $vertex2['lat']) and $point['lat'] < max($vertex1['lat'], $vertex2['lat']))
			{ // Check if point is on an horizontal polygon boundary
				return "boundary";
			}
			if ($point['lng'] > min($vertex1['lng'], $vertex2['lng']) and $point['lng'] <= max($vertex1['lng'], $vertex2['lng']) and $point['lat'] <= max($vertex1['lat'], $vertex2['lat']) and $vertex1['lng'] != $vertex2['lng']) {
				$xinters = ($point['lng'] - $vertex1['lng']) * ($vertex2['lat'] - $vertex1['lat']) / ($vertex2['lng'] - $vertex1['lng']) + $vertex1['lat'];
				if ($xinters == $point['lat'])
				{ // Check if point is on the polygon boundary (other than horizontal)
					return "boundary";
				}
				if ($vertex1['lat'] == $vertex2['lat'] || $point['lat'] <= $xinters)
				{
					$intersections++;
				}
			}
		}

		if ($intersections % 2 != 0) {
			return 1;
		} else {
			return 0;
		}
	}

	function pointOnVertex($point, $vertices) {
		foreach($vertices as $vertex) {
			if ($point == $vertex) {
				return true;
			}
		}
	}


?>