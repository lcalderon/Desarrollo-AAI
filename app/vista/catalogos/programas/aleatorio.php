<?php
 
 
    //  +-------------------------------------------------------------------+
    //  |  TAD Aleatorios. Generador de numeros aleatorios con distintos    |
    //  |  formatos.                                                        |
    //  +-------------------------------------------------------------------+
    
    
    class Aleatorios{
        
        private $_tipo;
        
        function __construct(){
            $this->_tipo=array("hex","dec","bin");
        }
        
        
        
        //@  PARAMETRO 1: (OBLIGATORIO)Recibe el tipo:
        //@  hex ->  c�digo hexadecimal
        //@  bin ->  c�digo binario
        //@  dec ->  c�digo decimal 
        
        //@ PARAMETRO 2: (OBLIGATORIO)Recibe la longitud del codigo deseado
        
        //@ PARAMETRO 3: (OPCIONAL) TRUE/FALSE
        //@  TRUE -> LETRAS EN MAY�SCULAS
        //@  FALSE -> LETRAS EN MIN�SCULAS
        
        //@ Ejemplo: $rn->getAleatorio("hex",16,TRUE);
        
        
        //@ genera un numero aleatorio y lo devuelve.
        public function getAleatorio($tipo,$lon,$mayus = 0){
            
        
            if(strcmp( $tipo,$this->_tipo[0] ) == 0 )
            {
                $caracteres=array(48,49,50,51,52,53,54,55,56,57,65,66,67,68,69,70);
                $codigo="";
                 for($i=0;$i<$lon;$i++)
                {
                     $letra=chr($caracteres[rand(0,15)]);
                    if($mayus==1)
                        $codigo .= strtoupper($letra);
                    else if($mayus==0)
                        $codigo .= strtolower($letra);
                    //i echo $i;
                }
                return $codigo;
            }
            if(strcmp( $tipo,$this->_tipo[1] ) == 0 )
            {
                
                $codigo="";
                 for($i=0;$i<$lon;$i++)
                {
                     $letra=rand(0,9);
                    if($mayus==1)
                        $codigo .= strtoupper($letra);
                    else if($mayus==0)
                        $codigo .= strtolower($letra);
                    //i echo $i;
                }
                return $codigo;
            }
            if(strcmp( $tipo,$this->_tipo[2] ) == 0 )
            {
                
                $codigo="";
                 for($i=0;$i<$lon;$i++)
                {
                     $letra=rand(0,1);
                    if($mayus==1)
                        $codigo .= strtoupper($letra);
                    else if($mayus==0)
                        $codigo .= strtolower($letra);
                    //i echo $i;
                }
                return $codigo;
            }
        }
    }
?>

 
