//utf8 ÁÁ
function validaciones() {

	obligado = true;

	var oficina_tarea = '';
	if ( $( "#selectoficina" ).length > 0 ) {
		oficina_tarea = $( "#selectoficina" ).val( );
	}

	//GESTION DE LOS APORTES
	if ( $( "#selectaporte" ).length && $( '#selectaporteempresa' ).length && obligado ) {
		
		$( "#selectaporte" ).removeClass( "rojo" );
		$( "#selectaporteempresa" ).removeClass( "rojo" );
		var selectaporte = $( "#selectaporte" ).val( );
		var selectaporteempresa = $( "#selectaporteempresa" ).val( );
		
		// VALIDAMOS QUE SE INFORME APORTE ORGANIZACION (PROPIO)
		if ( (selectaporte == 'N/A' || selectaporte == '') && !$.isNumeric( selectaporteempresa) && obligado ) {
			obligado = false;
			$( "#selectaporte" ).focus( );
			$( "#selectaporte" ).addClass( "rojo" );
			$( "#selectaporteempresa" ).addClass( "rojo" );
			mensaje( 'Campo obligatorio' , 'Es necesario informar el aporte propio o el aporte empresa, para más información vea la ayuda.' , 'alert.png' );
		}
		
		// VALIDAMOS QUE SE INFORME APORTE EMPRESA
		if ( (selectaporteempresa == 'N/A' || selectaporteempresa == '') && !$.isNumeric( selectaporte ) && obligado ) {
			obligado = false;
			$( "#selectaporteempresa" ).focus( );
			$( "#selectaporteempresa" ).addClass( "rojo" );
			mensaje( 'Campo obligatorio' , 'Es necesario informar el aporte propio o el aporte empresa, para más información vea la ayuda.' , 'alert.png' );
		}
	}

	// VALIDAMOS QUE SE INFORME MOTIVO BAP CUANDO EL VALOR DE BAP SEA DADO
	if ( $( '#selectbap' ).length ) {
		if ( oficina_tarea == 'TI' ) {
			var selectbap = $( "#selectbap" ).val( );
			if ( selectbap != '0' ) {
				if ( $( '#motivobap' ).length && obligado ) {
					$( "#motivobap" ).removeClass( "rojo" );
					var obligadobap = $( "#motivobap" ).val( );
					obligadobap = obligadobap.replace( " " , '' );
					if ( obligadobap == '' ) {
						obligado = false;
						$( '#motivobap' ).focus( );
						$( "#motivobap" ).addClass( "rojo" );
						mensaje( 'Campo obligatorio' , 'El campo motivo bap es obligatorio' , 'alert.png' );
					}
				}
			}
		}
	}

	// VALIDAMOS QUE EL CAMPO HORAS ESTE INFORMADO
	if ( $( '#selecttipo' ).length && obligado ) {
		var selecttipo = $( "#selecttipo" ).val( );
    	//var target = document.getElementById( 'micuerpo' );
    	//var spinner = new Spinner( opts ).spin( target );
        $.ajax({
        	url: "select/getTiposObligadoHora.php?tipo=0&selecttipo="+selecttipo+"&oficina="+oficina_tarea, 
        	async:false,
        	cache:false,
            success: function(data) {
            	if(data == 1){
        			var obligadohorasestimadas = $( "#horasestimadas" ).val( );
        			obligadohorasestimadas = obligadohorasestimadas.replace( " " , '' );
        			obligadohorasestimadas = obligadohorasestimadas.replace( "." , '' );
        			intHoras = parseInt( obligadohorasestimadas );
        			$( "#horasestimadas" ).removeClass( "rojo" );
        			if ( obligadohorasestimadas == '' || intHoras == 0 ) {
        				obligado = false;
        				$( '#horasestimadas' ).focus( );
        				$( "#horasestimadas" ).addClass( "rojo" );
        				mensaje( 'Campo obligatorio' , 'El campo horas estimadas es obligatorio' , 'alert.png' );
        			}
            	}
            	//spinner.stop( );
            }
        });
		
		/*
		if ( selecttipo == 'Tarea' || selecttipo == 'Propuesta Mejora' || selecttipo == 'Antierror' || selecttipo == 'Mejora' || selecttipo == 'Formación' ) {
			var obligadohorasestimadas = $( "#horasestimadas" ).val( );
			obligadohorasestimadas = obligadohorasestimadas.replace( " " , '' );
			obligadohorasestimadas = obligadohorasestimadas.replace( "." , '' );
			intHoras = parseInt( obligadohorasestimadas );
			$( "#horasestimadas" ).removeClass( "rojo" );
			if ( obligadohorasestimadas == '' || intHoras == 0 ) {
				obligado = false;
				$( '#horasestimadas' ).focus( );
				$( "#horasestimadas" ).addClass( "rojo" );
				mensaje( 'Campo obligatorio' , 'El campo horas estimadas es obligatorio' , 'alert.png' );
			}
		}
		*/
	}
	
	// ++ VALIDAMOS QUE LA CRITICIDAD EXISTA EN LOS TIPOS INCIDENCIA SEGURIDAD
	if(obligado && $( "#selectcriticidad" ).length == 1 && $( "#selecttipo" ).length == 1 ){
		$( "#selectcriticidad" ).removeClass( "rojo" );
		if(($( "#selectcriticidad" ).val( ) == '-' || $( "#selectcriticidad" ).val( ) == '') && $( "#selecttipo" ).val( ) == 'Incidencia Seguridad'){
			$( '#selectcriticidad' ).focus( );
			$( "#selectcriticidad" ).addClass( "rojo" );
			mensaje( 'Campo obligatorio' , 'El campo criticidad es obligatorio para el tipo de tarea Incidencia Seguridad' , 'alert.png' );
			obligado = false;
		}
	};
	// -- VALIDAMOS QUE LA CRITICIDAD EXISTA EN LOS TIPOS INCIDENCIA SEGURIDAD
	
	return obligado;
}
