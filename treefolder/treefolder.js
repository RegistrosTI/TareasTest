function init_treefolder(ruta)
{
	$.ajax({
		type: 'POST',   
		url: './treefolder/treefolder.php?usuario='+encodeURIComponent(usuariovalidado),	
		cache : false,		
		data:
		{
		     treefolder:true,
			 ruta:ruta
		},
		success: function(data)
		{
			treefolder_message(data);
		}
	});	
}
function open_treefolder(fichero)
{
	miPopup = window.open("treefolder/treefolder.php?archivo="+encodeURIComponent(fichero));
	miPopup.focus(); 
}
function treefolder_message(texto)
{	
	$( "#treefolder" ).remove();
	$('body').append('<div id="treefolder" name="treefolder" class="treefolder"><div id="treefolder_cerrar" name="treefolder_cerrar" class="treefolder_cerrar" onClick="cerrar_treefolder();"></div><div id="treefolder_resultado" name="treefolder_resultado" class="treefolder_resultado"><div id="treef">'+texto+'</div></div></div>');
	$("#treefolder").toggle("fast");
	$("#treefolder").css('z-index',900999);
	$("#treefolder").css('left',100+'px');
	$("#treefolder").css('top',100+'px');
	$("#treefolder").draggable({refreshPositions: true  });
	
	$("#treef").fancytree();
}
function cerrar_treefolder()
{
	$( "#treefolder" ).toggle("fast");	
	$( "#treefolder" ).remove();	
}
function treefolder_folders(tarea)
{
	$.ajax({
		type: 'POST',   
		url: './treefolder/treefolder.php?usuario='+encodeURIComponent(usuariovalidado)+'&tarea='+encodeURIComponent(tarea),	
		cache : false,		
		data:
		{
		     treefolder_folders:true
		},
		success: function(data)
		{
			treefolder_message_folder(data,tarea);			
		}
	});	
}
function erase_treefolder(Numero)
{	
	$.ajax({
		type: 'POST',   
		url: './treefolder/treefolder.php?usuario='+encodeURIComponent(usuariovalidado),	
		cache : false,		
		data:
		{
		     treefolder_erase_folders:true,
			 Numero:Numero
		},
		success: function(data)
		{
			$( "#treefolder_"+Numero ).remove();
		}
	});	
}
function treefolder_message_folder(texto,tarea)
{
	
	$( "#treefolder_folder" ).remove();
	$('body').append('<div id="treefolder_folder" name="treefolder_folder" class="treefolder"><div id="treefolder_cerrar_folder" name="treefolder_cerrar_folder" class="treefolder_cerrar" onClick="cerrar_treefolder_folder();"></div><div id="treefolder_mas" name="treefolder_mas" class="treefolder_mas" onClick="mas_treefolder('+tarea+');"></div><div id="treefolder_folder_resultado" name="treefolder_folder_resultado" class="treefolder_resultado"><div id="treef_folder">'+texto+'</div></div></div>');
	$("#treefolder_folder").toggle("fast");
	$("#treefolder_folder").css('z-index',900998);
	$("#treefolder_folder").css('left',100+'px');
	$("#treefolder_folder").css('top',100+'px');
	$("#treefolder_folder").draggable({refreshPositions: true  });
	
}
function cerrar_treefolder_folder()
{
	$( "#treefolder_folder" ).toggle("fast");	
	$( "#treefolder_folder" ).remove();	
}
function mas_treefolder(tarea)
{
	texto = GetPantallaMasTreeFolder(tarea);
	$( "#treefolder_more" ).remove();
	$('body').append('<div id="treefolder_more" name="treefolder_more" class="treefolder"><div id="treefolder_cerrar_mas" name="treefolder_cerrar_mas" class="treefolder_cerrar" onClick="cerrar_treefolder_mas();"></div><div id="treefolder_resultado_mas" name="treefolder_resultado_mas" class="treefolder_resultado_mas"><div id="treef">'+texto+'</div></div></div>');
	$("#treefolder_more").toggle("fast");
	$("#treefolder_more").css('z-index',901999);
	$("#treefolder_more").css('left',100+'px');
	$("#treefolder_more").css('top',100+'px');
	$("#treefolder_more").draggable({refreshPositions: true  });
}
function cerrar_treefolder_mas()
{
	$( "#treefolder_more" ).toggle("fast");	
	$( "#treefolder_more" ).remove();	
}
function GetPantallaMasTreeFolder(tarea)
{
	var lista = '';
	lista = lista + '<div>';
	lista = lista + '<div>Ruta</div>';
	lista = lista + '<input class="input_treefolder" id="treefolder_ruta" onChange="cambio_treefolder_ruta(this);"></input>';	
	lista = lista + '</div>';
	lista = lista + '<div>';
	lista = lista + '<div>Nombre</div>';
	lista = lista + '<input class="input_treefolder" id="treefolder_ruta_nombre"></input>';	
	lista = lista + '</div>';
	lista = lista + '<div class="boton_treefolder" onClick="acepta_treefolder('+tarea+');">Aceptar</div>';
	return lista;
}
function cambio_treefolder_ruta(obj)
{
	$( "#treefolder_ruta_nombre" ).val(obj.value);	
}
function acepta_treefolder(tarea)
{
	var ruta   = $( "#treefolder_ruta" ).val();	
	var nombre = $( "#treefolder_ruta_nombre" ).val();	
	$.ajax({
		type: 'POST',   
		url: './treefolder/treefolder.php?usuario='+encodeURIComponent(usuariovalidado),	
		cache : false,		
		data:
		{
		     treefolder_insert:true,
			 ruta:ruta,
			 nombre:nombre,
			 tarea:tarea
		},
		success: function(data)
		{				
			$( "#treef_folder" ).append( data );
			cerrar_treefolder_mas();
		}
	});			
}