<?



class proveedor extends DB_mysqli {
	var $idproveedor;
	var $nombrefiscal;
	var $nombrecomercial;
	var $idtipodocumento;
	var $iddocumento;
	var $email1;
	var $email2;
	var $email3;
	var $telefonos;
	var $contactos;

	// datos de ubigeo
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
	var $referencia1;
	var $referencia2;

	var $activo;
	var $interno;
	var $observaciones;
	var $arrevalranking;
	var $cde;
	var $skill;
	var $evalfidelidad;
	var $evalinfraestructura;
	var $evalsatisfaccion;




	var $idmoneda;
	var $brsch;
	var $fdgrv;
	var $zterm;
	var $mwskz;
	var $parvo;
	var $pavip;
	var $anio;
	var $mes;
	var $dia;

	var $servicios;
	var $obs_servicio;
	var $sociedades;

	var $horario;



	// CARGA LOS DATOS DEL OBJETO PROVEEDOR //
	function carga_datos($idproveedor)
	{

		// carga los datos generales
		$sql="
		SELECT
			cp.IDPROVEEDOR, cp.NOMBREFISCAL, cp.NOMBRECOMERCIAL, 
			cp.IDTIPODOCUMENTO, cp.IDDOCUMENTO, cp.EMAIL1, cp.EMAIL2, cp.EMAIL3,
			cp.BRSCH, cp.FDGRV, cp.ZTERM, cp.MWSKZ, cp.PARVO, cp.PAVIP,
			cp.ACTIVO,cp.INTERNO,
			cp.ARREVALRANKING,
			cp.CDE,
			cp.SKILL,
			cp.EVALFIDELIDAD,
			cp.EVALINFRAESTRUCTURA,
			cp.EVALSATISFACCION,
			cp.IDMONEDA,
			cp.OBSERVACIONES,
			YEAR(cp.FECHAINICIOACTIVIDADES) ANIO,
			MONTH(cp.FECHAINICIOACTIVIDADES) MES, 
			DAY(cp.FECHAINICIOACTIVIDADES) DIA
		FROM
		$this->catalogo.catalogo_proveedor cp
		WHERE
			IDPROVEEDOR='$idproveedor'
		";

		$result=$this->query($sql);
		while ($reg = $result->fetch_object()) {
			$this->idproveedor= $reg->IDPROVEEDOR;
			$this->nombrefiscal= $reg->NOMBREFISCAL;
			$this->nombrecomercial = $reg->NOMBRECOMERCIAL;
			$this->idtipodocumento = $reg->IDTIPODOCUMENTO;
			$this->iddocumento = $reg->IDDOCUMENTO;
			$this->idubigeo = $reg->IDUBIGEO;
			$this->activo = $reg->ACTIVO;
			$this->interno = $reg->INTERNO;
			$this->arrevalranking = $reg->ARREVALRANKING;
			$this->cde = $reg->CDE;
			$this->skill = $reg->SKILL;
			$this->evalfidelidad = $reg->EVALFIDELIDAD;
			$this->evalinfraestructura = $reg->EVALINFRAESTRUCTURA;
			$this->evalsatisfaccion = $reg->EVALSATISFACCION;
			$this->moneda = new moneda();
			$this->moneda->carga_datos($reg->IDMONEDA);
			$this->observaciones = $reg->OBSERVACIONES;

			$this->email1 = $reg->EMAIL1;
			$this->email2 = $reg->EMAIL2;
			$this->email3 = $reg->EMAIL3;
			$this->brsch = $reg->BRSCH;
			$this->fdgrv = $reg->FDGRV;
			$this->zterm = $reg->ZTERM;
			$this->mwskz = $reg->MWSKZ;
			$this->parvo = $reg->PARVO;
			$this->pavip = $reg->PAVIP;
			$this->anio = $reg->ANIO;
			$this->mes = $reg->MES;
			$this->dia = $reg->DIA;

		}

		// carga los datos de ubigeo
		$sql="
		select 
			* 
		from
		$this->catalogo.catalogo_proveedor_ubigeo
		where
			IDPROVEEDOR='$idproveedor'
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

		// 	   CARGA DE LOS TELEFONOS DEL PROVEEDOR //
		$sql="
SELECT 
	cpt.IDPROVEEDOR, 
	cpt.CODIGOAREA,
	cpt.IDTIPOTELEFONO,
	cpt.NUMEROTELEFONO,
	cpt.EXTENSION,
	cpt.IDTSP,
	cpt.COMENTARIO,
	cpt.PRIORIDAD,
	cd.DESCRIPCION NOMBRECODIGOAREA,
	ctsp.DESCRIPCION NOMBRETSP,
	ctt.DESCRIPCION NOMBRETIPOTELEFONO
FROM
	catalogo_proveedor_telefono cpt
	LEFT JOIN catalogo_ddn cd ON cd.DDN = cpt.CODIGOAREA
	LEFT JOIN catalogo_tsp ctsp ON ctsp.IDTSP = cpt.IDTSP
	LEFT JOIN catalogo_tipotelefono	ctt ON ctt.IDTIPOTELEFONO = cpt.IDTIPOTELEFONO
WHERE
	cpt.IDPROVEEDOR ='$idproveedor'
	ORDER BY cpt.PRIORIDAD;
		";
		$resul=$this->query($sql);
		unset($this->telefonos);
		while ($reg = $resul->fetch_object())
		{
			if ($reg->NUMEROTELEFONO!='')
			{
				$this->telefonos[$reg->PRIORIDAD-1]= array(
				'IDTIPOTELEFONO'=>$reg->IDTIPOTELEFONO,
				'CODIGOAREA' => $reg->CODIGOAREA,
				'NUMEROTELEFONO' => strip_tags($reg->NUMEROTELEFONO),
				'EXTENSION' => $reg->EXTENSION,
				'IDTSP' => $reg->IDTSP,
				'TELF_COMENTARIO'=>$reg->COMENTARIO,
				'NOMBRECODIGOAREA'=>$reg->NOMBRECODIGOAREA,
				'NOMBRETSP'=>$reg->NOMBRETSP,
				'NOMBRETIPOTELEFONO'=>$reg->NOMBRETIPOTELEFONO

				);
			}
		}

		//    CARGA DE LOS SERVICIOS DEL PROVEEDOR //
		$sql="
		SELECT 
			IDSERVICIO
		FROM
			catalogo_proveedor_servicio
		WHERE
			IDPROVEEDOR='$idproveedor'
		";
		$resul=$this->query($sql);
		while ($reg = $resul->fetch_object()) {
			$this->servicios[]=$reg->IDSERVICIO;

		}

		//    CARGA DE SOCIEDADES DEL PROVEEDOR
		$sql="
		SELECT 
			IDSOCIEDAD
		FROM
			catalogo_proveedor_sociedad
		WHERE
			IDPROVEEDOR='$idproveedor'
		";
		$resul=$this->query($sql);
		while ($reg = $resul->fetch_object()) {
			$this->sociedades[$reg->IDSOCIEDAD]=$reg->IDSOCIEDAD;

		}

		//   CARGA DE CONTACTOS //
		$sql="
		SELECT * 
			FROM $this->catalogo.catalogo_proveedor_contacto
		WHERE	
			IDPROVEEDOR ='$idproveedor'
			ORDER BY RESPONSABLE DESC
		";
		$resul=$this->query($sql);
		while ($reg = $resul->fetch_object()) {
			$this->contactos[$reg->IDCONTACTO]=$reg->APPATERNO.' '.$reg->APMATERNO.' '.$reg->NOMBRE;
		}
		return;
	}   // fin del metodo de carga_datos //


	// LEE LOS TELEFONOS DE UN PROVEEDOR //
	function leer_telefonos($idproveedor){
		$sql="
		SELECT
			cpt.IDPROVEEDOR,ctsp.DESCRIPCION,
			cpt.CODIGOAREA,
			cpt.NUMEROTELEFONO
		FROM
		$this->catalogo.catalogo_proveedor_telefono cpt,
		$this->catalogo.catalogo_tsp ctsp
		WHERE
			cpt.IDTSP = ctsp.IDTSP
			AND cpt.IDPROVEEDOR='$idproveedor'
			";
		$result = $this->query($sql);
		while($reg= $result->fetch_object())
		{
			$telef[$reg->IDTELEFONO] = array(
			'TSP'=>$reg->DESCRIPCION,
			'CODIGOAREA'=>$reg->CODIGOAREA,
			'NUMEROTELEFONO'=>$reg->NUMEROTELEFONO
			);
		}
		return $telef;
	}

	// LEE EL HORARIO DEFINIDO PARA UN PROVEEDOR //
	function leer_horario($idproveedor){
		$sql="SELECT * FROM catalogo_proveedor_horario WHERE IDPROVEEDOR = '$idproveedor'";
		//		echo $sql;
		$result = $this->query($sql);

		while ($reg = $result->fetch_object())
		{
			$this->horario = array(
			'HORAINICIO'=>$reg->HORAINICIO,
			'HORAFINAL'=>$reg->HORAFINAL,
			'DOMINGO'=>$reg->DOMINGO,
			'LUNES'=>$reg->LUNES,
			'MARTES'=>$reg->MARTES,
			'MIERCOLES'=>$reg->MIERCOLES,
			'JUEVES'=>$reg->JUEVES,
			'VIERNES'=>$reg->VIERNES,
			'SABADO'=>$reg->SABADO,
			);
		}
		return;
	}

	//   LEE LOS COSTOS NEGOCIADOS DE UN SERVICIO PARA UN PROVEEDOR //
	function leer_prov_serv_cost($idproveedor,$idservicio){
		$sql="
		SELECT
			*
		FROM
		$this->catalogo.catalogo_proveedor_servicio_costo_negociado
		WHERE
			IDPROVEEDOR='$idproveedor'
			AND IDSERVICIO='$idservicio'
		";

		$result= $this->query($sql);
		while ($reg=$result->fetch_object()){
			$this->costos[$reg->IDCOSTO] = array(
			'MONTOLOCAL'=>$reg->MONTOLOCAL,
			'MONTOINTERMEDIO'=>$reg->MONTOINTERMEDIO,
			'MONTOFORANEO'=>$reg->MONTOFORANEO,
			'PLUSNOCTURNO'=>$reg->PLUSNOCTURNO,
			'PLUSFESTIVO'=>$reg->PLUSFESTIVO,
			'UNIDAD'=>$reg->UNIDAD,
			'IDMEDIDA'=>$reg->IDMEDIDA);
		}

		return;
	}

	function leer_observacion_servicio($idservicio){
		$sql="
		 SELECT
		 	IDSERVICIO, 
		 	OBSERVACIONES
		 FROM 
		 $this->catalogo.catalogo_proveedor_servicio_observacion
		 WHERE
			IDPROVEEDOR='$this->idproveedor'
			AND IDSERVICIO='$idservicio'
		";
		 //		echo $sql;
		 $result= $this->query($sql);
		 while ($reg=$result->fetch_object()){
		 	$this->obs_servicio = $reg->OBSERVACIONES;
		 }
		 return;
	}

	//   GRABA UN PROVEEDOR //
	function grabar($datos)
	{
		if (($datos[IDPROVEEDOR]=='') && ($this->exist("$this->catalogo.catalogo_proveedor",'IDPROVEEDOR',"  WHERE NOMBREFISCAL = '$datos[NOMBREFISCAL]'" )))
		{
			return 0;
		}
		else
		{
			$reg_proveedor[IDPROVEEDOR] = $datos[IDPROVEEDOR];
			$reg_proveedor[NOMBREFISCAL] = trim(strtoupper($datos[NOMBREFISCAL]));
			$reg_proveedor[NOMBRECOMERCIAL]  = trim(strtoupper($datos[NOMBRECOMERCIAL]));
			$reg_proveedor[IDTIPODOCUMENTO] = $datos[IDTIPODOCUMENTO];
			$reg_proveedor[IDDOCUMENTO] = trim($datos[IDDOCUMENTO]);
			$reg_proveedor[EMAIL1] = trim(strtolower($datos[EMAIL1]));
			$reg_proveedor[EMAIL2] = trim(strtolower($datos[EMAIL2]));
			$reg_proveedor[EMAIL3] = trim(strtolower($datos[EMAIL3]));
			$reg_proveedor[ACTIVO] = $datos[ACTIVO];
			$reg_proveedor[INTERNO] = $datos[INTERNO];

			if ($datos[ARREVALRANKING]=='SKILL'){
				$reg_proveedor[ARREVALRANKING]=$datos[ARREVALRANKING];
				$reg_proveedor[SKILL]= $datos[SKILL];
			}
			else{
				$reg_proveedor[ARREVALRANKING]='CDE';
			}
			$reg_proveedor[EVALFIDELIDAD]=$datos[EVALFIDELIDAD];
			$reg_proveedor[EVALINFRAESTRUCTURA]=$datos[EVALINFRAESTRUCTURA];
			$reg_proveedor[IDMONEDA]=$datos[IDMONEDA];
			$reg_proveedor[OBSERVACIONES]=$datos[OBSERVACIONES];

			$reg_proveedor[BRSCH] = $datos[BRSCH];
			$reg_proveedor[FDGRV] = $datos[FDGRV];
			$reg_proveedor[ZTERM] = $datos[ZTERM];
			$reg_proveedor[MWSKZ] = $datos[MWSKZ];
			$reg_proveedor[PARVO] = $datos[PARVO];
			$reg_proveedor[PAVIP] = $datos[PAVIP];
			$reg_proveedor[FECHAINICIOACTIVIDADES]= trim($datos[ANIO].'-'.$datos[MES].'-'.$datos[DIA]);

			$prov_ubigeo[CVEPAIS]= $datos[CVEPAIS];
			$prov_ubigeo[CVEENTIDAD1]= $datos[CVEENTIDAD1];
			$prov_ubigeo[CVEENTIDAD2]= $datos[CVEENTIDAD2];
			$prov_ubigeo[CVEENTIDAD3]= $datos[CVEENTIDAD3];
			$prov_ubigeo[CVEENTIDAD4]= $datos[CVEENTIDAD4];
			$prov_ubigeo[CVEENTIDAD5]= $datos[CVEENTIDAD5];
			$prov_ubigeo[CVEENTIDAD6]= $datos[CVEENTIDAD6];
			$prov_ubigeo[CVEENTIDAD7]= $datos[CVEENTIDAD7];
			$prov_ubigeo[CVETIPOVIA]= $datos[CVETIPOVIA];
			$prov_ubigeo[DIRECCION]= $datos[DIRECCION];
			$prov_ubigeo[NUMERO]= $datos[NUMERO];
			$prov_ubigeo[CODPOSTAL] = $datos[CODPOSTAL];
			$prov_ubigeo[LATITUD] = $datos[LATITUD];
			$prov_ubigeo[LONGITUD] = $datos[LONGITUD];
			$prov_ubigeo[IDUSUARIOMOD]= $datos[IDUSUARIOMOD];
			$prov_ubigeo[REFERENCIA1]= $datos[REFERENCIA1];
			$prov_ubigeo[REFERENCIA2]= $datos[REFERENCIA2];

			$datos_telf[CODIGOAREA] = $datos[CODIGOAREA];
			$datos_telf[IDTIPOTELEFONO] = $datos[IDTIPOTELEFONO];
			$datos_telf[NUMEROTELEFONO] = $datos[NUMEROTELEFONO];
			$datos_telf[EXTENSION] = $datos[EXTENSION];
			$datos_telf[IDTSP] = $datos[IDTSP];
			$datos_telf[COMENTARIO]=$datos[TELF_COMENTARIO];
			$datos_telf[IDUSUARIOMOD] = $datos[IDUSUARIOMOD];

			$this->borrar_telefono($datos[IDPROVEEDOR]); // BORRA LOS PROVEEDORES

			if ( $datos[IDPROVEEDOR]=='')
			{

				$this->insert_reg("$this->catalogo.catalogo_proveedor",$reg_proveedor);
				$datos[IDPROVEEDOR] = $this->reg_id(); //OBTIENE EL ID DEL NUEVO PROVEEDOR
				$prov_ubigeo[IDPROVEEDOR]=$datos[IDPROVEEDOR];
				$prov_horario[IDPROVEEDOR]=$datos[IDPROVEEDOR];
				$this->insert_reg("$this->catalogo.catalogo_proveedor_ubigeo",$prov_ubigeo);
				$this->insert_reg("$this->catalogo.catalogo_proveedor_horario",$prov_horario);
			}
			else{

				$this->update("$this->catalogo.catalogo_proveedor",$reg_proveedor," where IDPROVEEDOR = '$datos[IDPROVEEDOR]'");
				$this->update("$this->catalogo.catalogo_proveedor_ubigeo",$prov_ubigeo," where IDPROVEEDOR = '$datos[IDPROVEEDOR]'");
			}

			if ($datos[IDPROVEEDOR]!='')
			{
				for($i=0;$i<count($datos_telf[NUMEROTELEFONO]);$i++)
				{
					$telf[IDPROVEEDOR] = $datos[IDPROVEEDOR];
					$telf[CODIGOAREA]=$datos_telf[CODIGOAREA][$i];
					$telf[IDTIPOTELEFONO]=$datos_telf[IDTIPOTELEFONO][$i];
					$telf[NUMEROTELEFONO]=$datos_telf[NUMEROTELEFONO][$i];
					$telf[EXTENSION]=$datos_telf[EXTENSION][$i];
					$telf[IDTSP]=$datos_telf[IDTSP][$i];
					$telf[COMENTARIO]=$datos_telf[COMENTARIO][$i];
					$telf[IDUSUARIOMOD] = $datos_telf[IDUSUARIOMOD];
					$telf[PRIORIDAD]=$i+1;

					$this->insert_reg("$this->catalogo.catalogo_proveedor_telefono",$telf);

				}
			}

			$datos_sociedad = $datos[IDSOCIEDAD];


			// GRABA LOS SERVICIOS DEL PROVEEDOR //
			$sql="delete from catalogo_proveedor_servicio where IDPROVEEDOR='$datos[IDPROVEEDOR]'";
			$this->query($sql);

			$nueva_prioridad=1;

			foreach ($datos[CHECKSERVICIO] as $servi)
			{
				$servicio[IDSERVICIO] = $servi;
				$servicio[IDPROVEEDOR] = $datos[IDPROVEEDOR];
				$servicio[PRIORIDAD] = $nueva_prioridad;
				$servicio[IDUSUARIOMOD] = $datos[IDUSUARIOMOD];

				if ($servi!='')
				{
					$this->insert_reg('catalogo_proveedor_servicio',$servicio);
					$nueva_prioridad++;
				}

			}

			// GRABA LAS SOCIEDADES DEL PROVEEDOR
			$sql="delete from catalogo_proveedor_sociedad where IDPROVEEDOR='$datos[IDPROVEEDOR]'";
			$this->query($sql);
			foreach ($datos[IDSOCIEDAD] as $soc)
			{
				$sociedad[IDSOCIEDAD]   = $soc;
				$sociedad[IDPROVEEDOR]  = $datos[IDPROVEEDOR];
				$sociedad[IDUSUARIOMOD] = $datos[IDUSUARIOMOD];
				$this->insert_reg('catalogo_proveedor_sociedad',$sociedad);
			}
			return $datos[IDPROVEEDOR];
		}
		return $datos[IDPROVEEDOR];
	}// fin del metodo grabar

	// GRABA EL HORARIO PARA UN PROVEEDOR //
	function grabar_horario($datos){
		$registro[IDPROVEEDOR] = $datos[IDPROVEEDOR];
		$registro[IDUSUARIOMOD]= $datos[IDUSUARIOMOD];
		$registro[HORAINICIO]=$datos[HORAINICIO];
		$registro[HORAFINAL]=$datos[HORAFINAL];
		$registro[DOMINGO]=($datos[DOMINGO]=='on')?1:0;
		$registro[LUNES]=($datos[LUNES])?1:0;
		$registro[MARTES]=($datos[MARTES])?1:0;
		$registro[MIERCOLES]=($datos[MIERCOLES])?1:0;
		$registro[JUEVES]=($datos[JUEVES])?1:0;
		$registro[VIERNES]=($datos[VIERNES])?1:0;
		$registro[SABADO]=($datos[SABADO])?1:0;


		if ($this->exist('catalogo_proveedor_horario',IDPROVEEDOR," WHERE IDPROVEEDOR = '$registro[IDPROVEEDOR]'"))
		{
			$this->update('catalogo_proveedor_horario',$registro," WHERE IDPROVEEDOR = '$registro[IDPROVEEDOR]'");
		}
		else {
			$this->insert_reg('catalogo_proveedor_horario',$registro);

		}
	}

	// GRABA LOS COSTOS NEGOCIADOS //
	function grabar_prov_serv_cost($datos)
	{
		$this->borrar_prov_serv_cost($datos[IDPROVEEDOR],$datos[IDSERVICIO]);
		$reg_prov_serv_cost[IDPROVEEDOR]=$datos[IDPROVEEDOR];
		$reg_prov_serv_cost[IDSERVICIO]=$datos[IDSERVICIO];
		$reg_prov_serv_cost[IDUSUARIOMOD]=$datos[IDUSUARIOMOD];
		$reg_prov_serv_cost[OBSERVACIONES]=$datos[OBSERVACIONES];

		//		var_dump($reg_prov_serv_cost);
		$this->insert_update("$this->catalogo.catalogo_proveedor_servicio",$reg_prov_serv_cost);


		$reg_prov_serv_obser[IDPROVEEDOR]=$datos[IDPROVEEDOR];
		$reg_prov_serv_obser[IDSERVICIO]=$datos[IDSERVICIO];
		$reg_prov_serv_obser[IDUSUARIOMOD]=$datos[IDUSUARIOMOD];
		$reg_prov_serv_obser[OBSERVACIONES]=$datos[OBSERVACIONES];
		$this->insert_update("$this->catalogo.catalogo_proveedor_servicio_observacion",$reg_prov_serv_obser);

		unset($reg_prov_serv_cost[OBSERVACIONES]);

		for($i=0;$i<=50;$i++ )
		{
			if ($datos[MONTOLOCAL][$i]!='')
			{
				$reg_prov_serv_cost[IDCOSTO]=$datos[IDCOSTO][$i];
				$reg_prov_serv_cost[UNIDAD]=$datos[UNIDAD][$i];
				$reg_prov_serv_cost[IDMEDIDA] = $datos[IDMEDIDA][$i];
				$reg_prov_serv_cost[MONTOLOCAL]=$datos[MONTOLOCAL][$i];
				$reg_prov_serv_cost[MONTOINTERMEDIO]=$datos[MONTOINTERMEDIO][$i];
				$reg_prov_serv_cost[MONTOFORANEO]=$datos[MONTOFORANEO][$i];
				$reg_prov_serv_cost[PLUSNOCTURNO]=$datos[PLUSNOCTURNO][$i];
				$reg_prov_serv_cost[PLUSFESTIVO]=$datos[PLUSFESTIVO][$i];
				$this->insert_reg("$this->catalogo.catalogo_proveedor_servicio_costo_negociado",$reg_prov_serv_cost);
			}
		}
		return;
	}

	// GRABA LA EXPERIENCIA  DEL PROVEEDOR//
	function grabar_experiencia($datos)
	{

		if ($this->exist("$this->catalogo.catalogo_proveedor_experiencia",'EMPRESAREFERENCIA'," WHERE EMPRESAREFERENCIA ='$datos[EMPRESAREFERENCIA]'"))
		{
			$this->update("$this->catalogo.catalogo_proveedor_experiencia",$datos, "  WHERE EMPRESAREFERENCIA ='$datos[EMPRESAREFERENCIA]' ");
		}
		else {
			$this->insert_reg("$this->catalogo.catalogo_proveedor_experiencia", $datos);
		}
		return;
	}

	//  GRABA CONTACTO DEL PROVEEDOR//
	function grabar_contacto($datos)
	{

		$contacto[IDPROVEEDOR]= $datos[IDPROVEEDOR];
		$contacto[IDCONTACTO]=$datos[IDCONTACTO];
		$contacto[IDUSUARIOMOD]= $datos[IDUSUARIOMOD];
		$contacto[NOMBRE]= strtoupper(trim(($datos[NOMBRE])));
		$contacto[APPATERNO]=strtoupper(trim(($datos[APPATERNO])));
		$contacto[APMATERNO]=strtoupper(trim(($datos[APMATERNO])));
		$contacto[EMAIL1]= trim($datos[EMAIL1]);
		$contacto[EMAIL2]= trim($datos[EMAIL2]);
		$contacto[EMAIL3]= trim($datos[EMAIL3]);
		$contacto[RESPONSABLE]=($datos[RESPONSABLE]=='on')?1:0;
		$contacto[FECHANAC]= ($datos[ANIO]==''?'0000':$datos[ANIO]).'-'.($datos[MES]==''?'00':$datos[MES]).'-'.($datos[DIA]==''?'00':$datos[DIA]);

		//		$contacto_telf[CODIGOAREA]=$datos[CODIGOAREA];
		//		$contacto_telf[NUMEROTELEFONO]=$datos[NUMEROTELEFONO];
		//		$contacto_telf[EXTENSION]=$datos[EXTENSION];
		//		$contacto_telf[IDTIPOTELEFONO]=$datos[IDTIPOTELEFONO];
		//		$contacto_telf[COMENTARIO]=$datos[TELF_COMENTARIO];
		//		$contacto_telf[IDTSP]=$datos[IDTSP];


		$this->insert_update("$this->catalogo.catalogo_proveedor_contacto",$contacto);
		if ($this->reg_id()) $datos[IDCONTACTO] = $this->reg_id();

		$this->borrar_telefono_contacto($datos[IDCONTACTO]); // BORRA LOS TELEFONOS DE CONTACTO

		if ($datos[IDCONTACTO]!=''){
			for($i=0;$i<=count($datos[NUMEROTELEFONO]);$i++)
			{
				$telf[IDCONTACTO] = $datos[IDCONTACTO];
				$telf[CODIGOAREA]=$datos[CODIGOAREA][$i];
				$telf[IDTIPOTELEFONO]=$datos[IDTIPOTELEFONO][$i];
				$telf[NUMEROTELEFONO]=$datos[NUMEROTELEFONO][$i];
				$telf[EXTENSION]=$datos[EXTENSION][$i];
				$telf[IDTSP]=$datos[IDTSP][$i];
				$telf[COMENTARIO]=$datos[TELF_COMENTARIO][$i];
				$telf[IDUSUARIOMOD] = $datos[IDUSUARIOMOD];
				$telf[PRIORIDAD]=$i+1;

				$this->insert_reg("$this->catalogo.catalogo_proveedor_contacto_telefono",$telf);

			}
		}

		return;
	}

	// BORRA LOS TELEFONOS DEL PROVEEDOR //
	function borrar_telefono($idproveedor)
	{
		$sql="
		DELETE FROM catalogo_proveedor_telefono WHERE IDPROVEEDOR='$idproveedor'
		";
		$this->query($sql);
		return;
	}

	function borrar_telefono_contacto($idcontacto){
		$sql="
		DELETE FROM catalogo_proveedor_contacto_telefono WHERE IDCONTACTO='$idcontacto'
		";
		$this->query($sql);
		return;
	}


	// BORRA LOS COSTOS NEGOCIADOS //
	function borrar_prov_serv_cost($idproveedor,$idservicio){
		$sql="
		DELETE FROM catalogo_proveedor_servicio_costo_negociado WHERE IDPROVEEDOR='$idproveedor' AND  IDSERVICIO='$idservicio'
		";
		$this->query($sql);
		return;
	}

	// BORRA EMPRESA DEL CATALOGO PROVEEDOR EXPERIENCIA //
	function borrar_empresa_exp($idproveedorexp){
		$sql="delete from catalogo_proveedor_experiencia where IDPROVEEDOREXP='$idproveedorexp'";
		$this->query($sql);

		return;
	}

	function borrar_contacto($idcontacto){
		$sql="delete from catalogo_proveedor_contacto where IDCONTACTO='$idcontacto'";
		$this->query($sql);
		return ;
	}




	//**************** DETERMINA LOS POLIGONOS ASIGNADO AL PROVEEDOR **************//
	function lista_proveedores($ubicacion,$idfamilia,$idservicio,$interno,$activo,$texto)
	{

		$condicion =" WHERE 1 ";

		if ($idfamilia!='') $condicion.= " AND cs.IDFAMILIA ='$idfamilia'";
		if ($activo!='') $condicion.= " AND (cp.ACTIVO = $activo)";
		if ($idservicio!='') $condicion.= " AND (cps.IDSERVICIO = '$idservicio'  OR cps.IDSERVICIO ='0' )";
		if ($interno!='') $condicion.= " AND cp.INTERNO = '$interno' ";
		if ($texto!='') $condicion.= " AND (cp.NOMBRECOMERCIAL like '%$texto%' OR cp.NOMBREFISCAL like '%$texto%')";

		/* QUERY BUSCA LOA PROVEEDORES QUE PRESTAN EL SERVICIO*/

		$sql="
			SELECT 
				cp.IDPROVEEDOR
			FROM 
			$this->catalogo.catalogo_proveedor cp
				LEFT JOIN $this->catalogo.catalogo_proveedor_servicio cps ON cps.IDPROVEEDOR = cp.IDPROVEEDOR 
				LEFT JOIN $this->catalogo.catalogo_servicio cs ON cs.IDSERVICIO = cps.IDSERVICIO
				$condicion
				
			GROUP BY cps.IDPROVEEDOR
			order by cp.INTERNO DESC, cp.NOMBRECOMERCIAL ASC
		";


			//	echo $sql;
				$res_prv= $this->query($sql);
				$lista_prov= array();
				$prov = new proveedor();
				$poli = new poligono();
				$circulo = new circulo();
				$point= array('lat'=>$ubicacion->latitud,'lng'=>$ubicacion->longitud);

				if ($ubicacion->cveentidad1==0){
					while ($reg=$res_prv->fetch_object())
					{
						$lista_prov[$reg->IDPROVEEDOR][DISTANCIA]='';
					}

				}
				else
				{
					while ($reg_prv=$res_prv->fetch_object())
					{
						$prov->carga_datos($reg_prv->IDPROVEEDOR);
						$distancia= geoDistancia($ubicacion->latitud,$ubicacion->longitud,$prov->latitud,$prov->longitud,$prov->lee_parametro('UNIDAD_LONGITUD'));
						$sw=0;

						$condicion="";
						$condicion.= " cuf.CVEPAIS = '$ubicacion->cvepais'";

						$condicion.= " AND (cuf.CVEENTIDAD1 in ('$ubicacion->cveentidad1','0'))";
						$condicion.= " AND (cuf.CVEENTIDAD2 in ('$ubicacion->cveentidad2','0'))";
						$condicion.= " AND (cuf.CVEENTIDAD3 in ('$ubicacion->cveentidad3','0'))";
						$condicion.= " AND (cuf.CVEENTIDAD4 in ('$ubicacion->cveentidad4','0'))";
						$condicion.= " AND (cuf.CVEENTIDAD5 in ('$ubicacion->cveentidad5','0'))";
						$condicion.= " AND (cuf.CVEENTIDAD6 in ('$ubicacion->cveentidad6','0'))";
						$condicion.= " AND (cuf.CVEENTIDAD7 in ('$ubicacion->cveentidad7','0'))";
						
						$condicion.= " OR ("; 

						if ($ubicacion->cveentidad1!='0') $condicion.= " (cuf.CVEENTIDAD1 = '$ubicacion->cveentidad1')";
						if ($ubicacion->cveentidad2!='0') $condicion.= " AND (cuf.CVEENTIDAD2 = '$ubicacion->cveentidad2')";
						if ($ubicacion->cveentidad3!='0') $condicion.= " AND (cuf.CVEENTIDAD3 = '$ubicacion->cveentidad3')";
						if ($ubicacion->cveentidad4!='0') $condicion.= " AND (cuf.CVEENTIDAD4 = '$ubicacion->cveentidad4')";
						if ($ubicacion->cveentidad5!='0') $condicion.= " AND (cuf.CVEENTIDAD5 = '$ubicacion->cveentidad5')";
						if ($ubicacion->cveentidad6!='0') $condicion.= " AND (cuf.CVEENTIDAD6 = '$ubicacion->cveentidad6')";
						if ($ubicacion->cveentidad7!='0') $condicion.= " AND (cuf.CVEENTIDAD7 = '$ubicacion->cveentidad7')";
						
						$condicion .= " )";

						/* QUERY BUSCA LAS UNIDADES FEDERATIVAS QUE COINCIDAN CON LA UBICACION PARA CADA PROVEEDOR*/
						$sql="
			SELECT 
				cpsxuf.IDPROVEEDOR, cpsxuf.IDUNIDADFEDERATIVA,cpsxuf.ARRAMBITO
			FROM
			    $this->catalogo.catalogo_proveedor_servicio_x_unidad_federativa cpsxuf
				LEFT JOIN $this->catalogo.catalogo_unidadfederativa cuf ON $condicion
			WHERE 
				cpsxuf.IDUNIDADFEDERATIVA = cuf.IDUNIDADFEDERATIVA
				AND	cpsxuf.IDPROVEEDOR ='$reg_prv->IDPROVEEDOR'
				and cpsxuf.IDSERVICIO IN ('$idservicio', '0')
				ORDER BY 3 DESC
			";
					//echo $sql;
			$res_prv_entid= $this->query($sql);
			while ($reg_prv_entid= $res_prv_entid->fetch_object())
			{
				$lista_prov[$reg_prv_entid->IDPROVEEDOR][DISTANCIA]= $distancia;
				$lista_prov[$reg_prv_entid->IDPROVEEDOR][AMBITO]=	$reg_prv_entid->ARRAMBITO;
				$lista_prov[$reg_prv_entid->IDPROVEEDOR][TIPOPOLIGONO] ='ENTIDAD';
				$lista_prov[$reg_prv_entid->IDPROVEEDOR][ID] = $reg_prv_entid->IDUNIDADFEDERATIVA;
				if ($reg_prv_entid->ARRAMBITO=='LOC') $sw=1; // si hubo algun entidad LOC se activa el sw

			}
			if (($ubicacion->latitud !='' )&& ($ubicacion->longitud!=''))
			{
				$sql="
				SELECT 
					cpsxp.IDPROVEEDOR, cpsxp.IDPOLIGONO
				FROM 
				$this->catalogo.catalogo_proveedor_servicio_x_poligono cpsxp
				WHERE
					cpsxp.IDPROVEEDOR ='$reg_prv->IDPROVEEDOR'
				";
				$res_prv_poli= $this->query($sql);
				while ($reg_prv_poli = $res_prv_poli->fetch_object())
				{
					// verifica si el punto esta incluido en el poligono
					$in = pointInPolygon($point,$poli->lee_vertices($reg_prv_poli->IDPOLIGONO));
					if (( $in ) && ($sw==0))
					{
						$lista_prov[$reg_prv_poli->IDPROVEEDOR][DISTANCIA]= $distancia;
						$lista_prov[$reg_prv_poli->IDPROVEEDOR][AMBITO]=	$reg_prv_poli->ARRAMBITO;
						$lista_prov[$reg_prv_poli->IDPROVEEDOR][TIPOPOLIGONO] ='POLIGONO';
						$lista_prov[$reg_prv_poli->IDPROVEEDOR][ID] = $reg_prv_poli->IDPOLIGONO;
						if ($reg_prv_poli->ARRAMBITO=='LOC') $sw=1; // si hubo algun poligono LOC se activa el sw
					}
				}

				$sql="
				SELECT 
					cpsxc.IDPROVEEDOR, cpsxc.IDCIRCULO
				FROM
				$this->catalogo.catalogo_proveedor_servicio_x_circulo cpsxc
				WHERE
					cpsxc.IDPROVEEDOR='$res_prv->IDPROVEEDOR'
				";

				$res_prv_circ = $this->query($sql);
				while ($reg_prv_circ = $res_prv_circ->fetch_object())
				{
					$circulo->leer($reg_prv_circ->IDCIRCULO);
					$vertices = $circulo->verticesCircle($circulo->latitud,$circulo->longitud,$circulo->radio,$circulo->idmedida);
					$in= pointInPolygon($point,$vertices);

					if (($in ) && ($sw==0))
					{
						$lista_prov[$reg_prv_circ->IDPROVEEDOR][DISTANCIA]= $distancia;
						$lista_prov[$reg_prv_circ->IDPROVEEDOR][AMBITO]=	$reg_prv_circ->ARRAMBITO;
						$lista_prov[$reg_prv_circ->IDPROVEEDOR][TIPOPOLIGONO] ='CIRCULO';
						$lista_prov[$reg_prv_circ->IDPROVEEDOR][ID] = $reg_prv_circ->IDCIRCULO;
						if ($reg_prv_circ->ARRAMBITO=='LOC') $sw=1;
					}
				}
			} // fin de la busqueda por LATITUD Y LONGITUD





					} // fin del while del proveedor
				}

				/* LEE LAS PROPORCIONES DE LA FORMULA DEL RANKING */
				$sql="
			SELECT 	
				ELEMENTO,PROPORCION 
			FROM 
			$this->catalogo.catalogo_proporciones_ranking
			";
			$result=$this->query($sql);

			while($reg = $result->fetch_object())
			$proporcion[$reg->ELEMENTO] = $reg->PROPORCION;


			$max_costo_int=0;
			$max_costo_ext=0;


			/* OBTENER DATOS PARA EVALUAR EL RANKING */
			foreach ($lista_prov as $idproveedor=>$proveedor)
			{
				$sql="
		SELECT 
			IF (cp.ARREVALRANKING='SKILL',IF (cp.SKILL>0,cp.SKILL/100,0), IF (cp.CDE>0,cp.CDE/100,0)) VALRANKING,
			IF (cpscn.MONTOLOCAL IS NULL,0,cpscn.MONTOLOCAL) COSTO,
			IF (cp.EVALSATISFACCION>0,cp.EVALSATISFACCION/100,0)  EVALSATISFACCION ,
			cp.EVALINFRAESTRUCTURA EVALINFRAESTRUCTURA,
			cp.EVALFIDELIDAD  EVALFIDELIDAD,
			cp.INTERNO
		FROM 
		$this->catalogo.catalogo_proveedor cp
			LEFT JOIN $this->catalogo.catalogo_proveedor_servicio_costo_negociado cpscn ON cpscn.IDPROVEEDOR = cp.IDPROVEEDOR AND cpscn.IDSERVICIO = '$idservicio'  AND cpscn.IDCOSTO = 1
		WHERE 
			cp.IDPROVEEDOR = '$idproveedor' 
		";
		//				echo $sql;
		$result = $this->query($sql);

		while ($reg=$result->fetch_object())
		{
			if  ($reg->INTERNO){
				$temp_prov_int[$idproveedor][VALRANKING] = $reg->VALRANKING;
				$temp_prov_int[$idproveedor][COSTO] = $reg->COSTO;
				$temp_prov_int[$idproveedor][EVALSATISFACCION] = $reg->EVALSATISFACCION;
				$temp_prov_int[$idproveedor][EVALINFRAESTRUCTURA] = $reg->EVALINFRAESTRUCTURA;
				$temp_prov_int[$idproveedor][EVALFIDELIDAD] = $reg->EVALFIDELIDAD;
				if ($reg->COSTO > $max_costo_int) $max_costo_int= $reg->COSTO;
			}
			else
			{
				$temp_prov_ext[$idproveedor][VALRANKING] = $reg->VALRANKING;
				$temp_prov_ext[$idproveedor][COSTO] = $reg->COSTO;
				$temp_prov_ext[$idproveedor][EVALSATISFACCION] = $reg->EVALSATISFACCION;
				$temp_prov_ext[$idproveedor][EVALINFRAESTRUCTURA] = $reg->EVALINFRAESTRUCTURA;
				$temp_prov_ext[$idproveedor][EVALFIDELIDAD] = $reg->EVALFIDELIDAD;
				if ($reg->COSTO > $max_costo_ext) $max_costo_ext= $reg->COSTO;
			}
		}

			}

			/*calcula el Ranking de proveedores internos */

			foreach ($temp_prov_int as $idproveedor=>$proveedor)
			{
				$valranking = round($proveedor[VALRANKING] * $proporcion[CDE_SKILL],4);
				$valcosto =  ($proveedor[COSTO]==0)?0:(1-($proveedor[COSTO]/$max_costo_int))*$proporcion[COSTO];
				$evalsatisfaccion = round($proveedor[EVALSATISFACCION] * $proporcion[SATISFACCION],2);
				$evalinfraestructura = round($proveedor[EVALINFRAESTRUCTURA] * $proporcion[INFRAESTRUCTURA],2);
				$evalfidelidad = round($proveedor[EVALFIDELIDAD] * $proporcion[FIDELIDAD],2);
				$lista_prov_int[$idproveedor][RANKING] = ($valranking + $valcosto+$evalsatisfaccion + $evalinfraestructura + $evalfidelidad)*100;
			}

			/*calcula el Ranking de proveedores externos */
			foreach ($temp_prov_ext as $idproveedor=>$proveedor)
			{
				$valranking = round($proveedor[VALRANKING] * $proporcion[CDE_SKILL],4);
				$valcosto =  ($proveedor[COSTO]==0)?0:(1-($proveedor[COSTO]/$max_costo_ext))*$proporcion[COSTO];
				$evalsatisfaccion = round($proveedor[EVALSATISFACCION] * $proporcion[SATISFACCION],2);
				$evalinfraestructura = round($proveedor[EVALINFRAESTRUCTURA] * $proporcion[INFRAESTRUCTURA],2);
				$evalfidelidad = round($proveedor[EVALFIDELIDAD] * $proporcion[FIDELIDAD],2);
				$lista_prov_ext[$idproveedor][RANKING] = ($valranking + $valcosto+$evalsatisfaccion + $evalinfraestructura + $evalfidelidad)*100;
			}

			$temp_prov_int = ordernarArray($lista_prov_int,'RANKING',1);
			$temp_prov_ext = ordernarArray($lista_prov_ext,'RANKING',1);


			foreach ($temp_prov_int as $idproveedor => $proveedor)
			{
				$lista_ordenada[$idproveedor] = $lista_prov[$idproveedor];
				$lista_ordenada[$idproveedor][RANKING] = $lista_prov_int[$idproveedor][RANKING];
			}
			foreach ($temp_prov_ext as $idproveedor => $proveedor)
			{
				$lista_ordenada[$idproveedor] = $lista_prov[$idproveedor];
				$lista_ordenada[$idproveedor][RANKING] = $lista_prov_ext[$idproveedor][RANKING];
			}
			/* DEVUELVE EL ARRAY*/
			return $lista_ordenada ;

	}



	function niveles($area){

		//if ($area->CVEENTIDAD1=='0') return 1;
		if ($area->cveentidad2=='0') return 1;
		if ($area->cveentidad3=='0') return 2;
		if ($area->cveentidad4=='0') return 3;
		if ($area->cveentidad5=='0') return 4;
		if ($area->cveentidad6=='0') return 5;
		if ($area->cveentidad7=='0') return 6;
	}

	function inclusion($area,$ubigeo){
		$sw1 = true;
		for($i=1;$i<=$this->niveles($area);$i++)
		{

			if ($area->{cveentidad.$i} != $ubigeo->{CVEENTIDAD.$i}) {

				$sw1 = false;
				break;
			}

		}
		return $sw1;
	}


	function listar_proveedores(){
		/*

		SELECT
		cp.IDPROVEEDOR IDPROV, cp.NOMBREFISCAL, cp.NOMBRECOMERCIAL,
		cp.IDTIPODOCUMENTO, cp.IDDOCUMENTO, cp.EMAIL1, cp.EMAIL2, cp.EMAIL3,
		cp.BRSCH, cp.FDGRV, cp.ZTERM, cp.MWSKZ, cp.PARVO, cp.PAVIP,
		cp.ACTIVO,cp.INTERNO,
		cp.ARREVALRANKING,
		cp.CDE,
		cp.SKILL,
		cp.EVALFIDELIDAD,
		cp.EVALINFRAESTRUCTURA,
		cp.EVALSATISFACCION,
		cp.IDMONEDA,
		YEAR(cp.FECHAINICIOACTIVIDADES) ANIO,
		MONTH(cp.FECHAINICIOACTIVIDADES) MES,
		DAY(cp.FECHAINICIOACTIVIDADES) DIA,
		cpu.*
		FROM
		$this->catalogo.catalogo_proveedor cp
		LEFT JOIN $this->catalogo.catalogo_proveedor_ubigeo cpu ON cp.IDPROVEEDOR=cpu.IDPROVEEDOR


		*/
		$sql="
	SELECT
		cp.IDPROVEEDOR IDPROV, cp.NOMBREFISCAL, cp.NOMBRECOMERCIAL, 
		cp.IDTIPODOCUMENTO, cp.IDDOCUMENTO, cp.EMAIL1, cp.EMAIL2, cp.EMAIL3,
		cp.BRSCH, csb.RAMO, cp.FDGRV, cp.ZTERM, cp.MWSKZ, cp.PARVO, cp.PAVIP,
		cp.ACTIVO,cp.INTERNO,
		cp.ARREVALRANKING,
		cp.CDE,
		cp.SKILL,
		cp.EVALFIDELIDAD,
		cp.EVALINFRAESTRUCTURA,
		cp.EVALSATISFACCION,
		cp.IDMONEDA,
		YEAR(cp.FECHAINICIOACTIVIDADES) ANIO,
		MONTH(cp.FECHAINICIOACTIVIDADES) MES, 
		DAY(cp.FECHAINICIOACTIVIDADES) DIA,
		cpu.*,
		cpt.*
	FROM
	$this->catalogo.catalogo_proveedor cp
		LEFT JOIN $this->catalogo.catalogo_proveedor_ubigeo cpu ON cp.IDPROVEEDOR=cpu.IDPROVEEDOR 
		LEFT JOIN $this->catalogo.catalogo_sap_brsch csb ON cp.BRSCH = csb.BRSCH	
		LEFT JOIN $this->catalogo.catalogo_proveedor_telefono cpt ON cp.IDPROVEEDOR = cpt.IDPROVEEDOR AND cpt.PRIORIDAD=1
	
		";

	$result =$this->query($sql);
	return $result;
	}

	function listar_contactos($idproveedor){
		$sql="
		SELECT
			cpc.IDCONTACTO,cpc.IDPROVEEDOR,cpc.RESPONSABLE,
			CONCAT(cpc.NOMBRE,' ',cpc.APPATERNO,' ',cpc.APMATERNO) NOMBRE
		FROM
			catalogo_proveedor_contacto cpc
		WHERE
	 		cpc.IDPROVEEDOR='$idproveedor'
		";
		$result=$this->query($sql);

		return $result;
	}






}


function ordernarArray($array_ordenar,$columna,$ord){

	$columna_ordenar = array();

	foreach ($array_ordenar as $indice => $row)
	$columna_ordenar[$indice]  = $row[$columna];

	if ($ord)
	arsort($columna_ordenar);
	else
	asort($columna_ordenar);

	$array_ordenado = array();
	foreach ($columna_ordenar as $indice => $pos)
	$array_ordenado[$indice] = $lista_prov[$indice];



	return  $array_ordenado;
}



?>