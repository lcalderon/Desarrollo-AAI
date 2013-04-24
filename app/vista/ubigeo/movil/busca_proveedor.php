<?
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli('','pe');
$sql=" select
        cp.NOMBRECOMERCIAL 
       FROM 
        $con->catalogo.catalogo_proveedor cp
        LEFT JOIN $con->catalogo.catalogo_proveedor_telefono cpt ON cpt.IDPROVEEDOR = cp.IDPROVEEDOR 
        LEFT JOIN $con->temporal.posicionGPS pgps ON  pgps.receptor_id = cpt.NUMEROTELEFONO
       WHERE 
        cp.ACTIVO = 1
        AND cp.INTERNO = 1
        AND cpt.IDTSP = 3
        AND pgps.receptor_id IS NOT NULL
        AND cp.NOMBRECOMERCIAL LIKE '%$_POST[txtnombre]%'
       GROUP BY cp.NOMBRECOMERCIAL 
        ";
$result=$con->query($sql);
?>
<ul>
<? while($reg = $result->fetch_object()):?>
	<li id='' onclick="fill('<?=utf8_encode($reg->NOMBRECOMERCIAL)?>')"><?=utf8_encode($reg->NOMBRECOMERCIAL)?></li>
<? endwhile;?>
</ul>