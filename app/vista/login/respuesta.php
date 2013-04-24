<?php

	if(isset($_GET["urlacces"]))	$urlacces="?urlacces=".urlencode($_GET["urlacces"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head><title>Acceso Restringuido</title>    
<meta http-equiv="Content-Type" content="text/html; utf-8">
<meta http-equiv="Refresh" content="3; url=/app/vista/login/index.php<?=$urlacces;?>">
<style type="text/css">
.redirect {
    text-align: center;
    border: 2px dotted;
	background-color:#FFFF00;
    padding: 25px;
    width: 70%;
    color:#0000FF;
    font-family: Trebuchet MS, Arial;
}
</style></head><body>
<br><br><br><br>
<div align="center">            
<div class="redirect">
    <b><?=_("Ud. no tiene suficientes derechos para acceder a esta area.") ;?></b>
    <hr style="border: 2px solid rgb(0, 0, 0); color:#FF0000; height: 3px; width: 100%;"><?=_("SI LA PAGINA NO SE ACTULIZA AUTOMATICAMENTE,POR FAVOR CLICK ") ;?><a href="/app/vista/login/index.php<?=$urlacces;?>"><font color="#C6242F"><strong><?=_("AQUI") ;?></strong></a>
</div> 
</div>
</body>
</html>