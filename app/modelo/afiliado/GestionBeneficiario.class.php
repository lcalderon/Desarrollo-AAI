<?php

	class GestionBeneficiario 
	 {
        public $valorPost;
        public $db;

        public function __construct($valorPost)
         {
            $this->valorPost= $valorPost;
			
			$this->db = new DB_mysqli();			
			//$this->db->select_db($this->db->catalogo);

         }
 
        public function grabar_beneficiario($id_afiliado)
         {

// grabar beneficiario persona

			if(!$this->valorPost["idbeneficiario"])
			 {	
				$beneficiarioper->IDAFILIADO = $id_afiliado;
				$beneficiarioper->IDUSUARIOCREACION = $_SESSION["user"];
				$beneficiarioper->FECHACREACION = date("Y-m-d H:i:s");
			 }
			
			$beneficiarioper->NOMBRE = strtoupper($this->valorPost["txtnombres"]);
			$beneficiarioper->APPATERNO = strtoupper($this->valorPost["txtpaterno"]);
			$beneficiarioper->APMATERNO = strtoupper($this->valorPost["txtmaterno"]);
			$beneficiarioper->IDTIPODOCUMENTO = strtoupper($this->valorPost["cmbtipodoc"]);
			$beneficiarioper->IDDOCUMENTO = strtoupper($this->valorPost["txtndocumento"]);
			$beneficiarioper->EMAIL1 = $this->valorPost["txtemail"];
			$beneficiarioper->EMAIL2 = $this->valorPost["txtemail2"];
			$beneficiarioper->EMAIL3 = $this->valorPost["txtemail3"];
			$beneficiarioper->ARRPARENTESCO = $this->valorPost["cmbparentesco"];
			$beneficiarioper->GENERO = strtoupper($this->valorPost["cmbgenero"]);
			$beneficiarioper->NEWREGISTRO = 1;
			$beneficiarioper->IDUSUARIOMOD = $_SESSION["user"];		
		 
			if(!$this->valorPost["idbeneficiario"])
			 {
				$resp_benf=$this->db->insert_reg("$db->catalogo.catalogo_afiliado_beneficiario",$beneficiarioper);
				
				$rowlog->IDBENEFICIARIO=$this->db->reg_id();
				$rowlog->IDAFILIADO=$id_afiliado;
				$rowlog->IDUSUARIOMOD=$_SESSION["user"];		
				if($resp_benf)	$this->db->insert_reg("$db->catalogo.catalogo_afiliado_beneficiario_log",$rowlog);				
			
			 }
			else
			 {
				$this->db->update("$db->catalogo.catalogo_afiliado_beneficiario",$beneficiarioper,"WHERE IDBENEFICIARIO='".$this->valorPost["idbeneficiario"]."'");
			 }
				
			if($resp_benf)	$id_beneficiario=$this->db->reg_id();		else	$id_beneficiario=$this->valorPost["idbeneficiario"];
			
			return $id_beneficiario;

         }

		public function grabar_telefonos($id_beneficiario)
		 {
				$rs_eliminatel=$this->db->query("delete from $db->catalogo.catalogo_afiliado_beneficiario_telefono where IDBENEFICIARIO=".$id_beneficiario."");
				
				if($rs_eliminatel)
				 { 
					foreach($this->valorPost["txttelefono$tipo"] as $indice => $numtelefono){

						if($numtelefono)
						 {
							$rowtele["IDBENEFICIARIO"]=$id_beneficiario;
							$rowtele["IDTIPOTELEFONO"]=$this->valorPost["cmbtelefono$indice$tipo"];
							$rowtele["CODIGOAREA"]=$this->valorPost["txtcodigoa$indice$tipo"];
							$rowtele["NUMEROTELEFONO"]=$numtelefono;
							$rowtele["EXTENSION"]=$this->valorPost["txtextension$indice$tipo"];
							$rowtele["IDTSP"]=$this->valorPost["cmbtsp$indice$tipo"];
							$rowtele["IDUSUARIOMOD"]=$_SESSION["user"];
							$rowtele["PRIORIDAD"]=$indice;

							$this->db->insert_reg("$db->catalogo.catalogo_afiliado_beneficiario_telefono",$rowtele);
						}
					}
				 }
         }

	}

// fin  class afiliado
?>
