<script>

/***********************************************
* Omni Slide Menu script - � John Davenport Scheuer
* very freely adapted from Dynamic-FX Slide-In Menu (v 6.5) script- by maXimus
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full original source code
* as first mentioned in http://www.dynamicdrive.com/forums
* username:jscheuer1
***********************************************/

//One global variable to set, use true if you want the menus to reinit when the user changes text size (recommended):
resizereinit=true;

menu[1] = {
	id:'menu1', //use unique quoted id (quoted) REQUIRED!!
	fontsize:'60%', // express as percentage with the % sign
	linkheight:50,  // linked horizontal cells height
	hdingwidth:200,  // heading - non linked horizontal cells width
	kviewtype:'fixed',       // Type of keepinview - 'fixed' utilizes fixed positioning where available, 'absolute' fluidly follows page scroll,
	bartext:'ETAPAS',       // bar text (the vertical cell) use text or img tag
	menutop:5,     // initial top offset - except for top menu, where it is meaningless
	barwidth:20,     // bar (the vertical cell) width
	hdingheight:20,  // heading - non linked horizontal cells height
	hdingbgcolor:'#444444',  // heading - non linked horizontal cells background color
	d_colspan:3,     // Available columns in menu body as integer
	linktxtalign:'left',     // linked horizontal cells right left or center text alignment
	menuspeed:1,
	menupause:50,
	// Finished configuration. Use default values for all other settings for this particular menu (menu[1]) ///

	menuItems:[ // REQUIRED!!
	//[name, link, target, colspan, endrow?] - leave 'link' and 'target' blank to make a header
	<?
		
	    $varexis=crypt($idasistencia,"666"); 
	
		$parametro="?varexis=$varexis&gestion=CALIDAD&idasistencia=".$idasistencia;	
		$etapa0="/app/vista/calidad/etapa0.php".$parametro;

	?>
	[
	"<br>CONSOLIDADO DE DEFICIENCIAS", 
	"<?=$etapa0?>",
	""
	],
	<?
	 
		$result = $con->query("SELECT IDETAPA,DESCRIPCION,URL FROM $con->catalogo.catalogo_etapa WHERE IDETAPA <=$idetapa_asis");
		while($reg=$result->fetch_object()){
			echo "[";
			if($idetapa==$reg->IDETAPA) echo "'<br><font color=red><strong>$reg->DESCRIPCION</strong></font> ',"; else echo "'<br>  $reg->DESCRIPCION ',";
			echo "'$reg->URL$parametro',";
			echo "''";
			echo "],";
		}
	?>

	]}; // REQUIRED!! do not edit or remove
 
	make_menus();
</script>