<?php 

 // Distribution.php 

 // Copyright John Pezullo 
 // Released under same terms as PHP. 
 // PHP Port and OO'fying by Paul Meagher 

   function doCommonMath($q, $i, $j, $b) { 
       
   $zz = 1;  
   $z  = $zz;  
   $k  = $i;  
       
       
   while($k <= $j) {  
        $zz = $zz * $q * $k / ($k - $b);  
        $z  = $z + $zz;  
        $k  = $k + 2;  
   } 
   return $z; 
  } 
       
  function getStudentT($t, $df) {   

   $t  = abs($t);  
   $w  = $t  / sqrt($df);  
   $th = atan($w); 
       
   if ($df == 1) {  
    return 1 - $th / (pi() / 2);  
   } 
     
   $sth = sin($th);  
   $cth = cos($th); 
     
   if( ($df % 2) ==1 ) {  
    return 
      1 - ($th + $sth * $cth * doCommonMath($cth * $cth, 2, $df - 3, -1)) 
                         / (pi()/2); 
   } else { 
    return 1 - $sth * doCommonMath($cth * $cth, 1, $df - 3, -1);  
   } 
     
  } 
     
  function getInverseStudentT($p, $df) {  
       
   $v =  0.5;  
   $dv = 0.5;  
   $t  = 0; 
       
   while($dv > 1e-6) {  
    $t = (1 / $v) - 1;  
    $dv = $dv / 2;  
    if ( getStudentT($t, $df) > $p) {  
     $v = $v - $dv; 
    } else {  
     $v = $v + $dv; 
    }  
   } 
   return $t; 
  } 
     
 
 
 ?> 
