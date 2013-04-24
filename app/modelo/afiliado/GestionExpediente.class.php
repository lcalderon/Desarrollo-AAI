<?php

	class GestionExpediente 
	 {
        public $valorPost;
        public $db;

        public function __construct($valorPost)
         {
            $this->valorPost= $valorPost;
			
			$this->db = new DB_mysqli();			
			$this->db->select_db($this->db->catalogo);
         } 
 
        public function grabar_expediente()
         {					
				if(!$this->valorPost["idexpediente"])
				  {	
					 $newexpediente->IDEXPEDIENTE = "";
					 $newexpediente->IDEXPEDIENTE_REFERENTE = $this->valorPost["idexpediente2"];
					 $newexpediente->FECHAREGISTRO = date("Y-m-d H:i:s");
				  }

				$newexpediente->IDAFILIADO = strtoupper($this->valorPost["idafiliado"]);
				$newexpediente->IDCUENTA = strtoupper($this->valorPost["cmbcuentatitular"]);
				$newexpediente->IDPROGRAMA =$this->valorPost["cmbprogramatitular"];
				$newexpediente->ARRSTATUSEXPEDIENTE =$this->valorPost["cmbstatusexp"];			 
				$newexpediente->OBSERVACIONES = remplazar_enes_tildes(utf8_encode($this->valorPost["txtobservacion"]));
				$newexpediente->ARRTIPOAUTORIZACION = strtoupper($this->valorPost["cmbautorizacion"]);
				$newexpediente->NUMAUTORIZACION = strtoupper($this->valorPost["txtnumautoriza"]);
				$newexpediente->NOMAUTORIZACION = remplazar_enes_tildes(utf8_encode($this->valorPost["txtnomautoriza"]));
				$newexpediente->ANI =strtoupper($this->valorPost["txtani"]);	
				$newexpediente->IDUSUARIOMOD =$_SESSION["user"];	
				$newexpediente->CVEAFILIADO =strtoupper($this->valorPost["txtclavetitular"]);
 			
				if(!$this->valorPost["idexpediente"])	$resp_expediente=$this->db->insert_reg($this->db->temporal.".expediente",$newexpediente);	else	$this->db->update($this->db->temporal.".expediente",$newexpediente,"WHERE expediente.IDEXPEDIENTE='".$this->valorPost["idexpediente"]."'");
			
				if($resp_expediente)	$id_expediente=$this->db->reg_id();		else	$id_expediente=$this->valorPost["idexpediente"];
			
			//grabando expediente-usuario

				$rowexpusu->IDEXPEDIENTE = $id_expediente;
				$rowexpusu->FECHAHORA = date("Y-m-d H:i:s");
				$rowexpusu->IDUSUARIO = $_SESSION["user"];
				$rowexpusu->ARRTIPOMOVEXP = "REG";	

				$rowexpusu2->IDEXPEDIENTE = $id_expediente;
				$rowexpusu2->FECHAHORA = date("Y-m-d H:i:s");
				$rowexpusu2->IDUSUARIO = $_SESSION["user"];
				$rowexpusu2->ARRTIPOMOVEXP = "SEG";
				
				$rowexpusu3->IDEXPEDIENTE = $id_expediente;
				$rowexpusu3->FECHAHORA =  $this->valorPost["fechaapertura"];
				$rowexpusu3->IDUSUARIO = $_SESSION["user"];
				$rowexpusu3->ARRTIPOMOVEXP = "APE";	
				
				$rowexpusu4->IDEXPEDIENTE = $id_expediente;
				$rowexpusu4->FECHAHORA =  $this->valorPost["fechasac"];
				$rowexpusu4->IDUSUARIO = $_SESSION["user"];
				$rowexpusu4->ARRTIPOMOVEXP = "TVA";
				

				if($id_expediente and !$this->valorPost["idexpediente"])		$this->db->insert_reg($this->db->temporal.".expediente_usuario",$rowexpusu);
				if($id_expediente and !$this->valorPost["idexpediente"])		$this->db->insert_reg($this->db->temporal.".expediente_usuario",$rowexpusu3);
				if($id_expediente and !$this->valorPost["idexpediente"])		$this->db->insert_reg($this->db->temporal.".expediente_usuario",$rowexpusu4);
				$this->db->insert_reg($this->db->temporal.".expediente_usuario",$rowexpusu2);

			return $id_expediente;
			
         }

        public function grabar_persona($tipo,$expediente,$idpersona)
         {
			$newpersona->IDEXPEDIENTE = $expediente;
			$newpersona->ARRTIPOPERSONA = strtoupper($tipo);
			$newpersona->NOMBRE = strtoupper($this->valorPost["txtnombre$tipo"]);
			$newpersona->APPATERNO = strtoupper($this->valorPost["txtpaterno$tipo"]);
			$newpersona->APMATERNO = strtoupper($this->valorPost["txtmaterno$tipo"]);
			$newpersona->IDTIPODOCUMENTO = strtoupper($this->valorPost["cmbtipodoc$tipo"]);
			$newpersona->IDDOCUMENTO = strtoupper($this->valorPost["txtnumdoc$tipo"]);
			$newpersona->DIGITOVERIFICADOR = strtoupper($this->valorPost["txtdig$tipo"]);
			if($tipo=="titular")	$newpersona->CVETITULAR = strtoupper($this->valorPost["txtclavetitular"]);
			
			//$id_persona=($tipo=="titular")?$this->valorPost["titular"]:$this->valorPost["contacto"];

			if($this->valorPost["idexpediente"] and $idpersona)	$this->db->update($this->db->temporal.".expediente_persona",$newpersona,"WHERE IDPERSONA='".$idpersona."'"); else if($expediente) $resp_persona=$this->db->insert_reg($this->db->temporal.".expediente_persona",$newpersona);
			 
			if($resp_persona)	$idpersona=$this->db->reg_id();
		 
			return $idpersona;
         }

		public function grabar_telefonos($persona,$tipo)
		 {
				$rs_eliminatel=$this->db->query("delete from ".$this->db->temporal.".expediente_persona_telefono where IDPERSONA=".$persona."");
				
				if($rs_eliminatel)
				 { 

					foreach($this->valorPost["txttelefono$tipo"] as $indice => $numtelefono){

						if($numtelefono)
						 {
							$rowtele["IDPERSONA"]=$persona;
							$rowtele["IDTIPOTELEFONO"]=$this->valorPost["cmbtelefono$indice$tipo"];
							$rowtele["CODIGOAREA"]=$this->valorPost["txtcodigoa$indice$tipo"];
							$rowtele["NUMEROTELEFONO"]=$numtelefono;
							$rowtele["EXTENSION"]=$this->valorPost["txtextension$indice$tipo"];
							$rowtele["IDTSP"]=$this->valorPost["cmbtsp$indice$tipo"];
							$rowtele["IDUSUARIOMOD"]	=$_SESSION["user"]; 
							$rowtele["PRIORIDAD"]=$indice;
							
							$this->db->insert_reg($this->db->temporal.".expediente_persona_telefono",$rowtele);
							
						}
					}
				 }
         }

	} 		 

// fin  class expediente
?>
