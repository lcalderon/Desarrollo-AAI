<?
	session_start();  
	
	include_once("../../../modelo/clase_lang.inc.php");
	include_once("../../../modelo/clase_mysqli.inc.php");
 	include_once('../../../modelo/functions.php');

    $con= new DB_mysqli();
	
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	}
	 
	$idmod=($_POST["cmbmodelo"])?$_POST["cmbmodelo"]:$datomodelo[0][0];
 	$sql="SELECT CLAVE FROM $con->temporal.acceso_transferencia_deficiencia where IDUSUARIO='".$_SESSION["user"]."' and IDMODELO='".$idmod."' ORDER BY ID";

	if($idmod){
	
		$rs=$con->query($sql);
		while($reg=$rs->fetch_object()) $opcions[$reg->CLAVE]=$reg->CLAVE;
	} 

//cantidad de ubigeos a mostrar
	$nroubigeo=$con->lee_parametro("UBIGEO_NIVELES_ENTIDADES");
	
	$entidad1=quitar_caracteresEspeciales(_("ENTIDAD 1"));
	$entidad2=quitar_caracteresEspeciales(_("ENTIDAD 2"));
	$entidad3=quitar_caracteresEspeciales(_("ENTIDAD 3"));
	$entidad4=quitar_caracteresEspeciales(_("ENTIDAD 4"));
	$entidad5=quitar_caracteresEspeciales(_("ENTIDAD 5"));
	$entidad6=quitar_caracteresEspeciales(_("ENTIDAD 6"));
?>
        <br/>
        <p align="right">
        <a href="javascript:seleccionar_todo()"><?=_("Marcar Todos") ;?></a> | 
        <a href="javascript:deseleccionar_todo()"><?=_("Desmarcar Todos") ;?></a></p>
		<strong><?=_("EXPEDIENTE") ;?></strong><br/>
        
		<table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #CC3366">          
			<tr>
				<td><input name="chkopciones9" type="checkbox" id="chkopciones37" value="IDCUENTA" checked disabled /><strong><?=_("IDEXP") ;?>.</strong></td>
				<td><input name="chkopciones10" type="checkbox" id="chkopciones38" value="IDCUENTA" checked disabled /><strong><?=_("IDASIST") ;?>.</strong></td>
				<td><input name="chkopcion2[]" type="checkbox" id="chkopciones8" value="IDCUENTA" <?=($opcions["IDCUENTA"])?"checked":"" ;?> /><?=_("IDCUENTA") ;?></td>
				<td><input name="chkopcion2[]" type="checkbox" id="chkopciones36" value="CUENTA" <?=($opcions["CUENTA"])?"checked":"" ;?>  /><?=_("NOMBRECUENTA") ;?></td>
				<td><input name="chkopcion2[]" type="checkbox" id="chkopciones35" value="IDPLAN" <?=($opcions["IDPLAN"])?"checked":"" ;?>  /><?=_("IDPLAN") ;?></td>
				<td><input name="chkopcion2[]" type="checkbox" id="chkopciones34" value="PLAN" <?=($opcions["PLAN"])?"checked":"" ;?>  /><?=_("NOMBREPLAN") ;?></td>
			</tr>
			<tr>
				<td><input name="chkopcion5[]" type="checkbox" id="chkopciones9" value="NOMBRETITULAR" <?=($opcions["NOMBRETITULAR"])?"checked":"" ;?>  /><?=_("NOMBRETITULAR") ;?></td>
				<td><input name="chkopcion5[]" type="checkbox" id="chkopciones10" value="CVEAFILIADO" <?=($opcions["CVEAFILIADO"])?"checked":"" ;?>  /><?=_("CLAVETITULAR") ;?></td>
				<td><input name="chkopcion5[]" type="checkbox" id="chkopciones11" value="NOMBRECONTACTO" <?=($opcions["NOMBRECONTACTO"])?"checked":"" ;?>  /><?=_("NOMBRECONTACTO") ;?></td>
				<td><input name="chkopcion5[]" type="checkbox" id="chkstatusasist2" value="NOMBREAUTORIZACION"<?=($opcions["NOMBREAUTORIZACION"])?"checked":"" ;?>  /><?=_("NOM.AUTORIZA") ;?></td>
				<td><input name="chkopcion5[]" type="checkbox" id="chkopciones12" value="NUMEROAUTORIZACION" <?=($opcions["NUMEROAUTORIZACION"])?"checked":"" ;?>  /><?=_("NROAUTORIZACION") ;?></td>
				<td><input name="chkopcion5[]" type="checkbox" id="chkopciones14" value="OBSERVACIONES" <?=($opcions["OBSERVACIONES"])?"checked":"" ;?>  /><?=_("OBSERVACION") ;?></td>
			</tr>
			<tr>
				<td><input name="chkopcion5[]" type="checkbox" id="chktiposerv1"  value="<?=$entidad1?>" <?=($opcions[$entidad1])?"checked":"" ;?>  /><?=_("ENTIDAD 1") ;?></td>
				<td><input name="chkopcion5[]" type="checkbox" id="chktiposerv2"  value="<?=$entidad2?>" <?=($opcions[$entidad2])?"checked":"" ;?>  /><?=_("ENTIDAD 2") ;?></td>
				<? if($nroubigeo >2){ ?><td><input name="chkopcion5[]" type="checkbox" id="chktiposerv3" value="<?=$entidad3?>" <?=($opcions[$entidad3])?"checked":"" ;?>  /><?=_("ENTIDAD 3") ;?></td> <?} else{?> <td colspan="3">&nbsp;</td> <? } ?>
				<? if($nroubigeo >3){ ?><td><input name="chkopcion5[]" type="checkbox" id="chktiposerv4" value="<?=$entidad4?>" <?=($opcions[$entidad4])?"checked":"" ;?>  /><?=_("ENTIDAD 4") ;?></td> <?} else{?> <td colspan="2">&nbsp;</td> <? } ?>
				<? if($nroubigeo >4){ ?><td><input name="chkopcion5[]" type="checkbox" id="chktiposerv5" value="<?=$entidad5?>" <?=($opcions[$entidad5])?"checked":"" ;?>  /><?=_("ENTIDAD 5") ;?></td> <?} else{?> <td colspan="1">&nbsp;</td> <? } ?>
				<? if($nroubigeo >5){ ?><td><input name="chkopcion5[]" type="checkbox" id="chktiposerv6" value="<?=$entidad6?>" <?=($opcions[$entidad6])?"checked":"" ;?>  /><?=_("ENTIDAD 6") ;?></td> <? } ?>
			</tr>
			<tr>
				<td><input name="chkopcion5[]" type="checkbox" id="chkserviciopor2" value="EXP_GARANTIA" <?=($opcions["EXP_GARANTIA"])?"checked":"" ;?>  /><?=_("REFER. GARANTIA") ;?></td>
				<td><input name="chkopcion5[]" type="checkbox" id="chktelefono1" value="EXP_TELEFONO1" <?=($opcions["EXP_TELEFONO1"])?"checked":"" ;?>  /><?=_("TELEFONO1") ;?></td>
				<td><input name="chkopcion5[]" type="checkbox" id="chktelefono2" value="EXP_TELEFONO2" <?=($opcions["EXP_TELEFONO2"])?"checked":"" ;?>  /><?=_("TELEFONO2") ;?></td>			
				<td><input name="chkopcion5[]" type="checkbox" id="chkpoliza" value="POLIZA" <?=($opcions["POLIZA"])?"checked":"" ;?>  /><?=_("POLIZA") ;?></td>
				<td><input name="chkopcion5[]" type="checkbox" id="chkasigdiferida" value="ASIG_DIFERIDA" <?=($opcions["ASIG_DIFERIDA"])?"checked":"" ;?> title="Asignaci&oacute;n Diferida" /><?=_("ASIG. DIFERIDA") ;?></td>
				<td><input name="chkopcion5[]" type="checkbox" id="chkopciones29" value="COORDINADOR_APERTURA_EXP" <?=($opcions["COORDINADOR_APERTURA_EXP"])?"checked":"" ;?>/><?=_("COORDINADOR APERTURA EXP.") ;?></td>				
			</tr>			
        </table>		
        <strong><?=_("ASISTENCIA") ;?></strong><br />
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #CC3366">        
			<tr>
				<td><input name="chkopcion3[]" type="checkbox" id="chkopciones31" value="STATUSASISTENCIA" <?=($opcions["STATUSASISTENCIA"])?"checked":"" ;?>  /><?=_("STATUSASIS") ;?>.</td>
				<td><input name="chkopcion3[]" type="checkbox" id="chkopciones33" value="IDSERVICIO" <?=($opcions["IDSERVICIO"])?"checked":"" ;?>  /><?=_("IDSERVICIO") ;?></td>
				<td><input name="chkopcion3[]" type="checkbox" id="chkopciones32" value="SERVICIO" <?=($opcions["SERVICIO"])?"checked":"" ;?>  /><?=_("NOMBRESERVICIO") ;?></td>
				<td><input name="chkopcion3[]" type="checkbox" id="chkopciones15" value="NOMBRECOMERCIALSERV" <?=($opcions["NOMBRECOMERCIALSERV"])?"checked":"" ;?>  /><?=_("NOM.COMERCIAL") ;?></td>
				<td><input name="chkopcion6[]" type="checkbox" id="chkopciones16" value="PRIORIDAD_ATENCION" <?=($opcions["PRIORIDAD_ATENCION"])?"checked":"" ;?>  /><?=_("PRIO.ATENCION") ;?></td>
			</tr>
            <tr>
                <td><input name="chkopcion6[]" type="checkbox" id="chkopciones17" value="CONDICIONSERVICIO" <?=($opcions["CONDICIONSERVICIO"])?"checked":"" ;?>/><?=_("CONDICIONSERV.") ;?></td>
                <td><input name="chkopcion6[]" type="checkbox" id="chkopciones19" value="REEMBOLSO" <?=($opcions["REEMBOLSO"])?"checked":"" ;?>/><?=_("REEMBOLSO") ;?></td>
                <td><input name="chkopcion6[]" type="checkbox" id="chkopciones20" value="COORDINADOR_SEGUIMIENTO" <?=($opcions["COORDINADOR_SEGUIMIENTO"])?"checked":"" ;?>/><?=_("COORDINADOR SEGUIMIENTO") ;?></td>
                <td><input name="chkopcion6[]" type="checkbox" id="chkopciones18" value="AMBITO" <?=($opcions["AMBITO"])?"checked":"" ;?>  /><?=_("AMBITO") ;?></td>
                <td><input name="chkopcion7[]" type="checkbox" id="chkopciones" value="DIRECCIONASIST" <?=($opcions["DIRECCIONASIST"])?"checked":"" ;?> /><?=_("DIRECCION ASIST.") ;?></td>
            </tr>	
            <tr>
				<td><input name="chkopcion6[]" type="checkbox" id="chkopciones17" value="FAMILIASERVICIO" <?=($opcions["FAMILIASERVICIO"])?"checked":"" ;?>/><?=_("FAMILIA SERV.") ;?></td>
				<td><input name="chkopcion6[]" type="checkbox" id="chketapa" value="NUMERO_ETAPA" title="N&uacute;mero de etapa" <?=($opcions["NUMERO_ETAPA"])?"checked":"" ;?>/><?=_("NUMERO ETAPA") ;?></td>
				<td><input name="chkopcion6[]" type="checkbox" id="chkopciones20" value="COORDINADOR_APERTURA_ASIS" <?=($opcions["COORDINADOR_APERTURA_ASIS"])?"checked":"" ;?>/><?=_("COORDINADOR APERTURA ASIS.") ;?></td>
				<td colspan="2"><input name="chkopcion6[]" type="checkbox" id="chkopciones20" value="DESCRIPCION_OCURRIDO" <?=($opcions["DESCRIPCION_OCURRIDO"])?"checked":"" ;?>/><?=_("DESCRIP. DE LO OCURRIDO") ;?></td>
				
			</tr>
        </table>
        <strong><?=_("VEHICULAR") ;?></strong><br />
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #CC3366">
			<tr>
				<td><input name="chkopcion4[]" type="checkbox" id="chkopciones4" value="SERVICIOPOR" <?=($opcions["SERVICIOPOR"])?"checked":"" ;?>  /><?=_("SERVICIOPOR") ;?></td>
				<td><input name="chkopcion4[]" type="checkbox" id="chkopciones24" value="TIPOSERVICIOVIAL" <?=($opcions["TIPOSERVICIOVIAL"])?"checked":"" ;?> /><?=_("TIPOSERV") ;?>.</td>
				<td><input name="chkopcvehicular[]" type="checkbox" id="chkopciones13" value="MARCA" <?=($opcions["MARCA"])?"checked":"" ;?>  /><?=_("MARCA") ;?></td>
				<td><input name="chkopcvehicular[]" type="checkbox" id="chkopciones22" value="MODELO" <?=($opcions["MODELO"])?"checked":"" ;?>  /><?=_("MODELO") ;?></td>
				<td><input name="chkopcvehicular[]" type="checkbox" id="chkopciones5" value="NUMSERIECHASIS" <?=($opcions["NUMSERIECHASIS"])?"checked":"" ;?>  /><?=_("NRO CHASIS") ;?></td>
			</tr>
			<tr>
				<td><input name="chkopcvehicular[]" type="checkbox" id="chkopciones6" value="NUMSERIEMOTOR" <?=($opcions["NUMSERIEMOTOR"])?"checked":"" ;?>  /><?=_("NRO MOTOR") ;?></td>
				<td><input name="chkopcvehicular[]" type="checkbox" id="chkopciones7" value="COLORVEH" <?=($opcions["COLORVEH"])?"checked":"" ;?>  /><?=_("COLOR") ;?></td>
				<td><input name="chkopcvehicular[]" type="checkbox" id="chkopciones21" value="ANIO" <?=($opcions["ANIO"])?"checked":"" ;?>  /><?=_("ANIO") ;?></td>
				<td><input name="chkopcvehicular[]" type="checkbox" id="chkopciones23" value="VIN" <?=($opcions["VIN"])?"checked":"" ;?>  /><?=_("VIN") ;?></td>
				<td><input name="chkopcvehicular[]" type="checkbox" id="chkopciones24" value="PLACA" <?=($opcions["PLACA"])?"checked":"" ;?>  /><?=_("PLACA") ;?></td>
			</tr>		
			<tr>
				<td><input name="chkopcvehicular[]" type="checkbox" id="chkopciones33" value="NUMERORECLAMO" <?=($opcions["NUMERORECLAMO"])?"checked":"" ;?>  /><?=_("NRO RECLAMO") ;?></td>
				<td colspan="4"><input name="chkopcvehicular[]" type="checkbox" id="chkopciones44" value="NOMBREAJUSTADOR" <?=($opcions["NOMBREAJUSTADOR"])?"checked":"" ;?>  /><?=_("NOMBRE AJUSTADOR") ;?></td>
				
			</tr>
        </table>
        <strong><?=_("PROVEEDOR") ;?></strong><br />
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #CC3366">
			<tr>
				<td width="16%"><input name="chkopcproveedor[]" type="checkbox" id="chkopciones2" value="IDPROVEEDOR" <?=($opcions["IDPROVEEDOR"])?"checked":"" ;?>  /><?=_("IDPROVEEDOR") ;?></td>
				<td width="18%"><input name="chkopcproveedor[]" type="checkbox" id="chkopciones2" value="NOMBREPROVEEDOR" <?=($opcions["NOMBREPROVEEDOR"])?"checked":"" ;?> /><?=_("NOMBREPROVEEDOR") ;?></td>
				<td width="19%"><input name="chkopcproveedor[]" type="checkbox" id="chkopciones3" value="STATUSPROVEEDOR" <?=($opcions["STATUSPROVEEDOR"])?"checked":"" ;?> />              <?=_("STAUSPROVEEDO") ;?></td>
				<td width="16%"><input name="chkopcproveedor[]" type="checkbox" id="chkopciones4" value="CONDICIONPROVEEDOR" <?=($opcions["CONDICIONPROVEEDOR"])?"checked":"" ;?>  /><?=_("INTERNO/EXTERNO") ;?></td> 
			</tr>
			<tr>
				<td><input name="chkopcproveedor[]" type="checkbox" id="chkopciones25" value="FECHATEAT" <?=($opcions["FECHATEAT"])?"checked":"" ;?>  /><?=_("FECHAPROGRAMADA(TEAT)") ;?></td>
				<td><input name="chkopcproveedor[]" type="checkbox" id="chkopciones26" value="HORATEAT" <?=($opcions["HORATEAT"])?"checked":"" ;?>  /><?=_("HORAPROGRAMADA(TEAT)") ;?></td>
				<td colspan="2"><input name="chkopcproveedor[]" type="checkbox" id="chkopciones4" value="LOCALFORANEO" <?=($opcions["LOCALFORANEO"])?"checked":"" ;?>  /><?=_("LOCAL/FORANEO") ;?></td>
			</tr>
        </table>
        <strong><?=_("COSTO") ;?></strong><br />
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #CC3366">
			<tr>
				<td width="33%"><input name="chkopccosto[]" type="checkbox" id="chkopciones3" value="MONTOAA" <?=($opcions["MONTOAA"])?"checked":"" ;?> /><?=_("MONTO AA") ;?></td>
				<td width="32%"><input name="chkopccosto[]" type="checkbox" id="chkopciones3" value="MONTOCC" <?=($opcions["MONTOCC"])?"checked":"" ;?>  /><?=_("MONTO CC") ;?></td>
				<td width="35%"><input name="chkopccosto[]" type="checkbox" id="chkopciones3" value="MONTONA" <?=($opcions["MONTONA"])?"checked":"" ;?>  /><?=_("MONTO NA") ;?></td>
			</tr>
        </table>
        <strong><?=_("MOTIVO DE JUSTIFICACION") ;?></strong><br />
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFE8" style="border:1px dashed #CC3366">
			<tr>
				<td width="33%"><input name="chkopmotivo[]" type="checkbox" id="chkopciones3" value="CANCELADOMOMENTO" <?=($opcions["CANCELADOMOMENTO"])?"checked":"" ;?> /><?=_("CANCELADO AL MOMENTO") ;?></td>
				<td width="32%"><input name="chkopmotivo[]" type="checkbox" id="chkopciones3" value="CANCELADOPOSTERIOR" <?=($opcions["CANCELADOPOSTERIOR"])?"checked":"" ;?> /><?=_("CANCELADO POSTERIOR") ;?></td>
				<td width="35%"><input name="chkopmotivo[]" type="checkbox" id="chkopciones3" value="REASIGNACIONPROV" <?=($opcions["REASIGNACIONPROV"])?"checked":"" ;?> /><?=_("REASIGNACION DE PROVEEDORES") ;?></td>
			</tr>
        </table>
        <strong><?=_("FECHAS") ;?></strong><br />
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF4F4" style="border:1px dashed #CC3366">
			<tr>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones1" value="FECHA_APERTURA_EXP" <?=($opcions["FECHA_APERTURA_EXP"])?"checked":"" ;?>  /><?=_("Fecha_aper_exp") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones2" value="HORA_APERTURA_EXP" <?=($opcions["HORA_APERTURA_EXP"])?"checked":"" ;?>  /><?=_("Hora_aper_exp") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones3" value="FECHA_REGISTRO_ASIS" <?=($opcions["FECHA_REGISTRO_ASIS"])?"checked":"" ;?>  /><?=_("Fecha_reg_asis") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones4" value="HORA_REGISTRO_ASIS" <?=($opcions["HORA_REGISTRO_ASIS"])?"checked":"" ;?>  /><?=_("Hora_reg_asis") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones5" value="FECHA_ASIGNACION_PROV" <?=($opcions["FECHA_ASIGNACION_PROV"])?"checked":"" ;?>  /><?=_("Fecha_asig_prov") ;?></td>
			</tr>
			<tr>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones6" value="HORA_ASIGNACION_PROV" <?=($opcions["HORA_ASIGNACION_PROV"])?"checked":"" ;?>  /><?=_("Hora_asig_prov") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones7" value="TIEMPO_ASIGNACION" <?=($opcions["TIEMPO_ASIGNACION"])?"checked":"" ;?> /><?=_("Tiempo_asignacion") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones8" value="FECHACONTACTO" <?=($opcions["FECHACONTACTO"])?"checked":"" ;?> /><?=_("FechaContacto") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones9" value="HORACONTACTO" <?=($opcions["HORACONTACTO"])?"checked":"" ;?> /><?=_("HoraContacto") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones10" value="TIEMPO_CONTACTO" <?=($opcions["TIEMPO_CONTACTO"])?"checked":"" ;?> /><?=_("Tiempo_contacto") ;?></td>
			</tr>
			<tr>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones11" value="FECHA_TERMINO_PROV" <?=($opcions["FECHA_TERMINO_PROV"])?"checked":"" ;?> /><?=_("Fecha_term_prov") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones12" value="HORA_TERMINO_PROV" <?=($opcions["HORA_TERMINO_PROV"])?"checked":"" ;?> /><?=_("Hora_term_prov") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones13" value="TIEMPO_TERMINO" <?=($opcions["TIEMPO_TERMINO"])?"checked":"" ;?> /><?=_("Tiempo_termino") ;?></td>
				<td colspan="2"><input name="chkopcion1[]" type="checkbox" id="chkopciones14" value="TIEMPO_TOTAL" <?=($opcions["TIEMPO_TOTAL"])?"checked":"" ;?> /><?=_("Tiempo_total") ;?></td>
			</tr>			
			<tr>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones16" value="FECHAARRIBO" <?=($opcions["FECHAARRIBO"])?"checked":"" ;?> /><?=_("Fecha_Arribo_prov") ;?></td>
				<td><input name="chkopcion1[]" type="checkbox" id="chkopciones17" value="HORAARRIBO" <?=($opcions["HORAARRIBO"])?"checked":"" ;?> /><?=_("Hora_Arribo_prov") ;?></td>
			</tr>
        </table> 
        <strong><?=_("CALIDAD") ;?></strong><br>
        <table width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF4F4" style="border:1px dashed #CC3366">
			<tr>
				<td><input name="chkopccalidad[]" type="checkbox" id="chkopciones30" value="CALIFSERV" <?=($opcions["CALIFSERV"])?"checked":"" ;?>  /><?=_("CALIFICACION ENCUESTA") ;?></td>
				<td><input name="chkopccalidad[]" type="checkbox" id="chkopciones31" value="STATUSENCUESTA" <?=($opcions["STATUSENCUESTA"])?"checked":"" ;?>  /><?=_("STATUS ENCUESTA") ;?></td>
				<td><input name="chkopccalidad[]" type="checkbox" id="chkopciones32" value="SUPERVISOR" <?=($opcions["SUPERVISOR"])?"checked":"" ;?>  /><?=_("SUPERVISOR ENCUESTA") ;?></td> 
				<td><input name="chkopccalidad[]" type="checkbox" id="chkopciones33" value="STATUSCALIFICAEXP" <?=($opcions["STATUSCALIFICAEXP"])?"checked":"" ;?> /><?=_("STATUS EXPEDIENTE") ;?></td>				
			</tr>         
			<tr>           
				<td><input name="chkopccalidad[]" type="checkbox" id="chkopciones34" value="USUARIOCALIFICA" <?=($opcions["USUARIOCALIFICA"])?"checked":"" ;?>  /><?=_("USUARIO CALIFCA EXP.") ;?></td>
				<td><input name="chkopccalidad[]" type="checkbox" id="chkopciones35" value="STATUSAUDITORIA" <?=($opcions["STATUSAUDITORIA"])?"checked":"" ;?>  /><?=_("STATUS AUDITORIA") ;?></td>
				<td colspan="2"><input name="chkopccalidad[]" type="checkbox" id="chkopciones37" value="COMENTARIOENCUESTA" <?=($opcions["COMENTARIOENCUESTA"])?"checked":"" ;?>  /><?=_("COMENTARIO ENCUESTA") ;?></td>
			</tr>
        </table>