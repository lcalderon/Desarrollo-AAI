<?
$idproveedor = $_GET['idproveedor'];
$error = $_GET['error'];
?>
<html>
<head>
<meta http-equiv="Content-Language" content="en" />
<meta name="GENERATOR" content="Zend Studio" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>title</title>
</head>
<body bgcolor="#B4BFCF" text="#000000" link="#FF9966" vlink="#FF9966" alink="#FFCC99">
<div id='mensaje'><?=$error?></div>
<div style="float:left">
	<img src='foto/foto.php?idproveedor=<?=$idproveedor?>' width="128px" height="96px"/>
</div>
<div style="float:rigth">
	<form action='procesa_foto.php' enctype="multipart/form-data" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
	
    <input type="file"  name="foto"/>
    <input type="hidden" name="IDPROVEEDOR" value='<?=$idproveedor?>' >
        
    <input type="submit" value="Enviar Foto" />
</form>
</div>
</body>
</html>