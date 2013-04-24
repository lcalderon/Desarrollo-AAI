function serviciotecnico(){
	var sw=false;
	if ($F('descripcionproblema')=='') alert("INGRESE DESCRIPCION DEL PROBLEMA");
	else 	sw = true;
	return sw;
}
