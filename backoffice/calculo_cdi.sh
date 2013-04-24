#!/bin/bash

fechaini=`/bin/date --date="-1 month"  +%Y%m%d`
fechafin=`/bin/date --date="-1 day"  +%Y%m%d`
#fecha=`/bin/date +%Y%m%d`


php /var/www/soaang_preproduccion/backoffice/calculo_cdi.php $fechaini $fechafin
