   <tr>
        <td><span class="style5">
          <?=_("TELEFONO1") ;?> <?=($nombreobj=="titular" or $nombreobj=="contacto")?"<span class='style2'>*</span>":""?>
        </span></td>
        <td colspan="3"><input name="txttelefono<?=$nombreobj?>[]" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class="classtexto" id="txttelefono<?=$nombreobj?>" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[1];?>" /><?
				$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbtelefono0$nombreobj",$tipotelefono[1]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
			?><input type="button" name="btnver1<?=$nombreobj?>" title="Mas..." id="btnver1<?=$nombreobj?>" value="V" style="font-weight:bold;width:30px;font-size:10px;" onClick="verificardiv('telefono1<?=$nombreobj;?>',this.value,this.name);" /><? if($telefono[1]!="" and !$nombreobj){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[1];?>','<?=$telefono[1];?>')" title="Llamar" /><? }?></td>
        <td><span class="style5">
          <?=_("TELEFONO2") ;?> <?=($nombreobj=="titular" or $nombreobj=="contacto")?"<span class='style2'>*</span>":""?>
        </span></td>
        <td colspan="3"><input name="txttelefono<?=$nombreobj?>[]" type="text" class="classtexto" id="txttelefono<?=$nombreobj?>" <?=($_GET["verinfo"])?$readonly:"" ;?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[2];?>"/><?
				$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbtelefono1$nombreobj",$tipotelefono[2]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?><input type="button" name="btnver2<?=$nombreobj;?>" title="Mas..." id="btnver2<?=$nombreobj;?>" value="V" style="font-weight:bold;width:30px;font-size:10px;"  onclick="verificardiv('telefono2<?=$nombreobj;?>',this.value,this.name);" /> <? if($telefono[2]!="" and !$nombreobj){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[1];?>','<?=$telefono[2];?>')" title="Llamar" /><? }?></td>
      </tr>
      <tr>
        <td colspan="4"><div id='telefono1<?=$nombreobj;?>' style="display: none">
            <table width="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFE7D1" style="border:1px dashed #FF8080">
              <tr>
                <td width="156"><span class="style6">
                  <?=_("COD.AREA") ;?>
                </span></td>
                <td width="89"><input name="txtcodigoa0<?=$nombreobj;?>" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtcodigoa0<?=$nombreobj?>" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$codigoa[1];?>"/></td>
              </tr>
              <tr>
                <td><span class="style6">
                  <?=_("PROVEEDOR TELEFONIA") ;?>
                </span></td>
                <td><?
				$sql="select IDTSP,DESCRIPCION from catalogo_tsp order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbtsp0$nombreobj",$tsp[1]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
		?></td>
              </tr>
              <tr>
                <td><span class="style6">
                  <?=_("EXTENSION") ;?>
                </span></td>
                <td><input name="txtextension0<?=$nombreobj;?>" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtextension0<?=$nombreobj?>" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$extension[1];?>" /></td>
              </tr>
            </table>
        </div></td>
        <td colspan="4"><div  id='telefono2<?=$nombreobj;?>' style="display: none">
            <table width="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFE7D1" style="border:1px dashed #FF8080">
              <tr>
                <td width="156"><span class="style6">
                  <?=_("COD.AREA") ;?>
                </span></td>
                <td width="89"><input name="txtcodigoa1<?=$nombreobj;?>" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtcodigoa1<?=$nombreobj?>" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$codigoa[2];?>" /></td>
              </tr>
              <tr>
                <td><span class="style6">
                  <?=_("PROVEEDOR TELEFONIA") ;?>
                </span></td>
                <td><?
				$sql="select IDTSP,DESCRIPCION from catalogo_tsp order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbtsp1$nombreobj",$tsp[2]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
		?></td>
              </tr>
              <tr>
                <td><span class="style6">
                  <?=_("EXTENSION") ;?>
                </span></td>
                <td><input name="txtextension1<?=$nombreobj;?>" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtextension1<?=$nombreobj;?>" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10"  value="<?=$extension[2];?>" /></td>
              </tr>
            </table>
        </div></td>
      </tr>
      <tr>
        <td><span class="style5">
          <?=_("TELEFONO3") ;?>
        </span></td>
        <td colspan="3"><input name="txttelefono<?=$nombreobj;?>[]" type="text" class="classtexto" id="txttelefono<?=$nombreobj;?>"  <?=($_GET["verinfo"])?$readonly:"" ;?> style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[3];?>" /><?
			$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
			$con->cmbselectdata($sql,"cmbtelefono2$nombreobj",$tipotelefono[3]," $disabled onchange=\"verificardiv('+','telefono3',this.value)\" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?><input type="button" name="btnver3<?=$nombreobj;?>" title="Mas..." id="btnver3<?=$nombreobj;?>" value="V" style="font-weight:bold;width:30px;font-size:10px;"  onclick="verificardiv('telefono3<?=$nombreobj;?>',this.value,this.name);" /><? if($telefono[3]!="" and !$nombreobj){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[3];?>','<?=$telefono[3];?>')" title="Llamar" /><? }?></td>
        <td><span class="style5">
          <?=_("TELEFONO4") ;?>
        </span></td>
        <td colspan="3"><span class="style5">
          <input name="txttelefono<?=$nombreobj;?>[]" type="text" class="classtexto" id="txttelefono<?=$nombreobj;?>" style="text-transform:uppercase;" <?=($_GET["verinfo"])?$readonly:"" ;?> onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="15" maxlength="17" value="<?=$telefono[4];?>"/></span><?
				$sql="select IDTIPOTELEFONO,DESCRIPCION from catalogo_tipotelefono order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbtelefono3$nombreobj",$tipotelefono[4]," $disabled onchange=\"verificardiv('+','telefono4',this.value)\" onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","2");
		?><input type="button" name="btnver4<?=$nombreobj;?>" title="Mas..." id="btnver4<?=$nombreobj;?>" value="V" style="font-weight:bold;width:30px;font-size:10px;" onClick="verificardiv('telefono4<?=$nombreobj;?>',this.value,this.name);" /><? if($telefono[4]!="" and !$nombreobj){?><img src="../../../../imagenes/iconos/telefono.jpg" align="absbottom"  border="0" style="cursor:pointer" onclick="llamada('<?=$codigoa[4];?>','<?=$telefono[4];?>')" title="Llamar" /><? }?></td>
      </tr>
      <tr>
        <td colspan="4"><div  id='telefono3<?=$nombreobj;?>' style="display: none">
            <table width="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFE7D1" style="border:1px dashed #FF8080">
              <tr>
                <td width="156"><span class="style6">
                  <?=_("COD.AREA") ;?>
                </span></td>
                <td width="89"><input name="txtcodigoa2<?=$nombreobj;?>" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtcodigoa2<?=$nombreobj;?>" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$codigoa[3];?>"/></td>
              </tr>
              <tr>
                <td><span class="style6">
                  <?=_("PROVEEDOR TELEFONIA") ;?>
                </span></td>
                <td><?
				$sql="select IDTSP,DESCRIPCION from catalogo_tsp order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbtsp2$nombreobj",$tsp[3]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
		?></td>
              </tr>
              <tr>
                <td><span class="style6">
                  <?=_("EXTENSION") ;?>
                </span></td>
                <td><input name="txtextension2<?=$nombreobj;?>" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtextension2<?=$nombreobj;?>" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10"  value="<?=$extension[3];?>"/></td>
              </tr>
            </table>
        </div></td>
        <td colspan="4"><div  id='telefono4<?=$nombreobj;?>' style="display:none">
            <table width="200" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFE7D1" style="border:1px dashed #FF8080">
              <tr>
                <td width="156"><span class="style6">
                  <?=_("COD.AREA") ;?>
                </span></td>
                <td width="89"><input name="txtcodigoa3<?=$nombreobj;?>" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?>  class="classtexto" id="txtcodigoa3<?=$nombreobj;?>" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$codigoa[4];?>"/></td>
              </tr>
              <tr>
                <td><span class="style6">
                  <?=_("PROVEEDOR TELEFONIA") ;?>
                </span></td>
                <td><?
				$sql="select IDTSP,DESCRIPCION from catalogo_tsp order by DESCRIPCION";
				$con->cmbselectdata($sql,"cmbtsp3$nombreobj",$tsp[4]," $disabled onFocus='coloronFocus(this);' onBlur='colorOffFocus(this);' class='classtexto' ","");
		?></td>
              </tr>
              <tr>
                <td><span class="style6">
                  <?=_("EXTENSION") ;?>
                </span></td>
                <td><input name="txtextension3<?=$nombreobj;?>" type="text" <?=($_GET["verinfo"])?$readonly:"" ;?> class="classtexto" id="txtextension3<?=$nombreobj;?>" style="text-transform:uppercase;" onFocus="coloronFocus(this);" onBlur="colorOffFocus(this);" size="12" maxlength="10" value="<?=$extension[4];?>" /></td>
              </tr>
            </table>
        </div></td>
      </tr>