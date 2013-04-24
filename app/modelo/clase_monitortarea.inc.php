<?
class monitortarea extends DB_mysqli {

	var $enrojo;


	/*	$ventana = tiempo de la venta en minutos default 30 minutos	*/
	function listar_tareas($idusuarios,$statustarea,$ventana=30){

		// procedimiento para colocar comillas a la lista de usuarios
		foreach ($idusuarios as $usuario)
		if ($usuario!='')
		{
			$usuarios[]="'$usuario'";
			$usuarios_selec[] =$usuario;
		}
		$lista_usuarios = (isset($usuarios))?implode(',',$usuarios):'';
		$fecha= date("Y-m-d H:i:s",time()+($ventana*60));
		$sql="
	SELECT
		mt.ID,mt.IDTAREA,mt.FECHATAREA,mt.STATUSTAREA,mt.DISPLAY, mt.IDEXPEDIENTE,mt.IDASISTENCIA, 
		ct.DESCRIPCION NOMBRETAREA, ct.ETAPA, 
		a.IDSERVICIO,a.IDETAPA, a.IDUSUARIORESPONSABLE,a.IDCUENTA,
		cs.DESCRIPCION NOMBRESERVICIO, 
		(UNIX_TIMESTAMP(mt.FECHATAREA) - UNIX_TIMESTAMP(NOW())) DIFERENCIA, 
		CONCAT(ept.CODIGOAREA,ept.NUMEROTELEFONO) NUMEROTELEFONOCONTACTO, 
		CONCAT(pct.CODIGOAREA,pct.NUMEROTELEFONO) NUMEROTELEFONOPROVEEDORCONTACTO,
		cu.TODOCUENTAS
	FROM 
	( 
	$this->temporal.monitor_tarea mt, 
	$this->catalogo.catalogo_tarea ct, 
	$this->temporal.asistencia a, 
	$this->catalogo.catalogo_servicio cs, 
	$this->temporal.expediente_persona ep, 
	$this->temporal.expediente_persona_telefono ept,
	$this->catalogo.catalogo_usuario cu
 	) 
	LEFT JOIN $this->temporal.asistencia_asig_proveedor aap ON (aap.IDASISTENCIA=a.IDASISTENCIA) 
	LEFT JOIN $this->catalogo.catalogo_proveedor_contacto pc ON (pc.IDPROVEEDOR = aap.IDPROVEEDOR AND responsable=1) 
	LEFT JOIN $this->catalogo.catalogo_proveedor_contacto_telefono pct ON (pct.IDCONTACTO =pc.IDCONTACTO AND pct.PRIORIDAD=1) 
	WHERE 
	mt.IDTAREA = ct.IDTAREA 
	AND mt.IDASISTENCIA = a.IDASISTENCIA 
	AND a.IDSERVICIO = cs.IDSERVICIO 
	AND a.IDEXPEDIENTE = ep.IDEXPEDIENTE 
	AND ep.ARRTIPOPERSONA='CONTACTO' 
	AND ep.IDPERSONA = ept.IDPERSONA 
	AND ept.PRIORIDAD=0 
	AND a.IDUSUARIORESPONSABLE IN ($lista_usuarios)
	AND mt.STATUSTAREA='$statustarea'
	AND mt.FECHATAREA <= '$fecha' 
	AND a.IDUSUARIORESPONSABLE = cu.IDUSUARIO
	AND (
	(cu.TODOCUENTAS = 1) OR
	(
	   (cu.TODOCUENTAS=0) 
		AND (a.IDCUENTA IN 
			(SELECT IDCUENTA FROM $this->temporal.seguridad_acceso_cuenta sac WHERE sac.IDUSUARIO=a.IDUSUARIORESPONSABLE)
		     )
	 )
     )
GROUP BY mt.ID ORDER BY DIFERENCIA";
//echo $sql;
		$result=$this->query($sql);
		return $result;
	}


	/*	BORRA UNA TAREA ESPECIFICA	*/
	function borrar_tarea($id){
		$sql="UPDATE $this->temporal.monitor_tarea  SET STATUSTAREA='CANCELADA' WHERE ID='$id'";
		$this->query($sql);
		return;
	}

	function borrar_tarea_asistencia($idasistencia,$idtarea=''){
		if ($idtarea!='') $condicion= " AND IDTAREA='$idtarea'";
		$sql="UPDATE $this->temporal.monitor_tarea SET STATUSTAREA='CANCELADA'  WHERE IDASISTENCIA ='$idasistencia' AND STATUSTAREA in ('PENDIENTE','NO ATENDIDA') $condicion";
		$this->query($sql);
		return;
	}

	function tiempos_tarea($idtarea){
		$this->enrojo=5;
		$sql="select ENROJO from $this->catalogo.catalogo_tarea  where IDTAREA='$idtarea'";
		$result = $this->query($sql);
		while($reg= $result->fetch_object())
		{
			$this->enrojo= $reg->ENROJO;
		}
		return;
	}
}
?>
