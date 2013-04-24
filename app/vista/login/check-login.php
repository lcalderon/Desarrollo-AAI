<?  
	// $adminLogin = 0;
	// if(isset($_SESSION['user'])) $adminLogin = 1;
	$varid=session_id();
	
	include_once('../../modelo/clase_mysqli.inc.php');

		
	$con = new DB_mysqli(); 
		
	if($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	
?>