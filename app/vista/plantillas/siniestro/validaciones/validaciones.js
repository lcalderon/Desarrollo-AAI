function cabinadesiniestro(){
	var sw=false;
	if ($F('descripciondelhecho')=='') alert("INGRESE DESCRIPCION DEL HECHO");
	else 	sw = true;
	return sw;
}
