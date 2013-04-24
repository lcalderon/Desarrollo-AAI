<?
session_start();
include_once('../../../modelo/clase_mysqli.inc.php');
include_once('../../includes/head_prot.php');
$con = new DB_mysqli();

$idextension = $_SESSION['extension'];

/* obtenemos el IP del server */
$idserver = $con->lee_parametro('IPSERVIDOR_ASTERISK');

/* obtenemos la contraseña de la extension */
$sql ="select SECRET,PROTOCOLO from $con->catalogo.catalogo_extensiones_asterisk where IDEXTENSION =$idextension";
$result = $con->query($sql);
while($reg=$result->fetch_object()){
	$secret = $reg->SECRET;
	$protocolo = $reg->PROTOCOLO;
}



?>

<link rel="stylesheet" href="/librerias/TinyAccordion/style2.css" type="text/css" />
<script type="text/javascript">
    var Zoiper;
    var ActiveCall;
    function Quit()
    {
      Status("Quit() called");
      if (Zoiper != null)
      {
        Zoiper.DelAccount("9005");
      }
      document.getElementById('ZoiperA').innerHTML = "";
    }

          
    function OnZoiperReady(phone)
    {
      Zoiper = phone;
      Zoiper.AllowMultipleInstances();
      var Config = Zoiper.GetConfig();
      Config.SetSIPIAXPorts("4566","5061");
      Config.NumberOfCallsLimit("2");
      Config.PopupMenuOnIncomingCall = "true";
      Config.DebugLogPath = "c:\\";
      Config.EnableDebugLog = "true";
      Config.RingWhenTalking = "false";
      
      Account = Zoiper.AddAccount("<?=$idextension?>", "<?=$protocolo?>");
      Account.Domain   = "<?=$idserver?>";
      Account.CallerID = "<?=$idextension?>";
      Account.UserName = "<?=$idextension?>";
      Account.Password = "<?=$secret?>";
	  Account.AddCodec("GSM");
      Account.DTMFType = "media_inband";
      Account.Apply();
      Account.Register();
      Zoiper.UseAccount("<?= $idextension?>"); 
     
    }
    
    function OnZoiperCallAccept(call)
    {
	if (call.IsIncoming=='true') {
		 alert("LLAMADA ENTRANTE "+ call.Phone);
		window.open('../../expediente/entrada/expediente_frmexpediente.php','EXPEDIENTE');
	}
    }
   
    function OnZoiperCallIncoming(call)
    {
    }
  </script>





</head>
<body onload="inicio()" onunload="Quit()">
<!-- <ul class="acc" id="acc">
	<li>
		<h4><table width="100%">
			<tr>
				<td><h3><b><?=_('LLAMADAS ENTRANTES')?></b><h3></td>
				<td align='right'><img src='../../../../imagenes/iconos/collapse-expand2.GIF' ></td>
			</tr>
			</table>
		<h4>
		<div class="acc-section" id="en_espera">
			
		</div>
	</li>
</ul>  -->

  <object id="ZoiperA" classid="clsid:BCCA9B64-41B3-4A20-8D8B-E69FE61F1F8B" CODEBASE="http://www.zoiper.com/webphone/InstallerWeb.cab#Version=2,5,0,11285" align="left" width="434" height="236" >
  <embed id="ZoiperN" type="application/x-zoiper-plugin" align="left" width="434" height="236"/>
  </object>
</body>

</html>
