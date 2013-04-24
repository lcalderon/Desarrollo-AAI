<?php

	include_once('../../../modelo/clase_lang.inc.php');
	include_once('../../../modelo/clase_mysqli.inc.php');
	
	$con = new DB_mysqli();
		
	if ($con->Errno)
	 {
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	session_start();

	$rs_areas=$con->query("SELECT IDGRUPO,NOMBRE,CONCAT(LEFT(NOMBRE,3),IDGRUPO) AS ide FROM $con->catalogo.catalogo_grupo WHERE IDGRUPO LIKE  '".$_REQUEST["idcodigo"]."' and  ACTIVO=1");
 
	$i=0;
	if($regs=$rs_areas->fetch_object())
	 {
		$nombre=strtolower($regs->ide);
		$n=$n+1;

?> 
    <style type="text/css">
		<!--
		.style3 {
			color: #FFFFFF;
			font-weight: bold;
			font-size: 12px;
		}
		-->
    </style>
	<input type="hidden" name="codgrupo" name="codgrupo" value="<?=$regs->IDGRUPO;?>" >
    <table width="82%"  border="0" cellpadding="1" cellspacing="1" bgcolor="#E8E8E8"  style="border:1px dashed #2D5986">
  <tr>
	  <td bgcolor="#336699" ><span class="style3">
	     <?=_("GRUPO ") ;?> <?=$regs->NOMBRE ;?></span></td>
	  <td bgcolor="#336699"><span class="style3">
	    <?=_("GRUPO DE EMAILS EXTERNOS") ;?></span></td>
  </tr>
		<tr>
		  <td width="380" rowspan="2" bgcolor="#ECFFF5" >	  
		  
		  <table width="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#ECFFF5" id="<?=$nombre;?>" >
            <tr>
              <td style="font-size: 12px"><?=_("RESPONSABLE") ;?></td>
              <td style="font-size: 12px"><?=_("EMAIL") ;?></td>
              <td colspan="2"><div align="right">
                <input type="button" name="btnmas" id="btnmas" value="+" onclick="adicionarFila('<?=$nombre;?>')" title="Mas responsables" />
              </div></td>
            </tr>
			<?
				$Sql="SELECT
					  grupo_usuario.IDUSUARIO,
					  catalogo_usuario.EMAIL
					FROM $con->temporal.grupo_usuario
					  INNER JOIN $con->catalogo.catalogo_usuario
						ON catalogo_usuario.IDUSUARIO = grupo_usuario.IDUSUARIO
					WHERE grupo_usuario.IDGRUPO = '".$regs->IDGRUPO."'
					ORDER BY grupo_usuario.IDUSUARIO";

				$rs_data=$con->query($Sql);
				$num=$rs_data->num_rows;
				
			if($num >0)
			 {
				 while($row=$rs_data->fetch_object())
				 {
					$i=$i+1;
			?>			
            <tr>
              <td><input type="text" name="txtusuario[]" id="txtusuario<?=$nombre.$i;?>" value="<?=$row->IDUSUARIO;?>" readonly onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" class="classtexto"/></td>
              <td><input type="text" name="txtemail[]" id="txtemail<?=$nombre.$i;?>" value="<?=$row->EMAIL;?>" size="50" readonly style="text-transform:uppercase;" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);" class="classtexto"/></td>
              <td><input type="button" name="btnagragar" id="btnagragar" value="..." onclick="ventana_responsable('txtusuario<?=$nombre.$i;?>','txtemail<?=$nombre.$i;?>')" /></td>
              <td><? //if($i!=1){?><img src="../../../../imagenes/iconos/deletep.gif" style="cursor:pointer"  title="Eliminar" width="16" height="16" onclick="borrarFila(this,'<?=$nombre;?>')" /><? //} ?></td>
            </tr>
			<?						
				 }
				  $i=0;
			 }
				
				 
			?>	
          </table>
		  
		  </td>
		  <td width="318" bgcolor="#E1FFF0"><table width="315" border="0" cellpadding="1" cellspacing="1" bgcolor="#E1FFF0" id="nom-<?=$nombre;?>">
		  
	  
			<?
				$rs_emailext=$con->query("SELECT IDGRUPO,EMAIL FROM $con->temporal.grupo_emailexterno WHERE IDGRUPO='".$regs->IDGRUPO."' ORDER BY EMAIL");
			?>
            <tr>
              <td colspan="2" align="right"><input type="button" name="btnmasemail" id="btnmasemail" value="+" title="Mas emails" onclick="adicionarFilaemail('<?=$nombre;?>','frm_sac')" /></td>
            </tr>	
			<?
				while($rows=$rs_emailext->fetch_object())
				 {
					$m=$m+1; 				
			?>			  
            <tr>
				<td width="279"><input type="text" name="txtmasemail[]" id="txtmasemail<?=$nombre.$m;?>" value="<?=strtolower($rows->EMAIL);?>" size="50" onfocus="coloronFocus(this);" onblur="colorOffFocus(this);isEmail(document.frm_sac.txtmasemail<?=$nombre.$m;?>)" class="classtexto"/></td>
				<td><img src="../../../../imagenes/iconos/deletep.gif" style="cursor:pointer"  title="Eliminar" width="16" height="16" onclick="borrarFila(this,'nom-<?=$nombre;?>')" /></td>
            </tr>
			<?					 
				 }
				 
				 $m=0;
			?>			

          </table></td>
		</tr>
		<tr>
    </tr>
  </table>  
		    
          <div>
            <input type="button" name="btngrabar" id="btngrabar" value="<?=_(">>> GRABAR GRUPO ");?>" onclick="grabar_data()"/>
          </div>

		<?
				$n=0;
			}
		?>	 