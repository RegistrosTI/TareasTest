function kalvinnet_gantt_cambia_factor(obj)
{
	(function ($) 
	{
		$("body").css("cursor", "progress");

		var scroll_actual  = $( "#kalvinnet_gantt_calendario" ).scrollLeft();
		var factor_actual  = $( "#kalvinnet_gantt_slider" ).attr( "factor_anterior" );
		var fecha_inicio   = $( "#kalvinnet_gantt" ).attr( "inicio" );
		var fecha_fin      = $( "#kalvinnet_gantt" ).attr( "fin" );
		var usuario_filtro = $( "#kalvinnet_gantt" ).attr( "usuario" );
		var factor_nuevo   = $( "#kalvinnet_gantt_slider" ).val();
		var resultado      = scroll_actual/factor_actual;
		
	
		var opts               = { lines: 13, length: 20, width: 10, radius: 30, corners: 1, rotate: 0, direction: 1, color: '#000',  speed: 1, trail: 60, shadow: true, hwaccel: false, className: 'spinner', zIndex: 2e9, top: 'auto', left: 'auto' };
		var target             = document.getElementById('kalvinnet_gantt_contenedor');
		var spinner_calendario = new Spinner(opts).spin(target);	
	
		$.ajax({
		type: 'POST',
		cache : false,
		url: Drupal.settings.basePath + 'kalvinnet_gantt/kalvinnet_gantt_ajax',    
		success: function(data)
		{
		
			$("body").css("cursor", "default");
			$('#kalvinnet_gantt_contenedor').html(data);
			if(scroll_actual!=0 && factor_nuevo!=0)
			{
				$( "#kalvinnet_gantt_calendario" ).scrollLeft(resultado*factor_nuevo);
			}
			kalvinnet_gantt_controladores();
			kalvinnet_gantt_cambia_pantalla();			
		},
		data:
		{
		     factor:obj.value,
		     inicio:fecha_inicio,
		     fin:fecha_fin,
		     usuario:usuario_filtro
		}		
		});
		
	})(jQuery);
}
function kalvinnet_gantt_controladores()
{
	(function ($) 
	{
		$(document).ready(function() 
		{
			var position     = $('#kalvinnet_gantt_calendario').position();
			var top_anyo     = $('.Anyo').css('height').replace(/[^-\d\.]/g, '');
			var top_mes      = $('.Mes').css('height').replace(/[^-\d\.]/g, '');	
			var top_semana   = $('.Semanas').css('height').replace(/[^-\d\.]/g, '');	
			var top_dia      = $('.Dias').css('height').replace(/[^-\d\.]/g, '');
			var altura_gantt = $('#kalvinnet_gantt').css('height').replace(/[^-\d\.]/g, '');	
			
			var width_anyo = 0;
			$(".Anyo").each(function (index) 
			{
				width_anyo = width_anyo + $('#'+this.id).width();				
			});

			
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_mas_anyo_atras" class="Controlador_mas" onClick="kalvinnet_gantt_agrega_fecha(1)"></div>' );
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_mas_anyo_adelante" class="Controlador_mas" onClick="kalvinnet_gantt_agrega_fecha(2)"></div>' );
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_mas_mes_atras" class="Controlador_mas" onClick="kalvinnet_gantt_agrega_fecha(3)"></div>' );
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_mas_mes_adelante" class="Controlador_mas" onClick="kalvinnet_gantt_agrega_fecha(4)"></div>' );
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_mas_dia_atras" class="Controlador_mas_beige" onClick="kalvinnet_gantt_agrega_fecha(5)"></div>' );
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_mas_dia_adelante" class="Controlador_mas_beige" onClick="kalvinnet_gantt_agrega_fecha(6)"></div>' );
			
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_next_anyo" class="Controlador_desplazar_next" onClick="kalvinnet_gantt_desplaza_fecha(1)"></div>' );
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_prev_anyo" class="Controlador_desplazar_prev" onClick="kalvinnet_gantt_desplaza_fecha(2)"></div>' );
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_next_mes" class="Controlador_desplazar_next" onClick="kalvinnet_gantt_desplaza_fecha(3)"></div>' );
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_prev_mes" class="Controlador_desplazar_prev" onClick="kalvinnet_gantt_desplaza_fecha(4)"></div>' );
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_next_dia" class="Controlador_desplazar_next" onClick="kalvinnet_gantt_desplaza_fecha(5)"></div>' );
			$( "#kalvinnet_gantt_contenedor" ).append( '<div id="kalvinnet_gantt_prev_dia" class="Controlador_desplazar_prev" onClick="kalvinnet_gantt_desplaza_fecha(6)"></div>' );
			
			if(parseFloat($('#kalvinnet_gantt_calendario').width())<width_anyo)
			{
				width_anyo = $('#kalvinnet_gantt_calendario').width();
			}	
			$( "#kalvinnet_gantt_mas_anyo_atras" ).css( "top", position.top+"px" );
			$( "#kalvinnet_gantt_mas_anyo_atras" ).css( "left", (parseFloat(position.left)-20)+"px" );
			$( "#kalvinnet_gantt_mas_anyo_atras" ).css( "height", top_anyo+"px" );					       
			$( "#kalvinnet_gantt_mas_anyo_atras" ).css( "border-radius", "5px 0px 0px 5px" );		
			$( "#kalvinnet_gantt_mas_anyo_adelante" ).css( "top", position.top+"px" );
			$( "#kalvinnet_gantt_mas_anyo_adelante" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo))+"px" );
			$( "#kalvinnet_gantt_mas_anyo_adelante" ).css( "height", top_anyo+"px" );
			$( "#kalvinnet_gantt_mas_anyo_adelante" ).css( "border-radius", "0px 5px 5px 0px" );
			
			$( "#kalvinnet_gantt_prev_anyo" ).css( "top", position.top+"px" );
			$( "#kalvinnet_gantt_prev_anyo" ).css( "left", (parseFloat(position.left))+"px" );
			$( "#kalvinnet_gantt_prev_anyo" ).css( "height", top_anyo+"px" );					       			
			$( "#kalvinnet_gantt_next_anyo" ).css( "top", position.top+"px" );
			$( "#kalvinnet_gantt_next_anyo" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo)-20)+"px" );
			$( "#kalvinnet_gantt_next_anyo" ).css( "height", top_anyo+"px" );			
			
			$( "#kalvinnet_gantt_mas_mes_atras" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo))+"px" );
			$( "#kalvinnet_gantt_mas_mes_atras" ).css( "left", (parseFloat(position.left)-20)+"px" );
			$( "#kalvinnet_gantt_mas_mes_atras" ).css( "height", top_mes+"px" );
			$( "#kalvinnet_gantt_mas_mes_atras" ).css( "border-radius", "5px 0px 0px 5px" );						
			$( "#kalvinnet_gantt_mas_mes_adelante" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo))+"px" );
			$( "#kalvinnet_gantt_mas_mes_adelante" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo))+"px" );
			$( "#kalvinnet_gantt_mas_mes_adelante" ).css( "height", top_mes+"px" );
			$( "#kalvinnet_gantt_mas_mes_adelante" ).css( "border-radius", "0px 5px 5px 0px" );	

			$( "#kalvinnet_gantt_prev_mes" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo))+"px" );
			$( "#kalvinnet_gantt_prev_mes" ).css( "left", (parseFloat(position.left))+"px" );
			$( "#kalvinnet_gantt_prev_mes" ).css( "height", top_mes+"px" );			
			$( "#kalvinnet_gantt_next_mes" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo))+"px" );
			$( "#kalvinnet_gantt_next_mes" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo)-20)+"px" );
			$( "#kalvinnet_gantt_next_mes" ).css( "height", top_mes+"px" );	
			
			$( "#kalvinnet_gantt_mas_dia_atras" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo)+parseFloat(top_mes))+"px" );
			$( "#kalvinnet_gantt_mas_dia_atras" ).css( "left", (parseFloat(position.left)-20)+"px" );
			$( "#kalvinnet_gantt_mas_dia_atras" ).css( "height", (parseFloat(top_semana)+parseFloat(top_dia))+"px" );
			$( "#kalvinnet_gantt_mas_dia_atras" ).css( "border-radius", "5px 0px 0px 5px" );					
			$( "#kalvinnet_gantt_mas_dia_adelante" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo)+parseFloat(top_mes))+"px" );
			$( "#kalvinnet_gantt_mas_dia_adelante" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo))+"px" );
			$( "#kalvinnet_gantt_mas_dia_adelante" ).css( "height", (parseFloat(top_semana)+parseFloat(top_dia))+"px" );
			$( "#kalvinnet_gantt_mas_dia_adelante" ).css( "border-radius", "0px 5px 5px 0px" );		
			
			$( "#kalvinnet_gantt_prev_dia" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo)+parseFloat(top_mes))+"px" );
			$( "#kalvinnet_gantt_prev_dia" ).css( "left", (parseFloat(position.left))+"px" );
			$( "#kalvinnet_gantt_prev_dia" ).css( "height", (parseFloat(altura_gantt)-(parseFloat(top_semana)+parseFloat(top_dia)))+"px" );
			$( "#kalvinnet_gantt_next_dia" ).css( "top", (parseFloat(position.top)+parseFloat(top_anyo)+parseFloat(top_mes))+"px" );
			$( "#kalvinnet_gantt_next_dia" ).css( "left", (parseFloat(position.left)+parseFloat(width_anyo)-20)+"px" );
			$( "#kalvinnet_gantt_next_dia" ).css( "height", (parseFloat(altura_gantt)-(parseFloat(top_semana)+parseFloat(top_dia)))+"px" );
			

			$("div.Tarea.nueva_alta").click(function(){ $("div.Tarea.nueva_alta").css("display","none");kalvinnet_ocultar_ausencia_alta(); });
			$("div.Tarea.baja").click(function(){ $("div.Tarea.baja").css("display","none");kalvinnet_ocultar_ausencia_baja(); });			
			$("div.Tarea.ausencia_enfermero").click(function(){ $("div.Tarea.ausencia_enfermero").css("display","none");kalvinnet_ocultar_ausencia_enfermero(); });
		});
	})(jQuery);
}
function kalvinnet_mostrar_ausencia_enfermero()
{
	(function ($) 
	{
		$("div.Tarea.ausencia_enfermero").css("display","block");
		$("#Usuarios_Hueco div.ausencia_enfermero").remove();
	})(jQuery);
}
function kalvinnet_ocultar_ausencia_enfermero()
{
	(function ($) 
	{
		$("#Usuarios_Hueco").append('<div onClick="kalvinnet_mostrar_ausencia_enfermero()" class="ausencia_enfermero" style="width: 100%;text-align: center;color: #FFFFEA;font-size: 12px;top: 0px;font-weight: bold;text-shadow: 2px 2px 0px #000;">Ausencia Enfermero</div>');
	})(jQuery);	
}

function kalvinnet_mostrar_ausencia_alta()
{
	(function ($) 
	{
		$("div.Tarea.nueva_alta").css("display","block");
		$("#Usuarios_Hueco div.nueva_alta").remove();
	})(jQuery);	 
}
function kalvinnet_ocultar_ausencia_alta()
{
	(function ($) 
	{
		$("#Usuarios_Hueco").append('<div onClick="kalvinnet_mostrar_ausencia_alta()" class="nueva_alta" style="width: 100%;text-align: center;color: #FFFFEA;font-size: 12px;top: 18px;font-weight: bold;text-shadow: 2px 2px 0px #000;">Ausencia Alta</div>');
	})(jQuery);		
}
function kalvinnet_mostrar_ausencia_baja()
{
	(function ($) 
	{
		$("div.Tarea.baja").css("display","block");
		$("#Usuarios_Hueco div.baja").remove();
	})(jQuery);		
}
function kalvinnet_ocultar_ausencia_baja()
{
	(function ($) 
	{
		$("#Usuarios_Hueco").append('<div onClick="kalvinnet_mostrar_ausencia_baja()" class="baja" style="width: 100%;text-align: center;color: #FFFFEA;font-size: 12px;top: 36px;font-weight: bold;text-shadow: 2px 2px 0px #000;">Baja Enfermero</div>');
	})(jQuery);		
}

function kalvinnet_gantt_agrega_fecha(paso)
{
	(function ($) 
	{
	
		var fecha_inicio  = $( "#kalvinnet_gantt" ).attr( "inicio" );
		var fecha_fin     = $( "#kalvinnet_gantt" ).attr( "fin" );
		switch(paso) 
		{
		case 6:
			var fecha      = fecha_fin.split("/");
			var dt         = new Date(fecha[1]+"/"+fecha[0]+"/"+fecha[2]);		
			var dayOfMonth = dt.getDate();
			dt.setDate(dayOfMonth + 7);
			$( "#kalvinnet_gantt" ).attr( "fin",kalvinnet_gantt_convertDate(dt));			
			break;
		case 5:
			var fecha      = fecha_inicio.split("/");
			var dt         = new Date(fecha[1]+"/"+fecha[0]+"/"+fecha[2]);		
			var dayOfMonth = dt.getDate();
			dt.setDate(dayOfMonth - 7);
			$( "#kalvinnet_gantt" ).attr( "inicio",kalvinnet_gantt_convertDate(dt));			
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
			$( "#kalvinnet_gantt" ).attr( "fin",kalvinnet_gantt_convertDate(dt));				
			break;
		case 3:
			var fecha      = fecha_inicio.split("/");
			var dt         = new Date(fecha[2],(parseInt(fecha[1])-2),"1");					
			var day        = dt.getDay()-1;
			var dayOfMonth = dt.getDate();
			dt.setDate(dayOfMonth - day);
			$( "#kalvinnet_gantt" ).attr( "inicio",kalvinnet_gantt_convertDate(dt));			
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
			$( "#kalvinnet_gantt" ).attr( "fin",kalvinnet_gantt_convertDate(dt));				
			break;
		case 1:
			var fecha      = fecha_inicio.split("/");
			var dt         = new Date(parseInt(fecha[2])-1,"0","1");					
			var day        = dt.getDay()-1;
			var dayOfMonth = dt.getDate();
			dt.setDate(dayOfMonth - day);
			$( "#kalvinnet_gantt" ).attr( "inicio",kalvinnet_gantt_convertDate(dt));			
			break;			
		}
		kalvinnet_gantt_cambia_factor($( "#kalvinnet_gantt_slider" )[0]);

	})(jQuery);
}
function kalvinnet_gantt_convertDate(inputFormat) 
{
  function pad(s) { return (s < 10) ? '0' + s : s; }
  var d = new Date(inputFormat);
  return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/');
}
function kalvinnet_gantt_cambia_pantalla()
{
	(function ($) 
	{
		$(window).resize(function()
		{
            $( "#kalvinnet_gantt_mas_anyo_atras" ).remove();
			$( "#kalvinnet_gantt_mas_anyo_adelante" ).remove();
			$( "#kalvinnet_gantt_mas_mes_atras" ).remove();
			$( "#kalvinnet_gantt_mas_mes_adelante" ).remove();
			$( "#kalvinnet_gantt_mas_dia_atras" ).remove();
			$( "#kalvinnet_gantt_mas_dia_adelante" ).remove();  

			$( "#kalvinnet_gantt_prev_anyo" ).remove();
			$( "#kalvinnet_gantt_next_anyo" ).remove();
			$( "#kalvinnet_gantt_prev_mes" ).remove();
			$( "#kalvinnet_gantt_next_mes" ).remove();
			$( "#kalvinnet_gantt_prev_dia" ).remove();
			$( "#kalvinnet_gantt_next_dia" ).remove();  
			
			kalvinnet_gantt_controladores();
        });
	})(jQuery);
}
function kalvinnet_gantt_desplaza_fecha_paso(porcion,velocidad,direccion)
{
	(function ($) 
	{
		var scroll_actual   = parseInt($( "#kalvinnet_gantt_calendario" ).scrollLeft());
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
		$( "#kalvinnet_gantt_calendario").animate(
		{
			scrollLeft: posicion_ultima
		}, 
		velocidad);
	})(jQuery);	
}
function kalvinnet_gantt_desplaza_fecha(paso)
{
	(function ($) 
	{
		switch(paso) 
			{
			case 6:
				kalvinnet_gantt_desplaza_fecha_paso('Semana',800,-1);
				break;
			case 5:
				kalvinnet_gantt_desplaza_fecha_paso('Semana',800,1);
				break;
			case 4:
				kalvinnet_gantt_desplaza_fecha_paso('Mes',600,-1);
				break;
			case 3:
				kalvinnet_gantt_desplaza_fecha_paso('Mes',600,1);
				break;
			case 2:
				kalvinnet_gantt_desplaza_fecha_paso('Anyo',300,-1);
				break;
			case 1:
				kalvinnet_gantt_desplaza_fecha_paso('Anyo',300,1);
				break;			
			}
	})(jQuery);
}
function kalvinnet_gantt_mostrar_servicio(id_servicio,id_delta,id_enfermero)
{
	(function ($) 
	{
		var scroll_actual   = parseInt($( "#kalvinnet_gantt_calendario" ).scrollLeft());
		var tamano_mensaje  = parseInt($("#kalvinnet_gantt").width())-parseInt($("#Usuarios_Nombres").width());				
		$( "#kalvinnet_gantt_mensaje" ).remove();		
		$("#kalvinnet_gantt_calendario").css("overflow", "hidden");
		$('#kalvinnet_gantt_calendario').append('<div id="kalvinnet_gantt_mensaje" name="kalvinnet_gantt_mensaje" class="kalvinnet_gantt_mensaje" style="width:'+tamano_mensaje+'px;"><div id="kalvinnet_gantt_mensaje_cerrar" name="kalvinnet_gantt_mensaje_cerrar" class="kalvinnet_gantt_mensaje_cerrar" onClick="kalvinnet_gantt_cerrar_mensaje();"></div><div id="kalvinnet_gantt_mensaje_resultado" name="kalvinnet_gantt_mensaje_resultado" class="kalvinnet_gantt_mensaje_resultado"></div></div>');
		$("#kalvinnet_gantt_mensaje").toggle("fast");
		$("#kalvinnet_gantt_mensaje").css('z-index',10);
		$("#kalvinnet_gantt_mensaje").css('z-index',10);
		$("#kalvinnet_gantt_mensaje").css('left',scroll_actual+'px');
		$("#kalvinnet_gantt_mensaje").css('top',0+'px');
		
		
		$( "#kalvinnet_gantt_mas_anyo_atras" ).remove();
		$( "#kalvinnet_gantt_mas_anyo_adelante" ).remove();
		$( "#kalvinnet_gantt_mas_mes_atras" ).remove();
		$( "#kalvinnet_gantt_mas_mes_adelante" ).remove();
		$( "#kalvinnet_gantt_mas_dia_atras" ).remove();
		$( "#kalvinnet_gantt_mas_dia_adelante" ).remove();  

		$( "#kalvinnet_gantt_prev_anyo" ).remove();
		$( "#kalvinnet_gantt_next_anyo" ).remove();
		$( "#kalvinnet_gantt_prev_mes" ).remove();
		$( "#kalvinnet_gantt_next_mes" ).remove();
		$( "#kalvinnet_gantt_prev_dia" ).remove();
		$( "#kalvinnet_gantt_next_dia" ).remove();  
		
		kalvinnet_gantt_mostrar_servicio_dato(id_servicio,id_delta,id_enfermero);

	})(jQuery);
}	
function kalvinnet_gantt_mostrar_servicio_dato(id_servicio,id_delta,id_enfermero)
{
	(function ($) 
	{
		var opts          = {lines: 13,length: 20,width: 10,radius: 30,corners: 1,rotate: 0, direction: 1, color: '#000', speed: 1, trail: 60, shadow: true, hwaccel: false,className: 'spinner', zIndex: 2e9, top: 'auto', left: 'auto' };
		var target        = document.getElementById('kalvinnet_gantt_contenedor');
		var spinner_ficha = new Spinner(opts).spin(target);
		
		$.ajax({
		type: 'POST',
		cache : false,
		url: Drupal.settings.basePath + 'kalvinnet_gantt/kalvinnet_gantt_servicio',    
		success: function(data)
		{
			$("body").css("cursor", "default");
			$('#kalvinnet_gantt_mensaje_resultado').html(data);
			spinner_ficha.stop();
		},
		data:
		{
		     id_servicio:id_servicio,
		     id_delta:id_delta,
		     id_enfermero:id_enfermero
		}		
		});	

	})(jQuery);
}			
function kalvinnet_gantt_cerrar_mensaje()
{
	(function ($) 
	{
		$( "#kalvinnet_gantt_mensaje" ).toggle("fast");	
		$( "#kalvinnet_gantt_mensaje" ).remove();	
		$("#kalvinnet_gantt_calendario").css("overflow", "auto");
		kalvinnet_gantt_controladores();
	})(jQuery);
}
