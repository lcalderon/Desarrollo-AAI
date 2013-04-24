<?php
	class Auth
	{
		public static function required($url){

			$conex = new DB_mysqli();
			$usuarioValido=$conex->consultation("SELECT DATO FROM $conex->catalogo.catalogo_parametro WHERE IDPARAMETRO='USUARIO_SOAANG_LOGIN'");
			
			if(!$_SESSION["user"] and $_SESSION["userhost"] != $usuarioValido[0][0]){
		
				$urlTotal="?urlacces=".urlencode($url);
				echo $urlTotal;
				header("Location:/app/vista/login/respuesta.php".$urlTotal);
			}
			
			if(!$_SESSION["user"])	break;
		}

		public static function verifyAccess($tipo)
		 {
			
			if(!empty($_SESSION["user"]))
			 {
				$conex = new DB_mysqli();	
				
				$sql="
				select 
					$conex->temporal.seguridadmodulo.IDMODULO,
					if($conex->temporal.seguridadmodulo.TIPO='MENU_CAT',substr($conex->temporal.seguridadmodulo.DESCRIPCION,10,20),
					$conex->temporal.seguridadmodulo.DESCRIPCION) as DESCRIPCION,
					$conex->temporal.seguridadmodulo.UBICACION,
					$conex->temporal.seguridadmodulo.TARGET
				from 
					$conex->temporal.seguridad_modulosxusuario
				 	inner join $conex->temporal.seguridadmodulo on $conex->temporal.seguridadmodulo.IDMODULO=$conex->temporal.seguridad_modulosxusuario.IDMODULO 
   				where 
   					$conex->temporal.seguridad_modulosxusuario.IDUSUARIO='".$_SESSION["user"]."' 
   					AND $conex->temporal.seguridadmodulo.TIPO='$tipo' 
   				order by $conex->temporal.seguridadmodulo.DESCRIPCION";
 //echo $sql."--";
				$result = $conex->query($sql);
				while($reg = $result->fetch_object())
				 {
					$codigos[]=$reg->IDMODULO;
					$nombres[]=$reg->DESCRIPCION;
					$ubicaciones[]=$reg->UBICACION;
					$target[]=$reg->TARGET;
				
				 }
			 }
			 
			return array($codigos,$nombres,$ubicaciones,$target);
		 }
		 
	}
?>