<?

class contacto extends DB_mysqli {
	var $idcontacto;
	var $idproveedor;
	var $nombre;
	var $appaterno;
	var $apmaterno;
	var $email1;
	var $email2;
	var $email3;
	var $fechanac;
	var $responsable;
	var $telefonos;

	function carga_datos($idcontacto)
	{
		$sql="select
				* 
			  from 
			 	$this->catalogo.catalogo_proveedor_contacto 
			  where IDCONTACTO ='$idcontacto'"
		;
		$result= $this->query($sql);
		while($reg = $result->fetch_object()){
			$this->idcontacto=$reg->IDCONTACTO;
			$this->idproveedor= $reg->IDPROVEEDOR;
			$this->nombre = $reg->NOMBRE;
			$this->appaterno = $reg->APPATERNO;
			$this->apmaterno = $reg->APMATERNO;
			$this->email1=$reg->EMAIL1;
			$this->email2=$reg->EMAIL2;
			$this->email3=$reg->EMAIL3;
			$this->fechanac=$reg->FECHANAC;
			$this->responsable = $reg->RESPONSABLE;

		}

		// carga de telefonos del contacto

		$sql="
SELECT
	cpct.IDCONTACTO, 
	cpct.CODIGOAREA,
	cpct.IDTIPOTELEFONO,
	cpct.NUMEROTELEFONO,
	cpct.EXTENSION,
	cpct.IDTSP,
	cpct.COMENTARIO,
	cpct.PRIORIDAD,
	cd.DESCRIPCION NOMBRECODIGOAREA,
	ctsp.DESCRIPCION NOMBRETSP,
	ctt.DESCRIPCION NOMBRETIPOTELEFONO
FROM
	$this->catalogo.catalogo_proveedor_contacto_telefono cpct
	LEFT JOIN $this->catalogo.catalogo_ddn cd ON cd.DDN = cpct.CODIGOAREA
	LEFT JOIN $this->catalogo.catalogo_tsp ctsp ON ctsp.IDTSP = cpct.IDTSP
	LEFT JOIN $this->catalogo.catalogo_tipotelefono	ctt ON ctt.IDTIPOTELEFONO = cpct.IDTIPOTELEFONO
WHERE
	cpct.IDCONTACTO='$idcontacto'
	ORDER BY cpct.PRIORIDAD;
	";
		$result= $this->query($sql);
		unset($this->telefonos);
		while($reg = $result->fetch_object())
		{
			if ($reg->NUMEROTELEFONO!='')
			{
				$this->telefonos[$reg->PRIORIDAD-1]= array(
				'IDTIPOTELEFONO'=>$reg->IDTIPOTELEFONO,
				'CODIGOAREA' => $reg->CODIGOAREA,
				'NUMEROTELEFONO' => $reg->NUMEROTELEFONO,
				'EXTENSION' => $reg->EXTENSION,
				'IDTSP' => $reg->IDTSP,
				'TELF_COMENTARIO'=>$reg->COMENTARIO,
				'NOMBRECODIGOAREA'=>$reg->NOMBRECODIGOAREA,
				'NOMBRETSP'=>$reg->NOMBRETSP,
				'NOMBRETIPOTELEFONO'=>$reg->NOMBRETIPOTELEFONO
				);
			}
		}
		return;
	}




}
?>