//UTF Á Ñ
function CargaGridTarea(txtTitulo , txtGrid) {
	
	$.ajax( {
		type : 'GET' ,
		url : 'grid/control_grid.php?usuario=' + encodeURIComponent( usuariovalidado ) + '&titulo=' + encodeURIComponent( txtTitulo ) + '&grid=' + encodeURIComponent( txtGrid ) + '&tipo=3&rol=' + id_visualizacion ,
		success : function(msgcolumnas) {
			resultado_msgcolumnas = JSON.parse( msgcolumnas );
			$.ajax( {
				type : 'GET' ,
				url : 'grid/control_grid.php?usuario=' + encodeURIComponent( usuariovalidado ) + '&titulo=' + encodeURIComponent( txtTitulo ) + '&grid=' + encodeURIComponent( txtGrid ) + '&tipo=1' ,
				success : function(msg) {
					GridTareas( txtTitulo , txtGrid , msg );
				}
			} );
		}
	} );
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @param txtTitulo
 * @description nombre de la pagina (GESTIONTI, GESTIONLEAN, etc)
 * @param txtGrid
 * @description tipo de dato de ventana. ¿Siempre es 'grid_array'?
 * @param msg
 * @description JSON con nombre de columna, valor hidden y valor width
 */
function GridTareas(txtTitulo , txtGrid , msg) {
	mi_titulo = ObtenerTitulo( txtTitulo );
	listaestrategico = [ "Si" , "No" ];
	usuariosti = [ ];
	usuariosnoti = [ ];
	categorias = [ ];
	subcategorias = [ ];
	colas = [ ];
	departamentos = [ ];
	departamentos = [ ];
	tipos = [ ];
	prioridades = [ ];
	estadostarea = [ ];
	aportesOrg = [ ];
	oficinas = [];
	
	$.ajax( {
		url : "select/getOficinasUsuario.php?usuario=" + encodeURIComponent(usuariovalidado),
		success : function(response) {
			oficinas = response.split( "," );
		}
	} );
	
	$.ajax( {
		url : "select/obtener_usuarios_ti.php?id=3" ,
		success : function(response) {
			usuariosti = response.split( "," );
		}
	} );
	$.ajax( {
		url : "select/obtener_usuarios.php" ,
		success : function(response) {
			usuariosnoti = response.split( "," );
		}
	} );
	$.ajax( {
		url : "select/getCategorias.php" ,
		success : function(response) {
			categorias = response.split( "," );
		}
	} );
	$.ajax( {
		url : "select/getsubCategorias.php" ,
		success : function(response) {
			subcategorias = response.split( "," );
		}
	} );
	$.ajax( {
		url : "select/getColas.php" ,
		success : function(response) {
			colas = response.split( "," );
		}
	} );
	$.ajax( {
		url : "select/getDepartamentos.php" ,
		success : function(response) {
			departamentos = response.split( "," );
		}
	} );
	$.ajax( {
		url : "select/getDepartamentos.php" ,
		success : function(response) {
			departamentos = response.split( "," );
		}
	} );
	$.ajax( {
		url : "select/getTipos.php" ,
		success : function(response) {
			tipos = response.split( "," );
		}
	} );
	$.ajax( {
		url : "select/getPrioridades.php" ,
		success : function(response) {
			prioridades = response.split( "," );
		}
	} );
	$.ajax( {
		url : "select/getEstadosTarea.php" ,
		success : function(response) {
			estadostarea = response.split( "," );
		}
	} );
	$.ajax( {
		url : "select/getAportesOrg.php" ,
		success : function(response) {
			aportesOrg = response.split( "," );
		}
	} );
	
	$.ajax( {
		url : "select/getEntradaCorreo.php" ,
		dataType: "json",
		success : function(response) {
			$("span#prueba > span").empty();
			$("span#prueba > span").remove();
			if(response.length == 0){
				//NO HAY CORREOS//
				//$("span#prueba > span").empty();
				$('<span>('+response[0].Tareas+')</span>).appendTo($toolbar).button({ icons: { primary: "	ui-icon-mail-closed" } }).click(function(evt) { cambia_menu(22); });').appendTo('#prueba');
			}else{
				//HAY CORREOS//
				if(response[0].Importantes > 0){
					//Hay correos importantes//
					console.log('HAY IMPORTANTES: ' +response[0].Importantes );
					//$("span#prueba > span").empty();
					$('<span style="color: red;margin-left: 1px;font-weight: bolder;"> ('+response[0].Tareas+')</span>).appendTo($toolbar).button({ icons: { primary: "	ui-icon-mail-closed" } }).click(function(evt) { cambia_menu(22); });').appendTo('#prueba');
					
					indicadorimportantes = function(){
						$("span#prueba").fadeTo(500, .1)
	                    					  .fadeTo(500, 1);
						
					}
					setInterval(indicadorimportantes, 1000);
				}else{
					//No hay importantes//
					//$("span#prueba > span").empty();
					$('<span style="color: green;margin-left: 1px;font-weight: bolder;"> ('+response[0].Tareas+')</span>).appendTo($toolbar).button({ icons: { primary: "	ui-icon-mail-closed" } }).click(function(evt) { cambia_menu(22); });').appendTo('#prueba');
				}
			}
			//LeerCorreosBotAviso();
			setInterval(LeerCorreosBotAviso, 30000);
		}
	} );
	

	function LeerCorreosBotAviso(){
		$("span#prueba > span").empty();
		$("span#prueba > span").remove();
		$.ajax( {
			url : "select/getEntradaCorreo.php" ,
			dataType: "json",
			success : function(response) {
				if(response.length == 0){
					//NO HAY CORREOS//
					//$("span#prueba > span").empty();
					$('<span> ('+response[0].Tareas+')</span>).appendTo($toolbar).button({ icons: { primary: "	ui-icon-mail-closed" } }).click(function(evt) { cambia_menu(22); });').appendTo('#prueba');
				}else{
					//HAY CORREOS//
					if(response[0].Importantes > 0){
						//Hay correos importantes//
						console.log('HAY IMPORTANTES: ' +response[0].Importantes );
						//$("span#prueba > span").empty();
						$('<span style="color: red;margin-left: 1px;font-weight: bolder;"> ('+response[0].Tareas+') </span>).appendTo($toolbar).button({ icons: { primary: "	ui-icon-mail-closed" } }).click(function(evt) { cambia_menu(22); });').appendTo('#prueba');
						
						indicadorimportantes = function(){
							$("span#prueba").fadeTo(500, .1)
												.fadeTo(500, 1);
							
						}
						setInterval(indicadorimportantes, 1000);
					}else{
						//No hay importantes//						
						$('<span style="color: green;margin-left: 1px;font-weight: bolder;"> ('+response[0].Tareas+')</span>).appendTo($toolbar).button({ icons: { primary: "	ui-icon-mail-closed" } }).click(function(evt) { cambia_menu(22); });').appendTo('#prueba');
					}
				}
			}
		} );
	}

	autoCompleteEditor = function(ui) {
		var $cell = ui.$cell , rowData = ui.rowData , dataIndx = ui.dataIndx , width = ui.column.width , cls = ui.cls;
		var dc = $.trim( rowData[ dataIndx ] );
		var $inp = $( "<input type='text' name='" + dataIndx + "' class='" + cls + " pq-ac-editor' />" ).width( width - 6 ).appendTo( $cell ).val( dc );
		$inp.autocomplete( {
			source : usuariosnoti ,
			minLength : 0
		} ).focus( function() {
			$( this ).data( "autocomplete" ).search( $( this ).val( ) );
		} );
	}
	autoCompleteDepartamentoEditor = function(ui) {
		var $cell = ui.$cell , rowData = ui.rowData , dataIndx = ui.dataIndx , width = ui.column.width , cls = ui.cls;
		var dc = $.trim( rowData[ dataIndx ] );
		var $inp = $( "<input type='text' name='" + dataIndx + "' class='" + cls + " pq-ac-editor' />" ).width( width - 6 ).appendTo( $cell ).val( dc );
		$inp.autocomplete( {
			source : departamentos ,
			minLength : 0
		} ).focus( function() {
			$( this ).data( "autocomplete" ).search( $( this ).val( ) );
		} );
	}
	autoCompleteTareaProyecto = function(ui) {
		var $cell = ui.$cell , rowData = ui.rowData , dataIndx = ui.dataIndx , width = ui.column.width , cls = ui.cls;
		var dc = $.trim( rowData[ dataIndx ] );
		pdc = $.trim( rowData[ "Tarea / Proyecto" ] );
		var $inp = $( "<input type='text' name='" + dataIndx + "' class='" + cls + " pq-ac-editor' />" ).width( width - 6 ).appendTo( $cell ).val( dc );
		$inp.autocomplete( {
			type : 'post' ,
			source : function(request , response) {
				$.get( "select/getTareaautocomplete.php" , {
					buscar : request.term
				} , function(data) {
					tags = data.split( "|" );
					response( tags );
				} );
			} ,
			minLength : 0
		} ).focus( function() {
			$( this ).data( "autocomplete" ).search( $( this ).val( ) );
		} );
	}
	autoCompleteProgramaEditor = function(ui) {
		var $cell = ui.$cell , rowData = ui.rowData , dataIndx = ui.dataIndx , width = ui.column.width , cls = ui.cls;
		var dc = $.trim( rowData[ dataIndx ] );
		pdc = $.trim( rowData[ "Departamento Solicitud" ] );
		var $inp = $( "<input type='text' name='" + dataIndx + "' class='" + cls + " pq-ac-editor' />" ).width( width - 6 ).appendTo( $cell ).val( dc );
		$inp.autocomplete( {
			type : 'post' ,
			source : function(request , response) {
				$.get( "select/getProgramasautocomplete.php" , {
					buscar : request.term ,
					departamento : pdc
				} , function(data) {
					tags = data.split( "|" );
					response( tags );
				} );
			} ,
			minLength : 0
		} ).focus( function() {
			$( this ).data( "autocomplete" ).search( $( this ).val( ) );
		} );
	}
	dateEditor = function(ui) {
		var $cell = ui.$cell , rowData = ui.rowData , dataIndx = ui.dataIndx , cls = ui.cls , dc = $.trim( rowData[ dataIndx ] );
		$cell.css( 'padding' , '0' );
		var $inp = $( "<input style=\"z-index:9;\" type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' />" ).appendTo( $cell ).val( dc ).datepicker( {
			changeMonth : true ,
			changeYear : true ,
			dateFormat : "dd-mm-yy" ,
			numberOfMonths : 1 ,
			firstDay : 1 ,
			onClose : function() {
				$inp.focus( );
			} ,
			beforeShow : function() {
				setTimeout( function() {
					$( '.ui-datepicker' ).css( 'z-index' , 99999999999999 );
				} , 0 );
			}
		} );
	}


	var lista_columnas = ObtieneColumnas( txtGrid );
	var columnas = [ ];
	var j = 0;
	if ( msg != "" ) {
		resultadoglobal = JSON.parse( msg );
		resultado = resultadoglobal.colModel;
		if ( resultado.length > 0 ) {
			for ( i = 0 ; i < resultado.length ; i++ ) {
				for ( k = 0 ; k < lista_columnas.length ; k++ ) {
					if ( resultado[ i ][ 'columna' ] == lista_columnas[ k ].dataIndx ) {
						columnas[ j ] = lista_columnas[ k ];
						columnas[ j ].hidden = resultado[ i ][ 'hidden' ];
						columnas[ j ].width = resultado[ i ][ 'width' ];
						columnas[ j ].title = resultado[ i ][ 'title' ];
						j = j + 1;
					}
				}
			}
		} else {
			columnas = lista_columnas;
		}

		resultado = resultadoglobal.pageModel;
		if ( resultado.length > 0 ) {
			for ( i = 0 ; i < resultado.length ; i++ ) {
				if ( resultado[ i ][ 'columna' ] == 'Id' ) {
					rPP_Usuario = resultado[ i ][ 'valores' ];
				}
				if ( resultado[ i ][ 'columna' ] == 'Lista' ) {
					rPPOptions_Usuario = resultado[ i ][ 'valores' ].split( "," );
				}
			}
		} else {
			rPP_Usuario = 50;
			rPPOptions_Usuario = [  50, 100,1000 ];
		}
	} else {
		columnas = lista_columnas;
		rPP_Usuario = 50;
		rPPOptions_Usuario = [   50, 100,1000 ];
	}
	
	var obj = {
			width : '100%' ,
			height : '100%-2' ,
			// height : screen.height - 340 ,
			title : mi_titulo ,
			hoverMode : 'row' ,
			selectionModel : {
				type : 'row'
			} ,
			editModel : {
				saveKey : $.ui.keyCode.ENTER ,
				select : false ,
				keyUpDown : false ,
				cellBorderWidth : 0
			} ,
			pageModel : {
				type : 'remote' ,
				//rPP : 20 ,
				strRpp : "{0}",
				curPage : 1 ,
				rPP : rPP_Usuario ,
				rPPOptions : rPPOptions_Usuario ,
			} ,
			filterModel : {
				on : true ,
				mode : "AND" ,
				header : true
			} ,
			editor : {
				type : "textbox"
			},
	        scrollModel:{
	        	pace: 'fast', 
	        	//autoFit: true, 
	        	theme: true 
	        },
	        toggle: function( event, ui ) {
	        	gridMaximice(event, ui);
	        }
		};

	obj.colModel = columnas;

	obj.dataModel = {
		sorting : "remote" ,
		paging : "remote" ,
		sortIndx : [ "Id" ] ,
		sortDir : [ "down" ] ,
		location : "remote" ,
		dataType : "JSON" ,
		method : "GET" ,
		url : "select/consultaTareas.php" + ObtenerRestoFiltros( ) ,
		getData : function(dataJSON , textStatus , jqXHR) {
			var data = dataJSON.data;
			ID_K = -1;

			var pageModel = $( "#" + txtGrid ).pqGrid( "option" , "pageModel" );
			var pageModelrPP = pageModel.rPP;
			if ( rPP_Usuario != pageModelrPP ) {
				myJsonString = "Id=" + pageModel.rPP + "|Lista=" + pageModel.rPPOptions;
				$.ajax( {
					type : 'POST' ,
					url : 'grid/control_grid.php?tipo=2' ,
					data : {
						'myJsonString' : myJsonString ,
						'usuario' : usuariovalidado ,
						'grid' : txtGrid ,
						'tipomodel' : 'pageModel'
					} ,
					success : function(msg) {

					}
				} );
			}
			var rpp_actual = pageModel.rPPOptions;
			if ( rpp_actual.length > 6 ) {
				rpp_actual.pop( );
			}
			rpp_actual.push( dataJSON.totalRecords );
			return {
				curPage : dataJSON.curPage ,
				totalRecords : dataJSON.totalRecords ,
				data : dataJSON.data
			};
		}
	};

	obj.rowSelect = function(evt , obj) {
		ID_SELECCIONADA = obj.rowData[ "Id" ];
	};
	obj.cellSelect = function(evt , obj) {
		ID_SELECCIONADA = obj.rowData[ "Id" ];
	};
	obj.cellDblClick = function(evt , obj) {
		ID_SELECCIONADA = obj.rowData[ "Id" ];
		cambia_menu( 3 );
	};

	obj.render = function(evt , obj) {
		var $toolbar = $( "<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>" ).appendTo( $( ".pq-grid-top" , this ) );
		
		if(externo == "" || externo == 'LABORATORIO'){
			if ( GetDatosColumnas( "btn_Nueva" , "Hidden" ) == false ) {
				$( "<span>" + GetDatosColumnas( "btn_Nueva" , "Nombre" ) + "</span>" ).appendTo( $toolbar ).button( {
					icons : {
						primary : GetDatosColumnas( "btn_Nueva" , "jqueryui-icons" )
					}
				} ).click( function(evt) {
					if ( GetDatosColumnas( "btn_Nueva" , "Editar" ) == true ) {
						addRow( );
					} else {
					}
				} );
			}
		}
		
		if ( GetDatosColumnas( "btn_Nueva Alta" , "Hidden" ) == false ) {
			$( "<span>" + GetDatosColumnas( "btn_Nueva Alta" , "Nombre" ) + "</span>" ).appendTo( $toolbar ).button( {
				icons : {
					primary : GetDatosColumnas( "btn_Nueva Alta" , "jqueryui-icons" )
				}
			} ).click( function(evt) {
				if ( GetDatosColumnas( "btn_Nueva Alta" , "Editar" ) == true ) {
					addRowAlta( );
				}
			} );
		}
		if ( GetDatosColumnas( "btn_Modificar" , "Hidden" ) == false ) {
			$( "<span>" + GetDatosColumnas( "btn_Modificar" , "Nombre" ) + "</span>" ).appendTo( $toolbar ).button( {
				icons : {
					primary : GetDatosColumnas( "btn_Modificar" , "jqueryui-icons" )
				}
			} ).click( function(evt) {
				if ( GetDatosColumnas( "btn_Modificar" , "Editar" ) == true ) {
					editRow( );
				}
			} );
		}
		if ( GetDatosColumnas( "btn_Iniciar" , "Hidden" ) == false ) {
			$( "<span>" + GetDatosColumnas( "btn_Iniciar" , "Nombre" ) + "</span>" ).appendTo( $toolbar ).button( {
				icons : {
					primary : GetDatosColumnas( "btn_Iniciar" , "jqueryui-icons" )
				}
			} ).click( function(evt) {
				if ( GetDatosColumnas( "btn_Iniciar" , "Editar" ) == true ) {
					// ALBERTO: T#16695 14/12/2016 Reemplazar función initRow por InicarLaTarea que comprueba las -1
					// initRow( );
					IniciarLaTarea( );
					
					
				}
			} );
		}
		if ( GetDatosColumnas( "btn_Parar" , "Hidden" ) == false ) {
			$( "<span>" + GetDatosColumnas( "btn_Parar" , "Nombre" ) + "</span>" ).appendTo( $toolbar ).button( {
				icons : {
					primary : GetDatosColumnas( "btn_Parar" , "jqueryui-icons" )
				}
			} ).click( function(evt) {
				if ( GetDatosColumnas( "btn_Parar" , "Editar" ) == true ) {
					closeRow( );
				}
			} );
		}
		if ( GetDatosColumnas( "btn_Actual" , "Hidden" ) == false ) {
			$( "<span>" + GetDatosColumnas( "btn_Actual" , "Nombre" ) + "</span>" ).appendTo( $toolbar ).button( {
				icons : {
					primary : GetDatosColumnas( "btn_Actual" , "jqueryui-icons" )
				}
			} ).click( function(evt) {
				if ( GetDatosColumnas( "btn_Actual" , "Editar" ) == true ) {
					irTareaActual( );
				}
			} );
		}
		if ( GetDatosColumnas( "btn_Resumen Semanal" , "Hidden" ) == false ) {
			$( "<span>" + GetDatosColumnas( "btn_Resumen Semanal" , "Nombre" ) + "</span>" ).appendTo( $toolbar ).button( {
				icons : {
					primary : GetDatosColumnas( "btn_Resumen Semanal" , "jqueryui-icons" )
				}
			} ).click( function(evt) {
				if ( GetDatosColumnas( "btn_Resumen Semanal" , "Editar" ) == true ) {

					slidesemanal( );
				} else {
					spb_mensaje( 'grid_array' , GetDatosColumnas( "btn_Resumen Semanal" , "Editar Mensaje" ) );
				}
			} );
		}
		if ( GetDatosColumnas( "btn_Resumen Comentarios" , "Hidden" ) == false ) {
			$( "<span>" + GetDatosColumnas( "btn_Resumen Comentarios" , "Nombre" ) + "</span>" ).appendTo( $toolbar ).button( {
				icons : {
					primary : GetDatosColumnas( "btn_Resumen Comentarios" , "jqueryui-icons" )
				}
			} ).click( function(evt) {
				if ( GetDatosColumnas( "btn_Resumen Comentarios" , "Editar" ) == true ) {
					slidecomentarios( );
				} else {
					spb_mensaje( 'grid_array' , GetDatosColumnas( "btn_Resumen Comentarios" , "Editar Mensaje" ) );
				}
			} );
		}
    	if(externo == '' ){
    		$("<span>Evaluaciones</span>").appendTo($toolbar).button({ icons: { primary: "	ui-icon-star" } }).click(function(evt) { cambia_menu(21); });
    		$("<span>Hoy</span>").appendTo($toolbar).button({ icons: { primary: "	ui-icon-clock" } }).click(function(evt) { 
    			
    			var fechahoy = obtenerFecha(0);
    			
        		ver_fecha_horas(fechahoy);
        	}); 
    		
    		if(oficinausuariovalidado == 'TI'){
    			$("<span>Entrada correo<span id='prueba'></span></span>").appendTo($toolbar).button({ icons: { primary: "	ui-icon-mail-closed" } }).click(function(evt) { cambia_menu(22); });
    		}
    	}
    	if((evaluador == 1 && externo == '') /*|| externo != ''*/ ){
    		$("<span>Acceso Evaluadores</span>").appendTo($toolbar).button({ icons: { primary: "	ui-icon-star" } }).click(function(evt) { cambia_menu(13); }); 
    	}
    	
	};

	// asignamos obj al grid
	var $grid = $( "#" + txtGrid ).pqGrid( obj );

	// ORDENAMOS EL GRID
	ordenarGrid( $grid );

	// SORT PQGRID EVENT
	$grid.pqGrid( {
		sort : function(event , ui) {
			guardarDataModel( 'grid_array' );
		}
	} );

	// COLUMNRESIZE PQGRID EVENT
	$grid.pqGrid( {
		columnResize : function(event , ui) {
			guardarColModel( 'grid_array' );
		}
	} );

	$( "#" + txtGrid ).pqGrid( {
		collapsible : false
	} );
	$grid.pqGrid( "option" , "freezeCols" , 2 );
	//$grid.pqGrid( "option" , "scrollModel" , {		horizontal : true	} , {		vertical : true	} );
	$grid.pqGrid( "option" , "columnBorders" , true );
	$grid.pqGrid( "option" , "rowBorders" , true );
	$grid.pqGrid( "option" , "oddRowsHighlight" , true );

	$grid.on( "pqgridcellsave" , function(evt , ui) {
		var Campo = ui.dataIndx;
		var Dato = ui.rowData[ ui.dataIndx ];
		var ID = ui.rowData[ "Id" ];
		var url = "select/ModificarCampoTarea.php?id=" + ID + "&dato=" + encodeURIComponent( Dato ) + "&usuario=" + encodeURIComponent( usuariovalidado ) + "&campo=" + encodeURIComponent( Campo );
		$.ajax( {
			url : url
		} ).done( function() {
			$( "#grid_array" ).pqGrid( "refreshDataAndView" );
		} );
	} );
	function slidesemanal() {
		crearslidesemanal( 0 );
	}
	function slidecomentarios() {
		crearslidecomentarios( 0 );
	}
	function editRow() {
		if ( ID_SELECCIONADA == '-1' ) {
			var dialogo = document.getElementById( 'dialog-iniciar' );
			dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/info.png' width=\"30\" height=\"30\" />&nbsp;" + 'Seleccione una tarea antes</p>';
			$( "#dialog-iniciar" ).dialog( {
				resizable : false ,
				height : 250 ,
				modal : true ,
				buttons : {
					"Ok" : function() {
						$( this ).dialog( "close" );
					}
				}
			} );
		} else {
			cambia_menu( 3 );
		}
	}
	function addRow() {
		cambia_menu( 4 );
	}
	function addRowAlta() {
		cambia_menu( 14 );
	}
	function initRow() {
		if ( ID_SELECCIONADA == '-1' ) {
			var dialogo = document.getElementById( 'dialog-iniciar' );
			dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/info.png' width=\"30\" height=\"30\" />&nbsp;" + 'Seleccione una tarea antes</p>';
			$( "#dialog-iniciar" ).dialog( {
				resizable : false ,
				height : 250 ,
				modal : true ,
				buttons : {
					"Ok" : function() {
						$( this ).dialog( "close" );
					}
				}
			} );
		} else {
			var posible = buscarpermiteiniciartarea( ID_SELECCIONADA );
			if ( posible == '1' ) {
				$( "#dialog-iniciar" ).html( '<img style="width:20px;height:20px;" src="./imagenes/alert.png">No se puede iniciar una tarea cerrada' );
				$( "#dialog-iniciar" ).dialog( {
					resizable : false ,
					title : 'Prohibido Iniciar Tarea' ,
					width : 400 ,
					height : 200 ,
					modal : false ,
					buttons : {
						"Ok" : function() {
							$( this ).dialog( "close" );
						}
					}
				} );
			} else {
				var lista = buscartareahoraabierta( );
				var res = lista.split( "|" );
				if ( lista.length > 3 ) {
					var dialogo = document.getElementById( 'dialog-iniciar' );
					dialogo.innerHTML = '<p><span class="ui-icon-circle-triangle-e" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/info.png' width=\"30\" height=\"30\" />&nbsp;" + res[ 2 ] + '¿Desea cerrar esta tarea antes?</p>';
					$( "#dialog-iniciar" ).dialog( {
						resizable : false ,
						height : 250 ,
						modal : true ,
						buttons : {
							"Si" : function() {
								$( this ).dialog( "close" );
								if ( res[ 1 ] == '0' ) {
									var dialogo = document.getElementById( 'dialog-iniciar' );
									dialogo.innerHTML = '<p><span class="ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/info.png' width=\"30\" height=\"30\" />&nbsp;" + 'La tarea se inicio en otro día.¿Se le olvido cerrarla?</p>';
									$( "#dialog-iniciar" ).dialog( {
										resizable : false ,
										height : 250 ,
										modal : true ,
										buttons : {
											"Si" : function() {
												$( this ).dialog( "close" );
												ID_HORA_SELECCIONADA = res[ 0 ];
												ID_HORA_SELECCIONADA_ESTADO = res[ 1 ];
												cierrotareahora( 1 );
												iniciotareahora( );
												$( "#grid_array" ).pqGrid( "refreshDataAndView" );
											} ,
											"No" : function() {
												$( this ).dialog( "close" );
												ID_HORA_SELECCIONADA = res[ 0 ];
												ID_HORA_SELECCIONADA_ESTADO = res[ 1 ];
												cierrotareahora( 2 );
												iniciotareahora( );
												$( "#grid_array" ).pqGrid( "refreshDataAndView" );
											}
										}
									} );
								} else {
									ID_HORA_SELECCIONADA = res[ 0 ];
									ID_HORA_SELECCIONADA_ESTADO = res[ 1 ];
									cierrotareahora( 2 );
									iniciotareahora( );
									$( "#grid_array" ).pqGrid( "refreshDataAndView" );
								}
								ID_SELECCIONADA = '-1';
							} ,
							"No" : function() {
								$( this ).dialog( "close" );
								ID_SELECCIONADA = '-1';
								ID_PLAN_SELECCIONADA = -1;
								$( "#grid_array" ).pqGrid( "refreshDataAndView" );
							}
						}
					} );
				} else {
					iniciotareahora( );
					ID_SELECCIONADA = '-1';
					$( "#grid_array" ).pqGrid( "refreshDataAndView" );
				}
			}
		}
	}
	function closeRow() {
		PararTarea( );
	}
	function irTareaActual(){
		irActual(0);
	}
	function getRowIndx() {
		var arr = $grid.pqGrid( "selection" , {
			type : 'row' ,
			method : 'getSelection'
		} );
		if ( arr && arr.length > 0 ) {
			var rowIndx = arr[ 0 ].rowIndx;
			return rowIndx;
		} else {
			return null;
		}
	}
	$( "#" + txtGrid ).pqGrid( "option" , "pageModel.rPP" , rPP_Usuario );
	$( "#" + txtGrid ).pqGrid( "option" , "pageModel.rPPOptions" , rPPOptions_Usuario );
	$( "#" + txtGrid ).pqGrid( "refreshDataAndView" );
	menu_contextual_grid( txtGrid );
}

function ordenarGrid($grid) {
	// RECUPERAMOS ORDENACION DE COLUMNAS DE LA BD

	$.ajax( {
		type : 'GET' ,
		url : 'grid/control_grid.php?usuario=' + encodeURIComponent( usuariovalidado ) + '&tipo=5' ,
		success : function(msg) {
			if ( msg != "" ) {

				msg = msg.replace( "," , ";" );
				msg = msg.split( "|" );

				var array1 = [ ];
				var array2 = [ ];

				array0 = msg[ 0 ].split( ";" );
				array1 = msg[ 1 ].split( ";" );

				$grid.pqGrid( "option" , "dataModel.sortIndx" , array0 );
				$grid.pqGrid( "option" , "dataModel.sortDir" , array1 );
			} else {
				$grid.pqGrid( "option" , "dataModel.sortIndx" , [ "Id" ] );
				$grid.pqGrid( "option" , "dataModel.sortDir" , [ "down" ] );
			}
			$grid.pqGrid( "refreshDataAndView" );
		}
	} );
}
function filter(txtGrid , dataIndx , value) {
	$( "#" + txtGrid ).pqGrid( "filter" , {
		data : [ {
			dataIndx : dataIndx ,
			value : value
		} ]
	} );
}
function ObtenerTitulo(txtTitulo) {
	var parte_00 = " tareas ";
	var parte_01 = "";
	// Tipo Visibilidad que afecta a usuario y departamento tipo_visibilidad_1
	// 1--Todas
	// 2--Departamento
	// 3--Mias
	if ( tipo_visibilidad_1 == '1' ) {
		parte_00 = " todas las tareas ";
	}
	if ( tipo_visibilidad_1 == '2' ) {
		parte_00 = " las tareas de mi departamento ";
	}
	if ( tipo_visibilidad_1 == '3' ) {
		parte_00 = " mis tareas ";
	}

	// Conseguir la descripción de las consultas personalizadas en el titulo del grid
	// Alberto 11/08/2016
	lista_personalizada = "";
	for ( x = 0 ; x < cantidad_personalizadas.length ; x++ ) {
		if ( $( "#li_consulta_personalizada_" + cantidad_personalizadas[ x ] + "_visibilidad" ).attr( "src" ) == "./imagenes/RedCheckBox.png" ) {
			lista_personalizada = lista_personalizada + cantidad_personalizadas[ x ] + ",";
		}
	}
	lista_personalizada = lista_personalizada.substring( 0 , lista_personalizada.length - 1 );

	if ( tipo_visibilidad_1 == '1' ) {
		if ( lista_personalizada != '' ) {
			var url = "select/getConsultaPersonalizada.php?lista_personalizada=" + lista_personalizada;
			$.ajax( {
				cache : false ,
				async : false ,
				type : "POST" ,
				url : url ,
				success : function(data) {
					if ( parte_01 == '' ) {
						parte_01 = "";
					} else {
						parte_01 = parte_01 + ",";
					}
					parte_01 = parte_01 + data;
				}
			} );
		}
	}

	// Tipo Visibilidad que afecta a desarrollo tipo_visibilidad_2
	// 1--Todas
	// 2--Participo
	if ( tipo_visibilidad_2 == '2' ) {
		if ( parte_01 == '' ) {
			parte_01 = "";
		} else {
			parte_01 = parte_01 + ",";
		}
		parte_01 = parte_01 + " participo ";
	}

	// Tipo Visibilidad que afecta a desarrollo tipo_visibilidad_3
	// 1--Todas
	// 2--Iniciadas
	if ( tipo_visibilidad_3 == '2' ) {
		if ( parte_01 == '' ) {
			parte_01 = " ";
		} else {
			parte_01 = parte_01 + ",";
		}
		parte_01 = parte_01 + " iniciadas ";
	}

	// Tipo Visibilidad que afecta a desarrollo tipo_visibilidad_4
	// 1--Todas
	// 2--Pendientes
	if ( tipo_visibilidad_4 == '2' ) {
		if ( parte_01 == '' ) {
			parte_01 = " ";
		} else {
			parte_01 = parte_01 + ",";
		}
		parte_01 = parte_01 + " pendientes ";
	}

	// Tipo Visibilidad que afecta a desarrollo tipo_visibilidad_5
	// 1--Todas
	// 2--Recientes
	if ( tipo_visibilidad_5 == '2' ) {
		if ( parte_01 == '' ) {
			parte_01 = " ";
		} else {
			parte_01 = parte_01 + ",";
		}
		parte_01 = parte_01 + " recientes ";
	}

	// Tipo Visibilidad que afecta a desarrollo tipo_visibilidad_6
	// 1--Todas
	// 2--Recientes
	if ( tipo_visibilidad_6 == '2' ) {
		if ( parte_01 == '' ) {
			parte_01 = " ";
		} else {
			parte_01 = parte_01 + " ,";
		}
		parte_01 = parte_01 + " sin asignar ";
	}

	// Tipo Visibilidad que afecta a desarrollo tipo_visibilidad_6
	// 1--Todas
	// 2--Recientes
	if ( tipo_visibilidad_7 == '2' ) {
		if ( parte_01 == '' ) {
			parte_01 = " ";
		} else {
			parte_01 = parte_01 + ",";
		}
		parte_01 = parte_01 + " sin iniciar ";
	}

	// Tipo Visibilidad que afecta a desarrollo tipo_visibilidad_6
	// 1--Todas
	// 2--Recientes
	if ( tipo_visibilidad_8 == '2' ) {
		if ( parte_01 == '' ) {
			parte_01 = " ";
		} else {
			parte_01 = parte_01 + " ,";
		}
		parte_01 = parte_01 + " retrasadas ";
	}

	// Tipo Visibilidad que afecta a desarrollo tipo_visibilidad_9
	// 1--Todas
	// 2--Recientes
	if ( tipo_visibilidad_9 == '2' ) {
		if ( parte_01 == '' ) {
			parte_01 = " ";
		} else {
			parte_01 = parte_01 + " ,";
		}
		parte_01 = parte_01 + " teletrabajo ";
	}

	// Tipo Visibilidad que afecta a desarrollo tipo_visibilidad_A_Descripcion
	// ''--Todas
	// 2--Pendientes
	if ( tipo_visibilidad_A_Descripcion != '' ) {
		if ( parte_01 == '' ) {
			parte_01 = " que estan en la cola ";
		} else {
			parte_01 = parte_01 + " y esten en la cola ";
		}
		parte_01 = parte_01 + " " + tipo_visibilidad_A_Descripcion + " ";
	}

	return "Lista de " + parte_00 + parte_01;
}

function menu_contextual_grid(target) {
	$( function() {
		$( '#' + target ).droppable( {
			drop : function(event , ui) {
				$( function() {
					setTimeout( function() {
						var colM = $( '#' + target ).pqGrid( "option" , "colModel" );
						var myJsonString = "";
						for ( var j = 0 ; j < colM.length ; j++ ) {
							// myJsonString = myJsonString + colM[ j ].dataIndx + "=" + colM[ j ].hidden + "|";
							myJsonString = myJsonString + colM[ j ].dataIndx + "=" + colM[ j ].hidden + "=" + colM[ j ].width + "=" + colM[ j ].title + "|";
						}
						$.ajax( {
							type : 'POST' ,
							url : 'grid/control_grid.php?tipo=2' ,
							data : {
								'myJsonString' : myJsonString ,
								'usuario' : usuariovalidado ,
								'grid' : target ,
								'tipomodel' : 'colModel'
							} ,
							success : function(msg) {

							}
						} );
					} , 3000 );
				} );
			}

		} );

		$( '#' + target ).bind( 'contextmenu' , function(e) {
			// EVITO QUE SE EJECUTE EL MENU POR DEFECTO
			e.preventDefault( );

			var checkeado = '';

			// OBTENEMOS EL COLMOD ACTUAL
			// http://paramquery.com/pro/api24#option-colModel
			// getter
			var colModel = $( '#' + target ).pqGrid( "option" , "colModel" );

			// CREAMOS UNA TABLA HTML CON TODOS LOS CAMPOS DEL COLMODEL CON UNA COLUMNA CHECKED SI NO ESTA OCULTA
			tablacolumnas = '<table class="tabla_columnas"><tr><th  width="*">Campo</th><th width="*">Nombre personalizado</th><th width="100">Visible</th><th width="100">Ancho</th></tr>';
			for ( var i = 0 ; i < colModel.length ; i++ ) {
				tipo_menu_columna = colModel[ i ].spb_menu || id_visualizacion;
				if ( tipo_menu_columna === id_visualizacion ) {
					if ( colModel[ i ].hidden == true ) {
						checkeado = '';
					} else {
						checkeado = ' checked="checked" ';
					}
					tablacolumnas = tablacolumnas + '<tr>' + '<td>' + colModel[ i ].dataIndx + '</td>' + '<td><input type="text" id="' + i + '" class="title_grid_array"  name="title_' + colModel[ i ].title + '" value="' + colModel[ i ].title + '"></td>' + '<td><input type="checkbox" id="' + i + '" class="hidden_grid_array" name="hidden_' + colModel[ i ].title + '" value="' + colModel[ i ].title + '" ' + checkeado + '></td>' + '<td><input type="text"     id="' + i + '" class="width_grid_array"  name="width_' + colModel[ i ].title + '" value="' + colModel[ i ].width + '"></td>' + '</tr>';
				}
			}
			tablacolumnas = tablacolumnas + '</table>';

			// CREAMOS UN NUEVO DIV Y LO LLENAMOS CON LA TABLA tablacolumnas
			$( "#mensaje_" + target ).remove( );
			$( 'body' ).append( '<div id="mensaje_' + target + '" name="mensaje_' + target + '" class="mensaje"></div>' );
			$( "#mensaje_" + target ).html( tablacolumnas );
			$( "#mensaje_" + target ).dialog( {
				resizable : false ,
				title : 'Edición de columnas' ,
				width : 720 ,
				height : 500 ,
				modal : true ,
				buttons : {
					"Exportar Grid a Excel" : function() {
						$( "#" + target ).pqGrid( "exportExcel" , {
							url : "./export/excel.php" ,
							sheetName : "Exportacion"
						} );
					} ,
					"Restaurar Grid" : function() {
						var url = 'grid/control_grid.php?usuario=' + encodeURIComponent( usuariovalidado ) + '&tipo=4';
						$.ajax( {
							url : url
						} ).done( function() {
							window.location.reload( true );
						} );
						$( this ).dialog( "close" );
					} ,
					// GUARDAR DATAMODEL EN LA BD
					"Cancelar" : function() {
						$( this ).dialog( "close" );
					} ,

					"Guardar" : function() {
						guardarColModel( target );
						$( this ).dialog( "close" );
						$( "#mensaje_" + target ).remove( );
					}
				}
			} );
		} );
	} );
}
function guardarColModel(target) {
	// OBTENEMOS EL COLMOD ACTUAL Y VAMOS A CAMBIAR EL HIDDEN DE LAS COLUMNAS DESDE
	// LOS ELEMENTOS DE LA TABLA QUE HA PODIDO CAMBIAR EL USUARIO
	// getter
	var colM = $( '#' + target ).pqGrid( "option" , "colModel" );
	var title = $( '#' + target ).pqGrid( "option" , "title" );
	var contador = 0;

	// BUCLE FOR EACH PARA LOS INPUTS HIDDEN
	$( '.hidden_grid_array' ).each( function() {
		var sThisVal = ( this.checked ? "1" : "0" );
		var micontador = this.id;
		// alert( "Contador: " + contador + " MiContador" + micontador );
		// alert( "this.checked: " + this.checked + " - sThisVal: " + sThisVal );
		// alert( contador + ": " + this.value + ' ' + sThisVal );
		if ( sThisVal == "1" ) {
			colM[ micontador ].hidden = false;
		} else {
			colM[ micontador ].hidden = true;
		}
		contador = contador + 1;
	} );

	// AHORA LO MISMO PARA LOS INPUTS WIDTH
	$( '.width_grid_array' ).each( function() {
		var micontador = this.id;
		colM[ micontador ].width = this.value;
	} );

	// AHORA LO MISMO PARA LOS INPUTS TITLE
	$( '.title_grid_array' ).each( function() {
		var micontador = this.id;
		colM[ micontador ].title = this.value;
		// alert("Title: " + this.value);
	} );

	// ASIGNAMOS COLMODEL colM AL GRID ACTUAL
	$( '#' + target ).pqGrid( "option" , "colModel" , colM );

	// YA HEMOS CARGADO EL COLMODEL DEL GRID ACTUAL
	// AHORA QUEREMOS GUARDAR SU CONFIGURACION EN LA BD

	// PRIMERO GUARDAMOS LA CONFIGURACIÓN DE LAS COLUMNAS HIDDEN
	var myJsonString = "";
	for ( var j = 0 ; j < colM.length ; j++ ) {
		myJsonString = myJsonString + colM[ j ].dataIndx + "=" + colM[ j ].hidden + "=" + colM[ j ].width + "=" + colM[ j ].title + "|";
	}
	$.ajax( {
		type : 'POST' ,
		url : 'grid/control_grid.php?tipo=2' ,
		data : {
			'myJsonString' : myJsonString ,
			'usuario' : usuariovalidado ,
			'grid' : target ,
			'tipomodel' : 'colModel'
		} ,
		success : function(msg) {

		}
	} );
}
function guardarDataModel(target) {

	// getter de sortIndx
	var ordenColumnas = $( '#' + target ).pqGrid( "option" , "dataModel.sortIndx" );
	var dataModel = "";
	if ( Array.isArray( ordenColumnas ) ) {
		dataModel = ordenColumnas.toString( );
	} else {
		dataModel = ordenColumnas;
	}

	// getter de sortDir
	var dirColumnas = $( '#' + target ).pqGrid( "option" , "dataModel.sortDir" );
	if ( Array.isArray( ordenColumnas ) ) {
		var str = dirColumnas.toString( );
		dataModel = dataModel + "|" + dirColumnas.toString( );
	} else {
		dataModel = dataModel + "|" + dirColumnas;
	}

	dataModel = dataModel.replace( /,/g , ';' );

	$.ajax( {
		type : 'POST' ,
		url : 'grid/control_grid.php?tipo=2' ,
		data : {
			'myJsonString' : dataModel ,
			'usuario' : usuariovalidado ,
			'grid' : target ,
			'tipomodel' : 'dataModel'
		} ,
		success : function(msg) {
		}
	} );
}

function GetDatosColumnas(campo , dato) {
	
	checkcampo = resultado_msgcolumnas[ campo ];
	if ( typeof checkcampo == "undefined" ) {
		if ( dato == "Ocultar" ) {
			return id_visualizacion;
		}
		if ( dato == "Nombre" ) {
			return campo;
		}
		if ( dato == "Editar" ) {
			return true;
		}
		if ( dato == "Editar Mensaje" ) {
			if ( campo.substr( 0 , 4 ) == 'btn_' ) {
				return 'No tiene permiso para ejecutar el botón <div style="float: right;margin-left: 10px;border: 1px solid #DEDEDE;color: black;padding: 5px;border-radius: 3px;background: linear-gradient(rgb(227, 227, 227), rgb(204, 204, 204)) repeat scroll 0% 0% transparent;">' + campo.substr( 4 ) + '</div>';
			}
		}
		if ( dato == "Hidden" ) {
			return false;
		}
		if ( dato == "jqueryui-icons" ) {
			return 'ui-icon-radio-on';
		}
	} else {
		if ( dato == "Ocultar" ) {
			if ( resultado_msgcolumnas[ campo ][ dato ] == 1 ) {
				return parseInt( id_visualizacion ) * -1;
			}
			return id_visualizacion;
		}
		if ( dato == "Hidden" ) {
			if ( resultado_msgcolumnas[ campo ][ 'Ocultar' ] == 1 ) {
				return true;
			}
			return false;
		}
		if ( dato == "Nombre" ) {
			return resultado_msgcolumnas[ campo ][ dato ];
		}
		if ( dato == "Editar" ) {
			if ( resultado_msgcolumnas[ campo ][ dato ] == 1 ) {
				return true;
			}
			return false;
		}
		if ( dato == "Editar Mensaje" ) {
			if ( campo.substr( 0 , 4 ) == 'btn_' ) {
				return 'No tiene permiso para ejecutar el botón <div style="float: right;margin-left: 10px;border: 1px solid #DEDEDE;color: black;padding: 5px;border-radius: 3px;background: linear-gradient(rgb(227, 227, 227), rgb(204, 204, 204)) repeat scroll 0% 0% transparent;">' + resultado_msgcolumnas[ campo ][ 'Nombre' ] + '</div>';
			}
		}
		if ( dato == "jqueryui-icons" ) {
			return resultado_msgcolumnas[ campo ][ 'Imagen' ];
		}
	}
}
function ObtieneColumnas(txtGrid) {

	var lista = [ {
		title : GetDatosColumnas( "Id" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Id" , "Ocultar" ) ,
		width : 100 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Id" , "Hidden" ) ,
		editable : false,
		dataIndx : "Id" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Id" , $( this ).val( ) );
				}
			} ]
		} ,
		render : function(ui) {
			var rowData = ui.rowData;
			var rowRender = '';
			var title = "";

			
			if ( rowData[ "MARCADA_BANDERA" ] != "0" ) {
				rowRender = rowRender + "<img src='imagenes/marcada.png' width=\"15\" height=\"15\" />&nbsp;";
			}
			
			if ( rowData[ "iniciado_sn" ] == "1" ) {
				if ( rowData[ "iniciado_usuario" ] == "1" ) {
					rowRender = rowRender + "<img src='imagenes/play2.png' width=\"10\" height=\"10\" />&nbsp;<b><font color='red'></b>";
					title = title + "-Usted está fichando en esta tarea.&#10;";
				}else{
					rowRender = rowRender + "<img src='imagenes/play.png' width=\"10\" height=\"10\" />";
					title = title + "-Un usuario está fichando en esta.&#10;";
				}
			}

			rowRender = rowRender + rowData[ "Id" ];

			if ( rowData[ "Prioridad" ] == "1 - Crítica" ) {
				rowRender = rowRender + "&nbsp<img src='imagenes/red_circle.png' width=\"10\" height=\"10\" />";
				title = title + "-Esta tarea es crítica.&#10;";
			}
			if ( rowData[ "Prioridad" ] == "2 - Importante" ) {
				rowRender = rowRender + "&nbsp<img src='imagenes/orange_circle.png' width=\"10\" height=\"10\" />";
				title = title + "-Esta tarea es importante.&#10;";
			}
			
			if ( rowData[ "Planificada" ] != "0" ) {				
				
				if(rowData[ "Planificada" ] != "1"){
					title = title + "-Tarea planificada "+rowData[ "Planificada" ]+" veces.&#10;"
				}else{
					title = title + "-Tarea planificada una vez.&#10;";
				}
				rowRender = rowRender + "&nbsp<img src='imagenes/planificador.png' width='10' height='10'  />";
			}
			
			// Alberto: de momento se quita la estrellita de evaluaciones, por que ya se encarga el jefe de evaluar lo pendiente
			if ( rowData[ "Evaluable" ] == 'SI'){
				if ( parseInt(rowData[ "Evaluaciones_Pendientes" ]) == 0 ) {
					//rowRender = rowRender + "<img src='imagenes/estrella_verde.png' width=\"11\" height=\"11\" />&nbsp;";
					//title = title + "-Esta tarea no tiene evaluaciones pendientes.&#10;";
				}else{
					if ( parseInt(rowData[ "Evaluaciones_Pendientes" ]) == 1 ) {
						rowRender = rowRender + "<img src='imagenes/estrella_amarilla.png' width=\"11\" height=\"11\" />&nbsp;";
						title = title + "-Esta tarea tiene una evaluación pendiente de evaluar.&#10;";
					}else{
						rowRender = rowRender + "<img src='imagenes/estrella_roja.png' width=\"11\" height=\"11\" />&nbsp;";
						title = title + "-Esta tarea tiene "+rowData[ "Evaluaciones_Pendientes" ]+" evaluaciones pendientes de evaluar.&#10;";
					}
				}
			}
			return "<div style='width:100%' title=\""+title+"\">" + rowRender + "</div>";
		}
	} , {
		title : GetDatosColumnas( "Oficina" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Oficina" , "Ocultar" ) ,
		width : 150 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Oficina" , "Hidden" ) ,
		editable : false ,
		dataIndx : "Oficina" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Oficina" , $( this ).val( ) );
				}
			} ]
		} ,
		render : function(ui) {
			var rowData = ui.rowData;
			
			
			if ( rowData[ "Usuario_colabora_sn" ] == "1" ) {
				return rowData[ "Oficina" ] + "&nbsp<img src='imagenes/colaboradores3.png' width=\"10\" height=\"10\" title='Colaboras en esta tarea'/>";
			}else{
				return rowData[ "Oficina" ];
			}
		} ,

	} , {
		title : GetDatosColumnas( "Título" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Título" , "Ocultar" ) ,
		width : 400 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Título" , "Hidden" ) ,
		editable : false,
		dataIndx : "Título" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Título" , $( this ).val( ) );
				}
			} ]
		} ,
		render : function(ui) {
			var rowData = ui.rowData;
			return rowData[ "Título" ];
		}
	} ,
	// { title: GetDatosColumnas("Numero_Prioridad","Nombre"),spb_menu: GetDatosColumnas("Numero_Prioridad","Ocultar"), dataType: "string",hidden:GetDatosColumnas("Numero_Prioridad","Hidden"),editable: GetDatosColumnas("Numero_Prioridad","Editar"), dataIndx: "Numero_Prioridad"},
	{
		title : GetDatosColumnas( "Categoría" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Categoría" , "Ocultar" ) ,
		width : 150 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Categoría" , "Hidden" ) ,
		editable : false ,
		dataIndx : "Categoría" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Categoría" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return categorias;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "Tipo" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Tipo" , "Ocultar" ) ,
		width : 90 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Tipo" , "Hidden" ) ,
		editable : false,
		dataIndx : "Tipo" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Tipo" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return tipos;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "Asignado a" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Asignado a" , "Ocultar" ) ,
		width : 90 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Asignado a" , "Hidden" ) ,
		editable : false  ,
		dataIndx : "Asignado a" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Asignado a" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return usuariosti;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "Destinatario" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Destinatario" , "Ocultar" ) ,
		width : 90 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Destinatario" , "Hidden" ) ,
		editable : false  ,
		dataIndx : "Destinatario" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Destinatario" , $( this ).val( ) );
				}
			} ]
		} 
	} , {
		title : GetDatosColumnas( "Fecha alta" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Fecha alta" , "Ocultar" ) ,
		width : 70 ,
		dataType : "date" ,
		hidden : GetDatosColumnas( "Fecha alta" , "Hidden" ) ,
		editable : false ,
		dataIndx : "Fecha alta" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Fecha alta" , $( this ).val( ) );
				}
			} ]
		}
	} , {
		title : GetDatosColumnas( "Prioridad" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Prioridad" , "Ocultar" ) ,
		width : 90 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Prioridad" , "Hidden" ) ,
		editable : false ,
		dataIndx : "Prioridad" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Prioridad" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return prioridades;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "Estado" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Estado" , "Ocultar" ) ,
		width : 90 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Estado" , "Hidden" ) ,
		editable : false ,
		dataIndx : "Estado" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Estado" , $( this ).val( ) );
				}
			} ]
		} ,
		render : function(ui) {
			var rowData = ui.rowData;
			if ( rowData[ "iniciado_sn" ] == "1" ) {
				return "<img src='imagenes/play.png' width=\"5\" height=\"5\" />&nbsp;" + rowData[ "Estado" ];
			} else {
				return rowData[ "Estado" ];
			}
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return estadostarea;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "Estrategico" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Estrategico" , "Ocultar" ) ,
		width : 90 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Estrategico" , "Hidden" ) ,
		editable : false,
		dataIndx : "Estrategico" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Estrategico" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return listaestrategico;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "Solicitado" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Solicitado" , "Ocultar" ) ,
		width : 90 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Solicitado" , "Hidden" ) ,
		editable : false ,
		dataIndx : "Solicitado" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Solicitado" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : autoCompleteEditor
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} , {
			type : function(ui) {
				var value = ui.value;
				if ( $.inArray( ui.value , countries ) == -1 ) {
					ui.msg = value + " not found in list";
					return false;
				}
			}
		} ]
	} , {
		title : GetDatosColumnas( "Subcategoría" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Subcategoría" , "Ocultar" ) ,
		width : 90 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Subcategoría" , "Hidden" ) ,
		editable : false,
		dataIndx : "Subcategoría" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Subcategoría" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return subcategorias;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "Usuario" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Usuario" , "Ocultar" ) ,
		width : 90 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Usuario" , "Hidden" ) ,
		editable : false,
		dataIndx : "Usuario" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Usuario" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : autoCompleteEditor
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} , {
			type : function(ui) {
				var value = ui.value;
				if ( $.inArray( ui.value , countries ) == -1 ) {
					ui.msg = value + " not found in list";
					return false;
				}
			}
		} ]
	} , {
		title : GetDatosColumnas( "Fecha objetivo" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Fecha objetivo" , "Ocultar" ) ,
		width : 90 ,
		dataType : "date" ,
		hidden : GetDatosColumnas( "Fecha objetivo" , "Hidden" ) ,
		editable : false ,
		dataIndx : "Fecha objetivo" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Fecha objetivo" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : dateEditor
		} ,
		validations : [ {
			type : 'regexp' ,
			value : '[0-9]{2}/[0-9]{2}/[0-9]{4}' ,
			msg : 'Not en dd/mm/yyyy formato'
		} ]
	} , {
		title : GetDatosColumnas( "Tarea / Proyecto" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Tarea / Proyecto" , "Ocultar" ) ,
		width : 450 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Tarea / Proyecto" , "Hidden" ) ,
		editable : false ,
		dataIndx : "Tarea / Proyecto" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Tarea / Proyecto" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : autoCompleteTareaProyecto
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} , {
			type : function(ui) {
				var value = ui.value;
				if ( $.inArray( ui.value , countries ) == -1 ) {
					ui.msg = value + " not found in list";
					return false;
				}
			}
		} ]
	} , {
		title : GetDatosColumnas( "Cola" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Cola" , "Ocultar" ) ,
		width : 150 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Cola" , "Hidden" ) ,
		editable : false,
		dataIndx : "Cola" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Cola" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return colas;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "MARCADA_BANDERA" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "MARCADA_BANDERA" , "Ocultar" ) ,
		width : 90 ,
		dataType : "integer" ,
		hidden : GetDatosColumnas( "MARCADA_BANDERA" , "Hidden" ) ,
		editable : false ,
		dataIndx : "MARCADA_BANDERA"
	} , {
		title : GetDatosColumnas( "iniciado_sn" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "iniciado_sn" , "Ocultar" ) ,
		width : 90 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "iniciado_sn" , "Hidden" ) ,
		editable : false,
		dataIndx : "iniciado_sn"
	} , {
		title : GetDatosColumnas( "Referencia" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Referencia" , "Ocultar" ) ,
		width : 200 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Referencia" , "Hidden" ) ,
		editable : false,
		dataIndx : "Referencia" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Referencia" , $( this ).val( ) );
				}
			} ]
		}
	} , {
		title : GetDatosColumnas( "Motivo bap" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Motivo bap" , "Ocultar" ) ,
		width : 200 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Motivo bap" , "Hidden" ) ,
		editable : false,
		dataIndx : "Motivo bap" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Motivo bap" , $( this ).val( ) );
				}
			} ]
		}
	} , {
		title : GetDatosColumnas( "Horas estimadas" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Horas estimadas" , "Ocultar" ) ,
		width : 110 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Horas estimadas" , "Hidden" ) ,
		editable : false,
		dataIndx : "Horas estimadas" ,
		align: 'right',
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Horas estimadas" , $( this ).val( ) );
				}
			} ]
		}
	}, {
		title : GetDatosColumnas( "HorasReales" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "HorasReales" , "Ocultar" ) ,
		width : 110 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "HorasReales" , "Hidden" ) ,
		editable : false,
		dataIndx : "HorasReales" ,
		align: 'right', 
		sortable: false 
	} , {
		title : GetDatosColumnas( "Fecha Solicitud" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Fecha Solicitud" , "Ocultar" ) ,
		width : 90 ,
		dataType : "date" ,
		hidden : GetDatosColumnas( "Fecha Solicitud" , "Hidden" ) ,
		editable : false,
		dataIndx : "Fecha Solicitud" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Fecha Solicitud" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : dateEditor
		} ,
		validations : [ {
			type : 'regexp' ,
			value : '[0-9]{2}/[0-9]{2}/[0-9]{4}' ,
			msg : 'Not en dd/mm/yyyy formato'
		} ]
	} , {
		title : GetDatosColumnas( "Departamento Solicitud" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Departamento Solicitud" , "Ocultar" ) ,
		width : 150 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Departamento Solicitud" , "Hidden" ) ,
		editable : false,
		dataIndx : "Departamento Solicitud" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Departamento Solicitud" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : autoCompleteDepartamentoEditor
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} , {
			type : function(ui) {
				var value = ui.value;
				if ( $.inArray( ui.value , countries ) == -1 ) {
					ui.msg = value + " not found in list";
					return false;
				}
			}
		} ]
	} , {
		title : GetDatosColumnas( "Programa Solicitud" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Programa Solicitud" , "Ocultar" ) ,
		width : 150 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Programa Solicitud" , "Hidden" ) ,
		editable : false,
		dataIndx : "Programa Solicitud" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Programa Solicitud" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : autoCompleteProgramaEditor
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} , {
			type : function(ui) {
				var value = ui.value;
				if ( $.inArray( ui.value , countries ) == -1 ) {
					ui.msg = value + " not found in list";
					return false;
				}
			}
		} ]
	} , {
		title : GetDatosColumnas( "AporteOrganizacion" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "AporteOrganizacion" , "Ocultar" ) ,
		width : 100 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "AporteOrganizacion" , "Hidden" ) ,
		editable : false,
		dataIndx : "AporteOrganizacion" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "AporteOrganizacion" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return null;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "AporteEmpresa" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "AporteEmpresa" , "Ocultar" ) ,
		width : 100 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "AporteEmpresa" , "Hidden" ) ,
		editable : false,
		dataIndx : "AporteEmpresa" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "AporteEmpresa" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return null;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "AporteJefe" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "AporteJefe" , "Ocultar" ) ,
		width : 100 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "AporteJefe" , "Hidden" ) ,
		editable : false,
		dataIndx : "AporteJefe" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "AporteJefe" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return null;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	} , {
		title : GetDatosColumnas( "Actividad" , "Nombre" ) ,
		spb_menu : GetDatosColumnas( "Actividad" , "Ocultar" ) ,
		width : 150 ,
		dataType : "string" ,
		hidden : GetDatosColumnas( "Actividad" , "Hidden" ) ,
		editable : false,
		dataIndx : "Actividad" ,
		filter : {
			type : 'textbox' ,
			condition : 'contain' ,
			listeners : [ {
				change : function(evt , ui) {
					filter( txtGrid , "Actividad" , $( this ).val( ) );
				}
			} ]
		} ,
		editor : {
			type : 'select' ,
			options : function(ui) {
				return null;
			}
		} ,
		validations : [ {
			type : 'minLen' ,
			value : 1 ,
			msg : "Required"
		} ]
	}  ];

	return lista;
}