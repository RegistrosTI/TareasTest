function spb_gantt_inicia()
{
	(function ($) 
	{
		$("body").css("cursor", "progress");		
	
		var opts               = { lines: 13, length: 20, width: 10, radius: 30, corners: 1, rotate: 0, direction: 1, color: '#000',  speed: 1, trail: 60, shadow: true, hwaccel: false, className: 'spinner', zIndex: 2e9, top: 'auto', left: 'auto' };
		var target             = document.getElementById('spb_gantt_contenedor');
		var spinner_calendario = new Spinner(opts).spin(target);	
		
		$.ajax({
		type: 'POST',
		url: './gantt/' + 'spb_gantt.php',    
		success: function(data)
		{			
			$("body").css("cursor", "default");
			$('#spb_gantt_contenedor').html(data);			
			spb_gantt_controladores();
			spb_gantt_cambia_pantalla();			
		}		
		});
		
	})(jQuery);
}
function spb_gantt_cambia_factor_slide()
{
	(function ($) 
	{
		var Usuario       = new Array();
		$("#Usuarios_Nombres .Usuario").each(function() 
		{	
			id_usuario = this.id;
			Usuario.push(id_usuario.replace("_", "."));			
							
		});	
		var jsonString = JSON.stringify(Usuario);		
		spb_gantt_cambia_factor(jsonString);
	})(jQuery);	
}
function spb_gantt_cambia_factor(lista_usuarios)
{
	(function ($) 
	{
		$("body").css("cursor", "progress");

		var scroll_actual = $( "#spb_gantt_calendario" ).scrollLeft();
		var factor_actual = $( "#spb_gantt_slider" ).attr( "factor_anterior" );
		var factor_nuevo  = $( "#spb_gantt_slider" ).val();
		var fecha_inicio  = $( "#spb_gantt" ).attr( "inicio" );
		var fecha_fin     = $( "#spb_gantt" ).attr( "fin" );
		var factor_nuevo  = $( "#spb_gantt_slider" ).val();
		var resultado     = scroll_actual/factor_actual;
		
	
		var opts               = { lines: 13, length: 20, width: 10, radius: 30, corners: 1, rotate: 0, direction: 1, color: '#000',  speed: 1, trail: 60, shadow: true, hwaccel: false, className: 'spinner', zIndex: 2e9, top: 'auto', left: 'auto' };
		var target             = document.getElementById('spb_gantt_contenedor');
		var spinner_calendario = new Spinner(opts).spin(target);	
		
		$.ajax({
		type: 'POST',
		url: './gantt/' + 'spb_gantt.php',  
		success: function(data)
		{			
			$("body").css("cursor", "default");
			$('#spb_gantt_contenedor').html(data);
			if(scroll_actual!=0 && factor_nuevo!=0)
			{
				$( "#spb_gantt_calendario" ).scrollLeft(resultado*factor_nuevo);
			}
			spb_gantt_controladores();
			spb_gantt_cambia_pantalla();			
		},
		data:
		{
			 ajax:true,
		     factor:factor_nuevo,
		     inicio:fecha_inicio,
		     fin:fecha_fin,
			 usuarios:lista_usuarios
		}		
		});
		
	})(jQuery);
}
function spb_gantt_controladores()
{
	(function ($) 
	{
		$(document).ready(function() 
		{
			var position     = $('#spb_gantt_calendario').position();
			var top_anyo     = $('.Anyo').css('height').replace(/[^-\d\.]/g, '');
			var top_mes      = $('.Mes').css('height').replace(/[^-\d\.]/g, '');	
			var top_semana   = $('.Semanas').css('height').replace(/[^-\d\.]/g, '');	
			var top_dia      = $('.Dias').css('height').replace(/[^-\d\.]/g, '');
			var altura_gantt = $('#spb_gantt').css('height').replace(/[^-\d\.]/g, '');	
			
			var width_anyo = 0;
			$(".Anyo").each(function (index) 
			{
				width_anyo = width_anyo + $('#'+this.id).width();				
			});

			
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_mas_anyo_atras" class="Controlador_mas" onClick="spb_gantt_agrega_fecha(1)"></div>' );
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_mas_anyo_adelante" class="Controlador_mas" onClick="spb_gantt_agrega_fecha(2)"></div>' );
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_mas_mes_atras" class="Controlador_mas" onClick="spb_gantt_agrega_fecha(3)"></div>' );
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_mas_mes_adelante" class="Controlador_mas" onClick="spb_gantt_agrega_fecha(4)"></div>' );
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_mas_dia_atras" class="Controlador_mas_beige" onClick="spb_gantt_agrega_fecha(5)"></div>' );
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_mas_dia_adelante" class="Controlador_mas_beige" onClick="spb_gantt_agrega_fecha(6)"></div>' );
			
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_next_anyo" class="Controlador_desplazar_next" onClick="spb_gantt_desplaza_fecha(1)"></div>' );
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_prev_anyo" class="Controlador_desplazar_prev" onClick="spb_gantt_desplaza_fecha(2)"></div>' );
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_next_mes" class="Controlador_desplazar_next" onClick="spb_gantt_desplaza_fecha(3)"></div>' );
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_prev_mes" class="Controlador_desplazar_prev" onClick="spb_gantt_desplaza_fecha(4)"></div>' );
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_next_dia" class="Controlador_desplazar_next" onClick="spb_gantt_desplaza_fecha(5)"></div>' );
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_prev_dia" class="Controlador_desplazar_prev" onClick="spb_gantt_desplaza_fecha(6)"></div>' );
			
			$( "#spb_gantt_contenedor" ).append( '<div id="spb_gantt_mas_usuarios" class="Controlador_mas_beige" onClick="spb_gantt_mas_usuarios()"></div>' );
			
			if(parseFloat($('#spb_gantt_calendario').width())<width_anyo)
			{
				width_anyo = $('#spb_gantt_calendario').width();
			}	
			
			
			var position_Usuarios     = $('#Usuarios').position();
			var tamano_Usuarios       = $('#Usuarios').width();
			var Usuarios_Hueco        = $('#Usuarios_Hueco').height();
			
			$( "#spb_gantt_mas_usuarios" ).css( "top", (Usuarios_Hueco+position_Usuarios.top-20)+"px" );
			$( "#spb_gantt_mas_usuarios" ).css( "left", (parseFloat(position_Usuarios.left))+"px" );
			$( "#spb_gantt_mas_usuarios" ).css( "width", (parseFloat(tamano_Usuarios))+"px" );			
			$( "#spb_gantt_mas_usuarios" ).css( "height", 20+"px" );					       
			$( "#spb_gantt_mas_usuarios" ).css( "border-radius", "5px 0px 0px 5px" );	
			
			$( "#spb_gantt_mas_anyo_atras" ).css( "top", position.top+"px" );
			$( "#spb_gantt_mas_anyo_atras" ).css( "left", (parseFloat(position.left)-20)+"px" );
			$( "#spb_gantt_mas_anyo_atras" ).css( "height", top_anyo+"px" );					       
			$( "#spb_gantt_mas_anyo_atras" ).css( "border-radius", "5px 0px 0px 5px" );		
			$( "#spb_gantt_mas_anyo_adelante" ).css( "top", position.top+"px" );
			$( "#spb_gantt_mas_anyo_adelante" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo))+"px" );
			$( "#spb_gantt_mas_anyo_adelante" ).css( "height", top_anyo+"px" );
			$( "#spb_gantt_mas_anyo_adelante" ).css( "border-radius", "0px 5px 5px 0px" );
			
			$( "#spb_gantt_prev_anyo" ).css( "top", position.top+"px" );
			$( "#spb_gantt_prev_anyo" ).css( "left", (parseFloat(position.left))+"px" );
			$( "#spb_gantt_prev_anyo" ).css( "height", top_anyo+"px" );					       			
			$( "#spb_gantt_next_anyo" ).css( "top", position.top+"px" );
			$( "#spb_gantt_next_anyo" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo)-20)+"px" );
			$( "#spb_gantt_next_anyo" ).css( "height", top_anyo+"px" );			
			
			$( "#spb_gantt_mas_mes_atras" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo))+"px" );
			$( "#spb_gantt_mas_mes_atras" ).css( "left", (parseFloat(position.left)-20)+"px" );
			$( "#spb_gantt_mas_mes_atras" ).css( "height", top_mes+"px" );
			$( "#spb_gantt_mas_mes_atras" ).css( "border-radius", "5px 0px 0px 5px" );						
			$( "#spb_gantt_mas_mes_adelante" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo))+"px" );
			$( "#spb_gantt_mas_mes_adelante" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo))+"px" );
			$( "#spb_gantt_mas_mes_adelante" ).css( "height", top_mes+"px" );
			$( "#spb_gantt_mas_mes_adelante" ).css( "border-radius", "0px 5px 5px 0px" );	

			$( "#spb_gantt_prev_mes" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo))+"px" );
			$( "#spb_gantt_prev_mes" ).css( "left", (parseFloat(position.left))+"px" );
			$( "#spb_gantt_prev_mes" ).css( "height", top_mes+"px" );			
			$( "#spb_gantt_next_mes" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo))+"px" );
			$( "#spb_gantt_next_mes" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo)-20)+"px" );
			$( "#spb_gantt_next_mes" ).css( "height", top_mes+"px" );	
			
			$( "#spb_gantt_mas_dia_atras" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo)+parseFloat(top_mes))+"px" );
			$( "#spb_gantt_mas_dia_atras" ).css( "left", (parseFloat(position.left)-20)+"px" );
			$( "#spb_gantt_mas_dia_atras" ).css( "height", (parseFloat(top_semana)+parseFloat(top_dia))+"px" );
			$( "#spb_gantt_mas_dia_atras" ).css( "border-radius", "5px 0px 0px 5px" );					
			$( "#spb_gantt_mas_dia_adelante" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo)+parseFloat(top_mes))+"px" );
			$( "#spb_gantt_mas_dia_adelante" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo))+"px" );
			$( "#spb_gantt_mas_dia_adelante" ).css( "height", (parseFloat(top_semana)+parseFloat(top_dia))+"px" );
			$( "#spb_gantt_mas_dia_adelante" ).css( "border-radius", "0px 5px 5px 0px" );		
			
			$( "#spb_gantt_prev_dia" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo)+parseFloat(top_mes))+"px" );
			$( "#spb_gantt_prev_dia" ).css( "left", (parseFloat(position.left))+"px" );
			$( "#spb_gantt_prev_dia" ).css( "height", (parseFloat(altura_gantt)-(parseFloat(top_semana)+parseFloat(top_dia)))+"px" );
			$( "#spb_gantt_next_dia" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo)+parseFloat(top_mes))+"px" );
			$( "#spb_gantt_next_dia" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo)-20)+"px" );
			$( "#spb_gantt_next_dia" ).css( "height", (parseFloat(altura_gantt)-(parseFloat(top_semana)+parseFloat(top_dia)))+"px" );
			

			$("div.Ausencia.particular").click(function(){ $("div.Ausencia.particular").css("display","none");spb_ocultar_ausencia_particular(); });
			$("div.Ausencia.global").click(function(){ $("div.Ausencia.global").css("display","none");spb_ocultar_ausencia_global(); });			
		});
	})(jQuery);
}
function spb_mostrar_ausencia_global()
{
	 $("div.Ausencia.global").css("display","block");
	 $("#Usuarios_Hueco div.Ausencia.global").remove();
}
function spb_ocultar_ausencia_global()
{
		$("#Usuarios_Hueco").append('<div onClick="spb_mostrar_ausencia_global()" class="Ausencia global" style="width: 100%;text-align: center;color: #FFF;font-size: 12px;top: 0px;font-weight: bold;text-shadow: 2px 2px 0px #000;">Vacaciones Globales</div>');
}
function spb_mostrar_ausencia_particular()
{
	 $("div.Ausencia.particular").css("display","block");
	 $("#Usuarios_Hueco div.Ausencia.particular").remove();
}
function spb_ocultar_ausencia_particular()
{
		$("#Usuarios_Hueco").append('<div onClick="spb_mostrar_ausencia_particular()" class="Ausencia particular" style="width: 100%;text-align: center;color: #FFF;font-size: 12px;top: 18px;font-weight: bold;text-shadow: 2px 2px 0px #000;">Vacaciones Particulares</div>');
}
function spb_gantt_agrega_fecha(paso)
{
	(function ($) 
	{
	
		var fecha_inicio  = $( "#spb_gantt" ).attr( "inicio" );
		var fecha_fin     = $( "#spb_gantt" ).attr( "fin" );
		switch(paso) 
		{
		case 6:
			var fecha      = fecha_fin.split("/");
			var dt         = new Date(fecha[1]+"/"+fecha[0]+"/"+fecha[2]);		
			var dayOfMonth = dt.getDate();
			dt.setDate(dayOfMonth + 7);
			$( "#spb_gantt" ).attr( "fin",spb_gantt_convertDate(dt));			
			break;
		case 5:
			var fecha      = fecha_inicio.split("/");
			var dt         = new Date(fecha[1]+"/"+fecha[0]+"/"+fecha[2]);		
			var dayOfMonth = dt.getDate();
			dt.setDate(dayOfMonth - 7);
			$( "#spb_gantt" ).attr( "inicio",spb_gantt_convertDate(dt));			
			break;	
		case 4:
			var fecha      = fecha_fin.split("/");
			var dt         = new Date(fecha[2],(parseInt(fecha[1])+1),0);		
			var day        = (7-dt.getDay());
			if(day==7)
			{
				day=0;
			}
			var dayOfMonth = dt.getDate();
			dt.setDate(dayOfMonth + day);
			$( "#spb_gantt" ).attr( "fin",spb_gantt_convertDate(dt));				
			break;
		case 3:
			var fecha      = fecha_inicio.split("/");
			var dt         = new Date(fecha[2],(parseInt(fecha[1])-2),"1");					
			var day        = dt.getDay()-1;
			var dayOfMonth = dt.getDate();
			dt.setDate(dayOfMonth - day);
			$( "#spb_gantt" ).attr( "inicio",spb_gantt_convertDate(dt));			
			break;
		case 2:
			var fecha      = fecha_fin.split("/");
			var dt         = new Date(parseInt(fecha[2])+1,"12","0");				
			var day        = (7-dt.getDay());
			if(day==7)
			{
				day=0;
			}
			var dayOfMonth = dt.getDate();
			dt.setDate(dayOfMonth + day);
			$( "#spb_gantt" ).attr( "fin",spb_gantt_convertDate(dt));				
			break;
		case 1:
			var fecha      = fecha_inicio.split("/");
			var dt         = new Date(parseInt(fecha[2])-1,"0","1");					
			var day        = dt.getDay()-1;
			var dayOfMonth = dt.getDate();
			dt.setDate(dayOfMonth - day);
			$( "#spb_gantt" ).attr( "inicio",spb_gantt_convertDate(dt));			
			break;			
		}
		spb_gantt_cambia_factor_slide();

	})(jQuery);
}
function spb_gantt_convertDate(inputFormat) 
{
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/');
}
function spb_gantt_cambia_pantalla()
{
	(function ($) 
	{
		$(window).resize(function()
		{
		
            $( "#spb_gantt_mas_anyo_atras" ).remove();
			$( "#spb_gantt_mas_anyo_adelante" ).remove();
			$( "#spb_gantt_mas_mes_atras" ).remove();
			$( "#spb_gantt_mas_mes_adelante" ).remove();
			$( "#spb_gantt_mas_dia_atras" ).remove();
			$( "#spb_gantt_mas_dia_adelante" ).remove();  

			$( "#spb_gantt_prev_anyo" ).remove();
			$( "#spb_gantt_next_anyo" ).remove();
			$( "#spb_gantt_prev_mes" ).remove();
			$( "#spb_gantt_next_mes" ).remove();
			$( "#spb_gantt_prev_dia" ).remove();
			$( "#spb_gantt_next_dia" ).remove();  
			
			$( "#spb_gantt_mas_usuarios" ).remove();  		
			
			spb_gantt_controladores();
        });
	})(jQuery);
}
function spb_gantt_desplaza_fecha_paso(porcion,velocidad,direccion)
{
	(function ($) 
	{
		var scroll_actual   = parseInt($( "#spb_gantt_calendario" ).scrollLeft());
		var posicion        = 0;
		var tamano          = 0;
		var posicion_ultima = 0;
		$("." + porcion).each(function() 
		{	
			posicion    = $(this).css( "left" ).replace(/[^-\d\.]/g, '');
			tamano      = $(this).css( "width" ).replace(/[^-\d\.]/g, '');
			if(direccion<0)
			{
				if(parseFloat(posicion)<parseFloat(scroll_actual))
				{				
					posicion_ultima = parseFloat(posicion);
				}
			}
			if(direccion>0)
			{
				if(parseFloat(posicion)>parseFloat(scroll_actual) && posicion_ultima == 0)
				{			
					posicion_ultima = parseFloat(posicion);											
				}
			}
		});	
		$( "#spb_gantt_calendario").animate(
		{
			scrollLeft: posicion_ultima
		}, 
		velocidad);
	})(jQuery);	
}
function spb_gantt_desplaza_fecha(paso)
{
	(function ($) 
	{
		switch(paso) 
			{
			case 6:
				spb_gantt_desplaza_fecha_paso('Semana',800,-1);
				break;
			case 5:
				spb_gantt_desplaza_fecha_paso('Semana',800,1);
				break;
			case 4:
				spb_gantt_desplaza_fecha_paso('Mes',600,-1);
				break;
			case 3:
				spb_gantt_desplaza_fecha_paso('Mes',600,1);
				break;
			case 2:
				spb_gantt_desplaza_fecha_paso('Anyo',300,-1);
				break;
			case 1:
				spb_gantt_desplaza_fecha_paso('Anyo',300,1);
				break;			
			}
	})(jQuery);
}
function spb_gantt_mostrar_servicio(id_servicio,id_delta,id_usuarios)
{
	(function ($) 
	{
		var scroll_actual   = parseInt($( "#spb_gantt_calendario" ).scrollLeft());
		var tamano_mensaje  = parseInt($("#spb_gantt").width())-parseInt($("#Usuarios_Nombres").width());				
		$( "#spb_gantt_mensaje" ).remove();		
		$("#spb_gantt_calendario").css("overflow", "hidden");
		$('#spb_gantt_calendario').append('<div id="spb_gantt_mensaje" name="spb_gantt_mensaje" class="spb_gantt_mensaje" style="width:'+tamano_mensaje+'px;"><div id="spb_gantt_mensaje_cerrar" name="spb_gantt_mensaje_cerrar" class="spb_gantt_mensaje_cerrar" onClick="spb_gantt_cerrar_mensaje();"></div><div id="spb_gantt_mensaje_resultado" name="spb_gantt_mensaje_resultado" class="spb_gantt_mensaje_resultado"></div></div>');
		$("#spb_gantt_mensaje").toggle("fast");
		$("#spb_gantt_mensaje").css('z-index',10);
		$("#spb_gantt_mensaje").css('z-index',10);
		$("#spb_gantt_mensaje").css('left',scroll_actual+'px');
		$("#spb_gantt_mensaje").css('top',0+'px');
		
		
		$( "#spb_gantt_mas_anyo_atras" ).remove();
		$( "#spb_gantt_mas_anyo_adelante" ).remove();
		$( "#spb_gantt_mas_mes_atras" ).remove();
		$( "#spb_gantt_mas_mes_adelante" ).remove();
		$( "#spb_gantt_mas_dia_atras" ).remove();
		$( "#spb_gantt_mas_dia_adelante" ).remove();  

		$( "#spb_gantt_prev_anyo" ).remove();
		$( "#spb_gantt_next_anyo" ).remove();
		$( "#spb_gantt_prev_mes" ).remove();
		$( "#spb_gantt_next_mes" ).remove();
		$( "#spb_gantt_prev_dia" ).remove();
		$( "#spb_gantt_next_dia" ).remove();  
		
		spb_gantt_mostrar_servicio_dato(id_servicio,id_delta,id_usuarios);

	})(jQuery);
}	
function spb_gantt_mostrar_servicio_dato(id_servicio,id_delta,id_usuarios)
{
	(function ($) 
	{
		var opts          = {lines: 13,length: 20,width: 10,radius: 30,corners: 1,rotate: 0, direction: 1, color: '#000', speed: 1, trail: 60, shadow: true, hwaccel: false,className: 'spinner', zIndex: 2e9, top: 'auto', left: 'auto' };
		var target        = document.getElementById('spb_gantt_contenedor');
		var spinner_ficha = new Spinner(opts).spin(target);
		
		$.ajax({
		type: 'POST',
		url: './gantt/' + 'spb_gantt.php',    
		success: function(data)
		{
			$("body").css("cursor", "default");
			$('#spb_gantt_mensaje_resultado').html(data);
			spinner_ficha.stop();
		},
		data:
		{
		     id_servicio:id_servicio,
		     id_delta:id_delta,
		     id_usuarios:id_usuarios
		}		
		});	

	})(jQuery);
}			
function spb_gantt_cerrar_mensaje()
{
	(function ($) 
	{
		$( "#spb_gantt_mensaje" ).toggle("fast");	
		$( "#spb_gantt_mensaje" ).remove();	
		$("#spb_gantt_calendario").css("overflow", "auto");
		spb_gantt_controladores();
	})(jQuery);
}
function spb_gantt_mas_usuarios()
{
	(function ($) 
	{
		var scroll_actual   = parseInt($( "#spb_gantt_calendario" ).scrollLeft());
		var tamano_mensaje  = parseInt($("#Usuarios_Nombres").width());				
		$( "#spb_gantt_mensaje_usuarios" ).remove();				
		$('#Usuarios').append('<div id="spb_gantt_mensaje_usuarios" name="spb_gantt_mensaje_usuarios" class="spb_gantt_mensaje" style="width:'+tamano_mensaje+'px;"><div id="spb_gantt_mensaje_cerrar" name="spb_gantt_mensaje_cerrar" class="spb_gantt_mensaje_cerrar" onClick="spb_gantt_cerrar_mensaje_usuarios();"></div><div id="spb_gantt_mensaje_resultado_usuarios" name="spb_gantt_mensaje_resultado_usuarios" class="spb_gantt_mensaje_resultado"></div></div>');
		$("#spb_gantt_mensaje_usuarios").toggle("fast");
		$("#spb_gantt_mensaje_usuarios").css('z-index',50);
		$("#spb_gantt_mensaje_usuarios").css('z-index',50);
		$("#spb_gantt_mensaje_usuarios").css('left',0+'px');
		$("#spb_gantt_mensaje_usuarios").css('top',0+'px');
		
		
		$( "#spb_gantt_mas_anyo_atras" ).remove();
		$( "#spb_gantt_mas_anyo_adelante" ).remove();
		$( "#spb_gantt_mas_mes_atras" ).remove();
		$( "#spb_gantt_mas_mes_adelante" ).remove();
		$( "#spb_gantt_mas_dia_atras" ).remove();
		$( "#spb_gantt_mas_dia_adelante" ).remove();  

		$( "#spb_gantt_prev_anyo" ).remove();
		$( "#spb_gantt_next_anyo" ).remove();
		$( "#spb_gantt_prev_mes" ).remove();
		$( "#spb_gantt_next_mes" ).remove();
		$( "#spb_gantt_prev_dia" ).remove();
		$( "#spb_gantt_next_dia" ).remove();  
		
		spb_gantt_mas_usuarios_dato();

	})(jQuery);
}
function spb_gantt_cerrar_mensaje_mensaje()
{
	$( "#mensaje" ).remove();  
}
function spb_gantt_mas_usuarios_dato()
{
	(function ($) 
	{
		var opts          = {lines: 13,length: 20,width: 10,radius: 30,corners: 1,rotate: 0, direction: 1, color: '#000', speed: 1, trail: 60, shadow: true, hwaccel: false,className: 'spinner', zIndex: 2e9, top: 'auto', left: 'auto' };
		var target        = document.getElementById('spb_gantt_contenedor');
		var spinner_ficha = new Spinner(opts).spin(target);
		var Usuario       = new Array();
 		$(".Usuario").each(function() 
		{				
			Usuario.push($(this).attr( "id" ).replace("Usuario_", ""));
		});	
		var jsonString = JSON.stringify(Usuario);

		$.ajax({
		type: 'POST',
		url: './gantt/' + 'spb_gantt.php',    
		success: function(data)
		{
			$("body").css("cursor", "default");
			$('#spb_gantt_mensaje_resultado_usuarios').html(data);
			spinner_ficha.stop();
		},
		data:
		{
		     lista_usuarios:true,
			 usuarios:jsonString
		}		
		});	

	})(jQuery);
}	
function spb_gantt_cerrar_mensaje_usuarios()
{
	(function ($) 
	{
		$( "#spb_gantt_mensaje_usuarios" ).toggle("fast");	
		$( "#spb_gantt_mensaje_usuarios" ).remove();			
		spb_gantt_controladores();
	})(jQuery);
}
function spb_gantt_selecciona_usuario(obj)
{
	(function ($) 
	{		
		imagen = $( "#spb_gantt_mensaje_usuarios #" + obj.id ).attr("src");
		if(imagen.indexOf("yes.png") > -1)
		{
			imagen = imagen.replace("yes.png","no.png");			
		}
		else
		{
			imagen = imagen.replace("no.png","yes.png");					
		}
		$( "#spb_gantt_mensaje_usuarios #" + obj.id ).attr("src",imagen);
	})(jQuery);
}
function spb_gantt_selecciona_acepta_usuario(obj)
{
	(function ($) 
	{
		var Usuario       = new Array();
		$("#spb_gantt_mensaje_resultado_usuarios img").each(function() 
		{	
			id_usuario = this.id;
			imagen = $( "#spb_gantt_mensaje_resultado_usuarios #" + id_usuario ).attr("src");
			if(imagen.indexOf("yes.png") > -1)
			{
				Usuario.push(id_usuario.replace("_", "."));			
			}
							
		});	
		var jsonString = JSON.stringify(Usuario);
		spb_gantt_cerrar_mensaje_usuarios();
		spb_gantt_cambia_factor(jsonString);
	})(jQuery);
}
function spb_selecciono_fecha_dia(obj,dia,mes,anyo)
{
	var offset = $( obj ).offset();

	$( "#mensaje" ).remove();
	$('body').append('<div id="mensaje" name="mensaje" class="mensaje"><div id="mensaje_tarjet" name="mensaje_tarjet" class="mensaje_tarjet"></div></div>');
	$("#mensaje").toggle("fast");
	$("#mensaje").css('z-index',900999);
	$("#mensaje").css('left',offset.left+'px');
	$("#mensaje").css('top',offset.top+'px');
	$("#mensaje").draggable({refreshPositions: true  });

	spb_inicializo_fecha_dia('mensaje_tarjet',dia,mes,anyo);
}
function spb_selecciono_detalle_dia(obj,dia,mes,anyo)
{
	var offset = $( obj ).offset();

	$( "#mensaje" ).remove();
	$('body').append('<div id="mensaje" name="mensaje" class="mensaje"><div id="spb_gantt_mensaje_cerrar" name="spb_gantt_mensaje_cerrar" class="spb_gantt_mensaje_cerrar" onClick="spb_gantt_cerrar_mensaje_mensaje();"></div><div id="mensaje_tarjet" name="mensaje_tarjet" class="mensaje_tarjet"></div></div>');
	$("#mensaje").toggle("fast");
	$("#mensaje").css('z-index',900999);
	$("#mensaje").css('left',offset.left+'px');
	$("#mensaje").css('top',offset.top+'px');
	$("#mensaje").draggable({refreshPositions: true  });

	spb_inicializo_detalle_dia('mensaje_tarjet',dia,mes,anyo);
}
function spb_inicializo_detalle_dia(tarjet,dia,mes,anyo)
{
	(function ($) 
	{
		var opts          = {lines: 13,length: 20,width: 10,radius: 30,corners: 1,rotate: 0, direction: 1, color: '#000', speed: 1, trail: 60, shadow: true, hwaccel: false,className: 'spinner', zIndex: 2e9, top: 'auto', left: 'auto' };
		var target        = document.getElementById('spb_gantt_contenedor');
		var spinner_ficha = new Spinner(opts).spin(target);
		
		$.ajax({
		type: 'POST',
		url: './gantt/' + 'spb_gantt.php',   
		success: function(data)
		{
			$("body").css("cursor", "default");
			$('#'+tarjet).html(data);
			spinner_ficha.stop();
		},
		data:
		{
			 detalle_dia:true,
		     dia:dia,
		     mes:mes,
		     anyo:anyo
		}		
		});	

	})(jQuery);
}
function spb_inicializo_fecha_dia(tarjet,dia,mes,anyo)
{	
	$(function($)
	{
		$.datepicker.regional['es'] = 
		{
			 closeText: 'Cerrar',
			 prevText: '<Ant',
			 nextText: 'Sig>',
			 currentText: 'Hoy',
			 monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			 monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
			 dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
			 dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','S&aacute;b'],
			 dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
			 weekHeader: 'Sm',
			 dateFormat: 'dd-mm-yy',
			 firstDay: 1,
			 isRTL: false,
			 showMonthAfterYear: false,
			 yearSuffix: ''
		};
		$.datepicker.setDefaults($.datepicker.regional['es']);
		var fecha_inicio  = $( "#spb_gantt" ).attr( "inicio" );
		var fecha_fin     = $( "#spb_gantt" ).attr( "fin" );
		var fechai        = fecha_inicio.split("/");
		var fechaf        = fecha_fin.split("/");

	 	$('#'+tarjet).datepicker(
		{	
			minDate: new Date(fechai[2], parseInt(fechai[1])-1, fechai[0]),
			maxDate: new Date(fechaf[2], parseInt(fechaf[1])-1, fechaf[0]),
			showButtonPanel: true,
			dateFormat: 'dd-mm-yy',
			
			constrainInput: true,	
	
			onSelect: function (date) 
			{	
				$( '#'+tarjet ).datepicker( "destroy" );
				$( "#mensaje" ).remove();

				var scroll_actual   = parseInt($( "#spb_gantt_calendario" ).scrollLeft());
				var posicion        = 0;
				var tamano          = 0;
				var posicion_ultima = 0;
				var fecha_seleccion = date.split("-");	
				
				var dia             = "Dia_"+fecha_seleccion[2]+"_"+parseInt(fecha_seleccion[1])+"_"+fecha_seleccion[0];
				posicion            = $("#"+dia).css( "left" ).replace(/[^-\d\.]/g, '');
				//posicion = $("#Dia_2015_1_01").css( "left" ).replace(/[^-\d\.]/g, '');
				$( "#spb_gantt_calendario").animate(
				{
					scrollLeft: posicion
				}, 
				900);
				
				
			}
		});
	});	
}
function spb_calculadora_crear(tarjet)
{
	if($( '#' + tarjet ).attr( "spb_disparador" )==1)
	{
		var offset = $( '#' + tarjet ).offset();

		$( "#mensaje" ).remove();
		$('body').append('<div id="mensaje" name="mensaje" class="mensaje"><div id="spb_gantt_mensaje_cerrar" name="spb_gantt_mensaje_cerrar" class="spb_gantt_mensaje_cerrar" onClick="spb_gantt_cerrar_mensaje_mensaje();"></div><div id="mensaje_tarjet" name="mensaje_tarjet" class="mensaje_tarjet"></div></div>');
		$("#mensaje").toggle("fast");
		$("#mensaje").css('z-index',5);
		$("#mensaje").css('left',offset.left+'px');
		$("#mensaje").css('top',offset.top+'px');
		$("#mensaje").draggable({refreshPositions: true  });
		
		(function ($) 
		{
			var opts          = {lines: 13,length: 20,width: 10,radius: 30,corners: 1,rotate: 0, direction: 1, color: '#000', speed: 1, trail: 60, shadow: true, hwaccel: false,className: 'spinner', zIndex: 2e9, top: 'auto', left: 'auto' };
			var target        = document.getElementById('body');
			var spinner_ficha = new Spinner(opts).spin(target);
			
			$.ajax({
			type: 'POST',
			url: './gantt/' + 'spb_gantt_calculadora.php',   
			success: function(data)
			{
				$("body").css("cursor", "default");
				$('#mensaje_tarjet').html(data);
				spb_inicializo_fecha('inicio_calculadora');
				spb_inicializo_fecha('fin_calculadora');
				spinner_ficha.stop();
			},
			data:
			{
				 tarea:ID_SELECCIONADA
				 //dia:dia,
				 //mes:mes,
				 //anyo:anyo
			}		
			});	

		})(jQuery);
	}
	
}
function spb_inicializo_fecha(obj)
{	

	$('#'+obj).datepicker({		
		dateFormat: 'dd-mm-yy',
		constrainInput: true,
		onSelect: function (date) 
		{			
				if(obj=='inicio_calculadora')
				{
					spb_cambio_inicio();
				}
				if(obj=='fin_calculadora')
				{
					spb_cambio_fin();
				}
		}
	});

	$(function($){
     $.datepicker.regional['es'] = {
         closeText: 'Cerrar',
         prevText: '<Ant',
         nextText: 'Sig>',
         currentText: 'Hoy',
         monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
         monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
         dayNames: ['Domingo', 'Lunes', 'Martes', 'Mi&eacutercoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
         dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','S&aacute;b'],
         dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
         weekHeader: 'Sm',
         dateFormat: 'dd-mm-yy',
         firstDay: 1,
         isRTL: false,
         showMonthAfterYear: false,
         yearSuffix: ''
     };
     $.datepicker.setDefaults($.datepicker.regional['es']);
	});	
}
function spb_cambio_horas()
{
	var spb_calc_usuario = $('#usuario_calculadora').val();
	var spb_calc_inicio  = $('#inicio_calculadora').val();	
	var spb_calc_fin     = $('#fin_calculadora').val();
	var spb_calc_horas   = $('#horas_calculadora').val();
	var spb_calc_img_1   = $("#calculadora_imagen_inicio").attr("src");
	var spb_calc_img_2   = $("#calculadora_imagen_fin").attr("src");
	if(spb_calc_usuario!='' && spb_calc_img_1=='./gantt/image/private.png' && spb_calc_inicio!='')
	{
		spb_calculadora_cambio('fin_calculadora','[GestionPDT].[dbo].[CALCULADORA_OBJETIVOS_FIN] ',spb_calc_inicio,spb_calc_fin,spb_calc_usuario,spb_calc_horas);
	}
	else
	{
		if(spb_calc_usuario!='' && spb_calc_img_2=='./gantt/image/private.png' && spb_calc_fin!='')
		{
			spb_calculadora_cambio('inicio_calculadora','[GestionPDT].[dbo].[CALCULADORA_OBJETIVOS_INICIO] ',spb_calc_inicio,spb_calc_fin,spb_calc_usuario,spb_calc_horas);
		}		
	}
}
function spb_cambio_inicio()
{
	var spb_calc_usuario = $('#usuario_calculadora').val();
	var spb_calc_inicio  = $('#inicio_calculadora').val();	
	var spb_calc_fin     = $('#fin_calculadora').val();
	var spb_calc_horas   = $('#horas_calculadora').val();
	var spb_calc_img_1   = $("#calculadora_imagen_horas").attr("src");
	var spb_calc_img_2   = $("#calculadora_imagen_fin").attr("src");	
	if(spb_calc_usuario!='' && spb_calc_img_2=='./gantt/image/private.png' && spb_calc_fin!='')
	{
		spb_calculadora_cambio('horas_calculadora','[GestionPDT].[dbo].[CALCULADORA_OBJETIVOS_HORAS] ',spb_calc_inicio,spb_calc_fin,spb_calc_usuario,spb_calc_horas);		
	}
	else
	{
		if(spb_calc_usuario!='' && spb_calc_img_1=='./gantt/image/private.png' && spb_calc_horas!='')
		{
			spb_calculadora_cambio('fin_calculadora','[GestionPDT].[dbo].[CALCULADORA_OBJETIVOS_FIN] ',spb_calc_inicio,spb_calc_fin,spb_calc_usuario,spb_calc_horas);
		}		
	}
}
function spb_cambio_fin()
{
	var spb_calc_usuario = $('#usuario_calculadora').val();
	var spb_calc_inicio  = $('#inicio_calculadora').val();	
	var spb_calc_fin     = $('#fin_calculadora').val();
	var spb_calc_horas   = $('#horas_calculadora').val();
	var spb_calc_img_1   = $("#calculadora_imagen_horas").attr("src");
	var spb_calc_img_2   = $("#calculadora_imagen_inicio").attr("src");	
	if(spb_calc_usuario!='' && spb_calc_img_2=='./gantt/image/private.png' && spb_calc_inicio!='' && spb_calc_fin!='')
	{		
		spb_calculadora_cambio('horas_calculadora','[GestionPDT].[dbo].[CALCULADORA_OBJETIVOS_HORAS] ',spb_calc_inicio,spb_calc_fin,spb_calc_usuario,spb_calc_horas);		
	}
	else
	{
		if(spb_calc_usuario!='' && spb_calc_img_1=='./gantt/image/private.png' && spb_calc_fin!='' && spb_calc_horas!='' )
		{
			spb_calculadora_cambio('inicio_calculadora','[GestionPDT].[dbo].[CALCULADORA_OBJETIVOS_INICIO] ',spb_calc_inicio,spb_calc_fin,spb_calc_usuario,spb_calc_horas);
		}		
	}
}
function spb_cambio_usuario()
{
	var spb_calc_usuario = $('#usuario_calculadora').val();
	var spb_calc_inicio  = $('#inicio_calculadora').val();	
	var spb_calc_fin     = $('#fin_calculadora').val();
	var spb_calc_horas   = $('#horas_calculadora').val();
	var spb_calc_img_1   = $("#calculadora_imagen_inicio").attr("src");
	var spb_calc_img_2   = $("#calculadora_imagen_fin").attr("src");
	var spb_calc_img_3   = $("#calculadora_imagen_horas").attr("src");
	if(spb_calc_usuario!='' && spb_calc_img_1=='./gantt/image/private.png' && spb_calc_inicio!='')
	{
		alert('calculo horas');
	}
	else
	{
		if(spb_calc_usuario!='' && spb_calc_img_2=='./gantt/image/private.png' && spb_calc_fin!='')
		{
			alert('calculo dia inicio');
		}	
		else
		{
			if(spb_calc_usuario!='' && spb_calc_img_3=='./gantt/image/private.png' && spb_calc_horas!='')
			{				
				spb_calculadora_cambio('inicio_calculadora','[GestionPDT].[dbo].[CALCULADORA_OBJETIVOS_INICIO] ','',spb_calc_fin,spb_calc_usuario,spb_calc_horas);
			}		
		}
	}
}
function spb_calculadora_cambio(tarjet,funcion,inicio,fin,usuario,horas)
{
	
	(function ($) 
	{
		var opts          = {lines: 13,length: 20,width: 10,radius: 30,corners: 1,rotate: 0, direction: 1, color: '#000', speed: 1, trail: 60, shadow: true, hwaccel: false,className: 'spinner', zIndex: 2e9, top: 'auto', left: 'auto' };
		var target        = document.getElementById('body');
		var spinner_ficha = new Spinner(opts).spin(target);
		
		$.ajax({
		type: 'POST',
		url: './gantt/' + 'spb_gantt_calculadora.php',   
		success: function(data)
		{
			$("body").css("cursor", "default");			
			$('#'+tarjet).val(data);
			spinner_ficha.stop();
		},
		data:
		{
			 tarea:ID_SELECCIONADA,
		     inicio:inicio,
		     fin:fin,
		     asignado:usuario,
			 horas:horas,
			 funcion:funcion
		}		
		});	

	})(jQuery);
	
}
function spb_cambia_imagen_calculadora(obj)
{
	if(obj.id=='calculadora_imagen_inicio')
	{
		if($("#calculadora_imagen_inicio").attr("src")=="./gantt/image/nothing.png")
		{
			$("#calculadora_imagen_inicio").attr("src","./gantt/image/private.png");
			$("#calculadora_imagen_fin").attr("src","./gantt/image/nothing.png");
			$("#calculadora_imagen_horas").attr("src","./gantt/image/nothing.png");
		}
	}
	if(obj.id=='calculadora_imagen_fin')
	{
		if($("#calculadora_imagen_fin").attr("src")=="./gantt/image/nothing.png")
		{
			$("#calculadora_imagen_fin").attr("src","./gantt/image/private.png");
			$("#calculadora_imagen_inicio").attr("src","./gantt/image/nothing.png");
			$("#calculadora_imagen_horas").attr("src","./gantt/image/nothing.png");
		}
	}
	if(obj.id=='calculadora_imagen_horas')
	{
		if($("#calculadora_imagen_horas").attr("src")=="./gantt/image/nothing.png")
		{
			$("#calculadora_imagen_horas").attr("src","./gantt/image/private.png");
			$("#calculadora_imagen_inicio").attr("src","./gantt/image/nothing.png");
			$("#calculadora_imagen_fin").attr("src","./gantt/image/nothing.png");
		}
	}
}
function spb_acepta_calculadora()
{
	(function ($) 
	{
		var opts             = {lines: 13,length: 20,width: 10,radius: 30,corners: 1,rotate: 0, direction: 1, color: '#000', speed: 1, trail: 60, shadow: true, hwaccel: false,className: 'spinner', zIndex: 2e9, top: 'auto', left: 'auto' };
		var target           = document.getElementById('body');
		var spinner_ficha    = new Spinner(opts).spin(target);
		var spb_calc_usuario = $('#usuario_calculadora').val();
		var spb_calc_inicio  = $('#inicio_calculadora').val();	
		var spb_calc_fin     = $('#fin_calculadora').val();
		var spb_calc_horas   = $('#horas_calculadora').val();
		$.ajax({
		type: 'POST',
		url: './gantt/' + 'spb_gantt_calculadora.php',   
		success: function(data)
		{
			$("body").css("cursor", "default");			
			$('#'+tarjet).val(data);
			spinner_ficha.stop();
		},
		data:
		{
			 guardar:true,
			 tarea:ID_SELECCIONADA,
		     inicio:spb_calc_inicio,
		     fin:spb_calc_fin,
		     asignado:usuario,
			 horas:spb_calc_horas
		}		
		});	

	})(jQuery);
}