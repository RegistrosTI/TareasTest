function feedback_feedbacks(obj , tarea) {

	$.ajax( {
		type : 'POST' ,
		url : './feedback/comprobarFeedback.php' ,// ?usuario=' + encodeURIComponent( usuariovalidado ) + '&tarea=' + encodeURIComponent( tarea ) ,
		cache : false ,
		data : {
			tarea : encodeURIComponent( tarea )
		} ,
		success : function(data) {
			if ( data == 0 ) {
				 mensaje('Solicitud de Feedback' ,'No existe ninguna pregunta en el maestro de preguntas de feedback para este tipo de tarea.<br><br>Es necesario crear al menos una pregunta en el Maestro de Feedback del menú de configuración.');
			}else{
				// var offset = $( '#' + obj.id ).offset();
				$( "#feedback_feedback" ).remove( );
				$( 'body' ).append( '<div id="feedback_feedback" name="feedback_feedback" class="feedback"><div id="feedback_cerrar_feedback" name="feedback_cerrar_feedback" class="feedback_cerrar" onClick="cerrar_feedback();"></div><div id="feedback_resultado" name="feedback_resultado" class="feedback_resultado"><div id="feedback_texto"></div></div></div>' );
				$( "#feedback_feedback" ).toggle( 2000 );
				//$( "#feedback_feedback" ).css( 'left' , '50px' );
				//$( "#feedback_feedback" ).css( 'top' , '250px' );
				//$( "#feedback_feedback" ).draggable( {
				//	refreshPositions : true
				//} );
				$.ajax( {
					type : 'POST' ,
					url : './feedback/feedback.php' ,// ?usuario=' + encodeURIComponent( usuariovalidado ) + '&tarea=' + encodeURIComponent( tarea ) ,
					cache : false ,
					data : {
						feedback_feedbacks : true ,
						usuario : encodeURIComponent( usuariovalidado ) ,
						tarea : encodeURIComponent( tarea ) ,
					} ,
					success : function(data) {
						feedback_message_feedback( obj , data , tarea );
						if ( $( '#autocompletemascorreo' ).length ) {
							$( "#autocompletemascorreo" ).autocomplete( {
								type : 'post' ,
								source : function(request , response) {
									$.get( "select/getUsuarioautocomplete.php" , {
										buscar : request.term ,
										tipo : 1
									} , function(data) {
										tags = data.split( "|" );
										response( tags );
									} );
								}
							} );
						}
					}
				} );
			}
		}
	} );

}
function feedback_message_feedback(obj , texto , tarea) {
	$( "#feedback_texto" ).html( texto );
}
function cerrar_feedback() {
	$( "#feedback_feedback" ).toggle( "slow",function(){$( "#feedback_feedback" ).remove( )} );
	//
}
function feedback_less(div) {
	$( "#" + div ).remove( );
}
function feedback_more(tarea) {
	var numero = $( "#feedback_destinatarios_mas div.feedback_anadido" ).length;
	$( "#feedback_destinatarios_mas" ).append( '<div id="feedback_anadido_' + numero + '" class="feedback_anadido"><img class="feedback_menos_imagen" id="feedback_menos_imagen" src="feedback/imagen/erase.png" onclick="feedback_less(\'feedback_anadido_' + numero + '\');"><div class="correo_feedback_anadido">' + $( "#autocompletemascorreo" ).val( ) + '</div></div>' );
	$( "#autocompletemascorreo" ).val( '' );
}
function feedback_send(tarea) {
	var correos_extras = '';
	$( ".correo_feedback_anadido" ).each( function(index) {
		correos_extras = correos_extras + '|' + $( this ).html( );
	} );

	var opts = {
		lines : 13 ,
		length : 20 ,
		width : 10 ,
		radius : 30 ,
		corners : 1 ,
		rotate : 0 ,
		direction : 1 ,
		color : '#000' ,
		speed : 1 ,
		trail : 60 ,
		shadow : true ,
		hwaccel : false ,
		className : 'spinner' ,
		zIndex : 2e9 ,
		top : 'auto' ,
		left : 'auto'
	};
	var target = document.getElementById( 'feedback_feedback' );
	var spinner = new Spinner( opts ).spin( target );
	$.ajax( {
		type : 'POST' ,
		url : './feedback/feedback.php' ,// ?usuario=' + encodeURIComponent( usuariovalidado ) + '&tarea=' + encodeURIComponent( tarea ) ,
		cache : false ,
		data : {
			feedback_send : true ,
			usuario : encodeURIComponent( usuariovalidado ) ,
			tarea : encodeURIComponent( tarea ) ,
			feedback_message : $( "#feedback_textarea" ).html( ) ,
			correos_extras : correos_extras
		} ,
		success : function(data) {
			spinner.stop;
			var myJson = JSON.parse( data );
			var mensaje = myJson.data[ 0 ].mensaje;
			var offset = $( '#feedback_textarea' ).offset( );
			$( "#respuesta" ).remove( );
			$( 'body' ).append( '<div class="respuesta" id="respuesta"></div>' );
			if ( myJson.tipo == -1 ) {
				$( "#respuesta" ).html( '<div id="div_respuesta" class="div_subir_ficheros_ocultar"><img class="subir_ficheros_ocultar" src="imagenes/close.png" onclick="respuesta_ocultar();"></div><div class="titulo_error">Se ha producido el siguiente error:</div>' + mensaje );
				$( "#respuesta" ).toggle( "fast" );
				$( "#respuesta" ).draggable( {
					refreshPositions : true
				} );
				$( "#respuesta" ).css( "left" , offset.left + 'px' );
				$( "#respuesta" ).css( "top" , offset.top + 'px' );
			}
			if ( myJson.tipo == 1 ) {
				$( "#respuesta" ).html( mensaje );
				$( "#respuesta" ).toggle( "fast" );
				$( "#respuesta" ).draggable( {
					refreshPositions : true
				} );
				$( "#respuesta" ).css( "left" , offset.left + 'px' );
				$( "#respuesta" ).css( "top" , offset.top + 'px' );
				$( document ).ready( function() {
					setTimeout( function() {
						$( "#respuesta" ).fadeOut( 1000 );
						$( "#respuesta" ).remove( );
					} , 6000 );
				} );
				$( "#feedback_imagen" ).attr( "src" , "./imagenes/feedback.png" );
				cerrar_feedback( );
			}

		}
	} );

}

function feedback_ver_respuestas(tarea){
	alert(tarea);
}