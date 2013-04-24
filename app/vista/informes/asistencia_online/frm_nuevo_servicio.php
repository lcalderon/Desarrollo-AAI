<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../../../../estilos/styles/blue/rpt_style_web.css" rel="stylesheet" type="text/css">
<link href="../../../../estilos/styles/blue/rpt_style_button.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../../librerias/js/util.js"></script>

<script language="javascript">
function validarCampo() {
	var v_cod_afiliado = document.getElementById('txt_cod_afiliado').value;
	
	if (v_cod_afiliado=='') {
		alert('INGRESE EL CODIGO DE AFILIADO');
		return false;
	}
}

function Validar_afiliado()
{
	if (validarCampo()==true) {
	
		var contenedor = document.getElementById('td_info');
		var v_cod_afiliado = document.getElementById('txt_cod_afiliado').value;
		ajax = nuevoAjax();
		ajax.open("GET","obtener_datos_afiliado.php?va_cod_afiliado="+v_cod_afiliado,true);
		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4){
				contenedor.innerHTML = ajax.responseText
			}
		}
		ajax.send(null)
	}
}

function CerrarModal()
{
	self.parent.tb_remove()
}
</script>
</head>
<body style="margin-top:0px">
<table class="style_table_2" width="100%" border="0" cellpadding="1" cellspacing="0">
<tr>
<td align="right"><img name="img_close" src="../../../../estilos/styles/blue/images/button-close-focus.gif" border="0" style="cursor:pointer" title="Cerrar" onClick="CerrarModal();"></td>
</tr>
</table>
<form name="frmnuevo" id="frmnuevo" method="POST">
<table class="style_table" border="0" width="100%" bordercolor="#0c298f" cellspacing="0" cellpadding="2" align="center">
			<tr>
				<td width="13%" style="padding-top:10px"><div align="right"><strong><?=_("Codigo afiliado:");?></strong></div></td>
				<td width="87%" style="padding-top:10px">
                <table class="style_table" border="0" cellpadding="0" cellspacing="0" bordercolor="#0c298f">
                <tr>
                <td><input class="txt" type="text" id="txt_cod_afiliado" name="txt_cod_afiliado"  maxlength="30" style="width:120"></td>
				<td style="padding-left:8px">
				  <input class="button small blue" type="button" id="btn_validar" name="btn_validar" value="Validar" onClick="Validar_afiliado();">
				</td>
                </tr>
                </table>
                </td>
			</tr>
			<tr>
			  <td colspan="2">&nbsp;</td>
	  </tr>
      <tr>
      <td colspan="2" id="td_info">
      <table class="style_table" border="0" width="100%" bordercolor="#0c298f" cellspacing="0" cellpadding="2" align="center">
      <tr>
			  <td colspan="2">
              <fieldset style="width:97%">
     		  <legend style="color:#333">Datos afiliado:</legend>
              	<table class="style_table" border="0" width="100%" bordercolor="#0c298f" cellspacing="0" cellpadding="2" align="center">
			    <tr>
			      <td width="12%"><div align="right"><strong><?=_("Cuenta:");?></strong></div></td>
			      <td width="21%">
                  <select id="cmbcuentatitular" name="cmbcuentatitular" style="width:150" class="txt">
                  </select>
                  </td>
			      <td width="13%">&nbsp;</td>
			      <td width="22%">&nbsp;</td>
			      <td width="12%"><div align="right"><strong><?=_("Plan:");?></strong></div></td>
			      <td width="20%">
                  <select id="cmbprogramatitular" name="cmbprogramatitular" style="width:150" class="txt">
                  </select>
                  </td>
		        </tr>
			    <tr>
			      <td><div align="right"><strong><?=_("AP. Paterno:");?></strong></div></td>
			      <td><input class="txt" type="text" name="txt_afi_ap_paterno"  maxlength="200" style="width:150"></td>
			      <td><div align="right"><strong><?=_("AP. Materno:");?></strong></div></td>
			      <td><input class="txt" type="text" name="txt_afi_ap_materno"  maxlength="200" style="width:150"></td>
			      <td><div align="right"><strong><?=_("Nombres:");?></strong></div>

                  </td>
			      <td><input class="txt" type="text" name="txt_afi_nombres"  maxlength="200" style="width:150"></td>
		        </tr>
			    <tr>
			      <td><div align="right"><strong><?=_("Telefono 1:");?></strong></div></td>
			      <td><input class="txt" type="text" name="txt_afi_telefono_1"  maxlength="200" style="width:100"></td>
			      <td><div align="right"><strong><?=_("Telefono 2:");?></strong></div></td>
			      <td><input class="txt" type="text" name="txt_afi_telefono_2"  maxlength="200" style="width:100"></td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
		        </tr>
			    </table>
              </fieldset>
              </td>
	  </tr>
			<tr>
			  <td colspan="2">
               <fieldset style="width:97%">
     		   <legend style="color:#333">Datos contacto:</legend>
              	<table class="style_table" border="0" width="100%" bordercolor="#0c298f" cellspacing="0" cellpadding="2" align="center">
			    <tr>
			      <td width="12%"><div align="right"><strong><?=_("AP. Paterno:");?></strong></div></td>
			      <td width="21%"><input class="txt" type="text" name="txt_con_ap_paterno"  maxlength="200" style="width:150"></td>
			      <td width="13%"><div align="right"><strong><?=_("AP. Materno:");?></strong></div></td>
			      <td width="22%"><input class="txt" type="text" name="txt_con_ap_materno"  maxlength="200" style="width:150"></td>
			      <td width="12%"><div align="right"><strong><?=_("Nombres:");?></strong></div>
			        
			        </td>
			      <td width="20%"><input class="txt" type="text" name="txt_con_nombres"  maxlength="200" style="width:150"></td>
			      </tr>
			    <tr>
			      <td><div align="right"><strong><?=_("Telefono 1:");?></strong></div></td>
			      <td><input class="txt" type="text" name="txt_con_telefono_1"  maxlength="200" style="width:100"></td>
			      <td><div align="right"><strong><?=_("Telefono 2:");?></strong></div></td>
			      <td><input class="txt" type="text" name="txt_con_telefono_2"  maxlength="200" style="width:100"></td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
		        </tr>
			    <tr>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
			      <td>&nbsp;</td>
		        </tr>
		      </table>
              </fieldset>
              </td>
	  </tr>
      </table>
      </td>
      </tr>
			<tr>
			  <td colspan="2">&nbsp;</td>
	  </tr>
			<tr>
            <td colspan="2" align="center" style="padding-top:12px">
            <table class="style_table" border="0" cellpadding="4" cellspacing="0" align="center">
            <tr>
            <td style="padding-top:10px"><input class="button small blue" type="button" id="btn_grabar" name="btn_grabar" value="Grabar"></td>
            </tr>
            </table>
            </td>
            </tr>
		</table>
	</form>
</body>
</html>