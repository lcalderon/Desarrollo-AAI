<?
$desc_horario = array(
				'DIU'=>'DIURNO',
				'NOC'=>'NOCTURNO'
				//'FES'=>'FESTIVO'
				);

$dia_semana = array(
				'LUN'=>_('LUNES'),
				'MAR'=>_('MARTES'),
				'MIE'=>_('MIERCOLES'),
				'JUE'=>_('JUEVES'),
				'VIE'=>_('VIERNES'),
				'SAB'=>_('SABADO'),
				'DOM'=>_('DOMINGO')
				);
				
$mes_del_anio = array(
				'01'=>_('ENERO'),
				'02'=>_('FEBRERO'),
				'03'=>_('MARZO'),
				'04'=>_('ABRIL'),
				'05'=>_('MAYO'),
				'06'=>_('JUNIO'),
				'07'=>_('JULIO'),
				'08'=>_('AGOSTO'),
				'09'=>_('SETIEMBRE'),
				'10'=>_('OCTUBRE'),
				'11'=>_('NOVIEMBRE'),
				'12'=>_('DICIEMBRE')
				);				

$ambito = array(
			'LOC'=>_('LOCAL'),
			'FOR'=>_('FORANEO')
			);

$status_comercial = array(
			'EMO'=>_('EN MORA'),
			'CPL'=>_('CAMBIO DE PLASTICO'),
			'ACT'=>_('ACTIVO'),
			'CAN'=>_('CANCELACION DE CUENTA')
			);

$desc_parentesco = array(
			'MAD'=>_('MADRE'),
			'HIJ'=>_('HIJO'),
			'HIJA'=>_('HIJA'),
			'HER'=>_('HERMANO'),
			'HERM'=>_('HERMANA'),
			'ABU'=>_('ABUELO'),
			'OTR'=>_('OTROS'),
			'ABUA'=>_('ABUELA'),
			'EMPH'=>_('EMPLEADA DEL HOGAR'),
			'PAD'=>_('PADRE')
			);

$desc_genero = array(
			'M'=>_('MASCULINO'),
			'F'=>_('FEMENINO')
			);
			
$desc_transmision = array(
			'MEC'=>_('MECANICA'),
			'AUT'=>_('AUTOMATICO'),
			'DUA'=>_('DUAL'),
			'OTR'=>_('OTROS')
			);		
			
$desc_combustible = array(
			'GAS0'=>_('GASOLINA'),
			'DIES'=>_('DIESEL'),
			'GNV'=>_('GAS NATURAL'),
			'GLP'=>_('GAS LICUADO PETROLEO')
			);		
			
$desc_uso = array(
			'PAR'=>_('PARTICULAR'),
			'COMP'=>_('COMERCIAL DE PASAJEROS'),
			'COMC'=>_('COMERCIAL DE CARGA'),
			'COME'=>_('COMERCIAL'),
			'GOB'=>_('GOBIERNO')
			);
			
$desc_peso = array(
			'LIV'=>_('LIVIANO'),
			'SEM'=>_('SEMIPESADO'),
			'PES'=>_('PESADO')
			);
			
$desc_prioridadAtencion = array(
			'EME'=>_('EMERGENCIA'),
			'PRO'=>_('PROGRAMADO')
			);
						
$desc_cargoacuenta = array(
			'AA'=>_('AMERICAN ASSIST'),
			'NA'=>_('NUESTROA AFILIADO'),
			'CC'=>_('CLIENTE CORPORATIVO')
			);
			
$desc_estadocivil = array(
			'S'=>_('SOLTERO'),
			'C'=>_('CASADO'),
			'D'=>_('DIVORCIADO'),
			'V'=>_('VIUDO')
			);
			

$status_asig_prov = array(
			'AC'=>_('ACTIVO'),
			'CA'=>_('CANCELADO'),
			'PC'=>_('PROVEEDOR CONCLUIDO')
			);
$desc_statusAfiliado = array(
			'ACT'=>_('ACTIVO'),
			'CAN'=>_('CANCELADO')
			);			
			
$tipo_horario = array(
			'FULL'=>_('24 Horas'),
			'TURN'=>_('Turno')
			);
			
$desc_status_expeduiente = array(
			'PRO'=>_('EN PROCESO'),
			'CER'=>_('CERRADO')
			);
			
$desc_lugar= array(
			'VIAP'=>_('VIA PUBLICA'),
			'CASA'=>_('CASA'),
			'OFIC'=>_('OFICINA'),
			'OTRO'=>_('OTROS')
			);	
			
$desc_autorizacion= array(
			'BD'=>_('BASE DE DATOS'),
			'CAL'=>_('CALL CENTER'),
			'SUP'=>_('SUPERVISOR'),
			'WEB'=>_('WEB/ SOFT EXTERNO')
			);
			
$desc_movimiento_expediente= array(
			'APE'=>_('APERTURA'),
			'REG'=>_('REGISTRO'),
			'SEG'=>_('SEGUIMIENTO'),
			'TRA'=>_('TRASNFERENCIA'),
			'CIE'=>_('CIERRE')
			);
			
$desc_movimiento_asistencia= array(
			'APE'=>_('APERTURA'),
			'REG'=>_('REGISTRO'),
			'SEG'=>_('ASIGNACION'),
			'CIE'=>_('CIERRE')
			);

$desc_tipo_inmueble= array(
			'CASA'=>_('CASA'),
			'DEPA'=>_('DEPARTAMENTO'),
			'DUPL'=>_('DUPLEX'),
			'LCOM'=>_('LOCALCOMERCIAL'),
			'OTRO'=>_('OTROS')
			);
			
$desc_tipo_inmueble_veh= array(
			'CASA'=>_('CASA'),
			'OFIC'=>_('OFICINA'),
			'TALL'=>_('TALLER'),
			'VIAP'=>_('VIA PUBLICA'),
			'OTRO'=>_('OTROS')
			);
			
			
$ubicacion_fisica= array(
			'VIAP'=>_('VIA PUBLICA'),
			'CASA'=>_('CASA'),
			'OFIC'=>_('OFICINA'),
			'OTRO'=>_('OTROS')
			);
			
$confirmar = array(
			'1'=>_('SI'),
			'0'=>_('NO')
			);			
			
$tipo_atencion = array(
			'MED'=>_('MEDICA'),
			'ODO'=>_('ODONTOLOGICA'),
			'ENF'=>_('ENFERMERIA'),
			'LAB'=>_('LABORATORIO'),
			'RAD'=>_('RADIOGRAFIA'),
			'OTR'=>_('OTROS')
			);
			
$tipo_prestacion = array(
			'AMB'=>_('AMBULATORIA'),
			'DOM'=>_('DOMICILIARIA')
			);

$ubicacion_danio = array(
			'TOT'=>_('TOTAL'),
			'PAR'=>_('PARCIAL')
			);

$ubicacion_danio_parcial = array(
			'COCI'=>_('COCINA'),
			'BANI'=>_('BAÑO'),
			'LAVA'=>_('LAVADERO'),
			'SALA'=>_('LIVING-SALA'),
			'RECA'=>_('RECAMARA'),
			'COME'=>_('COMEDOR'),
			'OTRO'=>_('OTROS')

			);

$ubicacion_danio_cerrajeria = array(
			'PPRI'=>_('PUERTA PRINCIPAL'),
			'PAUX'=>_('PUERTA AUXILIAR')
			);

$ubicacion_danio_cerrajeria_auxiliar = array(
			'PLAT'=>_('PUERTA LATERAL'),
			'PTRA'=>_('PUERTA TRASERA'),
			'PINT'=>_('PUERTA INTERNA'),
			'POTR'=>_('PUERTA OTROS')
			);

$ubicacion_danio_vidrieria = array(
			'VFRO'=>_('VENTANA FRONTAL'),
			'VLAT'=>_('VENTANA LATERAL'),
			'VTRA'=>_('VENTANA TRASERA'),
			'VOTR'=>_('OTROS')
			);

$pieza_danio_vidrieria = array(
			'CROT'=>_('CRISTAL ROTO'),
			'CRAJ'=>_('CRISTAL RAJADO'),
			'COTR'=>_('OTROS')
			);


$desc_tipo_cobertura = array(
			'PEVE'=>_('Por Evento'),
			'SLIM'=>_('Sin Limite'),
			'COPA'=>_('Copago'),
			'CONEX'=>_('Conexion')
			);


$desc_cobertura_servicio = array(
			'CON'=>_('CONEXION'),
			'COR'=>_('CORTESIA'),
			'COB'=>_('EN COBERTURA'),
			'GAR'=>_('GARANTIA'),
			'CNO'=>_('CONTINUO'),
			'ADI'=>_('ADICIONAL')
			);

$desc_status_asistencia = array(
			'CM'=>_('CANCELADO AL MOMENTO'),
			'CP'=>_('CANCELADO POSTERIOR'),
			'CON'=>_('CONCLUIDO'),
			'PRO'=>_('EN PROCESO')
			);
			

$desc_ambito = array(
			'NAC'=>_('NACIONAL'),
			'INT'=>_('INTERNACIONAL')
			);

$desc_plomeriagas_subservicio = array(
			'ROFU'=>_('ROTURA/FUGA'),
			'DESO'=>_('DESOBSTRUCCION/DESTAPE'),
			'INST'=>_('INSTALACION DE ARTEFACTOS'),
			'DIAG'=>_('DIAGNOSTICO/PRESUPUESTO'),
			'OTRO'=>_('OTROS')
			);

$desc_electricidad_subservicio = array(
			'INTE'=>_('INTERRUPCION/CORTE DE ELECTRICIDAD'),
			'DIAG'=>_('DIAGNOSTICO/PRESUPUESTO'),
			'OTRO'=>_('OTROS')
			);

$desc_cerrajeria_subservicio = array(
			'APER'=>_('APERTURA'),
			'REPA'=>_('REPARACION'),
			'COMB'=>_('CAMBIO DE COMBINACION'),
			'REPO'=>_('REPOSICION'),
			'INST'=>_('INSTALACION DE ARTEFACTOS'),
			'DIAG'=>_('DIAGNOSTICO/PRESUPUESTO'),
			'OTRO'=>_('OTROS')
			);

$desc_vidrieria_subservicio = array(
			'REPO'=>_('REPOSICION'),
			'INST'=>_('INSTALACION DE VIDRIOS/ESPEJOS'),
			'DIAG'=>_('DIAGNOSTICO/PRESUPUESTO'),
			'OTRO'=>_('OTROS')
			);

$desc_varios_subservicio = array(
			'TECV'=>_('TECNICOS VARIOS'),
			'SERV'=>_('SERVICIOS VARIOS')
			);

$desc_seguridad_subservicio = array(
			'VIGI'=>_('VIGILANCIA DOMICILIARIA'),
			'CUST'=>_('CUSTODIA DE BIENES'),
			'OTRO'=>_('OTROS')
			);

$desc_varios_subservicio_tec = array(
			'PINT'=>_('PINTURA'),
			'ALBA'=>_('ALBAÑILERIA'),
			'CARP'=>_('CARPINTERIA'),
			'HERR'=>_('HERRERIA'),
			'REPT'=>_('REPARACION DE TECHOS'),
			'INSE'=>_('INSTALACION DE ELECTRODOMESTICOS')
			);

$desc_varios_subservicio_var = array(
			'MUDA'=>_('MUDANZA/TRASLADO DE MUEBLES'),
			'SEDO'=>_('SERVICIO DOMESTICO/MUCAMA'),
			'CUME'=>_('CUIDADO DE MENORES'),
			'LIMP'=>_('LIMPIEZA DE OBRA'),
			'OTRO'=>_('OTROS')
			);

$desc_modulo_justificaciones = array(
			'CS'=>_('CONDICION DE SERVCIO'),
			'PROV'=>_('ASGINACION PROVEEDORES'),
			'FAM'=>_('FAMILIA'),
			'COST'=>_('COSTO')
			);

$desc_tipo_auxiliovial = array(
			'NEU'=>_('CAMBIO DE LLANTA'),
			'ELE'=>_('PASO DE CORRIENTE '),
			'COM'=>_('CARGA COMBUSTIBLE'),
			'MAN'=>_('MANIOBRAS MECANICA LIGERA')
			);
			 
$desc_status_afi_asistencia = array(
			'ACT'=>_('ACTIVO'),
			'CAN'=>_('CANCELADO')
			);

$desc_status_afi_comercial = array(
			'MOR'=>_('MORA'),
			'SUT'=>_('SUSPENSION TEMPORAL'),
			'NOP'=>_('NO PAGO')
			);
			
$calidad1 = array(
			'1'=>_('SI'),
			'0'=>_('NO'),
			'0'=>_('NS/NC')
			);
$calidad2 = array(
			'10'=>_('EXCELENTE'),
			'7.5'=>_('MUY BUENO'),
			'5'=>_('BUENO'),
			'2.5'=>_('REGULAR'),
			'0'=>_('MALO')
			);

$desc_calificacion = array(
			'1E'=>_('EXCELENTE'),
			'MB'=>_('MUY BUENO'),
			'B'=>_('BUENO'),
			'R'=>_('REGULAR'),
			'M'=>_('MALO')
			);
			
$calidad3 = array(
			'10'=>_('MUY SATISFECHO'),
			'7'=>_('SATISFECHO'),
			'4'=>_('POCO SATISFECHO'),
			'1'=>_('INSATISFECHO')
			);	
			
$calidad4 = array(
			'1'=>_('SI'),
			'0'=>_('NO'),
			'0'=>_('TAL VEZ')
			);
			
$statusproceso_sac = array(
			'PRO'=>_('EN PROCESO'),
			'REC'=>_('RECIBIDO'),
			'ANU'=>_('ANULADO'),
			'CON'=>_('CONCLUIDO')
			);	
						
$validez_sac = array(
			'FUPR'=>_('FUNDADO - PROCEDE'),
			'INPR'=>_('INFUNDADO - PROCEDE'),
			'INNP'=>_('INFUNDADO - NO PROCEDENTE')
			);	
									
// $asignacioncaso_sac = array(
			// 'SAC'=>_('SAC'),
			// 'CAL'=>_('CALIDAD'),
			// 'OPE'=>_('OPERACIONES'),
			// 'PRO'=>_('PROVEEDORES'),
			// 'POS'=>_('POSTVENTA'),
			// 'FIN'=>_('FINANZAS'),
			// 'GER'=>_('GERENCIA GENERAL'),
			// 'OTR'=>_('OTRAS AREAS')
			// );	
									
$serviciovehicular_por = array(
			'AVER'=>_('AVERIA'),
			'COLI'=>_('COLISION')
			);	
			
 $referencia = array(
			'TELC'=>_('TELEFONO DE CONSULTA'),
			'ONUT'=>_('ORIENTACION NUTRICIONAL'),
			'TALL'=>_('TALLERES'),
			'RMED'=>_('REFERENCIAS MEDICAS'),
			'RHOG'=>_('REFERENCIAS HOGAR'),
			'RVAR'=>_('REFERENCIAS VARIAS'),
			'IEME'=>_('INFORMACION EMERGENCIAS'),
			'OMED'=>_('ORIENTACION MEDICA'),
			'OTRA'=>_('OTRAS')
			
			
			);
 $referencia_varios = array(
			'FLOR'=>_('FLORISTERIA'),
			'REST'=>_('RESTAURANTES'),
			'TEAT'=>_('TEATRO'),
			'CINE'=>_('CINE')
			
			
			);		
			
 $referencia_taller = array(
			'MECA'=>_('MECANICA'),
			'ELEC'=>_('ELECTRICIDAD'),
			'CHPI'=>_('CHAPA Y PINTURA')	
			);
			
$referencia_hogar = array(
			'FUMI'=>_('FUMIGACION'),
			'PILE'=>_('PILETERIA'),
			'JARD'=>_('JARDINERIA')	
			);
			
$varios = array(
			'EDOC'=>_('ENVIO DE DOCUMENTOS'),
			'COVA'=>_('COMPRAS VARIAS'),
			'MERE'=>_('MENSAJES Y REGALOS')	
			);
			
$procedencia_mediogestion = array( 
			'LLAF'=>_('LLAMADA DEL AFILIADO/USUARIO'),
			'LLSP'=>_('LLAMADA DE SPONSORS'),
			'MASP'=>_('MAIL DE SPONSOR'),
			'CAAF'=>_('CARTA DE AFILIADO / USUARIO'),
			'CASP'=>_('CARTA DE SPONSOR'),
			'EMAA'=>_('EMPLEADO DE AAP'),
			'REPRE'=>_('RECLAMO PRESENCIAL'),
			'MAAF'=>_('MAIL DE AFILIADO/USUARIO')	
			);
			
$varios_concepto_entrega = array(
			'PRIV'=>_('PRIVADO'),
			'RCPA'=>_('RECEPTOR ABIERTO')
			);

$combo_horas = array(
			'00'=>_('00'),
			'01'=>_('01'),
			'02'=>_('02'),
			'03'=>_('03'),
			'04'=>_('04'),
			'05'=>_('05'),
			'06'=>_('06'),
			'07'=>_('07'),
			'08'=>_('08'),
			'09'=>_('09'),
			'10'=>_('10'),
			'11'=>_('11'),
			'12'=>_('12'),
			'13'=>_('13'),
			'14'=>_('14'),
			'15'=>_('15'),
			'16'=>_('16'),
			'17'=>_('17'),
			'18'=>_('18'),
			'19'=>_('19'),
			'20'=>_('20'),
			'21'=>_('21'),
			'22'=>_('22'),
			'23'=>_('23')
			);
	
$tipoatencion_escolar = array(
			'DOM'=>_('DOMICILIO'),
			'INS'=>_('INSTITUTO')
			);

$evalencuesta = array(
			'E'=>_('EXCELENTE'),
			'MB'=>_('MUY BUENO'),
			'B'=>_('BUENO'),
			'R'=>_('REGULAR'),
			'SE'=>_('SIN-ENCUESTA'),
			'M'=>_('MALO')
			);	
			
$evalencuesta_new = array(
			'SEVA'=>_('SIN-ENCUESTAR'),
			'EVAL'=>_('ENCUESTADO'),
			'CERR'=>_('CERRADO'),
			'SLOC'=>_('SIN-LOCALIZAR'),
			'NCEN'=>_('NO-CONT. ENCUESTA')
			);	

$evalauditoria = array(
			'AUDITADO'=>_('AUDITADO'),
			'SAUDITAR'=>_('SIN-AUDITAR'),
			'CERRADO'=>_('CERRADO')

			);

/*$evalexped = array(
			'EVALUADO'=>_('EEVALUACION'),
			'NO EVALUADO'=>_('NO EVALUADO'),
			'PENDIENTE CONFIRMACION'=>_('PENDIENTE CONFIRMACION')

			);
*/			
$evalexped = array(
			'CERRADO'=>_('CERRADO'),
			'SEVALUAR'=>_('SIN-EVALUAR'),
			'EVALUADO'=>_('EVALUADO')

			);			
 
$arrtiposiniestro = array(
			'HOG'=>_('HOGAR'),
			'VEH'=>_('VEHICULAR'),
			'DOC'=>_('DOCUMENTO'),
			'ART'=>_('ARTEFACTO'),
			'OTR'=>_('OTRO')
			);
	
$modalidad_pg = array(
			'1M'=>_('MENSUAL'),
			'6M'=>_('SEMESTRAL'),
			'12M'=>_('ANUAL')
			);

$arrtipotransporte = array(
			'TER'=>_('TERRESTRE'),
			'AER'=>_('AEREO'),
			'MAR'=>_('MARITIMO')
			);

$arrtipomovilidad = array(
			'MOT'=>_('MOTO'),
			'AUT'=>_('AUTOMOVIL'),
			'CAM'=>_('CAMIONETA')
		);

$arr_motivodelegar = array(
			'ASIG'=>_('ASIGNAR'),
			'RECH'=>_('RECHAZAR'),
			'ACEP'=>_('ACEPTAR')
	);		

$nombre_semana = array(
				'1'=>_('LUNES'),
				'2'=>_('MARTES'),
				'3'=>_('MIERCOLES'),
				'4'=>_('JUEVES'),
				'5'=>_('VIERNES'),
				'6'=>_('SABADO'),
				'7'=>_('DOMINGO')

);		

$arr_ambito = array(
				'L'=>_('LOCAL'),
				'F'=>_('FORANEO'),
				'I'=>_('INTERMEDIO')

);

$arr_gruposanguineo = array(
				'GRUPOAB'=>_('AB'),
				'GRUPOA'=>_('A'),
				'GRUPOB'=>_('B'),
				'GRUPOON'=>_('O')

);



$clasificacion = array(
'BIT'=>_('INGRESO EN BITACORA'),
'REG_ASIS'=>_('REGISTRO DE LA ASISTENCIA'),
'JUST'	  =>_('JUSTIFICACION'),
'ASIG'	  =>_('ASIGNACION DE PROVEEDOR'),
'CP'      =>_('CANCELADO POSTERIOR'),
'CM'      =>_('CANCELADO AL MOMENTO'),
'REP'     =>_('REPROGRAMAR TIEMPOS'),
'REASIG'  =>_('REASIGNACION DE PROVEEDOR'),
'CONF_SERV'=>_('CONFIRMACION DEL SERVICIO'),
'MON_PROV' =>_('MONITOREO DE PROVEEDOR'),
'MON_AFIL' =>_('MONITOREO DE AFILIADO'),
'ARR_PROV' =>_('ARRIBO DEL PROVEEDOR'),
'CONT_AFIL'=>_('CONTACTO AL AFILIADO'),
'PROV_CONC'=>_('PROVEEDOR CONCLUIDO'),
'LLCNF'	=>_('LLAMADA DE CONFORMIDAD'),
'CON_ASIS' =>_('ASISTENCIA CONCLUIDA'),
'DEM_ASIG' =>_('DEMORA ASIGNACION')


);

?>