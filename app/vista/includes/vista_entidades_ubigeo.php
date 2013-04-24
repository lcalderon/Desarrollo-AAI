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

<? if ($n_ent>=1) { ?>
<tr >
	<td><?=_('PAIS')?></td>
	<td>
	<?
	$sql="
	SELECT 
		IDPAIS,NOMBRE 
	FROM 
	$con->catalogo.catalogo_pais ";
	$con->cmbselect_db('CVEPAIS',$sql,$idpais,'id="cvepais" class="classtexto" onfocus="coloronFocus(this);" onBlur="colorOffFocus(this);"','','TODOS');
	?>
	</td>
</tr>
<tr>
	<td><?=_('ENTIDAD 1')?></td>
	<td>
	<?
	$sql="
	select 
	CVEENTIDAD1, DESCRIPCION 
	FROM catalogo_entidad
	WHERE 
	CVEPAIS ='$idpais'
	AND CVEENTIDAD1<> '0'
	AND CVEENTIDAD2='0'
	AND CVEENTIDAD3='0' 
	AND CVEENTIDAD4='0'
	AND CVEENTIDAD5='0'
	AND CVEENTIDAD6='0'
	AND CVEENTIDAD7='0'
	ORDER BY 
	DESCRIPCION
	";
	$con->cmbselect_db('CVEENTIDAD1',$sql,$entfed1_default,'id="cveentidad1" ','','TODOS' );
	?>
	</td>
</tr>
	<?}?>
	
<? if ($n_ent>=2) { ?>
<tr  >
	<td><?=_('ENTIDAD 2')?></td>
	<td>
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
	$con->cmbselect_db('CVEENTIDAD2',$sql,$entfed2_default,'id="cveentidad2" ','','TODOS' );
	?>
	</td>
</tr>
	<?}?>

<? if ($n_ent>=3) { ?>
<tr >
	<td><?=_('ENTIDAD 3')?></td>
	<td>
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
	$con->cmbselect_db('CVEENTIDAD3',$sql,$entfed3_default,'id="cveentidad3"  ','','TODOS' );
	?>
	</td>
</tr>
	<?}?>

<? if ($n_ent>=4) { ?>
<tr >
	<td><?=_('ENTIDAD 4')?></td>
	<td>
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
	$con->cmbselect_db('CVEENTIDAD4',$sql,$entfed4_default,'id="cveentidad4"  ','','TODOS' );
	?>
	</td>
</tr>
	<?}?>	
	
<? if ($n_ent>=5) { ?>
<tr >
	<td><?=_('ENTIDAD 5')?></td>
	<td>
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
	$con->cmbselect_db('CVEENTIDAD5',$sql,$entfed4_default,'id="cveentidad5"  ','','TODOS' );
	?>
	</td>
</tr>
	<?}?>

<? if ($n_ent>=6) { ?>
<tr >
	<td><?=_('ENTIDAD 6')?></td>
	<td>
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
	$con->cmbselect_db('CVEENTIDAD6',$sql,$entfed5_default,'id="cveentidad6"  ','','TODOS' );
	?>
	</td>
</tr>
	<?}?>
	
	<? if ($n_ent>=7) { ?>
<tr >
	<td><?=_('ENTIDAD 7')?></td>
	<td>
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
	$con->cmbselect_db('CVEENTIDAD7',$sql,$entfed6_default,'id="cveentidad7"  ','','TODOS' );
	?>
	</td>
</tr>
	<?}?>


	
<script type="text/javascript">  //  ****************         COMBOS DEPENDIENTES    ***********************//
// cambia opciones en el combo ENTIDAD 1
new Event.observe('cvepais','change',function (){
	new Ajax.Updater('cveentidad1',"/app/controlador/ajax/ajax_entfed1.php",
	{	
		insertion: Insertion.Blank,
		method: 'post',
		parameters: { pais: $F('cvepais') }
	}
	);
	new Ajax.Updater('cveentidad2',"/app/controlador/ajax/ajax_entfed2.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1: $F('cveentidad1') }
	}
	);
	new Ajax.Updater('cveentidad3',"/app/controlador/ajax/ajax_entfed3.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad4',"/app/controlador/ajax/ajax_entfed4.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad5',"/app/controlador/ajax/ajax_entfed5.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
});

// cambia opciones en el combo ENTIDAD 2
new Event.observe('cveentidad1','change',function (){
	new Ajax.Updater('cveentidad2',"/app/controlador/ajax/ajax_entfed2.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1: $F('cveentidad1') }
	}
	);
	new Ajax.Updater('cveentidad3',"/app/controlador/ajax/ajax_entfed3.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad4',"/app/controlador/ajax/ajax_entfed4.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad5',"/app/controlador/ajax/ajax_entfed5.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
});
// cambia opciones en el combo ENTIDAD 3
new Event.observe('cveentidad2','change',function (){
	new Ajax.Updater('cveentidad3',"/app/controlador/ajax/ajax_entfed3.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1 : $F('cveentidad1'), ent2 : $F('cveentidad2') }
	}
	);

	new Ajax.Updater('cveentidad4',"/app/controlador/ajax/ajax_entfed4.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad5',"/app/controlador/ajax/ajax_entfed5.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
});

// cambia opciones en el combo ENTIDAD 4
new Event.observe('cveentidad3','change',function (){
	new Ajax.Updater('cveentidad4',"/app/controlador/ajax/ajax_entfed4.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1 : $F('cveentidad1'), ent2 : $F('cveentidad2'), ent3 : $F('cveentidad3') }
	}
	);

	new Ajax.Updater('cveentidad5',"/app/controlador/ajax/ajax_entfed5.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
});

// cambia opciones en el combo ENTIDAD 5
new Event.observe('cveentidad4','change',function (){
	new Ajax.Updater('cveentidad5',"/app/controlador/ajax/ajax_entfed5.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1 : $F('cveentidad1'), ent2 : $F('cveentidad2'), ent3 : $F('cveentidad3'), ent4 : $F('cveentidad4') }
	}
	);
	new Ajax.Updater('cveentidad6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank}
	);
	new Ajax.Updater('cveentidad7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
});

// cambia opciones en el combo ENTIDAD 6
new Event.observe('cveentidad5','change',function (){
	new Ajax.Updater('cveentidad6',"/app/controlador/ajax/ajax_entfed6.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1 : $F('cveentidad1'), ent2 : $F('cveentidad2'), ent3 : $F('cveentidad3'), ent4 : $F('cveentidad4'), ent5 : $F('cveentidad5') }
	}
	);

	new Ajax.Updater('cveentidad7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank}
	);
});

// cambia opciones en el combo ENTIDAD 7
new Event.observe('cveentidad6','change',function (){
	new Ajax.Updater('cveentidad7',"/app/controlador/ajax/ajax_entfed7.php",
	{	insertion: Insertion.Blank,
	method: 'post',
	parameters: { ent1 : $F('cveentidad1'), ent2 : $F('cveentidad2'), ent3 : $F('cveentidad3'), ent4 : $F('cveentidad4'), ent5 : $F('cveentidad5'), ent6 : $F('cveentidad6') }
	}
	);

});

</script>
