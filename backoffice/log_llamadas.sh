#!/bin/bash

fecha=`/bin/date --date="-1 day"  +%Y%m%d`
#fecha=`/bin/date +%Y%m%d`

php /var/www/soaang_preproduccion/backoffice/log_llamadas.php $fecha
