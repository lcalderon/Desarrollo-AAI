<?

$valida_familia = array(
				'5'=> array( // FAMILIA PC
				/*ETAPA	*/	'1'=>array('MARCA','MODELO','NUMEROSERIE','FECHACOMPRA','SISTEMAOPERATIVO')
							)
				);

$valida_servicio =array(							
				'21'=>array( // CONSULTA TELEFONICA
							'1'=>array('DESCRIPCIONSERVICIO'),
							'8'=>array('DIAGNOSTICO','SOLUCIONFALLA','RECOMENDACIONES','OTROS')
							),
				'71'=>array(  // VISITA TECNICA
							'1'=>array('IDDESTINO,DESCRIPCIONSERVICIO'),
							'8'=>array('DIAGNOSTICO','SOLUCIONFALLA','RECOMENDACIONES')
							),
				'72'  // ASISTENTE DE TAREAS WEB
				);




?>
