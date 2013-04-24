<?php



					
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	if($_GET['action'] == "logout")
	{
		$login = new Login();
		$login->logout();

		//setInfo("Su sesi&oacute;n ha sido cerrada correctamente.");
		
	}
	else if($_GET['action'] == "pwd")
	{

		
		
	}
		
?>