
    <script language="javascript">

      function rellenaCombo(formulario)
      {
        with (document.forms[formulario]){
		
          var cmbprincipal = cmbcuenta[cmbcuenta.selectedIndex].value; // Valor seleccionado en el primer combo.
          var n = cmbprograma.length;
          //var n2 = cmbcanal.length;  
          
          cmbprograma.disabled = false;  
          //cmbcanal.disabled = false; 
          
          for (var i = 0; i < n; ++i){
            cmbprograma.remove(cmbprograma.options[i]); // Eliminamos todas las líneas del combo.
            //cmbcanal.remove(cmbcanal.options[i]); 
          }
		  
          // for (var i = 0; i < n2; ++i){
            // cmbcanal.remove(cmbcanal.options[i]); 
          // }
		  
          cmbprograma[0] = new Option('<?=_("SELECCIONE") ;?>', ''); // Creamos la primera línea del combo.
          //cmbcanal[0] = new Option("S/D", ''); 
          
	if (cmbprincipal != '')  // Si el valor del primer combo es distinto de 'null'.
	 {
   <?
		$rs_cuenta = $con->query("SELECT * FROM catalogo_cuenta ORDER BY NOMBRE");
		
		for ($l = 0; $l < $rs_cuenta->num_rows; ++$l)
		 {
			$reg = $rs_cuenta->fetch_object();	
   ?>
			if (cmbprincipal == '<?=$reg->IDCUENTA;?>')
			 {
    <?          
				$rs_programa = $con->query("SELECT IDPROGRAMA,NOMBRE FROM catalogo_programa WHERE ACTIVO=1 AND IDCUENTA = '".$reg->IDCUENTA."' ORDER BY NOMBRE");
			  
				for ($m = 0; $m < $rs_programa->num_rows; ++$m)
				 {
					$row = $rs_programa->fetch_object();
    ?>
					cmbprograma[cmbprograma.length] = new Option("<?=$row->NOMBRE;?>", '<?=$row->IDPROGRAMA;?>');
    <?
				 }
	  
				//$rs_canal = $con->query("SELECT DISTINCT catalogo_canal_venta.IDCANALVENTA,catalogo_canal_venta.DESCRIPCION FROM catalogo_canal_venta_cuenta INNER JOIN catalogo_canal_venta ON catalogo_canal_venta.IDCANALVENTA=catalogo_canal_venta_cuenta.IDCANALVENTA WHERE catalogo_canal_venta_cuenta.IDCUENTA ='".$reg->IDCUENTA."' ORDER BY catalogo_canal_venta.DESCRIPCION");
      
				//for ($m = 0; $m < $rs_canal->num_rows; ++$m)
				// {
					//$reg = $rs_canal->fetch_object();  
	  
    ?>
					//cmbcanal[cmbcanal.length] = new Option("<?=$reg->DESCRIPCION;?>", '<?=$reg->IDCANALVENTA;?>');	
	
	<?
				//}
   ?>
			 }
   <?
		 }
   ?>
		cmbprograma.focus();
		//cmbcanal.focus();
	 }
    else  // El valor del primer combo es 'null'.
     {
            //cmbcanal.disabled = true;  // Desactivamos el  combo (que estará vacío).
            cmbprograma.disabled = true;
            cmbcuenta.focus();
      }
          
          cmbprograma.selectedIndex = 0;  // Seleccionamos el primer valor del combo ('null').
         // cmbcanal.selectedIndex = 0;  
        }
      }
    </script>	
  	<tr>
        <td width="24"><?=_("CUENTA") ;?></td>
        <td colspan="3">			
        <?
			if($allcuentas==1)	$sql="SELECT IDCUENTA,NOMBRE FROM $con->catalogo.catalogo_cuenta ORDER BY NOMBRE"; else $sql=" SELECT catalogo_cuenta.IDCUENTA,catalogo_cuenta.NOMBRE FROM catalogo_cuenta INNER JOIN $con->temporal.seguridad_acceso_cuenta ON seguridad_acceso_cuenta.IDCUENTA=catalogo_cuenta.IDCUENTA WHERE seguridad_acceso_cuenta.IDUSUARIO='".$_SESSION["user"]."'";			

			$con->cmbselectdata($sql,"cmbcuenta",$_REQUEST["cuenta"],"onChange=\"rellenaCombo('form1')\"  class='classtexto' onFocus='coloronFocus(this);' onBlur='colorOffFocus(this)' ","");			
		?>
		</td>
        <td width="24"><?=_("PLAN") ;?></td>
        <td colspan="3"><select name="cmbprograma" disabled class="classtexto"onfocus="coloronFocus(this);" onblur="colorOffFocus(this);">
				<option value=""><?=_("SELECCIONE") ;?></option>
			  </select></td>
      </tr>		
	 