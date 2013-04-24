<?php

$client = new  SoapClient("http://201.234.127.214/AONAffinityWebServices/AnulacionBAIS.asmx?wsdl");



$param = array('pnIdProducto'=>5148,
				'pcIdTipDocumento'=>'L',
				'pcNumDocumento'=>'10321192',
				'pnIdMotivo' =>5,
				'pcDescripcionMotivo'=>'Razones multiples',
				'pcUsuario'=>'anulacionAAP'

				);
				
				
$response =$client->AnularCertificadoxNroDocumento($param);

var_dump($response);

?>
