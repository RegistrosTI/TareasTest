//UTF8 Espàñá
function menu_incidencia(id) {
	var autorizacion_vacio = document.getElementById( 'misecciondatos' );
	autorizacion_vacio.innerHTML = "";
	ID_PANTALLA = id_visualizacion;

	var ajax = nuevoAjax( );
	var lista;

	var target = document.getElementById( 'micuerpo' );
	var spinner = new Spinner( opts ).spin( target );

	ajax.open( "POST" , "select/obtener_incidencia.php?ext=" + externo + "&incidencia=" + ID_SELECCIONADA + '&usuario=' + encodeURIComponent( usuariovalidado ) + "&id=" + id_visualizacion , true );
	ajax.onreadystatechange = function() {
		if ( ajax.readyState != 4 ) {
			// NO ESTA LISTO!!!!!!!!!!
		} else {
			
			spinner.stop( );
			lista = ( ajax.responseText );
			var autorizacion = document.getElementById( 'misecciondatos' );
			autorizacion.innerHTML = lista;

			// VALIDAMOS QUE EL USUARIO ASIGNADO EXISTA Y ESTÉ EN LA MISMA OFICINA QUE LA TAREA
			var asignadoa_anterior;
			if ( $( "#autocompleteasignado" ).length && $( "#selectoficina" ).length ) {
				$( "#autocompleteasignado" ).on( 'focus' , function() {
					asignadoa_anterior = this.value;
				} ).change( function() {
					var autocompleteasignado = $( "#autocompleteasignado" ).val( );
					// alert("NUEVO: "+autocompleteasignado+"\nANTERIOR: "+asignadoa_anterior);
					$( "#autocompleteasignado" ).removeClass( "rojo" );
					if ( autocompleteasignado != '' ) {
						var selectoficina = $( "#selectoficina" ).val( );
						var url = "select/comprobarExisteNombre.php?buscar=usuarios&nombre=" + encodeURIComponent( autocompleteasignado ) + "&oficina=" + selectoficina;
						$.ajax( {
							url : url ,
							async : false ,
							success : function(data) {
								if ( data == '0' ) {
									$( "#autocompleteasignado" ).val( asignadoa_anterior );
									$( "#autocompleteasignado" ).focus( );
									$( "#autocompleteasignado" ).addClass( "rojo" );
									mensaje( 'Error' , 'El usuario asignado <b>' + autocompleteasignado + '</b> no existe o no es del departamento. Por favor, vuelva a escribirlo o elija un nombre de la lista.' , 'alert.png' );
								} else {
									// COMPROBAMOS TAMBIEN SI YA EXISTEN EVALUACIONES, SE AVISE AL USUARIO
									// var asignadoa_nuevo = this.value;
									ActualizarAportes( );
									var url = "select/consultarEvaluacionesAsignadoTarea.php?asignadoa=" + encodeURIComponent( asignadoa_anterior ) + "&tarea=" + ID_SELECCIONADA;
									$.ajax( {
										url : url ,
										success : function(response) {
											if ( response != 0 ) {
												var dialogo = document.getElementById( 'dialog-confirm' );
												dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/user.png' width=\"30\" height=\"30\" />&nbsp;" + 'El usuario ' + asignadoa_anterior + ' tiene evaluaciones asociadas a esta tarea, si cambia el Asignado, a partir de ahora las nuevas evaluaciones se generarán al nuevo usuario ' + autocompleteasignado + '.<br><br>Si existen evaluaciones pendientes de valorar, estas deberán ser valorados por el responsable anterior.<br><br>Pulse Aceptar para realizar el cambio o pulse cancelar para volver al Asignado anterior.</p>';
												$( "#dialog-confirm" ).dialog( {
													resizable : false ,
													height : "auto" ,
													width : 500 ,
													modal : true ,
													title : 'Cambio de usuario asignado a tarea' ,
													buttons : {
														"Aceptar" : function() {
															$( this ).dialog( "close" );
														} ,
														"Cancelar" : function() {
															$( this ).dialog( "close" );
															$( "#autocompleteasignado" ).val( asignadoa_anterior );
														}
													}
												} );
											}
										}
									} );
								}
							}
						} );
					}
				} );
			}
			
			// AL CAMBIAR DE OFICINA SE TIENEN QUE BORRAR CIERTOS CAMPOS:
			// -USUARIO
			// -ASIGNADO
			// -ACTIVIDAD 
			// -SUBACTIVIDAD
			// -CRITICIDAD
			// -TAREA PADRE
			var oficina_anterior;
			if ( $( "#selectoficina" ).length) {
				$( "#selectoficina" ).on( 'focus' , function() {
					oficina_anterior = this.value;
				} ).change( function() {
					//TODO 
					var dialogo = document.getElementById( 'dialog-confirm' );
					dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<div style='width:100%;text-align:center'><img src='imagenes/user.png' width='70' height='70' /></div><br>Si cambia la tarea de departamento, se borrarán las evaluaciones generadas, los feedbacks enviados, planificaciones y se inicializarán las opciones por defecto de la tarea para el nuevo departamento. <br><br><span style='color:green'>Los fichajes no se verán afectados incluso si las personas que ficharon no pertenecen al nuevo departamento.<br><br>Los documentos adjuntos, observaciones, comentarios e histórico de correos de alta de tareas no se verán afectados.</span><br><br><span style='color:red'>La eliminación de evaluaciones, feedbacks y planificaciones no podrá deshacerse.</span><br><br>¿Desea continuar?</p>";
					$( "#dialog-confirm" ).dialog( {
						resizable : false ,
						height : "auto" ,
						width : 700 ,
						modal : true ,
						title : 'Cambiar departamento de tarea' ,
						buttons : {
							"Aceptar" : function() {
								var target = document.getElementById( 'micuerpo' );
								var spinner = new Spinner( opts ).spin( target );
								var url = "select/modificarOficinaTarea.php?tarea="+ID_SELECCIONADA+"&oficina="+$('#selectoficina').find(":selected").text()+"&usuario=" + encodeURIComponent( usuariovalidado );
								$.ajax( {
									url : url ,
									async : false ,
									success : function(data) {
										spinner.stop( );
										cambia_menu(3);
										//alert(oficina_anterior);
									}
								} );
								$( this ).dialog( "close" );
							} ,
							"Cancelar" : function() {
								$( "#selectoficina" ).val( oficina_anterior );
								$( this ).dialog( "close" );
								
							}
						}
					} );

				});
			}

			// VALIDAMOS QUE EL SOLICITADO EXISTA
			if ( $( "#autocompletesolicitado" ).length ) {
				$( "#autocompletesolicitado" ).on( 'focus' , function() {
				} ).change( function() {
					var autocompletesolicitado = $( "#autocompletesolicitado" ).val( );
					$( "#autocompletesolicitado" ).removeClass( "rojo" );
					if ( autocompletesolicitado != '' ) {
						var selectoficina = $( "#selectoficina" ).val( );
						var url = "select/comprobarExisteNombre.php?buscar=dominio&nombre=" + encodeURIComponent( autocompletesolicitado );
						$.ajax( {
							url : url ,
							async : false ,
							success : function(data) {
								if ( data == '0' ) {
									$( "#autocompletesolicitado" ).focus( );
									$( "#autocompletesolicitado" ).addClass( "rojo" );
									mensaje( 'Aviso' , 'El usuario <b>' + autocompletesolicitado + '</b> no existe en el sistema. Aunque usted puede continuar con este nombre, si la persona existe y no ha escrito bien el nombre, esta no podrá entrar a consultar su incidencia.' , 'user.png' );
								}
							}
						} );
					}
				} );
			}

			// VALIDAMOS QUE EL USUARIO EXISTA Y ESTÉ EN LA MISMA OFICINA QUE LA TAREA
			var usuario_anterior;
			if ( $( "#autocompleteusuario" ).length && $( "#selectoficina" ).length ) {
				$( "#autocompleteusuario" ).on( 'focus' , function() {
					usuario_anterior = this.value;
				} ).change( function() {
					var autocompleteusuario = $( "#autocompleteusuario" ).val( );
					$( "#autocompleteusuario" ).removeClass( "rojo" );
					if ( autocompleteusuario != '' ) {
						var selectoficina = $( "#selectoficina" ).val( );
						var url = "select/comprobarExisteNombre.php?buscar=usuarios&nombre=" + encodeURIComponent( autocompleteusuario ) + "&oficina=" + selectoficina;
						$.ajax( {
							url : url ,
							async : false ,
							success : function(data) {
								if ( data == '0' ) {
									$( "#autocompleteusuario" ).val( usuario_anterior );
									$( "#autocompleteusuario" ).focus( );
									$( "#autocompleteusuario" ).addClass( "rojo" );
									mensaje( 'Error' , 'El usuario <b>' + autocompleteusuario + '</b> no existe o no es del departamento. Por favor, vuelva a escribirlo o elija un nombre de la lista.' , 'alert.png' );
								}
							}
						} );
					}
				} );
			}

			// VALIDAMOS QUE EL EVALUADOR EXISTA
			var evaluador_anterior;
			if ( $( "#autocompleteevaluador" ).length ) {
				$( "#autocompleteevaluador" ).on( 'focus' , function() {
					evaluador_anterior = this.value;
				} ).change( function() {
					var autocompleevaluador = $( "#autocompleteevaluador" ).val( );
					$( "#autocompleteevaluador" ).removeClass( "rojo" );
					if ( autocompleevaluador != '' ) {

						var url = "select/comprobarExisteNombre.php?buscar=dominio&nombre=" + encodeURIComponent( autocompleevaluador );
						$.ajax( {
							url : url ,
							async : false ,
							success : function(data) {
								if ( data == '0' ) {
									$( "#autocompleteevaluador" ).val( evaluador_anterior );
									$( "#autocompleteevaluador" ).focus( );
									$( "#autocompleteevaluador" ).addClass( "rojo" );
									mensaje( 'Error' , 'El usuario <b>' + autocompleevaluador + '</b> no existe. Por favor, vuelva a escribirlo o elija un nombre de la lista.' , 'alert.png' );
								}
							}
						} );
					}
				} );
			}

			
			// COMPROBAMOS QUE SOLO EL EVALUADOR PUEDA CAMBIAR EL APORTE JEFE
			if ( $( "#selectaportejefe" ).length ) {
				var selectaportejefe_anterior;
				$( "#selectaportejefe" ).on( 'focus' , function() {
					selectaportejefe_anterior = this.value;
				} ).change( function() {
					if ( evaluador != 1 ) {
						mensaje( 'Error de permisos' , 'Solo un evaluador puede cambiar este campo.' , 'alert.png' );
						// this.value = selectaportejefe_anterior;
						$( "#selectaportejefe" ).val( selectaportejefe_anterior );
					}
				} );
			}

			// GESTION DE HABILITAR / DESHABILITAR APORTES
			/*
			 * function habilitar_aportes() { if ( $.isNumeric( $( "#selectaporte" ).val( ) ) && $.isNumeric( $( "#selectaporteempresa" ).val( ) ) ) { // Si los dos son numericos por que ya estaban informados de antes, que no se inhabiliten } else { // SI PONEMOS VALOR EN EL CAMPO APORTE SE DESHABILITA EL APORTEEMPRESA if ( $.isNumeric( $( "#selectaporte" ).val( ) ) ) { $( '#selectaporteempresa option:not(:selected)' ).prop( 'disabled' , true ); $( "#selectaporteempresa" ).addClass( "deshabilitado" ); } else { $( '#selectaporteempresa option:not(:selected)' ).prop( 'disabled' , false ); $( "#selectaporteempresa" ).removeClass( "deshabilitado" ); }
			 *  // SI PONEMOS VALOR EN EL CAMPO APORTEEMPRESA SE DESHABILITA EL APORTE if ( $.isNumeric( $( "#selectaporteempresa" ).val( ) ) ) { $( '#selectaporte option:not(:selected)' ).prop( 'disabled' , true ); $( "#selectaporte" ).addClass( "deshabilitado" ); } else { $( '#selectaporte option:not(:selected)' ).prop( 'disabled' , false ); $( "#selectaporte" ).removeClass( "deshabilitado" ); } } }
			 */
			if ( $( "#selectaporte" ).length && $( '#selectaporteempresa' ).length ) {
				// EJECUTAMOS LA FUNCIÓN AL CARGAR LA TAREA
				// habilitar_aportes( );
				// EJECUTAMOS LA FUNCIÓN AL CAMBIAR APORTE
				$( "#selectaporte" ).change( function() {
					// habilitar_aportes( );
					$( "#selectaporteempresa" ).val( '-' );
				} );
				// EJECUTAMOS LA FUNCIÓN AL CAMBIAR APORTEEMEPRESA
				$( "#selectaporteempresa" ).change( function() {
					// habilitar_aportes( );
					$( "#selectaporte" ).val( '-' );
				} );
			}

			// ACTUALIZAMOS EL APORTE PROPIO AL CAMBIAR DE ACTIVIDAD
			if ( $( "#selectactividad" ).length ) {
				$( "#selectactividad" ).change( function() {
					ActualizarAportes( );
				} );
			}

			function ActualizarAportes() {
				if ( $( "#selectactividad" ).length && $( "#autocompleteasignado" ).length && $( "#selectoficina" ).length ) {
					var selectactividad = $( "#selectactividad" ).val( );
					var autocompleteasignado = $( "#autocompleteasignado" ).val( );
					var selectoficina = $( "#selectoficina" ).val( );
					if ( selectactividad != '-' ) {
						var target = document.getElementById( 'micuerpo' );
						var spinner = new Spinner( opts ).spin( target );
						var url = "select/getAporteActividadUsuario.php?selectactividad=" + encodeURIComponent( selectactividad ) + "&Id_Tarea=" + ID_SELECCIONADA + "&autocompleteasignado=" + encodeURIComponent( autocompleteasignado ) + "&selectoficina=" + encodeURIComponent( selectoficina );
						$.ajax( {
							url : url ,
							async : false ,
							success : function(data) {
								spinner.stop( );
								if ( data != '' ) {
									data = data.split( ';' );
									$( "#selectaporte" ).val( data[ 0 ] );
									$( "#selectaporteempresa" ).val( data[ 1 ] );
									$( "#selectaportejefe" ).val( data[ 2 ] );
								} else {
									$( "#selectaporte" ).val( '-' );
									$( "#selectaporteempresa" ).val( '-' );
									$( "#selectaportejefe" ).val( '-' );
								}
							}
						} );
					}
				}
			}
			if ( $( '#descripcion' ).length ) {
				editor = new TINY.editor.edit( 'editor' , {
					id : 'descripcion' ,
					width : '100%' ,
					height : '98%' ,
					cssclass : 'tinyeditor' ,
					controlclass : 'tinyeditor-control' ,
					rowclass : 'tinyeditor-header' ,
					dividerclass : 'tinyeditor-divider' ,
					controls : [ 'bold' , 'italic' , 'underline' , 'strikethrough' , '|' , 'subscript' , 'superscript' , '|' , 'orderedlist' , 'unorderedlist' , '|' , 'outdent' , 'indent' , '|' , 'leftalign' , 'centeralign' , 'rightalign' , 'blockjustify' , '|' , 'unformat' , '|' , 'undo' , 'redo' , 'n' , 'font' , 'size' , 'style' , '|' , 'image' , 'hr' , 'link' , 'unlink' , '|' , 'print' ] ,
					footer : true ,
					fonts : [ 'Verdana' , 'Arial' , 'Georgia' , 'Trebuchet MS' ] ,
					xhtml : true ,
					cssfile : 'custom.css' ,
					bodyid : 'editor' ,
					footerclass : 'tinyeditor-footer' ,
					// toggle: {text: 'source', activetext: 'wysiwyg', cssclass: 'toggle'},
					resize : {
						cssclass : 'resize'
					}
				} );
			}
		
			if ( $( '#div_ficheros_adjuntos' ).length) {
				var url = 'files/server/php/';
				existeFileUpload = true;
				$( '#fileupload' ).fileupload( {
					url : url ,
					dataType : 'json' ,
					pasteZone: null, //para evitar trigger de fileupload por copy/paste
					fail : function(e , data) {
						var dialogo = document.getElementById( 'dialog-confirm' );
						dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/alert.png' width=\"30\" height=\"30\" />&nbsp;" + 'Se ha producido un error y no se puede subir el fichero. Recuerde que el máximo permitido es ' + maxfilesize_permitido + '</p>';
						$( "#dialog-confirm" ).dialog( {
							resizable : false ,
							height : 250 ,
							modal : true ,
							buttons : {
								"Ok" : function() {
									$( this ).dialog( "close" );
								}
							}
						} );
					} ,
					done : function(e , data) {
						$.each( data.result.files , function(index , file) {
							if(file.error !== undefined){
								str = "ERROR: \n" 
								+ "\nArchivo: " + file.name 
								+ "\nTipo: " + file.type 
								+ "\nTamaño: " + (file.size/1024).toFixed(2) +"KB"
								+ "\nUsuario: " + usuariovalidado
								+ "\nDescripcion del error: " + file.error;
								alert( str );
							}else{							
								$.post( "uploader/upload.php" , {
									file : file.name ,
									usuario : usuariovalidado ,
									id : ID_SELECCIONADA
								} , function(data) {
									if ( file.size > 0 ) {
										agregar_linea( data );
										refresh_tools( );
										guardar_historial( 49 );
										refrescarAdjuntos();
									} else {
										var dialogo = document.getElementById( 'dialog-confirm' );
										dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/alert.png' width=\"30\" height=\"30\" />&nbsp;" + 'No se puede subir el fichero. El máximo permitido es ' + maxfilesize_permitido + '</p>';
										$( "#dialog-confirm" ).dialog( {
											resizable : false ,
											height : 250 ,
											modal : true ,
											buttons : {
												"Ok" : function() {
													$( this ).dialog( "close" );
													var elem = data.split( "|" );
													var tarea = elem[ 0 ];
													var carpeta = elem[ 1 ];
													var fichero = elem[ 2 ];
													var id = elem[ 3 ];
													var url = "uploader/unupload_error.php?tarea=" + ID_SELECCIONADA + "&id=" + id;
													$.ajax( {
														url : url
													} ).done( function() {
													} );
												}
											}
										} );
	
									}
								} );
								
							}

						} );
					} ,
					progressall : function(e , data) {
						var progress = parseInt( data.loaded / data.total * 100 , 10 );
						$( '#progress .progress-bar' ).css( 'width' , progress + '%' );
					}
				} ).prop( 'disabled' , !$.support.fileInput ).parent( ).addClass( $.support.fileInput ? undefined : 'disabled' );
				
			}
			
			if ( $( '#grid_array_horas' ).length ) {
				ID_HORA_SELECCIONADA = '-2';
				ID_HORA_SELECCIONADA_ESTADO = '-2';
				CargaGridHoras( );
			}
			if ( $( '#grid_array_costes' ).length ) {
				ID_COSTE_SELECCIONADA = '-2';
				ID_COSTE_SELECCIONADA_ESTADO = '-2';
				CargaGridCostes( );
			}
			if ( $( '#grid_array_evaluaciones' ).length ) {
				ID_EVALUACION_SELECCIONADA = '-1';
				CargaGridEvaluaciones( );
			}
			if ( $( '#grid_array_comentarios' ).length ) {
				CargaGridComentarios( );
			}
			if ( $( '#porcentaje_visible' ).length ) {
				var porcentaje = document.getElementById( 'porcentaje' );
				var valordecimal = parseFloat( porcentaje.value ) * 100;
				var porcentaje_visible = document.getElementById( 'porcentaje_visible' );
				porcentaje_visible.innerHTML = valordecimal + "%";
				if ( $( '#slider' ).length ) {
					$( "#slider" ).slider( {
						min : 0 ,
						max : 100 ,
						value : valordecimal ,
						change : function(event , ui) {
							var porcentaje = document.getElementById( 'porcentaje' );
							porcentaje.value = ( ui.value / 100 );
							var porcentaje_visible = document.getElementById( 'porcentaje_visible' );
							porcentaje_visible.innerHTML = ui.value + "%";
						}
					} );
				}
			}
			if ( $( '#div_alta_usuarios_plegar' ).length ) {
				inicializar_fecha( 'fechaincorporacion' );
				$( "#div_alta_usuarios_plegar" ).animate( {
					height : "1px"
				} );
				$( "#autocompletereportaa" ).autocomplete( {
					type : 'post' ,
					source : function(request , response) {
						$.get( "select/getUsuarioautocomplete.php" , {
							buscar : request.term ,
							tipo : 1
						} , function(data) {
							tags = data.split( "|" );
							response( tags );
						} );
					} ,
					change : function(event , ui) {
						var miuser = ui.item.value;
						var url = "select/getDepartamentosautocomplete.php?usuario=" + encodeURIComponent( ui.item.value );
						$.ajax( {
							url : url ,
							success : function(data) {
								$( "#selectdepartamentoalta" ).html( data );
								var eluser = miuser;
								var eldepartamento = $( "#selectdepartamentoalta" ).val( );
								var url = "select/getProgramasautocomplete2.php?usuario=" + encodeURIComponent( eluser ) + "&departamento=" + encodeURIComponent( eldepartamento );
								$.ajax( {
									url : url ,
									success : function(data) {
										$( "#selectprogramaalta" ).html( data );
									}
								} );
							}
						} );
					}
				} );
				$( "#autocompletepermisoscomo" ).autocomplete( {
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
			if ( $( '#las_observaciones_plegar' ).length ) {
				$( "#las_observaciones_plegar" ).animate( {
					height : "1px"
				} );
			}
			if ( $( '#grid_array_horas_plegar' ).length ) {
				$( "#grid_array_horas_plegar" ).animate( {
					height : "1px"
				} );
			}
			if ( $( '#grid_array_costes_plegar' ).length ) {
				$( "#grid_array_costes_plegar" ).animate( {
					height : "1px"
				} );
			}
			if ( $( '#div_valorar_usuarios_plegar' ).length ) {
				$( "#div_valorar_usuarios_plegar" ).animate( {
					height : "1px"
				} );
			}
			if ( $( '#div_ficheros_adjuntos_plegar' ).length ) {
				$( "#div_ficheros_adjuntos_plegar" ).animate( {
					height : "1px"
				} );
			}
			if ( $( '#grid_array_comentarios_plegar' ).length ) {
				$( "#grid_array_comentarios_plegar" ).animate( {
					height : "1px"
				} );
			}
			if ( $( '#grid_array_evaluaciones_plegar' ).length ) {

				// $( "#grid_array_evaluaciones_plegar" ).hide();

				$( "#grid_array_evaluaciones_plegar" ).animate( {
					height : "1px"
				} );

			}
			if ( $( '#btn_enviar' ).length ) {
				$( "#btn_enviar" ).click( function() {
					guardar_la_tarea( 0 );
					return false;
				} );
				
				// Evitamos el doble click y doble guardado de tarea
				/*
				$( "#btn_enviar" ).on( 'click' , function(event) {
					event.preventDefault( );
					var botonEnviar = $( this );
					var botonSalir = $( "#btn_salir" );
					botonEnviar.prop( 'disabled' , true );
					botonSalir.prop( 'disabled' , true );
					setTimeout( function() {
						//botonEnviar.prop( 'disabled' , false );
						//botonSalir.prop( 'disabled' , false );
					} , 4000 );
				} );
				*/

			}
			
			if ( $( '#btn_salir' ).length ) {

				if ( 1 == 1 ) {
					$( "#btn_salir" ).click( function() {
						var dialogo = document.getElementById( 'dialog-confirm' );
						dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/info.png' width=\"30\" height=\"30\" />&nbsp;" + '¿Desea guardar los cambios realizados?</p>';
						$( "#dialog-confirm" ).dialog( {
							resizable : false ,
							height : 170 ,
							width : 400 ,
							modal : true ,
							position : [ 100 , 380 ] ,
							buttons : {
								"Guardar" : function() {
									$( this ).dialog( "close" );
									guardar_la_tarea( 1 );
								} ,
								"Salir" : function() {
									$( this ).dialog( "close" );
									cambia_menu( 1 );
									$( "#grid_array" ).pqGrid( "refreshDataAndView" );
								}
							}
						} );
						return false;
					} );
				}
			}
			if ( $( '#btn_cancelar' ).length ) {
				$( "#btn_cancelar" ).click( function() {
					var dialogo = document.getElementById( 'dialog-cancelar' );
					dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/papelera.png' width=\"30\" height=\"30\" />&nbsp;" + '<span style=\"color: red;\">¿Está seguro que desea borrar la tarea?</span></p>';
					$( "#dialog-cancelar" ).dialog( {
						resizable : false ,
						height : 170 ,
						width : 400 ,
						modal : true ,
						position : [ 100 , 380 ] ,
						buttons : {
							"Borrar" : function() {
								$( this ).dialog( "close" );
								var url = "select/borrar_datos_tarea.php?tarea=" + ID_SELECCIONADA;
								$.ajax( {
									type : "POST" ,
									url : url ,
									data : $( "#formulario" ).serialize( ) ,
									success : function(data) {
										$( "#respuesta" ).html( data );
										cambia_menu( 1 );
										$( "#grid_array" ).pqGrid( "refreshDataAndView" );
									}
								} );
								cambia_menu( 1 );
								$( "#grid_array" ).pqGrid( "refreshDataAndView" );
							} ,
							"Cancelar" : function() {
								$( this ).dialog( "close" );
							}
						}
					} );
					return false;
				} );
			}
			if ( $( '#pda' ).length ) {
				generar_tooltip_adjunto( 'pda' );
				$( "#tooltip2" ).draggable( );
			}
			if ( $( '#horasestimadas' ).length ) {
				// $("#horasestimadas").click(function(){ spb_calculadora_crear(this.id); });
				$( "#horasestimadas" ).keydown( function(e) {
					var key = e.which;
					if ( key != 8 && key != 9 && key != 37 && key != 38 && key != 39 && key != 40 && key != 46 && key != 110 && key != 190 && key != 13 ) {
						if ( key < 48 ) {
							e.preventDefault( );
						} else if ( key > 57 && key < 96 ) {
							e.preventDefault( );
						} else if ( key > 105 ) {
							e.preventDefault( );
						}
					}
				} );
			}
			if ( $( '#ahorro' ).length ) {
				$( "#ahorro" ).keydown( function(e) {
					var key = e.which;
					if ( key != 8 && key != 9 && key != 37 && key != 38 && key != 39 && key != 40 && key != 46 && key != 110 && key != 190 && key != 13 ) {
						if ( key < 48 ) {
							e.preventDefault( );
						} else if ( key > 57 && key < 96 ) {
							e.preventDefault( );
						} else if ( key > 105 ) {
							e.preventDefault( );
						}
					}
				} );
			}
			if ( $( '#fechaalta' ).length ) {
				var spb_edit = $( "#fechaalta" ).attr( 'readonly' );
				if ( spb_edit != 'readonly' ) {
					jQuery('#fechaalta').datetimepicker({
						datepicker: true,
						timepicker: false,
						format: 'd-m-Y',
						weeks:true,
						dayOfWeekStart: 1,
					});
					/*new JsDatePick( {
						useMode : 2 ,
						target : "fechaalta" ,
						dateFormat : "%d-%m-%Y"
					} );*/
				}
			}
			if ( $( '#fechasolicitud' ).length ) {
				var spb_edit = $( "#fechasolicitud" ).attr( 'readonly' );
				if ( spb_edit != 'readonly' ) {
					jQuery('#fechasolicitud').datetimepicker({
						datepicker: true,
						timepicker: false,
						format: 'd-m-Y',
						weeks:true,
						dayOfWeekStart: 1,
					});
					/*new JsDatePick( {
						useMode : 2 ,
						target : "fechasolicitud" ,
						dateFormat : "%d-%m-%Y"
					} );*/
				}
			}
			if ( $( '#fechaobjetivo' ).length ) {
				var spb_edit = $( "#fechaobjetivo" ).attr( 'readonly' );
				if ( spb_edit != 'readonly' ) {					
					jQuery('#fechaobjetivo').datetimepicker({
						datepicker: true,
						timepicker: false,
						format: 'd-m-Y',
						weeks:true,
						dayOfWeekStart: 1,
						minDate: 0
					});
					/*new JsDatePick( {
						useMode : 2 ,
						target : "fechaobjetivo" ,
						dateFormat : "%d-%m-%Y"
					} );*/
				}
			}
			if ( $( '#fechanecesidad' ).length ) {
				var spb_edit = $( "#fechanecesidad" ).attr( 'readonly' );
				if ( spb_edit != 'readonly' ) {
					jQuery('#fechanecesidad').datetimepicker({
						datepicker: true,
						timepicker: false,
						format: 'd-m-Y',
						weeks:true,
						dayOfWeekStart: 1,
						minDate:0
					});
					/*new JsDatePick( {
						useMode : 2 ,
						target : "fechanecesidad" ,
						dateFormat : "%d-%m-%Y" ,
						beforeShow : function() {
							setTimeout( function() {
								$( '.ui-datepicker' ).css( 'z-index' , 99999999999999 );
							} , 0 );
						}
					} );*/
				}
			}

			if ( $( '#autocomplete' ).length ) {
				$( "#autocomplete" ).autocomplete( {
					type : 'post' ,
					source : function(request , response) {
						$.get( "select/getTareaautocomplete.php?tarea="+ID_SELECCIONADA , {
							buscar : request.term
						} , function(data) {
							tags = data.split( "|" );
							response( tags );
						} );
					}
				} );
			}

			if ( $( '#autocompletesolicitado' ).length ) {
				$( "#autocompletesolicitado" ).autocomplete( {
					type : 'post' ,
					source : function(request , response) {
						$.get( "select/getUsuarioautocomplete.php" , {
							buscar : request.term ,
							tipo : 1
						} , function(data) {
							tags = data.split( "|" );
							response( tags );
						} );
					} ,
					change : function(event , ui) {
						var miuser = ui.item.value;
						var url = "select/getDepartamentosautocomplete.php?usuario=" + encodeURIComponent( ui.item.value );
						$.ajax( {
							url : url ,
							success : function(data) {
								$( "#selectdepartamento" ).html( data );
								var eluser = miuser;
								var eldepartamento = $( "#selectdepartamento" ).val( );
								var url = "select/getProgramasautocomplete2.php?usuario=" + encodeURIComponent( eluser ) + "&departamento=" + encodeURIComponent( eldepartamento );
								$.ajax( {
									url : url ,
									success : function(data) {
										$( "#selectprograma" ).html( data );
									}
								} );
							}
						} );
					}
				} );
			}

			if ( $( '#autocompleteasignado' ).length ) {
				$( "#autocompleteasignado" ).autocomplete( {
					type : 'post' ,
					source : function(request , response) {
						$.get( "select/getUsuarioAutocompleteOficina.php" , {
							buscar : request.term ,
							tipo : 1 ,
							oficinas : oficinas ,
							oficinaTarea : $('#selectoficina').find(":selected").text(),
							externo : externo
						} , function(data) {
							tags = data.split( "|" );
							response( tags );
						} );
					}
				} );
			}
			if ( $( '#autocompleteusuario' ).length ) {
				$( "#autocompleteusuario" ).autocomplete( {
					type : 'post' ,
					source : function(request , response) {
						$.get( "select/getUsuarioAutocompleteOficina.php" , {
							buscar : request.term ,
							tipo : 1 ,
							oficinas : oficinas ,
							oficinaTarea : $('#selectoficina').find(":selected").text(),
							externo : externo
						} , function(data) {
							tags = data.split( "|" );
							response( tags );
						} );
					}
				} );
			}
			if ( $( '#autocompleteevaluador' ).length ) {
				$( "#autocompleteevaluador" ).autocomplete( {
					type : 'post' ,
					source : function(request , response) {
						$.get( "select/getUsuarioautocomplete.php" , {
							buscar : request.term ,
							tipo : 1 ,
							oficinas : oficinas ,
							externo : externo
						} , function(data) {
							tags = data.split( "|" );
							response( tags );
						} );
					}
				} );
			}
			if ( $( '#autocompletedestinatario' ).length ) {
				$( "#autocompletedestinatario" ).autocomplete( {
					type : 'post' ,
					source : function(request , response) {
						$.get( "select/getUsuarioautocomplete.php" , {
							buscar : request.term ,
							tipo : 1 ,
							oficinas : oficinas ,
							externo : externo
						} , function(data) {
							tags = data.split( "|" );
							response( tags );
						} );
					}
				} );
			}

			MenuContextualBarra( "#la_tarea" );
			MenuContextualFichero( ".ver_fichero" );
			refresh_tools( );

			// AVISO A LUIS PARA QUE GUARDE LA TAREA
			//if ( usuariovalidado == 'luis.barrachina' || usuariovalidado == 'alberto.ruiz' ) {
				$.ajax( {
					cache : false ,
					async : true ,
					method : "POST" ,
					url : "select/consulta_si_borrada.php" ,
					data : {
						tarea : ID_SELECCIONADA
					}
				} ).done( function(control) {
					if ( control == 1 ) {
						mensaje( 'Aviso' , 'Este portal no realiza ninguna función de autoguardado, recuerde que debe guardar esta tarea antes de salir.'  );
					}
				} );
			//}

			// COLABORADORES: SI ENTRO EN UNA OFICINA QUE NO ES DE LAS MIAS, DEBO OCULTAR EL BOTON GUARDAR, DESABILITAR INPUTS, OCULTAR LISTA DE OFICINAS
			if ( !$( "#selectoficina" ).length ) {
				mensaje( 'Error' , 'FALTA EL CAMPO DE OFICINA, CONSULTAR CON EL ADMINISTRADOR DEL PORTAL' );
			} else {
				// TENGO QUE OBTENER DEL SERVIDOR LA OFICINA DE LA TAREA

				$.ajax( {
					cache : false ,
					async : true ,
					method : "POST" ,
					url : "select/getValorCampoTabla.php" ,
					data : {
						Id : ID_SELECCIONADA ,
						Tabla : 'Tareas y Proyectos' ,
						Campo : 'Oficina'
					}
				} ).done( function(valor) {
					var oficinasusuario_array = oficinausuariovalidado.split( ',' );
					if ( jQuery.inArray( valor , oficinasusuario_array ) !== -1 ) {
						// NO PASA NADA POR QUE ES MI OFICINA
					} else {
						// ESTOY ENTRANDO EN UNA TAREA QUE NO ES DE MI OFICINA
						// Sobreescribo el valor de mis oficinas, por el de la oficina de la tarea
						var selectoficinatarea = $( "#selectoficina" );
						selectoficinatarea.empty( ); // borro los options
						selectoficinatarea.append( $( "<option>" + valor + "</option>" ) ); // Sobreescribo con el valor

						// Elimino el botón de guardar, para evitar cambios en la tarea
						$( "#btn_enviar" ).prop( "disabled" , true );
						$( "#btn_salir" ).hide( );

						// Oculto div de evaluaciones
						$( "#grid_array_evaluaciones" ).hide( );
						$( "#div_valorar_usuarios" ).hide( );
						$( "#div_alta_usuarios" ).hide( );

						mensaje( 'ATENCIÓN: Aviso sobre esta tarea de ' + valor , 'Acaba de entrar en una tarea que no pertenece a ninguno de sus departamentos.<br><br>Como colobarador en esta tarea, su funcionalidad está limitada. Usted no puede modificar los datos de esta tarea, sin embargo si puede: <br><br>- Fichar tiempos en la tarea.<br><br>- Añadir comentarios.<br><br>- Añadir ficheros adjuntos.' );
					}
				} );

			}

		}
	}
	ajax.send( null );
}
function refresh_tools() {
	var url = "tools/consultaTools.php?tarea=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent( usuariovalidado ) + "&id_pantalla=" + ID_PANTALLA;
	$.ajax( {
		type : "JSON" ,
		url : url ,
		success : function(data) {
			var tools = JSON.parse( data );
			for ( var i = 0 ; i < tools.data.length ; i++ ) {
				if ( tools.data[ i ].DESCRIPCION == 'ARBOL' ) {
					if ( $( '#la_tarea_tool' ).length ) {
						if ( parseInt( tools.data[ i ].TOTAL ) > 0 ) {
							$( "#la_tarea_tool" ).html( '<img onClick="ver_arbol(' + ID_SELECCIONADA + ');"src="imagenes/tree.png" style="width: 15;"/>' );
						} else {
							$( "#la_tarea_tool" ).html( '' );
						}
					}
				}
				if ( tools.data[ i ].DESCRIPCION == 'ADJUNTO' ) {
					if ( $( '#div_ficheros_adjuntos_tools' ).length ) {
						if ( parseInt( tools.data[ i ].TOTAL ) > 0 ) {
							$( "#div_ficheros_adjuntos_tools" ).html( '<img src="imagenes/Clip-icon.png" style="width: 15;"/><span class="tool-mio">' + tools.data[ i ].CANTIDAD + '</span><span class="tool">/' + tools.data[ i ].TOTAL + '</span>' );
						} else {
							$( "#div_ficheros_adjuntos_tools" ).html( '' );
						}
					}
				}
				if ( tools.data[ i ].DESCRIPCION == 'HORAS' ) {
					if ( $( '#grid_array_horas_tools' ).length ) {
						if ( parseInt( tools.data[ i ].TOTAL ) > 0 ) {
							$( "#grid_array_horas_tools" ).html( '<img src="imagenes/horas.png" style="width: 15;"/><span class="tool-mio">' + tools.data[ i ].CANTIDAD + '</span><span class="tool">/' + tools.data[ i ].TOTAL + '</span>' );
						} else {
							$( "#grid_array_horas_tools" ).html( '' );
						}
					}
				}
				if ( tools.data[ i ].DESCRIPCION == 'COSTES' ) {
					if ( $( '#grid_array_costes_tools' ).length ) {
						if ( parseInt( tools.data[ i ].TOTAL ) > 0 ) {
							$( "#grid_array_costes_tools" ).html( '<img src="imagenes/dollar.png" style="width: 15;"/><span class="tool-mio">' + tools.data[ i ].CANTIDAD_TEXTO + '</span><span class="tool">/' + tools.data[ i ].TOTAL_TEXTO + '</span>' );
						} else {
							$( "#grid_array_costes_tools" ).html( '' );
						}
					}
				}
				if ( tools.data[ i ].DESCRIPCION == 'VALORACION' ) {
					if ( $( '#div_valorar_usuarios_tools' ).length ) {
						if ( parseInt( tools.data[ i ].TOTAL ) > 0 ) {
							$( "#div_valorar_usuarios_tools" ).html( '<img src="imagenes/Buzz-Star-icon.png" style="width: 15;"/><span class="tool-mio">' + tools.data[ i ].CANTIDAD + '</span><span class="tool">/' + tools.data[ i ].TOTAL + '</span>' );
						} else {
							$( "#div_valorar_usuarios_tools" ).html( '' );
						}
					}
				}
				if ( tools.data[ i ].DESCRIPCION == 'COMENTARIOS' ) {
					if ( $( '#grid_array_comentarios_tools' ).length ) {
						if ( parseInt( tools.data[ i ].TOTAL ) > 0 ) {
							$( "#grid_array_comentarios_tools" ).html( '<img src="imagenes/comentarios.png" style="width: 15;"/><span class="tool-mio">' + tools.data[ i ].CANTIDAD + '</span><span class="tool">/' + tools.data[ i ].TOTAL + '</span>' );
						} else {
							$( "#grid_array_comentarios_tools" ).html( '' );
						}
					}
				}
				if ( tools.data[ i ].DESCRIPCION == 'OBSERVACIONES' ) {
					if ( $( '#las_observaciones_tools' ).length ) {
						if ( parseInt( tools.data[ i ].TOTAL ) == 0 ) {
							$( "#las_observaciones_tools" ).html( '<img src="imagenes/eye.png" style="width: 15;"/><span class="tool"></span>' );
						} else {
							$( "#las_observaciones_tools" ).html( '' );
						}
					}
				}
				if ( tools.data[ i ].DESCRIPCION == 'TIEMPO' ) {
					if ( $( '#grid_array_horas_tools' ).length ) {
						if ( ( tools.data[ i ].TOTAL_TEXTO ) != "0:0:0" ) {
							var MI_TIEMPO = '0';
							if ( tools.data[ i ].CANTIDAD_TEXTO != "0:0:0" ) {
								MI_TIEMPO = tools.data[ i ].CANTIDAD_TEXTO;
							}

							$( "#grid_array_horas_tools" ).html( '<img src="imagenes/clock2.png" style="height:12;width: 12;"/><span class="tool-mio">' + MI_TIEMPO + '</span><span class="tool">/' + tools.data[ i ].TOTAL_TEXTO + '</span>' + $( "#grid_array_horas_tools" ).html( ) );
						}
					}
				}
			}
		}
	} );
	
}
function MenuContextualFichero(destino) {
	if ( ID_PANTALLA == 1 ) {
		lista_menu = [ {
			title : "Ver" ,
			cmd : "ver" ,
			uiIcon : "ui-icon-circle-zoomin"
		} , {
			title : "Cambiar Visibilidad" ,
			cmd : "publicar" ,
			uiIcon : "ui-icon-transferthick-e-w"
		} , {
			title : "Borrar" ,
			cmd : "borrar" ,
			uiIcon : "ui-icon-scissors"
		} , {
			title : "Es Genérica" ,
			cmd : "generica" ,
			uiIcon : "ui-icon-circle-arrow-e"
		} , {
			title : "Es Documentacion" ,
			cmd : "documentacion" ,
			uiIcon : "ui-icon-circle-arrow-e"
		} , {
			title : "Es Documentacion Técnica" ,
			cmd : "documentaciontecnica" ,
			uiIcon : "ui-icon-circle-arrow-e"
		} , {
			title : "Es Mensaje" ,
			cmd : "mensaje" ,
			uiIcon : "ui-icon-circle-arrow-e"
		} ];
	} else {
		lista_menu = [ {
			title : "Ver" ,
			cmd : "ver" ,
			uiIcon : "ui-icon-circle-zoomin"
		} , {
			title : "Borrar" ,
			cmd : "borrar" ,
			uiIcon : "ui-icon-scissors"
		} , {
			title : "Es Genérica" ,
			cmd : "generica" ,
			uiIcon : "ui-icon-circle-arrow-e"
		} , {
			title : "Es Documentacion" ,
			cmd : "documentacion" ,
			uiIcon : "ui-icon-circle-arrow-e"
		} , {
			title : "Es Mensaje" ,
			cmd : "mensaje" ,
			uiIcon : "ui-icon-circle-arrow-e"
		} ]
	}

	$( destino ).contextmenu( {
		// Alberto 10/07/2017, añadir 1 al z-index del parent
		beforeOpen : function(event , ui) {
			var $menu = ui.menu , $target = ui.target;

			ui.menu.zIndex( $( event.target ).zIndex( ) + 1 );
		} ,
		menu : lista_menu ,
		select : function(event , ui) {
			var $target = ui.target;
			if ( ui.cmd == "ver" ) {
				var target_id = $target.attr( 'id' );
				var list_id = target_id.split( "_" );
				miPopup = window.open( "select/archivo.php?id=" + list_id[ 1 ] );
				miPopup.focus( );
			}
			if ( ui.cmd == "borrar" ) {
				var t = document.getElementById( $target.attr( 'id' ) );
				var td = t.parentNode;
				var tr = td.parentNode;
				var table = tr.parentNode;
				table.removeChild( tr );
				var target_id = $target.attr( 'id' );
				var list_id = target_id.split( "_" );
				var id = list_id[ 1 ];
				var ajax = nuevoAjax( );
				ajax.open( "POST" , "uploader/unupload.php?id=" + id + "&usuario=" + encodeURIComponent( usuariovalidado ) , false );
				ajax.onreadystatechange = function() {
					if ( ajax.readyState != 4 ) {
						// NO ESTA LISTO!!!!!!!!!!
					} else {
						lista = ( ajax.responseText );
					}
				}
				ajax.send( null );
			}
			if ( ui.cmd == "publicar" ) {
				var target_id = $target.attr( 'id' );
				var list_id = target_id.split( "_" );
				var id = 'IMAGEN_FICHERO_' + list_id[ 1 ];
				var tipo = "";
				if ( $( "#" + id ).attr( "src" ) == './imagenes/transparent.png' ) {
					$( "#" + id ).attr( "src" , "./imagenes/private.png" );
					tipo = "0";
				} else {
					if ( $( "#" + id ).attr( "src" ) == './imagenes/private.png' ) {
						$( "#" + id ).attr( "src" , "./imagenes/share.png" );
						tipo = "2";
					} else {
						if ( $( "#" + id ).attr( "src" ) == './imagenes/share.png' ) {
							$( "#" + id ).attr( "src" , "./imagenes/transparent.png" );
							tipo = "1";
						}
					}
				}
				var url = "select/modificarvisibilidadadjunto.php?Id=" + list_id[ 1 ] + "&tipo=" + tipo + "&usuario=" + encodeURIComponent( usuariovalidado ) + "&tarea=" + ID_SELECCIONADA
				$.ajax( {
					url : url
				} ).done( function() {
				} );
			}
			if ( ui.cmd == "generica" ) {

				var target_id = $target.attr( 'id' );
				var list_id = target_id.split( "_" );
				var id = 'IMAGEN_FICHERO_TIPO_' + list_id[ 1 ];
				$( "#" + id ).attr( "src" , "./imagenes/0.png" );
				var url = "select/modificartipoadjunto.php?Id=" + list_id[ 1 ] + "&tipo=0&usuario=" + encodeURIComponent( usuariovalidado ) + "&tarea=" + ID_SELECCIONADA;
				$.ajax( {
					url : url
				} ).done( function() {
				} );
			}
			if ( ui.cmd == "documentacion" ) {
				var target_id = $target.attr( 'id' );
				var list_id = target_id.split( "_" );
				var id = 'IMAGEN_FICHERO_TIPO_' + list_id[ 1 ];
				$( "#" + id ).attr( "src" , "./imagenes/1.png" );
				var url = "select/modificartipoadjunto.php?Id=" + list_id[ 1 ] + "&tipo=1&usuario=" + encodeURIComponent( usuariovalidado ) + "&tarea=" + ID_SELECCIONADA;
				$.ajax( {
					url : url
				} ).done( function() {
				} );
			}
			if ( ui.cmd == "documentaciontecnica" ) {
				var target_id = $target.attr( 'id' );
				var list_id = target_id.split( "_" );
				var id = 'IMAGEN_FICHERO_TIPO_' + list_id[ 1 ];
				$( "#" + id ).attr( "src" , "./imagenes/2.png" );
				var url = "select/modificartipoadjunto.php?Id=" + list_id[ 1 ] + "&tipo=2&usuario=" + encodeURIComponent( usuariovalidado ) + "&tarea=" + ID_SELECCIONADA;
				$.ajax( {
					url : url
				} ).done( function() {
				} );
			}
			if ( ui.cmd == "mensaje" ) {
				var target_id = $target.attr( 'id' );
				var list_id = target_id.split( "_" );
				var id = 'IMAGEN_FICHERO_TIPO_' + list_id[ 1 ];
				$( "#" + id ).attr( "src" , "./imagenes/3.png" );
				var url = "select/modificartipoadjunto.php?Id=" + list_id[ 1 ] + "&tipo=3&usuario=" + encodeURIComponent( usuariovalidado ) + "&tarea=" + ID_SELECCIONADA;
				$.ajax( {
					url : url
				} ).done( function() {
				} );
			}

		}

	} );
}
function MenuContextualBarra(destino) {
	if ( ID_PANTALLA == 1 ) {
		lista_menu = [ {
			title : "Fusionar" ,
			cmd : "fusionar" ,
			uiIcon : "ui-icon-arrow-4-diag"
		} , {
			title : "Nueva Hija" ,
			cmd : "hija" ,
			uiIcon : "ui-icon-plusthick"
		} , {
			title : "Marcarla" ,
			cmd : "marcar" ,
			uiIcon : "ui-icon-flag"
		} , {
			title : "Borrar" ,
			cmd : "borrar" ,
			uiIcon : "ui-icon-scissors"
		} , {
			title : "Valoracion" ,
			cmd : "valoracion" ,
			uiIcon : "ui-icon-mail-closed"
		} , {
			title : "Invitar" ,
			cmd : "invitar" ,
			uiIcon : "ui-icon-circle-plus"
		} , {
			title : "Historial" ,
			cmd : "historial" ,
			uiIcon : "ui-icon-note"
		} ];
	} else {
		lista_menu = [ {
			title : "Marcarla" ,
			cmd : "marcar" ,
			uiIcon : "ui-icon-flag"
		} , {
			title : "Historial" ,
			cmd : "historial" ,
			uiIcon : "ui-icon-note"
		} ];
	}

	$( destino ).contextmenu( {
		menu : lista_menu ,
		select : function(event , ui) {
			var $target = ui.target;
			if ( ui.cmd == "fusionar" ) {
				var target_id = $target.html( );
				var dialogo = document.getElementById( 'dialog-cancelar' );
				dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/expand.png' width=\"30\" height=\"30\" />&nbsp;" + 'Seleccione una tarea a la que fusionarse.<div><input id="autocompletetareapopup" name="autocompletetareapopup" style="width:300px;" value=""></div>';
				$( "#dialog-cancelar" ).dialog( {
					resizable : false ,
					height : 250 ,
					width : 500 ,
					modal : true ,
					open : function(event , ui) {
						$( "#autocompletetareapopup" ).autocomplete( {
							type : 'post' ,
							source : function(request , response) {
								$.get( "select/getTareaautocomplete.php" , {
									buscar : request.term
								} , function(data) {
									tags = data.split( "|" );
									response( tags );
								} );
							}
						} );
					} ,
					buttons : {
						"Fusionar" : function() {
							$( this ).dialog( "close" );
							$( "#autocompletetareapopup" ).submit( );
							var seleccion_texto = $( "#autocompletetareapopup" ).val( );
							var list_id = seleccion_texto.split( "-" );
							var tarea_destino = list_id[ 0 ];
							var url = "select/fusionar_tareas.php?tarea_origen=" + ID_SELECCIONADA + "&tarea_destino=" + tarea_destino + "&usuario=" + encodeURIComponent( usuariovalidado ); // El script a dónde se realizará la petición.
							$.ajax( {
								type : "GET" ,
								url : url ,
								success : function(data) {
									selecciono_tarea_historial( tarea_destino , 1 );
								}
							} );
						} ,
						"Cancelar" : function() {
							$( this ).dialog( "close" );
						}
					}
				} );
			}
			if ( ui.cmd == "hija" ) {
				var target_id = $target.html( );
				var url = "select/hijas_tareas.php?tarea_origen=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent( usuariovalidado ); // El script a dónde se realizará la petición.
				$.ajax( {
					type : "GET" ,
					url : url ,
					success : function(data) {
						selecciono_tarea_historial( data , 1 );
					}
				} );
			}

			if ( ui.cmd == "invitar" ) {
				var target_id = $target.html( );
				var dialogo = document.getElementById( 'dialog-cancelar' );
				dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/invitation.png' width=\"30\" height=\"30\" />&nbsp;" + 'Seleccione un usuario al que desee invitar.<div><input id="autocompleteusuariotiinvitar" name="autocompleteusuariotiinvitar" style="width:350px;" value=""></div>';
				$( "#dialog-cancelar" ).dialog( {
					resizable : false ,
					height : 250 ,
					width : 500 ,
					modal : true ,
					open : function(event , ui) {
						$( "#autocompleteusuariotiinvitar" ).autocomplete( {
							type : 'post' ,
							source : function(request , response) {
								$.get( "select/getUsuarioautocomplete.php" , {
									buscar : request.term ,
									tipo : 2
								} , function(data) {
									tags = data.split( "|" );
									response( tags );
								} );
							}
						} );
					} ,
					buttons : {
						"Invitar" : function() {
							$( this ).dialog( "close" );
							$( "#autocompleteusuariotiinvitar" ).submit( );
							var seleccion_texto = $( "#autocompleteusuariotiinvitar" ).val( );
							var list_id = seleccion_texto.split( " - (" );
							var tarea_destino = list_id[ 1 ];
							tarea_destino = tarea_destino.replace( ')' , '' );
							var url = "select/invitar_tarea.php?tarea=" + ID_SELECCIONADA + "&usuario_destino=" + tarea_destino + "&usuario_origen=" + encodeURIComponent( usuariovalidado );
							$.ajax( {
								type : "GET" ,
								url : url ,
								success : function(data) {
								}
							} );
							$( this ).dialog( "close" );
						} ,
						"Cancelar" : function() {
							$( this ).dialog( "close" );
						}
					}
				} );
			}
			if ( ui.cmd == "marcar" ) {
				var url = "select/MarcarTarea.php?id=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent( usuariovalidado );
				$.ajax( {
					url : url
				} ).done( function() {
				} );
			}
			if ( ui.cmd == "borrar" ) {
				var url = "select/ModificarCampoTarea.php?id=" + ID_SELECCIONADA + "&dato=1&usuario=" + encodeURIComponent( usuariovalidado ) + "&campo=Control";
				$.ajax( {
					url : url
				} ).done( function() {
					cambia_menu( 1 );
				} );
			}
			if ( ui.cmd == "valoracion" ) {
				var url = "valoracion/ControlValoracion.php?tarea=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent( usuariovalidado );
				$.ajax( {
					url : url ,
					success : function(data) {
						var res = data.split( "," );
						if ( res[ 0 ] == '0' ) {
							$( "#dialog-iniciar" ).html( '<div style="text-align: center;"><img style="width:20px;height:20px;" src="./imagenes/informe1.png">¿Desea enviar esta tarea para su valoracion?' + res[ 2 ] + '</div>' );
							$( "#dialog-iniciar" ).dialog( {
								resizable : false ,
								title : 'Mandar Correo de Valoración' ,
								width : 550 ,
								height : 400 ,
								modal : true ,
								open : function(event , ui) {
									$( "#dialog-iniciar" ).tooltip( {
										position : {
											my : "center bottom-20" ,
											at : "center top" ,
											using : function(position , feedback) {
												$( this ).css( position );
												$( "<div>" ).addClass( "arrow" ).addClass( feedback.vertical ).addClass( feedback.horizontal ).appendTo( this );
											}
										}

									} );
								} ,
								buttons : {
									"Ok" : function() {
										$( this ).dialog( "close" );
										var url = "mails/MailValoracion.php?tarea=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent( usuariovalidado );
										$.ajax( {
											url : url ,
											success : function(data) {
												if ( data != '1' ) {
													$( "#dialog-iniciar" ).html( data );
													$( "#dialog-iniciar" ).dialog( {
														resizable : false ,
														title : 'Error en el Envio' ,
														width : 300 ,
														height : 200 ,
														modal : true ,
														buttons : {
															"Ok" : function() {
																$( this ).dialog( "close" );
															}
														}
													} );
												}
											}
										} );
									} ,
									"Cancelar" : function() {
										$( this ).dialog( "close" );
									}
								}
							} );
						}
						if ( res[ 0 ] == '1' ) {
							$( "#dialog-iniciar" ).html( '<div style="text-align: center;"><img style="width:20px;height:20px;" src="./imagenes/informe1.png">Esta solicitud esta pendiente de ser valorada.' + res[ 2 ] + '</div>' );
							$( "#dialog-iniciar" ).dialog( {
								resizable : false ,
								title : 'Valoración pendiente' ,
								width : 550 ,
								height : 400 ,
								modal : true ,
								open : function(event , ui) {
									$( "#dialog-iniciar" ).tooltip( {
										position : {
											my : "center bottom-20" ,
											at : "center top" ,
											using : function(position , feedback) {
												$( this ).css( position );
												$( "<div>" ).addClass( "arrow" ).addClass( feedback.vertical ).addClass( feedback.horizontal ).appendTo( this );
											}
										}
									} );
								} ,
								buttons : {
									"Ok" : function() {
										$( this ).dialog( "close" );
									} ,
									"Borrar Solicitud" : function() {
										$( this ).dialog( "close" );
										var url = "valoracion/borrar_valoracion.php?envio=" + res[ 1 ];
										$.ajax( {
											url : url
										} );
									}
								}
							} );
						}
						if ( res[ 0 ] == '2' ) {
							var url = "valoracion/valoracion.php?envio=" + res[ 1 ];
							$.ajax( {
								url : url ,
								success : function(data) {
									$( "#dialog-iniciar" ).html( data );
									$( "#dialog-iniciar" ).dialog( {
										resizable : false ,
										title : 'Resultado de la Valoración' ,
										width : 400 ,
										height : 350 ,
										modal : true ,
										open : function(event , ui) {
											$( ".rating" ).jRating( {
												step : true ,
												rateMax : 5 ,
												length : 5 ,
												sendRequest : false ,
												canRateAgain : true ,
												isDisabled : true ,
												nbRates : 99999999 ,
												CustomLeyend : [ "Nada satisfecho" , "Poco satisfecho" , "Medianamente satisfecho" , "Bastante satisfecho" , "Muy satisfecho" ] ,
												onClick : function(element , rate) {
													var elementos_pulsados = element.id;
													var elemento_pulsado = elementos_pulsados.split( '_' );
													if ( elemento_pulsado[ 1 ] == '1' ) {
														nota_1 = elemento_pulsado[ 2 ];
														id_nota_1 = rate;
													}
													if ( elemento_pulsado[ 1 ] == '2' ) {
														nota_2 = elemento_pulsado[ 2 ];
														id_nota_2 = rate;
													}
													$( ".rating" ).jRating( {
														isDisabled : true
													} );
												} ,
												onSuccess : function() {
													alert( 'Success : your rate has been saved :)' );
												} ,
												onError : function() {
													alert( 'Error : please retry' );
												}
											} );
										} ,
										buttons : {
											"Ok" : function() {
												$( this ).dialog( "close" );
											}
										}
									} );
								}
							} );
						}
					}
				} );
			}
			if ( ui.cmd == "historial" ) {
				filas_historico = -1;
				fecha_historial = 1;
				crearslidehistorico( 0 );

			}
		}
	} );
}
function guardar_la_tarea(quehacerdespues) {
	var obligado = true;
	var els = $( "#formulario .campo_obligado" );
	for ( var i = 0, n = els.length ; i < n ; i++ ) {
		var el = els[ i ];
		var spb_submit = el.getAttribute( 'spb_submit' );
		var spb_campo = el.getAttribute( 'spb_campo' );
		if ( spb_submit == "1" ) {
			$( "#" + el.id ).submit( );
		}

		if ( obligado ) {// SI ENCUENTRO UNO YA NO HACE FALTA VOLVER A ENTRAR
			$( "#" + el.id ).removeClass( "rojo" );
			if ( $( "#" + el.id ).val( ).replace( " " , '' ) == "" ) {
				obligado = false;
				$( "#" + el.id ).focus( );
				$( "#" + el.id ).addClass( "rojo" );
				mensaje( 'Campo obligatorio' , 'El campo ' + spb_campo + ' es obligatorio' , 'alert.png' );
			}
		}
	}

	if ( $( '#descripcion' ).length ) {
		editor.post( );
	}
	if ( obligado == true ) {
		var els = $( "#formulario input" );
		for ( var i = 0, n = els.length ; i < n ; i++ ) {
			var el = els[ i ];
			var spb_submit = el.getAttribute( 'spb_submit' );
			if ( spb_submit == "1" ) {
				$( "#" + el.id ).submit( );
			}

		}
		//alert(obligado);
		obligado = validaciones( );
		//alert(obligado);
	}

	if ( $( '#selectactividad' ).length ) {
		if ( obligado == true ) {
			$( "#selectactividad" ).removeClass( "rojo" );
			var selectactividad = $( "#selectactividad" ).val( );
			if ( selectactividad == '-' ) {
				obligado = false;
				$( "#selectactividad" ).focus( );
				$( "#selectactividad" ).addClass( "rojo" );
				mensaje( 'Campo obligatorio' , 'El campo Actividad es obligatorio' , 'alert.png' );
			}
		}
	}

	//T#44326, Alberto 18/02/2021
	if ( $( '#fechaobjetivo' ).length  && $( "#selectoficina" ).length) {
		var selectoficina = $( "#selectoficina" ).val( );
		if ( selectoficina == 'TI' ) {
			if ( obligado == true ) {
				$( "#fechaobjetivo" ).removeClass( "rojo" );
				var inputFechaObjetivo = $( "#fechaobjetivo" ).val( );
				if ( inputFechaObjetivo == '') {
					obligado = false;
					$( "#fechaobjetivo" ).focus( );
					$( "#fechaobjetivo" ).addClass( "rojo" );
					mensaje( 'Campo obligatorio' , 'El campo fecha objetivo es obligatorio' , 'alert.png' );
				}				
			}	
		}
	}
	
	//T#28631 - JUAN ANTONIO ABELLAN - (07/02/2019) - (CCARRASCOSA)
	estado = $('#selectestado').val();
	oficina = $('#selectoficina').val();
	
	urls = "select/getEstadoObligado.php";
	$.ajax( {
		type : "POST" ,
		url :  urls,
		async : false,
		data:{
			estado : estado,
			oficina : oficina
		},
		success : function(data) {
			if(data == 1 ){ 
				//obligado = true; // 28/04/2020 comentado por Alberto, si anteriormente estaba a false por no cumplir algun control obligatorio, si lo pones true, permites que un campo obligatorio anterior pueda no estar informado
							
				if ( $( '#selectareasolicitante' ).length ) {
					if ( obligado == true ) {
						$( "#selectareasolicitante" ).removeClass( "rojo" );
						var selectareasolicitante = $( "#selectareasolicitante" ).val( );
						if ( selectareasolicitante == '-' ) {
							obligado = false;
							$( "#selectareasolicitante" ).focus( );
							$( "#selectareasolicitante" ).addClass( "rojo" );
							mensaje( 'Campo obligatorio' , 'El campo Area Solicitante es obligatorio' , 'alert.png' );
						}
					}
				}
				
				if ( $( '#selectplanta' ).length ) {
					if ( obligado == true ) {
						$( "#selectplanta" ).removeClass( "rojo" );
						var selectplanta = $( "#selectplanta" ).val( );
						if ( selectplanta == '-' ) {
							obligado = false;
							$( "#selectplanta" ).focus( );
							$( "#selectplanta" ).addClass( "rojo" );
							mensaje( 'Campo obligatorio' , 'El campo Area Planta es obligatorio.' , 'alert.png' );
						}
					}
				}
			}
		}
	});
	
	//Alberto 28/04/2020 T#37004 - NUEVO CAMPO EN TAREAS: AHORRO EN HORAS POR SEMANA DERIVADO DE LA TAREA SOLICITADA
	if ( $( '#ahorro' ).length && $( '#selectahorro' ).length && obligado) {
		$( "#selectahorro" ).removeClass( "rojo" );
		if(parseFloat( $( '#ahorro' ).val() ) != 'NaN' && parseFloat( $( '#ahorro' ).val() ) > 0 && $( '#selectahorro' ).val() == ''){
			obligado = false;
			$( "#selectahorro" ).focus( );
			$( "#selectahorro" ).addClass( "rojo" );
			mensaje( 'Campo obligatorio' , 'Si la tarea tiene ahorros has de indicar el tipo de ahorro.' , 'alert.png' );
		}
	}
	
	if ( obligado == true ) {
		var selecttipo = '';
		var selectestado = ''
		if ( $( '#selecttipo' ).length ) {
			selecttipo = $( "#selecttipo" ).val( );
		}
		if ( $( '#selectestado' ).length ) {
			selectestado = $( "#selectestado" ).val( );
		}
		
		/*var url = "select/valoracion_usuario.php?id=" + id_visualizacion + "&tarea=" + ID_SELECCIONADA + "&tipo=" + encodeURIComponent( selecttipo ) + "&estado=" + encodeURIComponent( selectestado ) + "&usuario=" + encodeURIComponent( usuariovalidado );
		$.ajax( {
			type : "GET" ,
			url : url ,
			success : function(data) {
				if ( data == '' ) {
					var alta_usuarios_sn = '0';
					if ( $( '#div_alta_usuarios' ).length ) {
						if ( $( '#crear_usuario' ).length ) {
							alta_usuarios_sn = '1';
						}
					}
			*/	
					// Evitamos el doble click y doble guardado de tarea
					if ( $( '#btn_enviar' ).length ) {
						var botonEnviar = $( "#btn_enviar" );
						var botonSalir = $( "#btn_salir" );
						botonEnviar.prop( 'disabled' , true );
						botonSalir.prop( 'disabled' , true );
					}

					//T#24860 28/02/2018
					var target = document.getElementById('micuerpo');
					var spinner = new Spinner(opts).spin(target);

					var url = "select/guarda_datos_tarea.php?post_get=1&tarea=" + ID_SELECCIONADA + "&id=" + id_visualizacion + "&usuario=" + encodeURIComponent( usuariovalidado ) + "&alta_usuarios_sn=1";
					$.ajax( {
						type : "POST" ,
						url : url ,
						data : $( "#formulario" ).serialize( ) ,
						success : function(data) {
							spinner.stop();
							if ( $( '#btn_enviar' ).length ) {
								botonEnviar.prop( 'disabled' , false );
								botonSalir.prop( 'disabled' , false );
							}
							if ( quehacerdespues == 0 ) {
								$( "#respuesta" ).html( data );
								refresh_tools( );
							} else {
								if ( data == '' ) {
									cambia_menu( 1 );
									$( "#grid_array" ).pqGrid( "refreshDataAndView" );
								} else {
									$( "#respuesta" ).html( data );
									refresh_tools( );
								}
							}
						}
					} );
				} //else {					mensaje( 'Aviso' , 'Es obligatorio ' + data , 'alert.png' );				}
			//}
		//} );
	//}
}


function refrescarAdjuntos() {
	$('#adjuntosdiv').empty();	
	
	var url = "select/obtener_Adjuntos.php?ext=" + externo + "&incidencia=" + ID_SELECCIONADA + '&usuario=' + encodeURIComponent( usuariovalidado ) + "&id=" + id_visualizacion;
	
	$.ajax( {
		type : "POST" ,
		url : url ,
		success: function(adjuntos){
			$(adjuntos)	.appendTo('#adjuntosdiv');
			
		}
	});
}
