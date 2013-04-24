<?php
  $mostrarInfo=$con->consultation("SELECT if(MOSTRARINFO1 =1,INFO1,'') as info1,if(MOSTRARINFO2 =1,INFO2,'') as info2,if(MOSTRARINFO3 =1,INFO3,'') as info3,TITULO1,TITULO2,TITULO3 from $con->catalogo.catalogo_afiliado_persona_datosadicionales WHERE IDAFILIADO='".$row->IDAFILIADO."'");
?>
<style type="text/css">
/* tamaño y forma del panel principal */
div#panel {
	position: relative;
	width:400px;
	height: 300px;
}

/* configuracion de las pestañas */
ul#tabs {
	position:absolute;
	left: 0px;
	top: 0px;
	margin:0;
	padding:0;
	width: 400px;
	height: 24px;
	z-index: 20;
}
	ul#tabs li {
		float:left;
		height: 23px;
		padding-left: 8px;
		list-style: none;
		margin-right: 1px;
		background: url(tabs/tabs.png) left -48px;
	}
	ul#tabs li.actual {
		height: 24px;
		background: url(tabs/tabs.png) left -72px;
	}
		ul#tabs li a {
			display: block;
				/* hack para ie6 */
				.display: inline-block;
				/* fin del hack */
			height: 23px;
			line-height: 23px;
			padding-right: 8px;
			outline: 0px none;
			font-family: arial;
			font-size: 10px;
			text-decoration: none;
			color: #000;
			background: url(tabs/tabs.png) right 0px;
		}
		
		ul#tabs li.actual a {
			height: 24px;
			line-height: 24px;
			background: url(tabs/tabs.png) right -24px;
			cursor: default;	
		}

/* Configuración de los paneles */
div#panel #paneles {
	position:absolute;
	left: 0px;
	top: 23px;
	width: 398px;
	height: 150px;
	border: 1px solid #91a7b4;
	background: #fff;
	overflow: hidden;
	z-index: 10px;
}
	div#panel #paneles div {
		margin:10px;
		width: 378px;
		height: 150px;
		font-family: arial;
		font-size: 12px;
		text-decoration: none;
		color: #000;
		overflow: auto;
	}
</style>
<div id="panel">
	<ul id="tabs">
    	<li id="tab_01"><a href="#" onclick="tab('tab_01','panel_01');">Info 1</a></li>
        <li id="tab_02"><a href="#" onclick="tab('tab_02','panel_02');">Info 2</a></li>
        <li id="tab_03"><a href="#" onclick="tab('tab_03','panel_03');">Info 3</a></li>
         
    </ul>
	<div id="paneles" style="background-color:#E1E1FF">
		<div id="panel_01">		
			<strong><U><?=$mostrarInfo[0][3]?></U></strong><p></p>
			<?=nl2br($mostrarInfo[0][0])?>		
		</div>
		<div id="panel_02">
			<strong><U><?=$mostrarInfo[0][4]?></U></strong><p></p>
			<?=nl2br($mostrarInfo[0][1])?>
		</div>
		<div id="panel_03">
			<strong><U><?=$mostrarInfo[0][5]?></U></strong><p></p>
			<?=nl2br($mostrarInfo[0][2])?>
		</div>
	</div>
	<script type="text/javascript">
		tab('tab_01','panel_01');
	</script>
</div>
