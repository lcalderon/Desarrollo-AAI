#!/bin/bash

if [ $1 = 'pe' ]; then
	ruta="soaang_produccion" ;
elif [ $1 = 'cl' ]; then
	ruta="cl_soaang_produccion" ;
elif [ $1 = 'ar' ]; then
	ruta="ar_soaang_produccion" ;	
elif [ $1 = 'co' ]; then
	ruta="co_soaang_produccion" ;
elif [ $1 = 'pr' ]; then
	ruta="pr_soaang_produccion" ;
elif [ $1 = 'uy' ]; then
	ruta="uy_soaang_produccion" ;
elif [ $1 = 'ec' ]; then
	ruta="ec_soaang_produccion" ;
elif [ $1 = 'do' ]; then
	ruta="do_soaang_produccion" ;	
elif [ $1 = 'cr' ]; then
	ruta="cr_soaang_produccion" ;
elif [ $1 = 'pa' ]; then
	ruta="pa_soaang_produccion" ;
elif [ $1 = 'bo' ]; then
	ruta="bo_soaang_produccion" ;	
fi
 
php /var/www/html/$ruta/app/vista/catalogos/siac/generarHistorico.php $ruta