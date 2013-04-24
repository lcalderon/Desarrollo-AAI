<?php

	class ActionUser
	{
		//private $config;
		private $isLogin = false;
		private $user;
	
	    public function __construct($user)
         {
			// if(!is_array($config))
			// die("Error en la configuraci&oacute;n del login");
				
			$this->user = $user;

         }
   	
		public function vigenciamaxima()
		{
			$db = new DB_mysqli();
			$rs = $db->consultation("select DATEDIFF(NOW(),(select MAX(FECHA_MOD) from $db->temporal.seguridad_accesos where  CVEUSUARIOMOVIMIENTO in('UN','RC') and  CVEUSUARIO='".$this->user."' ) ) AS maxmes,DATEDIFF(NOW(),(select MAX(FECHA_MOD) from $db->temporal.seguridad_accesos where  CVEUSUARIOMOVIMIENTO ='LOGIN' and  CVEUSUARIO='".$this->user."' )) as maxsemana from $db->temporal.seguridad_accesos where CVEUSUARIO='".$this->user."' LIMIT 1");
			$maxmes= $rs[0]["maxmes"];
			$maxsemana= $rs[0]["maxsemana"];
			
			return array($maxmes,$maxsemana);
		
		}
		
		public function permisocambiopass()
		{
			$db = new DB_mysqli();
			$rs = $db->consultation("select REINICIACONTRASENIA as accesopass from $db->catalogo.catalogo_usuarios where CVEUSUARIO='".$this->user."'");
			
			return $rs[0]["accesopass"];
			 
		}
		
		public function logout()
		{
			unset($_SESSION); 
			session_destroy();
			header("Location:../login/");
		}		
		
   
	}
	
	

?>