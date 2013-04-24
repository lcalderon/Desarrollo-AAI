#!/bin/bash

while /bin/true # Mantente en este ciclo por siempre
do
#curl -k https://localhost:800/dev_soaa_ng/app/controlador/alerta.php
php tarea.php
#php alerta.php
curl -k https://192.168.0.188:800/backoffice/alerta.php
#php /var/www/soaang_preproduccion/librerias/xmpphp/XMPPHP/alerta.php
sleep 1 # sleep seguido de un numero se duerme la cantidad
# de segundos que le indiques. Podrias darle tambien
# sleep 10 (cada 10 segundos), sleep 150 (cada 2.5
# minutos), etc.
done
