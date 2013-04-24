<?
	session_start();  
	
	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
 
	$con= new DB_mysqli();	 
 
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	 
	$idmod=($_POST["cmbmodelo"])?$_POST["cmbmodelo"]:$datomodelo[0][0];
 	$sql="SELECT CLAVE FROM $con->temporal.acceso_transferencia_deficiencia where IDUSUARIO='".$_SESSION["user"]."' and IDMODELO='".$idmod."' order by ID";
 
	if($idmod)
	 {
		$rs=$con->query($sql);
		while($reg=$rs->fetch_object())
		 {	 
			$opcions[$reg->CLAVE]=$reg->CLAVE;
		 } 
	 } 
 
//cantidad de ubigeos a mostrar
	$nroubigeo=$con->lee_parametro("UBIGEO_NIVELES_ENTIDADES");
	
	$entidad1=str_replace(" ", "_",_("ENTIDAD 1"));
	$entidad2=str_replace(" ", "_",_("ENTIDAD 2"));
	$entidad3=str_replace(" ", "_",_("ENTIDAD 3"));
	$entidad4=str_replace(" ", "_",_("ENTIDAD 4"));
	$entidad5=str_replace(" ", "_",_("ENTIDAD 5"));
	$entidad6=str_replace(" ", "_",_("ENTIDAD 6"));
	
?>
        <br />
        <p align="right">
        <a href="javascript:seleccionar_todo()"><?=_("Marcar Todos") ;?></a> | 
        <a href="javascript:deseleccionar_todo()"><?=_("Desmarcar Todos") ;?></a></p>
 <strong>
        <?=_("EXPEDIENTE") ;?>
</strong><br />
        
		<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #6095CA">
          
          <tr>
            <td><input name="chkopciones1" type="checkbox" id="chkopciones1" value="IDCUENTA" checked disabled />
              <strong><?=_("IDEXP") ;?>.              </strong></td>
            <td><input name="chkopciones2" type="checkbox" id="chkopciones2" value="IDCUENTA" checked disabled />
              <strong>
              <?=_("IDASIST") ;?>.</strong></td>
            <td><input name="chkexpediente[]" type="checkbox" id="chkexpediente1" value="IDCUENTA" <?=($opcions["IDCUENTA"])?"checked":"" ;?> />
              <?=_("IDCUENTA") ;?></td>
            <td><input name="chkexpediente[]" type="checkbox" id="chkexpediente2" value="CUENTA" <?=($opcions["CUENTA"])?"checked":"" ;?>  />
              <?=_("NOMCUENTA") ;?></td>
            <td><input name="chkexpediente[]" type="checkbox" id="chkexpediente3" value="IDPLAN" <?=($opcions["IDPLAN"])?"checked":"" ;?>  />
              <?=_("IDPLAN") ;?></td>
            <td><input name="chkexpediente[]" type="checkbox" id="chkexpediente4" value="PLAN" <?=($opcions["PLAN"])?"checked":"" ;?>  />
              <?=_("NOMPLAN") ;?></td>
          </tr>
          <tr>
            <td><input name="chkexpediente[]" type="checkbox" id="chkexpediente5" value="NOMBRETITULAR" <?=($opcions["NOMBRETITULAR"])?"checked":"" ;?>  />
              <?=_("NOMTITULAR") ;?></td>
            <td><input name="chkexpediente[]" type="checkbox" id="chkexpediente6" value="CVEAFILIADO" <?=($opcions["CVEAFILIADO"])?"checked":"" ;?>  />
              <?=_("CVETITULAR") ;?></td>
            <td><input name="chkexpediente[]" type="checkbox" id="chkexpediente7" value="NOMBRECONTACTO" <?=($opcions["NOMBRECONTACTO"])?"checked":"" ;?>  />
              <?=_("NOMCONTACTO") ;?></td>
            <td><input name="chkexpediente[]" type="checkbox" id="chkexpediente8" value="NOMBREAUTORIZACION"<?=($opcions["NOMBREAUTORIZACION"])?"checked":"" ;?>  />
              <?=_("NOM.AUTORIZA") ;?></td>
            <td><input name="chkexpediente[]" type="checkbox" id="chkexpediente9" value="NUMEROAUTORIZACION" <?=($opcions["NUMEROAUTORIZACION"])?"checked":"" ;?>  />
              <?=_("NROAUTORIZACION") ;?></td>
            <td><input name="chkexpediente[]" type="checkbox" id="chkexpediente10" value="OBSERVACIONES" <?=($opcions["OBSERVACIONES"])?"checked":"" ;?>  />
              <?=_("OBSERVACION") ;?></td>
          </tr>
			<tr>
				<td><input name="chkexpediente[]" type="checkbox" id="chktiposerv1"  value="<?=$entidad1?>" <?=($opcions[$entidad1])?"checked":"" ;?>  /><?=_("ENTIDAD 1") ;?></td>
				<td><input name="chkexpediente[]" type="checkbox" id="chktiposerv2"  value="<?=$entidad2?>" <?=($opcions[$entidad2])?"checked":"" ;?>  /><?=_("ENTIDAD 2") ;?></td>
				<? if($nroubigeo >2){ ?><td><input name="chkexpediente[]" type="checkbox" id="chktiposerv3" value="<?=$entidad3?>" <?=($opcions[$entidad3])?"checked":"" ;?>  /><?=_("ENTIDAD 3") ;?></td> <?} else{?> <td colspan="3">&nbsp;</td> <? } ?>
				<? if($nroubigeo >3){ ?><td><input name="chkexpediente[]" type="checkbox" id="chktiposerv4" value="<?=$entidad4?>" <?=($opcions[$entidad4])?"checked":"" ;?>  /><?=_("ENTIDAD 4") ;?></td> <?} else{?> <td colspan="2">&nbsp;</td> <? } ?>
				<? if($nroubigeo >4){ ?><td><input name="chkexpediente[]" type="checkbox" id="chktiposerv5" value="<?=$entidad5?>" <?=($opcions[$entidad5])?"checked":"" ;?>  /><?=_("ENTIDAD 5") ;?></td> <?} else{?> <td colspan="1">&nbsp;</td> <? } ?>
				<? if($nroubigeo >5){ ?><td><input name="chkexpediente[]" type="checkbox" id="chktiposerv6" value="<?=$entidad6?>" <?=($opcions[$entidad6])?"checked":"" ;?>  /><?=_("ENTIDAD 6") ;?></td> <? } ?>
			</tr>		  
          <tr>            
            <td colspan="6"><input name="chkexpediente[]" type="checkbox" id="chkexpediente14" value="EXP_GARANTIA" <?=($opcions["EXP_GARANTIA"])?"checked":"" ;?>  />
              <?=_("REFER. GARANTIA") ;?></td>
          </tr>
          
        </table>
		
        <strong><?=_("ASISTENCIA") ;?></strong><br />
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #6095CA">        
          <tr>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia1" value="STATUSASISTENCIA" <?=($opcions["STATUSASISTENCIA"])?"checked":"" ;?>  />
              <?=_("STATUSASIS") ;?>.</td>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia2" value="IDSERVICIO" <?=($opcions["IDSERVICIO"])?"checked":"" ;?>  />
              <?=_("IDSERVICIO") ;?></td>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia3" value="SERVICIO" <?=($opcions["SERVICIO"])?"checked":"" ;?>  />
              <?=_("NOMBRESERVICIO") ;?></td>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia4" value="NOMBRECOMERCIALSERV" <?=($opcions["NOMBRECOMERCIALSERV"])?"checked":"" ;?>  />
                <?=_("NOM.COMERCIAL") ;?></td>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia5" value="PRIORIDAD_ATENCION" <?=($opcions["PRIORIDAD_ATENCION"])?"checked":"" ;?>  />
              <?=_("PRIO.ATENCION") ;?></td>
          </tr>
          <tr>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia6" value="CONDICIONSERVICIO" <?=($opcions["CONDICIONSERVICIO"])?"checked":"" ;?>  />
<?=_("CONDICIONSERV.") ;?></td>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia7" value="REEMBOLSO" <?=($opcions["REEMBOLSO"])?"checked":"" ;?>  />
<?=_("REEMBOLSO") ;?></td>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia8" value="COORDINADOR" <?=($opcions["COORDINADOR"])?"checked":"" ;?>  />
<?=_("COORDINADOR") ;?></td>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia9" value="AMBITO" <?=($opcions["AMBITO"])?"checked":"" ;?>  />
<?=_("AMBITO") ;?></td>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia10" value="DIRECCIONASIST" <?=($opcions["DIRECCIONASIST"])?"checked":"" ;?>  />
<?=_("DIRECCION ASIST.") ;?></td>
          </tr>
          <tr>
            <td><input name="chkasistencia[]" type="checkbox" id="chkasistencia11" value="NOMBREPROVEEDOR" <?=($opcions["NOMBREPROVEEDOR"])?"checked":"" ;?>  />
              <?=_("PROVEEDOR") ;?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>

        
         <strong>
        <?=_("DEFICIENCIA") ;?>
        </strong><br> 
 
         <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #6095CA">        
          <tr>
            <td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia1" value="CVEDEFICIENCIA" <?=($opcions["CVEDEFICIENCIA"])?"checked":"" ;?>  />
              <?=_("CVEDEFICIEN.") ;?></td>
            <td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia2" value="NOMBREDEFICIENCIA" <?=($opcions["NOMBREDEFICIENCIA"])?"checked":"" ;?>  />
              <?=_("DEFICIENCIENCIA") ;?></td>
            <td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia3" value="ETAPA" <?=($opcions["ETAPA"])?"checked":"" ;?>  />
              <?=_("ETAPA") ;?></td>
            <td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia4" value="ORIGENDEF" <?=($opcions["ORIGENDEF"])?"checked":"" ;?>  />
                <?=_("ORIGEN") ;?></td>
            <td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia5" value="IMPORTANCIADEF" <?=($opcions["IMPORTANCIADEF"])?"checked":"" ;?>  />
              <?=_("IMPORTANCIA") ;?></td>
            <td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia6" value="MEDIOASIGNACION" <?=($opcions["MEDIOASIGNACION"])?"checked":"" ;?>  />
              <?=_("MEDIO ASIGNAC.") ;?></td>
          </tr>
          <tr>
            <td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia7" value="OBSASIGDEF" <?=($opcions["OBSASIGDEF"])?"checked":"" ;?>  />
			<?=_("OBS ASIG. DEF") ;?></td>
						<td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia8" value="SUPERVISORDEF" <?=($opcions["SUPERVISORDEF"])?"checked":"" ;?>  />
			<?=_("SUPERV.CONFIR./RECH. DEF") ;?></td>
						<td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia9" value="FECHADEF" <?=($opcions["FECHADEF"])?"checked":"" ;?>  />
			<?=_("FECHADEF.") ;?></td>
						<td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia10" value="STATUSDEF" <?=($opcions["STATUSDEF"])?"checked":"" ;?>  />
			<?=_("STATUSDEF.") ;?></td>
 			<td><input name="chkdeficiencia[]" type="checkbox" id="chkdeficiencia10" value="USUARIODEF" <?=($opcions["USUARIODEF"])?"checked":"" ;?>  />
			<?=_("USUARIODEF.") ;?></td>
 
          </tr>
        </table>

		<!--strong>
        <?=_("ENCUESTA") ;?>
        </strong><br> 
		
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #6095CA">        
          <tr>
            <td><input name="chkencuesta[]" type="checkbox" id="chkencuesta1" value="CALIFENCUESTA" <?=($opcions["CALIFENCUESTA"])?"checked":"" ;?>  />
              <?=_("CALIFICAC. ENCUESTA") ;?>.</td>
            <td><input name="chkencuesta[]" type="checkbox" id="chkencuesta2" value="DECSCALIFICACION" <?=($opcions["DECSCALIFICACION"])?"checked":"" ;?>  />
              <?=_("DESC CALIFICACION") ;?></td>
            <td><input name="chkencuesta[]" type="checkbox" id="chkencuesta3" value="SUPERVISORENCUESTA" <?=($opcions["SUPERVISORENCUESTA"])?"checked":"" ;?>  />
              <?=_("SUPERVISOR ENCUESTA") ;?></td>  
			<td><input name="chkencuesta[]" type="checkbox" id="chkencuesta3" value="FECHAENCUESTA" <?=($opcions["FECHAENCUESTA"])?"checked":"" ;?>  />
              <?=_("FECHA ENCUESTA") ;?></td>
          </tr>          
        </table>
			 
		<strong>
        <?=_("AUDITORIA") ;?>
        </strong><br> 
		
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #6095CA">        
          <tr>
            <td><input name="chkauditoria[]" type="checkbox" id="chkauditoria1" value="SUPERVAUDITORIA" <?=($opcions["SUPERVAUDITORIA"])?"checked":"" ;?>  />
              <?=_("SUPERVISOR AUDITORIA") ;?></td>
            <td><input name="chkauditoria[]" type="checkbox" id="chkauditoria2" value="EVALUACIONAUDITOR" <?=($opcions["EVALUACIONAUDITOR"])?"checked":"" ;?>  />
              <?=_("EVALUACION AUDITORIA") ;?></td>  
			<td><input name="chkauditoria[]" type="checkbox" id="chkauditoria3" value="USUARIO_AUDITA" <?=($opcions["USUARIO_AUDITA"])?"checked":"" ;?> /><?=_("USUARIO AUDITA") ;?></td>
			<td><input name="chkauditoria[]" type="checkbox" id="chkauditoria4" value="FECHA_AUDITA" <?=($opcions["FECHA_AUDITA"])?"checked":"" ;?> /><?=_("FECHA AUDITA") ;?></td>
          </tr>          
        </table-->
		 
        <strong>
        <?=_("FECHAS") ;?>
        </strong><br>
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF4F4" style="border:1px dashed #6095CA">
          <tr>
            <td><input name="chkfecha[]" type="checkbox" id="chkfecha4" value="FECHA_APERTURA_EXP" <?=($opcions["FECHA_APERTURA_EXP"])?"checked":"" ;?>  />
              <?=_("FECHA APER. EXPED.") ;?></td>
            <td><input name="chkfecha[]" type="checkbox" id="chkfecha1" value="HORA_APERTURA_EXP" <?=($opcions["HORA_APERTURA_EXP"])?"checked":"" ;?>  />
                <?=_("HORA APER. EXPED.") ;?></td>
            <td><input name="chkfecha[]" type="checkbox" id="chkfecha3" value="FECHA_REGISTRO_ASIS" <?=($opcions["FECHA_REGISTRO_ASIS"])?"checked":"" ;?>  />
              <?=_("FECHA REGIS. ASISTENCIA") ;?></td>
            <td><input name="chkfecha[]" type="checkbox" id="chkfecha2" value="HORA_REGISTRO_ASIS" <?=($opcions["HORA_REGISTRO_ASIS"])?"checked":"" ;?>  />
                <?=_("HORA REGIS. ASISTENCIA") ;?></td>
          </tr>
        </table>
