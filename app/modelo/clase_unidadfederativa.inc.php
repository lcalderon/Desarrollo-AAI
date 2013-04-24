<?
class unidadfederativa extends DB_mysqli
{
	var $idunidadfederativa;
	var $unidades;



	function leer($idunidadfederativa){
		$sql="
		select
			*
		from
			$this->catalogo.catalogo_unidadfederativa
		where
		IDUNIDADFEDERATIVA='$idunidadfederativa'
		 ";
		$result= $this->query($sql);

		while($reg = $result->fetch_object()){
			$this->idunidadfederativa= $reg->IDUNIDADFEDERATIVA;
			$this->unidades[1] = $reg->CVEENTIDAD1;
			$this->unidades[2] = $reg->CVEENTIDAD2;
			$this->unidades[3] = $reg->CVEENTIDAD3;
			$this->unidades[4] = $reg->CVEENTIDAD4;
			$this->unidades[5] = $reg->CVEENTIDAD5;
			$this->unidades[6] = $reg->CVEENTIDAD6;
			$this->unidades[7] = $reg->CVEENTIDAD7;
		}
		return;
	}

	function grabar($form)
	{
		$serv_unid[IDPROVEEDOR] = $form[IDPROVEEDOR];
		$serv_unid[IDSERVICIO] = $form[IDSERVICIO];
		$serv_unid[ARRAMBITO] = $form[ARRAMBITO];
		$serv_unid[IDUSUARIOMOD] = $form[IDUSUARIOMOD];

		if ($form[IDUNIDADFEDERATIVA]!='')
		{
			$serv_unid[IDUNIDADFEDERATIVA] = $form[IDUNIDADFEDERATIVA];
			$this->update('catalogo_proveedor_servicio_x_unidad_federativa',$serv_unid," where IDUNIDADFEDERATIVA ='$serv_unid[IDUNIDADFEDERATIVA]'");
		}
		else
		{
			$this->insert_reg('catalogo_proveedor_servicio_x_unidad_federativa',$serv_unid);
			$serv_unid[IDUNIDADFEDERATIVA] = $this->insert_id;

		}

		$unidad[IDUNIDADFEDERATIVA] = $serv_unid[IDUNIDADFEDERATIVA];
		$unidad[CVEPAIS]=$form[CVEPAIS];
		$unidad[CVEENTIDAD1]=($form[CVEENTIDAD1]=='')?'0':$form[CVEENTIDAD1];
		$unidad[CVEENTIDAD2]=($form[CVEENTIDAD2]=='')?'0':$form[CVEENTIDAD2];
		$unidad[CVEENTIDAD3]=($form[CVEENTIDAD3]=='')?'0':$form[CVEENTIDAD3];
		$unidad[CVEENTIDAD4]=($form[CVEENTIDAD4]=='')?'0':$form[CVEENTIDAD4];
		$unidad[CVEENTIDAD5]=($form[CVEENTIDAD5]=='')?'0':$form[CVEENTIDAD5];
		$unidad[CVEENTIDAD6]=($form[CVEENTIDAD6]=='')?'0':$form[CVEENTIDAD6];
		$unidad[CVEENTIDAD7]=($form[CVEENTIDAD7]=='')?'0':$form[CVEENTIDAD7];

		$this->borrar($unidad[IDUNIDADFEDERATIVA]);

		$this->insert_reg('catalogo_unidadfederativa',$unidad);


		return;
	}

	function borrar($idunidadfederativa)
	{
		$sql="
		 DELETE FROM $this->catalogo.catalogo_unidadfederativa
		 WHERE
		 IDUNIDADFEDERATIVA = '$idunidadfederativa'
		";
		$this->query($sql);
		return;
	}


	function descripcion_unidad($reg){

		$condicion =  "CVEENTIDAD1 = '$reg->CVEENTIDAD1'";
		$condicion.= ' AND CVEENTIDAD2 =';
		$condicion.= ($reg->CVEENTIDAD2=='')?"'0'":"'$reg->CVEENTIDAD2'";
		$condicion.= ' AND CVEENTIDAD3 =';
		$condicion.= ($reg->CVEENTIDAD3=='')?"'0'":"'$reg->CVEENTIDAD3'";
		$condicion.= ' AND CVEENTIDAD4 =';
		$condicion.= ($reg->CVEENTIDAD4=='')?"'0'":"'$reg->CVEENTIDAD4'";
		$condicion.= ' AND CVEENTIDAD5 =';
		$condicion.= ($reg->CVEENTIDAD5=='')?"'0'":"'$reg->CVEENTIDAD5'";
		$condicion.= ' AND CVEENTIDAD6 =';
		$condicion.= ($reg->CVEENTIDAD6=='')?"'0'":"'$reg->CVEENTIDAD6'";
		$condicion.= ' AND CVEENTIDAD7 =';
		$condicion.= ($reg->CVEENTIDAD7=='')?"'0'":"'$reg->CVEENTIDAD7'";


		$sql="
		SELECT
		  DESCRIPCION,NIVEL
		 FROM 
		  catalogo_entidad	
		 WHERE
		   $condicion
		";	

		$result = $this->query($sql);
		while ($reg1= $result->fetch_object()){
			$descripcion->DESCRIPCION = utf8_encode($reg1->DESCRIPCION);
			$descripcion->NIVEL = $reg1->NIVEL;
		}

		return $descripcion;
	}


	function nombre_entidades_array($arrubigeo)
	{
		$sql="
		SELECT 
			DESCRIPCION
		FROM
			$this->catalogo.catalogo_entidad
		WHERE
			CVEENTIDAD1 = '$arrubigeo[1]'
			AND CVEENTIDAD2 ='0'
		";
		
		$result1 = $this->query($sql);
		while ($reg1= $result1->fetch_object()) {
			$arr[1]= utf8_encode($reg1->DESCRIPCION);
		}
		
		$sql="
		SELECT 
			DESCRIPCION
		FROM
			$this->catalogo.catalogo_entidad
		WHERE
			CVEENTIDAD1 = '$arrubigeo[1]'
			AND CVEENTIDAD2 = '$arrubigeo[2]'
			AND CVEENTIDAD3 ='0'
		";
		$result1 = $this->query($sql);
		while ($reg1= $result1->fetch_object()) {
			$arr[2]=utf8_encode($reg1->DESCRIPCION);
		}

		$sql="
		SELECT 
			DESCRIPCION
		FROM
			$this->catalogo.catalogo_entidad
		WHERE
			CVEENTIDAD1 = '$arrubigeo[1]'
			AND CVEENTIDAD2 = '$arrubigeo[2]'
			AND CVEENTIDAD3 ='$arrubigeo[3]'
			AND CVEENTIDAD4 ='0'
			
		";
		$result1 = $this->query($sql);
		while ($reg1= $result1->fetch_object()) {
			$arr[3]=utf8_encode($reg1->DESCRIPCION);
		}

		$sql="
		SELECT 
			DESCRIPCION
		FROM
			$this->catalogo.catalogo_entidad
		WHERE
			CVEENTIDAD1 = '$arrubigeo[1]'
			AND CVEENTIDAD2 = '$arrubigeo[2]'
			AND CVEENTIDAD3 ='$arrubigeo[3]'
			AND CVEENTIDAD4 ='$arrubigeo[4]'
			AND CVEENTIDAD5 ='0'
			
		";
		$result1 = $this->query($sql);
		while ($reg1= $result1->fetch_object()) {
			$arr[4]=utf8_encode($reg1->DESCRIPCION);
		}

		$sql="
		SELECT 
			DESCRIPCION
		FROM
			$this->catalogo.catalogo_entidad
		WHERE
			CVEENTIDAD1 = '$arrubigeo[1]'
			AND CVEENTIDAD2 = '$arrubigeo[2]'
			AND CVEENTIDAD3 ='$arrubigeo[3]'
			AND CVEENTIDAD4 ='$arrubigeo[4]'
			AND CVEENTIDAD5 ='$arrubigeo[5]'
			AND CVEENTIDAD6 ='0'
			
		";
		$result1 = $this->query($sql);
		while ($reg1= $result1->fetch_object()) {
			$arr[5]=utf8_encode($reg1->DESCRIPCION);
			}

		$sql="
		SELECT 
			DESCRIPCION
		FROM
			$this->catalogo.catalogo_entidad
		WHERE
			CVEENTIDAD1 = '$arrubigeo[1]'
			AND CVEENTIDAD2 = '$arrubigeo[2]'
			AND CVEENTIDAD3 ='$arrubigeo[3]'
			AND CVEENTIDAD4 ='$arrubigeo[4]'
			AND CVEENTIDAD5 ='$arrubigeo[5]'
			AND CVEENTIDAD6 ='$arrubigeo[6]'
			AND CVEENTIDAD7 ='0'
			
		";
		$result1 = $this->query($sql);
		while ($reg1= $result1->fetch_object()) {
			$arr[6]=utf8_encode($reg1->DESCRIPCION);
			}
			
		$sql="
		SELECT 
			DESCRIPCION
		FROM
			$this->catalogo.catalogo_entidad
		WHERE
			CVEENTIDAD1 = '$arrubigeo[1]'
			AND CVEENTIDAD2 = '$arrubigeo[2]'
			AND CVEENTIDAD3 ='$arrubigeo[3]'
			AND CVEENTIDAD4 ='$arrubigeo[4]'
			AND CVEENTIDAD5 ='$arrubigeo[5]'
			AND CVEENTIDAD6 ='$arrubigeo[6]'
			AND CVEENTIDAD7 ='$arrubigeo[7]'
			
		";
		$result1 = $this->query($sql);
		while ($reg1= $result1->fetch_object()) {
			$arr[7]=utf8_encode($reg1->DESCRIPCION);
			}
			

		return $arr;
	}

	
	
	
}



?>