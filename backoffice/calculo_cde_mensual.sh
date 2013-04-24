#!/bin/bash
fecha=`/bin/date --date="-1 month"  +%Y%m%d`
#fecha=`/bin/date +%Y-%m-%d`
idproveedor=0

php /var/www/soaang_preproduccion/backoffice/calculo_cde_mensual.php $fecha $idproveedor > /var/www/test2.txt
