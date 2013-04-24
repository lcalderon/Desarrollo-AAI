<?
class DB_mysqli extends mysqli
{
	var $Prefix;
	var $BaseDatos;
	var $Servidor;
	var $Usuario;
	var $Clave;
	var $version;
	var $Errno = 0;
	var $Error = "";
	var $fechaversion;
	var $logoMensaje;

	var $temporal;
	var $catalogo;
	var $historia;
	var $trash;

	var $logo;



	/*
	mï¿½todo constructor

	$host :  ip del serv. mysql o localhost
	$user :	 usuario
	$passw:  contraseï¿½a
	$bd	  :  Base de datos
	*/

	function __construct($context='')
	{

		$matriz_ini = parse_ini_file("config/config.ini", true);
		if ($context==''){
			$environment =$matriz_ini['global']['environment'];

			$this->Servidor= $matriz_ini[$environment]['dbhost'];
			$this->Usuario= $matriz_ini[$environment]['user'];
			$this->Clave= $matriz_ini[$environment]['password'];
			$this->Prefix = $matriz_ini[$environment]['prefix'];
			$this->BaseDatos=  $matriz_ini[$environment]['dbname'];
			$this->version = $matriz_ini[$environment]['version'];
			$this->fechaversion = $matriz_ini[$environment]['fechaversion'];
			$this->logoMensaje = $matriz_ini[$environment]['logoMensaje'];
			$this->temporal = $this->Prefix.$this->BaseDatos.'temporal';
			$this->catalogo = $this->Prefix.$this->BaseDatos.'catalogo';
//			$this->historia = $this->Prefix.$this->BaseDatos.'historia';
//			$this->trash = $this->Prefix.$this->BaseDatos.'trash';

			$this->logo = $matriz_ini[$environment]['logo'];

			parent::connect($this->Servidor, $this->Usuario, $this->Clave, $this->catalogo);
			parent::query("SET time_zone = (SELECT IF(DATO='',DATODEFAULT,DATO) TIME_ZONE FROM $this->catalogo.catalogo_parametro WHERE IDPARAMETRO='ZONA_HORARIA')");
			date_default_timezone_set($this->lee_parametro('ZONA_HORARIA'));
			
			$this->Error= mysqli_connect_error();
			$this->Errno= mysqli_connect_errno();

		}
		else {
			
			$this->Servidor= $matriz_ini[$context]['dbhost'];
			$this->Usuario= $matriz_ini[$context]['user'];
			$this->Clave= $matriz_ini[$context]['password'];
			$this->Prefix = $matriz_ini[$context]['prefix'];
			$this->BaseDatos=  $matriz_ini[$context]['dbname'];
			$this->temporal = $this->Prefix.$this->BaseDatos.'temporal';
			$this->catalogo = $this->Prefix.$this->BaseDatos.'catalogo';
			
			parent::connect($this->Servidor, $this->Usuario, $this->Clave, $this->catalogo);
			parent::query("SET time_zone = (SELECT IF(DATO='',DATODEFAULT,DATO) TIME_ZONE FROM $this->catalogo.catalogo_parametro WHERE IDPARAMETRO='ZONA_HORARIA')");
			date_default_timezone_set($this->lee_parametro('ZONA_HORARIA'));

			$this->Error= mysqli_connect_error();
			$this->Errno= mysqli_connect_errno();


		}

		return;

	}

	/*
	mï¿½todo para insertar registros

	boolean insert_reg('ejemplo',$registros);

	table_nane:  nombre de la tabla
	$field: array asociado con los campos a insertar
	boolean : False o True si se concreto la operaciï¿½n

	*/

	function insert_reg($table_name, $field,$opc)
	{
		//		$this->autocommit(FALSE);
		$sql = '';
		foreach ($field as $index => $value) {

			//if (is_string($value))  $value=utf8_decode(strtoupper($value));
			if($opc==1) $value=$value; else $value=strtoupper($value);
			//if (is_string($value))  $value=utf8_decode(trim($value));
			if (is_string($value))  $value=trim($value);

			$value = addslashes($value);

			if (!$sql) {
				if ($value=='CURRENT_TIMESTAMP()')
				$sql = "$index = $value";
				else
				$sql = "$index = '$value'";

			}
			else {
				if ($value=='CURRENT_TIMESTAMP()')
				$sql .= ", $index = $value";
				else
				$sql .= ", $index = '$value'";


			}
		}
		$sql = "INSERT INTO $table_name SET " . $sql;

		$result = $this->query($sql);
		//echo $sql;
		//die();
		return $result;

	}


	function borrar_reg($table_name,$condition){
		$sql="DELETE FROM $table_name WHERE $condition ;";

		$this->query($sql);
		//echo $sql;

	}

	/*
	mï¿½todo para actualizar registros

	boolean update('ejemplo',$registros,'where telefono=55555');

	table_nane:  nombre de la tabla
	$field: array asociado con los campos a insertar
	$condition: condiciï¿½n que debe cumplirse para actualizar
	boolean : False o True si se concreto la operaciï¿½n

	*/

	function update($table_name, $field, $condition)
	{

		$sql = '';

		foreach ($field as $index => $value) {


			if (is_string($value))  $value=utf8_decode(trim(strtoupper($value)));

			$value = addslashes($value);

			if (!$sql) {
				if ($value=='CURRENT_TIMESTAMP()')
				$sql = "$index = $value";
				else
				$sql = "$index = '$value'";
			}
			else {
				if ($value=='CURRENT_TIMESTAMP()')
				$sql .= ", $index = $value";
				else
				$sql .= ", $index = '$value'";
			}
		}

		$sql = "UPDATE $table_name SET " . $sql . ' ' . $condition;

		$result= $this->query($sql);
		// echo $sql;
		// die();
		return $result;
	}


	function insert_update($table_name, $field)
	{
		//		$this->autocommit(FALSE);
		$sql = '';

		foreach ($field as $index => $value) {

			//if (is_string($value))  $value=utf8_decode(strtoupper($value));
			if (is_string($value))  $value=utf8_decode(trim(strtoupper($value)));

			$value = addslashes($value);

			if (!$sql) {
				if ($value=='CURRENT_TIMESTAMP()')
				$sql = "$index = $value";
				else
				$sql = "$index = '$value'";
			}
			else {
				if ($value=='CURRENT_TIMESTAMP()')
				$sql .= ", $index = $value";
				else
				$sql .= ", $index = '$value'";
			}
		}

		$sql = "INSERT INTO $table_name SET " . $sql . '  ON DUPLICATE KEY UPDATE ' .  $sql;

		$result= $this->query($sql);
		//		$this->commit();
		//die($sql);
		return $result;
	}




	/*
	mï¿½todo uparray

	$sql :  query de donde se extrae los datos a almacenar en el array


	Ejemplo:
	$lista = $obj->uparray('select CVEPAIS, NOMBRE from catalogo_paises');

	donde:
	CVEPAIS  tiene los datos del indice
	NOMBRE   son los valores de cada elemento del array

	*/
	function uparray($sql)
	{
		$result=$this->query($sql);
		while($reg= $result->fetch_array()){
			$datos[$reg[0]]=$reg[1];
		}
		return $datos;
	}






	/*
	mï¿½todo cmbmselect_db

	$sql:       	query de donde se extrae la inf. a presentar en el combo
	$field_name:	nombre del campo en el formulario
	$selected:		'Blank' -> agrega la opcion seleccione, 'Texto'->selecciona el texto que coincida con la opciï¿½n
	$event:			evento a incluir para el select opcional
	$style:			stilo ha definir para el select opcional

	Ejemplo:
	$obj->cmbselect_db('combo1','select telf_cuenta,nombre from cuentas', 'Interbank','');

	*/

	function cmbselect_db($field_name,$sql,$selected,$event='',$style='',$addoption='TODOS')
	{

		echo '<select name='."'$field_name'".' '.$event. ' '.$style. ' >';
		//		if ($selected=='Blank')
		echo "<option value='' selected>$addoption</option>";



		$result= $this->query($sql);
		while ($reg=$result->fetch_array(MYSQLI_NUM)){

			if ($selected==$reg[1] or $selected==$reg[0] ) 	echo '<option value='."'$reg[0]'".' selected >'.$reg[1].'</option>';
			else echo '<option value='."'$reg[0]'".' >'.$reg[1].'</option>';

		}

		echo '</select>';
	}


	/*
	mï¿½todo cbmselect_ar

	$lista:      	array asociado con las opciones del combo
	$field_name:	nombre del campo en el formulario
	$selected:		'Blank' -> agrega la opciï¿½n Seleccione, 'Texto'->selecciona el texto que coincida con la opciï¿½n
	$event:			evento a incluir para el select opcional
	$style:			stilo ha definir para el select opcional

	Ejemplo:
	$obj->cmbselect_ar('tipo_telef',array('F'=>'Fijo','C'=>'Celular','R'=>'Red privada'), 'Fijo','disabled');

	Nota:  este mï¿½todo puede usarse con array ingresados directamente o previamente cargados con el mï¿½todo uparray

	*/

	function cmbselect_ar($field_name,$lista,$selected,$event='',$style='',$addoption='TODOS')
	{

		echo "<select name='$field_name' $event $style >";
		if ($addoption!='') echo "<option value='' selected >$addoption</option>";

		foreach ($lista as $index=>$value)
		{
			if ($selected==$value or $selected==$index) 	echo "<option value='$index' selected >"."$value".'</option>';
			else echo "<option value='$index' >"."$value".'</option>';
		}

		echo '</select>';
	}

	function cmbselect_anio($field_name,$anio_inicial,$anios_atras,$anio_select,$event,$style,$addoption='ANIO')
	{
		echo "<select name='$field_name' $event $style >";
		if ($addoption!='') echo "<option value='' selected >".$addoption."</option>";

		for($i=$anio_inicial; $i>=($anio_inicial-$anios_atras);$i--)
		{
			if ($anio_select==$i) 	echo '<option value='."$i".' selected >'."$i".'</option>';
			else echo '<option value='."$i".' >'."$i".'</option>';
		}

		echo '</select>';
	}

	function cmbselect_dia($field_name,$dia_select,$event,$style,$addoption='DIA')
	{
		echo "<select name='$field_name' $event $style >";
		if ($addoption!='') echo "<option value='' selected >".$addoption."</option>";
		for($i=1; $i<=31;$i++)
		{

			$ii=($i<=9)?"0".$i:$i;
			if ($dia_select==$i)
			echo '<option value='.$ii.' selected >'."$ii".'</option>';
			else echo '<option value='."$ii".' >'."$ii".'</option>';
		}

		echo '</select>';
	}

	function cmbselect_hora($field_name,$hora_select,$event,$style,$addoption='')
	{
		echo "<select name='$field_name' $event $style >";
		if ($addoption!='') echo "<option value='' selected >".$addoption."</option>";
		for($i=0; $i<=23;$i++)
		{
			$ii=($i<=9)?"0".$i:"$i";
			if ($hora_select==$i)
			echo '<option value='.$ii.' selected >'."$ii".'</option>';
			else echo '<option value='."$ii".' >'."$ii".'</option>';
		}

		echo '</select>';
	}

	function cmbselect_minuto($field_name,$minuto_select,$interval,$event,$style,$addoption='')
	{
		echo "<select name='$field_name' $event $style >";
		if ($addoption!='') echo "<option value='' selected >".$addoption."</option>";
		for($i=0; $i<=59;$i+=$interval)
		{
			$ii=($i<=9)?"0".$i:"$i";
			if ($minuto_select==$i)
			echo '<option value='.$ii.' selected >'."$ii".'</option>';
			else echo '<option value='."$ii".' >'."$ii".'</option>';
		}

		echo '</select>';
	}



	public function consultation($sql)
	{
		$result = $this->query($sql);
		$data = array();
		for($i=0; $i< $result->num_rows; $i++)
		{
			$data[$i] = $result->fetch_array();
		}

		return $data;

	}

	public function createcmb($field_name,$lista,$selected,$style='')
	{
		echo "<select name='$field_name'  $style >";

		if ($selected == '') echo "<option value='todos' selected >TODOS</option>";

		foreach($lista as $index=>$value)
		{
			if ($selected==$value)
			{
				echo "<option value='".$index."' selected >$value</option>";
			}
			else
			{
				echo "<option value='".$index."' >$value</option>";
			}
		}

		echo "</select>";
	}

	/* FECHA Y HORA ACTUAL */

	public function fechaHora_actual2()
	{
		$this->fecha = date("Y-m-d");
		$this->hora = date("H:i:s");

		return array($this->fecha,$this->hora);
	}


	public function cmbselectopc($sql,$namecmb,$selected,$stylos="",$opc)
	{
		echo "<select name='$namecmb' id='$namecmb' $stylos >";
		if($opc=="")	echo "<option value='' selected >TODOS</option>";

		$result= $this->query($sql);
		while ($reg=$result->fetch_array(MYSQLI_NUM))
		{
			if ($selected==$reg[0])
			{
				echo "<option value='".$reg[0]."' selected >".utf8_decode($reg[1])."</option>";
			}
			else
			{
				if($opc==2) continue;	else echo "<option value='".$reg[0]."' >".utf8_decode($reg[1])."</option>";
			}
		}

		echo "</select>";

	}

	public function cmbselectnivel($sql,$namecmb,$selected,$stylos="",$nivel)
	{
		echo "<select name='$namecmb' id='$namecmb' $stylos >";
		if($nivel=="ADMI")	echo "<option value='todos' selected >TODOS</option>";
		//echo "<option value='' >Seleccione</option>";

		$result= $this->query($sql);
		while ($reg=$result->fetch_array(MYSQLI_NUM))
		{
			if ($selected==$reg[0])
			{
				echo "<option value='".$reg[0]."' selected >".utf8_decode($reg[1])."</option>";
			}
			else
			{
				if($nivel!="OPE")	echo "<option value='".$reg[0]."' >".utf8_decode($reg[1])."</option>";	else continue;
			}
		}

		echo "</select>";

	}



	function exist($table_name, $field, $xwhere)
	{
		$sql="select $field from $table_name $xwhere";

		$res=$this->query($sql);
		if ($res->num_rows) return true;
		return false;
	}


	function reg_id(){
		$resul=$this->query('select LAST_INSERT_ID() as INSERT_ID');
		while ($reg=$resul->fetch_object()) {
			$insert_id= $reg->INSERT_ID;
		}
		return  $insert_id;

	}


	function lee_parametro($idparametro){
		$sql="
		select 
			if(DATO='',DATODEFAULT,DATO) VALOR 
		from 
			$this->catalogo.catalogo_parametro 
		where 
			IDPARAMETRO='$idparametro'
			";
		$result= $this->query($sql);
		if ($result->num_rows){
			while ($reg=$result->fetch_object()){
				$valor = $reg->VALOR;
			}
		}
		else
		$valor= 'Error';
		return $valor ;
	}

	function url(){
		$protocolo=($_SERVER['HTTPS'])?'HTTPS://':'';
		$ip= $_SERVER['SERVER_NAME'];
		$port= $_SERVER['SERVER_PORT'];
		$url= $protocolo.$ip.':'.$port.'/';

		return $url;
	}




	public function cmbselectall($sql,$namecmb,$selected,$stylos="",$opc)
	{
		echo "<select name='$namecmb' id='$namecmb' $stylos >";
		if(!$opc)	echo "<option value='' selected >TODOS >>> </option>";

		$result= $this->query($sql);
		while ($reg=$result->fetch_array(MYSQLI_NUM))
		{
			if ($selected==$reg[0])
			{
				echo "<option value='".$reg[0]."' selected >".utf8_encode($reg[1])."</option>";
			}
			else
			{
				if($opc) continue;	else echo "<option value='".$reg[0]."' >".utf8_encode($reg[1])."</option>";
			}
		}

		echo "</select>";

	}


	function cmb_array($field_name,$lista,$selected,$event="",$opc="",$nombre="S/D",$sort="",$noshow="")
	{

		if(!$sort)	asort($lista);
		echo "<select name='$field_name' id='$field_name' $event $style >";
		if(!$opc)	echo "<option value='' selected >$nombre</option>";

		foreach ($lista as $index=>$value)
		{
			if($selected==$value || $selected==$index)
			{
				echo '<option value='."$index".' selected >'."$value".'</option>';
			}
			else
			{
				if($index==$noshow || $opc==2) continue;	else	echo '<option value='."$index".' >'."$value".'</option>';
			}
		}

		echo '</select>';
	}

	public function cmbselectdata($sql,$namecmb,$selected,$stylos="",$opc="",$nombre="SELECCIONE",$valor="",$regOpcional="")
	{
		echo "<select name='$namecmb' id='$namecmb' $stylos >";
		if($opc!=1 and $opc!=2)	echo "<option value='$valor' selected >$nombre</option>";

		$result= $this->query($sql);
		while ($reg=$result->fetch_array(MYSQLI_NUM))
		{
			if ($selected==$reg[0])
			{
				echo "<option value='".$reg[0]."' selected >".utf8_encode($reg[1])."</option>";
			}
			else
			{
				if($opc==1) continue;	else echo "<option value='".$reg[0]."' >".utf8_encode($reg[1])."</option>";
			}
		}

		if($regOpcional) echo "<option value='OPCIONAL'>$regOpcional</option>";

		echo "</select>";

	}

	public function cmbselect_cuenta($sql,$namecmb,$selected,$stylos="",$opc="",$nombre="SELECCIONE")
	{
		//$cantidad_cuenta= $this->consultation("select count(*) as total from $this->temporal.seguridad_acceso_cuenta where IDCUENTA='VERALL' and IDUSUARIO='".$_SESSION["user"]."' ");
		$allcuentas= $this->consultation("SELECT TODOCUENTAS FROM $this->catalogo.catalogo_usuario WHERE IDUSUARIO='$usuario'  ");
		//$result= $this->query($sql);

		echo "<select name='$namecmb' id='$namecmb' $stylos >";

		if($allcuentas[0][0]==1)		echo "<option value='' selected >$nombre</option>";

		while($reg=$result->fetch_array(MYSQLI_NUM))
		{
			if($selected==$reg[0])
			{
				echo "<option value='".$reg[0]."' selected >- ".utf8_decode($reg[1])."</option>";
			}
			else
			{
				if($opc==1) continue;	else echo "<option value='".$reg[0]."' >".utf8_decode($reg[1])."</option>";
			}
		}

		echo "</select>";

	}

	public function convert_upper($str){
		return strtr(utf8_encode(strtoupper($str)),"Ã¡Ã©Ã­Ã³ÃºÃ±", "Ã?Ã‰Ã?Ã“ÃšÃ‘");
	}

	public function __destruct(){
		parent::close();
		
	}
	
	

}

?>