<?php

	class GestionAfiliado 
	 {
        public $valorPost;
        public $db;

        public function __construct($valorPost)
         {
            $this->valorPost= $valorPost;
			
			$this->db = new DB_mysqli();			
			$this->db->select_db($this->db->catalogo);

         }
 
        public function grabar_afiliado(){
			
			if($this->valorPost["ckbvicio"])	$this->valorPost["cmbstatusys"]="SINVALIDAR";
			
			$newafiliado->CVEAFILIADO = strtoupper($this->valorPost["txtidentificador"]);
			$newafiliado->ARRORIGENTABLA = "PERSONAS";
			$newafiliado->IDCUENTA = strtoupper($this->valorPost["cmbcuenta"]);
			$newafiliado->IDPROGRAMA = strtoupper($this->valorPost["cmbprograma"]);
			$newafiliado->IDCANALVENTA = strtoupper($this->valorPost["cmbcanal"]);
			$newafiliado->FECHAINICIOVIGENCIA = strtoupper($this->valorPost["txtfechaini"]);
			$newafiliado->FECHAFINVIGENCIA = strtoupper($this->valorPost["txtfechafin"]);
			$newafiliado->STATUSASISTENCIA = strtoupper($this->valorPost["cmbstatus"]);
			$newafiliado->STATUSCOMERCIAL = strtoupper($this->valorPost["cmbstatus"]);
			$newafiliado->AFILIADO_SISTEMA =$this->valorPost["cmbstatusys"];
			$newafiliado->PERSONAVICIO = $this->valorPost["ckbvicio"];
			$newafiliado->IDUSUARIOMOD = $_SESSION["user"];
			$newafiliado->ARRMODALIDADPG = $this->valorPost["cmbmodalidad"];
			$newafiliado->FECHACREACION = date("Y-m-d H:i:s");
			$newafiliado->IDUSUARIOCREACION = $_SESSION["user"];
			
// grabar afiliado
			 
			$this->db->insert_reg("catalogo_afiliado",$newafiliado);
			
			$id_afiliado=$this->db->reg_id();
			
// grabar afiliado persona

			$newafiliadoper->IDAFILIADO = $id_afiliado;
			$newafiliadoper->NOMBRE = strtoupper($this->valorPost["txtnombres"]);
			$newafiliadoper->APPATERNO = strtoupper($this->valorPost["txtpaterno"]);
			$newafiliadoper->APMATERNO = strtoupper($this->valorPost["txtmaterno"]);
			$newafiliadoper->IDTIPODOCUMENTO = strtoupper($this->valorPost["cmbtipodoc"]);
			$newafiliadoper->IDDOCUMENTO = strtoupper($this->valorPost["txtndocumento"]);
			$newafiliadoper->EMAIL1 = $this->valorPost["txtemail"];
			$newafiliadoper->EMAIL2 = $this->valorPost["txtemail2"];
			$newafiliadoper->EMAIL3 = $this->valorPost["txtemail3"];
			$newafiliadoper->GENERO = strtoupper($this->valorPost["cmbgenero"]);
			$newafiliadoper->IDUSUARIOMOD = $_SESSION["user"];		
			$newafiliadoper->NEWREGISTRO = 1;		
			
//grabar canal de venta
			
			$rowcanal["IDAFILIADO"]=$id_afiliado;
			$rowcanal["IDCANALVENTA"]=$_POST['cmbcanal'];			
		
//nuevo registro tabla retencion
				
				$rowcaso["IDRETENCION"]="";
				$rowcaso["FECHARETENCION"]=date("Y-m-d H:i:s");
				$rowcaso["IDCUENTA"]=$_POST['cmbcuenta'];
				$rowcaso["IDPROGRAMA"]=$_POST['cmbprograma'];
				$rowcaso["IDAFILIADO"]=$id_afiliado;
				$rowcaso["MOTIVOLLAMADA"]=$_POST['txtmotllamada'];
				$rowcaso["IDDETMOTIVOLLAMADA"]=$_POST['cmbopciones'];
				$rowcaso["MESREINTEGRO"]=$_POST['txtmesreitegro'];
				$rowcaso["STATUS_RETENCION_AFILIADO"]="SIN VALIDAR";
				$rowcaso["STATUS_SEGUIMIENTO"]="REC";
				$rowcaso["IDGRUPO"]=$_POST['cmbasignacionc'];
				$rowcaso["ARRPROCEDENCIA"]=$_POST['cmbprocedencia'];
				$rowcaso["COMENTARIO"]=strtoupper($_POST["txtacomentario"]);
				$rowcaso["IDUSUARIO"]=$_SESSION["user"];

				if($id_afiliado)
				 {
					$this->db->insert_reg("catalogo_afiliado_persona",$newafiliadoper);
					if($_POST['cmbcanal']!="")	$this->db->insert_reg("catalogo_afiliado_canalventa",$rowcanal);				
					$this->db->insert_reg($this->db->temporal.".retencion",$rowcaso);				
				 }
		 
			return $id_afiliado;
        }

		public function grabar_telefonos($idafiliado)
		 {
 				
				//if($rs_eliminatel)	$rs_eliminatel2=$this->db->query("delete from catalogo_afiliado_persona_telefono where IDAFILIADO='".$idafiliado."'");				
				if($rs_eliminatel2 or $datocodigos[0][0] == null)
				 { 

					foreach($this->valorPost["txttelefono$tipo"] as $indice => $numtelefono){

						if($numtelefono)
						 {
							$rowtele["IDAFILIADO"]=$idafiliado;
							$rowtele["IDTIPOTELEFONO"]=$this->valorPost["cmbtelefono$indice$tipo"];
							$rowtele["CODIGOAREA"]=$this->valorPost["txtcodigoa$indice$tipo"];
							$rowtele["NUMEROTELEFONO"]=$numtelefono;
							$rowtele["EXTENSION"]=$this->valorPost["txtextension$indice$tipo"];
							$rowtele["IDTSP"]=$this->valorPost["cmbtsp$indice$tipo"];
							$rowtele["IDUSUARIOMOD"]	=$_SESSION["user"];
							$rowtele["PRIORIDAD"]	=$indice;

							$respuesta_telefono=$this->db->insert_reg("catalogo_afiliado_persona_telefono",$rowtele);					 	
							
						}
					}
				 }
         }

	}

// fin  class afiliado
?>
