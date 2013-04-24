<?
// número de niveles de entidad federativa
$con = new DB_mysqli();
$n_ent= $con->lee_parametro('UBIGEO_NIVELES_ENTIDADES');    // para Perú 3 niveles (DEPARTAMENTO/PROVINCIA/DISTRITO)

if (isset($ubigeo->cveentidad1))
{
$idpais =$ubigeo->cvepais;
$entfed1_default=$ubigeo->cveentidad1;
$entfed2_default=$ubigeo->cveentidad2;
$entfed3_default=$ubigeo->cveentidad3;
$entfed4_default=$ubigeo->cveentidad4;
$entfed5_default=$ubigeo->cveentidad5;
$entfed6_default=$ubigeo->cveentidad6;
$entfed7_default=$ubigeo->cveentidad7;
}
else
{
$idpais =$con->lee_parametro('IDPAIS');
$entfed1_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD1');
$entfed2_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD2');
$entfed3_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD3');
$entfed4_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD4');
$entfed5_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD5');
$entfed6_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD6');
$entfed7_default=$con->lee_parametro('UBICACION_PRIMARIA_CVEENTIDAD7');
}
?>
<tr>
<? if ($n_ent>=1) { ?>
	<td><?=_('ENTIDAD 1')?><br>
	<?
	$sql="
	select 
	CVEENTIDAD1, DESCRIPCION 
	FROM catalogo_entidad
	WHERE 
	CVEENTIDAD1<> ''
	AND CVEENTIDAD2='0'
	AND CVEENTIDAD3='0' 
	AND CVEENTIDAD4='0'
	AND CVEENTIDAD5='0'
	AND CVEENTIDAD6='0'
	AND CVEENTIDAD7='0'
	ORDER BY 
	DESCRIPCION
	";
	$con->cmbselect_db('CVEENTIDAD1',$sql,$entfed1_default,'id="cveentidad1" ',"onchange=ent1('cveentidad')",'TODOS' );
	?>
	</td>
	<?}?>
	
<? if ($n_ent>=2) { ?>	
	<td><?=_('ENTIDAD 2')?><br>
	<?
	$sql="
	select 
	CVEENTIDAD2, DESCRIPCION 
	FROM catalogo_entidad
	WHERE 
	CVEENTIDAD1='$entfed1_default'
	AND CVEENTIDAD2<>'0'
	AND CVEENTIDAD3='0' 
	AND CVEENTIDAD4='0'
	AND CVEENTIDAD5='0'
	AND CVEENTIDAD6='0'
	AND CVEENTIDAD7='0'
	ORDER BY 
	DESCRIPCION
	";
	$con->cmbselect_db('CVEENTIDAD2',$sql,$entfed2_default,'id="cveentidad2" ',"onchange=ent2('cveentidad')",'TODOS' );
	?>
	</td>

<?}?>
<? if ($n_ent>=3) { ?>
	<td><?=_('ENTIDAD 3')?><br>
	<?
	$sql="
	select 
	CVEENTIDAD3, DESCRIPCION 
	FROM catalogo_entidad
	WHERE 
	CVEENTIDAD1='$entfed1_default'
	AND CVEENTIDAD2='$entfed2_default'
	AND CVEENTIDAD3<>'0' 
	AND CVEENTIDAD4='0'
	AND CVEENTIDAD5='0'
	AND CVEENTIDAD6='0'
	AND CVEENTIDAD7='0'
	ORDER BY 
	DESCRIPCION
	";
	$con->cmbselect_db('CVEENTIDAD3',$sql,$entfed3_default,'id="cveentidad3"  ',"onchange=ent3('cveentidad')",'TODOS' );
	?>
	</td>

<?}?>	
</tr>
	
<tr>
<? if ($n_ent>=4) { ?>
	<td><?=_('ENTIDAD 4')?><br>
	<?
	$sql="
	select 
	CVEENTIDAD4, DESCRIPCION 
	FROM catalogo_entidad 
	WHERE 
	CVEENTIDAD1='$entfed1_default'
	AND CVEENTIDAD2='$entfed2_default'
	AND CVEENTIDAD3='$entfed3_default' 
	AND CVEENTIDAD4<>'0'
	AND CVEENTIDAD5='0'
	AND CVEENTIDAD6='0'
	AND CVEENTIDAD7='0'
	ORDER BY 
	DESCRIPCION
	";
	$con->cmbselect_db('CVEENTIDAD4',$sql,$entfed4_default,'id="cveentidad4"  ',"onchange=ent4('cveentidad')",'TODOS' );
	?>
	</td>

	<?}?>	
	
<? if ($n_ent>=5) { ?>
	<td><?=_('ENTIDAD 5')?><br>
	<?
	$sql="
	select 
	CVEENTIDAD5, DESCRIPCION 
	FROM catalogo_entidad 
	WHERE 
	CVEENTIDAD1='$entfed1_default'
	AND CVEENTIDAD2='$entfed2_default'
	AND CVEENTIDAD3='$entfed3_default' 
	AND CVEENTIDAD4='$entfed4_default'
	AND CVEENTIDAD5<>'0'
	AND CVEENTIDAD6='0'
	AND CVEENTIDAD7='0'
	ORDER BY 
	DESCRIPCION
	";
	$con->cmbselect_db('CVEENTIDAD5',$sql,$entfed4_default,'id="cveentidad5"  ',"onchange=ent5('cveentidad')",'TODOS' );
	?>
	</td>
	<?}?>

<? if ($n_ent>=6) { ?>

	<td><?=_('ENTIDAD 6')?><br>
	<?
	$sql="
	select 
	CVEENTIDAD6, DESCRIPCION 
	FROM catalogo_entidad
	WHERE 
	CVEENTIDAD1='$entfed1_default'
	AND CVEENTIDAD2='$entfed2_default'
	AND CVEENTIDAD3='$entfed3_default' 
	AND CVEENTIDAD4='$entfed4_default'
	AND CVEENTIDAD5='$entfed5_default'
	AND CVEENTIDAD6<>'0'
	AND CVEENTIDAD7='0'
	ORDER BY 
	DESCRIPCION
	";
	$con->cmbselect_db('CVEENTIDAD6',$sql,$entfed5_default,'id="cveentidad6"  ',"onchange=ent6('cveentidad')",'TODOS' );
	?>
	</td>

	<?}?>
</tr>

<tr>
	<? if ($n_ent>=7) { ?>
	<td><?=_('ENTIDAD 7')?><br>
	<?
	$sql="
	select 
	CVEENTIDAD7, DESCRIPCION 
	FROM catalogo_entidad 
	WHERE 
	CVEENTIDAD1='$entfed1_default'
	AND CVEENTIDAD2='$entfed2_default'
	AND CVEENTIDAD3='$entfed3_default' 
	AND CVEENTIDAD4='$entfed4_default'
	AND CVEENTIDAD5='$entfed5_default'
	AND CVEENTIDAD6='$entfed6_default'
	AND CVEENTIDAD7<>'0'
	ORDER BY 
	DESCRIPCION
	";
	$con->cmbselect_db('CVEENTIDAD7',$sql,$entfed6_default,'id="cveentidad7"  ',"onchange=ent7('cveentidad')",'TODOS' );
	?>
	</td>

	<?}?>
</tr>

	
<script type="text/javascript">  //  ****************         COMBOS DEPENDIENTES    ***********************//

// cambia opciones en el combo ENTIDAD 2
function ent1(campo){

	new Ajax.Updater(campo+'2',"/app/controlador/ajax/ajax_entfed2.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1: $F(campo+'1') }
	}
	);
	new Ajax.Updater(campo+'3',"/app/controlador/ajax/ajax_entfed3.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater(campo+'4',"/app/controlador/ajax/ajax_entfed4.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater(campo+'5',"/app/controlador/ajax/ajax_entfed5.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater(campo+'6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater(campo+'7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
}



// cambia opciones en el combo ENTIDAD 3
function ent2(campo){
	new Ajax.Updater(campo+'3',"/app/controlador/ajax/ajax_entfed3.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1 : $F(campo+'1'), ent2 : $F(campo+'2') }
	}
	);

	new Ajax.Updater(campo+'4',"/app/controlador/ajax/ajax_entfed4.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater(campo+'5',"/app/controlador/ajax/ajax_entfed5.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater(campo+'6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater(campo+'7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
}

// cambia opciones en el combo ENTIDAD 4
function ent3(campo){
	new Ajax.Updater(campo+'4',"/app/controlador/ajax/ajax_entfed4.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1 : $F(campo+'1'), ent2 : $F(campo+'2'), ent3 : $F(campo+'3') }
	}
	);

	new Ajax.Updater(campo+'5',"/app/controlador/ajax/ajax_entfed5.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater(campo+'6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater(campo+'7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
}

// cambia opciones en el combo ENTIDAD 5
function ent4(campo){
	new Ajax.Updater(campo+'5',"/app/controlador/ajax/ajax_entfed5.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1 : $F(campo+'1'), ent2 : $F(campo+'2'), ent3 : $F(campo+'3'), ent4 : $F(campo+'4') }
	}
	);
	new Ajax.Updater(campo+'6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater(campo+'7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
}

// cambia opciones en el combo ENTIDAD 6
function ent5(campo){
	new Ajax.Updater(campo+'6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1 : $F(campo+'1'), ent2 : $F(campo+'2'), ent3 : $F(campo+'3'), ent4 : $F(campo+'4'), ent5 : $F(campo+'5') }
	}
	);

	new Ajax.Updater(campo+'7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
}

// cambia opciones en el combo ENTIDAD 7
function ent6(campo){
	new Ajax.Updater(campo+'7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1 : $F(campo+'1'), ent2 : $F(campo+'2'), ent3 : $F(campo+'3'), ent4 : $F(campo+'4'), ent5 : $F(campo+'5'), ent6 : $F(campo+'6') }
	}
	);

}

</script>
