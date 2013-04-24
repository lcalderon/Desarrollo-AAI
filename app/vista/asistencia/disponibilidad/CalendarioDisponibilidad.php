<?php

/**
 * EasyWeeklyCalClass V 1.0. A class that generates a weekly schedule easily configurable *
 * @author Ruben Crespo Alvarez [rumailster@gmail.com] http://peachep.wordpress.com
 */

class EasyWeeklyCalClass {

    var $dia;
    var $mes;
    var $ano;
    var $date;


    function EasyWeeklyCalClass ($dia, $mes, $ano) {

        $this->dia = $dia;
        $this->mes = $mes;
        $this->ano = $ano;
        $this->date = $this->showDate ($hora, $min, $seg, $mes, $dia, $ano);
    }


    function showCalendar ($idasistencia) {
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
	$fec = date('YmdHi');
	$con->select_db($con->temporal);
	$catalogo = $con->catalogo;
	$tablatmp = 'tmp_'.$fec.$idasistencia;
	//echo $tablatmp;
	$sql_create_tmp = "CREATE TABLE $tablatmp (`ID` int(10) unsigned NOT NULL auto_increment,
  `IDPROVEEDOR` int(10) default NULL,
  `FECHAHORA` datetime default NULL,
  PRIMARY KEY  (`ID`)
)";
//echo $sql_create_tmp;
      $exec_create_tmp = $con->query($sql_create_tmp);


	$sql="SELECT * FROM $catalogo.catalogo_proveedor WHERE INTERNO = 1 AND ACTIVO =1";
	$exec = $con->query($sql);
	$rows = $exec->num_rows;
	//echo $rows;
        $Output .= "<table border='1' width='99%' class='table1'>";
	$Output .= $this->buttonsWeek ($this->dia, $this->mes, $this->ano, $this->date["numDiasMes"],$idasistencia,$rows);
        //$Output .= $this->buttons ($this->dia, $this->mes, $this->ano, $this->date["numDiasMes"],$idasistencia);
        $Output .= $this->WeekTable ($this->date ["diaMes"], $this->date ["diaSemana"], $this->date["numDiasMes"], $this->date["nombreMes"], $this->dia, $this->mes, $this->ano,$idasistencia,$rows,$sql,$tablatmp);
        $Output .= "</table>";

      $sql_drop_tmp = "DROP TABLE $tablatmp";
      $exec_drop_tmp = $con->query($sql_drop_tmp);
        return $Output;
    }
    
    
    function WeeksInMonth ($mes, $leapYear, $firstDay){
        if ($mes == 1 or $mes == 3 or $mes == 5 or $mes == 7 or $mes == 8 or $mes == 10 or $mes == 12) {
    
            if ($firstDay > 5) {
                return 6;
            } else {
                return 5;
            }
        
        } else if ($mes == 4 or $mes == 6 or $mes == 9 or $mes == 11) {
        
            if ($firstDay == 7) {
                return 6;
            } else {
                return 5;
            }
        
        
        } else if ($mes == 2) {
            
            if ($leapYear == "0" and $firstDay == 1) {
                return 4;
            }else{
                return 5;
            }
            
        }
        
    
    }


    function showDate ($hora, $min, $seg, $mes, $dia, $ano){
include_once('../../../modelo/clase_mysqli.inc.php');
$con = new DB_mysqli();
  $con->select_db($con->temporal);
	$catalogo = $con->catalogo;
        $fecha = mktime ($hora, $min, $seg, $mes, $dia, $ano);
	$MESTEXTO = date ("F", $fecha);
	switch($MESTEXTO){
	  case 'January':  $MESFORMATOTEXTO ='ENE';break;
	  case 'February':  $MESFORMATOTEXTO ='FEB';break;
	  case 'March':  $MESFORMATOTEXTO ='MAR';break;
	  case 'April':  $MESFORMATOTEXTO ='ABR';break;
	  case 'May':  $MESFORMATOTEXTO ='MAY';break;
	  case 'June':  $MESFORMATOTEXTO ='JUN';break;
	  case 'July':  $MESFORMATOTEXTO ='JUL';break;
	  case 'August':  $MESFORMATOTEXTO ='AGO';break;
	  case 'September':  $MESFORMATOTEXTO ='SEP';break;
	  case 'October':  $MESFORMATOTEXTO ='OCT';break;
	  case 'November':  $MESFORMATOTEXTO ='NOV';break;
	  case 'December':  $MESFORMATOTEXTO ='DIC';break;
	}
        $cal ["diaMes"] = date ("d", $fecha);
        $cal ["nombreMes"] = $MESFORMATOTEXTO; 
        $cal ["numDiasMes"] = date ("t", $fecha); 
        
        if (date ("w", $fecha) == "0")
        {
            $cal ["diaSemana"] = 7;
        } else {
            $cal ["diaSemana"] = date ("w", $fecha);
        }
        
        $cal ["nombreDiaSem"] = date ("l", $fecha);
        $cal ["leapYear"] = date ("L", $fecha);
       
        
       
        return $cal;
    }
    

    function dayName ($dia) {
    
        if ($dia == 1)
        {
            $Output = "LUNES";
        } else if ($dia == 2) {
            $Output = "MARTES";
        } else if ($dia == 3) {
            $Output = "MIERCOLES";
        } else if ($dia == 4) {
            $Output = "JUEVES";
        } else if ($dia == 5) {
            $Output = "VIERNES";
        } else if ($dia == 6) {
            $Output = "SABADO";
        } else if ($dia == 7) {
            $Output = "DOMINGO";
        }

        return $Output;
    }
           

    function previousMonth ($dia, $mes, $ano){
        $mes = $mes-1;
        $mes= $this->showDate ($hora, $min, $seg, $mes, $dia, $ano);
        return $mes;
    }
    

    function nextMonth ($dia, $mes, $ano){
        $mes = $mes+1;
        $mes= $this->showDate ("10", "00", "00", $mes, 1, $ano);
        return $mes;
    }
        
    
  
    function numberOfWeek ($dia, $mes, $ano) {
        $firstDay = $this->showDate ($hora, $min, $seg, $mes, 1, $ano);
        $numberOfWeek = ceil (($dia + ($firstDay ["diaSemana"]-1)) / 7);
        return $numberOfWeek;
    }
   


    function WeekTable ($diaMes, $diaSemana, $numDiasMes, $nombreMes, $dia, $mes, $ano,$idasistencia,$rows,$sql_x,$tablatmp) {
    
    include_once('../../../modelo/clase_mysqli.inc.php');
    $con = new DB_mysqli();
$con->select_db($con->temporal);
	$catalogo = $con->catalogo;
      $exec = $con->query($sql_x);

    $colores = array('#B5C0F7','#B5F7CD','#F38B8B','#F3EF8B','#B18BF3','#B8F38B','#F3BF8B','#A49B9B','#89BDE1','#A6DFA8','#D8E873');
      $c =0;
      while($rset_nom=$exec->fetch_object()){

	    $sql_asignado="SELECT AP.IDPROVEEDOR,A.IDASISTENCIA,S.IDSERVICIO,P.INTERNO,S.TECS,AP.TEAT,IF(AP.TEC='0000-00-00 00:00:00',ADDDATE(AP.TEAT, INTERVAL S.TECS MINUTE),AP.TEC) TEC FROM
	      asistencia_asig_proveedor AP INNER JOIN $catalogo.catalogo_proveedor P
	      ON  AP.IDPROVEEDOR = P.IDPROVEEDOR
	      INNER JOIN asistencia A
	      ON AP.IDASISTENCIA = A.IDASISTENCIA
	      INNER JOIN $catalogo.catalogo_servicio S
	      ON A.IDSERVICIO = S.IDSERVICIO
	      WHERE P.INTERNO = 1 AND P.ACTIVO = 1 AND P.IDPROVEEDOR=".$rset_nom->IDPROVEEDOR;
	      $exec_asignado = $con->query($sql_asignado);
	      while($rset_asignado=$exec_asignado->fetch_object()){
		  $TEAT = $rset_asignado->TEAT;
		  $TEC = $rset_asignado->TEC;
		  $horaini = substr($TEAT,11,2);
		  $horafin = substr($TEC,11,2);
		  $varanio =substr($TEAT,0,4);
		  $varmes = substr($TEAT,5,2);
		  $vardia = substr($TEAT,8,2);
		  for($j=$horaini;$j<=$horafin;$j++){
		      $fechahora=$varanio.'-'.$varmes.'-'.$vardia.' '.$j.':00:00';
		      $sql_insert = "INSERT INTO $tablatmp(IDPROVEEDOR,FECHAHORA) VALUES(".$rset_nom->IDPROVEEDOR.",'".$fechahora."')";
		      //echo $sql_insert;
		      $exec_insert = $con->query($sql_insert);
     
		  }

	      }
	    
	 // $cab1 .= "<td style='background:#98A5D9'>D"."</td>";
	      $idprov[] = $rset_nom->IDPROVEEDOR;
	      $var = $rset_nom->IDPROVEEDOR;
	      //cabecera de campo - idproveedor
	      for($k=0;$k<8;$k++){
		      $texto .= substr($rset_nom->NOMBRECOMERCIAL,$k,1).'<br>';
		      
		}
		$background = $colores[$c];
		$cab .= "<th align='center' style='background:$background'><p style='font-weight:bold; font-size:0.8em;'>".$texto."</p></th>";
		$texto = '';
	     // $campo = $rset->$var;
	      //celda cuando no esta disponible
	      $td .= "<td >&nbsp;"."</td>";
	      $c++;
	}

       $rows = $rows+1;
		
        if ($diaSemana == 0)
        {
            $diaSemana = 7;
        }
            
        $n = 0;
       // echo 'variables '.$dia.' '.$mes,' '.$ano;
        /*NUMBER OF WEEKS AND WEEK NUMBER*/      
        $WeekNumber = $this->showDate ($hora, $min, $seg, $mes, 1, $ano);    
        $WeeksInMonth = $this->WeeksInMonth ($mes, $WeekNumber["leapYear"], $WeekNumber["diaSemana"]); 
        $numberOfWeek = $this->numberOfWeek ($dia, $mes, $ano);
        
        $Output .="<tr><th align='center' style='font-weight:bold; font-size:10px' colspan=2 rowspan=2>HORA</th>";
        //<td>".$numberOfWeek."&ordf; SEMANA DE ".$WeeksInMonth."</td>";

        $resta = $diaSemana - 1;
        $diaMes = $diaMes - $resta;

        //Hasta llegar al dia seleccionado
        for ($i=1; $i < $diaSemana; $i++)
        {

            if ($diaMes < 1)
            {
                $previousMonth = $this->previousMonth ($dia, $mes, $ano);
                $diasAnterior = $previousMonth ["numDiasMes"];
                $nameAnterior = $previousMonth ["nombreMes"];

                if ($mes == 1)
                {
                    $mesVar = 12;
                    $anoVar = $ano - 1;
                    
                } else {
                
                    $mesVar = $mes - 1;
                    $anoVar = $ano;
                }

                $cambio = 1;
                $diaMes = $diasAnterior + $diaMes;
                
            } else {
            
                if ($cambio != 1)
                {
                    $nameAnterior = $nombreMes;
                    $mesVar = $mes;
                    $anoVar = $ano;
                }
            }
			
			
            if ($diaMes == date('d') && $mes==date('m') && $ano ==date('Y'))
            {
	  //echo 'yyyyy'.$diaMes;
            
		  $Output .="<th style='background:#95A2D5; font-weight:bold; font-size:10px' colspan='$rows'>".$this->dayName ($i)."<br>".$diaMes."</th>";
             
            }else{
            
            $Output .="<th style='font-weight:bold; font-size:10px' colspan='$rows'>".$this->dayName ($i)."<br>".$diaMes."</th>";
            }
			if($diaMes<=9){
				$diaMes='0'.$diaMes;
			}
			if($mesVar<=9){
				$mesVar='0'.$mesVar;
			}
            $diaEnlace[$n]["dia"] = $diaMes;
            $diaEnlace[$n]["mes"] = $mesVar;
            $diaEnlace[$n]["ano"] = $anoVar;


            if ($diaMes == $previousMonth["numDiasMes"])
            {
                $diaMes = 1;
                $cambio = 0;
            }else{
                $diaMes ++;
            }

            $n++;

        }



        //Seguimos a partir del dia seleccionado
        for ($diaSemana; $diaSemana <= 7; $diaSemana++)
        {

            if ($diaMes > $numDiasMes)
            {
                $mesS = $this->nextMonth ($dia, $mes, $ano);
                $nameSiguiente = $mesS ["nombreMes"];
                if ($mes == 12)
                {
                    $mesVar = 1;
                    $anoVar = $ano + 1;
                } else {
                    $mesVar = $mes + 1;
                }

                $cambio = 1;
                $diaMes = 1;

            } else {

                if ($cambio != 1)
                {
                    $nameSiguiente = $nombreMes;
                    $mesVar = $mes;
                    $anoVar = $ano;
                }
            }

            if ($diaMes == date('d') && $mes==date('m') && $ano ==date('Y'))
            {
		
		  $Output .="<th style='background:#95A2D5; font-weight:bold; font-size:10px' colspan='$rows'>".$this->dayName ($i)."<br>".$diaMes." </th>";
                
                
            }else{
                $Output .="<th style='font-weight:bold; font-size:10px' colspan='$rows'>".$this->dayName ($diaSemana)."<br>".$diaMes." </th>";
            }
			if($diaMes<=9){
				$diaMes='0'.$diaMes;
			}
			if($mesVar<=9){
				$mesVar='0'.$mesVar;
			}
            $diaEnlace[$n]["dia"] = $diaMes;
            $diaEnlace[$n]["mes"] = $mesVar;
            $diaEnlace[$n]["ano"] = $anoVar;
            $n++;

            $diaMes ++;
            
        }
		$Output .="</tr>";
	
			$dia1 = $diaEnlace[0]["dia"];
			$dia2 = $diaEnlace[1]["dia"];
			$dia3 = $diaEnlace[2]["dia"];
			$dia4 = $diaEnlace[3]["dia"];
			$dia5 = $diaEnlace[4]["dia"];
			$dia6 = $diaEnlace[5]["dia"];
			$dia7 = $diaEnlace[6]["dia"];

			$idcheck1=0;
			$idcheck2=1;
			$idcheck3=2;
			$idcheck4=3;
			$idcheck5=4;
			$idcheck6=5;
			$idcheck7=6;
	
        if($diaEnlace[0]["ano"]<date("Y")){

	  $Output .="<tr><th align=center><input type=checkbox  disabled ></th>".$cab."<th align=center><input type=checkbox disabled ></th>".$cab."<th align=center><input type=checkbox disabled></th>".$cab."<th align=center><input type=checkbox disabled></th>".$cab."<th align=center><input type=checkbox disabled></th>".$cab."<th align=center><input type=checkbox disabled></th>".$cab."<th align=center><input type=checkbox disabled></th>".$cab."</tr>";
	}elseif($diaEnlace[0]["ano"]==date("Y")){
	      if($diaEnlace[0]["mes"]<date("m")){
		    $Output .="<tr><th align=center><input type=checkbox  disabled ></th>".$cab."<th align=center><input type=checkbox disabled ></th>".$cab."<th align=center><input type=checkbox disabled></th>".$cab."<th align=center><input type=checkbox disabled></th>".$cab."<th align=center><input type=checkbox disabled></th>".$cab."<th align=center><input type=checkbox disabled></th>".$cab."<th align=center><input type=checkbox disabled></th>".$cab."</tr>";			
	      }elseif($diaEnlace[0]["mes"]==date("m")){
		
					if($dia1<date('d')){ $enabled1 = 'disabled'; }
					if($dia2<date('d')){ $enabled2 = 'disabled'; }
					if($dia3<date('d')){ $enabled3 = 'disabled'; }
					if($dia4<date('d')){ $enabled4 = 'disabled'; }
					if($dia5<date('d')){ $enabled5 = 'disabled'; }
					if($dia6<date('d')){ $enabled6 = 'disabled'; }
					if($dia7<date('d')){ $enabled7 = 'disabled'; }
		    $Output .="<tr><th align=center><input type=checkbox id=".$idcheck1." ".$enabled1." onclick='marcartodo(".$idcheck1.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck2." ".$enabled2." onclick='marcartodo(".$idcheck2.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck3." ".$enabled3." onclick='marcartodo(".$idcheck3.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck4." ".$enabled4." onclick='marcartodo(".$idcheck4.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck5." ".$enabled5." onclick='marcartodo(".$idcheck5.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck6." ".$enabled6." onclick='marcartodo(".$idcheck6.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck7." ".$enabled7." onclick='marcartodo(".$idcheck7.")'></th>".$cab."</tr>";	
	
	      }else{
		    $Output .="<tr><th align=center><input type=checkbox id=".$idcheck1." ".$enabled1." onclick='marcartodo(".$idcheck1.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck2." ".$enabled2." onclick='marcartodo(".$idcheck2.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck3." ".$enabled3." onclick='marcartodo(".$idcheck3.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck4." ".$enabled4." onclick='marcartodo(".$idcheck4.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck5." ".$enabled5." onclick='marcartodo(".$idcheck5.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck6." ".$enabled6." onclick='marcartodo(".$idcheck6.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck7." ".$enabled7." onclick='marcartodo(".$idcheck7.")'></th>".$cab."</tr>";
	
	      }
	}else{
	    $Output .="<tr><th align=center><input type=checkbox id=".$idcheck1." ".$enabled1." onclick='marcartodo(".$idcheck1.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck2." ".$enabled2." onclick='marcartodo(".$idcheck2.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck3." ".$enabled3." onclick='marcartodo(".$idcheck3.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck4." ".$enabled4." onclick='marcartodo(".$idcheck4.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck5." ".$enabled5." onclick='marcartodo(".$idcheck5.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck6." ".$enabled6." onclick='marcartodo(".$idcheck6.")'></th>".$cab."<th align=center><input type=checkbox id=".$idcheck7." ".$enabled7." onclick='marcartodo(".$idcheck7.")'></th>".$cab."</tr>";
	}


        for ($i=0; $i < 24;$i++)
        {

	

			if($i<=9){
					$i='0'.$i;
			}
	    if($i%2==0)  $Output .="<tr>"; else $Output .="<tr style='background:#D6E7F3'>";
	    

					$Output .="
<th align='center'><b>".$i.":00</b></th><th align='center'><input type='checkbox' name=chkhora[] id=".$i." onclick='habilitachkhora(".$i.")'></b></th>";
	
            for ($n=0; $n<=6; $n++)
            {
			$sql_disponibilidad="SELECT * FROM asistencia_disponibilidad_afiliado WHERE IDASISTENCIA = $idasistencia
		AND DATE_FORMAT(FECHAHORA,'%H')=$i AND DATE_FORMAT(FECHAHORA,'%d')=".$diaEnlace[$n]["dia"]." AND DATE_FORMAT(FECHAHORA,'%m')=".$diaEnlace[$n]["mes"]." AND DATE_FORMAT(FECHAHORA,'%Y')=".$diaEnlace[$n]["ano"];
		
		$exec_disponibilidad = $con->query($sql_disponibilidad);
		while($rset_disponibilidad=$exec_disponibilidad->fetch_object())
		{
			$XFECHAHORA= $rset_disponibilidad->FECHAHORA;
			$XANIO = substr($XFECHAHORA,0,4);
			$XMES = substr($XFECHAHORA,5,2);
			$XDIA = substr($XFECHAHORA,8,2);
			$XHORA = substr($XFECHAHORA,11,2);
		}
		
		     $sql="SELECT FECHAHORA,";
      for($j=0;$j<count($idprov);$j++){
	  if($j==count($idprov)-1){
	      $sql .= "MAX(CASE(IDPROVEEDOR) WHEN ".$idprov[$j]." THEN 'A' ELSE '' END) AS 'PROV$idprov[$j]' ";
	   }else{
	      $sql .= "MAX(CASE(IDPROVEEDOR) WHEN ".$idprov[$j]." THEN 'A' ELSE '' END) AS 'PROV$idprov[$j]', ";
	   }
      }
   $sql .= "  FROM $tablatmp
      WHERE date_format(FECHAHORA,'%H')=$i
 AND DATE_FORMAT(FECHAHORA,'%d')=".$diaEnlace[$n]["dia"]." AND DATE_FORMAT(FECHAHORA,'%m')=".$diaEnlace[$n]["mes"]." AND DATE_FORMAT(FECHAHORA,'%Y')=".$diaEnlace[$n]["ano"]." GROUP BY FECHAHORA";
//echo $sql;
  $exec = $con->query($sql);
  $cont = $exec->num_rows;
if($cont==0){
 
         $td1 = $td;
  }else{
	
	while($rset=$exec->fetch_object()){
	    //if($total_prov==1){
		for($h=0;$h<count($idprov);$h++){
		    $idp = $idprov[$h];
		    //echo 'p'.$idp;
		    $campo = 'PROV'.$idp;
		    //echo 'xxx'.$rset->$campo;
		    //if()
		    $background = $colores[$h];
		    if($rset->$campo==''){
		      $td1 .= "<td >&nbsp;".$rset->$campo."</td>";
		    }else{
		      $td1 .= "<td style='background:$background;font-size:0.7em;font-weight:bold;' align=center>".$rset->$campo."</td>";
		    }  
		}
	}	
  }
		  
			if($diaEnlace[$n]["ano"]<date("Y")){
				if($diaEnlace[$n]["ano"] == $XANIO && $diaEnlace[$n]["mes"]==$XMES && $diaEnlace[$n]["dia"]==$XDIA &&$i==$XHORA){
				      $Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n." checked disabled value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' /></td>".$td1;
				}
				else{
				      $Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n."  disabled value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' /></td>".$td1;
				}
			      $td1='';
			}elseif($diaEnlace[$n]["ano"]==date("Y")){
				if($diaEnlace[$n]["mes"]<date("m"))
				{
					if($diaEnlace[$n]["ano"] == $XANIO && $diaEnlace[$n]["mes"]==$XMES && $diaEnlace[$n]["dia"]==$XDIA &&$i==$XHORA){
						
							$Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n." checked disabled value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' /></td>".$td1;
												
					}
					else{
						$Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n."  disabled value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' /></td>".$td1;
					}
					$td1='';
				}elseif($diaEnlace[$n]["mes"]==date("m")){
				
					if($diaEnlace[$n]["dia"]<date("d")){
						if($diaEnlace[$n]["ano"] == $XANIO && $diaEnlace[$n]["mes"]==$XMES && $diaEnlace[$n]["dia"]==$XDIA &&$i==$XHORA){
						   	$Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n." checked disabled value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' /></td>".$td1;
						   
							
						}
						else{
							$Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n."  disabled value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' /></td>".$td1;
						}
						 $td1='';
					}
					else{
						if($diaEnlace[$n]["ano"] == $XANIO && $diaEnlace[$n]["mes"]==$XMES && $diaEnlace[$n]["dia"]==$XDIA &&$i==$XHORA){
						
							$Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n." checked value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' /></td>".$td1;
						    
						}else{
							
						       $Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n." value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' /></td>".$td1;
						 
						     
						}
						 $td1='';
					}
				}else{

				      
					if($diaEnlace[$n]["ano"] == $XANIO && $diaEnlace[$n]["mes"]==$XMES && $diaEnlace[$n]["dia"]==$XDIA &&$i==$XHORA){
					     
				      		
						      $Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n." checked value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' />"."</td>".$td1;
						 
					}
					else{
						
						      $Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n." value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' /></td>".$td1;
						 
						
					}
					 $td1='';
						
				}

			}
			else{
				if($diaEnlace[$n]["ano"] == $XANIO && $diaEnlace[$n]["mes"]==$XMES && $diaEnlace[$n]["dia"]==$XDIA &&$i==$XHORA){
					     
						      $Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n." checked value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' />"."</td>".$td1;
						 
				      
				}
				else{
					$Output .= "<td align='center'><input type='checkbox' name='programDay[]' id=".$i.$n." value='".$i.$diaEnlace[$n]["dia"].$diaEnlace[$n]["mes"].$diaEnlace[$n]["ano"]."&"."' />"."</td>".$td1;
				}
				 $td1='';
			}
           }


            $Output .="</tr>";
        }

        return $Output;
    }



	function buttonsWeek ($dia, $mes, $ano, $numDiasMes,$idasistencia,$rows) {
		$thisMonth= $this->showDate ($hora, $min, $seg, $mes, $dia, $ano);
		$thisMontOne = $this->showDate ($hora, $min, $seg, $mes, 1, $ano);
	    $previousMonth = $this->previousMonth ($dia, $mes, $ano);
        $WeeksInMonth = $this->WeeksInMonth ($mes, $thisMonth["leapYear"], $thisMonth["diaSemana"]);
        $numberOfWeek = $this->numberOfWeek ($dia, $mes, $ano);      
        $diasRestan = (7 - $thisMonth["diaSemana"]);
      $rows=$rows+1;
      $colspan = ($rows*7)+2;

        //BOTON ANT
        if ($dia <= 7)
        {
        
         $ant = $previousMonth["numDiasMes"] - (($thisMontOne["diaSemana"]-1)) + 1;
            $mesAnt = $mes - 1;

            if ($mes == 1)
            {
                $anoAnt = $ano-1;
                $mesAnt = 12;
            } else {
                $anoAnt = $ano;
            }


        }else{
        
            $ant = $dia - ($thisMonth["diaSemana"] + 6);
            $mesAnt= $mes;
            $anoAnt = $ano;
        }
      



        //BOTON POST
	//echo 'number'.$numberOfWeek;
	//echo 'week'.$WeeksInMonth;
        if ($numberOfWeek == $WeeksInMonth)
        {
            $post="1";
            $mesPost=$mes+1;

            if ($mes == 12)
            {
                $anoPost = $ano+1;
                $mesPost = 1;
            } else {
                $anoPost = $ano;
            }

        }else{

            $post=$dia+($diasRestan+1);
            $mesPost=$mes;
            $anoPost = $ano;
        }


        $Output .= "<tr><th colspan='$colspan'><p style='font-weight:bold; font-size:0.9em;'>

<a href='".$PHP_SELF."?dia=".date('d')."&mes=".date('m')."&ano=".date('Y')."&idasistencia=".$idasistencia."'> HOY </a> |

<a href='".$PHP_SELF."?dia=".$post."&mes=".$mesPost."&ano=".$anoPost."&idasistencia=".$idasistencia."'>SEMANA SIGUIENTE &raquo;</a>
</p></th></tr>";

        return $Output;
	
	}




    function buttons ($dia, $mes, $ano, $numDiasMes,$idasistencia){
        $previousMonth = $this->previousMonth ($dia, $mes, $ano);
        $nextMonth = $this->nextMonth ($dia, $mes, $ano);

        $ant= $dia-1;


        //BOTON ANT
        if ($dia == 1)
        {
            $ant = $previousMonth ["numDiasMes"];
            $mesAnt = $mes - 1;

            if ($mes == 1)
            {
                $anoAnt = $ano-1;
                $mesAnt = 12;
            } else {
                $anoAnt = $ano;
            }


        }else{
            $ant = $dia - 1;
            $mesAnt= $mes;
            $anoAnt = $ano;
        }




        //BOTON POST
        if ($dia == $numDiasMes)
        {
            $post="1";
            $mesPost=$mes+1;

            if ($mes == 12)
            {
                $anoPost = $ano+1;
                $mesPost = 1;
            } else {
                $anoPost = $ano;
            }

        }else{

            $post=$dia+1;
            $mesPost=$mes;
            $anoPost = $ano;
        }


        $Output .= "<tr><th colspan='9'><p style='font-weight:bold; font-size:0.8em;'>

<a href='".$PHP_SELF."?dia=".$ant."&mes=".$mesAnt."&ano=".$anoAnt."&idasistencia=".$idasistencia."'>&laquo; DIA ANTERIOR</a> |

<a href='".$PHP_SELF."?dia=".$post."&mes=".$mesPost."&ano=".$anoPost."&idasistencia=".$idasistencia."'> DIA SIGUIENTE &raquo;</a>
</p></th></tr>";

        return $Output;
    }



}//End of WeeklyCalendar Class


?>
