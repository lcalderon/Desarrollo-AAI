<?php

	class Asignacion
	 {
       public $sql;
       public $regmostrar;
       public $nombre;
       //protected $datasql;

	    public function __construct($sql,$regmostrar,$nombre)
         {
	
            $this->sql = $sql;
            $this->regmostrar= $regmostrar;
            $this->nombre= $nombre;
	  
         }
		 
        public function CrearTablaCatalogo($campos,$cabecera,$boton,$style="catalogos",$ancho="99%")
         {
			$conex = new DB_mysqli;			
			$temporal=$conex->temporal;
				
					if(isset($_GET['pag']))
					 {
						$RegistrosAEmpezar=($_GET['pag']-1)*$this->regmostrar;
						$PagAct=$_GET['pag'];
				// caso contrario los iniciamos
					 }
					else
					 {
						$RegistrosAEmpezar=0;
						$PagAct=1;
						$_GET['pag']="1";
					 }
				//echo $this->$sql;	
				$result = $conex->query($this->sql);
				
				echo "<table border='0' width='$ancho' cellpadding='0' cellspacing='1' class='$style'>";
				if($boton)
				 {
					echo "<tr>";
					echo "<td colspan='3' align='left'><input type='button'  class='boton' onclick=\"reDirigir('add_catalogo.php')\" value='AGREGAR NUEVO $this->nombre'  title='AGREGAR $this->nombre' /></td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td colspan='2'></td>";
					echo "</tr>";
				 }

	//definimos dos arrays uno para los nombre de los campos de la tabla y
	//para los nombres que mostraremos en vez de los de la tabla-encabezados

	//los separamos mediante coma
				$cabecera=explode(",",$cabecera);
				$campos=explode(",",$campos);

	//numero de elementos en el primer array
				$nroItemsArray=count($campos);

	//iniciamos variable i=0
				$i=0;
				if($nroItemsArray>1)
				 {
						echo "<tr>";
			//mediante un bucle crearemos las columnas
						while($i<=$nroItemsArray-1)
						 {
			//comparamos: si la columna campo es igual al elemento actual del array 
							if($campos[$i]==$_GET['campo'])
							 {
			//comparamos: si esta Descendente cambiamos a Ascendente y viceversa
								if($_GET['orden']=="DESC")
								 {
									$orden="ASC";
									$flecha="../../../../imagenes/iconos/arrow_down.gif";
								 }
								else
								 {
									$orden="DESC";
									$flecha="../../../../imagenes/iconos/arrow_up.gif";
								 }
								 
								echo "<th><a onclick=\"OrdenarPor('".$campos[$i]."','".$orden."','".$_GET['pag']."')\"><img src=\"".$flecha."\"  width='11' height='11'/>".$cabecera[$i]."</a></th> \n";
							 }
							else
							 {
								echo "<th><a onclick=\"OrdenarPor('".$campos[$i]."','DESC','".$_GET['pag']."')\">".$cabecera[$i]."</a></th> \n";
							 }
							 
							$i++;
						 }

						echo "</tr>";
				 }				
				echo "<tr>";

				$ii=0;
		     
			//echo $nroItemsArray;
				while($reg=$result->fetch_array(MYSQLI_NUM))
				 {
			//estableciendo colores intercaldos
					if($ii%2==0) $fondo='#f0f0f0'; else $fondo='#bbe0ff';

					echo "<tr  bgcolor=$fondo>";
					echo "<td><b>$reg[0]</b></td>";
					echo "<td align='left'  >".utf8_decode($reg[1])."</td>";
					if($nroItemsArray==3)	echo "<td>".utf8_decode($reg[2])."</td>";
					if($nroItemsArray==4)	echo "<td>".utf8_decode($reg[2])."</td><td>".utf8_decode($reg[3])."</td>";
					if($nroItemsArray==5)	echo "<td>".utf8_decode($reg[2])."</td><td>".utf8_decode($reg[3])."</td><td>".utf8_decode($reg[4])."</td>";
					if($nroItemsArray==6)	echo "<td>".utf8_decode($reg[2])."</td><td>".utf8_decode($reg[3])."</td><td>".utf8_decode($reg[4])."</td><td>$reg[5]</td>";					
					if($nroItemsArray==7)	echo "<td>".utf8_decode($reg[2])."</td><td>".utf8_decode($reg[3])."</td><td>".utf8_decode($reg[4])."</td><td>$reg[5]</td><td>$reg[6]</td>";					
					if($nroItemsArray==8)	echo "<td>".utf8_decode($reg[2])."</td><td>".utf8_decode($reg[3])."</td><td>".utf8_decode($reg[4])."</td><td>$reg[5]</td><td>$reg[6]</td><td>$reg[7]</td>";
					if($nroItemsArray==9)	echo "<td>".utf8_decode($reg[2])."</td><td>".utf8_decode($reg[3])."</td><td>".utf8_decode($reg[4])."</td><td>$reg[5]</td><td>$reg[6]</td><td>$reg[7]</td><td>$reg[8]</td>";
					//echo "<td colspan='2' style='background-color:#FFFFFF;text-align:left'><input name='asignar' type='button' value='ASIGNAR' title='ASIGNAR' onclick=\"reDirigir('edit_catalogo.php?codigo=$reg[0]')\"  style='font-size:10px;' ></td>";
					echo "</tr>";

					$ii=$ii+1;
				 }
			
				echo "</table>";		
         }

        public function MostrarBusqueda()
         {
			$conex = new DB_mysqli;			
			$conex->select_db($conex->catalogo);
			
 

			if(isset($_POST['buscarpage']) and $_POST['buscarpage']!="" and $_POST['buscarpage'] > 0 )	$_GET['pag']=$_POST['buscarpage'];
	
			if(isset($_GET['pag']))
			 {				
				$PagAct=$_GET['pag'];
			 }
			else
			 {				
				$PagAct=1;
				$_GET['pag']="1";
			 }
 
	 
			$rsregistros=$conex->query($this->sql);
			$NroRegistros=$rsregistros->num_rows;
	 
			 
			$PagAnt=$PagAct-1;
			$PagSig=$PagAct+1;
			$PagUlt=$NroRegistros/$this->regmostrar;

			//verificamos residuo para ver si llevar� decimales
			$Res=$NroRegistros%$this->regmostrar;
			// si hay residuo usamos funcion floor para que me devuelva la parte entera, SIN REDONDEAR, y le sumamos
			// una unidad para obtener la ultima pagina
			
			if($Res>0) $PagUlt=floor($PagUlt)+1;

			echo "<table width='517' border='0' cellpadding='1' cellspacing='1'>";
			echo "<tr>";
			echo "<td colspan='6'><form name='form1' method='post' action=''><img title='' style='cursor:pointer;' src='../../../../imagenes/iconos/first.png' width='16' height='16' onClick='Pagina(1)'/>" ;
			if($PagAct > 1) { echo "<img title='' style='cursor:pointer;' src='../../../../imagenes/iconos/left.png' width='16' height='16' onclick='Pagina($PagAnt)'/>"; }
			echo "<input onKeyPress='return validarnumero(event)' style='text-align:center;' name='buscarpage' type='text' size='1' maxlength='2' value='$_POST[buscarpage]' />";
			if($PagAct < $PagUlt)	{ echo "<img title='' style='cursor:pointer;' src='../../../../imagenes/iconos/right.png' width='16' height='16' onclick='Pagina($PagSig)' />"; }
			if($PagUlt > 0) { echo "<img title='' style='cursor:pointer;' src='../../../../imagenes/iconos/last.png' width='16' height='16'  onclick='Pagina($PagUlt)' />";	}
			echo "<strong><font size='1px'>&nbsp;PAGINA $PagAct / $PagUlt&nbsp;</font></strong></form></td>";
		
			echo "<td align='right' style='font-size:11px'><form name='form2' method='post' action=''>BUSCAR&nbsp;<input name='busqueda' type='text' id='busqueda' size='18' class='fondoimg' value='$_POST[busqueda]' title='BUSCAR'></form></td>";
			echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
			echo "</tr>";
			echo "</table>";		 
		 }
		 
        public function MostrarTitulo($nombre)
         {
			 echo "<h2><font size='3px'>$nombre</font></h2>";		 
		 
		 }
	}

// fin  class     
?>