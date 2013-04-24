<?

class  usuario {

	var $extension;
	
	
	var $con;			// variable de conexion
	
	function __construct(){
		$this->con = new DB_mysqli();
		$bd=$this->con->temporal;
		$this->con->select_db($bd);
	} // fin del constructor
	
	function extension_usada($ideusuario){
		$extension='';
		
		$sql="
		SELECT 
 			EXTENSION
		FROM
			seguridad_acceso
		WHERE
			IDUSUARIO ='$ideusuario'
			
		ORDER BY 
			FECHAMOD DESC LIMIT 1
		";
		
		$result = $this->con->query($sql);
		while ($reg = $result->fetch_object()){
			$extension = $reg->EXTENSION;
		}
		
		return $extension;
	}
	
	
}
?>