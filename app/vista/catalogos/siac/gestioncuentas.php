<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	include_once('../../../modelo/validar_permisos.php');	
	include_once("../../../vista/login/Auth.class.php");
	include_once("../../includes/arreglos.php");
	include_once("pagination.class.php");
	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }

	session_start();
	Auth::required();

	validar_permisos("MENU_SAC",1);
	
	//$rs_areas=$con->query("SELECT IDGRUPO,NOMBRE,CONCAT(LEFT(NOMBRE,3),IDGRUPO) AS ide FROM $con->catalogo.catalogo_grupo WHERE ACTIVADO=1 ");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>American Assist</title>

	<!-- se usa para validar y dar estilos -->
	<script type="text/javascript" src="../../../../estilos/functionjs/validator.js"></script>
	<link href="../../../../estilos/tablas/styletable.css" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/prototype.js"></script>
	<script type="text/javascript" src="../../../../librerias/scriptaculous/scriptaculous.js"></script>
	<link href="../../../../librerias/windows_js_1.3/themes/default.css" rel="stylesheet" type="text/css" >	 </link> 
	<link href="../../../../librerias/windows_js_1.3/themes/alphacube.css" rel="stylesheet" type="text/css" >	 </link>

	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/window_effects.js"> </script>
	<script type="text/javascript" src="../../../../librerias/windows_js_1.3/javascripts/debug.js"> </script>
		
	<link rel="stylesheet" href="../../../../estilos/tablas/pagination.css" media="all">
		
	<script type="text/javascript">
			
		function validarIngreso(variable){
			
			document.frm_principal.txtnomnuevo.value=document.frm_principal.txtnomnuevo.value.replace(/^\s*|\s*$/g,"");

			if(document.frm_principal.txtnomnuevo.value==""){
				  alert("INGRESE EL NOMBRE DEL AREA.");
				  document.frm_principal.txtnomnuevo.focus();
				  return (false);
			}	
			
			return (true);
				
		} 
	</script>
	
 	<script type="text/javascript">
		var cont=0;
		
		function adicionarFila(txtnombre){
				
				cont+=1;
				
				var tabla = document.getElementById(txtnombre).tBodies[0];				
				var fila = document.createElement("TR");

				var celda1 = document.createElement("TD");
				var celda2 = document.createElement("TD");
				var celda3 = document.createElement("TD");
				var celda4 = document.createElement("TD");

				var txt1 = document.createElement('INPUT');
				var txt2 = document.createElement('INPUT');
				var btn = document.createElement('INPUT');
				var btnimg = document.createElement('IMG');
 
				txtidusuario="txtu"+txtnombre+cont;
				txtidemail="txte"+txtnombre+cont
				
				txt1.setAttribute("type","text");
				txt1.setAttribute("size","20");
				txt1.setAttribute("id",txtidusuario);
				txt1.setAttribute("name","txtusuario"+"[]");
				txt1.setAttribute("readonly","true");			
				txt1.setAttribute("onFocus","coloronFocus(this)");
				txt1.setAttribute("onBlur","colorOffFocus(this)");
				txt1.setAttribute("class","classtexto");				
		//email		
				txt2.setAttribute("type","text");
				txt2.setAttribute("size","50");
				txt2.setAttribute("id",txtidemail);
				txt2.setAttribute("name","txtemail"+"[]");
				txt2.setAttribute("readonly","true");	
				txt2.setAttribute('style','text-transform:uppercase');
				txt2.setAttribute("onFocus","coloronFocus(this)");
				txt2.setAttribute("onBlur","colorOffFocus(this)");
				txt2.setAttribute("class","classtexto");
				
				btn.setAttribute("type","button");
				btn.setAttribute('style','width:33px;height:25px');				
				btn.setAttribute("name","btnbuscar");
				btn.setAttribute("value","...");
				btn.setAttribute('onclick',"ventana_responsable('"+txtidusuario+"','"+txtidemail+"')");
				

				btnimg.setAttribute('src','../../../../imagenes/iconos/deletep.gif');
				btnimg.setAttribute('title','ELIMINAR');
				btnimg.setAttribute('border','0');
				btnimg.setAttribute('height','14');
				btnimg.setAttribute('style','cursor:pointer');
				btnimg.onclick=function(){borrarFila(this,txtnombre);}
				
				celda1.appendChild(txt1); 
				celda2.appendChild(txt2); 
				celda3.appendChild(btn); 
				celda4.appendChild(btnimg); 
				

				fila.appendChild(celda1);
				fila.appendChild(celda2);
				fila.appendChild(celda3);
				fila.appendChild(celda4);

				tabla.appendChild(fila);	

				}

				function borrarFila(button,nomdiv){
				var fila = button.parentNode.parentNode;
				var tabla = document.getElementById(nomdiv).getElementsByTagName('tbody')[0];
				tabla.removeChild(fila);
			}
			
		</script>			
			
	<script type="text/javascript">
		
	//emails externos
	
			var conta=0;			
				
			function adicionarFilaemail(nombre,frm){

				conta+=1;
				var tabla = document.getElementById("nom-"+nombre).tBodies[0];
							
				var fila = document.createElement("TR");
				var celda1 = document.createElement("TD");
				var celda2 = document.createElement("TD");
 
				var txt1 = document.createElement('INPUT');
				var btnimg = document.createElement('IMG');
				
				//alert(nombre+conta);
				txt1.setAttribute("type","text");
				txt1.setAttribute("size","50");
				txt1.setAttribute("id","txtm"+nombre+conta);
				txt1.setAttribute("name","txtmasemail[]");
				txt1.setAttribute("onFocus","coloronFocus(this)");
				txt1.setAttribute("onBlur","colorOffFocus(this);isEmail(document."+frm+".txtm"+nombre+conta+")");
				txt1.setAttribute("class","classtexto");	

				btnimg.setAttribute('src','../../../../imagenes/iconos/deletep.gif');
				btnimg.setAttribute('title','ELIMINAR');
				btnimg.setAttribute('border','0');
				btnimg.setAttribute('height','14');
				btnimg.setAttribute('style','cursor:pointer');
				btnimg.onclick=function(){borrarFila(this,"nom-"+nombre);}
				

				celda1.appendChild(txt1);		
				celda2.appendChild(btnimg);		

				fila.appendChild(celda1);
				fila.appendChild(celda2);
	
				tabla.appendChild(fila);
			}
			
		function validarEmail(valor){

			if(!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3,4})+$/.test(valor))){
				alert("La dirección de email " + valor + " es correcta.");
			}
			else{
			
				document.getElementById('email').focus();
				alert("La dirección de email es incorrecta.");						
			}						
		}
		
	</script>
  
    <style type="text/css">
		<!--
		.style3 {
			color: #FFFFFF;
			font-weight: bold;
		}
		-->
    </style>
</head>
<body>
<div class="pagination"><a href="buscarafiliado.php"><?=_("Nueva Busqueda") ;?></a><a href="newafiliado.php"><?=_("Registrar Afiliado") ;?></a><a href="reportes.php"><?=_("Reporte") ;?></a><a href="estadisticas.php"><?=_("Estadistica") ;?></a><span class="current"><?=_("Config. Cuentas") ;?></span></div>
<h2 class="Box"><?=_("CONFIGURACION DE CUENTAS") ;?>
  <br />
</h2>
	<?
		//VALIDAR ACCESO SAC
		
		validar_permisos("CONFIGURAR_CUENTA",1);
		
		echo _("AREAS EXISTENTES")." ";
		
		$sql="select IDGRUPO,NOMBRE from $con->catalogo.catalogo_grupo order by NOMBRE";
		$con->cmbselectdata("select IDGRUPO,NOMBRE from $con->catalogo.catalogo_grupo order by NOMBRE","cmbarearesp",$_POST["cmbarearesp"],"  onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
	?>

 	<br/><br/>	 
	<form name="frm_sac" id="frm_sac" >		    
		<div id='div-areasac'  ><? include("vista_cuentas.php");?></div>		 
	</form>

	<div id='div-general'></div>	
	
<input type="button" name="btnagregar" id="btnagregar" value="<?=_("AGREGAR NUEVA AREA") ;?>" onclick="document.getElementById('div-area').style.display='block'" />
 
<div id='div-area' style="display:none">
<form id="frm_principal" name="frm_principal" method="post" action="g_configcuentas.php" onSubmit = "return validarIngreso(this)" >
 <br>
<input name="txtopc" type="hidden" id="txtopc" value="1" > 
 
  <table width="82%"  border="0" cellpadding="1" cellspacing="1" bgcolor="#F2F2F2"  style="border:1px dashed #2D5986">
	<tr>
	  <td bgcolor="#336699" ><span class="style3">
	      <?=_("NOMBRE AREA") ;?>
	      <input name="txtnomnuevo" type="text" class="classtexto" id="txtnomnuevo" style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" size="55">
	  </span></td>
	  <td bgcolor="#2D5986"><span class="style3">
	    <?=_("CUENTAS DE EMAILS EXTERNOS") ;?></span></td>
    </tr>
		<tr>
		  <td width="120" rowspan="2" ><table width="200" border="0" cellpadding="1" cellspacing="1" id="principal" >
            <tr>
              <td><?=_("RESPONSABLE") ;?></td>
              <td><?=_("EMAIL") ;?></td>
              <td><input type="button" name="btnmas" id="btnmas" value="+" onclick="adicionarFila('principal')" title="Mas responsables" /></td>
            </tr>
            <tr>
              <td><input type="text" name="txtusuario[]" id="txtusuario"   onfocus="coloronFocus(this);" readonly onblur="colorOffFocus(this);" class="classtexto"/></td>
              <td><input type="text" name="txtemail[]" id="txtemail" size="50" style="text-transform:uppercase;" readonly  onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" class="classtexto"/></td>
              <td><input type="button" name="btnagragar" id="btnagragar" value="..." onclick="ventana_responsable('txtusuario','txtemail')" /></td>
            </tr>
          </table></td>
		  <td width="318"><table width="315" border="0" cellpadding="1" cellspacing="1"  id="nom-principal">
            <tr>
              <td width="279"><input type="text" name="txtmasemail[]" id="txtmasemail" size="50" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);isEmail(document.frm_principal.txtmasemail)" class="classtexto"/></td>
              <td width="20"><input type="button" name="btnmasemail" id="btnmasemail" value="+" title="Mas emails" onclick="adicionarFilaemail('principal','frm_principal')" /></td>
            </tr>

          </table></td>
		</tr>
		<tr>
		  <td><label>
		    
          <div align="right">
            <input type="button" name="btncancelar" id="btncancelar" value="CANCELAR" onclick="document.getElementById('div-area').style.display='none'" />
            <input type="submit" name="btngrabar" id="btngrabar" value="&gt;&gt;&gt; GRABAR"   />
          </div>
		  </label></td>
    </tr>
  </table>  
  
</form> 
  </div>
  
  
</body>
</html>

	<script type="text/javascript"> 
  
 	 new Event.observe('cmbarearesp','change',function()
	  {
			new Ajax.Updater('div-areasac', 'vista_cuentas.php', {
			parameters : { idcodigo : $('cmbarearesp').value },
			method: 'post'
		});
		
	});
	 

//crear nueva ventana de usuarios		
		var validar_func = '';
		var win = null;
		
		function ventana_responsable(usuario,email){
			if (win != null) alert('<?=_('CIERRE LA VENTANA ANTERIOR')?>');
			else
			{
				win = new Window({
					className: "alphacube",
					title: '<?=_("AGREGAR RESPONSABLE")?>',
					width: 700,
					height: 350,
					showEffect: Element.show,
					hideEffect: Element.hide,
					destroyOnClose: true,
					minimizable: false,
					maximizable: false,
					resizable: true,
					opacity: 0.95,
					url: "general.php?usuario="+usuario+"&email="+email
				});

				win.showCenter();
				myObserver = {onDestroy: function(eventName, win1)
				{
					if (win1 == win) {
						win = null;
						Windows.removeObserver(this);
					}
				}
				}
				Windows.addObserver(myObserver);
			}
			return;
		}		
	 </script>
		<script type="text/javascript">	 
	 function grabar_data(){
 
			new Ajax.Updater('div-general', 'g_configcuentas.php', {
			  parameters:  $('frm_sac').serialize(true),
			  method: 'post',
			  onSuccess: function(t)
			  {
					//alert(t.responseText);
					alert('<?=_("SE GRABARON LOS CAMBIOS.")?>');
					 
			  }
			});
			
		}	
		
	</script>