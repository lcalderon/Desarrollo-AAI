$(document).ready(function() {
	$("ul.subnav").parent().append("<span></span>");

	$("ul.topnav li a").click(function() { //When trigger is clickedâ€¦
	
	//Following events are applied to the subnav itself (moving subnav up and down)
	$(this).parent().find("ul.subnav").stop().slideDown('fast').show(); //Drop down the subnav on click
	
	$(this).parent().hover(function() {
	}, function(){
	$(this).parent().find("ul.subnav").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
	});
	
	//Following events are applied to the trigger (Hover events for the trigger)
	}).hover(function() {
	$(this).addClass("subhover"); //On hover over, add class "subhover"
	}, function(){ //On Hover Out
	$(this).removeClass("subhover"); //On hover out, remove class "subhover"
	});


});

function reDirigir_ventana(ruta,modovista,ancho,alto,titulo) {

	/*  tamaño por default */
	if (ancho <= 0)  ancho=650;
	if (alto <= 0) alto=600;

	switch (modovista){
	case 'iframe':
		$('#desktop').attr('style',"background: url('')");
		$('#desktop').attr('src',ruta);
		break;
	
	case 'window':
		$.newWindow({id: titulo,
		title: titulo,
		width: ancho,
		// posx: 200,
		// poxy: 100,
		height: alto,
		onDragBegin : null,
		onDragEnd : null,
		onResizeBegin : null,
		onResizeEnd : null,
		draggable : true,
		type : "iframe",
		resizeable: false
		
		});
		$.updateWindowContent(titulo,
			"<iframe src="+ruta+" width='100%' height='100%'/>");
		
		break;
		
	
	
}


}
