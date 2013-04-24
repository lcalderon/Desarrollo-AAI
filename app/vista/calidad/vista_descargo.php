<?
	session_start();  
	
	include_once("../../modelo/clase_lang.inc.php");	
	include_once("../../modelo/clase_mysqli.inc.php");
 
    $con= new DB_mysqli();	 
	if($con->Errno){
		printf("Fallo de conexion: %s\n", $con->Error);
		exit();
	 }
	
	$idcodigo=($_POST["idcodigo"])?$_POST["idcodigo"]:$idcodigo;
	
	$Sql_consulta="SELECT * FROM $con->temporal.asistencia_deficiencia_descargo	WHERE IDDEFICIENCIA_CORRELATIVO='$idcodigo' ORDER BY FECHAMOD DESC";	
	$result=$con->query($Sql_consulta);
	$numreg=$result->num_rows;	
	
	if($numreg >0){
?>
 
	<table width="100%" border="0" cellpadding="1" cellspacing="1" style="border:1px solid #af7b03" bgcolor="#fddc8e">       
		<tr>
			<td width="50" bgcolor="#fccf67"><div align="center"><strong><?=_("ID");?></strong></div></td>
			<td width="129" bgcolor="#fccf67"><div align="center"><strong><?=_("FECHAHORA") ;?></strong></div></td>
			<td width="115" bgcolor="#fccf67"><div align="center"><strong><?=_("USUARIO") ;?></strong></div></td>			
			<td width="20%" bgcolor="#fccf67" ><div align="center"><strong><?=_("COMENTARIO") ;?></strong></div></td>
		</tr>
		<?
			while($regs=$result->fetch_object()){
				if($i%2==0) $fondo='#c5ea80'; else $fondo='#fff4ff';
				$n++;
		?>
		<tr>
			<td bgcolor="<?=$fondo;?>" style="text-align:center" width="2%"><?=$n;?></td>
			<td bgcolor="<?=$fondo;?>" style="text-align:center" width="5%"><?=$regs->FECHAMOD; ?></td>
			<td bgcolor="<?=$fondo;?>" style="text-align:center" width="5%"><?=$regs->IDUSUARIOMOD; ?></td>
			<td bgcolor="<?=$fondo;?>"><?=$regs->DESCARGO; ?></td>
		</tr>                  
        <?
				$i=$i+1;		
			}
		?>
	</table>
<?
	}
?>