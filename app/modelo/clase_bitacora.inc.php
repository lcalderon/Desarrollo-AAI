<?
class bitacora extends DB_mysqli{
	var $idetapa;
	var $nom_etapa;
	var $fechamod;
	var $idusuario;
	var $comentario;


	function carga_datos($idasistencia,$idetapa=''){
		if ($idetapa!='') 
		$sql=" select IDBITACORA,IDASISTENCIA,IDUSUARIOMOD,FECHAMOD,COMENTARIO, $idetapa IDETAPA from $this->temporal.asistencia_bitacora_etapa$idetapa where idasistencia='$idasistencia' order by FECHAMOD DESC";
		else
		$sql="
		(SELECT IDBITACORA,IDASISTENCIA,IDUSUARIOMOD,FECHAMOD,COMENTARIO,ARRCLASIFICACION,1 IDETAPA,'' NOMBRECOMERCIAL from $this->temporal.asistencia_bitacora_etapa1 WHERE  IDASISTENCIA='$idasistencia')
		UNION ALL
		(SELECT abe.IDBITACORA,abe.IDASISTENCIA,abe.IDUSUARIOMOD,abe.FECHAMOD,abe.COMENTARIO,abe.ARRCLASIFICACION,2 IDETAPA,NOMBRECOMERCIAL FROM $this->temporal.asistencia_bitacora_etapa2 abe LEFT JOIN  $this->catalogo.catalogo_proveedor cp ON cp.IDPROVEEDOR = abe.IDPROVEEDOR  WHERE  IDASISTENCIA='$idasistencia')
		UNION ALL
		(SELECT IDBITACORA,IDASISTENCIA,IDUSUARIOMOD,FECHAMOD,COMENTARIO,ARRCLASIFICACION,3 IDETAPA,'' NOMBRECOMERCIAL FROM $this->temporal.asistencia_bitacora_etapa3 WHERE  IDASISTENCIA='$idasistencia')
		UNION ALL
		(SELECT abe.IDBITACORA,abe.IDASISTENCIA,abe.IDUSUARIOMOD,abe.FECHAMOD,abe.COMENTARIO,abe.ARRCLASIFICACION,4 IDETAPA,NOMBRECOMERCIAL FROM $this->temporal.asistencia_bitacora_etapa4 abe LEFT JOIN  $this->catalogo.catalogo_proveedor cp ON cp.IDPROVEEDOR = abe.IDPROVEEDOR WHERE  IDASISTENCIA='$idasistencia')
		UNION ALL
		(SELECT IDBITACORA,IDASISTENCIA,IDUSUARIOMOD,FECHAMOD,COMENTARIO,ARRCLASIFICACION,5 IDETAPA,'' NOMBRECOMERCIAL FROM $this->temporal.asistencia_bitacora_etapa5 WHERE  IDASISTENCIA='$idasistencia')
		UNION ALL
		(SELECT abe.IDBITACORA,abe.IDASISTENCIA,abe.IDUSUARIOMOD,abe.FECHAMOD,abe.COMENTARIO,abe.ARRCLASIFICACION,6 IDETAPA, NOMBRECOMERCIAL FROM $this->temporal.asistencia_bitacora_etapa6 abe LEFT JOIN  $this->catalogo.catalogo_proveedor cp ON cp.IDPROVEEDOR = abe.IDPROVEEDOR WHERE  IDASISTENCIA='$idasistencia')
		UNION ALL
		(SELECT abe.IDBITACORA,abe.IDASISTENCIA,abe.IDUSUARIOMOD,abe.FECHAMOD,abe.COMENTARIO,abe.ARRCLASIFICACION,7 IDETAPA, NOMBRECOMERCIAL FROM $this->temporal.asistencia_bitacora_etapa7 abe LEFT JOIN  $this->catalogo.catalogo_proveedor cp ON cp.IDPROVEEDOR = abe.IDPROVEEDOR WHERE  IDASISTENCIA='$idasistencia')
		UNION ALL
		(SELECT IDBITACORA,IDASISTENCIA,IDUSUARIOMOD,FECHAMOD,COMENTARIO,ARRCLASIFICACION,8 IDETAPA,'' NOMBRECOMERCIAL FROM $this->temporal.asistencia_bitacora_etapa8 WHERE  IDASISTENCIA='$idasistencia')
		ORDER BY FECHAMOD
		";
//		echo $sql;
		$result = $this->query($sql);
		return $result;
	}
}


?>

