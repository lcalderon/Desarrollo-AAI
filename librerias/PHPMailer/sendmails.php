<?php

 	function enviar_mails($mails,$asunto,$cuerpo)
	 {
		 
		$mail = new PHPMailer();		 

		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->Host       = "smtp.ihostexchange.net"; // SMTP server
		$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
												   // 1 = errors and messages
												   // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
		$mail->Host       = "smtp.ihostexchange.net";      // sets GMAIL as the SMTP server
		$mail->Port       = 587;                   // set the SMTP port for the GMAIL server
		$mail->Username   = "lcalderon@americanassist.com";  // GMAIL username
		$mail->Password   = "lc347658";            // GMAIL password

		$mail->SetFrom("procesos_soaang@americanassist.com","SAC SOAANG ");

		// $mail->AddReplyTo("name222@yourdomain.com","nombre 222");

		$mail->Subject    = $asunto;

		//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

		$mail->MsgHTML($cuerpo);

		// $address = "lcalderon@americanassist.com";
		// $mail->AddAddress($address, "John Doe");
		
		for($i=0;$i <=count($mails)-1;$i ++)	if($mails[$i]!="") $mail->AddAddress($mails[$i]);
		 
//		if(!$mail->Send()) 
//		 {
//			return false;
//		 }
//		else
//		 {
//			return true;
//		 }
 
	 }

?>