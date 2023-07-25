// UTF8 ÇÁÑÁ

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 13/06/2017
 */
function comprobar_version_portal_old() {
    // esta funcion ya no se usa
    $.ajax({
        cache: false,
        async: true,
        method: "POST",
        url: "php/comprobarVersionPortal.php",
        data: {}
    }).done(function (data) {

        data = data.split(";");
        var versionServidor = data[0];
        var fechaCambios = data[1];
        var descCambios = data[2];

        if (parseInt(versionServidor) != parseInt(versionPortal)) {

            if (fechaCambios != '') {
                fechaCambios = "&nbsp;&nbsp;&nbsp;FECHA:&nbsp;" + fechaCambios;
            }

            var mensaje = "&nbsp;&nbsp;&nbsp;Se ha detectado que su navegador utiliza una versión menos reciente de este portal de la que se encuentra en el servidor.<br>Es necesario recargar la página para que los nuevos cambios tengan efecto.<BR><BR> Pulse actualizar para recargar la página.<BR><BR><B><FONT COLOR='BLUE'>VER.ACT: " + versionServidor + fechaCambios + "</FONT></B></p><p style='color:red;text-align:justify';><b>" + descCambios + "</b></p>";
            $("#dialog-notificar").attr('title', 'Aviso de Actualización');
            $("#dialog-notificar").html('<p style="text-align:justify"><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/update.png' width=\"50\" height=\"50\" />" + mensaje);
            $("#dialog-notificar").dialog({
                title: 'Aviso de Actualización',
                resizable: false,
                height: "auto",
                width: 900,
                modal: true,
                buttons: {
                    "LOG DE CAMBIOS": function () {
                        window.open("php/cambios.php");
                    },
                    "ACTUALIZAR": function () {
                        $.ajax({
                            cache: false,
                            async: true,
                            method: "POST",
                            url: "php/comprobarVersionPortal.php",
                            data: {}
                        }).done(function (data) {
                            $(this).dialog("close");
                            location.reload(true);
                        });


                    }
                }
            });
        }

    });
}

function comprobar_version_portal() {
    // primero hacemos una primera llamada, solo para conocer la versión que tiene el servidor
    // no ponemos ningún parametro en la llamada
    $.ajax({
        cache: false,
        async: true,
        method: "POST",
        url: "php/comprobarVersionPortal.php",
        data: {}
    }).done(function (data) {
        data = data.split(";");
        var versionServidor = data[0];

        // vamos a comprobar que lo que ha llegado no es un error comprobando que la version es un entero
        // antes pasaba que se mostraba el error e el mensaje
        if (isNaN(versionServidor)) {
            // No hacemos nada, la consulta ha devuelto un posible error
        } else {
            // Actualizamos la version del portal
            versionPortal = versionServidor; // Tambien se actualiza en control_acceso.php, un include al iniciar el portal

            // Si la versión del usuario es inferior a la del servidor se pide actualizar
            if (versionPortalUsuario != -1 && versionPortalUsuario < versionPortal) {
                $.ajax({
                    cache: false,
                    async: true,
                    method: "POST",
                    url: "php/comprobarVersionPortal.php",
                    data: {
                        usuario: usuariovalidado,
                        versionPortalUsuario: versionPortalUsuario
                    }
                }).done(function (data) {
                    data = data.split(";");
                    var versionServidor2 = data[0];
                    var fechaCambios = data[1];
                    var descCambios = data[2];

                    if (fechaCambios != '') {
                        fechaCambios = "&nbsp;&nbsp;&nbsp;FECHA:&nbsp;" + fechaCambios;
                    }

                    var mensaje = "&nbsp;&nbsp;&nbsp;Se ha detectado que su navegador utiliza una versión menos reciente de este portal de la que se encuentra en el servidor.<br>Es necesario recargar la página para que los nuevos cambios tengan efecto.<BR><BR> Pulse actualizar para recargar la página.<BR><BR><B><FONT COLOR='BLUE'>VER.ACT: " + versionServidor + fechaCambios + "</FONT></B></p><p style='color:red;text-align:justify';>" + descCambios + "</p>";
                    $("#dialog-notificar").attr('title', 'Aviso de Actualización');
                    $("#dialog-notificar").html('<p style="text-align:justify"><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/update.png' width=\"50\" height=\"50\" />" + mensaje);
                    $("#dialog-notificar").dialog({
                        title: 'Aviso de Actualización',
                        resizable: false,
                        height: "auto",
                        width: 900,
                        modal: true,
                        buttons: {
                            "LOG DE CAMBIOS": function () {
                                window.open("php/cambios.php");
                            },
                            "CANCELAR": function () {
                                $(this).dialog("close");
                            },
                            "ACTUALIZAR": function () {
                                $(this).dialog("close");
                                var target = document.getElementById('micuerpo');
                                var spinner = new Spinner(opts).spin(target);
                                $.ajax({
                                    cache: false,
                                    async: true,
                                    method: "POST",
                                    url: "php/comprobarVersionPortal.php",
                                    data: {
                                        usuario: usuariovalidado,
                                        nuevaVersion: (versionPortalUsuario + 1)
                                    }
                                }).done(function (data) {
                                    spinner.stop( );
                                    location.reload(true);
                                });
                            }
                        }
                    });
                });
            }
        }
    });
}

function actualiza_fecha_conexion() {
    $.ajax({
        cache: false,
        async: true,
        method: "POST",
        url: "php/comprobarVersionPortal.php",
        data: {
            usuario: usuariovalidado,
            setDate: 1
        }
    }).done(function (data) {
        //spinner.stop( );
        //location.reload(true);
    });
}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 13/10/2017
 * @description
 * @param texto
 */
function limpiarTextoOffice(texto) {
    var tmp = document.createElement("DIV");
    tmp.innerHTML = texto;
    var textoLimpio = tmp.textContent || tmp.innerText;
    textoLimpio = textoLimpio.replace(/\n\n/g, "<br />").replace(/.*<!--.*-->/g, "");
    for (i = 0; i < 10; i++) {
        if (textoLimpio.substr(0, 6) == "<br />") {
            textoLimpio = textoLimpio.replace("<br />", "");
        }
    }
    return textoLimpio;
}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 24/05/2017
 * @description
 * @param titulo
 * @param mensaje
 * @param [icono]
 */
function mensaje(titulo, mensaje, icono) {

    if (icono == undefined) {
        icono = "user.png";
    }
    $("#dialog-notificar").attr('title', titulo);
    $("#dialog-notificar").html('<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/" + icono + "' width=\"30\" height=\"30\" />&nbsp;&nbsp;&nbsp;" + mensaje + '</p>');
    $("#dialog-notificar").dialog({
        title: titulo,
        resizable: false,
        height: "auto",
        width: 500,
        modal: true,
        buttons: {
            "Cerrar": function () {
                $(this).dialog("close");
            }
        }
    });
}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 14/07/2016
 * @param mensaje
 */
function dialogo(mensaje) {
    var dialogo = document.getElementById('dialog-notificar');
    dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/user.png' width=\"30\" height=\"30\" />&nbsp;&nbsp;&nbsp;" + mensaje + '</p>';
    $("#dialog-notificar").dialog({
        resizable: false,
        height: 250,
        width: 400,
        modal: true,
        position: [100, 380],
        buttons: {
            "Cerrar": function () {
                $(this).dialog("close");
            }
        }
    });
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * Función que emite un alert solo para un usuario determinado
 * 
 * @param esto
 */
function alertame(esto) {
    if (usuariovalidado == 'alberto.ruiz') {
        alert(esto);
    }
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @desc comprueba si el valor pasado es un entero
 * @param value
 * @returns
 */
function esEntero(value) {
    return !isNaN(value) && (function (x) {
        return (x | 0) === x;
    })(parseFloat(value))
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 25/05/2017
 * @param obj
 * @param usuario
 */
function modificar_tarea_prefijada(obj, usuario) {

    var tarea = obj.value;
    tarea = tarea.split(" - ");
    var idtarea = tarea[0];

    if (isNaN(idtarea)) {
        mensaje('Error', 'Elija una tarea de la lista de tareas.');
        cambia_menu(11);
        return;
    }

    var borrar = 0;
    // if (tarea[0] == undefined && tarea[1] == undefined){
    if (obj.value == '') {
        borrar = 1;
    }

    var numero = obj.id;
    numero = numero.split("_");
    numero = numero[1];

    var url = 'select/modificarTareaPrefijada.php?nombreusuario=' + encodeURIComponent(nombreusuariovalidado) + '&usuario=' + encodeURIComponent(usuariovalidado) + "&numero=" + numero + "&tarea=" + idtarea + "&borrar=" + borrar;
    $.ajax({
        url: url
    }).done(function (data) {

        if (data == '-1') {
            mensaje('Error', 'La tarea no existe o pertenece a otro usuario.');
        }
        if (data == '-2') {
            mensaje('Mensaje', 'La tarea prefijada número ' + numero + ' ha sido borrada.');
        }

        cambia_menu(11);
    });

}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 01/06/2017
 * @param obj
 * @param usuario
 */
function modificar_alias_tarea_prefijada(obj, usuario) {

    var alias = obj.value;
    var borrar = 0;
    // if (tarea[0] == undefined && tarea[1] == undefined){
    if (alias == '') {
        borrar = 1;
    }

    var numero = obj.id;
    numero = numero.split("_");
    numero = numero[1];

    var url = 'select/modificarTareaPrefijada.php?nombreusuario=' + encodeURIComponent(nombreusuariovalidado) + '&usuario=' + encodeURIComponent(usuariovalidado) + "&numero=" + numero + "&alias=" + encodeURIComponent(alias) + "&borrar=" + borrar;
    $.ajax({
        url: url
    }).done(function (data) {

        if (data == '-3') {
            mensaje('Mensaje', 'El alias de la tarea prefijada número ' + numero + ' ha sido borrado.');
        }
        CargaBannerFrecuentes();
        cambia_menu(11);
    });

}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 25/05/2017
 * @param numero
 */
function IrATareaPrefijada(numero) {

    var url = 'select/getTareaPrefijada.php?usuario=' + encodeURIComponent(usuariovalidado) + "&numero=" + numero;
    $.ajax({
        url: url
    }).done(function (data) {
        if (data == '-1' || data == '') {
            mensaje('Error', 'La tarea no existe o no ha prefijado el acceso: ALT+' + numero);
        } else {
            // Cambiamos la Id seleccionada
            ID_SELECCIONADA = data;
            // Abrimos la tarea seleccionada
            cambia_menu(3);
            // Fichamos en la tarea
            IniciarLaTarea( );
        }
    });
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 01/06/2017
 */
function CargaBannerFrecuentes() {
    var url = 'select/obtenerPrefijadasBanner.php?usuario=' + encodeURIComponent(usuariovalidado);
    $.ajax({
        url: url
    }).done(function (data) {
        $("#resumen_frecuentes").html(data);
    });
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 01/06/2017
 * @param tarea
 */
function IniciarFrecuente(tarea) {
    ID_SELECCIONADA = tarea;
    cambia_menu(3);
    IniciarLaTarea( );
}

function valoracion_a_usuario(obj, tipo, tarea) {
    var url = 'valoracion/modificarvaloracionusuario.php?usuario=' + encodeURIComponent(usuariovalidado) + "&tipo=" + tipo + "&valor=" + obj.value + "&tarea=" + tarea;
    $.ajax({
        url: url
    }).done(function () {

    });
}

function manda_correo(obj) {
    var imagen = obj.id;
    if (imagen == 'correo_informar') {
        $.ajax({
            url: "mails/mail.php?tipo=correo_informar&tarea=" + ID_SELECCIONADA + '&usuario=' + encodeURIComponent(usuariovalidado),
            success: function (response) {
                var wnd = window.open(response);
                setTimeout(function () {
                    wnd.close();
                }, 1000);

            }
        });
    }
}

function Banner(menu_pasado) {
    $.ajax({
        url: "banner/banner.php?usuario=" + encodeURIComponent(usuariovalidado) + "&menu=" + encodeURIComponent(menu_pasado),
        success: function (response) {
            if (response != "") {
                var valores = response.split("|");

                $.ajax({
                    url: valores[1],
                    success: function (response) {
                        $("#banner").html(response);
                        $("#banner").dialog({
                            resizable: true,
                            title: valores[0],
                            width: 700,
                            height: 500,
                            modal: false,
                            buttons: {
                                "No mostrar más": function () {
                                    $(this).dialog("close");
                                    $.ajax({
                                        url: "banner/no_banner.php?usuario=" + encodeURIComponent(usuariovalidado) + "&banner=" + encodeURIComponent(valores[2]),
                                        success: function (response) {

                                        }
                                    });
                                }
                            },
                            open: function (event, ui) {
                                var slider = $('.slider')
                                slider._TMS({
                                    items: '.items li',
                                    pagination: true,
                                    playBlock: true,
                                    slideShow: 7000,
                                    progressBar: true,
                                    banners: true,
                                    playBu: '.play',
                                    prevBu: '.prev',
                                    nextBu: '.next',
                                    pauseOnHover: true,
                                    numStatus: false,
                                    bannerMeth: 'custom',
                                    reverseWay: false,
                                    duration: 400,
                                    interval: 40,
                                    blocksX: 12,
                                    blocksY: 6,
                                    easing: "easeInQuad",
                                    way: "diagonal",
                                    anim: "expand",
                                    preset: 'diagonalFade',
                                    beforeAnimation: function (banner) {
                                        if (banner)
                                            banner.stop().animate({
                                                left: -banner.width(),
                                                opacity: 0
                                            }, 500, 'easeInBack')
                                    },
                                    afterAnimation: function (banner) {
                                        if (banner)
                                            banner.hide().fadeIn(1000)
                                    }
                                });
                            }
                        });
                    }
                });
            }
        }
    });
}

function Buscador() {
    var devolver = '';
    devolver = devolver + '<label for="opciones_busqueda">Buscar en</label>';
    devolver = devolver + '<select id="opciones_busqueda" multiple="multiple" style="width: 250px;">';
    devolver = devolver + '<option selected value="Titulo">Titulo</option>';
    devolver = devolver + '<option selected value="Observaciones">Observaciones</option>';
    devolver = devolver + '<option selected value="Horas">Horas</option>';
    devolver = devolver + '<option selected value="Comentarios">Comentarios</option>';
    devolver = devolver + '<option selected value="Adjuntos">Adjuntos</option>';
    devolver = devolver + '<option selected value="Valoracion">Valoracion</option>';
    devolver = devolver + '</select>';

    // **********
    $("#dialog-iniciar").html('Buscador<input type="text" style="width:70%;" name="casilla_buscar_palabras" id="casilla_buscar_palabras" value=""><input style="width:20%;"  type="button" id="btn_buscar_palabras" value="Buscar" onClick="iniciar_Buscar_Palabras();">' + devolver + '<div id="resultado-busqueda" name="resultado-busqueda"></div>');
    $("#dialog-iniciar").dialog({
        resizable: true,
        title: 'Buscador',
        width: 800,
        height: 800,
        modal: false,
        open: function (event, ui) {
            $('#opciones_busqueda').multipleSelect();
        }
    });
    // **********
}

function iniciar_Buscar_Palabras() {
    palabra_a_buscar = $("#casilla_buscar_palabras").val();
    if (palabra_a_buscar != '') {
        var opciones_busqueda = $("#opciones_busqueda").multipleSelect("getSelects");
        if (opciones_busqueda.length > 0) {
            var opcion1 = '0';
            var opcion2 = '0';
            var opcion3 = '0';
            var opcion4 = '0';
            var opcion5 = '0';
            var opcion6 = '00';
            for (a = 0; a < opciones_busqueda.length; a++) {
                if (opciones_busqueda[a] == 'Titulo') {
                    opcion1 = '1';
                }
                if (opciones_busqueda[a] == 'Observaciones') {
                    opcion2 = '1';
                }
                if (opciones_busqueda[a] == 'Horas') {
                    opcion3 = '1';
                }
                if (opciones_busqueda[a] == 'Comentarios') {
                    opcion4 = '1';
                }
                if (opciones_busqueda[a] == 'Adjuntos') {
                    opcion5 = '1';
                }
                if (opciones_busqueda[a] == 'Valoracion') {
                    opcion6 = '11';
                }
            }
            lista_opciones_seleccionada = opcion6 + opcion5 + opcion4 + opcion3 + opcion2 + opcion1;

        } else {
            lista_opciones_seleccionada = '0000000';
        }
        Buscar_Palabras(1);
    }
}

function Buscar_Palabras(paginapasada) {
    $.ajax({
        url: "buscador/verBuscador.php?usuario=" + encodeURIComponent(usuariovalidado) + "&departamento=" + encodeURIComponent(departamentousuariovalidado) + "&frase=" + encodeURIComponent(palabra_a_buscar) + "&opciones=" + encodeURIComponent(lista_opciones_seleccionada) + "&pagina=" + paginapasada + "&registros=20",
        success: function (response) {
            // **********
            $("#resultado-busqueda").html(response);
            // **********
        }
    });
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 13/07/2017
 * @description poner en el título de la pestaña la tarea en la que fichas
 */
function ActualizarTitulo() {
    $.ajax({
        url: "visibilidad/obtener_titulo.php",
        type: "POST",
        data: {id: ID_SELECCIONADA},
        success: function (data) {
            if (data != '') {
                document.title = data.toUpperCase();
            }
        }
    });
}

function IniciarLaTarea() {
    if (ID_SELECCIONADA != '-1') {
        IniciarTarea();

    }
}

function IniciarTarea() {

    // OJO: Este código está replicado en el componente angular tareas.ts

    var posible = buscarpermiteiniciartarea(ID_SELECCIONADA);

    if (parseInt(posible) >= 1) {
        // **********
        if (parseInt(posible) == 1) {
            mensaje('Prohibido Iniciar Tarea', 'No se puede iniciar una tarea cerrada.', 'alert.png');
        }
        if (parseInt(posible) == 2) {
            mensaje('Prohibido Iniciar Tarea', 'Hace falta el usuario asignado.', 'alert.png');
        }
    } else {
        var lista = buscartareahoraabierta();

        var res = lista.split("|");
        if (lista.length > 3) {

            // **********
            $("#dialog-iniciar").html('<img style="width:20px;height:20px;" src="./imagenes/alert.png">' + res[2] + "¿Desea cerrar esta tarea antes?");
            $("#dialog-iniciar").dialog({
                resizable: false,
                title: 'Iniciar Tarea',
                width: 400,
                height: 200,
                modal: false,
                buttons: {
                    "Ok": function () {
                        $(this).dialog("close");

                        if (res[1] == '0') {


                            // **********
                            $("#dialog-confirm").html('<img style="width:20px;height:20px;" src="./imagenes/alert.png">La tarea se abrió otro día.¿Se le olvidó cerrarla?');
                            $("#dialog-confirm").dialog({
                                resizable: false,
                                title: 'Cerrar la Tarea',
                                width: 400,
                                height: 200,
                                modal: false,
                                buttons: {
                                    "Si": function () {
                                        $(this).dialog("close");
                                        ID_HORA_SELECCIONADA = res[0];
                                        ID_HORA_SELECCIONADA_ESTADO = res[1];
                                        cierrotareahora(1);
                                        iniciotareahora();
                                        ActualizarTitulo();

                                    },
                                    "No": function () {
                                        $(this).dialog("close");
                                        ID_HORA_SELECCIONADA = res[0];
                                        ID_HORA_SELECCIONADA_ESTADO = res[1];
                                        cierrotareahora(2);
                                        iniciotareahora();
                                        ActualizarTitulo();

                                    }
                                }
                            });
                            // **********

                        } else {
                            ID_HORA_SELECCIONADA = res[0];
                            ID_HORA_SELECCIONADA_ESTADO = res[1];
                            cierrotareahora(2);
                            iniciotareahora();
                            ActualizarTitulo();

                        }

                    },
                    "Cancelar": function () {
                        $(this).dialog("close");
                        ID_PLAN_SELECCIONADA = -1;
                    }
                }
            });
            // **********


        } else {
            iniciotareahora();
            ActualizarTitulo();
        }

    }

}


function PararTarea() {
    var lista = buscartareahoraabierta();
    var res = lista.split("|");
    if (lista.length > 3) {
        if (res[1] == '0') {
            var dialogo = document.getElementById('dialog-iniciar');
            dialogo.innerHTML = '<p><span class="ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/info.png' width=\"30\" height=\"30\" />&nbsp;" + 'La tarea se abrió en otro día.¿Se le olvido cerrarla?</p>';
            $("#dialog-iniciar").dialog({
                resizable: false,
                height: 200,
                width: 400,
                modal: true,

                buttons: {
                    "Si": function () {
                        $(this).dialog("close");
                        ID_HORA_SELECCIONADA = res[0];
                        ID_HORA_SELECCIONADA_ESTADO = res[1];
                        cierrotareahora(1);
                        $("#grid_array").pqGrid("refreshDataAndView");
                    },
                    "No": function () {
                        $(this).dialog("close");

                        ID_HORA_SELECCIONADA = res[0];
                        ID_HORA_SELECCIONADA_ESTADO = res[1];
                        cierrotareahora(2);
                        $("#grid_array").pqGrid("refreshDataAndView");
                    }

                }
            });
        } else {
            ID_HORA_SELECCIONADA = res[0];
            ID_HORA_SELECCIONADA_ESTADO = res[1];
            cierrotareahora(2);
            $("#grid_array").pqGrid("refreshDataAndView");
        }
        var micuerpo1 = document.getElementById('misecciondatos');
        if (micuerpo1.style.display != 'block') {
            ID_SELECCIONADA = '-1';
        }


    } else {
        var dialogo = document.getElementById('dialog-iniciar');
        dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/info.png' width=\"30\" height=\"30\" />&nbsp;" + 'La tarea no esta abierta</p>';
        $("#dialog-iniciar").dialog({
            resizable: false,
            height: 250,
            modal: true,

            buttons: {
                "Ok": function () {
                    $(this).dialog("close");
                    var micuerpo1 = document.getElementById('misecciondatos');
                    if (micuerpo1.style.display != 'block') {
                        ID_SELECCIONADA = '-1';
                    }
                }
            }
        });
    }
    crearslidesemanal(2);
}
function ObtenerMenuContextualNavegador() {
    var text_menu = '';
    text_menu = '<div onClick="BorrarCache();" style="cursor: pointer;"><img src="./imagenes/tv.png" width="30" height="30" />Borrar la caché de pantalla</div>';
    text_menu = text_menu + '<div onClick="BorrarLista();" style="cursor: pointer;"><img src="./imagenes/file.png" width="30" height="30" />Borrar la lista historico</div>';
    text_menu = text_menu + '<div onClick="BorrarMarcadas();" style="cursor: pointer;"><img src="./imagenes/flag_black.png" width="30" height="30" />Borrar la lista marcadas</div>';
    text_menu = text_menu + '<div onClick="irActual(1);" style="cursor: pointer;"><img src="./imagenes/point.png" width="30" height="30" />Ir a Tarea actual</div>';
    return text_menu;
}

function irActual(id) {

    $.ajax({
        url: "select/getActual.php?usuario=" + encodeURIComponent(usuariovalidado),
        success: function (response) {
            // alert(id + '-' + response);
            if (response == '-1' || response == '') {
                // **********
                $("#dialog-cancelar").html('<img src="imagenes/alert.png" style="width:30px;height:30px"> No tiene ninguna tarea iniciada actualmente');
                $("#dialog-cancelar").dialog({
                    resizable: false,
                    title: 'Columnas',
                    width: 300,
                    height: 200,
                    modal: true,
                    buttons: {
                        "Ok": function () {
                            $(this).dialog("close");
                        }
                    }
                });
                // **********
            } else {
                if (id == 1) {
                    $("#dialog-iniciar").dialog("close");
                } else {
                    ID_SELECCIONADA = response;
                    cambia_menu(3);
                }

                // $("#cabecera_nueva_der").html("<h3>CONSULTA / EDICIÓN DE TAREA</h3>");
                // selecciono_tarea_historial(response, 1);
            }
        }
    });
}

function BorrarLista() {
    var url = 'select/borrarlista.php?usuario=' + encodeURIComponent(usuariovalidado) + "&marcada=0";
    $.ajax({
        url: url
    }).done(function () {});
}

function BorrarMarcadas() {
    var url = 'select/borrarlista.php?usuario=' + encodeURIComponent(usuariovalidado) + "&marcada=1";
    $.ajax({
        url: url
    }).done(function () {});
}

function BorrarCache() {
    var url = 'select/borrarcache.php?usuario=' + encodeURIComponent(usuariovalidado);
    $.ajax({
        url: url
    }).done(function () {
        $("#dialog-iniciar").dialog("close");
        dialogo('Se ha inicializado la configuración por defecto del grid de tareas, pulse actualizar la página.');
    });
}

function getURLvar(var_name) {
    // Funcion para obtener de la URL una variable
    var re = new RegExp(var_name + "(?:=([^&]*))?", "i");
    var pm = re.exec(decodeURIComponent(location.search));
    if (pm === null)
        return " ";
    return pm[1] || "";
}

function nuevoAjax() {
    // Funcion para ejecutar los AJAX
    var xmlhttp = false;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
                xmlhttp = new XMLHttpRequest();
        }
    }
    return xmlhttp;
}

function ver_arbol() {
    $.ajax({
        url: "select/verArbol.php?usuario=" + encodeURIComponent(usuariovalidado) + "&departamento=" + encodeURIComponent(departamentousuariovalidado) + "&tarea=" + ID_SELECCIONADA + "&oficina=" + encodeURIComponent(oficinausuariovalidado) + "&nombreusuariovalidado=" + encodeURIComponent(nombreusuariovalidado),
        success: function (response) {
            // **********
            $("#dialog-iniciar").html(response);
            $("#dialog-iniciar").dialog({
                resizable: false,
                title: 'Arbol de la Tarea',
                width: 600,
                height: 400,
                modal: false,
                buttons: {
                    "Cerrar": function () {
                        $(this).dialog("close");

                    }
                }
            });
            // **********
        }
    });
}

function ver_avisos() {
    $.ajax({
        url: "select/verAvisos.php?usuario=" + encodeURIComponent(usuariovalidado) + "&departamento=" + encodeURIComponent(departamentousuariovalidado),
        success: function (response) {
            // **********
            $("#dialog-iniciar").html(response);
            $("#dialog-iniciar").dialog({
                resizable: false,
                title: 'Avisos',
                width: 600,
                height: 400,
                modal: false,
                open: function (event, ui) {
                    $(".PLEGAR_AVISO").animate({
                        height: "1px"
                    });
                },
                buttons: {
                    "Ok": function () {
                        $(this).dialog("close");

                    },
                    "Limpiar": function () {
                        $(this).dialog("close");
                        var url = 'select/borraravisos.php?usuario=' + encodeURIComponent(usuariovalidado);
                        $.ajax({
                            url: url
                        }).done(function () {
                            Actualiza_Avisos();
                        });
                    }
                }
            });
            // **********
        }
    });
}

function Actualiza_Avisos() {
    $.ajax({
        url: "select/getAvisos.php?usuario=" + encodeURIComponent(usuariovalidado),
        success: function (response) {

            if (response == "1") {
                $("#imagen_avisos").attr("src", "./imagenes/star.png");
            } else {
                if (response == "2") {
                    $("#imagen_avisos").attr("src", "./imagenes/user.png");
                } else {
                    $("#imagen_avisos").attr("src", "./imagenes/Transparent.png");
                }
            }
        }
    });
}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @desc Función que controla el cambio de pantallas
 * @param id
 * 
 * @date 01/03/2016, Alberto, se cambia toda la función para usar jquery y se separan todos los ocultamientos a función externa (ocultar_secciones) para evitar código duplicado dificil de mantener
 */
function cambia_menu(id) {

    if ($("#formulario").data("changed")) {
        if (!confirm('Hay datos sin guardar, ¿salir de todos modos?')) {
            id = 0;
        } else {
            $("#formulario").data("changed", false);
        }
    }

    if (id > 0) {

        var inserta_alta_usuario;
        inserta_alta_usuario = 0;
        if (id == 14) {
            inserta_alta_usuario = 1;
            id = 4;
        }
        if (id == 16) {
            inserta_alta_usuario = 1;
            id = 6;
        }

        ID_PLAN_SELECCIONADA = -1;


        actualiza_fecha_conexion();


        Actualiza_Avisos();
        ocultar_secciones();

        // Alternancia de MENUS
        if (id == 1) {
            // ALBERTO T#14893 17/02/2016
            if (ID_SELECCIONADA > 0) {
                var control = 0;
                $.ajax({
                    cache: false,
                    async: false,
                    method: "POST",
                    url: "select/consulta_si_borrada.php",
                    data: {
                        tarea: ID_SELECCIONADA
                    }
                }).done(function (msg) {
                    control = msg;
                });

                if (control == 1) {
                    $("#dialog-iniciar").html('<img style="width:20px;height:20px;" src="./imagenes/user.png">No ha guardado la tarea, si sale ahora perderá los cambios.<br><br>¿Descartar tarea?');
                    $("#dialog-iniciar").dialog({
                        resizable: false,
                        title: 'Confirmar salir',
                        width: 600,
                        height: 200,
                        modal: true,
                        buttons: {
                            "Si": function () {
                                $(this).dialog("close");
                                salirtarea(1);
                            },
                            "No": function () {
                                $(this).dialog("close");
                                salirtarea(0);
                            }
                        }
                    });
                } else {
                    salirtarea(1);
                }
            } else {
                salirtarea(1);
            }
        }

        if (id == 2) {
            $("#miseccioninformes").css("display", "block");
        }

        if (id == 3) {
            $("#misecciondatos").css("display", "block");
            menu_incidencia(1);
            $("#cabecera_nueva_der").html("<h3>CONSULTA / EDICIÓN DE TAREA</h3>");
        }
        // NUEVA TAREA
        if (id == 4) {
            $("#misecciondatos").css("display", "block");
            if (inserta_alta_usuario == 1) {
                nuevo_menu_incidencia(11);
            } else {
                nuevo_menu_incidencia(1); // nueva tarea
            }
        }
        if (id == 5) {
            // Datos
            $("#misecciondatos").css("display", "block");

            menu_incidencia(2);
        }
        if (id == 6) {
            ocultar_secciones();
            $("#misecciondatos").css("display", "block");

            if (inserta_alta_usuario == 1) {
                nuevo_menu_incidencia(21);
            } else {
                nuevo_menu_incidencia(2);
            }
        }
        if (id == 7) {
            $("#cabecera_nueva_der").html("<h3>CONFIGURACIÓN</h3>");
            $("#miseccionconfiguracion").css("display", "block");
            menu_configuracion();
        }

        if (id == 8) {
            $("#cabecera_nueva_der").html("<h3>INFORMES</h3>");
            $("#miseccionlistamenu").css("display", "block");
            menu_lista_informes();
        }
        if (id == 9) {
            $("#cabecera_nueva_der").html("<h3>GANT</h3>");
            $("#misecciongant").css("display", "block");
            spb_gantt_inicia();
        }
        if (id == 10) {
            $("#miseccionhorasenundia").css("display", "block");
            $("#cabecera_nueva_der").html("<h3>HORAS EN UN DÍA</h3>");
        }
        if (id == 11) {
            $("#cabecera_nueva_der").html("<h3>ACCESOS DIRECTOS A TAREAS FRECUENTES</h3>");
            $("#miseccionprefijartareas").css("display", "block");
            menu_prefijar();
        }
        if (id == 13) {
            $("#cabecera_nueva_der").html("<h3>AREA DE EVALUACIONES</h3>");
            // los externos pueden acceder al menu de evlauadores externos pestaña 1
            if (externo == '') {
                // $( "#tabs-evaluacion-masiva" ).tabs( "option", "active", 0 );
                // $( "#tabs-evaluacion-masiva" ).tabs( "option", "disabled", [ 1 ] );
            } else {
                // $( "#tabs-evaluacion-masiva" ).tabs( "option", "active", 1 );
                // $( "#tabs-evaluacion-masiva" ).tabs( "option", "disabled", [ 0 ] );
                $("#fem").css("display", "none");
            }

            $("#miseccionevaluacionmasiva").css("display", "block");
            // var autorizacion_vacio = document.getElementById('miseccionevaluacionmasiva');
            // autorizacion_vacio.innerHTML = "<br><br><br><br><center><image src=./imagenes/working2.gif /></center>";

            carga_grid_eva_masiva(1);// 0- todo, 1- pendiente

        }
        if (id == 20) {
            if ($("#miseccionplanificadortareas_grid_array").html().length > 0) {
                $("#miseccionplanificadortareas_grid_array").pqGrid("destroy");
                $("#miseccionplanificadortareas_grid_array").empty();
            }
            $("#cabecera_nueva_der").html("<h3>PLANIFICADOR DE TAREAS</h3>");
            $("#miseccionplanificadortareas").css("display", "block");
            cargaGridPlanificacionesTareas();
        }
        if (id == 21) {
            $("#cabecera_nueva_der").html("<h3>AREA DE EVALUACIONES</h3>");
            $("#miseccionevaluacionesusuarios").css("display", "block");
            CargaGridEvaluacionesUsuario();
        }

        if (id == 22) {
            if ($("#miseccionentradacorreo_grid_array").html().length > 0) {
                $("#miseccionentradacorreo_grid_array").pqGrid("destroy");
                $("#miseccionentradacorreo_grid_array").empty();
            }
            $("#cabecera_nueva_der").html("<h3>CREACION DE ENTRADA DE CORREOS</h3>");
            $("#miseccionentradacorreo").css("display", "block");
            CargaGridEntradaCorreo();
        }

    }
}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author Alberto
 * @date 17/02/2016
 * 
 * @param salir
 */
function salirtarea(salir) {

    if (salir == 1) {

        ocultar_secciones();

        $("#miseccion").css("display", "block");

        ID_SELECCIONADA = '-1';
        ID_HORA_SELECCIONADA = '-2';
        ID_HORA_SELECCIONADA_ESTADO = '-2';
        ID_COMENTARIO_SELECCIONADO = '-2';
        $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
        if (antesdehistorial > -1) {
            $("#grid_array").pqGrid("option", "pageModel.rPP", antesdehistorial);
            $("#grid_array").pqGrid("option", "pageModel.rPPOptions", antesdehistorialOptions);

        }
        $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
        $("#grid_array").pqGrid("refreshDataAndView");
        $("#cabecera_nueva_der").html("<h3>LISTA DE TAREAS</h3>");
    } else {
        cambia_menu(3);
    }
}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @desc oculta todas las secciones del portal
 * @author Alberto
 * 
 * @date 01/03/2016
 */
function ocultar_secciones() {
    // Aprobaciones
    $("#miseccion").css("display", "none");
    // Datos
    $("#misecciondatos").css("display", "none");
    // Lista de Informes
    $("#miseccionlistamenu").css("display", "none");
    // Configuracion
    $("#miseccionconfiguracion").css("display", "none");
    // Gant
    $("#misecciongant").css("display", "none");
    // Informes
    $("#miseccioninformes").css("display", "none");
    // Horas en un dia
    $("#miseccionhorasenundia").css("display", "none");
    // misecciondatosmasivo
    $("#misecciondatosmasivo").css("display", "none");
    // miseccionprefijartareas
    $("#miseccionprefijartareas").css("display", "none");
    // miseccionevaluacionmasiva
    $("#miseccionevaluacionmasiva").css("display", "none");
    // miseccionplanificador
    $("#miseccionplanificador").css("display", "none");
    // miseccionplanificadortarea
    $("#miseccionplanificadortarea").css("display", "none");
    // miseccionplanificadortareas
    $("#miseccionplanificadortareas").css("display", "none");
    // miseccioncolaboradortarea
    $("#miseccioncolaboradortarea").css("display", "none");
    // miseccionevaluacionesusuarios
    $("#miseccionevaluacionesusuarios").css("display", "none");
    // miseccionentradacorreo
    $("#miseccionentradacorreo").css("display", "none");
}

function nuevo_menu_incidencia(id) {

    var target = document.getElementById('micuerpo');
    var spinner = new Spinner(opts).spin(target);

    var usuario = usuariovalidado; // document.getElementById('usuariovalidado');

    var ajax = nuevoAjax();
    if (id == 1) { // Nueva tarea
        ajax.open("POST", "select/insertar_incidencia.php?ext=" + encodeURIComponent(externo) + "&usuario=" + encodeURIComponent(usuario) + '&ti=1&altus=0', false);
    }
    if (id == 2) {
        ajax.open("POST", "select/insertar_incidencia.php?ext=" + encodeURIComponent(externo) + "&usuario=" + encodeURIComponent(usuario) + '&ti=0&altus=0', false);
    }
    if (id == 11) {
        ajax.open("POST", "select/insertar_incidencia.php?ext=" + encodeURIComponent(externo) + "&usuario=" + encodeURIComponent(usuario) + '&ti=1&altus=1', false);
        id = 1;
    }
    if (id == 21) {
        ajax.open("POST", "select/insertar_incidencia.php?ext=" + encodeURIComponent(externo) + "&usuario=" + encodeURIComponent(usuario) + '&ti=0&altus=1', false);
        id = 2;
    }
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {
            // Respuesta
            lista = (ajax.responseText);
            // lista = 25000;
            if (Number.isInteger(parseInt(lista))) {
                spinner.stop( );
                // Cargamos los datos en HTML. En el DIV correspondiente
                ID_SELECCIONADA = lista;
                menu_incidencia(id);
            } else {
                spinner.stop( );
                mensaje('Error', 'Se ha producido un error al crear la tarea con el siguiente mensaje:<br>' + lista + '<br>(Puede hacer una captura de esta pantalla para informar de esta incidencia a TI)', 'alert.png');
                cambia_menu(1);
            }
        }
    }
    ajax.send(null);
}

function selecciono(obj, id) {
    $(".visibilidad_tipo_1").attr("src", "./imagenes/Transparent.png");
    $(".visibilidad_tipo_1").attr("width", "20");
    $(".visibilidad_tipo_1").attr("height", "20");
    $("#" + obj).attr("src", "./imagenes/RedCheckBox.png");
    $("#" + obj).attr("width", "20");
    $("#" + obj).attr("height", "20");
    // Tipo Visibilidad que afecta a usuario y departamento
    // 1--Todas
    // 2--Departamento
    // 3--Mias
    tipo_visibilidad_1 = id;
    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");
}

function selecciono_participo(obj, id) {
    $(".visibilidad_tipo_2").attr("src", "./imagenes/Transparent.png");
    $(".visibilidad_tipo_2").attr("width", "20");
    $(".visibilidad_tipo_2").attr("height", "20");
    if (id == tipo_visibilidad_2) {
        tipo_visibilidad_2 = '1';
    } else {
        $("#" + obj).attr("src", "./imagenes/RedCheckBox.png");
        $("#" + obj).attr("width", "20");
        $("#" + obj).attr("height", "20");
        // Tipo Visibilidad que afecta a desarrollo
        // 1--Todas
        // 2--Participo
        tipo_visibilidad_2 = id;
    }
    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");
}

function selecciono_iniciada(obj, id) {
    $(".visibilidad_tipo_3").attr("src", "./imagenes/Transparent.png");
    $(".visibilidad_tipo_3").attr("width", "20");
    $(".visibilidad_tipo_3").attr("height", "20");
    if (id == tipo_visibilidad_3) {
        tipo_visibilidad_3 = '1';
    } else {
        $("#" + obj).attr("src", "./imagenes/RedCheckBox.png");
        $("#" + obj).attr("width", "20");
        $("#" + obj).attr("height", "20");
        // Tipo Visibilidad que afecta a desarrollo
        // 1--Todas
        // 2--Iniciadas
        tipo_visibilidad_3 = id;
    }
    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");
}

function selecciono_reciente(obj, id) {
    $(".visibilidad_tipo_5").attr("src", "./imagenes/Transparent.png");
    $(".visibilidad_tipo_5").attr("width", "20");
    $(".visibilidad_tipo_5").attr("height", "20");
    if (id == tipo_visibilidad_5) {
        tipo_visibilidad_5 = '1';
    } else {
        $("#" + obj).attr("src", "./imagenes/RedCheckBox.png");
        $("#" + obj).attr("width", "20");
        $("#" + obj).attr("height", "20");
        // Tipo Visibilidad que afecta a reciente
        // 1--Todas
        // 2--Recientes XX dias
        tipo_visibilidad_5 = id;
    }
    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");
}

function selecciono_consulta_personalizada(obj) {
    if ($("#li_consulta_personalizada_" + obj + "_visibilidad").attr("src") == "./imagenes/Transparent.png") {
        $("#li_consulta_personalizada_" + obj + "_visibilidad").attr("src", "./imagenes/RedCheckBox.png");
        $("#li_consulta_personalizada_" + obj + "_visibilidad").attr("width", "20");
        $("#li_consulta_personalizada_" + obj + "_visibilidad").attr("height", "20");
    } else {
        $("#li_consulta_personalizada_" + obj + "_visibilidad").attr("src", "./imagenes/Transparent.png");
        $("#li_consulta_personalizada_" + obj + "_visibilidad").attr("width", "20");
        $("#li_consulta_personalizada_" + obj + "_visibilidad").attr("height", "20");
    }


    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");


}

function selecciono_pendiente(obj, id) {
    $(".visibilidad_tipo_4").attr("src", "./imagenes/Transparent.png");
    $(".visibilidad_tipo_4").attr("width", "20");
    $(".visibilidad_tipo_4").attr("height", "20");
    if (id == tipo_visibilidad_4) {
        tipo_visibilidad_4 = '1';
    } else {
        $("#" + obj).attr("src", "./imagenes/RedCheckBox.png");
        $("#" + obj).attr("width", "20");
        $("#" + obj).attr("height", "20");
        // Tipo Visibilidad que afecta a desarrollo
        // 1--Todas
        // 2--Pendiente
        tipo_visibilidad_4 = id;
    }
    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");
}

function selecciono_sininiciar(obj, id) {
    $(".visibilidad_tipo_7").attr("src", "./imagenes/Transparent.png");
    $(".visibilidad_tipo_7").attr("width", "20");
    $(".visibilidad_tipo_7").attr("height", "20");
    if (id == tipo_visibilidad_7) {
        tipo_visibilidad_7 = '1';
    } else {
        $("#" + obj).attr("src", "./imagenes/RedCheckBox.png");
        $("#" + obj).attr("width", "20");
        $("#" + obj).attr("height", "20");
        // Tipo Visibilidad que afecta a desarrollo
        // 1--Todas
        // 2--Sin Iniciar
        tipo_visibilidad_7 = id;
    }
    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");
}

function selecciono_retrasadas(obj, id) {
    $(".visibilidad_tipo_8").attr("src", "./imagenes/Transparent.png");
    $(".visibilidad_tipo_8").attr("width", "20");
    $(".visibilidad_tipo_8").attr("height", "20");
    if (id == tipo_visibilidad_8) {
        tipo_visibilidad_8 = '1';
    } else {
        $("#" + obj).attr("src", "./imagenes/RedCheckBox.png");
        $("#" + obj).attr("width", "20");
        $("#" + obj).attr("height", "20");
        // Tipo Visibilidad que afecta a desarrollo
        // 1--Todas
        // 2--Sin Asignar
        tipo_visibilidad_8 = id;
    }
    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");
}

function selecciono_colas(obj, id, descripcion) {
    $(".visibilidad_colas").attr("src", "./imagenes/Transparent.png");
    $(".visibilidad_colas").attr("width", "20");
    $(".visibilidad_colas").attr("height", "20");
    $("#" + obj).attr("src", "./imagenes/RedCheckBox.png");
    $("#" + obj).attr("width", "20");
    $("#" + obj).attr("height", "20");
    // Tipo Visibilidad que afecta a usuario y departamento
    // 1--Todas
    // 2--Mis Colas
    // 3--....
    // 4--....
    // .......
    tipo_visibilidad_A_Descripcion = "";
    if (id == '-1') {
        tipo_visibilidad_A_Descripcion = "";
    } else {
        tipo_visibilidad_A_Descripcion = descripcion;
    }
    tipo_visibilidad_A = id;
    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");
}

function selecciono_sinasignar(obj, id) {
    $(".visibilidad_tipo_6").attr("src", "./imagenes/Transparent.png");
    $(".visibilidad_tipo_6").attr("width", "20");
    $(".visibilidad_tipo_6").attr("height", "20");
    if (id == tipo_visibilidad_6) {
        tipo_visibilidad_6 = '1';
    } else {
        $("#" + obj).attr("src", "./imagenes/RedCheckBox.png");
        $("#" + obj).attr("width", "20");
        $("#" + obj).attr("height", "20");
        // Tipo Visibilidad que afecta a desarrollo
        // 1--Todas
        // 2--Sin Asignar
        tipo_visibilidad_6 = id;
    }
    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");
}

function selecciono_teletrabajo(obj, id) {
    $(".visibilidad_tipo_9").attr("src", "./imagenes/Transparent.png");
    $(".visibilidad_tipo_9").attr("width", "20");
    $(".visibilidad_tipo_9").attr("height", "20");
    if (id == tipo_visibilidad_9) {
        tipo_visibilidad_9 = '1';
    } else {
        $("#" + obj).attr("src", "./imagenes/RedCheckBox.png");
        $("#" + obj).attr("width", "20");
        $("#" + obj).attr("height", "20");
        // Tipo Visibilidad que afecta a desarrollo
        // 1--Todas
        // 2--Sin Asignar
        tipo_visibilidad_9 = id;
    }
    $("#grid_array").pqGrid("option", "title", ObtenerTitulo());
    $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros());
    $("#grid_array").pqGrid("refreshDataAndView");
}

function ObtenerRestoFiltros() {
//debugger;
    lista_personalizada = "";

    for (x = 0; x < cantidad_personalizadas.length; x++) {
        if ($("#li_consulta_personalizada_" + cantidad_personalizadas[x] + "_visibilidad").attr("src") == "./imagenes/RedCheckBox.png") {
            lista_personalizada = lista_personalizada + cantidad_personalizadas[x] + ",";
        }

    }
    lista_personalizada = lista_personalizada.substring(0, lista_personalizada.length - 1);

    boton_filtro_tareas(tipo_visibilidad_1, tipo_visibilidad_2, tipo_visibilidad_3, tipo_visibilidad_4, tipo_visibilidad_5, tipo_visibilidad_6, tipo_visibilidad_7, tipo_visibilidad_8, tipo_visibilidad_9, lista_personalizada);

    return "?usuario=" + encodeURIComponent(nombreusuariovalidado) +
            "&idusuario=" + encodeURIComponent(usuariovalidado) +
            "&externo=" + encodeURIComponent(externo) +
            "&ambito=" + encodeURIComponent(ambito) +
            "&ambito_global=" + encodeURIComponent(ambito_global) +
            "&nombreusuariovalidado=" + encodeURIComponent(nombreusuariovalidado) +
            "&idvisualizacion=" + id_visualizacion +
            "&tipo_1=" + tipo_visibilidad_1 +
            "&tipo_2=" + tipo_visibilidad_2 +
            "&tipo_3=" + tipo_visibilidad_3 +
            "&tipo_4=" + tipo_visibilidad_4 +
            "&tipo_5=" + tipo_visibilidad_5 +
            "&tipo_6=" + tipo_visibilidad_6 +
            "&tipo_7=" + tipo_visibilidad_7 +
            "&tipo_8=" + tipo_visibilidad_8 +
            "&tipo_9=" + tipo_visibilidad_9 +
            "&tipo_A=" + tipo_visibilidad_A +
            "&lista_personalizada=" + lista_personalizada;

}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 10/08/2016
 * @tarea
 * @param t1
 * @param t2
 * @param t3
 * @param t4
 * @param t5
 * @param t6
 * @param t7
 * @param t8
 * @param t9
 * @param tp
 */
function boton_filtro_tareas(t1, t2, t3, t4, t5, t6, t7, t8, t9, tp) {
    var activo = false;

    if ((t1 == 1 || t1 == 3) && t2 == 1 && t3 == 1 && t4 == 1 && t5 == 1 && t6 == 1 && t7 == 1 && t8 == 1 && t9 == 1 && tp == '') {
        $("#menu_filtro").removeClass("menu_filtro_activo");
    } else {
        $("#menu_filtro").addClass("menu_filtro_activo");
    }
}

function cambiaparametro(obj) {
    var url = "select/modificarParametro.php?id=" + encodeURIComponent(obj.id) + "&valor=" + encodeURIComponent(obj.value) + "&usuario=" + encodeURIComponent(usuariovalidado);

    $.ajax({
        url: url
    }).done(function (data) {
        if (data != '') {
            data = data.split(';');
            if (isNaN(data[0])) {
                //puede que sea un error
            } else {
                mensaje('Error en el parámetro introducido', data[1]);
            }
        }
        cambia_menu(7);
        if (obj.id == 'mi_hist_ban_tareas' && externo == '') {
            actualizar_banner_tareas();
        }
    });
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * 
 */
function menu_prefijar() {

    var target = document.getElementById('micuerpo');
    var spinner = new Spinner(opts).spin(target);

    var autorizacion_vacio = document.getElementById('miseccionprefijartareas');
    autorizacion_vacio.innerHTML = "";

    var url = "select/obtener_prefijadas.php?usuario=" + encodeURIComponent(usuariovalidado);

    $.ajax({
        url: url
    }).done(function (data) {
        spinner.stop( );
        autorizacion_vacio.innerHTML = data;

        for (i = 1; i < 10; i++) {
            if ($('#autocompleteprefijada_' + i).length) {
                $('#autocompleteprefijada_' + i).autocomplete({
                    type: 'post',
                    source: function (request, response) {
                        $.get("select/getTareaAutocomplete.php?nombreusuario=" + encodeURIComponent(nombreusuariovalidado), {
                            buscar: request.term
                        }, function (data) {
                            tags = data.split("|");
                            response(tags);
                        });
                    }
                });
            }
        }
    });
}


function menu_configuracion() {
    var autorizacion_vacio = document.getElementById('miseccionconfiguracion');
    autorizacion_vacio.innerHTML = "";

    // Si tiene ID (Puede ser que no venga con ID porque abre simplemente el portal) obtenemos los datos
    var ajax = nuevoAjax();
    var lista;

    var target = document.getElementById('micuerpo');
    var spinner = new Spinner(opts).spin(target);

    ajax.open("POST", "select/obtener_configuracion.php?usuario=" + encodeURIComponent(usuariovalidado) + "&oficina=" + oficinausuariovalidado, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {
            // Spinner
            spinner.stop();


            // Respuesta
            lista = (ajax.responseText);


            // Cargamos los datos en HTML. En el DIV correspondiente
            var autorizacion = document.getElementById('miseccionconfiguracion');
            autorizacion.innerHTML = lista;
            ID_COLA_USUARIO_SELECCIONADA = '-1';
            ID_COLA_SELECCIONADA = '-1';
            CargaGridConsultasPeronalizada();
            CargaGridAvisosPeronalizada();
            CargaGridColas();
            CargaGridColas_2();
            CargaGridActividades();
            CargaGridPreguntasFeedback();

            $("#btn_salir_configuracion").click(function () {
                cambia_menu(1);
                $("#grid_array").pqGrid("refreshDataAndView");
            });
            $("#configuracion_consultas_plegar").animate({
                height: "1px"
            });
            $("#configuracion_avisos_plegar").animate({
                height: "1px"
            });
            $("#configuracion_actividades_plegar").animate({
                height: "1px"
            });
            $("#configuracion_feedback_plegar").animate({
                height: "1px"
            });

            $("#configuracion_colas_plegar").animate({
                height: "1px"
            });
            $("#configuracion_colas_plegar_2").animate({
                height: "1px"
            });

        }
    }
    ajax.send(null);

}

function menu_lista_informes() {
    var autorizacion_vacio = document.getElementById('miseccionlistamenu');
    autorizacion_vacio.innerHTML = "";

    // Si tiene ID (Puede ser que no venga con ID porque abre simplemente el portal) obtenemos los datos
    var ajax = nuevoAjax();
    var lista;

    var target = document.getElementById('micuerpo');
    var spinner = new Spinner(opts).spin(target);

    ajax.open("POST", "select/obtener_lista_menu.php", true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {
            // Spinner
            spinner.stop();


            // Respuesta
            lista = (ajax.responseText);


            // Cargamos los datos en HTML. En el DIV correspondiente
            var autorizacion = document.getElementById('miseccionlistamenu');
            autorizacion.innerHTML = lista;
            $("#tree").fancytree();

        }
    }
    ajax.send(null);

}

function guardar_historial(id) {
    var url = "select/guarda_historial.php?tarea=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado) + "&prioridad=" + id;
    $.ajax({
        type: "JSON",
        url: url,
        success: function (data) {



        }
    });
}

function selecciono_tarea_historial(id, menu) {
    ID_SELECCIONADA = id;
    // Menu de Aprobaciones+++++++++++++++++++++++++++++++++++++++++++
    // Aprobaciones
    var micuerpo = document.getElementById('miseccion');
    micuerpo.style.display = 'none';
    // Datos
    var micuerpo1 = document.getElementById('misecciondatos');
    micuerpo1.style.display = 'block';
    // Configuracion
    var micuerpo3 = document.getElementById('miseccionconfiguracion');
    micuerpo3.style.display = 'none';
    // Informes
    var micuerpo2 = document.getElementById('miseccioninformes');
    micuerpo2.style.display = 'none';
    // Gant
    var micuerpo3 = document.getElementById('misecciongant');
    micuerpo3.style.display = 'none';
    // Horas en un dia
    var micuerpo3 = document.getElementById('miseccionhorasenundia');
    micuerpo3.style.display = 'none';
    // misecciondatosmasivo
    var micuerpo3 = document.getElementById('misecciondatosmasivo');
    micuerpo3.style.display = 'none';


    menu_incidencia(menu);

}

function selecciono_todo_historial() {
    $.ajax({
        type: "GET",
        url: "select/consultaparametro.php?usuario=" + encodeURIComponent(usuariovalidado) + "&parametro=cantidadhistorial",
        success: function (data) {
            var pageModel = $("#grid_array").pqGrid("option", "pageModel");
            var pageModelrPP = pageModel.rPP;
            var pageModelrPPOptions = pageModel.rPPOptions;
            antesdehistorial = pageModelrPP;
            antesdehistorialOptions = pageModelrPPOptions;
            $("#grid_array").pqGrid("option", "title", "Mi historial de los " + data + " recientes");
            $("#grid_array").pqGrid("option", "pageModel.rPP", parseInt(data));
            // $( "#grid_array" ).pqGrid( "option", "pageModel.rPPOptions", [parseInt(data)] );
            $("#grid_array").pqGrid("option", "dataModel.url", "select/consultaTareas.php" + ObtenerRestoFiltros() + "&historial=" + data);
            $("#grid_array").pqGrid("refreshDataAndView");
            $("#grid_array").pqGrid("option", "pageModel.totalPages", 1);
        }
    });
}

function MenuContextualBarra(destino) {

    $(destino).contextmenu({

        // We're telling the context menu widget to display
        // each time a tab is right clicked.
        // delegate: ".ui-tabs-anchor",

        // The context menu widget takes care of assembling
        // the interanl menu widget for us.
        menu: [{
                title: "Fusionar",
                cmd: "fusionar",
                uiIcon: "ui-icon-arrow-4-diag"
            },
            {
                title: "Nueva Hija",
                cmd: "hija",
                uiIcon: "ui-icon-plusthick"
            },
            {
                title: "Marcarla",
                cmd: "marcar",
                uiIcon: "ui-icon-flag"
            },
            {
                title: "Borrar",
                cmd: "borrar",
                uiIcon: "ui-icon-scissors"
            },
            {
                title: "Valoracion",
                cmd: "valoracion",
                uiIcon: "ui-icon-mail-closed"
            },
            {
                title: "Invitar",
                cmd: "invitar",
                uiIcon: "ui-icon-circle-plus"
            },
            {
                title: "Historial",
                cmd: "historial",
                uiIcon: "ui-icon-note"
            }
        ],
        // Alberto 10/07/2017, añadir 1 al z-index del parent
        beforeOpen: function (event, ui) {
            var $menu = ui.menu,
                    $target = ui.target;

            ui.menu.zIndex($(event.target).zIndex() + 1);
        },
        // Fires when a context menu item is selected.
        select: function (event, ui) {

            // This is how we know which menu item was
            // selected.
            // if ( ui.cmd !== "close" ) {
            // return;
            // }

            // The ui object has our target tab anchor that
            // was right-clicked. Now we can remove the
            // tab, the panel, and refresh the tabs widget.
            var $target = ui.target;

            if (ui.cmd == "fusionar") {
                var target_id = $target.html();

                var dialogo = document.getElementById('dialog-cancelar');
                dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/expand.png' width=\"30\" height=\"30\" />&nbsp;" + 'Seleccione una tarea a la que fusionarse.<div><input id="autocompletetareapopup" name="autocompletetareapopup" style="width:300px;" value=""></div>';

                $("#dialog-cancelar").dialog({
                    resizable: false,
                    height: 250,
                    width: 500,
                    modal: true,
                    open: function (event, ui) {
                        $("#autocompletetareapopup").autocomplete({
                            type: 'post',
                            source: function (request, response) {
                                $.get("select/getTareaautocomplete.php?tarea=" + ID_SELECCIONADA, {
                                    buscar: request.term
                                }, function (data) {
                                    tags = data.split("|");
                                    response(tags);
                                });
                            }
                        });
                    },
                    buttons: {
                        "Fusionar": function () {
                            $(this).dialog("close");
                            $("#autocompletetareapopup").submit();

                            var seleccion_texto = $("#autocompletetareapopup").val();
                            var list_id = seleccion_texto.split("-");
                            var tarea_destino = list_id[0];

                            var url = "select/fusionar_tareas.php?tarea_origen=" + ID_SELECCIONADA + "&tarea_destino=" + tarea_destino + "&usuario=" + encodeURIComponent(usuariovalidado); // El script a dónde se realizará la petición.
                            $.ajax({
                                type: "GET",
                                url: url,
                                success: function (data) {
                                    selecciono_tarea_historial(tarea_destino, 1);
                                }
                            });

                        },
                        "Cancelar": function () {
                            $(this).dialog("close");
                        }

                    }
                });

            }
            // ----
            if (ui.cmd == "hija") {
                var target_id = $target.html();

                var url = "select/hijas_tareas.php?tarea_origen=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado); // El script a dónde se realizará la petición.
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function (data) {
                        selecciono_tarea_historial(data, 1);
                    }
                });

            }
            // ----
            if (ui.cmd == "invitar") {
                var target_id = $target.html();

                var dialogo = document.getElementById('dialog-cancelar');
                dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/invitation.png' width=\"30\" height=\"30\" />&nbsp;" + 'Seleccione un usuario al que desee invitar.<div><input id="autocompleteusuariotiinvitar" name="autocompleteusuariotiinvitar" style="width:350px;" value=""></div>';

                $("#dialog-cancelar").dialog({
                    resizable: false,
                    height: 250,
                    width: 500,
                    modal: true,
                    open: function (event, ui) {
                        $("#autocompleteusuariotiinvitar").autocomplete({
                            type: 'post',
                            source: function (request, response) {
                                $.get("select/getUsuarioautocomplete.php", {
                                    buscar: request.term,
                                    tipo: 2
                                }, function (data) {
                                    tags = data.split("|");
                                    response(tags);
                                });
                            }
                        });
                    },
                    buttons: {
                        "Invitar": function () {
                            $(this).dialog("close");
                            $("#autocompleteusuariotiinvitar").submit();

                            var seleccion_texto = $("#autocompleteusuariotiinvitar").val();
                            var list_id = seleccion_texto.split(" - (");
                            var tarea_destino = list_id[1];
                            tarea_destino = tarea_destino.replace(')', '');

                            var url = "select/invitar_tarea.php?tarea=" + ID_SELECCIONADA + "&usuario_destino=" + tarea_destino + "&usuario_origen=" + encodeURIComponent(usuariovalidado);
                            $.ajax({
                                type: "GET",
                                url: url,
                                success: function (data) {

                                }
                            });

                            $(this).dialog("close");
                        },
                        "Cancelar": function () {
                            $(this).dialog("close");
                        }

                    }
                });

            }
            // ----
            if (ui.cmd == "marcar") {
                var url = "select/MarcarTarea.php?id=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado);
                $.ajax({
                    url: url
                }).done(function () {

                });
            }
            if (ui.cmd == "borrar") {

                var url = "select/ModificarCampoTarea.php?id=" + ID_SELECCIONADA + "&dato=1&usuario=" + encodeURIComponent(usuariovalidado) + "&campo=Control";
                $.ajax({
                    url: url
                }).done(function () {
                    cambia_menu(1);
                });

            }
            if (ui.cmd == "valoracion") {

                var url = "valoracion/ControlValoracion.php?tarea=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado);
                $.ajax({
                    url: url,
                    success: function (data) {
                        var res = data.split(",");
                        if (res[0] == '0') {
                            // **********
                            $("#dialog-iniciar").html('<div style="text-align: center;"><img style="width:20px;height:20px;" src="./imagenes/informe1.png">¿Desea enviar esta tarea para su valoracion?' + res[2] + '</div>');
                            $("#dialog-iniciar").dialog({
                                resizable: false,
                                title: 'Mandar Correo de Valoración',
                                width: 550,
                                height: 400,
                                modal: true,

                                open: function (event, ui) {
                                    $("#dialog-iniciar").tooltip({

                                        position: {

                                            my: "center bottom-20",

                                            at: "center top",

                                            using: function (position, feedback) {

                                                $(this).css(position);

                                                $("<div>")

                                                        .addClass("arrow")

                                                        .addClass(feedback.vertical)

                                                        .addClass(feedback.horizontal)

                                                        .appendTo(this);

                                            }

                                        }

                                    });


                                },
                                buttons: {
                                    "Ok": function () {
                                        $(this).dialog("close");
                                        var url = "mails/MailValoracion.php?tarea=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado) + "&portal=" + encodeURIComponent(portal);
                                        $.ajax({
                                            url: url,
                                            success: function (data) {
                                                if (data != '1') {
                                                    // **********
                                                    $("#dialog-iniciar").html(data);
                                                    $("#dialog-iniciar").dialog({
                                                        resizable: false,
                                                        title: 'Error en el Envio',
                                                        width: 300,
                                                        height: 200,
                                                        modal: true,
                                                        /* position: [100,380], */
                                                        buttons: {
                                                            "Ok": function () {
                                                                $(this).dialog("close");
                                                            }
                                                        }
                                                    });
                                                    // **********
                                                }
                                            }
                                        });
                                    },
                                    "Cancelar": function () {
                                        $(this).dialog("close");
                                    }
                                }
                            });
                            // **********
                        }
                        if (res[0] == '1') {
                            // **********
                            $("#dialog-iniciar").html('<div style="text-align: center;"><img style="width:20px;height:20px;" src="./imagenes/informe1.png">Esta solicitud esta pendiente de ser valorada.' + res[2] + '</div>');
                            $("#dialog-iniciar").dialog({
                                resizable: false,
                                title: 'Valoración pendiente',
                                width: 550,
                                height: 400,
                                modal: true,
                                open: function (event, ui) {
                                    $("#dialog-iniciar").tooltip({

                                        position: {

                                            my: "center bottom-20",

                                            at: "center top",

                                            using: function (position, feedback) {

                                                $(this).css(position);

                                                $("<div>")

                                                        .addClass("arrow")

                                                        .addClass(feedback.vertical)

                                                        .addClass(feedback.horizontal)

                                                        .appendTo(this);

                                            }

                                        }

                                    });


                                },
                                buttons: {
                                    "Ok": function () {
                                        $(this).dialog("close");
                                    },
                                    "Borrar Solicitud": function () {
                                        $(this).dialog("close");
                                        var url = "valoracion/borrar_valoracion.php?envio=" + res[1];
                                        $.ajax({
                                            url: url
                                        });
                                    }
                                }
                            });
                            // **********
                        }
                        if (res[0] == '2') {


                            var url = "valoracion/valoracion.php?envio=" + res[1] + "&portal=" + portal;
                            $.ajax({
                                url: url,
                                success: function (data) {
                                    // **********
                                    $("#dialog-iniciar").html(data);
                                    $("#dialog-iniciar").dialog({
                                        resizable: false,
                                        title: 'Resultado de la Valoración',
                                        width: 400,
                                        height: 350,
                                        modal: true,
                                        open: function (event, ui) {
                                            $(".rating").jRating({
                                                step: true,
                                                rateMax: 5,
                                                length: 5,
                                                sendRequest: false,
                                                canRateAgain: true,
                                                isDisabled: true,
                                                nbRates: 99999999,
                                                CustomLeyend: ["Nada satisfecho", "Poco satisfecho", "Medianamente satisfecho", "Bastante satisfecho", "Muy satisfecho"],
                                                onClick: function (element, rate) {
                                                    // alert(element.id + rate);
                                                    var elementos_pulsados = element.id;
                                                    var elemento_pulsado = elementos_pulsados.split('_');
                                                    if (elemento_pulsado[1] == '1') {
                                                        nota_1 = elemento_pulsado[2];
                                                        id_nota_1 = rate;
                                                    }
                                                    if (elemento_pulsado[1] == '2') {
                                                        nota_2 = elemento_pulsado[2];
                                                        id_nota_2 = rate;
                                                    }

                                                    $(".rating").jRating({
                                                        isDisabled: true
                                                    });
                                                },
                                                onSuccess: function () {
                                                    alert('Success : your rate has been saved :)');
                                                },
                                                onError: function () {
                                                    alert('Error : please retry');
                                                }
                                            });
                                        },
                                        /* position: [100,380], */
                                        buttons: {
                                            "Ok": function () {
                                                $(this).dialog("close");
                                            }
                                        }
                                    });
                                    // **********
                                }
                            });
                        }
                    }
                });




            }
            if (ui.cmd == "historial") {
                filas_historico = -1;
                fecha_historial = 1;
                crearslidehistorico(0);

            }
        }

    });

}

function semaforo(color) {

    if (color == 'verde') {
        $("#imagen_tarea").attr("src", "imagenes/green_circle.png");
    }
    if (color == 'rojo') {

        if ($("#selectestado").val() == "Sin Iniciar") {
            $("#selectestado").val("En Curso");
        }

        $("#imagen_tarea").attr("src", "imagenes/red_circle.png");
    }
}


function cierrotareahora(opcion) {
    // Si tiene ID (Puede ser que no venga con ID porque abre simplemente el portal) obtenemos los datos
    var ajax = nuevoAjax();
    var lista;

    var target = document.getElementById('micuerpo');
    var spinner = new Spinner(opts).spin(target);

    ajax.open("POST", "select/cerrar_tarea_hora.php?tarea_hora=" + ID_HORA_SELECCIONADA + "&opcion=" + opcion, false);
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {

            // Respuesta
            lista = (ajax.responseText);

            if (lista == '0') {
                semaforo('verde');

                var micuerpo1 = document.getElementById('misecciondatos');
                if (micuerpo1.style.display == 'block') {
                    // $("#grid_array_horas_plegar").pqGrid("refreshDataAndView");
                }

            } else {
                semaforo('rojo');

                var micuerpo1 = document.getElementById('misecciondatos');
                if (micuerpo1.style.display == 'block') {
                    // $("#grid_array_horas_plegar").pqGrid("refreshDataAndView");
                }
            }
            // Cargamos los datos en HTML. En el DIV correspondiente

            $grid_horas_en_un_dia.pqGrid("refreshDataAndView");

            if ($GridPlanificacionesTareas != '') {
                $GridPlanificacionesTareas.pqGrid("refreshDataAndView");
            }

            spinner.stop();

            actualizar_banner_tareas();
        }
        crearslidesemanal(2);
    }
    ajax.send(null);
}

function iniciotareahora() {
    // Si tiene ID (Puede ser que no venga con ID porque abre simplemente el portal) obtenemos los datos

    var target = document.getElementById('micuerpo');
    var spinner = new Spinner(opts).spin(target);

    var ajax = nuevoAjax();
    var lista;
    var usuario = usuariovalidado; // document.getElementById('usuariovalidado');


    var url = "select/iniciar_tarea_hora.php?tarea=" + ID_SELECCIONADA + "&usuario=" + usuario + "&plan=" + ID_PLAN_SELECCIONADA + "&origen=" + ID_FICHADA_SELECCIONADA;

    $.ajax({
        url: url,
        success: function (response) {
            // Respuesta
            lista = (ajax.responseText);

            // ++ T#28318 Alberto. Si la tarea se reabre hay que avisar al usuario
            if (response == 0) {
                mensaje('Iniciar tarea', 'La tarea cerrada ' + ID_SELECCIONADA + ' se ha reabierto.');
            }
            // -- T#28318 Alberto. Si la tarea se reabre hay que avisar al usuario

            semaforo('rojo');
            ID_PLAN_SELECCIONADA = -1;
            ID_FICHADA_SELECCIONADA = -1;

            $("#selectestado").val("En Curso");
            refresh_tools();
            // Cargamos los datos en HTML. En el DIV correspondiente

            $("#grid_array").pqGrid("refreshDataAndView");

            $grid_horas_en_un_dia.pqGrid("refreshDataAndView");

            if ($GridPlanificacionesTareas != '') {
                $GridPlanificacionesTareas.pqGrid("refreshDataAndView");
            }

            spinner.stop();

            actualizar_banner_tareas();
        }
    });

}

function iniciotareacomentario(tarea) {

    var ajax = nuevoAjax();
    var lista;
    var usuario = usuariovalidado; // document.getElementById('usuariovalidado');

    ajax.open("POST", "select/iniciar_tarea_comentario.php?tarea=" + tarea + "&usuario=" + encodeURIComponent(usuario), false);
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {

            // Respuesta
            lista = (ajax.responseText);
            // Cargamos los datos en HTML. En el DIV correspondiente
            refresh_tools();
        }
    }
    ajax.send(null);
}

function buscarpermiteiniciartarea(idpasado) {

    // Si tiene ID (Puede ser que no venga con ID porque abre simplemente el portal) obtenemos los datos
    var ajax = nuevoAjax();
    var lista;

    ajax.open("POST", "select/buscar_tarea_iniciable.php?tarea=" + idpasado, false);
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {

            // Respuesta
            lista = (ajax.responseText);
            // Cargamos los datos en HTML. En el DIV correspondiente

        }
    }
    ajax.send(null);
    return lista;
}

function buscartareahoraabierta() {
    var usuario = usuariovalidado; // document.getElementById('usuariovalidado');

    // Si tiene ID (Puede ser que no venga con ID porque abre simplemente el portal) obtenemos los datos
    var ajax = nuevoAjax();
    var lista;

    ajax.open("POST", "select/buscar_tarea_hora_abierta.php?usuario=" + encodeURIComponent(usuario), false);
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {

            // Respuesta
            lista = (ajax.responseText);
            // Cargamos los datos en HTML. En el DIV correspondiente

        }
    }
    ajax.send(null);
    return lista;
}

function cambiacolor(obj) {
    $("#" + obj.id).css("background-color", "#3f51b5");
    $("#" + obj.id).css("color", "#ffffff");
}

function cambiacolororiginal(obj) {
    $("#" + obj.id).css("background-color", "#eceff1");
    $("#" + obj.id).css("color", "#000000");
}

function cambiacolororiginalaviso(obj) {
    $("#" + obj.id).css("background-color", "transparent");
    $("#" + obj.id).css("color", "#000000");
}

function plegardesplegar(obj) {
    objplegar = document.getElementById(obj.id + "_plegar");
    //
    if (objplegar.offsetHeight > 5) {
        // SI YA ESTÁ DESPLEGADO NO LO PLEGAMOS PARA DESPUÉS VOLVER A DESPLEGARLO
    } else {

        // $( "#grid_array_evaluaciones_plegar" ).show();

        $(".plegable").animate({
            height: "1px"
        }, 300, function () {
            $("#la_tarea_plegar").css("overflow", "hidden");
        });

        // $("#" + objplegar.id).animate({height:"350px"},500, function()
        if (ID_PANTALLA == 1) {
            var alto_porcentaje = '55%';
        } else {
            var alto_porcentaje = '60%';
        }
        $("#" + objplegar.id).animate({
            height: alto_porcentaje
        }, 700, function () {
            if ("#" + objplegar.id == "#grid_array_horas_plegar") {
                $("#grid_array_horas_plegar").pqGrid("option", "height", alto_porcentaje);
                $("#grid_array_horas_plegar").pqGrid("refreshDataAndView");
            }
            if ("#" + objplegar.id == "#grid_array_costes_plegar") {
                $("#grid_array_costes_plegar").pqGrid("option", "height", alto_porcentaje);
                $("#grid_array_costes_plegar").pqGrid("refreshDataAndView");
            }
            if ("#" + objplegar.id == "#grid_array_evaluaciones_plegar") {
                $("#grid_array_evaluaciones_plegar").pqGrid("option", "height", alto_porcentaje);
                $("#grid_array_evaluaciones_plegar").pqGrid("refreshDataAndView");
            }
            if ("#" + objplegar.id == "#grid_array_comentarios_plegar") {
                $("#grid_array_comentarios_plegar").pqGrid("option", "height", alto_porcentaje);
                $("#grid_array_comentarios_plegar").pqGrid("refreshDataAndView");
            }
        });
    }


}

function plegardesplegaraviso(obj) {
    objplegar = document.getElementById(obj.id + "_PLEGAR");
    if (objplegar.offsetHeight > 5) {
        // Plegar no hago nada
    } else {

        var el = $("#" + objplegar.id),
                curHeight = el.height(),
                autoHeight = el.css('height', 'auto').height();

        $(".PLEGAR_AVISO").animate({
            height: "1px"
        }, 300, function () {});

        $("#" + objplegar.id).animate({
            height: autoHeight
        }, 500, function () {});
    }


}

function plegardesplegarconfiguracion(obj) {

    var alto_porcentaje = '55%';
    objplegar = document.getElementById(obj.id + "_plegar");

    if (objplegar.offsetHeight > 5) {
        // Plegar no hago nada
    } else {
        $(".plegableconfiguracion").animate({
            height: "1px"
        }, 300, function () {

            $("#" + objplegar.id).animate({
                height: alto_porcentaje
            }, 500, function () {

                if ("#" + objplegar.id == "#configuracion_colas_plegar") {
                    $("#configuracion_colas_plegar").pqGrid("option", "height", alto_porcentaje);
                    $("#configuracion_colas_plegar").pqGrid("refreshDataAndView");
                    $("#configuracion_colas_plegar_2").pqGrid("option", "height", alto_porcentaje);
                    $("#configuracion_colas_plegar_2").pqGrid("refreshDataAndView");
                }
                if ("#" + objplegar.id == "#configuracion_consultas_plegar") {
                    $("#configuracion_consultas_plegar").pqGrid("option", "height", alto_porcentaje);
                    $("#configuracion_consultas_plegar").pqGrid("refreshDataAndView");
                }
                if ("#" + objplegar.id == "#configuracion_avisos_plegar") {
                    $("#configuracion_avisos_plegar").pqGrid("option", "height", alto_porcentaje);
                    $("#configuracion_avisos_plegar").pqGrid("refreshDataAndView");
                }
                if ("#" + objplegar.id == "#configuracion_actividades_plegar") {
                    $("#configuracion_actividades_plegar").pqGrid("option", "height", alto_porcentaje);
                    $("#configuracion_actividades_plegar").pqGrid("refreshDataAndView");
                }
                if ("#" + objplegar.id == "#configuracion_feedback_plegar") {
                    $("#configuracion_feedback_plegar").pqGrid("option", "height", alto_porcentaje);
                    $("#configuracion_feedback_plegar").pqGrid("refreshDataAndView");
                }
            });
        });
    }
}

function agregar_linea(resultado) {
    var elem = resultado.split("|");
    var tarea = elem[0];
    var carpeta = elem[1];
    var fichero = elem[2];
    var id = elem[3];

    var imagen_archivo = '<img src="./imagenes/transparent.png" width="20" id="IMAGEN_FICHERO_' + id + '"></img>';
    var imagen_archivo_tipo = '<img src="./imagenes/0.png" width="20" name="IMAGEN_FICHERO_TIPO_' + id + '" id="IMAGEN_FICHERO_TIPO_' + id + '"></img>'
    var nueva_linea = '<tr><td style="cursor: pointer;">' + imagen_archivo + imagen_archivo_tipo + '</td><td style="cursor: pointer;" title="Descargar el fichero"><a class="ver_fichero" id="FICHERO_' + id + '" href="select/archivo.php?id=' + id + '">' + fichero + '</td></tr>';
    var num1 = document.getElementById("tablaadjuntos1").rows.length;

    //var num2 = document.getElementById("tablaadjuntos2").rows.length;

    //var num3 = document.getElementById("tablaadjuntos3").rows.length;

    //var num4 = document.getElementById("tablaadjuntos4").rows.length;

    /*
     if (num2 < num1) {
     $(nueva_linea).appendTo('#tablaadjuntos2');
     } else {
     
     if (num3 < num2) {
     $(nueva_linea).appendTo('#tablaadjuntos3');
     } else {
     
     if (num4 < num3) {
     $(nueva_linea).appendTo('#tablaadjuntos4');
     } else {
     $(nueva_linea).appendTo('#tablaadjuntos1');
     }
     
     }
     
     }
     */
    MenuContextualFichero("#FICHERO_" + id);
}

function quitar_linea(t, id) {
    var td = t.parentNode;
    var tr = td.parentNode;
    var table = tr.parentNode;
    table.removeChild(tr);

    var ajax = nuevoAjax();
    ajax.open("POST", "uploader/unupload.php?id=" + id + "&usuario=" + encodeURIComponent(usuariovalidado), false);
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {

            // Respuesta
            lista = (ajax.responseText);
            // Cargamos los datos en HTML. En el DIV correspondiente

        }
    }
    ajax.send(null);
}

function lanza_informes(url) {
    // Llamamos a la ventana para que renderice ella sola
    miPopup = window.open(url);
}

function ver_parametro_informe(id) {
    if (id == 2 || id == 3 || id == 4 || id == 5) {
        // var left = (screen.width/2)-(300/2);
        // var top = (screen.height/2)-(150/2);
        var left = (screen.width);
        var top = (screen.height);
        if (id == 2) {
            // Llamamos a la ventana para que renderice ella sola
            miPopup = window.open("http://navisionsql/ReportServer/Pages/ReportViewer.aspx?%2fMCT%2fMENU&rs%3aCommand=Render");
        }
        if (id == 3) {
            // Llamamos a la ventana para que renderice ella sola
            miPopup = window.open("http://navisionsql/ReportServer/Pages/ReportViewer.aspx?%2fGestionTI%2fRPT_INFORME_HORAS_FICHADAS_HORAS_REALES&rs%3aCommand=Render");
        }
        if (id == 4) {
            // Llamamos a la ventana para que renderice ella sola
            miPopup = window.open("http://navisionsql/ReportServer/Pages/ReportViewer.aspx?%2fGestionTI%2fRPT_INFORME_OPERACIONAL&rs%3aCommand=Render");
        }
        if (id == 5) {
            // Llamamos a la ventana para que renderice ella sola
            miPopup = window.open("http://navisionsql/ReportServer/Pages/ReportViewer.aspx?%2fGestionTI%2fRPT_INFORME_COSTES_PROYECTOS&rs%3aCommand=Render");
        }
        miPopup.focus();
    } else {
        var micuerpo = document.getElementById('informe-parametros');
        micuerpo.style.display = 'block';
        var micuerpo = document.getElementById('boton_lanza_informes');
        micuerpo.innerHTML = '<img src="imagenes/boton_solicitar_informe.png" width="25" height="25" onClick="ver_informe(' + id + ',0);" style="cursor: pointer"/>';
        var micuerpo = document.getElementById('informe-parametros_plegar');
        micuerpo.style.display = 'block';
        micuerpo.innerHTML = obtenerListaParametros(id);
        inicializarparametros(id);
    }
}

function inicializarparametros(id) {
    if (id == 0) {
        // Informe de MCT
        $('#informe_select_usuarios_ti').multipleSelect();
        $('#informe_select_estados').multipleSelect();
        var fecha_ini = document.getElementById('informe_select_from');
        var fecha_fin = document.getElementById('informe_select_to');
        fecha_ini.value = '';
        fecha_fin.value = '';

        $("#informe_select_from").datetimepicker({
            defaultDate: "+1w",
            changeMonth: true,
            dateFormat: "dd/mm/yy",
            numberOfMonths: 1,
            firstDay: 1,
            onClose: function (selectedDate) {
                $("#informe_select_to").datetimepicker("option", "minDate", selectedDate);
                var diaini = document.getElementById('informe_select_from');
                var diafin = document.getElementById('informe_select_to');
                mostrarDias(from.value, to.value);
            }
        });

        $("#informe_select_to").datetimepicker({
            defaultDate: "+1w",
            changeMonth: true,
            dateFormat: "dd/mm/yy",
            numberOfMonths: 1,
            firstDay: 1,
            onClose: function (selectedDate) {
                $("#informe_select_from").datetimepicker("option", "maxDate", selectedDate);
                var diaini = document.getElementById('informe_select_from');
                var diafin = document.getElementById('to');
                mostrarDias(from.value, to.value);

            }
        });
        var max = document.getElementById('informe_select_max');
        var min = document.getElementById('informe_select_min');
        max.value = '40';
        min.value = '0';

    }
    if (id == 1) {
        // Informe de TIMELINE
        var fecha_ini = document.getElementById('informe_select_from');
        var fecha_fin = document.getElementById('informe_select_to');
        fecha_ini.value = '';
        fecha_fin.value = '';

        $("#informe_select_from").datetimepicker({
            defaultDate: "+1w",
            changeMonth: true,
            dateFormat: "dd/mm/yy",
            numberOfMonths: 1,
            firstDay: 1,
            onClose: function (selectedDate) {
                $("#informe_select_to").datetimepicker("option", "minDate", selectedDate);
                var myDate = new Date(selectedDate.substring(3, 5) + '/' + selectedDate.substring(0, 2) + '/' + selectedDate.substring(6, 10));

                var dayOfMonth = myDate.getDate();
                myDate.setDate(dayOfMonth + 6);

                $("#informe_select_to").datetimepicker("option", "maxDate", myDate.getDate() + "/" + (myDate.getMonth() + 1) + "/" + myDate.getFullYear());

                var diaini = document.getElementById('informe_select_from');
                var diafin = document.getElementById('informe_select_to');
                mostrarDias(from.value, to.value);

            }
        });

        $("#informe_select_to").datetimepicker({
            defaultDate: "+1w",
            changeMonth: true,
            dateFormat: "dd/mm/yy",
            numberOfMonths: 1,
            firstDay: 1,
            onClose: function (selectedDate) {
                $("#informe_select_from").datetimepicker("option", "maxDate", selectedDate);
                var diaini = document.getElementById('informe_select_from');
                var diafin = document.getElementById('to');
                mostrarDias(from.value, to.value);

            }
        });

    }
}

function obtenerListaParametros(id) {

    var devolver = '';
    var usuario = usuariovalidado;

    if (id == 0) {

        // INFORME MCT
        var ajax = nuevoAjax();
        var lista;

        ajax.open("POST", "select/obtener_estados.php", false);
        ajax.onreadystatechange = function () {
            if (ajax.readyState != 4) {
                // NO ESTA LISTO!!!!!!!!!!
            } else {
                lista_estados = (ajax.responseText);
                ajax.open("POST", "select/obtener_usuarios_ti.php?id=1", false);
                ajax.onreadystatechange = function () {
                    if (ajax.readyState != 4) {
                        // NO ESTA LISTO!!!!!!!!!!
                    } else {
                        // Respuesta
                        lista_usuarios_ti = (ajax.responseText);

                        // Cargamos los datos en HTML. En el DIV correspondiente
                        devolver = devolver + '<label for="informe_select_usuarios_ti">Asignado a</label>';
                        devolver = devolver + '<select id="informe_select_usuarios_ti" multiple="multiple" style="width: 250px;">';
                        devolver = devolver + lista_usuarios_ti;
                        devolver = devolver + '</select>';
                        devolver = devolver + '<label for="informe_select_estados">Estado</label>';
                        devolver = devolver + '<select id="informe_select_estados" multiple="multiple" style="width: 250px;">';
                        devolver = devolver + lista_estados;
                        devolver = devolver + '</select>';
                        devolver = devolver + '</br>';
                        devolver = devolver + '</br>';
                        devolver = devolver + '<label for="informe_select_min">Ranking minimo</label>';
                        devolver = devolver + '<input type="text" id="informe_select_min" name="informe_select_min">';
                        devolver = devolver + '<label for="informe_select_max">Ranking maximo</label>';
                        devolver = devolver + '<input type="text" id="informe_select_max" name="informe_select_max">';
                        devolver = devolver + '<label for="informe_select_from">Desde</label><input type="text" id="informe_select_from" name="informe_select_from">';
                        devolver = devolver + '<label for="informe_select_to">Hasta</label><input type="text" id="informe_select_to" name="informe_select_to">	';

                        return devolver;
                    }
                }
                ajax.send(null);
            }
        }
        ajax.send(null);

    }
    if (id == 1) {

        // TIMELINE
        var ajax = nuevoAjax();
        var lista;

        ajax.open("POST", "select/obtener_usuarios_ti.php?id=2", false);
        ajax.onreadystatechange = function () {
            if (ajax.readyState != 4) {
                // NO ESTA LISTO!!!!!!!!!!
            } else {
                // Respuesta
                lista_usuarios_ti = (ajax.responseText);
                // Cargamos los datos en HTML. En el DIV correspondiente
                devolver = devolver + '<label for="informe_select_usuarios_ti">Usuario TI</label>';
                devolver = devolver + '<select id="informe_select_usuarios_ti" style="width: 250px;">';
                devolver = devolver + lista_usuarios_ti;
                devolver = devolver + '</select>';
                devolver = devolver + '</br>';
                devolver = devolver + '<label for="informe_select_from">Desde</label><input type="text" id="informe_select_from" name="informe_select_from">';
                devolver = devolver + '<label for="informe_select_to">Hasta</label><input type="text" id="informe_select_to" name="informe_select_to">	';

                return devolver;
            }
        }
        ajax.send(null);


    }
    return devolver;
}

function plegardesplegar_parametroinforme(obj) {
    objplegar = document.getElementById(obj.id + "_plegar");

    if (($("#" + obj.id + "_plegar").css('height')) == "120px") {

        $("#" + obj.id + "_plegar").animate({
            height: "1px"
        }, 300, function () {
            $("#" + obj.id + "_plegar").css("overflow", "hidden");
        });
    } else {

        $("#" + obj.id + "_plegar").animate({
            height: "120px"
        }, 500, function () {
            $("#" + obj.id + "_plegar").css("overflow", "visible");
        });
    }
}

function ver_informe(id, accion) {

    // Mostramos los informes.
    // El id indica que informe quiero ver. Abajo se detalla la carpeta y el informa y los parametros
    // La accion determina que hace el informe.
    // 0 = PINTA EL INFORME EN PANTALLA
    // 1 = SACA EL INFORME EN PDF

    // Variables
    var ajax = nuevoAjax();
    var idusuario = usuariovalidado;
    var informe = "";
    var carpeta = "";
    var parametros = "";
    // Variables

    // DEFINICION DE INFORMES--------------------------------------------------
    if (id == 0) {
        // Informe MCT
        // Configuracion
        informe = 'RPT_HORAS_MCT';
        carpeta = 'GestionTI';
        var fecha_ini = document.getElementById('informe_select_from');
        var fecha_fin = document.getElementById('informe_select_to');
        var max = document.getElementById('informe_select_max');
        var min = document.getElementById('informe_select_min');

        listausuarios = $("#informe_select_usuarios_ti").multipleSelect("getSelects");
        listaestados = $("#informe_select_estados").multipleSelect("getSelects");
        if (listausuarios.length > 0) {
            for (a = 0; a < listausuarios.length; a++) {
                parametros = parametros + 'USUARIO=' + listausuarios[a] + '|';
            }
        }
        if (listaestados.length > 0) {
            for (a = 0; a < listaestados.length; a++) {
                parametros = parametros + 'ESTADO=' + listaestados[a] + '|';
            }
        }

        parametros = parametros + 'DE=' + min.value + '|';
        parametros = parametros + 'HASTA=' + max.value + '|';
        parametros = parametros + 'FECHAINI=' + fecha_ini.value.substring(3, 5) + '-' + fecha_ini.value.substring(0, 2) + '-' + fecha_ini.value.substring(6, 10) + '|';
        parametros = parametros + 'FECHAFIN=' + fecha_fin.value.substring(3, 5) + '-' + fecha_fin.value.substring(0, 2) + '-' + fecha_fin.value.substring(6, 10);

    }
    if (id == 1) {
        // Informe TIMELINE
        // Configuracion
        informe = 'RPT_TIME_LINE';
        carpeta = 'GestionTI';
        var fecha_ini = document.getElementById('informe_select_from');
        var fecha_fin = document.getElementById('informe_select_to');
        var usuario = document.getElementById('informe_select_usuarios_ti');

        parametros = parametros + 'USUARIO=' + usuario.value + '|';

        parametros = parametros + 'FECHAINI=' + fecha_ini.value.substring(3, 5) + '-' + fecha_ini.value.substring(0, 2) + '-' + fecha_ini.value.substring(6, 10) + '|';
        parametros = parametros + 'FECHAFIN=' + fecha_fin.value.substring(3, 5) + '-' + fecha_fin.value.substring(0, 2) + '-' + fecha_fin.value.substring(6, 10);
    }
    // DEFINICION DE INFORMES--------------------------------------------------

    // PINTO EN PANTALLA. Lo renderizamos en HTML y lo mostramos en mi cuerpo
    if (accion == 0) {

        var target = document.getElementById('miseccioninformes');
        var spinner = new Spinner(opts).spin(target);
        ajax.open("POST", "informe.php?usuario=" + encodeURIComponent(idusuario) + "&informe=" + encodeURIComponent(informe) + "&carpeta=" + encodeURIComponent(carpeta) + "&parametros=" + encodeURIComponent(parametros) + "&id=" + id + "&accion=" + accion, true);
        ajax.onreadystatechange = function () {
            if (ajax.readyState != 4) {
                // NO ESTA LISTO!!!!!!!!!!!
            } else {

                //
                spinner.stop();

                // Respuesta
                lista = (ajax.responseText);

                // Cargamos los datos en HTML. En el DIV correspondiente
                var combo = document.getElementById('informe-mio');
                combo.innerHTML = lista;
            }
        }
        ajax.send(null);
    }

    // GENERO EL PDF. Lo renderizamos en PDF y lo mostramos en otra pantalla

    if (accion > 0) {
        var left = (screen.width / 2) - (300 / 2);
        var top = (screen.height / 2) - (150 / 2);

        // Llamamos a la ventana para que renderice ella sola
        miPopup = window.open("informe.php?usuario=" + encodeURIComponent(idusuario) + "&informe=" + encodeURIComponent(informe) + "&carpeta=" + carpeta + "&parametros=" + encodeURIComponent(parametros) +
                "&id=" + id + "&accion=" + accion + "", "Documentos", "width=300,height=150,toolbar=no,menubar=no,scrollbars=yes,locationbar=no,location=no,resizable=no,directories=no, status=no, top=" + top + ", left=" + left);
        miPopup.focus();
    }


}

function cambia_departamento(select_departamento) {
    $("#div_programa").load("select/obtener_relacion.php", {
        departamento: select_departamento.value,
        programa: ""
    });
}

function cambia_departamento_alta(select_departamento) {
    $("#div_programaalta").load("select/obtener_relacion_alta.php", {
        departamento: select_departamento.value,
        programa: ""
    });
}

function esIntegerenGrid(e, Columna) {

}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 12/06/2017
 */
function inicia_fechas_evamasiva() {


    $('#femDesde').val('');
    $('#femHasta').val('');
    $('#femAutocompleteUsuario').val('');

    $('#femFechaPeriodo').val('');
    $('#femAutocompleteUsuarioDiferido').val('');

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; // Enero es 0!

    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    today = dd + '/' + mm + '/' + yyyy;

    $(function () {

        $("#femDesde").datepicker({
            // defaultDate: "+1w",
            changeMonth: true,
            dateFormat: "dd/mm/yy",
            numberOfMonths: 1,
            firstDay: 1,
            maxDate: today
        });


        $("#femHasta").datepicker({
            // defaultDate: "+1w",
            changeMonth: true,
            dateFormat: "dd/mm/yy",
            maxDate: today,
            numberOfMonths: 1,
            firstDay: 1
        });

        $("#femFechaPeriodo").datepicker({
            // defaultDate: "+1w",
            changeMonth: true,
            dateFormat: "dd/mm/yy",
            maxDate: today,
            numberOfMonths: 1,
            firstDay: 1
        });
    });

    if ($('#femAutocompleteUsuario').length) {
        $("#femAutocompleteUsuario").autocomplete({
            type: 'post',
            source: function (request, response) {
                $.get("select/getUsuarioAutocompleteOficina.php", {
                    buscar: request.term,
                    tipo: 1,
                    oficinas: oficinas,
                    oficinaTarea: $('#femOficina').find(":selected").text(),
                    externo: ''
                }, function (data) {
                    tags = data.split("|");
                    response(tags);
                });
            }
        });
    }

    if ($('#femAutocompleteUsuarioDiferido').length) {
        $("#femAutocompleteUsuarioDiferido").autocomplete({
            type: 'post',
            source: function (request, response) {
                $.get("select/getUsuarioAutocompleteOficina.php", {
                    buscar: request.term,
                    tipo: 1,
                    oficinas: oficinas,
                    oficinaTarea: $('#femOficinaDiferida').find(":selected").text(),
                    externo: ''
                }, function (data) {
                    tags = data.split("|");
                    response(tags);
                });
            }
        });
    }

}
function CargaGridHoras() {

    // Ocultar campos de hora y fecha en usuarios que no deban ver horas en los fichajes
    var ocultarCampo = false;
    if (OcultarHoraGrids == 1) {
        ocultarCampo = true;
    }

    var tipos = [];
    $.ajax({
        url: "select/getTiposTipos.php?tipo=8&id=" + ID_SELECCIONADA,
        success: function (response) {
            tipos = response.split(",");
        }
    });

    var aportesorganizacion = [];
    $.ajax({
        url: "select/getTiposTipos.php?tipo=102&id=" + ID_SELECCIONADA + "&orden=C",
        success: function (response) {
            aportesorganizacion = response.split(",");
        }
    });

    var aportesempresa = [];
    $.ajax({
        url: "select/getTiposTipos.php?tipo=103&id=" + ID_SELECCIONADA + "&orden=C",
        success: function (response) {
            aportesempresa = response.split(",");
        }
    });

    var dateEditor = function (ui) {
        var $cell = ui.$cell,
                rowData = ui.rowData,
                dataIndx = ui.dataIndx,
                cls = ui.cls,
                dc = $.trim(rowData[dataIndx]);
        $cell.css('padding', '0');
        var $inp = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' />")
                .appendTo($cell)
                .val(dc).datetimepicker({
            changeMonth: true,
            changeYear: true,
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            firstDay: 1,
            dateFormat: "dd/mm/yy",
            onClose: function () {
                $inp.focus();
            }
        });
        // $.datepicker.setDefaults($.datepicker.regional['es']);
        // inicializo_fecha(dataIndx);
        // inicializo_fecha(diafin);
    }

    var dateEditor2 = function (ui) {
        var $cell = ui.$cell;
        var dataIndx = ui.dataIndx;
        var rowData = ui.rowData;
        var cls = ui.cls;
        var dc = $.trim(rowData[dataIndx]);
        var input = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' autocomplete='off'  />")
                .appendTo($cell)
                .val(dc).datetimepicker({
            datepicker: true,
            timepicker: true,
            format: 'd/m/Y H:i:s',
            minTime: '07:00:00',
            weeks: false,
            step: 15,
            dayOfWeekStart: 1,
        });
    }

    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '100%',
        // height: '100%',
        title: "Lista de Tiempo Invertido ordenado descendentemente",
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
        editModel: {
            clicksToEdit: 1,
            saveKey: '13',
            onSave: null
        },
        pageModel: {
            type: 'remote',
            rPP: 13,
            strRpp: "{0}",
            curPage: 1,
            rPPOptions: [5, 13, 25, 50, 100, 1000]
        },
        scrollModel: {
            pace: 'fast',
            autoFit: true,
            theme: true
        },
        dataModel: {
            sorting: "remote",
            paging: "remote",
            sortIndx: ["Dia Inicio"],
            sortDir: ["down"],
            location: "remote",
            dataType: "JSON",
            method: "GET",
            url: "select/consultaHoras.php?tarea=" + ID_SELECCIONADA,
            getData: function (dataJSON, textStatus, jqXHR) {

                var data = dataJSON.data;
                return {
                    curPage: dataJSON.curPage,
                    totalRecords: dataJSON.totalRecords,
                    data: dataJSON.data
                };
            }
        }
    };


    // Columnas
    obj.colModel = [{
            title: "Numero",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Numero"
        },
        {
            title: "Tarea",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Tarea"
        },
        {
            title: "Dia Inicio",
            width: 75,
            minWidth: 75,
            maxWidth: 75,
            dataType: "text",
            dataIndx: "Dia Inicio",
            editor: {
                type: dateEditor2
            },
            validations: [{
                    type: 'regexp',
                    value: '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}',
                    msg: 'No en formato dd/mm/yyyy hh:mm '
                }]
        },
        {
            title: "Hora Inicio",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "text",
            editable: false,
            sortable: false,
            hidden: ocultarCampo,
            dataIndx: "Hora Inicio"
        },
        {
            title: "Dia Fin",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            hidden: ocultarCampo,
            dataType: "text",
            dataIndx: "Dia Fin",
            editor: {
                type: dateEditor2
            },
            validations: [{
                    type: 'regexp',
                    value: '^$|[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}',
                    msg: 'No en formato dd/mm/yyyy hh:mm '
                }]
        },
        {
            title: "Hora Fin",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "text",
            editable: false,
            sortable: false,
            hidden: ocultarCampo,
            dataIndx: "Hora Fin"
        },
        {
            title: "Minutos",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "text",
            editable: false,
            dataIndx: "Minutos"
        },
        {
            title: "Usuario",
            width: 145,
            minWidth: 145,
            maxWidth: 145,
            dataType: "text",
            editable: false,
            dataIndx: "Usuario"
        },
        {
            title: "Año ISO",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "text",
            editable: false,
            dataIndx: "Anyo_ISO"
        },
        {
            title: "Semana ISO",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "text",
            editable: false,
            dataIndx: "Semana_ISO"
        },
        {
            title: "Año",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "text",
            editable: false,
            dataIndx: "Anyo"
        },
        {
            title: "Mes",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "text",
            editable: false,
            dataIndx: "Mes"
        },
        {
            title: "Estado",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Estado"
        },
        {
            title: "Tipo",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "text",
            editable: true,
            dataIndx: "Tipo",
            editor: {
                type: 'select',
                // options: categorias
                options: function (ui) {
                    return tipos;
                }
            },
            validations: [{
                    type: 'minLen',
                    value: 1,
                    msg: "Required"
                }]
        },
        {
            title: "Gasto",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "text",
            editable: true,
            dataIndx: "Gasto"

        },
        {
            title: "Aporte Propio",
            width: 90,
            minWidth: 90,
            maxWidth: 90,
            dataType: "text",
            editable: true,
            dataIndx: "Aporte",
            editor: {
                type: 'select',
                // options: categorias
                options: function (ui) {
                    return aportesorganizacion;
                }
            }
        },
        {
            title: "Aporte Empresa",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "text",
            editable: true,
            dataIndx: "AporteEmpresa",
            editor: {
                type: 'select',
                // options: categorias
                options: function (ui) {
                    return aportesempresa;
                }
            }

        },
        {
            title: "Comentario",
            width: 450,
            dataType: "text",
            editable: true,
            dataIndx: "Comentario"
        },
        {
            title: "Inicio",
            width: 250,
            dataType: "text",
            editable: false,
            hidden: true,
            dataIndx: "Inicio"
        }
    ];

    // Barra de Tareas
    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

        // Botonera
        $("<span>Iniciar</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-triangle-1-e"
            }
        }).click(function (evt) {
            initRow();
        });
        $("<span>Parar</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-stop"
            }
        }).click(function (evt) {
            closeRow();
        });

        $("<span>Masivo</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-clock"
            }
        }).click(function (evt) {
            masiveRow();
        });

        $("<span>Separar</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-arrowstop-1-w"
            }
        }).click(function (evt) {
            separateRow();
        });

        $("<span>Exportar</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-extlink"
            }
        }).click(function (evt) {
            $("#grid_array_horas_plegar").pqGrid("exportExcel", {url: "./export/excel.php", sheetName: "Horas tarea " + ID_SELECCIONADA});
        });
    };

    // Seleccionar la fila
    obj.rowSelect = function (evt, obj) {
        ID_HORA_SELECCIONADA = obj.rowData["Numero"];
    };
    // Seleccionar la fila
    obj.cellSelect = function (evt, obj) {
        ID_HORA_SELECCIONADA = obj.rowData["Numero"];
    };
    // Doble Click
    obj.cellDblClick = function (evt, obj) {
        ID_HORA_SELECCIONADA = obj.rowData["Numero"];
    };

    var $grid = $("#grid_array_horas_plegar").pqGrid(obj);
    // Mostrar la parte de arriba
    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", false);
    // $grid.pqGrid("option", "collapsible", false);
    // Mostrar la parte de abajo
    // $grid.pqGrid("option", "bottomVisible", true);
    // Lineas de separacion verticales
    $grid.pqGrid("option", "columnBorders", true);
    // Lineas de separacion horizontales
    $grid.pqGrid("option", "rowBorders", true);
    // Sombreado en las filas
    // $grid.pqGrid("option", "oddRowsHighlight", true);
    // Numero de celda
    $grid.pqGrid("option", "numberCell", true);
    // Comprimir altura
    // $grid.pqGrid("option", "flexHeight", true);
    // $grid.pqGrid( {flexHeight:true} );
    // Comprimir Anchura
    // $grid.pqGrid("option", "flexWidth", true);
    // $grid.pqGrid("option", "freezeCols",1);
    // Barras de scroll
    // $grid.pqGrid("option", "scrollModel", { horizontal: true }, { vertical: true });
    // Permite cambiar el tamaño
    // $grid.pqGrid("option", "resizable", true);

    // Grabar la modificacion
    $grid.on("pqgridcellsave", function (evt, ui) {

        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);

        var ID = ui.rowData["Numero"];
        var TITULO = ui.rowData["Comentario"];
        var DIA_INICIO = ui.rowData["Dia Inicio"];
        var DIA_FIN = ui.rowData["Dia Fin"];
        var HORA_INICIO = ui.rowData["Hora Inicio"];
        var HORA_FIN = ui.rowData["Hora Fin"];
        var TIPO = ui.rowData["Tipo"];
        var GASTO = ui.rowData["Gasto"];
        var APORTE = ui.rowData["Aporte"];
        var APORTEEMPRESA = ui.rowData["AporteEmpresa"];

        TITULO = limpiarTextoOffice(TITULO);

        var url = "select/modificarHorasTarea.php?Id=" + ID + "&diainicio=" + DIA_INICIO + "&diafin=" + DIA_FIN + "&horainicio=" + HORA_INICIO + "&horafin=" + HORA_FIN + "&Titulo=" + encodeURIComponent(TITULO) + "&Tipo=" + encodeURIComponent(TIPO) + "&usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=" + ID_SELECCIONADA + "&gasto=" + encodeURIComponent(GASTO) + "&aporte=" + encodeURIComponent(APORTE) + "&aporteempresa=" + encodeURIComponent(APORTEEMPRESA);

        $.ajax({
            cache: false,
            url: url
        }).done(function (msg) {
            if (msg == 0) {
                mensaje('Aviso de cambio de estado', 'La tarea número ' + ID_SELECCIONADA + ' ha cambiado al estado <i><b>En Curso</b></i> al modificar la fichada.');
            }
            $("#grid_array_horas_plegar").pqGrid("refreshDataAndView");
            spinner.stop( );
        });

        /*
         * $.ajax({ cache: false, url: url, success: function(data){ alertame(data); $( "#grid_array_horas_plegar" ).pqGrid( "refreshDataAndView" ); } });
         */
        crearslidesemanal(2);
    });

    function separateRow() {
        // diana almeida
        if (ID_HORA_SELECCIONADA < 0) {
            // **********
            $("#dialog-iniciar").html('<img style="width:20px;height:20px;" src="./imagenes/error.png">Seleccione primero una de las fichadas de la tarea');
            $("#dialog-iniciar").dialog({
                resizable: false,
                title: 'Error al Separar',
                width: 400,
                height: 200,
                modal: false,
                buttons: {
                    "Ok": function () {
                        $(this).dialog("close");

                    }
                }
            });
            // **********
        } else {
            // **********
            $("#dialog-iniciar").html('<img style="width:20px;height:20px;" src="./imagenes/alert.png">¿Desea separar la fichada seleccionada en otra tarea independiente?');
            $("#dialog-iniciar").dialog({
                resizable: false,
                title: 'Cerrar Tarea',
                width: 400,
                height: 200,
                modal: false,
                buttons: {
                    "Si": function () {
                        $(this).dialog("close");
                        // david lorente sampedro
                        var url = "select/separar_horas_tareas.php?hora_origen=" + ID_HORA_SELECCIONADA + "&tarea_origen=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado); // El script a dónde se realizará la petición.
                        $.ajax({
                            type: "GET",
                            url: url,
                            success: function (data) {
                                selecciono_tarea_historial(data, 1);
                            }
                        });
                    },
                    "Cancelar": function () {
                        ID_HORA_SELECCIONADA = -2;
                        $(this).dialog("close");
                    }
                }
            });
            // **********
        }
    }

    function initRow() {
        IniciarTarea();


    }

    function closeRow() {

        var lista = buscartareahoraabierta();
        var res = lista.split("|");
        if (lista.length > 3) {
            if (res[1] == '0') {

                // **********
                $("#dialog-iniciar").html('<img style="width:20px;height:20px;" src="./imagenes/alert.png">La tarea se abrió otro día.¿Se le olvido cerrarla?');
                $("#dialog-iniciar").dialog({
                    resizable: false,
                    title: 'Cerrar Tarea',
                    width: 400,
                    height: 400,
                    modal: false,
                    buttons: {
                        "Si": function () {
                            $(this).dialog("close");
                            ID_HORA_SELECCIONADA = res[0];
                            ID_HORA_SELECCIONADA_ESTADO = res[1];
                            cierrotareahora(1);
                            $("#grid_array_horas_plegar").pqGrid("refreshDataAndView");

                        },
                        "No": function () {
                            $(this).dialog("close");
                            ID_HORA_SELECCIONADA = res[0];
                            ID_HORA_SELECCIONADA_ESTADO = res[1];
                            cierrotareahora(2);
                            $("#grid_array_horas_plegar").pqGrid("refreshDataAndView");

                        }
                    }
                });
                // **********

            } else {
                ID_HORA_SELECCIONADA = res[0];
                ID_HORA_SELECCIONADA_ESTADO = res[1];
                cierrotareahora(2);
                $("#grid_array_horas_plegar").pqGrid("refreshDataAndView");
            }



        }

        crearslidesemanal(2);

    }

    function masiveRow() {
        var misecciondatos = document.getElementById('misecciondatos');
        misecciondatos.style.display = 'none';
        var migridresult = document.getElementById('grid_array_resultado');
        migridresult.style.display = 'none';
        // Datos
        var misecciondatosmasivo = document.getElementById('misecciondatosmasivo');
        misecciondatosmasivo.style.display = 'block';

        var la_tarea = document.getElementById('la_tarea');
        var la_tarea_masiva = document.getElementById('la_tarea_masiva');
        la_tarea_masiva.innerHTML = la_tarea.innerHTML;
        var fecha_ini = document.getElementById('from');
        var fecha_fin = document.getElementById('to');
        fecha_ini.value = '';
        fecha_fin.value = '';

        var btnEJECUTAR = document.getElementById('btn_calcularyejecutar');
        btnEJECUTAR.style.visibility = 'hidden';
        btnEJECUTAR.style.display = 'none';

        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; // January is 0!

        var yyyy = today.getFullYear();
        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }
        today = dd + '/' + mm + '/' + yyyy;

//jQuery('#from, #to').datetimepicker();      


        /*jslint browser:true*/
        /*global jQuery, document*/



        //jQuery('#filter-date, #search-from-date, #search-to-date').datetimepicker();

        //$("#from").datetimepicker();


        $(function () {

            //Alberto 19/11/2020 NUEVOS DATETIMER PICKER
            jQuery('#from,#to').datetimepicker({
                datepicker: true,
                timepicker: true,
                format: 'd/m/Y H:i',
                step: 15,
                dayOfWeekStart: 1,
                weeks: true,
                onChangeDateTime: function (dp, $input) {
                    //alert($input.val())
                    var diaini = document.getElementById('from');
                    var diafin = document.getElementById('to');
                    mostrarDias(from.value, to.value);
                }
            });
            /* // OLD DATEPICKER WITH TIMER ADDON
             $("#from").datetimepicker({
             defaultDate: "+1w",
             changeMonth: true,
             dateFormat: "dd/mm/yy",
             numberOfMonths: 2,
             firstDay: 1,
             maxDate: today,
             onClose: function(selectedDate) {
             $("#to").datetimepicker("option", "minDate", selectedDate);
             var diaini = document.getElementById('from');
             var diafin = document.getElementById('to');
             mostrarDias(from.value, to.value);
             }
             });
             
             $("#to").datetimepicker({
             defaultDate: "+1w",
             changeMonth: true,
             dateFormat: "dd/mm/yy",
             maxDate: today,
             numberOfMonths: 2,
             firstDay: 1,
             onClose: function(selectedDate) {
             // $( "#from" ).datetimepicker( "option", "maxDate", selectedDate );
             var diaini = document.getElementById('from');
             var diafin = document.getElementById('to');
             mostrarDias(from.value, to.value);
             }
             });
             */


            // Boton salir
            $("#btn_nocalcular").click(function () {
                var misecciondatos = document.getElementById('misecciondatos');
                misecciondatos.style.display = 'block';
                // Datos
                var misecciondatosmasivo = document.getElementById('misecciondatosmasivo');
                misecciondatosmasivo.style.display = 'none';

                var la_tarea_masiva = document.getElementById('la_tarea_masiva');
                la_tarea_masiva.innerHTML = '';

                $("#grid_array_horas_plegar").pqGrid("refreshDataAndView");
                return false; // Evitar ejecutar el submit del formulario.
            });
            // Boton calcular
            $("#btn_calcular").click(function () {
                // $( "#grid_array_resultado" ).pqGrid( "destroy" );

                PreCargaGridResultado(1);

                return false; // Evitar ejecutar el submit del formulario.
            });

            // Boton ejecutar
            $("#btn_calcularyejecutar").click(function () {

                PreCargaGridResultado(2);

                return false; // Evitar ejecutar el submit del formulario.
            });
            CargaGridResultado();

        });


    }

    function getRowIndx() {
        var arr = $grid.pqGrid("selection", {
            type: 'row',
            method: 'getSelection'
        });
        if (arr && arr.length > 0) {
            var rowIndx = arr[0].rowIndx;
            return rowIndx;
        } else {

            return null;
        }
    }
    // ************************************GRID
    // ****************************************
}

function CargaGridHorasEvaluacion(tarea, periodo, fechaCreacion, usuarioEvaluado) {

    // Si es evaluador no pasamos el campo usuario para que filtre todas las horas de todos
    if (evaluador == 1) {
        usuarioEvaluado = '';
    }
    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '100%',
        height: '537',
        title: "Lista de Tiempo Invertido ordenado descendentemente",
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
        editModel: {
            clicksToEdit: 1,
            saveKey: '13',
            onSave: null
        },
        pageModel: {
            type: 'remote',
            rPP: 1000,
            strRpp: "{0}",
            rPPOptions: [5, 10, 20, 50, 100, 200, 1000]
        },
        groupModel: {
            dataIndx: ["Usuario"],
            collapsed: [false],
            title: ["<b style='font-weight:bold;'>{0} ({1} Fichadas)</b>", "{0} - {1}"],
            dir: ["up"]
        },
        dataModel: {
            sorting: "remote",
            paging: "remote",
            sortIndx: ["Inicio"],
            sortDir: ["down"],
            location: "remote",
            dataType: "JSON",
            method: "GET",
            url: "select/consultaHoras.php?tarea=" + tarea + "&periodo=" + periodo + "&fechaCreacion=" + fechaCreacion + "&usuario=" + encodeURIComponent(usuarioEvaluado),
            getData: function (dataJSON, textStatus, jqXHR) {

                var data = dataJSON.data;
                return {
                    curPage: dataJSON.curPage,
                    totalRecords: dataJSON.totalRecords,
                    data: dataJSON.data
                };
            }
        },
        scrollModel: {
            pace: 'fast',
            autoFit: true,
            theme: true
        }
    };

    // Columnas
    obj.colModel = [{
            title: "Numero",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Numero"
        },
        {
            title: "Dia Inicio",
            width: 70,
            dataType: "text",
            dataIndx: "Dia Inicio",
            editable: false
        },
        {
            title: "Minutos",
            width: 70,
            dataType: "text",
            editable: false,
            dataIndx: "Minutos",
            summary: {
                type: ["sum"],
                title: ["Total: {0}"]
            }
        },
        {
            title: "Usuario",
            width: 100,
            dataType: "text",
            editable: false,
            dataIndx: "Usuario",
            hidden: true
        },
        {
            title: "Comentario",
            width: 428,
            dataType: "text",
            editable: false,
            dataIndx: "Comentario"
        }
    ];




    // obj.pageModel = {
    // curPage: 1,
    // rPP: 200,
    // rPPOptions: [5, 10, 20, 50, 200]
    // };

    // $( obj ).pqGrid( {pageModel:{rPP:100}} );
    // $( obj ).pqGrid( {pageModel:{rPP:100}} );

    var $grid = $("#gridComentariosFichasTarea").pqGrid(obj);

    // $grid.pqGrid( "option", "pageModel.rPP", 10 );
    // $grid.pqGrid( "option", "pageModel.rPPOptions", [10, 20, 200] );
    // $grid.pqGrid( "option", "pageModel.curPage", 1 );
    // $grid.pqGrid( "option", "pageModel.type", 'remote' );

    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", false);
    $grid.pqGrid("option", "collapsible", false);
    $grid.pqGrid("option", "columnBorders", true);
    $grid.pqGrid("option", "rowBorders", true);
    $grid.pqGrid("option", "oddRowsHighlight", true);
    $grid.pqGrid("option", "numberCell", true);
    /*
     * $grid.pqGrid("option", "scrollModel", { horizontal: true }, { vertical: true });
     */

    // ************************************GRID
    // ****************************************

}
function CargaGridComentarios(tarea, target) {

    if (tarea == undefined) {
        tarea = ID_SELECCIONADA;
    }

    if (target == undefined) {
        target = "grid_array_comentarios_plegar";
    }

    var altura = '100%';
    var ancho = '100%';
    if (target == 'gridComentariosTarea') {
        altura = '264';
        // ancho = '50%';
    }

    // ************************************GRID
    var clases = [];
    $.ajax({
        url: "select/getTiposTipos.php?tipo=11&id=" + tarea,
        success: function (response) {
            clases = response.split(",");
        }
    });
    // Datos generales.
    var obj = {
        width: ancho,
        height: altura,
        title: "Lista de Comentarios ordenado descendentemente",
        hoverMode: 'cell',
        selectionModel: {
            type: 'cell'
        },
        editModel: {
            saveKey: '13'
        },
        pageModel: {
            type: 'remote',
            strRpp: "{0}",
            curPage: 1,
            rPP: 14,
            rPPOptions: [5, 14, 20, 30, 40, 50],
        },
        scrollModel: {
            pace: 'fast',
            autoFit: true,
            theme: true
        },
        dataModel: {
            sorting: "remote",
            paging: "remote",
            sortIndx: ["Numero"],
            sortDir: ["down"],
            location: "remote",
            dataType: "JSON",
            method: "GET",
            url: "select/consultaComentarios.php?usuario=" + encodeURIComponent(nombreusuariovalidado) + "&tarea=" + tarea,
            getData: function (dataJSON, textStatus, jqXHR) {
                var data = dataJSON.data;
                return {
                    curPage: dataJSON.curPage,
                    totalRecords: dataJSON.totalRecords,
                    data: dataJSON.data
                };
            }
        }
    };


    // Columnas
    obj.colModel = [{
            title: "Numero",
            width: 20,
            minWidth: 20,
            maxWidth: 20,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Numero"
        },
        {
            title: "Tarea",
            width: 20,
            minWidth: 20,
            maxWidth: 20,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Tarea"
        },
        {
            title: "Fecha",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "text",
            editable: false,
            dataIndx: "Fecha"
        },
        {
            title: "Hora",
            width: 60,
            minWidth: 60,
            maxWidth: 60,
            dataType: "text",
            editable: false,
            dataIndx: "Hora"
        },
        {
            title: "Usuario",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "text",
            editable: false,
            dataIndx: "Usuario"
        },
        {
            title: "Comentario",
            width: 550,
            dataType: "text",
            dataIndx: "Comentario"
        },
        {
            title: "Clase",
            width: 90,
            minWidth: 70,
            maxWidth: 100,
            dataType: "text",
            dataIndx: "Clase",
            editor: {
                type: 'select',
                options: function (ui) {
                    return clases;
                }
            }
        }
    ];





    // Barra de Tareas
    if (target == "grid_array_comentarios_plegar") {
        obj.render = function (evt, obj) {
            var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

            // Botonera
            $("<span>Nuevo Comentario</span>").appendTo($toolbar).button({
                icons: {
                    primary: "ui-icon-circle-plus"
                }
            }).click(function (evt) {
                initRowCom();
            });

        };
    }



    var $grid = $("#" + target).pqGrid(obj);
    // Mostrar la parte de arriba
    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", false);
    $grid.pqGrid("option", "collapsible", false);
    // Mostrar la parte de abajo
    // $grid.pqGrid("option", "bottomVisible", true);
    // Lineas de separacion verticales
    $grid.pqGrid("option", "columnBorders", true);
    // Lineas de separacion horizontales
    $grid.pqGrid("option", "rowBorders", true);
    // Sombreado en las filas
    $grid.pqGrid("option", "oddRowsHighlight", true);
    // Numero de celda
    $grid.pqGrid("option", "numberCell", true);
    // Comprimir altura
    // $grid.pqGrid("option", "flexHeight", true);
    // Comprimir Anchura
    // $grid.pqGrid("option", "flexWidth", true);
    // $grid.pqGrid("option", "freezeCols",1);
    // Permite cambiar el tamaño
    // $grid.pqGrid("option", "resizable", true);

    // Grabar la modificacion
    $grid.on("pqgridcellsave", function (evt, ui) {
        var ID = ui.rowData["Numero"];
        var TITULO = ui.rowData["Comentario"];
        var USUARIO = ui.rowData["Usuario"];
        var CLASE = ui.rowData["Clase"];
        var usuario = usuariovalidado; // document.getElementById('usuariovalidado');

        if (USUARIO.trim() == usuario.trim()) {
            var url = "select/modificarComentario.php?Id=" + ID + "&Titulo=" + encodeURIComponent(TITULO) + "&Clase=" + encodeURIComponent(CLASE) + "&usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=" + tarea;

            $.ajax({
                url: url,
                success: function (response) {
                    $("#grid_array_comentarios_plegar").pqGrid("refreshDataAndView");
                }
            });




        } else {

            // **********
            $("#dialog-iniciar").html('<img style="width:20px;height:20px;" src="./imagenes/alert.png">Los comentarios pertenecen a ' + usuario + ' no puede modificarlos');
            $("#dialog-iniciar").dialog({
                resizable: false,
                title: 'Modificación de comentarios',
                width: 600,
                height: 400,
                modal: false,
                buttons: {
                    "OK": function () {
                        $(this).dialog("close");

                    }
                }
            });
            // **********
        }
    });

    function initRowCom() {

        iniciotareacomentario(tarea);
        $("#" + target).pqGrid("refreshDataAndView");


    }

    // ************************************GRID
    // ****************************************
}

function crearslidehistorico(num) {

    if (filas_historico != -1) {
        if ((fecha_historial + num) < 1) {
            fecha_historial = fecha_historial;
        } else {
            if ((fecha_historial + num) > filas_historico) {
                fecha_historial = fecha_historial;
            } else {
                fecha_historial = fecha_historial + num;
            }
        }
    } else {
        fecha_historial = fecha_historial + num;
    }

    var url = "historial/consultahistorial.php?tarea=" + ID_SELECCIONADA + "&fecha=" + encodeURIComponent(fecha_historial);
    $.ajax({
        type: "JSON",
        url: url,
        success: function (data) {

            var tools = JSON.parse(data);
            filas_historico = tools.data[0].FILAS;


            var el_usuario = tools.data[0].USUARIOCAMBIA;
            var el_dia = tools.data[0].FECHACAMBIO;

            var Fecha_Necesidad = tools.data[0].Fecha_Necesidad;
            var Fecha_objetivo = tools.data[0].Fecha_objetivo;
            var Fecha_fin = tools.data[0].Fecha_fin;
            var Fecha_inicio = tools.data[0].Fecha_inicio;
            var Fecha_alta = tools.data[0].Fecha_alta;
            var Fecha_Solicitud = tools.data[0].Fecha_Solicitud;
            $("#fechaalta").val(Fecha_alta);
            $("#fechasolicitud").val(Fecha_Solicitud);
            $("#fechaobjetivo").val(Fecha_objetivo);
            $("#fechanecesidad").val(Fecha_Necesidad);

            var Tipo = tools.data[0].Tipo;
            var Categoria = tools.data[0].Categoria;
            var SubCategoria = tools.data[0].SubCategoria;
            var Valoracion = tools.data[0].Valoracion;
            var Estado = tools.data[0].Estado;
            $("#selecttipo").val(Tipo);
            $("#selectcategoria").val(Categoria);
            $("#selectsubcategoria").val(SubCategoria);
            $("#selectvaloracion").val(Valoracion);
            $("#selectestado").val(Estado);

            var usuario = tools.data[0].usuario;
            var solicitado = tools.data[0].solicitado;
            var asignado = tools.data[0].asignado;
            $("#autocompleteusuario").val(usuario);
            $("#autocompletesolicitado").val(solicitado);
            $("#autocompleteasignado").val(asignado);

            var BAP = tools.data[0].BAP;
            var Referencia = tools.data[0].Referencia;
            var Cola = tools.data[0].Cola;
            $("#selectbap").val(BAP);
            $("#referencia").val(Referencia);
            $("#selectcola").val(Cola);

            var PORCENTAJE = tools.data[0].PORCENTAJE;
            $("#porcentaje").val(PORCENTAJE);
            // SLIDER
            // var porcentaje = document.getElementById('porcentaje');
            var porcentaje = 0;
            var valordecimal = parseFloat(porcentaje.value);
            valordecimal = valordecimal * 100;
            // var porcentaje_visible = document.getElementById('porcentaje_visible');
            // var porcentaje_visible = document.getElementById('porcentaje_visible');
            // porcentaje_visible.innerHTML = '<i>Porcentaje completado al</i> ' + valordecimal + "%";

            $("#slider").slider({
                min: 0,
                max: 100,
                value: valordecimal,
                change: function (event, ui) {
                    // var porcentaje = document.getElementById('porcentaje');
                    // porcentaje.value = (ui.value / 100);
                    // var porcentaje_visible = document.getElementById('porcentaje_visible');
                    // porcentaje_visible.innerHTML = '<i>Porcentaje completado al</i> ' + ui.value + "%";
                }
            });
            // SLIDER

            var TAREA_PROYECTO_DESCRIPCION = tools.data[0].TAREA_PROYECTO_DESCRIPCION;
            $("#autocomplete").val(TAREA_PROYECTO_DESCRIPCION);

            var horas = tools.data[0].horas;
            $("#horasestimadas").val(horas);

            var prioridad = tools.data[0].prioridad;
            $("#selectprioridad").val(prioridad);

            var Departamento = tools.data[0].Departamento;
            var Programa = tools.data[0].Programa;
            $("#selectdepartamento").val(Departamento);
            $("#div_programa").load("select/obtener_relacion.php", {
                departamento: Departamento,
                programa: ""
            }, function (response, status, xhr) {
                $("#selectprograma").val(Programa);
            });

            var DESCRIPCION = tools.data[0].DESCRIPCION;
            var TITULO = tools.data[0].TITULO;
            $("#titulo").val(TITULO);

            var textareadescripcion = '<textarea name="descripcion" id="descripcion" style="height:100%">' + DESCRIPCION + '</textarea>';
            var dialogo = document.getElementById('las_observaciones_plegar');
            dialogo.innerHTML = '<div style="height:75%;">' + textareadescripcion + '</div>';

            editor = new TINY.editor.edit('editor', {
                id: 'descripcion',
                width: '100%',
                height: '100%',
                cssclass: 'tinyeditor',
                controlclass: 'tinyeditor-control',
                rowclass: 'tinyeditor-header',
                dividerclass: 'tinyeditor-divider',
                controls: ['bold', 'italic', 'underline', 'strikethrough', '|', 'subscript', 'superscript', '|',
                    'orderedlist', 'unorderedlist', '|', 'outdent', 'indent', '|', 'leftalign',
                    'centeralign', 'rightalign', 'blockjustify', '|', 'unformat', '|', 'undo', 'redo', 'n',
                    'font', 'size', 'style', '|', 'image', 'hr', 'link', 'unlink', '|', 'print'
                ],
                footer: true,
                fonts: ['Verdana', 'Arial', 'Georgia', 'Trebuchet MS'],
                xhtml: true,
                cssfile: 'custom.css',
                bodyid: 'editor',
                footerclass: 'tinyeditor-footer',
                // toggle: {text: 'source', activetext: 'wysiwyg', cssclass: 'toggle'},
                resize: {
                    cssclass: 'resize'
                }
            });


            editor.post();


            var dias = el_usuario + '<br>' + el_dia;


            var url = "historial/consultahistorialcambios.php?tarea=" + ID_SELECCIONADA + "&fecha=" + encodeURIComponent(fecha_historial);
            $.ajax({
                url: url,
                success: function (data) {

                    var dialogo = document.getElementById('slide-historico');
                    dialogo.innerHTML = '<div style="clear: both;"><div class="slide-blanco-antes"><img src="imagenes/horas_1.png" width=\"20\" height=\"20\" onClick="crearslidehistorico(-1);"/></div><div id="las_horas" align="center" style="overflow:hidden;float:left;">' + dias +
                            '</div><div class="slide-blanco-despues"><img src="imagenes/horas.png" width=\"20\" height=\"20\" onClick="crearslidehistorico(+1);"/></div>' + data + '</div>';
                    if (num == 0) {
                        $("#slide-historico").dialog({
                            resizable: false,
                            height: 200,
                            width: 270,
                            modal: false,
                            dragStop: function (event, ui) {},

                            open: function (event, ui) {
                                // $( "#slide-semanal" ).draggable();
                            }

                        });
                    }
                    if (num == 1) {
                    }
                    if (num == -1) {

                    }
                }
            });
        }
    });




    // -----------------------

}

function crearslidecomentarios(num) {
    var lunes = '';
    var martes = '';
    var miercoles = '';
    var jueves = '';
    var viernes = '';
    var sabado = '';
    var domingo = '';
    if (num == 0) {
        semana = 0;
    } else {
        semana = semana + num;
    }
    var url = "slide/consultaSlidecomentarios.php?semana=" + semana + "&usuario=" + encodeURIComponent(usuariovalidado);
    $.ajax({
        type: "JSON",
        url: url,
        success: function (data) {
            var tools = JSON.parse(data);
            var meses = new Array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
            var el_inicio = tools.data[0].DIA_INICIO + meses[tools.data[0].MES_INICIO - 1];
            var el_fin = tools.data[0].DIA_FIN + meses[tools.data[0].MES_FIN - 1];
            var solapado = '';
            var dias = '<div class="slide-antes">' + el_inicio + '</div><div class="slide-dias">L</div><div class="slide-dias">M</div><div class="slide-dias">X</div><div class="slide-dias">J</div><div class="slide-dias">V</div><div class="slide-dias-fiesta">S</div><div class="slide-dias-fiesta">D</div><div class="slide-despues">' + el_fin + '</div>';
            for (var i = 0; i < tools.data.length; i++) {
                solapado = '';
                if (tools.data[i].SOLAPADO != 0) {
                    solapado = '*';
                }
                if (tools.data[i].NUMERO_DIA == 1) {
                    lunes = '<div class="slide-horas" onClick="ver_fecha_comentarios(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                }
                if (tools.data[i].NUMERO_DIA == 2) {
                    martes = '<div class="slide-horas" onClick="ver_fecha_comentarios(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                }
                if (tools.data[i].NUMERO_DIA == 3) {
                    miercoles = '<div class="slide-horas" onClick="ver_fecha_comentarios(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                }
                if (tools.data[i].NUMERO_DIA == 4) {
                    jueves = '<div class="slide-horas" onClick="ver_fecha_comentarios(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                }
                if (tools.data[i].NUMERO_DIA == 5) {
                    viernes = '<div class="slide-horas" onClick="ver_fecha_comentarios(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                }
                if (tools.data[i].NUMERO_DIA == 6) {
                    sabado = '<div class="slide-horas" onClick="ver_fecha_comentarios(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                }
                if (tools.data[i].NUMERO_DIA == 7) {
                    domingo = '<div class="slide-horas" onClick="ver_fecha_comentarios(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                }
            }
            var dialogo = document.getElementById('slide-semanal');
            dialogo.title = 'Resumen Comentarios Semanales';
            dialogo.innerHTML = dias + '<div style="clear: both;"><div class="slide-blanco-antes"><img src="imagenes/horas_1.png" width=\"20\" height=\"20\" onClick="crearslidecomentarios(-1);"/></div><div id="las_horas" style="overflow:hidden;float:left;">' + lunes + martes + miercoles + jueves + viernes + sabado + domingo + '</div><div class="slide-blanco-despues"><img src="imagenes/horas.png" width=\"20\" height=\"20\" onClick="crearslidecomentarios(+1);"/></div></div>';
            if (num == 0) {
                $("#slide-semanal").dialog({
                    resizable: false,
                    height: 150,
                    width: 713,
                    modal: false,
                    dragStop: function (event, ui) {},
                    open: function (event, ui) {}
                });
            }

        }
    });
}

function crearslidesemanal(num) {
    var lunes = '';
    var martes = '';
    var miercoles = '';
    var jueves = '';
    var viernes = '';
    var sabado = '';
    var domingo = '';
    var todo = '';
    var totalhoras = 0;

    if (num == 0 || num == 2) {
        semana = 0;
    } else {
        semana = semana + num;
    }
    var url = "slide/consultaSlide.php?semana=" + semana + "&usuario=" + encodeURIComponent(usuariovalidado);
    $.ajax({
        type: "JSON",
        url: url,
        success: function (data) {
            var tools = JSON.parse(data);
            var meses = new Array("ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
            var el_inicio = tools.data[0].DIA_INICIO + meses[tools.data[0].MES_INICIO - 1];
            var el_fin = tools.data[0].DIA_FIN + meses[tools.data[0].MES_FIN - 1];
            var solapado = '';
            var solapadoclass = '';
            var dias = '<div class="slide-antes">' + el_inicio + '</div><div class="slide-dias">L</div><div class="slide-dias">M</div><div class="slide-dias">X</div><div class="slide-dias">J</div><div class="slide-dias">V</div><div class="slide-dias-fiesta">S</div><div class="slide-dias-fiesta">D</div><div class="slide-despues">' + el_fin + '</div>';

            todo += '<div class="slide-antes"><small><br></small><img src="imagenes/reload.png" width=\"16\" height=\"16\" onclick="crearslidesemanal(2);" style="cursor:pointer" /></div>';
            todo += '<div class="resumen_horas_fecha float_left" onClick="crearslidesemanal(-1);">' + el_inicio + '<br><img src="imagenes/horas_1.png" width=\"12\" height=\"12\" /></div>';

            for (var i = 0; i < tools.data.length; i++) {
                totalhoras += tools.data[i].MINUTOS;

                solapado = '';
                solapadoclass = ' ';
                if (tools.data[i].SOLAPADO != 0) {
                    solapado = '*';
                    solapadoclass = ' solapado ';
                }
                if (tools.data[i].NUMERO_DIA == 1) {
                    lunes = '<div class="slide-horas" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                    todo += '<div class="resumen_horas_dia float_left' + solapadoclass + '" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">L ' + tools.data[i].HORAS + ' |</div>'
                }
                if (tools.data[i].NUMERO_DIA == 2) {
                    martes = '<div class="slide-horas" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                    todo += '<div class="resumen_horas_dia float_left' + solapadoclass + '" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">M ' + tools.data[i].HORAS + ' |</div>'
                }
                if (tools.data[i].NUMERO_DIA == 3) {
                    miercoles = '<div class="slide-horas" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                    todo += '<div class="resumen_horas_dia float_left' + solapadoclass + '" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">X ' + tools.data[i].HORAS + ' |</div>'
                }
                if (tools.data[i].NUMERO_DIA == 4) {
                    jueves = '<div class="slide-horas" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                    todo += '<div class="resumen_horas_dia float_left' + solapadoclass + '" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">J ' + tools.data[i].HORAS + ' |</div>'
                }
                if (tools.data[i].NUMERO_DIA == 5) {
                    viernes = '<div class="slide-horas" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                    todo += '<div class="resumen_horas_dia float_left' + solapadoclass + '" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">V ' + tools.data[i].HORAS + ' |</div>'
                }
                if (tools.data[i].NUMERO_DIA == 6) {
                    sabado = '<div class="slide-horas" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                    todo += '<div class="resumen_horas_dia float_left' + solapadoclass + 'resumen_horas_festivo" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');"><span>S</span> ' + tools.data[i].HORAS + ' |</div>'
                }
                if (tools.data[i].NUMERO_DIA == 7) {
                    domingo = '<div class="slide-horas" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');">' + tools.data[i].HORAS + solapado + '</div>';
                    todo += '<div class="resumen_horas_dia float_left' + solapadoclass + 'resumen_horas_festivo" onClick="ver_fecha_horas(\'' + tools.data[i].FECHA + '\');"><span>D</span> ' + tools.data[i].HORAS + '</div>'
                }

            }
            var dialogo = document.getElementById('slide-semanal');
            dialogo.title = 'Resumen Horas Semanales';
            dialogo.innerHTML = dias + '<div style="clear: both;"><div class="slide-blanco-antes"><img src="imagenes/horas_1.png" width=\"20\" height=\"20\" onClick="crearslidesemanal(-1);"/></div><div id="las_horas" style="overflow:hidden;float:left;">' + lunes + martes + miercoles + jueves + viernes + sabado + domingo + '</div><div class="slide-blanco-despues"><img src="imagenes/horas.png" width=\"20\" height=\"20\" onClick="crearslidesemanal(+1);"/></div></div>';
            if (num == 0) {
                $("#slide-semanal").dialog({
                    resizable: false,
                    height: 150,
                    width: 713,
                    modal: false,
                    dragStop: function (event, ui) {},
                    open: function (event, ui) {}
                });
            }
            var horas = Math.floor(totalhoras / 60);
            var minutos = totalhoras % 60;

            if (minutos < 10) {
                minutos = "0" + minutos;
            } else {
                minutos = "" + minutos;
            }

            todo += '<div class="resumen_horas_fecha float_right "onClick="crearslidesemanal(+1);">' + el_fin + '<br><img src="imagenes/horas.png" width=\"12\" height=\"12\" /></div>';
            todo += '<div class="resumen_horas_dia float_right"> [T ' + horas + ':' + minutos + ']</div>'

            $("#resumen_horas").html(todo);
        }
    });
}

function ver_fecha_comentarios(fecha) {

    $("#slide-semanal").dialog("close");

    FECHA_HORA_SELECCIONADA = fecha;

    cambia_menu(10);

    new JsDatePick({
        useMode: 2,
        target: "fechacalculohorasenundia",
        dateFormat: "%d-%m-%Y"
    });

    var mifecha = document.getElementById('fechacalculohorasenundia');
    mifecha.value = fecha;

    CargaGridComentariosEnUnDia();
    CalculaTotalComentarios(fecha);
}

function ver_fecha_horas(fecha) {

    if ($("#slide-semanal").hasClass('ui-dialog-content')) {
        $("#slide-semanal").dialog("close");
    }

    // FECHA_HORA_SELECCIONADA = fecha;

    cambia_menu(10);

    jQuery('#fechacalculohorasenundia').datetimepicker({
        datepicker: true,
        timepicker: false,
        format: 'd-m-Y',
        weeks: true,
        dayOfWeekStart: 1,
    });
    /*
     new JsDatePick({
     useMode: 2,
     target: "fechacalculohorasenundia",
     dateFormat: "%d-%m-%Y"
     });*/

    var mifecha = document.getElementById('fechacalculohorasenundia');
    mifecha.value = fecha;

    FECHA_HORA_SELECCIONADA = fecha;

    // CargaGridHorasEnUnDia();
    calcularLasHorasUnDia();

    // $grid_horas_en_un_dia.pqGrid("refreshDataAndView");
    CalculaTotalHoras(fecha);

}

function CalculaTotalHoras(fecha) {

    var url = "select/consultasumahoras.php?dia=" + fecha + "&usuario=" + encodeURIComponent(usuariovalidado);
    $.ajax({
        type: "GET",
        url: url,
        success: function (data) {
            $grid_horas_en_un_dia.pqGrid("option", "title", "Lista de secciones . Total: " + data);
            $grid_horas_en_un_dia.pqGrid("refreshDataAndView");
        }
    });
}

function CalculaTotalComentarios(fecha) {
    var url = "select/consultasumacomentarios.php?dia=" + fecha + "&usuario=" + encodeURIComponent(nombreusuariovalidado);
    $.ajax({
        type: "GET",
        url: url,
        success: function (data) {
            $("#horasunundia_grid").pqGrid("option", "title", "Lista de secciones . Total: " + data);

        }
    });
}

function CargaGridTareas() {

    var mi_titulo = ObtenerTitulo();
    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '100%',
        height: '100%',
        title: mi_titulo,
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
        editModel: {
            saveKey: '13'
        },
        editable: false,
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        },
        filterModel: {
            on: true,
            mode: "AND",
            header: true
        }
    };


    // Columnas
    obj.colModel = [

        {
            title: "Id",
            width: 70,
            dataType: "integer",
            editable: false,
            dataIndx: "Id",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Id", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Título",
            width: 600,
            dataType: "string",
            editable: false,
            dataIndx: "Título",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Título", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Estado",
            width: 90,
            dataType: "string",
            hidden: false,
            editable: false,
            dataIndx: "Estado",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Estado", $(this).val());
                        }
                    }]
            },
            render: function (ui) {
                var rowData = ui.rowData;
                if (rowData["iniciado_sn"] == "1") {
                    return "<img src='imagenes/play.png' width=\"5\" height=\"5\" />&nbsp;" + rowData["Estado"];
                } else {
                    return rowData["Estado"];
                }
            },
            editor: {
                type: 'select',
                // options: categorias
                options: function (ui) {
                    return estadostarea;
                }
            },
            validations: [{
                    type: 'minLen',
                    value: 1,
                    msg: "Required"
                }]
        },
        {
            title: "Solicitado",
            width: 120,
            dataType: "string",
            hidden: false,
            editable: false,
            dataIndx: "Solicitado",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Solicitado", $(this).val());
                        }
                    }]
            }


        },
        {
            title: "Asignado a",
            width: 90,
            dataType: "string",
            hidden: false,
            editable: false,
            dataIndx: "Asignado a",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Asignado a", $(this).val());
                        }
                    }]
            }

        },
        {
            title: "Fecha alta",
            width: 70,
            dataType: "date",
            editable: false,
            dataIndx: "Fecha alta",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Fecha alta", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Fecha objetivo",
            width: 90,
            dataType: "date",
            hidden: true,
            editable: false,
            dataIndx: "Fecha objetivo",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Fecha objetivo", $(this).val());
                        }
                    }]
            }

        },
        {
            title: "Tipo",
            width: 90,
            dataType: "string",
            hidden: false,
            editable: false,
            dataIndx: "Tipo",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Tipo", $(this).val());
                        }
                    }]
            },
            editor: {
                type: 'select',
                // options: categorias
                options: function (ui) {
                    return tipos;
                }
            },
            validations: [{
                    type: 'minLen',
                    value: 1,
                    msg: "Required"
                }]
        },
        {
            title: "Tarea / Proyecto",
            width: 450,
            dataType: "string",
            hidden: false,
            editable: false,
            dataIndx: "Tarea / Proyecto",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Tarea / Proyecto", $(this).val());
                        }
                    }]
            },
            validations: [{
                    type: 'minLen',
                    value: 1,
                    msg: "Required"
                },
                {
                    type: function (ui) {
                        var value = ui.value;
                        if ($.inArray(ui.value, countries) == -1) {
                            ui.msg = value + " not found in list";
                            return false;
                        }
                    }
                }
            ]
        },
        {
            title: "iniciado_sn",
            width: 90,
            dataType: "string",
            hidden: true,
            editable: false,
            dataIndx: "iniciado_sn"
        }
    ];

    // Datos
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Id"],
        sortDir: ["down"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],

        url: "select/consultaTareas.php" + ObtenerRestoFiltros(),

        getData: function (dataJSON, textStatus, jqXHR) {

            var data = dataJSON.data;

            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    // Seleccionar la fila
    obj.rowSelect = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Id"];
    };

    // Doble Click
    obj.cellDblClick = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Id"];
        cambia_menu(5);
    };

    // Barra de Tareas
    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

        // BOTON NUEVA
        $("<span>Nueva</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-plus"
            }
        }).click(function (evt) {
            addRow();
        });
        $.ajax({
            url: "select/obtener_permiso_pantalla.php?id=1&usuario=" + encodeURIComponent(usuariovalidado),
            success: function (response) {
                if (response == '1') {
                    $("<span>Nueva Alta</span>").appendTo($toolbar).button({
                        icons: {
                            primary: "ui-icon-contact"
                        }
                    }).click(function (evt) {
                        addRowAlta();
                    });
                }
            }
        });
        // BOTON MODIFICAR
        $("<span>Modificar</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-pencil"
            }
        }).click(function (evt) {
            editRow();
        });




    };

    var $grid = $("#grid_array").pqGrid(obj);
    $("#grid_array").pqGrid({
        collapsible: false
    });
    // $grid.pqGrid( "option", "height", '100%' );
    $grid.pqGrid("option", "freezeCols", 1);
    $grid.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });
    // Lineas de separacion verticales
    $grid.pqGrid("option", "columnBorders", true);
    // Lineas de separacion horizontales
    $grid.pqGrid("option", "rowBorders", true);
    // Sombreado en las filas
    $grid.pqGrid("option", "oddRowsHighlight", true);

    // Permite cambiar el tamaño
    // $grid.pqGrid("option", "resizable", true);

    function editRow() {
        if (ID_SELECCIONADA == '-1') {
            // **********
            $("#dialog-iniciar").html('<img style="width:20px;height:20px;" src="./imagenes/alert.png">Seleccione una tarea a modificar');
            $("#dialog-iniciar").dialog({
                resizable: false,
                title: 'Modificar Tarea',
                width: 600,
                height: 400,
                modal: false,
                buttons: {
                    "OK": function () {
                        $(this).dialog("close");

                    }
                }
            });
            // **********
        } else {
            cambia_menu(5);
        }
    }

    function addRow() {
        cambia_menu(6);
    }

    function addRowAlta() {
        cambia_menu(16);
    }

    function getRowIndx() {
        var arr = $grid.pqGrid("selection", {
            type: 'row',
            method: 'getSelection'
        });
        if (arr && arr.length > 0) {
            var rowIndx = arr[0].rowIndx;
            return rowIndx;
        } else {

            return null;
        }
    }

    function filter(dataIndx, value) {
        $grid.pqGrid("filter", {
            data: [{
                    dataIndx: dataIndx,
                    value: value
                }]
        });
    }

    // ************************************GRID
    // ****************************************
}

function CargaGridResultado() {

    var from_casilla = document.getElementById('from');
    var from_valor = from_casilla.value;
    var to_casilla = document.getElementById('to');
    var to_valor = to_casilla.value;

    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '100%',
        height: '80%',
        title: "prueba",
        hoverMode: 'row',
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        }
    };

    // Columnas
    obj.colModel = [{
            title: "Numero",
            width: 70,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Numero"
        },
        {
            title: "Tarea",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Tarea"
        },
        {
            title: "Inicio",
            width: 150,
            dataType: "date",
            dataIndx: "Inicio",
            render: function (ui) {
                var rowData = ui.rowData;
                if (rowData["Accion"] == "borrar" || rowData["Accion"] == "nuevo inicio") {
                    return "<span style=\"color: red;\">" + rowData["Inicio"] + "</span>";
                } else {
                    return rowData["Inicio"];
                }
            }
        },
        {
            title: "Fin",
            width: 150,
            dataType: "date",
            dataIndx: "Fin",
            render: function (ui) {
                var rowData = ui.rowData;
                if (rowData["Accion"] == "borrar" || rowData["Accion"] == "nuevo fin") {
                    return "<span style=\"color: red;\">" + rowData["Fin"] + "</span>";
                } else {
                    return rowData["Fin"];
                }
            }
        },
        {
            title: "Accion",
            width: 250,
            dataType: "text",
            editable: false,
            hidden: false,
            dataIndx: "Accion",
            render: function (ui) {
                var rowData = ui.rowData;
                if (rowData["Accion"] == "borrar") {
                    return "<img src='imagenes/alert.png' width=\"20\" height=\"20\" /><span style=\"color: red;\">&nbsp;" + rowData["Accion"] + "</span>";
                }
                if (rowData["Accion"] == "tarea principal") {
                    return "<img src='imagenes/ok.png' width=\"16\" height=\"16\" /><span style=\"color: violet;\">&nbsp;" + rowData["Accion"] + "</span>";
                }
                if (rowData["Accion"] == "tarea secundaria") {
                    return "<img src='imagenes/ok.png' width=\"16\" height=\"16\" /><span style=\"color: violet;\">&nbsp;" + rowData["Accion"] + "</span>";
                }
                if (rowData["Accion"] == "nuevo inicio") {
                    return "<img src='imagenes/right.png' width=\"10\" height=\"10\" /><span style=\"color: green;\">&nbsp;" + rowData["Accion"] + "</span>";
                }
                if (rowData["Accion"] == "nuevo fin") {
                    return "<img src='imagenes/left.png' width=\"10\" height=\"10\" /><span style=\"color: green;\">&nbsp;" + rowData["Accion"] + "</span>";
                }
                if (rowData["Accion"] == "nuevo inicio parte 1") {
                    return "<img src='imagenes/right.png' width=\"10\" height=\"10\" /><img src='imagenes/left.png' width=\"10\" height=\"10\" /><span style=\"color: blue;\">&nbsp;" + rowData["Accion"] + "</span>";
                }
                if (rowData["Accion"] == "nuevo fin parte 2") {
                    return "<img src='imagenes/right.png' width=\"10\" height=\"10\" /><img src='imagenes/left.png' width=\"10\" height=\"10\" /><span style=\"color: blue;\">&nbsp;" + rowData["Accion"] + "</span>";
                }
                return rowData["Accion"];
            }
        },
        {
            title: "Minutos",
            width: 20,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Minutos"
        },
        {
            title: "Nuevo Inicio",
            width: 150,
            dataType: "date",
            editable: false,
            hidden: false,
            dataIndx: "Nuevo Inicio"
        },
        {
            title: "Nuevo Fin",
            width: 150,
            dataType: "date",
            editable: false,
            hidden: false,
            dataIndx: "Nuevo Fin"
        },
        {
            title: "Titulo",
            width: 250,
            dataType: "text",
            editable: false,
            hidden: false,
            dataIndx: "Titulo"
        },
        {
            title: "Usuario",
            width: 145,
            dataType: "text",
            editable: false,
            hidden: true,
            dataIndx: "Usuario"
        }

    ];


    // Datos
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 100,
        sortIndx: ["Numero"],
        sortDir: ["down"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [100],
        url: "select/iniciar_tarea_masiva.php?id=1&tarea=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado) + "&fechainicio=" + encodeURIComponent(from_valor) + "&fechafin=" + encodeURIComponent(to_valor),
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };


    var $grid = $("#grid_array_resultado").pqGrid(obj);
    // Mostrar la parte de arriba
    $grid.pqGrid("option", "topVisible", false);
    $grid.pqGrid("option", "showTitle", false);
    $grid.pqGrid("option", "collapsible", false);
    // Mostrar la parte de abajo
    // $grid.pqGrid("option", "bottomVisible", true);
    // Lineas de separacion verticales
    $grid.pqGrid("option", "columnBorders", true);
    // Lineas de separacion horizontales
    $grid.pqGrid("option", "rowBorders", true);
    // Sombreado en las filas
    $grid.pqGrid("option", "oddRowsHighlight", true);
    // Numero de celda
    $grid.pqGrid("option", "numberCell", true);
    // Comprimir altura
    // $grid.pqGrid("option", "flexHeight", true);
    // Comprimir Anchura
    // $grid.pqGrid("option", "flexWidth", true);
    // $grid.pqGrid("option", "freezeCols",1);
    // Barras de scroll
    $grid.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });
    // Permite cambiar el tamaño
    // $grid.pqGrid("option", "resizable", true);

    // ************************************GRID
    // ****************************************

}

function PreCargaGridResultado(id) {
    var lfckv = document.getElementById("masivo_principal").checked


    var from_casilla = document.getElementById('from');
    var from_valor = from_casilla.value;
    var to_casilla = document.getElementById('to');
    var to_valor = to_casilla.value;
    var minimo_configurado_casilla = document.getElementById('minimo_configurado');
    var minimo_configurado_valor = minimo_configurado_casilla.value;

    var migridresult = document.getElementById('grid_array_resultado');
    migridresult.style.display = 'block';
    if (id == 1) {
        var btnEJECUTAR = document.getElementById('btn_calcularyejecutar');
        btnEJECUTAR.style.visibility = 'visible';
        btnEJECUTAR.style.display = '';
    } else {
        var btnEJECUTAR = document.getElementById('btn_calcularyejecutar');
        btnEJECUTAR.style.visibility = 'hidden';
        btnEJECUTAR.style.display = 'none';
    }
    if (lfckv == true) {
        $("#grid_array_resultado").pqGrid("option", "dataModel.url", "select/iniciar_tarea_masiva.php?tipoestablecer=1&id=" + id + "&tarea=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado) + "&fechainicio=" + encodeURIComponent(from_valor) + "&fechafin=" + encodeURIComponent(to_valor) + "&minimo=" + minimo_configurado_valor);
    } else {
        $("#grid_array_resultado").pqGrid("option", "dataModel.url", "select/iniciar_tarea_masiva.php?tipoestablecer=2&id=" + id + "&tarea=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado) + "&fechainicio=" + encodeURIComponent(from_valor) + "&fechafin=" + encodeURIComponent(to_valor) + "&minimo=" + minimo_configurado_valor);
    }
    $("#grid_array_resultado").pqGrid("refreshDataAndView");

}

function mostrarDias(fechaInicio, fechaFin) {

    var diasTotal = 0;

    if (fechaInicio.length < 10 || fechaFin.length < 10) {

    } else {

        // Separamos las fechas en dias, meses y años
        var diaInicio = fechaInicio.substring(0, 2);
        var mesInicio = fechaInicio.substring(3, 5);
        var anoInicio = fechaInicio.substring(6, 10);

        var diaFin = fechaFin.substring(0, 2);
        var mesFin = fechaFin.substring(3, 5);
        var anoFin = fechaFin.substring(6, 10);

        // Los meses empiezan en 0 por lo que le restamos 1
        mesFin = mesFin - 1;
        mesInicio = mesInicio - 1;

        // Creamos una fecha con los valores que hemos sacado
        var fInicio = new Date(anoInicio, mesInicio, diaInicio);
        var fFin = new Date(anoFin, mesFin, diaFin);

        diasTotal = 0;

        if (fFin > fInicio) {

            // Para sumarle 365 días tienen que haber 2 años de diferencia
            // Si no solamente sumo los días entre meses
            anoInicio++;
            while (anoFin > anoInicio) {


                if (esBisiesto(anoFin)) {
                    dias_e_anio = 366;
                } else {
                    dias_e_anio = 365;
                }
                diasTotal = diasTotal + dias_e_anio;
                anoFin--;
            }

            // Para sumarle los días de un mes completo, tengo que ver que haya diferencia de 2 meses
            mesInicio++;
            while (mesFin > mesInicio) {
                dias_e_mes = getDays(mesFin - 1, anoFin);
                diasTotal = diasTotal + dias_e_mes;
                mesFin--;
            }

            // Solamente falta sumar los días
            mesInicio--;
            if (mesInicio == mesFin) {
                diasTotal = diaFin - diaInicio + 1;
            } else {

                // Saco los días desde el mesInicio hasta fin de mes
                dias_e_mes = getDays(mesInicio, anoInicio);
                diasTotal = diasTotal + (dias_e_mes - diaInicio) + 1;
                // ahora saco los días desde el principio de mesFin hasta el día
                diasTotal = diasTotal + parseInt(diaFin);

            }
        }

        // Si la fechaFin es mayor
        else if (fechaFin < fechaInicio) {
            diasTotal = 0;
        }

        // Si las fechas son iguales
        else {
            diasTotal = 1;
        }


        horas = calcular_horas(fechaInicio.substring(16, 5), fechaFin.substring(16, 5));
        var horas_masivas = document.getElementById('horas_masivas');
        horas_masivas.innerHTML = 'Imputar ' + horas + ' durante ' + diasTotal + ' dias';

    }

}

function esBisiesto(ano) {
    if (ano % 4 == 0)
        return true
    /* else */
    return false
}

function getDays(month, year) {

    var ar = new Array(12)
    ar[0] = 31 // Enero
    if (esBisiesto) {
        ar[1] = 29
    } else {
        ar[1] = 28
    }
    ar[2] = 31 // Marzo
    ar[3] = 30 // Abril
    ar[4] = 31 // Mayo
    ar[5] = 30 // Junio
    ar[6] = 31 // Julio
    ar[7] = 31 // Agosto
    ar[8] = 30 // Septiembre
    ar[9] = 31 // Octubre
    ar[10] = 30 // Noviembre
    ar[11] = 31 // Diciembre

    return ar[month];
}

function calcular_horas(v1, v2) {

    horas1 = v1.split(":"); /* Mediante la función split separamos el string por ":" y lo convertimos en array. */
    horas2 = v2.split(":");
    horatotale = new Array();
    for (a = 0; a < 3; a++) /* bucle para tratar la hora, los minutos y los segundos */ {



        horas1[a] = (isNaN(parseInt(horas1[a]))) ? 0 : parseInt(horas1[a]) /* si horas1[a] es NaN lo convertimos a 0, sino convertimos el valor en entero */
        horas2[a] = (isNaN(parseInt(horas2[a]))) ? 0 : parseInt(horas2[a])
        horatotale[a] = (horas1[a] - horas2[a]); /* insertamos la resta dentro del array horatotale[a]. */
    }
    horatotal = new Date() /* Instanciamos horatotal con la clase Date de javascript para manipular las horas */
    horatotal.setHours(horatotale[0]); /* En horatotal insertamos las horas, minutos y segundos calculados en el bucle */
    horatotal.setMinutes(horatotale[1]);
    horatotal.setSeconds(horatotale[2]);
    return horatotal.getHours() + " horas y " + horatotal.getMinutes() + " minutos "; // +horatotal.getSeconds();
    /* Devolvemos el valor calculado en el formato hh:mm:ss */
}

function CargaGridAvisosPeronalizada() {

    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '100%',
        height: '100%',
        title: "Avisos Personalizados",
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
        editModel: {
            saveKey: '13'
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        }
    };

    // Columnas
    obj.colModel = [{
            title: "Id",
            width: 20,
            editable: false,
            dataType: "text",
            dataIndx: "Id"
        },
        {
            title: "Descripcion",
            width: 300,
            editable: false,
            dataType: "text",
            dataIndx: "Descripcion"
        },
        {
            title: "Activo",
            width: 30,
            editable: false,
            dataType: "text",
            dataIndx: "Activo"
        }
    ];


    // Datos
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Id"],
        sortDir: ["up"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10],
        url: "select/consultaAvisosPersonalizadas.php?usuario=" + encodeURIComponent(usuariovalidado),
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            ID_CONSULTA_USUARIO_SELECCIONADA = '-1';
            // expand the first row
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    // Barra de Tareas
    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));


    };
    obj.cellDblClick = function (evt, obj) {
        ID_AVISO = obj.rowData["Id"];


        var url = "select/ModificarEstadoLinea.php?id=" + ID_AVISO + "&usuario=" + encodeURIComponent(usuariovalidado);
        $.ajax({
            url: url
        }).done(function (data) {

            $("#configuracion_avisos_plegar").pqGrid("refreshDataAndView");

        }).always(function () {});


    };




    var $grid = $("#configuracion_avisos_plegar").pqGrid(obj);
    // Mostrar la parte de arriba
    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", true);
    $grid.pqGrid("option", "collapsible", false);
    // Mostrar la parte de abajo
    // $grid.pqGrid("option", "bottomVisible", true);
    // Lineas de separacion verticales
    $grid.pqGrid("option", "columnBorders", true);
    // Lineas de separacion horizontales
    $grid.pqGrid("option", "rowBorders", true);
    // Sombreado en las filas
    $grid.pqGrid("option", "oddRowsHighlight", true);
    // Numero de celda
    // $grid.pqGrid("option", "numberCell", true);
    // Comprimir altura
    // $grid.pqGrid("option", "flexHeight", true);
    // Comprimir Anchura
    // $grid.pqGrid("option", "flexWidth", true);
    // $grid.pqGrid("option", "freezeCols",1);
    // Barras de scroll
    $grid.pqGrid("option", "scrollModel", {
        horizontal: false
    }, {
        vertical: true
    });
    // Permite cambiar el tamaño
    // $grid.pqGrid("option", "resizable", true);


    // ************************************GRID
    // ****************************************

}

function CargaGridConsultasPeronalizada() {

    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '100%',
        height: '100%',
        title: "Consultas Personalizadas",
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
        editModel: {
            saveKey: '13'
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        },
        scrollModel: {
            pace: 'fast',
            autoFit: true,
            theme: true
        }
    };

    // Columnas
    obj.colModel = [{
            title: "Descripcion",
            width: 150,
            maxWidth: 150,
            dataType: "text",
            dataIndx: "Descripcion"
        },
        {
            title: "Numero",
            width: 30,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Numero"
        },
        {
            title: "Consulta",
            width: 250,
            dataType: "text",
            dataIndx: "Consulta"
        }
    ];


    // Datos
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Numero"],
        sortDir: ["up"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10],
        url: "select/consultaConsultasPersonalizadas.php?usuario=" + encodeURIComponent(usuariovalidado),
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            ID_CONSULTA_USUARIO_SELECCIONADA = '-1';
            // expand the first row
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    // Barra de Tareas
    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

        // Botonera
        $("<span>Nueva</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-plus"
            }
        }).click(function (evt) {
            newRow();
        });
        $("<span>Borrar</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-close"
            }
        }).click(function (evt) {
            deleteRow();
        });

    };

    // Seleccionar la fila
    obj.rowSelect = function (evt, obj) {
        ID_CONSULTA_USUARIO_SELECCIONADA = obj.rowData["Numero"];
    };
    // Seleccionar la fila
    obj.cellSelect = function (evt, obj) {
        ID_CONSULTA_USUARIO_SELECCIONADA = obj.rowData["Numero"];
    };
    // Doble Click
    obj.cellDblClick = function (evt, obj) {
        ID_CONSULTA_USUARIO_SELECCIONADA = obj.rowData["Numero"];
    };

    var $grid = $("#configuracion_consultas_plegar").pqGrid(obj);
    // Mostrar la parte de arriba
    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", true);
    $grid.pqGrid("option", "collapsible", false);
    $grid.pqGrid("option", "columnBorders", true);
    $grid.pqGrid("option", "rowBorders", true);

    // Grabar la modificacion
    $grid.on("pqgridcellsave", function (evt, ui) {


        var ID = ui.rowData["Numero"];
        var TITULO = ui.rowData["Descripcion"];
        var CONSULTA = ui.rowData["Consulta"];
        if (typeof (CONSULTA) === "undefined") {
        } else {
            var url = "consulta/modificar_consultaConsultasPersonalizadas.php?Id=" + ID + "&Descripcion=" + encodeURIComponent(TITULO) + "&Consulta=" + encodeURIComponent(CONSULTA);
            $.ajax({
                url: url
            }).done(function () {
                $("#configuracion_consultas_plegar").pqGrid("refreshDataAndView");

            });
        }

    });

    function deleteRow(obj) {
        if (ID_CONSULTA_USUARIO_SELECCIONADA == '-1') {

            var dialogo = document.getElementById('dialog-iniciar');
            dialogo.innerHTML = '<p><span class="ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/info.png' width=\"30\" height=\"30\" />&nbsp;" + 'Seleccione primero una consulta</p>';
            $("#dialog-iniciar").dialog({
                resizable: false,
                height: 250,
                modal: true,

                buttons: {
                    "Ok": function () {
                        $(this).dialog("close");
                    }

                }
            });
        } else {
            var url = "consulta/borrar_Consulta.php?Id=" + ID_CONSULTA_USUARIO_SELECCIONADA;
            $.ajax({
                url: url
            }).done(function () {
                $("#configuracion_consultas_plegar").pqGrid("refreshDataAndView");
                ID_CONSULTA_USUARIO_SELECCIONADA = '-1';
            });
        }


    }

    function newRow() {
        var url = "consulta/insertar_Consulta.php?usuario=" + encodeURIComponent(usuariovalidado);

        $.ajax({
            url: url
        }).done(function () {
            $("#configuracion_consultas_plegar").pqGrid("refreshDataAndView");
        });

    }

    // ************************************GRID
    // ****************************************

}

function CargaGridColas() {

    var usuariosti = [];
    $.ajax({
        url: "select/obtener_usuarios_ti.php?id=4",
        success: function (response) {

            usuariosti = response.split(",");
        }
    });

    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '40%',
        height: '100%',
        title: "Colas",
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
        editModel: {
            saveKey: '13'
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        }
    };

    // Columnas
    obj.colModel = [{
            title: "",
            minWidth: 27,
            width: 27,
            type: "detail",
            resizable: false,
            editable: false /* no need to mention dataIndx */
        },
        {
            title: "Cola",
            width: 170,
            dataType: "text",
            dataIndx: "Descripcion"
        },
        {
            title: "Numero",
            width: 30,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Numero"
        },
        {
            title: "Predeterminado",
            width: 30,
            dataType: "text",
            dataIndx: "Predeterminado",
            editor: {
                type: 'select',
                // options: categorias
                options: function (ui) {
                    return ["1", "0"];
                }
            },
            validations: [{
                    type: 'minLen',
                    value: 1,
                    msg: "Required"
                }]
        }
    ];


    // Datos
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Descripcion"],
        sortDir: ["up"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],
        url: "select/consultaColas.php",
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            // expand the first row
            ID_COLA_USUARIO_SELECCIONADA = '-1';
            ID_COLA_SELECCIONADA = '-1';


            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };
    obj.detailModel = {
        init: function (ui) {

            // debugger;
            var rowData = ui.rowData,
                    Numero = ui.rowData["Numero"],
                    // get markup of the detail template.
                    $tmpl = $("#tmpl"),
                    html = $tmpl.html();
            ID_COLA_SELECCIONADA = Numero;
            for (var key in rowData) {
                var cellData = (rowData[key] == null) ? "" : rowData[key];
                html = html.replace("<#=" + key + "#>", cellData);
            }
            ID_COLA_USUARIO_SELECCIONADA = '-1';

            // create detail place holder
            var $detail = $("<div></div>");
            $detail.html(html);

            // make a deep copy of gridDetailModel
            var objCopy = $.extend(true, {}, gridDetailModel);

            objCopy.dataModel.url = "cola/detalle_Cola.php?Numero=" + Numero;
            // append pqGrid in the 2nd tab.
            var $grid = $("<div></div>").appendTo($("#tabs-2", $detail)).pqGrid(objCopy);

            return $detail;
        }
    }



    // Barra de Tareas
    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

        // Botonera
        $("<span>Nueva</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-plus"
            }
        }).click(function (evt) {
            newRow();
        });
        $("<span>Borrar</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-close"
            }
        }).click(function (evt) {
            deleteRow();
        });

    };

    // Seleccionar la fila
    obj.rowSelect = function (evt, obj) {
        ID_COLA_SELECCIONADA = obj.rowData["Numero"];
    };
    // Seleccionar la fila
    obj.cellSelect = function (evt, obj) {
        ID_COLA_SELECCIONADA = obj.rowData["Numero"];
    };
    // Doble Click
    obj.cellDblClick = function (evt, obj) {
        ID_COLA_SELECCIONADA = obj.rowData["Numero"];
    };

    var $grid = $("#configuracion_colas_plegar").pqGrid(obj);
    // Mostrar la parte de arriba
    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", true);
    $grid.pqGrid("option", "collapsible", false);
    // Mostrar la parte de abajo
    // $grid.pqGrid("option", "bottomVisible", true);
    // Lineas de separacion verticales
    $grid.pqGrid("option", "columnBorders", true);
    // Lineas de separacion horizontales
    $grid.pqGrid("option", "rowBorders", true);
    // Sombreado en las filas
    $grid.pqGrid("option", "oddRowsHighlight", true);
    // Numero de celda
    // $grid.pqGrid("option", "numberCell", true);
    // Comprimir altura
    // $grid.pqGrid("option", "flexHeight", true);
    // Comprimir Anchura
    // $grid.pqGrid("option", "flexWidth", true);
    // $grid.pqGrid("option", "freezeCols",1);
    // Barras de scroll
    $grid.pqGrid("option", "scrollModel", {
        horizontal: false
    }, {
        vertical: true
    });
    // Permite cambiar el tamaño
    // $grid.pqGrid("option", "resizable", true);

    // Grabar la modificacion
    $grid.on("pqgridcellsave", function (evt, ui) {


        var ID = ui.rowData["Numero"];
        var TITULO = ui.rowData["Descripcion"];
        var PREDETERMINADO = ui.rowData["Predeterminado"];
        if (typeof (PREDETERMINADO) === "undefined") {
        } else {
            var url = "cola/modificar_Cola.php?Id=" + ID + "&Titulo=" + encodeURIComponent(TITULO) + "&Predeterminado=" + encodeURIComponent(PREDETERMINADO);
            $.ajax({
                url: url
            }).done(function () {
                $("#configuracion_colas_plegar").pqGrid("refreshDataAndView");
                ID_COLA_USUARIO_SELECCIONADA = '-1';
                actualizarlistacolas();

            });
        }

    });

    // details grid
    var gridDetailModel = {
        pageModel: {
            type: "local",
            rPP: 10,
            strRpp: ""
        },
        dataModel: {
            location: "remote",
            sorting: "local",
            dataType: "JSON",
            method: "GET",
            sortIndx: "Usuario",
            getData: function (dataJSON) {
                return {
                    data: dataJSON.data
                };
            }
        },
        colModel: [{
                title: "Cola",
                width: 10,
                hidden: true,
                dataIndx: "Cola"
            },
            {
                title: "Numero",
                width: 10,
                editable: false,
                dataIndx: "Numero"
            },
            {
                title: "Usuario",
                width: 10,
                dataType: "text",
                dataIndx: "Usuario",
                editor: {
                    type: 'select',
                    // options: categorias
                    options: function (ui) {
                        return usuariosti;
                    }
                },
                validations: [{
                        type: 'minLen',
                        value: 1,
                        msg: "Required"
                    }]
            }
        ],

        editable: true,
        showTitle: false,
        selectionModel: {
            type: 'cell'
        },
        editModel: {
            saveKey: '13'
        },
        collapsible: false,
        flexHeight: true,
        width: "100%",
        numberCell: {
            show: true
        },
        showTop: true,
        showBottom: true,
        scrollModel: {
            horizontal: true,
            vertical: true
        },
        cellSave: function (evt, ui) {
            var ID = ui.rowData["Numero"];
            var TITULO = ui.rowData["Usuario"];

            var url = "cola/modificarusuario_Cola.php?Id=" + ID + "&Titulo=" + encodeURIComponent(TITULO);
            $.ajax({
                url: url
            }).done(function () {
                $("#tabs-2").pqGrid("refreshDataAndView");
                ID_COLA_USUARIO_SELECCIONADA = '-1';
            });
        },
        render: function (evt, obj) {

            var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));
            // Botonera
            $("<span>Nuevo</span>").appendTo($toolbar).button({
                icons: {
                    primary: "ui-icon-circle-plus"
                }
            }).click(function (evt) {
                newRowchild(obj);
            });
            $("<span>Borrar</span>").appendTo($toolbar).button({
                icons: {
                    primary: "ui-icon-circle-minus"
                }
            }).click(function (evt) {
                deleteRowchild(obj);
            });
        },
        cellSelect: function (evt, obj) {

            ID_COLA_USUARIO_SELECCIONADA = obj.rowData["Numero"];

        }
    };

    function deleteRowchild(obj) {

        if (ID_COLA_USUARIO_SELECCIONADA == '-1') {

            var dialogo = document.getElementById('dialog-iniciar');
            dialogo.innerHTML = '<p><span class="ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/info.png' width=\"30\" height=\"30\" />&nbsp;" + 'Seleccione primero un usuario</p>';
            $("#dialog-iniciar").dialog({
                resizable: false,
                height: 250,
                modal: true,

                buttons: {
                    "Ok": function () {
                        $(this).dialog("close");
                    }

                }
            });
        } else {
            var url = "cola/borrarusuario_Cola.php?Id=" + ID_COLA_USUARIO_SELECCIONADA;

            $.ajax({
                url: url
            }).done(function () {
                $(".tabs-2").pqGrid("refresh");
            });
        }


    }

    function deleteRow(obj) {

        var url = "cola/borrar_Cola.php?Id=" + ID_COLA_SELECCIONADA;
        $.ajax({
            url: url
        }).done(function () {
            $("#configuracion_colas_plegar").pqGrid("refreshDataAndView");
            $("#configuracion_colas_plegar_2").pqGrid("refreshDataAndView");
            ID_COLA_SELECCIONADA = '-1';
        });



    }



    function newRowchild(obj) {

        var url = "cola/insertarusuario_Cola.php?Id=" + ID_COLA_SELECCIONADA;

        $.ajax({
            url: url
        }).done(function () {
            $(".tabs-2").pqGrid("refresh");
        });

    }

    function newRow() {
        var url = "cola/insertar_Cola.php";

        $.ajax({
            url: url
        }).done(function () {
            $("#configuracion_colas_plegar").pqGrid("refreshDataAndView");
        });

    }

    // ************************************GRID
    // ****************************************

}

function actualizarlistacolas() {
    $.ajax({
        url: "select/getColas.php",
        success: function (response) {
            listacolas = response.split(",");
            $("#configuracion_colas_plegar_2").pqGrid("refreshDataAndView");
        }
    });
}

function CargaGridColas_2() {

    actualizarlistacolas();

    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '50%',
        height: '50%',
        title: "Servicios",
        hoverMode: 'cell',
        selectionModel: {
            type: 'cell'
        },
        editModel: {
            saveKey: '13'
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        },
        editor: {
            type: "textbox"
        }
    };

    // Columnas
    obj.colModel = [{
            title: "Numero",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Numero"
        },
        {
            title: "Tipo",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Tipo"
        },
        {
            title: "Descripcion",
            width: 150,
            dataType: "text",
            dataIndx: "Descripcion",

            render: function (ui) {
                var rowData = ui.rowData;
                if (rowData["visible"] != "1") {
                    return "<span style=\"color:red;\">&nbsp;" + rowData["Descripcion"] + '<span>';
                } else {
                    return rowData["Descripcion"];
                }
            }

        },
        {
            title: "Cola",
            width: 150,
            dataType: "text",
            dataIndx: "Cola",
            editor: {
                type: 'select',
                // options: categorias
                options: function (ui) {
                    return listacolas;


                }
            }

        },
        {
            title: "Predeterminado",
            width: 100,
            dataType: "integer",
            dataIndx: "Predeterminado",
            editor: {
                type: 'select',
                // options: categorias
                options: function (ui) {
                    return ["0", "1"];
                }
            },
            validations: [{
                    type: 'minLen',
                    value: 1,
                    msg: "Required"
                }]
        },
        {
            title: "Visible",
            width: 100,
            dataType: "integer",
            dataIndx: "visible",

            editor: {
                type: 'select',
                // options: categorias
                options: function (ui) {
                    return ["0", "1"];
                }
            },
            validations: [{
                    type: 'minLen',
                    value: 1,
                    msg: "Required"
                }]
        }
    ];


    // Datos
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Numero"],
        sortDir: ["down"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],
        url: "cola/consultaSubCategorias.php",
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };


    // Barra de Tareas
    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

        // Botonera
        $("<span>Nueva</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-plus"
            }
        }).click(function (evt) {
            initRowSrv();
        });

    };


    var $grid = $("#configuracion_colas_plegar_2").pqGrid(obj);
    // Mostrar la parte de arriba
    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", true);
    $grid.pqGrid("option", "collapsible", false);
    // Mostrar la parte de abajo
    // $grid.pqGrid("option", "bottomVisible", true);
    // Lineas de separacion verticales
    $grid.pqGrid("option", "columnBorders", true);
    // Lineas de separacion horizontales
    $grid.pqGrid("option", "rowBorders", true);
    // Sombreado en las filas
    $grid.pqGrid("option", "oddRowsHighlight", true);
    // Numero de celda
    $grid.pqGrid("option", "numberCell", true);
    // Comprimir altura
    // $grid.pqGrid("option", "flexHeight", true);
    // Comprimir Anchura
    // $grid.pqGrid("option", "flexWidth", true);
    // $grid.pqGrid("option", "freezeCols",1);
    // Barras de scroll
    $grid.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });
    // Permite cambiar el tamaño
    // $grid.pqGrid("option", "resizable", true);

    // Grabar la modificacion
    $grid.on("pqgridcellsave", function (evt, ui) {
        var ID = ui.rowData["Numero"];
        var TIPO = ui.rowData["Tipo"];
        var DESCRIPCION = ui.rowData["Descripcion"];
        var Predeterminado = ui.rowData["Predeterminado"];
        var COLA = ui.rowData["Cola"];
        var VISIBLE = ui.rowData["visible"];



        var url = "cola/modificartipo_Cola.php?id=" + ID + "&tipo=" + TIPO + "&descripcion=" + encodeURIComponent(DESCRIPCION) + "&predeterminado=" + encodeURIComponent(Predeterminado) + "&cola=" + COLA + "&visible=" + VISIBLE;

        $.ajax({
            url: url
        }).done(function () {
            $("#configuracion_colas_plegar_2").pqGrid("refreshDataAndView");
        });

    });

    function initRowSrv() {

        var url = "cola/insertartipo_Cola.php";
        $.ajax({
            url: url
        }).done(function () {
            $("#configuracion_colas_plegar_2").pqGrid("refreshDataAndView");
        });


    }

    // ****************************************
}

function CargaGridActividades() {
    var aporteOrganizacion = [];
    var aporteEmpresa = [];
    var aporteJefe = [];
    var editable = false;
    var hidden = true
    if (evaluador == 1) {
        editable = true;
        hidden = false;
    }

    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '100%',
        height: '50%',
        title: "Aporte propio de mis activiades",
        hoverMode: 'cell',
        selectionModel: {
            type: 'cell'
        },
        editModel: {
            clicksToEdit: 1,
            saveKey: '13',
            onSave: null
        },
        editor: {
            type: "textbox"
        },
        filterModel: {
            on: true,
            mode: "AND",
            header: true
        },
        pageModel: {
            type: 'remote',
            curPage: 1,
            rPP: 1000,
            rPPOptions: [5, 10, 20, 30, 40, 50, 1000],
            strRpp: "Registros por página: {0}"
        },
        scrollModel: {
            pace: 'fast',
            autoFit: true,
            theme: true
        }
    };

    // Datos
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        sortIndx: ["IdUsuario"],
        sortDir: ["up"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        url: "select/consultaActividadesUsuario.php?usuario=" + encodeURIComponent(usuariovalidado),
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    // Columnas
    obj.colModel = [{
            title: "Id",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Id",
        },
        {
            title: "Usuario",
            width: 150,
            minWidth: 150,
            maxWidth: 150,
            dataType: "text",
            editable: false,
            hidden: hidden,
            dataIndx: "IdUsuario",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("IdUsuario", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Aporte Propio",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "text",
            dataIndx: "AporteOrganizacion",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    return aporteOrganizacion;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("AporteOrganizacion", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Aporte Empresa",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "text",
            dataIndx: "AporteEmpresa",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    return aporteEmpresa;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("AporteEmpresa", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Aporte Jefe",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "text",
            dataIndx: "AporteJefe",
            editable: editable,
            editor: {
                type: 'select',
                options: function (ui) {
                    return aporteJefe;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("AporteJefe", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Oficina",
            width: 150,
            minWidth: 150,
            maxWidth: 150,
            dataType: "text",
            dataIndx: "OficinaTipo",
            editable: false,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("OficinaTipo", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Actividad",
            dataType: "text",
            dataIndx: "Actividad",
            editable: false,
        }
    ];

    // Barra de Tareas
    // obj.render = function(evt, obj) {};

    obj.rowSelect = function (evt, obj) {};

    obj.cellSelect = function (evt, obj) {
        var ofi = obj.rowData["OficinaTipo"];
        if (obj.dataIndx == 'AporteOrganizacion') {
            aporteOrganizacion = obtenerAporteOrganizacion(ofi);
        }
        if (obj.dataIndx == 'AporteEmpresa') {
            aporteEmpresa = obtenerAporteEmpresa(ofi);
        }
        if (obj.dataIndx == 'AporteJefe' && evaluador == 1) {
            aporteJefe = obtenerAporteJefe(ofi);
        }

    };
    obj.cellDblClick = function (evt, obj) {
        var ofi = obj.rowData["OficinaTipo"];
        aporteOrganizacion = obtenerAporteOrganizacion(ofi);
        aporteEmpresa = obtenerAporteEmpresa(ofi);
        aporteJefe = obtenerAporteJefe(ofi);
    };

    var $grid = $("#configuracion_actividades_plegar").pqGrid(obj);
    // Mostrar la parte de arriba
    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", true);
    $grid.pqGrid("option", "collapsible", false);
    // Mostrar la parte de abajo
    // $grid.pqGrid("option", "bottomVisible", true);
    // Lineas de separacion verticales
    $grid.pqGrid("option", "columnBorders", true);
    // Lineas de separacion horizontales
    $grid.pqGrid("option", "rowBorders", true);
    // Sombreado en las filas
    $grid.pqGrid("option", "oddRowsHighlight", true);
    // Numero de celda
    $grid.pqGrid("option", "numberCell", true);
    // Comprimir altura
    // $grid.pqGrid("option", "flexHeight", true);
    // Comprimir Anchura
    // $grid.pqGrid("option", "flexWidth", true);
    // $grid.pqGrid("option", "freezeCols",1);
    // Barras de scroll
    $grid.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });
    // Permite cambiar el tamaño
    // $grid.pqGrid("option", "resizable", true);

    // Grabar la modificacion
    $grid.on("pqgridcellsave", function (evt, ui) {

        var Id = ui.rowData["Id"];
        var AporteOrganizacion = ui.rowData["AporteOrganizacion"];
        var AporteEmpresa = ui.rowData["AporteEmpresa"];
        var AporteJefe = ui.rowData["AporteJefe"];

        // alert(Id + ' --> ' + AporteOrganizacion);

        var url = "select/modificarActividadUsuario.php?Id=" + Id + "&AporteOrganizacion=" + AporteOrganizacion + "&AporteEmpresa=" + AporteEmpresa + "&AporteJefe=" + AporteJefe + "&Usuario=" + encodeURIComponent(usuariovalidado);

        $.ajax({
            url: url
        }).done(function () {
            $grid.pqGrid("refreshDataAndView");
        });

    });

    function obtenerAporteOrganizacion(ofi) {
        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);
        $.ajax({
            url: "select/getTiposTipos.php?tipo=102&orden=C&oficina=" + ofi,
            async: false,
            cache: false,
            success: function (response) {
                AporteOrganizacion = response.split(",");
            }
        });
        spinner.stop( );
        return AporteOrganizacion;
    }

    function obtenerAporteEmpresa(ofi) {
        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);
        $.ajax({
            url: "select/getTiposTipos.php?tipo=103&orden=C&oficina=" + ofi,
            async: false,
            cache: false,
            success: function (response) {
                AporteEmpresa = response.split(",");
            }
        });
        spinner.stop( );
        return AporteEmpresa;
    }

    function obtenerAporteJefe(ofi) {
        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);
        $.ajax({
            url: "select/getTiposTipos.php?tipo=104&orden=C&oficina=" + ofi,
            async: false,
            cache: false,
            success: function (response) {
                AporteJefe = response.split(",");
            }
        });
        spinner.stop( );
        return AporteJefe;
    }

    function filter(dataIndx, value) {
        $grid.pqGrid("filter", {
            data: [{
                    dataIndx: dataIndx,
                    value: value
                }]
        });
    }

    // **************************************** GRID ACTIVADES USUARIO
}

// ++ Alberto 09/04/19
// ++ **************************************** GRID MAESTRO DE PREGUNTAS FEEDBACK
function CargaGridPreguntasFeedback() {

    var ID_FEEDBACK_SELECCIONADA = '-1';

    var oficinas = [];
    obtenerOficinasusuario();
    var tipos = [];
    var tiposFeedback = ['Comentario', 'Valoración'];

    var editable = false;
    if (evaluador == 1) {
        editable = true;
    }

    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '100%',
        height: '50%',
        title: "Maestro de preguntas feedback",
        hoverMode: 'cell',
        selectionModel: {
            type: 'cell'
        },
        editModel: {
            clicksToEdit: 1,
            saveKey: '13',
            onSave: null
        },
        editor: {
            type: "textbox"
        },
        filterModel: {
            on: true,
            mode: "AND",
            header: true
        },
        pageModel: {
            type: 'remote',
            curPage: 1,
            rPP: 1000,
            rPPOptions: [5, 10, 20, 30, 40, 50, 1000],
            strRpp: "Registros por página: {0}"
        },
        scrollModel: {
            pace: 'fast',
            autoFit: true,
            theme: true
        }
    };

    // Datos
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        sortIndx: ["Oficina", "Tipo"],
        sortDir: ["up", "up"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        url: "feedback/consultaMaestroFeedback.php?usuario=" + encodeURIComponent(usuariovalidado),
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    // Columnas
    obj.colModel = [{
            title: "Id",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Id",
        },
        {
            title: "Oficina",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "text",
            editable: editable,
            hidden: false,
            dataIndx: "Oficina",
            editor: {
                type: 'select',
                options: function (ui) {
                    return oficinas;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Oficina", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Tipo",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "text",
            dataIndx: "Tipo",
            editable: editable,
            editor: {
                type: 'select',
                options: function (ui) {
                    return tipos;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Tipo", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Tipo pregunta",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "text",
            dataIndx: "TipoFeedback",
            editable: editable,
            editor: {
                type: 'select',
                options: function (ui) {
                    return tiposFeedback;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("TipoFeedback", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Pregunta",
            width: 500,
            minWidth: 100,
            maxWidth: 5000,
            dataType: "text",
            dataIndx: "Pregunta",
            editable: editable,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Pregunta", $(this).val());
                        }
                    }]
            }
        }
    ];

    obj.render = function (evt, obj) {
        if (editable) {
            var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

            $("<span>Añadir Pregunta</span>").appendTo($toolbar).button({
                icons: {
                    primary: "ui-icon-plusthick"
                }
            }).click(function (evt) {
                insertarPregunta();
            });

            $("<span>Borrar Pregunta</span>").appendTo($toolbar).button({
                icons: {
                    primary: "ui-icon-minusthick"
                }
            }).click(function (evt) {
                borrarPregunta();
            });
        }
    };
    // Barra de Tareas
    // obj.render = function(evt, obj) {};

    obj.rowSelect = function (evt, obj) {
        ID_FEEDBACK_SELECCIONADA = obj.rowData["Id"];
    };

    obj.cellSelect = function (evt, obj) {

        ID_FEEDBACK_SELECCIONADA = obj.rowData["Id"];

        var ofi = obj.rowData["Oficina"];
        if (obj.dataIndx == 'Tipo') {
            tipos = obtenerTipos(ofi);
        }
    };
    obj.cellDblClick = function (evt, obj) {

        ID_FEEDBACK_SELECCIONADA = obj.rowData["Id"];

        var ofi = obj.rowData["Oficina"];
        if (obj.dataIndx == 'Tipo') {
            tipos = obtenerTipos(ofi);
        }
    };

    var $grid = $("#configuracion_feedback_plegar").pqGrid(obj);
    // Mostrar la parte de arriba
    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", true);
    $grid.pqGrid("option", "collapsible", false);
    $grid.pqGrid("option", "columnBorders", true);
    $grid.pqGrid("option", "rowBorders", true);
    // Sombreado en las filas
    $grid.pqGrid("option", "oddRowsHighlight", true);
    $grid.pqGrid("option", "numberCell", true);
    $grid.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });

    // Grabar la modificacion
    $grid.on("pqgridcellsave", function (evt, ui) {

        var Id = ui.rowData["Id"];
        var Tipo = ui.rowData["Tipo"];
        var Pregunta = ui.rowData["Pregunta"];
        var TipoFeedback = ui.rowData["TipoFeedback"];
        var Oficina = ui.rowData["Oficina"];

        // alert(Id + ' --> ' + AporteOrganizacion);

        var url = "feedback/modificarMaestroFeedback.php?Id=" + Id + "&Tipo=" + encodeURIComponent(Tipo) + "&Pregunta=" + encodeURIComponent(Pregunta) + "&TipoFeedback=" + encodeURIComponent(TipoFeedback) + "&Usuario=" + encodeURIComponent(usuariovalidado) + "&Oficina=" + encodeURIComponent(Oficina);

        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);
        $.ajax({
            url: url
        }).done(function () {
            $grid.pqGrid("refreshDataAndView");
            spinner.stop( );
        });

    });
    function insertarPregunta() {

        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);

        var url = "feedback/insertarMaestroFeedback.php?usuario=" + encodeURIComponent(usuariovalidado);
        $.ajax({
            url: url
        }).done(function (data) {
            $grid.pqGrid("refreshDataAndView");
            spinner.stop( );
        });

    }

    function borrarPregunta() {

        if (ID_FEEDBACK_SELECCIONADA == '-1') {
            mensaje('Error', 'Selecciona una línea para borrar.');
        } else {

            var target = document.getElementById('micuerpo');
            var spinner = new Spinner(opts).spin(target);

            var url = "feedback/borrarMaestroFeedback.php?Id=" + ID_FEEDBACK_SELECCIONADA + "&Usuario=" + encodeURIComponent(usuariovalidado);
            $.ajax({
                url: url
            }).done(function () {
                $grid.pqGrid("refreshDataAndView");
                spinner.stop( );
            });
        }
        crearslidesemanal(2);
    }

    function obtenerTipos(ofi) {
        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);
        $.ajax({
            url: "select/getTiposTipos.php?tipo=0&orden=C&oficina=" + ofi,
            async: false,
            cache: false,
            success: function (response) {
                tipos = response.split(",");
            }
        });
        spinner.stop( );
        return tipos;
    }
    function obtenerOficinasusuario() {

        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);
        $.ajax({
            url: "select/getOficinasUsuario.php?usuario=" + encodeURIComponent(usuariovalidado),
            async: false,
            cache: false,
            success: function (response) {
                oficinas = response.split(",");
            }
        });
        spinner.stop( );
        // return oficinas;
    }

    function filter(dataIndx, value) {
        $grid.pqGrid("filter", {
            data: [{
                    dataIndx: dataIndx,
                    value: value
                }]
        });
    }

// -- **************************************** GRID MAESTRO DE PREGUNTAS FEEDBACK
}


function CargaGridHorasEnUnDia() {

    ID_HORA_SELECCIONADA = '-1';

    // Ocultar campos de hora y fecha en usuarios que no deban ver horas en los fichajes
    var ocultarCampo = false;
    if (OcultarHoraGrids == 1) {
        ocultarCampo = true;
    }

    var tipos = [];
    $.ajax({
        url: "select/getTiposHoras.php",
        success: function (response) {
            tipos = response.split(",");
        }
    });

    var dateEditor = function (ui) {
        var $cell = ui.$cell,
                rowData = ui.rowData,
                dataIndx = ui.dataIndx,
                cls = ui.cls,
                dc = $.trim(rowData[dataIndx]);
        $cell.css('padding', '0');
        var $inp = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' />").appendTo($cell).val(dc).datetimepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy",
            onClose: function () {
                $inp.focus();
            }
        });
    }


    var dateEditor2 = function (ui) {
        var $cell = ui.$cell;
        var dataIndx = ui.dataIndx;
        var rowData = ui.rowData;
        var cls = ui.cls;
        var dc = $.trim(rowData[dataIndx]);
        var input = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' autocomplete='off' />")
                .appendTo($cell)
                .val(dc).datetimepicker({
            datepicker: true,
            timepicker: true,
            format: 'd/m/Y H:i:s',
            minTime: '07:00:00',
            weeks: false,
            step: 15,
            dayOfWeekStart: 1,
        });
    }

    var obj = {
        width: '100%',
        height: '95%',
        title: "Lista de fichadas",
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
        editModel: {
            clicksToEdit: 2,
            saveKey: '13',
            onSave: null
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        },
        groupModel: {
            dataIndx: ["Dia Inicio"],
            collapsed: [false],
            title: ["<b style='font-weight:bold;'>{0} ({1} Fichas)</b>", "{0} - {1}"],
            dir: ["up"]
        },
        scrollModel: {
            pace: 'fast',
            autoFit: true,
            theme: true
        },
        dataModel: {
            sorting: "remote",
            paging: "remote",
            curPage: 1,
            rPP: 20,
            sortIndx: ["Hora"],
            sortDir: ["up"],
            location: "remote",
            dataType: "JSON",
            method: "GET",
            rPPOptions: [5, 10, 20, 30, 40, 50],
            url: "select/consultaHorasenundia.php?usuario=" + encodeURIComponent(usuariovalidado) + "&fecha=" + FECHA_HORA_SELECCIONADA,
            getData: function (dataJSON, textStatus, jqXHR) {
                var data = dataJSON.data;
                ID_HORA_SELECCIONADA = '-1';
                return {
                    curPage: dataJSON.curPage,
                    totalRecords: dataJSON.totalRecords,
                    data: dataJSON.data
                };
            }
        }
    };

    obj.colModel = [{
            title: "Hora",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "integer",
            editable: false,
            hidden: ocultarCampo,
            dataIndx: "Hora"
        },
        {
            title: "Numero",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Numero"
        },
        {
            title: "Tarea",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            hidden: true,
            dataType: "integer",
            dataIndx: "Tarea",
            validations: [{
                    type: 'regexp',
                    value: '[0-9]{6}',
                    msg: 'La tarea no es correcta'
                }]
        },
        {
            title: "Tarea",
            width: 130,
            minWidth: 130,
            maxWidth: 130,
            dataType: "integer",
            editable: true,
            hidden: false,
            dataIndx: "Tarea",
            render: function (ui) {
                var tarea = ui.rowData["Tarea"];
                if (ui.rowData["Dia Fin"] == '') {
                    return tarea + "&nbsp;&nbsp;<input type='button' value='Abrir' title='Abrir tarea " + tarea + "' onclick='cambia_menu(3);' style='width:40px;' /><img src='imagenes/play.png' onclick='ID_FICHADA_SELECCIONADA = " + ui.rowData["Numero"] + ";ID_SELECCIONADA=" + tarea + ";IniciarTarea();' title='Iniciar tarea " + tarea + "' style='width:18px;height:18px;cursor: pointer;' /><img src='imagenes/stop.png' onclick='PararTarea();' title='Parar tarea " + tarea + "' style='width:18px;height:18px;cursor: pointer;' />";
                } else {
                    return tarea + "&nbsp;&nbsp;<input type='button' value='Abrir' title='Abrir tarea " + tarea + "' onclick='cambia_menu(3);' style='width:40px;' /><img src='imagenes/play.png' onclick='ID_FICHADA_SELECCIONADA = " + ui.rowData["Numero"] + ";ID_SELECCIONADA=" + tarea + ";IniciarTarea();' title='Iniciar tarea " + tarea + "' style='width:18px;height:18px;cursor: pointer;' />";
                }
            }
        },
        {
            title: "Departamento",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "text",
            dataIndx: "Oficina",
            editable: false
        },
        {
            title: "Titulo",
            width: 250,
            minWidth: 200,
            maxWidth: 400,
            dataType: "text",
            editable: false,
            dataIndx: "Titulo"
        },
        {
            title: "Dia Inicio",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "text",
            dataIndx: "Dia Inicio",
            editor: {
                type: dateEditor2
            },
            validations: [{
                    type: 'regexp',
                    value: '[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}',
                    msg: 'No en formato dd/mm/yyyy hh:mm '
                }]
        },
        {
            title: "Hora Inicio",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "text",
            editable: false,
            hidden: ocultarCampo,
            dataIndx: "Hora Inicio"
        },
        {
            title: "Inicio",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "text",
            editable: false,
            hidden: true,
            dataIndx: "Inicio"
        },
        {
            title: "Dia Fin",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "text",
            hidden: ocultarCampo,
            dataIndx: "Dia Fin",
            editor: {
                type: dateEditor2
            },
            validations: [{
                    type: 'regexp',
                    value: '^$|[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}',
                    msg: 'No en formato dd/mm/yyyy hh:mm '
                }]
        },
        {
            title: "Hora Fin",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "text",
            editable: false,
            hidden: ocultarCampo,
            dataIndx: "Hora Fin"
        },
        {
            title: "Minutos",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "integer",
            editable: false,
            dataIndx: "Minutos",
            summary: {
                type: ["sum"],
                title: ["Total: {0}"]
            }
        },
        {
            title: "Usuario",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "text",
            editable: false,
            dataIndx: "Usuario"
        },
        {
            title: "Tipo",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "text",
            editable: true,
            dataIndx: "Tipo",
            editor: {
                type: 'select',
                options: function (ui) {
                    // return tipos;
                    var tarea = ui.rowData["Tarea"];
                    return obtenerTiposHoras(tarea);
                }
            },
            validations: [{
                    type: 'minLen',
                    value: 1,
                    msg: "Required"
                }]
        },
        {
            title: "Estado",
            width: 110,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Estado"
        },
        {
            title: "Teletrabajo",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "text",
            editable: true,
            dataIndx: "Teletrabajo",
            editor: {
                type: 'select',
                options: function (ui) {
                    // return tipos;
                    var tarea = ui.rowData["Teletrabajo"];
                    return obtenerTiposTeletrabajo(tarea);
                }
            },
            validations: [{
                    type: 'minLen',
                    value: 1,
                }]
        },
        {
            title: "Comentario",
            minWidth: 300,
            dataType: "text",
            editable: true,
            dataIndx: "Comentario"
        }
        
    ];



    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

        $("<label>&nbsp;&nbsp;Minutos:&nbsp;</label><input type='number' id='minutostareaundia'       name='minutostareaundia'      style='width:50px' value='0' min='1' step='1' />").appendTo($toolbar);
        $("<label>&nbsp;&nbsp;Tarea:&nbsp;  </label><input type='text'   id='autocompletetareaundia'  name='autocompletetareaundia' style='width:600px' maxlength='400'  value='' placeholder='&nbsp;&nbsp;&nbsp;Indique número o descripción de la tarea para usar el buscador' class='ui-autocomplete-input' autocomplete='off' />").appendTo($toolbar);

        $("#autocompletetareaundia").change(function () {
            anadirFichada();
        });

        $("<span>Añadir Fichada</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-plusthick"
            }
        }).click(function (evt) {
            anadirFichada();
        });

        $("<span>Borrar Fichada</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-minusthick"
            }
        }).click(function (evt) {
            deleteRow();
        });

        $('#autocompletetareaundia').autocomplete({
            type: 'post',
            source: function (request, response) {
                $.get("select/getTareaAutocomplete.php?nombreusuario=" + encodeURIComponent(nombreusuariovalidado), {
                    buscar: request.term
                }, function (data) {
                    tags = data.split("|");
                    response(tags);
                });
            }
        });
    };

    obj.rowSelect = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Tarea"];
        ID_HORA_SELECCIONADA = obj.rowData["Numero"];
    };
    obj.cellSelect = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Tarea"];
        ID_HORA_SELECCIONADA = obj.rowData["Numero"];
    };
    obj.cellDblClick = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Tarea"];
        ID_HORA_SELECCIONADA = obj.rowData["Numero"];
        // cambia_menu(3);
    };



    $grid_horas_en_un_dia = $("#horasunundia_grid").pqGrid(obj);

    $grid_horas_en_un_dia.pqGrid("option", "topVisible", true);
    $grid_horas_en_un_dia.pqGrid("option", "showTitle", true);
    $grid_horas_en_un_dia.pqGrid("option", "collapsible", false);
    $grid_horas_en_un_dia.pqGrid("option", "columnBorders", true);
    $grid_horas_en_un_dia.pqGrid("option", "rowBorders", true);
    $grid_horas_en_un_dia.pqGrid("option", "oddRowsHighlight", true);
    $grid_horas_en_un_dia.pqGrid("option", "numberCell", true);
    // $grid_horas_en_un_dia.pqGrid("option", "freezeCols", 3);

    $grid_horas_en_un_dia.on("pqgridcellsave", function (evt, ui) {

        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);

        var ID = ui.rowData["Numero"];
        var TITULO = ui.rowData["Comentario"];
        var DIA_INICIO = ui.rowData["Dia Inicio"];
        var DIA_FIN = ui.rowData["Dia Fin"];
        var HORA_INICIO = ui.rowData["Hora Inicio"];
        var HORA_FIN = ui.rowData["Hora Fin"];
        var TAREA_HORA = ui.rowData["Tarea"];
        var TIPO = ui.rowData["Tipo"];
        var TELETRABAJO = ui.rowData["Teletrabajo"];

        TITULO = limpiarTextoOffice(TITULO);

        var url = "select/modificarHorasTarea.php?Id=" + ID + "&diainicio=" + DIA_INICIO + "&diafin=" + DIA_FIN + "&horainicio=" + HORA_INICIO + "&horafin=" + HORA_FIN + "&Titulo=" + encodeURIComponent(TITULO) + "&Tipo=" + encodeURIComponent(TIPO) + "&usuario=" + encodeURIComponent(usuariovalidado) + "&teletrabajo=" + TELETRABAJO + "&tarea=" + TAREA_HORA ;

        $.ajax({
            url: url
        }).done(function (data) {
            if (data == 0) {
                mensaje('Aviso de cambio de estado', 'La tarea número ' + TAREA_HORA + ' ha cambiado al estado <i><b>En Curso</b></i> al modificar la fichada.');
            }
            $grid_horas_en_un_dia.pqGrid("refreshDataAndView");
            var mifecha = document.getElementById('fechacalculohorasenundia');
            CalculaTotalHoras(mifecha.value);
            spinner.stop( );
        });
        ID_HORA_SELECCIONADA = '-1';
    });

    /***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
     * @desc añade una fichada con una cantidad de minutos al final del dia
     * @author alberto
     * @date 12/09/2017
     */
    function anadirFichada() {
        var enviar = true;
        var dia = $('#fechacalculohorasenundia').val();
        dia = dia.split('-');
        dia = dia[2] + '-' + dia[1] + '-' + dia[0];
        var minutos = $('#minutostareaundia').val();
        var tarea = $('#autocompletetareaundia').val();
        tarea = tarea.split(' - ');
        tarea = tarea[0];

        if (enviar && !esEntero(minutos)) {
            mensaje('Minutos', 'El campo minutos no es correcto.');
            enviar = false;
        }

        if (enviar && (minutos < 1 || minutos > 360)) {
            mensaje('Minutos', 'El campo minutos debe estar entre 1 y 360 (6 horas)');
            enviar = false;
        }

        if (enviar && !esEntero(tarea)) {
            mensaje('Tarea', 'El campo tarea no es correcto');
            enviar = false;
        }

        if (enviar) {

            var target = document.getElementById('micuerpo');
            var spinner = new Spinner(opts).spin(target);

            var url = "select/anadirFichadaUnDia.php?minutos=" + minutos + "&tarea=" + tarea + "&dia=" + dia + "&usuario=" + encodeURIComponent(usuariovalidado);
            $.ajax({
                url: url
            }).done(function (data) {

                spinner.stop( );
                if (data != '') {
                    mensaje('Error', data, 'alert.png');
                }

                $grid_horas_en_un_dia.pqGrid("refreshDataAndView");
                var mifecha = document.getElementById('fechacalculohorasenundia');
                CalculaTotalHoras(mifecha.value);
                $('#minutostareaundia').val('');
                $('#autocompletetareaundia').val('');
                crearslidesemanal(2);
            });
        }
    }

    function deleteRow() {
        if (ID_HORA_SELECCIONADA == '-1') {
            mensaje('Error', 'Selecciona una línea para borrar.');
        } else {

            var target = document.getElementById('micuerpo');
            var spinner = new Spinner(opts).spin(target);

            var url = "select/borrarHorasTarea.php?Id=" + ID_HORA_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=" + ID_SELECCIONADA;
            $.ajax({
                url: url
            }).done(function () {
                spinner.stop( );
                $grid_horas_en_un_dia.pqGrid("refreshDataAndView");
                var mifecha = document.getElementById('fechacalculohorasenundia');
                CalculaTotalHoras(mifecha.value);
            });
        }
        crearslidesemanal(2);
    }

    $("#btn_calcular_horasenundia").click(function () {
        var mifecha = document.getElementById('fechacalculohorasenundia');

        FECHA_HORA_SELECCIONADA = mifecha.value;
        calcularLasHorasUnDia();
        return false;
    });


    function obtenerTiposHoras(tarea) {
        var tipos = [];
        $.ajax({
            url: "select/getTiposTipos.php?tipo=1&id=" + tarea,
            cache: false,
            async: false,
            success: function (response) {
                tipos = response.split(",");
            }
        });

        return tipos;
    }

    function obtenerTiposTeletrabajo(tarea) {
        var tipos = ['SI', 'NO']
        return tipos;
    }


    // SI EL GRID SOLO SE LANZA DESDE EL SLIDER DE HORAS, ESTO NO HACE FALTA Y OPTIMIZAMOS EL ARRANQUE INICIAL
    // var url = "select/consultaHorasenundia.php?usuario=" + encodeURIComponent(usuariovalidado) + "&fecha=" + FECHA_HORA_SELECCIONADA;
    // $grid_horas_en_un_dia.pqGrid("option", "dataModel.url", url);
    // $grid_horas_en_un_dia.pqGrid("refreshDataAndView");
}

function calcularLasHorasUnDia() {
    ID_HORA_SELECCIONADA = '-1';
    var from_casilla = document.getElementById('fechacalculohorasenundia');
    var miseccionhorasenundia = from_casilla.value;
    FECHA_HORA_SELECCIONADA = miseccionhorasenundia;
    var url = "select/consultaHorasenundia.php?usuario=" + encodeURIComponent(usuariovalidado) + "&fecha=" + encodeURIComponent(FECHA_HORA_SELECCIONADA);

    $grid_horas_en_un_dia.pqGrid("option", "dataModel.url", url);
    $grid_horas_en_un_dia.pqGrid("refreshDataAndView");

    var mifecha = document.getElementById('fechacalculohorasenundia');
    CalculaTotalHoras(mifecha.value);
}

function CargaGridComentariosEnUnDia() {
    // existe_grid_horas_en_un_dia = true;
    ID_COMENTARIO_SELECCIONADA = '-1';

    /*
     * var dateEditor = function (ui) { var $cell = ui.$cell, rowData = ui.rowData, dataIndx = ui.dataIndx, cls = ui.cls, dc = $.trim(rowData[dataIndx]); $cell.css('padding', '0'); var $inp = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' />") .appendTo($cell) .val(dc).datetimepicker({ changeMonth: true, changeYear: true, currentText : 'Hoy' , monthNames : [ 'Enero' , 'Febrero' , 'Marzo' , 'Abril' , 'Mayo' , 'Junio' , 'Julio' , 'Agosto' , 'Septiembre' , 'Octubre' , 'Noviembre' , 'Diciembre' ] , monthNamesShort : [ 'Ene' , 'Feb' , 'Mar' , 'Abr' , 'May' , 'Jun' , 'Jul' , 'Ago' , 'Sep' , 'Oct' , 'Nov' , 'Dic' ] , dayNames : [ 'Domingo' , 'Lunes' , 'Martes' , 'Miércoles' , 'Jueves' , 'Viernes' , 'Sábado' ] , dayNamesShort : [ 'Dom' , 'Lun' , 'Mar' , 'Mié' , 'Juv' , 'Vie' , 'Sáb' ] , dayNamesMin : [ 'Do' , 'Lu' , 'Ma' , 'Mi' , 'Ju' , 'Vi' , 'Sá' ] , weekHeader : 'Sm' , firstDay : 1 , dateFormat:"dd/mm/yy", onClose: function () { $inp.focus(); } });
     */

    var dateEditor = function (ui) {
        var $cell = ui.$cell,
                rowData = ui.rowData,
                dataIndx = ui.dataIndx,
                cls = ui.cls,
                dc = $.trim(rowData[dataIndx]);
        $cell.css('padding', '0');
        var $inp = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' />")
                .appendTo($cell)
                .val(dc).datetimepicker({
            changeMonth: true,
            changeYear: true,
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            firstDay: 1,
            dateFormat: "dd/mm/yy",
            onClose: function () {
                $inp.focus();
            }
        });
    }


    var obj = {
        width: '100%',
        height: '75%',
        title: "Lista de Segmentos",
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
        editModel: {
            saveKey: '13'
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        },
        groupModel: {
            dataIndx: ["Hora"],
            collapsed: [false],
            title: ["<b style='font-weight:bold;'>{0} ({1} Segmentos)</b>", "{0} - {1}"],
            dir: ["up"]
        }
    };


    obj.colModel = [{
            title: "Hora",
            width: 50,
            dataType: "integer",
            editable: false,
            dataIndx: "Hora"
        },
        {
            title: "Numero",
            width: 50,
            dataType: "integer",
            editable: false,
            dataIndx: "Numero"
        },
        {
            title: "Tarea",
            width: 50,
            dataType: "integer",
            dataIndx: "Tarea"
        },
        {
            title: "Titulo",
            width: 250,
            dataType: "text",
            editable: false,
            dataIndx: "Titulo"
        },
        {
            title: "Dia Inicio",
            width: 70,
            dataType: "date",
            dataIndx: "Dia Inicio",
            editor: {
                type: dateEditor
            },
            validations: [{
                    type: 'regexp',
                    value: '[0-9]{2}/[0-9]{2}/[0-9]{4}',
                    msg: 'No en dd/mm/yyyy formato'
                }]
        },
        {
            title: "Hora Inicio",
            width: 70,
            dataType: "text",
            editable: false,
            dataIndx: "Hora Inicio"
        },
        {
            title: "Inicio",
            width: 70,
            dataType: "text",
            editable: false,
            hidden: true,
            dataIndx: "Inicio"
        },
        {
            title: "Dia Fin",
            width: 70,
            dataType: "date",
            dataIndx: "Dia Fin",
            editor: {
                type: dateEditor
            },
            validations: [{
                    type: 'regexp',
                    value: '[0-9]{2}/[0-9]{2}/[0-9]{4}',
                    msg: 'No en dd/mm/yyyy formato'
                }]
        },
        {
            title: "Hora Fin",
            width: 70,
            dataType: "text",
            editable: false,
            dataIndx: "Hora Fin"
        },
        {
            title: "Minutos",
            width: 70,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Minutos",
            summary: {
                type: ["sum"],
                title: ["Total: {0}"]
            }
        },
        {
            title: "Usuario",
            width: 70,
            dataType: "text",
            editable: false,
            dataIndx: "Usuario"
        },
        {
            title: "Estado",
            width: 110,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Estado"
        },
        {
            title: "Comentario",
            width: 110,
            dataType: "text",
            editable: false,
            dataIndx: "Comentario"
        }
    ];


    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Hora"],
        sortDir: ["up"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],
        url: "select/consultaComentariosenundia.php?usuario=" + encodeURIComponent(nombreusuariovalidado) + "&fecha=" + FECHA_HORA_SELECCIONADA,
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            ID_COMENTARIO_SELECCIONADA = '-1';
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

    };

    obj.rowSelect = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Tarea"];
        ID_COMENTARIO_SELECCIONADA = obj.rowData["Numero"];
    };
    obj.cellSelect = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Tarea"];
        ID_COMENTARIO_SELECCIONADA = obj.rowData["Numero"];
    };
    obj.cellDblClick = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Tarea"];
        cambia_menu(3);
    };

    var $grid = $("#horasunundia_grid").pqGrid(obj);
    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", true);
    $grid.pqGrid("option", "collapsible", false);
    $grid.pqGrid("option", "columnBorders", true);
    $grid.pqGrid("option", "rowBorders", true);
    $grid.pqGrid("option", "oddRowsHighlight", true);
    $grid.pqGrid("option", "numberCell", true);
    $grid.pqGrid("option", "freezeCols", 3);
    $grid.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });


    $("#btn_calcular_horasenundia").click(function () {
        ID_COMENTARIO_SELECCIONADA = '-1';
        var from_casilla = document.getElementById('fechacalculohorasenundia');
        var miseccionhorasenundia = from_casilla.value;
        FECHA_HORA_SELECCIONADA = miseccionhorasenundia;
        var url = "select/consultaComentariosenundia.php?usuario=" + encodeURIComponent(nombreusuariovalidado) + "&fecha=" + FECHA_HORA_SELECCIONADA;
        $("#horasunundia_grid").pqGrid("option", "dataModel.url", url);
        $("#horasunundia_grid").pqGrid("refreshDataAndView");
        var mifecha = document.getElementById('fechacalculohorasenundia');
        CalculaTotalComentarios(mifecha.value);
        return false;
    });

    var url = "select/consultaComentariosenundia.php?usuario=" + encodeURIComponent(nombreusuariovalidado) + "&fecha=" + FECHA_HORA_SELECCIONADA;
    $("#horasunundia_grid").pqGrid("option", "dataModel.url", url);
    $("#horasunundia_grid").pqGrid("refreshDataAndView");
}

function accede_navision(obj) {
    if (obj.checked == true) {
        obj.value = '1';
        $("#imagen_refresco_acceso_navision").css("display", "block");
        $("#tr_dato_acceso_navision_1").css("display", "block");
        $("#tr_dato_acceso_navision_2").css("display", "block");
        $("#tr_dato_acceso_navision_3").css("display", "block");
        var alta_usuario_nombre = document.getElementById('alta_usuario_nombre');
        var alta_usuario_apellido_1 = document.getElementById('alta_usuario_apellido_1');
        var alta_usuario_apellido_2 = document.getElementById('alta_usuario_apellido_2');
        var selectRegistrosEspejo = document.getElementById('selectRegistrosEspejo');
        var alta_usuario_acceso_navision_user = document.getElementById('alta_usuario_acceso_navision_user');
        var alta_usuario_acceso_navision_pass = document.getElementById('alta_usuario_acceso_navision_pass');

        if (alta_usuario_acceso_navision_user.value == '' && alta_usuario_acceso_navision_pass.value == '') {
            refrescar_usuario_password_navision();
        }

    } else {
        obj.value = '0';
        $("#imagen_refresco_acceso_navision").css("display", "none");
        $("#tr_dato_acceso_navision_1").css("display", "none");
        $("#tr_dato_acceso_navision_2").css("display", "none");
        $("#tr_dato_acceso_navision_3").css("display", "none");
    }

}

function accede_Mapex_Terminal(obj) {
    if (obj.checked == true) {
        obj.value = '1';
        $("#tr_dato_acceso_Mapex_Terminal_1").css("display", "block");
        $("#tr_dato_acceso_Mapex_Terminal_2").css("display", "block");



    } else {
        obj.value = '0';
        $("#tr_dato_acceso_Mapex_Terminal_1").css("display", "none");
        $("#tr_dato_acceso_Mapex_Terminal_2").css("display", "none");

    }

}

function accede_Mapex_Aplicacion(obj) {
    if (obj.checked == true) {
        obj.value = '1';
        $("#tr_dato_acceso_Mapex_Aplicacion_1").css("display", "block");
        $("#tr_dato_acceso_Mapex_Aplicacion_2").css("display", "block");



    } else {
        obj.value = '0';
        $("#tr_dato_acceso_Mapex_Aplicacion_1").css("display", "none");
        $("#tr_dato_acceso_Mapex_Aplicacion_2").css("display", "none");

    }

}

function cambio_mapex_terminal_espejo(obj) {
    alert(obj.value);
}

function refrescar_usuario_password_navision() {
    var alta_usuario_nombre = document.getElementById('alta_usuario_nombre');
    var alta_usuario_apellido_1 = document.getElementById('alta_usuario_apellido_1');
    var alta_usuario_apellido_2 = document.getElementById('alta_usuario_apellido_2');
    var selectRegistrosEspejo = document.getElementById('selectRegistrosEspejo');
    var alta_usuario_acceso_navision_user = document.getElementById('alta_usuario_acceso_navision_user');
    var alta_usuario_acceso_navision_pass = document.getElementById('alta_usuario_acceso_navision_pass');

    $.ajax({
        url: "select/Acceso_Navision.php?alta_usuario_nombre=" + encodeURIComponent(alta_usuario_nombre.value) + "&alta_usuario_apellido_1=" + encodeURIComponent(alta_usuario_apellido_1.value) + "&alta_usuario_apellido_2=" + encodeURIComponent(alta_usuario_apellido_2.value) + "&selectRegistrosEspejo=" + encodeURIComponent(selectRegistrosEspejo.value) + "&alta_usuario_acceso_navision_user=" + encodeURIComponent(alta_usuario_acceso_navision_user.value) + "&alta_usuario_acceso_navision_pass=" + encodeURIComponent(alta_usuario_acceso_navision_pass.value),
        success: function (response) {
            tipos = response.split("|");
            alta_usuario_acceso_navision_user.value = tipos[0];
            alta_usuario_acceso_navision_pass.value = tipos[1];
        }
    });

}

function inicializar_fecha(obj) {
    // Si tiene ID (Puede ser que no venga con ID porque abre simplemente el portal) obtenemos los datos
    var ajax = nuevoAjax();
    var lista
    ajax.open("POST", "select/obtener_fechas_posibles.php", false);
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {
            // Respuesta
            lista = (ajax.responseText);
            var elem = lista.split("|");
            var inicio = elem[0];
            var fin = elem[1];
            var array_dias = elem[2];

            var elem_inicio = inicio.split("-");
            diainicial = elem_inicio[1];
            mesinicial = elem_inicio[0] - 1;
            anyoinicial = elem_inicio[2];

            var elem_final = fin.split("-");
            diafinal = elem_final[1];
            mesfinal = elem_final[0] - 1;
            anyofinal = elem_final[2];


            // Cargamos los datos
            disabledDays = array_dias.split(";");
            inicializo_fecha(obj)
        }
    }
    ajax.send(null);
}

function inicializo_fecha(obj) {

    $('#' + obj).datepicker({
        minDate: new Date(anyoinicial, mesinicial, diainicial),
        maxDate: new Date(anyofinal, mesfinal, diafinal),
        dateFormat: 'dd-mm-yy',
        constrainInput: true,
        beforeShowDay: noWeekendsOrHolidays,
        onSelect: function (date) {
            // cambia_fecha(proveedor,pedido,date);
        }
    });

    $(function ($) {
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '<Ant',
            nextText: 'Sig>',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
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

function noWeekendsOrHolidays(date) {
    var noWeekend = $.datepicker.noWeekends(date);
    return noWeekend[0] ? nationalDays(date) : noWeekend;
}

function nationalDays(date) {
    var m = date.getMonth(),
            d = date.getDate(),
            y = date.getFullYear();
    for (i = 0; i < disabledDays.length; i++) {
        if ($.inArray((m + 1) + '-' + d + '-' + y, disabledDays) != -1 || new Date() > date) {
            return [false];
        }
    }
    return [true];
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author marivi
 */
function refrescar_usuario_incorporacion() {
    var alta_usuario_nombre = document.getElementById('alta_usuario_nombre');
    var alta_usuario_apellido_1 = document.getElementById('alta_usuario_apellido_1');
    var alta_usuario_apellido_2 = document.getElementById('alta_usuario_apellido_2');

    var Usuario_Incorporacion = document.getElementById('Usuario_Incorporacion');
    var Mail_Incorporacion = document.getElementById('Mail_Incorporacion');

    // if(Usuario_Incorporacion.value=='' && Mail_Incorporacion.value=='')
    // {
    refresca_usuario_incorporacion();

    // }

    var alta_usuario_pass = document.getElementById('alta_usuario_pass');
    // if(alta_usuario_pass.value=='' && alta_usuario_apellido_1.value!='')
    // {
    refresca_contrasenya_incorporacion();
    // }
}

function refresca_contrasenya_incorporacion() {
    var alta_usuario_apellido_1 = document.getElementById('alta_usuario_apellido_1');


    $.ajax({
        url: "select/Acceso_Contrasenya.php?alta_usuario_apellido_1=" + encodeURIComponent(alta_usuario_apellido_1.value),
        success: function (response) {

            alta_usuario_pass.value = response;
        }
    });

}

function refresca_usuario_incorporacion() {
    var alta_usuario_nombre = document.getElementById('alta_usuario_nombre');
    var alta_usuario_apellido_1 = document.getElementById('alta_usuario_apellido_1');
    var alta_usuario_apellido_2 = document.getElementById('alta_usuario_apellido_2');

    var Usuario_Incorporacion = document.getElementById('Usuario_Incorporacion');
    var Mail_Incorporacion = document.getElementById('Mail_Incorporacion');

    $.ajax({
        url: "select/Acceso_Incorporacion.php?alta_usuario_nombre=" + encodeURIComponent(alta_usuario_nombre.value) + "&alta_usuario_apellido_1=" + encodeURIComponent(alta_usuario_apellido_1.value) + "&alta_usuario_apellido_2=" + encodeURIComponent(alta_usuario_apellido_2.value) + "&Usuario_Incorporacion=" + encodeURIComponent(Usuario_Incorporacion.value) + "&Mail_Incorporacion=" + encodeURIComponent(Mail_Incorporacion.value),
        success: function (response) {

            tipos = response.split("|");
            Usuario_Incorporacion.value = tipos[0];
            Mail_Incorporacion.value = tipos[1];
        }
    });

}


function accede_internet(obj) {
    if (obj.checked == true) {
        obj.value = '1';

        $("#tr_dato_acceso_internet_1").css("display", "block");


    } else {
        obj.value = '0';
        $("#tr_dato_acceso_internet_1").css("display", "none");

    }

}

function accede_remoto(obj) {
    if (obj.checked == true) {
        obj.value = '1';
    } else {
        obj.value = '0';
    }
}

function accede_crear_ad(obj) {
    if (obj.checked == true) {
        obj.value = '1';
    } else {
        obj.value = '0';
    }
}

function accede_movil(obj) {
    if (obj.checked == true) {
        obj.value = '1';
    } else {
        obj.value = '0';
    }
}
/*
 * function accede_estrategico(obj) { if (obj.checked == true) { obj.value = '1'; } else { obj.value = '0'; } }
 * 
 * function accede_Requiere_Documentacion(obj) { if (obj.checked == true) { obj.value = '1'; } else { obj.value = '0'; } }
 */
function accede_pc(obj) {
    if (obj.checked == true) {
        obj.value = '1';
    } else {
        obj.value = '0';
    }
}

function accede_portatil(obj) {
    if (obj.checked == true) {
        obj.value = '1';
    } else {
        obj.value = '0';
    }
}

function accede_telefono(obj) {
    if (obj.checked == true) {
        obj.value = '1';
    } else {
        obj.value = '0';
    }
}

function crear_alta_usuario(id_pantalla, tarea, test) {

    if (!comprueba_inputs_alta_usuario()) {
        // LOS CAMPOS DEL FORMULARIO DE ALTA CONTIENEN ERRORES NO SE DA DE ALTA USUARIO
    } else {



        if (id_pantalla == 2) {
            var dialogo = document.getElementById('dialog-confirm');
            dialogo.innerHTML = '<p><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/alert.png' width=\"30\" height=\"30\" />&nbsp;" + 'No tiene permiso para ejecutar este proceso.</p>';
            $("#dialog-confirm").dialog({
                resizable: false,
                height: 200,
                width: 400,
                modal: true,
                position: [100, 380],
                buttons: {
                    "OK": function () {
                        $(this).dialog("close");

                    }
                }
            });
        } else {

            // Guardamos en la tabla de alta_usuario los últimos cambios
            /*
             * var alta_nombre = document.getElementById("alta_usuario_nombre").value; var alta_ape1 = document.getElementById("alta_usuario_apellido_1").value; var alta_ape2 = document.getElementById("alta_usuario_apellido_2").value; var alta_mail = document.getElementById("Mail_Incorporacion").value; var alta_reporta = document.getElementById("autocompletereportaa").value; var alta_permisoscomo = document.getElementById("autocompletepermisoscomo").value; var alta_usuario = document.getElementById("Usuario_Incorporacion").value; var alta_password = document.getElementById("alta_usuario_pass").value; var alta_usuario_nav = document.getElementById("alta_usuario_acceso_navision_user").value; var alta_password_nav = document.getElementById("alta_usuario_acceso_navision_pass").value; var alta_espejo_nav = document.getElementById("selectRegistrosEspejo").value;
             */

            // El script a dónde se realizará la petición.
            // var url = "select/modificar_alta_usuario.php?tarea="+tarea+"&alta_nombre="+encodeURIComponent(alta_nombre)+"&alta_ape1="+encodeURIComponent(alta_ape1)+"&alta_ape2="+encodeURIComponent(alta_ape2)+"&alta_usuario="+encodeURIComponent(alta_usuario)+"&alta_mail="+encodeURIComponent(alta_mail)+"&alta_reporta="+encodeURIComponent(alta_reporta)+"&alta_permisoscomo="+encodeURIComponent(alta_permisoscomo)+"&alta_password="+encodeURIComponent(alta_password)+"&alta_usuario_nav="+encodeURIComponent(alta_usuario_nav)+"&alta_password_nav="+encodeURIComponent(alta_password_nav)+"&alta_espejo_nav="+encodeURIComponent(alta_espejo_nav);
            var url = "select/modificar_alta_usuario.php?tarea=" + tarea;

            $.ajax({
                url: url,
                type: "GET",
                cache: false,
                data: {
                    // CUADRO DATOS USUARIO
                    alta_nombre: document.getElementById("alta_usuario_nombre").value,
                    alta_ape1: document.getElementById("alta_usuario_apellido_1").value,
                    alta_ape2: document.getElementById("alta_usuario_apellido_2").value,
                    alta_fecha: document.getElementById("fechaincorporacion").value,
                    alta_mail: document.getElementById("Mail_Incorporacion").value,
                    alta_reporta: document.getElementById("autocompletereportaa").value,
                    alta_permisoscomo: document.getElementById("autocompletepermisoscomo").value,
                    alta_usuario: document.getElementById("Usuario_Incorporacion").value,
                    alta_password: document.getElementById("alta_usuario_pass").value,
                    alta_departamento: document.getElementById("selectdepartamentoalta").value,
                    alta_programa: document.getElementById("selectprogramaalta").value,
                    // CUADRO NAVISION
                    alta_accede_nav: document.getElementById("alta_usuario_acceso_navision").value,
                    alta_usuario_nav: document.getElementById("alta_usuario_acceso_navision_user").value,
                    alta_password_nav: document.getElementById("alta_usuario_acceso_navision_pass").value,
                    alta_espejo_nav: document.getElementById("selectRegistrosEspejo").value,
                    // CUADRO MAPEX
                    alta_accede_mapex_ter: document.getElementById("alta_usuario_acceso_Mapex_Terminal").value,
                    alta_tipo_mapex_ter: document.getElementById("selectRegistrosTipoMapexTermimal").value,
                    alta_espejo_mapex_ter: document.getElementById("selectRegistrosEspejoMapexTermimal").value,

                    alta_accede_mapex_apl: document.getElementById("alta_usuario_acceso_Mapex_Aplicacion").value,
                    alta_tipo_mapex_apl: document.getElementById("selectRegistrosTipoMapexAplicacion").value,
                    alta_espejo_mapex_apl: document.getElementById("selectRegistrosEspejoMapexAplicacion").value,

                    // CUADRO INTERNET
                    alta_internet: document.getElementById("alta_usuario_acceso_internet").value,
                    alta_internet_tipo: document.getElementById("selecttipointernet").value,
                    // CUADRO REMOTO
                    alta_remoto: document.getElementById("alta_usuario_acceso_remoto").value,
                    // CUADRO CREAR AD
                    alta_crear_ad: document.getElementById("alta_usuario_crear_ad").value,
                    // CUADRO HERRAMIENTAS
                    alta_usuario_pc: document.getElementById("alta_usuario_acceso_pc").value,
                    alta_usuario_porta: document.getElementById("alta_usuario_acceso_portatil").value,
                    alta_usuario_tel: document.getElementById("alta_usuario_acceso_telefono").value,
                    alta_usuario_movil: document.getElementById("alta_usuario_acceso_movil").value,
                },

                success: function (data) {
                    if (data) {

                    }
                }
            });

            var url = "select/crear_usuario.php?tarea=" + tarea + "&test=" + test + "&usuario=" + encodeURIComponent(usuariovalidado); // El script a dónde se realizará la petición.
            $.ajax({
                type: "GET",
                url: url,
                cache: false,
                success: function (data) {
                    if (test == 1) {
                        var dialogo = document.getElementById('dialog-iniciar');
                        dialogo.innerHTML = data;
                        $("#dialog-iniciar").dialog({
                            resizable: false,
                            title: 'Creacion Alta',
                            height: 400,
                            width: 800,
                            modal: true,
                            position: [100, 380],
                            buttons: {
                                "CANCELAR": function () {
                                    $(this).dialog("close");
                                },
                                "EJECUTAR": function () {
                                    $(this).dialog("close");
                                    crear_alta_usuario(id_pantalla, tarea, 0);
                                }
                            }
                        });
                    } else {
                        // Test == 0
                        datos = data.split('#');

                        if (datos[1] == '1') {
                            var div_crear_alta_usuario = document.getElementById('div_crear_alta_usuario');
                            div_crear_alta_usuario.innerHTML = '';
                        }
                        var dialogo = document.getElementById('dialog-iniciar');
                        dialogo.innerHTML = datos[0];
                        $("#dialog-iniciar").dialog({
                            resizable: false,
                            height: 400,
                            width: 800,
                            title: 'Resultado Creacion Alta',
                            modal: true,
                            position: [100, 380],
                            buttons: {
                                "OK": function () {
                                    $(this).dialog("close");
                                }
                            }
                        });
                    }
                }
            });
        }
    }
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 13/07/2016
 * @returns {Boolean}
 */
function comprueba_inputs_alta_usuario() {

    var Usuario_Incorporacion = $("#Usuario_Incorporacion").val();
    var Mail_Incorporacion = $("#Mail_Incorporacion").val();
    var alta_usuario_pass = $("#alta_usuario_pass").val();
    var alta_usuario_acceso_navision_user = $("#alta_usuario_acceso_navision_user").val();
    var alta_usuario_acceso_navision_pass = $("#alta_usuario_acceso_navision_pass").val();

    var acentos = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç";

    var mayusculas = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var minusculas = "abcdefghijklmnopqrstuvwxyz";
    var numeros = "0123456789";

    var ret = true;

    // Comprobacion campo usuario
    if (ret) {
        ret = comprueba_patron(Usuario_Incorporacion, acentos);
        if (!ret) {
            dialogo('El usuario contiene acentos o caracteres no permitidos.');
        }
    }
    if (ret) {
        ret = comprueba_patron(Usuario_Incorporacion, mayusculas);
        if (!ret) {
            dialogo('El usuario no puede contener mayúsculas.');
        }
    }

    // Comprobacion campo mail
    if (ret) {
        ret = comprueba_patron(Mail_Incorporacion, acentos);
        if (!ret) {
            dialogo('El mail contiene acentos o caracteres no permitidos.');
        }
    }
    if (ret) {
        ret = comprueba_patron(Mail_Incorporacion, mayusculas);
        if (!ret) {
            dialogo('El mail no puede contener mayúsculas.');
        }
    }

    // Comprobacion campo contraseña usuario
    if (ret) {
        ret = comprueba_patron(alta_usuario_pass, acentos);
        if (!ret) {
            dialogo('La contraseña del usuario contiene acentos o caracteres no permitidos.');
        }
    }
    if (ret) {
        ret = comprueba_patron(alta_usuario_pass, mayusculas);
        if (!ret) {
            dialogo('La contraseña del usuario no puede contener mayúsculas.');
        }
    }

    // Comprobacion campo usuario navision
    if (ret && alta_usuario_acceso_navision_user != '') {
        ret = comprueba_patron(alta_usuario_acceso_navision_user, acentos);
        if (!ret) {
            dialogo('El usuario de Navision contiene acentos o caracteres no permitidos.');
        }
    }
    if (ret && alta_usuario_acceso_navision_user != '') {
        ret = comprueba_patron(alta_usuario_acceso_navision_user, minusculas);
        if (!ret) {
            dialogo('El usuario de Navision no puede contener minúsculas.');
        }
    }

    // Comprobacion campo contraseña navision
    if (ret && alta_usuario_acceso_navision_pass != '') {
        ret = comprueba_patron(alta_usuario_acceso_navision_pass, acentos);
        if (!ret) {
            dialogo('La contraseña de Navision contiene acentos o caracteres no permitidos.');
        }
    }
    if (ret && alta_usuario_acceso_navision_user != '') {
        ret = comprueba_patron(alta_usuario_acceso_navision_pass, mayusculas);
        if (!ret) {
            dialogo('La contraseña de Navision no puede contener mayúsculas.');
        }
    }

    return ret;
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @param texto
 * @date 13/07/2016
 * @returns {Boolean}
 */
function comprueba_patron(texto, patron) {

    for (i = 0; i < texto.length; i++) {
        if (patron.indexOf(texto.charAt(i), 0) != -1) {
            return false;
        }
    }
    return true;
}

function gant_slide(obj) {
    gant(obj.value);
}

function convertDate(inputFormat) {
    function pad(s) {
        return (s < 10) ? '0' + s : s;
    }
    var d = new Date(inputFormat);
    return [pad(d.getDate()), pad(d.getMonth() + 1), d.getFullYear()].join('/');
}

function gantt_agrega_fecha(paso) {
    (function ($) {

        var fecha_inicio = $("#TimeLine").attr("inicio");
        var fecha_fin = $("#TimeLine").attr("fin");
        switch (paso) {
            case 6:
                var fecha = fecha_fin.split("/");
                var dt = new Date(fecha[1] + "/" + fecha[0] + "/" + fecha[2]);
                var dayOfMonth = dt.getDate();
                dt.setDate(dayOfMonth + 7);
                $("#TimeLine").attr("fin", gantt_convertDate(dt));
                break;
            case 5:
                var fecha = fecha_inicio.split("/");
                var dt = new Date(fecha[1] + "/" + fecha[0] + "/" + fecha[2]);
                var dayOfMonth = dt.getDate();
                dt.setDate(dayOfMonth - 7);
                $("#TimeLine").attr("inicio", gantt_convertDate(dt));
                break;
            case 4:
                var fecha = fecha_fin.split("/");
                var dt = new Date(fecha[2], (parseInt(fecha[1]) + 1), 0);
                var day = (7 - dt.getDay());
                if (day == 7) {
                    day = 0;
                }
                var dayOfMonth = dt.getDate();
                dt.setDate(dayOfMonth + day);
                $("#TimeLine").attr("fin", gantt_convertDate(dt));
                break;
            case 3:
                var fecha = fecha_inicio.split("/");
                var dt = new Date(fecha[2], (parseInt(fecha[1]) - 2), "1");
                var day = dt.getDay() - 1;
                var dayOfMonth = dt.getDate();
                dt.setDate(dayOfMonth - day);
                $("#TimeLine").attr("inicio", gantt_convertDate(dt));
                break;
            case 2:
                var fecha = fecha_fin.split("/");
                var dt = new Date(parseInt(fecha[2]) + 1, "12", "0");
                var day = (7 - dt.getDay());
                if (day == 7) {
                    day = 0;
                }
                var dayOfMonth = dt.getDate();
                dt.setDate(dayOfMonth + day);
                $("#TimeLine").attr("fin", gantt_convertDate(dt));
                break;
            case 1:
                var fecha = fecha_inicio.split("/");
                var dt = new Date(parseInt(fecha[2]) - 1, "0", "1");
                var day = dt.getDay() - 1;
                var dayOfMonth = dt.getDate();
                dt.setDate(dayOfMonth - day);
                $("#TimeLine").attr("inicio", gantt_convertDate(dt));
                break;
        }
        gant_slide($("#slider1")[0]);

    })(jQuery);
}

function gantt_convertDate(inputFormat) {
    function pad(s) {
        return (s < 10) ? '0' + s : s;
    }
    var d = new Date(inputFormat);
    return [pad(d.getDate()), pad(d.getMonth() + 1), d.getFullYear()].join('/');
}

function gantt_controladores() {
    (function ($) {
        $(document).ready(function () {
            var position = $('#TimeLine_Calendario').position();
            var top_anyo = $('.Anyo').css('height').replace(/[^-\d\.]/g, '');
            var top_mes = $('.Mes').css('height').replace(/[^-\d\.]/g, '');
            var top_semana = 55;
            var top_dia = $('.Dias').css('height').replace(/[^-\d\.]/g, '');
            var altura_gantt = $('#TimeLine').css('height').replace(/[^-\d\.]/g, '');

            var width_anyo = 0;
            $(".Anyo").each(function (index) {
                width_anyo = width_anyo + $('#' + this.id).width();
            });


            $("#misecciongant").append('<div id="gantt_mas_anyo_atras" class="Controlador_mas" onClick="gantt_agrega_fecha(1)"></div>');
            $("#misecciongant").append('<div id="gantt_mas_anyo_adelante" class="Controlador_mas" onClick="gantt_agrega_fecha(2)"></div>');
            $("#misecciongant").append('<div id="gantt_mas_mes_atras" class="Controlador_mas" onClick="gantt_agrega_fecha(3)"></div>');
            $("#misecciongant").append('<div id="gantt_mas_mes_adelante" class="Controlador_mas" onClick="gantt_agrega_fecha(4)"></div>');
            $("#misecciongant").append('<div id="gantt_mas_dia_atras" class="Controlador_mas_beige" onClick="gantt_agrega_fecha(5)"></div>');
            $("#misecciongant").append('<div id="gantt_mas_dia_adelante" class="Controlador_mas_beige" onClick="gantt_agrega_fecha(6)"></div>');

            $("#misecciongant").append('<div id="gantt_next_anyo" class="Controlador_desplazar_next" onClick="gantt_desplaza_fecha(1)"></div>');
            $("#misecciongant").append('<div id="gantt_prev_anyo" class="Controlador_desplazar_prev" onClick="gantt_desplaza_fecha(2)"></div>');
            $("#misecciongant").append('<div id="gantt_next_mes" class="Controlador_desplazar_next" onClick="gantt_desplaza_fecha(3)"></div>');
            $("#misecciongant").append('<div id="gantt_prev_mes" class="Controlador_desplazar_prev" onClick="gantt_desplaza_fecha(4)"></div>');
            $("#misecciongant").append('<div id="gantt_next_dia" class="Controlador_desplazar_next" onClick="gantt_desplaza_fecha(5)"></div>');
            $("#misecciongant").append('<div id="gantt_prev_dia" class="Controlador_desplazar_prev" onClick="gantt_desplaza_fecha(6)"></div>');

            if (parseFloat($('#TimeLine_Calendario').width()) < width_anyo) {
                width_anyo = $('#TimeLine_Calendario').width();
            }
            $("#gantt_mas_anyo_atras").css("top", position.top + "px");
            $("#gantt_mas_anyo_atras").css("left", (parseFloat(position.left) - 20) + "px");
            $("#gantt_mas_anyo_atras").css("height", top_anyo + "px");
            $("#gantt_mas_anyo_atras").css("border-radius", "5px 0px 0px 5px");
            $("#gantt_mas_anyo_adelante").css("top", position.top + "px");
            $("#gantt_mas_anyo_adelante").css("left", (parseFloat(position.left) + parseFloat(width_anyo)) + "px");
            $("#gantt_mas_anyo_adelante").css("height", top_anyo + "px");
            $("#gantt_mas_anyo_adelante").css("border-radius", "0px 5px 5px 0px");

            $("#gantt_prev_anyo").css("top", position.top + "px");
            $("#gantt_prev_anyo").css("left", (parseFloat(position.left)) + "px");
            $("#gantt_prev_anyo").css("height", top_anyo + "px");
            $("#gantt_next_anyo").css("top", position.top + "px");
            $("#gantt_next_anyo").css("left", (parseFloat(position.left) + parseFloat(width_anyo) - 20) + "px");
            $("#gantt_next_anyo").css("height", top_anyo + "px");

            $("#gantt_mas_mes_atras").css("top", (parseFloat(position.top) + parseFloat(top_anyo)) + "px");
            $("#gantt_mas_mes_atras").css("left", (parseFloat(position.left) - 20) + "px");
            $("#gantt_mas_mes_atras").css("height", top_mes + "px");
            $("#gantt_mas_mes_atras").css("border-radius", "5px 0px 0px 5px");
            $("#gantt_mas_mes_adelante").css("top", (parseFloat(position.top) + parseFloat(top_anyo)) + "px");
            $("#gantt_mas_mes_adelante").css("left", (parseFloat(position.left) + parseFloat(width_anyo)) + "px");
            $("#gantt_mas_mes_adelante").css("height", top_mes + "px");
            $("#gantt_mas_mes_adelante").css("border-radius", "0px 5px 5px 0px");

            $("#gantt_prev_mes").css("top", (parseFloat(position.top) + parseFloat(top_anyo)) + "px");
            $("#gantt_prev_mes").css("left", (parseFloat(position.left)) + "px");
            $("#gantt_prev_mes").css("height", top_mes + "px");
            $("#gantt_next_mes").css("top", (parseFloat(position.top) + parseFloat(top_anyo)) + "px");
            $("#gantt_next_mes").css("left", (parseFloat(position.left) + parseFloat(width_anyo) - 20) + "px");
            $("#gantt_next_mes").css("height", top_mes + "px");

            $("#gantt_mas_dia_atras").css("top", (parseFloat(position.top) + parseFloat(top_anyo) + parseFloat(top_mes)) + "px");
            $("#gantt_mas_dia_atras").css("left", (parseFloat(position.left) - 20) + "px");
            $("#gantt_mas_dia_atras").css("height", (parseFloat(top_semana) + parseFloat(top_dia)) + "px");
            $("#gantt_mas_dia_atras").css("border-radius", "5px 0px 0px 5px");
            $("#gantt_mas_dia_adelante").css("top", (parseFloat(position.top) + parseFloat(top_anyo) + parseFloat(top_mes)) + "px");
            $("#gantt_mas_dia_adelante").css("left", (parseFloat(position.left) + parseFloat(width_anyo)) + "px");
            $("#gantt_mas_dia_adelante").css("height", (parseFloat(top_semana) + parseFloat(top_dia)) + "px");
            $("#gantt_mas_dia_adelante").css("border-radius", "0px 5px 5px 0px");

            $("#gantt_prev_dia").css("top", (parseFloat(position.top) + parseFloat(top_anyo) + parseFloat(top_mes)) + "px");
            $("#gantt_prev_dia").css("left", (parseFloat(position.left)) + "px");
            $("#gantt_prev_dia").css("height", (parseFloat(altura_gantt) - (parseFloat(top_semana) + parseFloat(top_dia))) + "px");
            $("#gantt_next_dia").css("top", (parseFloat(position.top) + parseFloat(top_anyo) + parseFloat(top_mes)) + "px");
            $("#gantt_next_dia").css("left", (parseFloat(position.left) + parseFloat(width_anyo) - 20) + "px");
            $("#gantt_next_dia").css("height", (parseFloat(altura_gantt) - (parseFloat(top_semana) + parseFloat(top_dia))) + "px");

        });
    })(jQuery);
}

function gantt_desplaza_fecha(paso) {
    (function ($) {
        // var hora_inicio = $( "#TimeLine" ).attr( "hora_inicio" );
        // var hora_fin = $( "#TimeLine" ).attr( "hora_fin" );
        // var horas = parseInt(parseInt(hora_fin)-parseInt(hora_inicio)) + 1;

        var scroll_actual = parseInt($("#TimeLine_Calendario").scrollLeft());
        var factor = parseInt($("#slider1").val());
        switch (paso) {
            case 6:
                var posicion = 0;
                var tamano = 0;
                var posicion_ultima = 0;
                $(".Semana").each(function () {
                    posicion = $(this).css("left").replace(/[^-\d\.]/g, '');
                    tamano = $(this).css("width").replace(/[^-\d\.]/g, '');
                    if (parseFloat(posicion) < parseFloat(scroll_actual)) {
                        posicion_ultima = parseFloat(posicion);
                    }
                });
                $("#TimeLine_Calendario").animate({
                    scrollLeft: posicion_ultima
                },
                        800);
                break;
            case 5:
                var posicion = 0;
                var tamano = 0;
                var posicion_ultima = 0;
                $(".Semana").each(function () {
                    posicion = $(this).css("left").replace(/[^-\d\.]/g, '');
                    tamano = $(this).css("width").replace(/[^-\d\.]/g, '');
                    if (parseFloat(posicion) > parseFloat(scroll_actual) && posicion_ultima == 0) {
                        posicion_ultima = parseFloat(posicion);
                    }
                });
                $("#TimeLine_Calendario").animate({
                    scrollLeft: posicion_ultima
                },
                        800);
                break;
            case 4:
                var posicion = 0;
                var tamano = 0;
                var posicion_ultima = 0;
                $(".Mes").each(function () {
                    posicion = $(this).css("left").replace(/[^-\d\.]/g, '');
                    tamano = $(this).css("width").replace(/[^-\d\.]/g, '');
                    if (parseFloat(posicion) < parseFloat(scroll_actual)) {
                        posicion_ultima = parseFloat(posicion);
                    }
                });
                $("#TimeLine_Calendario").animate({
                    scrollLeft: posicion_ultima
                },
                        600);
                break;
            case 3:
                var posicion = 0;
                var tamano = 0;
                var posicion_ultima = 0;
                $(".Mes").each(function () {
                    posicion = $(this).css("left").replace(/[^-\d\.]/g, '');
                    tamano = $(this).css("width").replace(/[^-\d\.]/g, '');
                    if (parseFloat(posicion) > parseFloat(scroll_actual) && posicion_ultima == 0) {
                        posicion_ultima = parseFloat(posicion);
                    }
                });
                $("#TimeLine_Calendario").animate({
                    scrollLeft: posicion_ultima
                },
                        600);
                break;
            case 2:
                var posicion = 0;
                var tamano = 0;
                var posicion_ultima = 0;
                $(".Anyo").each(function () {
                    posicion = $(this).css("left").replace(/[^-\d\.]/g, '');
                    tamano = $(this).css("width").replace(/[^-\d\.]/g, '');
                    if (parseFloat(posicion) < parseFloat(scroll_actual)) {
                        posicion_ultima = parseFloat(posicion);
                    }
                });
                $("#TimeLine_Calendario").animate({
                    scrollLeft: posicion_ultima
                },
                        300);
                break;
            case 1:
                var posicion = 0;
                var tamano = 0;
                var posicion_ultima = 0;
                $(".Anyo").each(function () {
                    posicion = $(this).css("left").replace(/[^-\d\.]/g, '');
                    tamano = $(this).css("width").replace(/[^-\d\.]/g, '');
                    if (parseFloat(posicion) > parseFloat(scroll_actual) && posicion_ultima == 0) {
                        posicion_ultima = parseFloat(posicion);
                    }
                });
                $("#TimeLine_Calendario").animate({
                    scrollLeft: posicion_ultima
                },
                        300);
                break;
        }
    })(jQuery);
}

function gantt_cambia_pantalla() {
    (function ($) {
        $(window).resize(function () {
            $("#gantt_mas_anyo_atras").remove();
            $("#gantt_mas_anyo_adelante").remove();
            $("#gantt_mas_mes_atras").remove();
            $("#gantt_mas_mes_adelante").remove();
            $("#gantt_mas_dia_atras").remove();
            $("#gantt_mas_dia_adelante").remove();

            $("#gantt_prev_anyo").remove();
            $("#gantt_next_anyo").remove();
            $("#gantt_prev_mes").remove();
            $("#gantt_next_mes").remove();
            $("#gantt_prev_dia").remove();
            $("#gantt_next_dia").remove();

            gantt_controladores();
        });
    })(jQuery);
}

function gant(tamano) {
    var size = '';
    var parametros = '';
    var opciones_busqueda = $("#informe_select_usuarios").multipleSelect("getSelects");
    if (opciones_busqueda.length > 0) {
        for (a = 0; a < opciones_busqueda.length; a++) {

            if (a > 0) {
                parametros = parametros + '|' + opciones_busqueda[a];
            } else {
                parametros = opciones_busqueda[a];
            }

        }

    }
    if (tamano != '') {
        var fecha_inicio = $("#TimeLine").attr("inicio");
        var fecha_fin = $("#TimeLine").attr("fin");
        size = '&tamano=' + tamano + "&inicio=" + fecha_inicio + "&fin=" + fecha_fin;
    }

    var target = document.getElementById('TimeLine_Calendario');
    var spinner_calendario = new Spinner(opts).spin(target);



    var url = "gant/gant.php?usuarios=" + parametros + size;
    $.ajax({
        type: "GET",
        url: url,
        success: function (data) {
            $("#misecciongant").html(data);
            $('#informe_select_usuarios').multipleSelect({
                onClose: function () {
                    gant($("#slider1").val());
                }
            });
            gantt_controladores();
            gantt_cambia_pantalla();



            $('.Tarea').draggable({
                refreshPositions: false
            });
            $('.Tarea')
                    .bind('dragstart', function (event) {
                        drageando = true;

                        var tam = $("#" + event.currentTarget.id).css('width');
                        var sob = $("#" + event.currentTarget.id).attr('sobrante');
                        tam = tam.replace('px', '');

                        var nuevotam = parseFloat(tam) - parseFloat(sob) + 'px';
                        $("#" + event.currentTarget.id).css('width', nuevotam);
                        $("#" + event.currentTarget.id).css('z-index', 2);

                        $("#" + event.currentTarget.id).attr('sobrante', 0);
                        $("#" + event.currentTarget.id).attr('agregante', sob);

                        var p = $("#TimeLine_Calendario");
                        var position = p.position();

                        desplazar(event.currentTarget.id);

                    })
                    .bind('drag', function (event) {




                    })
                    .bind('dragstop', function (event) {
                        drageando = false;
                        desplazar_parar();

                        $("[drop_tipo|='drop']").each(function (indice, elemento) {

                            var pos_inicial = $(elemento).attr("drop_posicion_inicio");
                            var pos_final = $(elemento).attr("drop_posicion_fin");

                            var mip = $("#TimeLine_Calendario");
                            var miposition = mip.position();
                            var mipos = (($("#TimeLine_Calendario").scrollLeft() + x) - miposition.left);

                            if (pos_inicial <= mipos && pos_final >= mipos) {
                                alert('Mueves la tarea ' + event.currentTarget.id + ' al mes ' + $(elemento).attr("drop_mes") +
                                        ' del año ' + $(elemento).attr("drop_anyo") +
                                        ' del dia ' + $(elemento).attr("drop_dia") +
                                        'a la hora ' + $(elemento).attr("drop_hora"));

                            }
                        });



                        $(event.dragProxy).remove();

                    });
        }

    });



}

function abrir_tarea(tarea) {
    ID_SELECCIONADA = tarea;
    cambia_menu(3);
}

function getPos(e) {
    x = e.clientX;
    y = e.clientY;

}

function desplazar(id) {
    if (drageando == true) {
        refreshIntervalId = setInterval('desplazar_scroll(' + 1 + ',' + id + ')', 1);
    } else {
        desplazar_parar();
    }
}

function desplazar_scroll(cantidad, id) {




    if (drageando == true) {
        var p = $("#TimeLine_Calendario");
        var position = p.position();
        var pos = $("#" + id.id).css('left');
        tam = pos.replace('px', '');

        nuevotam = (($("#TimeLine_Calendario").scrollLeft() + x) - position.left) + 'px';
        $("#" + id.id).css('left', nuevotam);

    } else {
        desplazar_parar();
    }
}

function desplazar_parar() {
    clearInterval(refreshIntervalId);
    refreshIntervalId = -1;
}

function generar_tooltip_adjunto(destino) {


    $(function ($) {
        $("#" + destino).click(
                function () {
                    var elemento_id = this.id;

                    var url = "select/ConsultaPDAs.php?id=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado);
                    $.ajax({
                        url: url
                    }).done(function (data) {
                        $('#progress .progress-bar').css('width', '0%');
                        var elemento = $("#" + destino);
                        var posicion = elemento.position();

                        $("#tooltip2sub").html(data);
                        $("#tooltip2").css('left', posicion.left + 'px');
                        $("#tooltip2").css('top', posicion.top + $(document).scrollTop() + 'px');
                        $("#tooltip2").css('display', 'block');

                        $("#autocompletepda").autocomplete({
                            type: 'post',
                            source: function (request, response) {
                                $.get("select/getPDAcomplete.php", {
                                    buscar: request.term,
                                    tipo: 1
                                }, function (data) {
                                    tags = data.split("|");
                                    response(tags);
                                });
                            }
                        });

                    });

                }
        );
    });
    $("#tooltip2").on("mouseleave", function () {
        $("#tooltip2").css('display', 'none');
    });

}

function ObtenerPDA() {
    $("#autocompletepda").submit();
    if ($("#autocompletepda").val() != '') {
        Asociar_PDA_bd($("#autocompletepda").val());
    }
}

function Asocia_PDA(obj) {
    $("#" + obj.id).submit();

    Asociar_PDA_bd($("#" + obj.id).val());
}

function Asociar_PDA_bd(valor) {
    var valores = valor.split("-");
    id_valor = valores[0];
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'uploader/insertar_PDA.php?tarea=' + ID_SELECCIONADA + '&numero=' + id_valor + "&usuario=" + encodeURIComponent(usuariovalidado),
        success: function (data) {

            $("#lista_subir_ficheros_lista_adjuntos").append(data);

            $('#pda_imagen').attr('src', 'imagenes/pda.png');
            $("#autocompletepda").val('');
        }
    });
}

function subir_pda_borrar_lista(id, numero, tarea) {
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'uploader/borrar_PDA.php?tarea=' + tarea + '&numero=' + numero + "&usuario=" + encodeURIComponent(usuariovalidado),
        success: function (data) {
            $("#" + id).remove();
            if (data == 0) {
                $('#pda_imagen').attr('src', 'imagenes/pda_sin.png');
            }
        }
    });
}

function spb_mensaje(tarjet, mensaje) {
    var offset = $('#' + tarjet).offset();

    $("#spb_mensaje").remove();
    $('body').append('<div id="spb_mensaje" name="spb_mensaje" class="spb_mensaje"><div id="spb_mensaje_cerrar" name="spb_mensaje_cerrar" class="spb_mensaje_cerrar" onClick="spb_mensaje_cerrar();"></div><div id="spb_mensaje_resultado" name="spb_mensaje_resultado" class="spb_mensaje_resultado">' + mensaje + '</div></div>');
    $("#spb_mensaje").toggle("fast");
    $("#spb_mensaje").css('z-index', 99999);
    $("#spb_mensaje").css('left', offset.left + 'px');
    $("#spb_mensaje").css('top', offset.top + 'px');
    $("#spb_mensaje").draggable({
        refreshPositions: true
    });
}

function spb_mensaje_cerrar() {
    $("#spb_mensaje").remove();
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @date 2018/01/18
 * @description llama a un procedimiento almacenado, que creará las evaluaciónes de las fichadas que se han creado de forma manual fuera del periodo
 */
function genera_evaluaciones_diferidas() {

    var femFechaPeriodo;
    var femAutocompleteUsuarioDiferido;
    var enviar = true;

    femFechaPeriodo = $("#femFechaPeriodo").val();
    femFechaPeriodo = femFechaPeriodo.substring(6, 10) + femFechaPeriodo.substring(3, 5) + femFechaPeriodo.substring(0, 2);

    femAutocompleteUsuarioDiferido = $("#femAutocompleteUsuarioDiferido").val();
    femOficinaDiferida = $("#femOficinaDiferida").val();

    $("#femFechaPeriodo").removeClass("rojo");
    $("#femAutocompleteUsuarioDiferido").removeClass("rojo");
    $("#femOficinaDiferida").removeClass("rojo");

    if (femFechaPeriodo == '') {
        $('#femFechaPeriodo').focus( );
        $("#femFechaPeriodo").addClass("rojo");
        mensaje('Campo obligatorio', 'Debe de indicar una fecha del periodo.', 'alert.png');
        enviar = false;
    } else {
        if (femAutocompleteUsuarioDiferido == '') {
            $('#femAutocompleteUsuarioDiferido').focus( );
            $("#femAutocompleteUsuarioDiferido").addClass("rojo");
            mensaje('Campo obligatorio', 'Debe de indicar el usuario a quien generar las evaluaciones.', 'alert.png');
            enviar = false;
        } else {
            if (femOficinaDiferida == '-1') {
                $('#femOficinaDiferida').focus( );
                $("#femOficinaDiferida").addClass("rojo");
                mensaje('Campo obligatorio', 'Debe de indicar en que oficina generar las evaluaciones.', 'alert.png');
                enviar = false;
            }
        }
    }

    // Si enviar es verdadero, llamaremos al procedure
    if (enviar) {
        var url = "select/generar_evaluaciones_en_diferido.php";

        $.ajax({
            cache: false,
            async: true,
            method: "POST",
            url: url,
            data: {
                femFechaPeriodo: femFechaPeriodo,
                femAutocompleteUsuarioDiferido: femAutocompleteUsuarioDiferido,
                femOficinaDiferida: femOficinaDiferida
            }
        }).done(function (ret) {
            $("#grid_array_evaluacion_masiva").pqGrid("refreshDataAndView");
        });
    }
}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @date 2017/06/13
 */
function evaluacion_masiva() {

    var femDesde;
    var femHasta;
    var femAutocompleteUsuario;
    var femValoracion;
    var femOficina;
    var enviar = true;

    femDesde = $("#femDesde").val();
    femHasta = $("#femHasta").val();

    femDesde = femDesde.substring(6, 10) + femDesde.substring(3, 5) + femDesde.substring(0, 2);
    femHasta = femHasta.substring(6, 10) + femHasta.substring(3, 5) + femHasta.substring(0, 2);

    femAutocompleteUsuario = $("#femAutocompleteUsuario").val();
    femValoracion = $("#femValoracion").val();
    femOficina = $("#femOficina").val();

    $("#femDesde").removeClass("rojo");
    $("#femHasta").removeClass("rojo");
    $("#femAutocompleteUsuario").removeClass("rojo");
    $("#femValoracion").removeClass("rojo");
    $("#femOficina").removeClass("rojo");

    if (femDesde == '') {
        $('#femDesde').focus( );
        $("#femDesde").addClass("rojo");
        mensaje('Campo obligatorio', 'Debe de indicar la fecha de inicio.', 'alert.png');
        enviar = false;
    } else {
        if (femHasta == '') {
            $('#femHasta').focus( );
            $("#femHasta").addClass("rojo");
            mensaje('Campo obligatorio', 'Debe de indicar la fecha de fin.', 'alert.png');
            enviar = false;
        } else {
            if (femAutocompleteUsuario == '') {
                $('#femAutocompleteUsuario').focus( );
                $("#femAutocompleteUsuario").addClass("rojo");
                mensaje('Campo obligatorio', 'Debe de indicar el usuario a quien va a evaluar.', 'alert.png');
                enviar = false;
            } else {
                if (femValoracion == '-1') {
                    $('#femValoracion').focus( );
                    $("#femValoracion").addClass("rojo");
                    mensaje('Campo obligatorio', 'Debe de indicar la nota de evaluación.', 'alert.png');
                    enviar = false;
                } else {
                    if (femOficina == '-1') {
                        $('#femOficina').focus( );
                        $("#femOficina").addClass("rojo");
                        mensaje('Campo obligatorio', 'Debe de indicar la oficina.', 'alert.png');
                        enviar = false;
                    }
                }
            }
        }
    }
    // Si enviar es verdadero, lanzamos llamada para la actualización
    if (enviar) {
        var url = "select/evaluacion_masiva_tareas.php";

        $.ajax({
            cache: false,
            async: true,
            method: "POST",
            url: url,
            data: {
                femUsuario: usuariovalidado,
                femDesde: femDesde,
                femHasta: femHasta,
                femAutocompleteUsuario: femAutocompleteUsuario,
                femValoracion: femValoracion,
                femOficina: femOficina
            }
        }).done(function (ret) {
            $("#grid_array_evaluacion_masiva").pqGrid("refreshDataAndView");
        });
    }
}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @date 2017/09/21
 * @param pdte
 */
function carga_grid_eva_masiva(pdte) {
    // pdte 0, todas
    // pdte 1, pendientes
    if (typeof $gridEvalMas !== 'undefined') {
        $gridEvalMas.pqGrid('destroy');
    }

    inicia_fechas_evamasiva();
    CargaGridEvaluacionesMasivas(pdte);
}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @date 2017/06/09
 */
function CargaGridEvaluacionesMasivas(pdte) {

    var valores = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    var valores_filtro = ['', 'Pdte', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];

    var obj = {
        height: '500',
        width: '100%',
        title: "Evaluación individual de tareas",
        hoverMode: 'cell',
        selectionModel: {
            type: 'cell',
            mode: 'single'},
        editModel: {
            clicksToEdit: 1,
            saveKey: '13',
            onSave: null
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        },
        filterModel: {
            on: true,
            mode: "AND",
            header: true
        },
        cellSave: function (event, ui) {
            cellSave(event, ui);
        },
    };

    obj.colModel = [
        {
            title: "Usuario",
            width: 120,
            dataType: "text",
            editable: false,
            dataIndx: "Usuario_Nombre",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Usuario_Nombre", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Id",
            width: 70,
            hidden: true,
            dataIndx: "Id",
        },
        {
            title: "Evaluación",
            width: 70,
            dataType: "string",
            editable: false,
            hidden: true,
            dataIndx: "Automatica",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData["Automatica"]) == 0) {
                    return "PARCIAL";
                } else {
                    return "FINAL";
                }
            }
        },
        {
            title: "Tarea",
            width: 40,
            dataType: "string",
            editable: false,
            hidden: false,
            dataIndx: "Tarea",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Tarea", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Descripcion Tarea",
            width: 300,
            dataType: "string",
            editable: false,
            hidden: false,
            dataIndx: "DescripcionTarea"
        },
        {
            title: "Observaciones, Fichajes y Comentarios",
            width: 85,
            dataType: "string",
            editable: false,
            hidden: false,
            align: "center",
            render: function (ui) {
                var rowData = ui.rowData;
                var num_obs = rowData["Num_Observaciones"];
                var btn = '';
                // if(num_obs > 0){ // 28/11/17 De momento se comenta para mostrar siempre el boton
                var tarea = rowData["Tarea"];
                var periodo = rowData["Periodo"];
                var fechaCreacion = rowData["FechaCreacion"];
                var usuarioEvaluado = rowData["Usuario"];
                btn = "<input type='button' style='width:70px;' value='Mostrar' title=' Ver información de la tarea " + tarea + "' onclick='pop_up_Comentarios(" + tarea + ",\"" + periodo + "\",\"" + fechaCreacion + "\",\"" + usuarioEvaluado + "\");' />";
                // }
                return btn;
            }
        },
        {
            title: "Aporte Jefe",
            width: 50,
            dataType: "string",
            editable: false,
            hidden: false,
            align: "center",
            dataIndx: "AporteJefe",
            editor: {
                type: 'select',
                options: function (ui) {
                    return valores;
                }
            }
        },
        {
            title: "Periodo",
            width: 60,
            dataType: "string",
            editable: false,
            hidden: false,
            dataIndx: "Periodo"
        },
        {
            title: "Año ISO",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Anyo_ISO",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Anyo_ISO", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Semana ISO",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Semana_ISO",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Semana_ISO", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Año",
            width: 30,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Anyo",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Anyo", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Mes",
            width: 30,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Mes",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Mes", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Tiempo Fichado Periodo",
            width: 30,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "TiempoFichado",
            align: "right"
        },
        {
            title: "Fecha Calidad",
            width: 70,
            dataType: "date",
            editable: false,
            dataIndx: "Fecha_Usuario",
        },
        {
            title: "Calidad del Trabajo Anterior",
            width: 70,
            dataType: "text",
            editable: false,
            dataIndx: "Med_Calidad_Anterior",
            align: "center",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Calidad_Anterior" ]) == -1) {
                    return '';
                } else {
                    if (parseInt(rowData[ "Med_Calidad_Anterior" ]) < 7) {
                        return "<span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Calidad_Anterior" ] + "</span>";
                    } else {
                        return "<span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Calidad_Anterior" ] + "</span></font>";
                    }
                }

            }
        },
        {
            title: "Calidad del Trabajo",
            width: 70,
            dataType: "text",
            editable: true,
            dataIndx: "Med_Calidad",
            align: "center",
            filter: {
                options: valores_filtro,
                type: 'select',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Med_Calidad", $(this).val( ));
                        }
                    }]
            },
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Calidad" ]) == -1) {
                    if (parseInt(rowData["Automatica"]) == 0) {
                        return '<img class="imagenes_pequenyas" src="./imagenes/finger.png" title="Pinche aquí para evaluar al usuario.">';
                    } else {
                        return '<img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Pendiente de evaluación por parte del usuario.">';
                    }
                } else {
                    if (parseInt(rowData[ "Med_Calidad" ]) < 7) {
                        return "<span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Calidad" ] + "</span>";
                    } else {
                        return "<span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Calidad" ] + "</span></font>";
                    }
                }
            },
            editor: {
                type: 'select',
                options: function (ui) {
                    return valores;
                }
            },
        },
        {
            title: "Fecha Calidad",
            width: 70,
            dataType: "date",
            editable: false,
            dataIndx: "Fecha_Usuario",
        },
        {
            title: "Enfoque al Cliente",
            width: 70,
            dataType: "text",
            editable: false,
            hidden: true,
            dataIndx: "Med_Enfoque_Cliente",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Med_Enfoque_Cliente", $(this).val());
                        }
                    }]
            },
            editor: {
                type: 'select',
                options: function (ui) {
                    return valores;
                }
            },
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Enfoque_Cliente" ]) == -1) {
                    if (parseInt(rowData["Automatica"]) == 0) {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Pendiente de evaluación por parte del usuario."> </center>';
                    } else {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Pendiente de evaluación por parte del usuario."> </center> ';
                    }
                } else {
                    if (parseInt(rowData[ "Med_Enfoque_Cliente" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Enfoque_Cliente" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_Enfoque_Cliente" ] + "</span></center>";
                    }
                }
            },
        },
        {
            title: "Fechas",
            width: 60,
            dataType: "text",
            editable: false,
            hidden: false,
            align: "center",
            dataIndx: "Med_En_Tiempo",
            filter: {
                options: valores_filtro,
                type: 'select',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Med_En_Tiempo", $(this).val( ));
                        }
                    }]
            },
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_En_Tiempo" ]) == -1) {
                    return '<img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Evaluación automática pendiente de completar tarea.">';
                } else {
                    if (parseInt(rowData[ "Med_En_Tiempo" ]) < 7) {
                        return "<span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_En_Tiempo" ] + "</span>";
                    } else {
                        return "<span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700'; >" + rowData[ "Med_En_Tiempo" ] + "</span>";
                    }
                }
            },
        },
        {
            title: "Rapidez / Estándar",
            width: 60,
            dataType: "text",
            editable: false,
            hidden: false,
            align: "center",
            dataIndx: "Med_Eficiencia",
            filter: {
                options: valores_filtro,
                type: 'select',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Med_Eficiencia", $(this).val( ));
                        }
                    }]
            },
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Eficiencia" ]) == -1) {
                    return '<img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Evaluación automática pendiente de completar tarea." />';
                } else {
                    if (parseInt(rowData[ "Med_Eficiencia" ]) < 7) {
                        return "<span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Eficiencia" ] + "</span>";
                    } else {
                        return "<span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Eficiencia" ] + "</span>";
                    }
                }
            },
        },
        {
            title: "Nota Evaluador Anterior",
            width: 60,
            dataType: "text",
            editable: false,
            align: "center",
            dataIndx: "Med_Evaluador_Anterior",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Evaluador_Anterior" ]) == -1) {
                    return '';
                } else {
                    if (parseInt(rowData[ "Med_Evaluador_Anterior" ]) < 7) {
                        return "<span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700';>" + rowData[ "Med_Evaluador_Anterior" ] + "</span>";
                    } else {
                        return "<span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_Evaluador_Anterior" ] + "</span>";
                    }
                }
            }
        },

        {
            title: "Nota Evaluador",
            width: 60,
            dataType: "text",
            editable: true,
            align: "center",
            dataIndx: "Med_Evaluador",
            filter: {
                options: valores_filtro,
                type: 'select',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Med_Evaluador", $(this).val( ));
                        }
                    }]
            },
            editor: {
                type: 'select',
                options: function (ui) {
                    return valores;
                }
            },
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Evaluador" ]) == -1) {
                    return '<img class="imagenes_pequenyas" src="./imagenes/finger.png" title="Pinche aquí para evaluar al usuario." />';
                } else {
                    if (parseInt(rowData[ "Med_Evaluador" ]) < 7) {
                        return "<span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700';>" + rowData[ "Med_Evaluador" ] + "</span>";
                    } else {
                        return "<span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_Evaluador" ] + "</span>";
                    }
                }
            }
        },
        {
            title: "Fecha Evaluación",
            width: 80,
            dataType: "date",
            editable: false,
            dataIndx: "Fecha_Evaluador"
        },
        {
            title: "Evaluado por",
            width: 150,
            dataType: "text",
            editable: false,
            dataIndx: "Evaluador_Nombre",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Evaluador_Nombre", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Comentario",
            width: 150,
            dataType: "text",
            editable: true,
            dataIndx: "Comentario_Evaluador",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Comentario_Evaluador", $(this).val());
                        }
                    }]
            },
            render: function (ui) {
                var rowData = ui.rowData;
                return "<b><font color='red'>" + rowData["Comentario_Evaluador"] + '</font></b>';
            }
        }
    ];

    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Anyo_ISO", "Semana_ISO"],
        sortDir: ["down", "down"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],
        url: "select/consultaEvaluacionesMasivas.php?usuario=" + encodeURIComponent(usuariovalidado) + "&externo=" + encodeURIComponent(externo) + "&pdte=" + pdte,
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    obj.render = function (evt, obj) {
        if (evaluador == 1) {
            var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));
            $("<span>Mostrar Todas las Evaluaciones</span>").appendTo($toolbar).button({icons: {primary: "ui-icon-triangle-1-s"}}).click(function (evt) {
                carga_grid_eva_masiva(0)
            });
            $("<span>Mostrar Evaluaciones Pendientes de Evaluar</span>").appendTo($toolbar).button({icons: {primary: "ui-icon-triangle-1-n"}}).click(function (evt) {
                carga_grid_eva_masiva(1)
            });
        }
    };

    // Seleccionar la fila
    obj.rowSelect = function (evt, obj) {
        ID_EVALUACION_M_SELECCIONADA = obj.rowData["Id"];
    };
    // Seleccionar la fila
    obj.cellSelect = function (evt, obj) {
        ID_EVALUACION_M_SELECCIONADA = obj.rowData["Id"];
    };
    // 
    obj.cellClick = function (evt, obj) {
        ID_EVALUACION_M_SELECCIONADA = obj.rowData["Id"];
    }
    // Doble Click
    obj.cellDblClick = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Tarea"];
        cambia_menu(3);
        // ID_EVALUACION_M_SELECCIONADA = obj.rowData["Id"];
    };

    $gridEvalMas = $("#grid_array_evaluacion_masiva").pqGrid(obj);

    $gridEvalMas.pqGrid("option", "topVisible", true);
    $gridEvalMas.pqGrid("option", "showTitle", false);
    $gridEvalMas.pqGrid("option", "collapsible", false);
    $gridEvalMas.pqGrid("option", "columnBorders", true);
    $gridEvalMas.pqGrid("option", "rowBorders", true);
    $gridEvalMas.pqGrid("option", "oddRowsHighlight", true);
    $gridEvalMas.pqGrid("option", "numberCell", false);
    $gridEvalMas.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });

    // $gridEvalMas.on("pqgridcellsave", function(evt, ui) {
    function cellSave(event, ui) {


        if (ui.column.dataIndx == 'AporteJefe') {
            var AporteJefe = ui.rowData["AporteJefe"];

            if (AporteJefe == '') {
                return null;
            }

            var Tarea = ui.rowData["Tarea"];
            var url = "select/setCampoTarea.php?Tarea=" + Tarea + "&Campo=AporteJefe&Valor=" + AporteJefe + "&usuario=" + encodeURIComponent(usuariovalidado);

            $.ajax({
                url: url
            }).done(function (data) {
                if (data != '') {
                    mensaje("Evaluaciones", data);
                }

                $("#grid_array_evaluacion_masiva").pqGrid("refreshDataAndView");

            });

        } else {

            var ID = ui.rowData["Id"];
            var Med_Calidad = ui.rowData["Med_Calidad"];
            var Med_Enfoque_Cliente = ui.rowData["Med_Enfoque_Cliente"];
            var Med_Evaluador = ui.rowData["Med_Evaluador"];
            var Comentario_Evaluador = ui.rowData["Comentario_Evaluador"];
            var campo_a_cambiar = '';

            if (Med_Calidad == '' || Med_Enfoque_Cliente == '' || Med_Evaluador == '') {
                $("#grid_array_evaluacion_masiva").pqGrid("refreshDataAndView");
                return null;
            }

            if (ui.column.dataIndx == 'Med_Calidad') {
                campo_a_cambiar = 'Med_Calidad'
            }

            if (ui.column.dataIndx == 'Med_Enfoque_Cliente') {
                campo_a_cambiar = 'Med_Enfoque_Cliente'
            }

            if (ui.column.dataIndx == 'Med_Evaluador') {
                campo_a_cambiar = 'Med_Evaluador'
            }

            if (ui.column.dataIndx == 'Comentario_Evaluador') {
                campo_a_cambiar = 'Comentario_Evaluador'
            }

            if (campo_a_cambiar != '') {
                var url = "select/modificarEvaluacionTarea.php?Id=" + ID + "&Med_Evaluador=" + Med_Evaluador + "&Med_Enfoque_Cliente=" + Med_Enfoque_Cliente + "&Med_Calidad=" + Med_Calidad + "&usuario=" + encodeURIComponent(usuariovalidado) + "&campo_a_cambiar=" + campo_a_cambiar + "&Comentario_Evaluador=" + encodeURIComponent(Comentario_Evaluador);

                $.ajax({
                    url: url
                }).done(function (data) {
                    if (data != '') {
                        mensaje("Evaluaciones", data);
                    }

                    $("#grid_array_evaluacion_masiva").pqGrid("refreshDataAndView");

                });
            } else {
                $("#grid_array_evaluacion_masiva").pqGrid("refreshDataAndView");
            }
        }
    }
    // });

    function filter(dataIndx, value) {
        $gridEvalMas.pqGrid("filter", {
            data: [{
                    dataIndx: dataIndx,
                    value: value
                }]
        });
    }


}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @date 2017/06/09
 */
function CargaGridEvaluacionesUsuario() {

    var valores = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    var notas = [{'-1': '--Pdte.--'}, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

    var obj = {
        width: '100%',
        height: '99%',
        title: "Lista de Evaluaciones",
        hoverMode: 'cell',
        selectionModel: {
            type: 'cell',
            mode: 'single'},
        editModel: {
            clicksToEdit: 1,
            saveKey: '13',
            onSave: null
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        },
        filterModel: {
            on: true,
            mode: "AND",
            header: true
        },
        cellSave: function (event, ui) {
            cellSave(event, ui);
        }
    };

    obj.colModel = [
        {
            title: "Id",
            width: 70,
            hidden: true,
            dataIndx: "Id",
        },
        {
            title: "Tarea",
            width: 50,
            dataType: "string",
            editable: false,
            hidden: false,
            dataIndx: "Tarea",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Tarea", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Título",
            width: 300,
            dataType: "string",
            editable: false,
            hidden: false,
            dataIndx: "Título",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Título", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Evaluación",
            width: 70,
            dataType: "string",
            editable: false,
            hidden: true,
            dataIndx: "Automatica",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData["Automatica"]) == 0) {
                    return "PARCIAL";
                } else {
                    return "FINAL";
                }
            }
        },
        {
            title: "Año ISO",
            width: 70,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Anyo_ISO",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Anyo_ISO", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Semana ISO",
            width: 90,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Semana_ISO",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Semana_ISO", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Año",
            width: 40,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Anyo",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Anyo", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Mes",
            width: 40,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Mes",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Mes", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Fecha",
            width: 80,
            dataType: "date",
            editable: false,
            dataIndx: "Fecha_Usuario",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Fecha_Usuario", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Calidad del Trabajo",
            width: 110,
            dataType: "text",
            editable: false,
            dataIndx: "Med_Calidad",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Calidad" ]) == -1) {
                    if (parseInt(rowData["Automatica"]) == 0) {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Esperando evaluación de calidad por parte de un evaluador."> </center>';
                    } else {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Esperando evaluación automática."> </center> ';
                    }
                } else {
                    if (parseInt(rowData[ "Med_Calidad" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Calidad" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Calidad" ] + "</span></font></center>";
                    }
                }
            },
            editor: {
                type: 'select',
                options: function (ui) {
                    return valores;
                }
            },
            filter: {type: "select",
                condition: 'equal',
                prepend: {'': '--Todo--'},
                listeners: ['change'],
                options: notas
            }
        },
        {
            title: "Enfoque al Cliente",
            width: 100,
            dataType: "text",
            editable: true,
            hidden: true,
            dataIndx: "Med_Enfoque_Cliente",
            editor: {
                type: 'select',
                options: function (ui) {
                    return valores;
                }
            },
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Enfoque_Cliente" ]) == -1) {
                    if (parseInt(rowData["Automatica"]) == 0) {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/finger.png" title="Pincha aquí para valorar."> </center>';
                    } else {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Esperando evaluación automática."> </center> ';
                    }
                } else {
                    if (parseInt(rowData[ "Med_Enfoque_Cliente" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Enfoque_Cliente" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_Enfoque_Cliente" ] + "</span></center>";
                    }
                }
            },
            filter: {type: "select",
                condition: 'equal',
                prepend: {'': '--Todo--'},
                listeners: ['change'],
                options: notas
            }
        },
        {
            title: "Fechas",
            width: 80,
            dataType: "text",
            editable: false,
            dataIndx: "Med_En_Tiempo",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_En_Tiempo" ]) == -1) {
                    if (parseInt(rowData["Automatica"]) == 0) {
                        return ' <center> </center>';
                    } else {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Esperando evaluación automática."> </center> ';
                    }
                } else {
                    if (parseInt(rowData[ "Med_En_Tiempo" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_En_Tiempo" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_En_Tiempo" ] + "</span></center>";
                    }
                }
            },
            filter: {type: "select",
                condition: 'equal',
                prepend: {'': '--Todo--'},
                listeners: ['change'],
                options: notas
            }
        },
        {
            title: "Rapidez / Estándar",
            width: 110,
            dataType: "text",
            editable: false,
            dataIndx: "Med_Eficiencia",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Eficiencia" ]) == -1) {
                    if (parseInt(rowData["Automatica"]) == 0) {
                        return ' <center> </center>';
                    } else {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Esperando evaluación automática."> </center> ';
                    }
                } else {
                    if (parseInt(rowData[ "Med_Eficiencia" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Eficiencia" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_Eficiencia" ] + "</span></center>";
                    }
                }
            },
            filter: {type: "select",
                condition: 'equal',
                prepend: {'': '--Todo--'},
                listeners: ['change'],
                options: notas
            }
        },
        {
            title: "Nota Evaluador",
            width: 90,
            dataType: "text",
            editable: false,
            dataIndx: "Med_Evaluador",
            editor: {
                type: 'select',
                options: function (ui) {
                    return valores;
                }
            },
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Evaluador" ]) == -1) {
                    return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Su evaluador no ha valorado la tarea."> </center>';
                } else {
                    if (parseInt(rowData[ "Med_Evaluador" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Evaluador" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_Evaluador" ] + "</span></center>";
                    }
                }
            },
            filter: {type: "select",
                condition: 'equal',
                prepend: {'': '--Todo--'},
                listeners: ['change'],
                options: notas
            }
        },
        {
            title: "Fecha Evaluación",
            width: 100,
            dataType: "date",
            editable: false,
            dataIndx: "Fecha_Evaluador",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Fecha_Evaluador", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Evaluado por",
            width: 150,
            dataType: "text",
            editable: false,
            dataIndx: "Evaluador_Nombre",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Evaluador_Nombre", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Comentario Evaluador",
            width: 150,
            dataType: "text",
            editable: false,
            dataIndx: "Comentario_Evaluador",
            render: function (ui) {
                var rowData = ui.rowData;
                return "<b><font color='red'>" + rowData["Comentario_Evaluador"] + '</font></b>';
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Comentario_Evaluador", $(this).val());
                        }
                    }]
            }
        },
    ];

    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 1000,
        sortIndx: ["Anyo_ISO", "Semana_ISO"],
        sortDir: ["down", "down"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50, 100],
        url: "select/consultaEvaluaciones.php?usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=-1",
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    obj.render = function (evt, obj) { }; // ???

    // Seleccionar la fila
    obj.rowSelect = function (evt, obj) {
        ID_EVALUACION_SELECCIONADA = obj.rowData["Id"];
    };
    // Seleccionar la fila
    obj.cellSelect = function (evt, obj) {
        ID_EVALUACION_SELECCIONADA = obj.rowData["Id"];
    };
    // 
    obj.cellClick = function (evt, obj) {
        ID_EVALUACION_SELECCIONADA = obj.rowData["Id"];
    }
    // Doble Click
    obj.cellDblClick = function (evt, obj) {
        // ID_EVALUACION_SELECCIONADA = obj.rowData["Id"];
        ID_SELECCIONADA = obj.rowData["Tarea"];
        cambia_menu(3);
    };

    var $gridEvaUsuarios = $("#grid_array_evaluacion_usuarios").pqGrid(obj);

    $gridEvaUsuarios.pqGrid("option", "topVisible", true);
    $gridEvaUsuarios.pqGrid("option", "showTitle", true);
    $gridEvaUsuarios.pqGrid("option", "collapsible", false);
    $gridEvaUsuarios.pqGrid("option", "columnBorders", true);
    $gridEvaUsuarios.pqGrid("option", "rowBorders", true);
    $gridEvaUsuarios.pqGrid("option", "oddRowsHighlight", true);
    $gridEvaUsuarios.pqGrid("option", "numberCell", false);
    $gridEvaUsuarios.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });

    function cellSave(evt, ui) {

        var ID = ui.rowData["Id"];
        var Med_Calidad = ui.rowData["Med_Calidad"];
        var Med_Enfoque_Cliente = ui.rowData["Med_Enfoque_Cliente"];
        var Med_Evaluador = ui.rowData["Med_Evaluador"];
        var campo_a_cambiar = '';

        if (ui.column.dataIndx == 'Med_Calidad') {
            campo_a_cambiar = 'Med_Calidad'
        }

        if (ui.column.dataIndx == 'Med_Enfoque_Cliente') {
            campo_a_cambiar = 'Med_Enfoque_Cliente'
        }

        if (ui.column.dataIndx == 'Med_Evaluador') {
            campo_a_cambiar = 'Med_Evaluador'
        }

        if (campo_a_cambiar != '') {
            var url = "select/modificarEvaluacionTarea.php?Id=" + ID + "&Med_Enfoque_Cliente=" + Med_Enfoque_Cliente + "&Med_Calidad=" + Med_Calidad + "&Med_Evaluador=" + Med_Evaluador + "&usuario=" + encodeURIComponent(usuariovalidado) + "&campo_a_cambiar=" + encodeURIComponent(campo_a_cambiar);

            $.ajax({
                url: url
            }).done(function (data) {
                if (data != '') {
                    mensaje("Evaluaciones", data);
                }
                $gridEvaUsuarios.pqGrid("refreshDataAndView");
            });
        } else {
            $gridEvaUsuarios.pqGrid("refreshDataAndView");
        }
    }

    function filter(dataIndx, value) {
        $gridEvaUsuarios.pqGrid("filter", {
            data: [{
                    dataIndx: dataIndx,
                    value: value
                }]
        });
    }
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * date 2017/06/08
 */
function CargaGridEvaluaciones() {



    var valores = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

    var obj = {
        width: '100%',
        // height: '100%',
        title: "Lista de Evaluaciones",
        hoverMode: 'cell',
        selectionModel: {
            type: 'cell',
            mode: 'single'},
        editModel: {
            clicksToEdit: 1,
            saveKey: '13',
            onSave: null
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        },
        cellSave: function (event, ui) {
            cellSave(event, ui);
        },
        // change: function( event, ui ) { cellSave(event,ui); }
    };

    obj.colModel = [
        {
            title: "Id",
            width: 70,
            hidden: true,
            dataIndx: "Id",
        },
        {
            title: "Asignado",
            width: 150,
            dataType: "string",
            editable: false,
            hidden: false,
            dataIndx: "Usuario_Nombre"
        },
        {
            title: "Evaluación",
            width: 70,
            dataType: "string",
            editable: false,
            hidden: false,
            dataIndx: "Automatica",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData["Automatica"]) == 0) {
                    return "PARCIAL";
                } else {
                    return "FINAL";
                }
            }
        },
        {
            title: "Año ISO",
            width: 80,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Anyo_ISO"
        },
        {
            title: "Semana ISO",
            width: 90,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Semana_ISO"
        },
        {
            title: "Año",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Anyo"
        },
        {
            title: "Mes",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Mes"
        },
        {
            title: "Fecha",
            width: 80,
            dataType: "date",
            editable: false,
            dataIndx: "Fecha_Usuario",
        },
        {
            title: "Calidad del Trabajo",
            width: 110,
            dataType: "text",
            editable: false,
            dataIndx: "Med_Calidad",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Calidad" ]) == -1) {
                    if (parseInt(rowData["Automatica"]) == 0) {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Esperando evaluación de calidad por parte de un evaluador."> </center>';
                    } else {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Esperando evaluación automática."> </center> ';
                    }
                } else {
                    if (parseInt(rowData[ "Med_Calidad" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Calidad" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Calidad" ] + "</span></font></center>";
                    }
                }
            },
            editor: {
                type: 'select',
                options: function (ui) {
                    return valores;
                }
            },
        },
        {
            title: "Enfoque al Cliente",
            width: 100,
            dataType: "text",
            editable: true,
            hidden: true,
            dataIndx: "Med_Enfoque_Cliente",
            editor: {
                type: 'select',
                options: function (ui) {
                    return valores;
                }
            },
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Enfoque_Cliente" ]) == -1) {
                    if (parseInt(rowData["Automatica"]) == 0) {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/finger.png" title="Pincha aquí para valorar."> </center>';
                    } else {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Esperando evaluación automática."> </center> ';
                    }
                } else {
                    if (parseInt(rowData[ "Med_Enfoque_Cliente" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Enfoque_Cliente" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_Enfoque_Cliente" ] + "</span></center>";
                    }
                }
            },
        },
        {
            title: "Fechas",
            width: 80,
            dataType: "text",
            editable: false,
            dataIndx: "Med_En_Tiempo",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_En_Tiempo" ]) == -1) {
                    if (parseInt(rowData["Automatica"]) == 0) {
                        return ' <center> </center>';
                    } else {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Esperando evaluación automática."> </center> ';
                    }
                } else {
                    if (parseInt(rowData[ "Med_En_Tiempo" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_En_Tiempo" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_En_Tiempo" ] + "</span></center>";
                    }
                }
            },
        },
        {
            title: "Rapidez / Estándar",
            width: 80,
            dataType: "text",
            editable: false,
            dataIndx: "Med_Eficiencia",
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Eficiencia" ]) == -1) {
                    if (parseInt(rowData["Automatica"]) == 0) {
                        return ' <center> </center>';
                    } else {
                        return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Esperando evaluación automática."> </center> ';
                    }
                } else {
                    if (parseInt(rowData[ "Med_Eficiencia" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Eficiencia" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_Eficiencia" ] + "</span></center>";
                    }
                }
            },
        },
        {
            title: "Nota Evaluador",
            width: 90,
            dataType: "text",
            editable: false,
            dataIndx: "Med_Evaluador",
            editor: {
                type: 'select',
                options: function (ui) {
                    return valores;
                }
            },
            render: function (ui) {
                var rowData = ui.rowData;
                if (parseInt(rowData[ "Med_Evaluador" ]) == -1) {
                    return ' <center><img class="imagenes_pequenyas" src="./imagenes/waiting.png" title="Su evaluador no ha valorado la tarea."> </center>';
                } else {
                    if (parseInt(rowData[ "Med_Evaluador" ]) < 7) {
                        return "<center><span style='text-shadow: 2px 2px silver; color:red; font-size:medium; font-weight: 700'; >" + rowData[ "Med_Evaluador" ] + "</span></center>";
                    } else {
                        return "<center><span style='text-shadow: 2px 2px silver; color:green; font-size:medium; font-weight: 700';>" + rowData[ "Med_Evaluador" ] + "</span></center>";
                    }
                }
            }
        },
        {
            title: "Fecha Evaluación",
            width: 100,
            dataType: "date",
            editable: false,
            dataIndx: "Fecha_Evaluador"
        },
        {
            title: "Evaluado por",
            width: 150,
            dataType: "text",
            editable: false,
            dataIndx: "Evaluador_Nombre"
        },
        {
            title: "Comentario Evaluador",
            width: 150,
            dataType: "text",
            editable: false,
            dataIndx: "Comentario_Evaluador",
            render: function (ui) {
                var rowData = ui.rowData;
                return "<b><font color='red'>" + rowData["Comentario_Evaluador"] + '</font></b>';
            }
        },
    ];

    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Anyo_ISO", "Semana_ISO"],
        sortDir: ["down", "down"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],
        url: "select/consultaEvaluaciones.php?usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=" + ID_SELECCIONADA,
        getData: function (dataJSON, textStatus, jqXHR) {


            var data = dataJSON.data;
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    obj.render = function (evt, obj) {
        if (evaluador == 1 && externo == '') {
            var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));
            $("<span>Acceso Evaluadores</span>").appendTo($toolbar).button({icons: {primary: "	ui-icon-star"}}).click(function (evt) {
                cambia_menu(13);
            });
            // $("<span>Borrar</span>").appendTo($toolbar).button({ icons: { primary: "ui-icon-circle-minus" } }).click(function(evt) { closeRow(); });
        }
    };

    // Seleccionar la fila
    obj.rowSelect = function (evt, obj) {
        ID_EVALUACION_SELECCIONADA = obj.rowData["Id"];
    };
    // Seleccionar la fila
    obj.cellSelect = function (evt, obj) {
        ID_EVALUACION_SELECCIONADA = obj.rowData["Id"];
    };
    // 
    obj.cellClick = function (evt, obj) {
        ID_EVALUACION_SELECCIONADA = obj.rowData["Id"];
    }
    // Doble Click
    obj.cellDblClick = function (evt, obj) {
        ID_EVALUACION_SELECCIONADA = obj.rowData["Id"];
    };

    var $grid = $("#grid_array_evaluaciones_plegar").pqGrid(obj);

    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", false);
    $grid.pqGrid("option", "collapsible", false);
    $grid.pqGrid("option", "columnBorders", true);
    $grid.pqGrid("option", "rowBorders", true);
    $grid.pqGrid("option", "oddRowsHighlight", true);
    $grid.pqGrid("option", "numberCell", false);
    $grid.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });


    function cellSave(evt, ui) {

        var ID = ui.rowData["Id"];
        var Med_Calidad = ui.rowData["Med_Calidad"];
        var Med_Enfoque_Cliente = ui.rowData["Med_Enfoque_Cliente"];
        var Med_Evaluador = ui.rowData["Med_Evaluador"];
        var campo_a_cambiar = '';

        if (ui.column.dataIndx == 'Med_Calidad') {
            campo_a_cambiar = 'Med_Calidad'
        }

        if (ui.column.dataIndx == 'Med_Enfoque_Cliente') {
            campo_a_cambiar = 'Med_Enfoque_Cliente'
        }

        if (ui.column.dataIndx == 'Med_Evaluador') {
            campo_a_cambiar = 'Med_Evaluador'
        }

        if (campo_a_cambiar != '') {
            var url = "select/modificarEvaluacionTarea.php?Id=" + ID + "&Med_Enfoque_Cliente=" + Med_Enfoque_Cliente + "&Med_Calidad=" + Med_Calidad + "&Med_Evaluador=" + Med_Evaluador + "&usuario=" + encodeURIComponent(usuariovalidado) + "&campo_a_cambiar=" + encodeURIComponent(campo_a_cambiar);
            $.ajax({
                url: url
            }).done(function (data) {
                if (data != '') {
                    mensaje("Evaluaciones", data);
                }
                $("#grid_array_evaluaciones_plegar").pqGrid("refreshDataAndView");
                refresh_tools();
            });
        } else {
            $("#grid_array_evaluaciones_plegar").pqGrid("refreshDataAndView");
        }
    }


    // $grid.on("pqgridcellsave", function(evt, ui) {});



}




function CargaGridCostes() {
    var tipos = [];
    $.ajax({
        url: "select/getTiposCostes.php",
        success: function (response) {
            tipos = response.split(",");
        }
    });
    var dateEditor = function (ui) {
        var $cell = ui.$cell,
                rowData = ui.rowData,
                dataIndx = ui.dataIndx,
                cls = ui.cls,
                dc = $.trim(rowData[dataIndx]);
        $cell.css('padding', '0');
        var $inp = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' />").appendTo($cell).val(dc).datetimepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: "dd/mm/yy",
            onClose: function () {
                $inp.focus();
            }
        });
    }

    var dateEditor2 = function (ui) {
        var $cell = ui.$cell;
        var dataIndx = ui.dataIndx;
        var rowData = ui.rowData;
        var cls = ui.cls;
        var dc = $.trim(rowData[dataIndx]);
        var input = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor'  autocomplete='off' />")
                .appendTo($cell)
                .val(dc).datetimepicker({
            datepicker: true,
            timepicker: true,
            format: 'd/m/Y H:i:s',
            minTime: '07:00:00',
            weeks: false,
            step: 15,
            dayOfWeekStart: 1,
        });
    }

    var obj = {
        width: '100%',
        height: '100%',
        title: "Lista de Costes",
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
        editModel: {
            saveKey: '13'
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        }
    };

    obj.colModel = [{
            title: "Numero",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Numero"
        },
        {
            title: "Tarea",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Tarea"
        },
        {
            title: "Dia Inicio",
            width: 100,
            dataType: "date",
            dataIndx: "Dia Inicio",
            editor: {
                type: dateEditor2
            },
            validations: [{
                    type: 'regexp',
                    value: '[0-9]{2}/[0-9]{2}/[0-9]{4}',
                    msg: 'No en dd/mm/yyyy formato'
                }]
        },
        {
            title: "Hora Inicio",
            width: 100,
            dataType: "text",
            editable: false,
            dataIndx: "Hora Inicio"
        },
        {
            title: "Dia Fin",
            width: 100,
            dataType: "date",
            dataIndx: "Dia Fin",
            editor: {
                type: dateEditor2
            },
            validations: [{
                    type: 'regexp',
                    value: '[0-9]{2}/[0-9]{2}/[0-9]{4}',
                    msg: 'No en dd/mm/yyyy formato'
                }]
        },
        {
            title: "Hora Fin",
            width: 100,
            dataType: "text",
            editable: false,
            dataIndx: "Hora Fin"
        },
        {
            title: "Usuario",
            width: 145,
            dataType: "text",
            editable: false,
            dataIndx: "Usuario"
        },
        {
            title: "Estado",
            width: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Estado"
        },
        {
            title: "Comentario",
            width: 250,
            dataType: "text",
            editable: true,
            dataIndx: "Comentario"
        },
        {
            title: "Tipo",
            width: 150,
            dataType: "text",
            editable: true,
            dataIndx: "Tipo",
            editor: {
                type: 'select',
                options: function (ui) {
                    return tipos;
                }
            },
            validations: [{
                    type: 'minLen',
                    value: 1,
                    msg: "Required"
                }]
        },
        {
            title: "Importe",
            width: 50,
            dataType: "float",
            editable: true,
            dataIndx: "Importe"
        },
        {
            title: "Inicio",
            width: 250,
            dataType: "text",
            editable: false,
            hidden: true,
            dataIndx: "Inicio"
        }
    ];

    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Inicio"],
        sortDir: ["down"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],
        url: "select/consultaCostes.php?usuario=" + encodeURIComponent(nombreusuariovalidado) + "&tarea=" + ID_SELECCIONADA,
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));
        $("<span>Nuevo</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-plus"
            }
        }).click(function (evt) {
            initRow();
        });
        $("<span>Borrar</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-minus"
            }
        }).click(function (evt) {
            closeRow();
        });
    };

    // Seleccionar la fila
    obj.rowSelect = function (evt, obj) {
        ID_COSTE_SELECCIONADA = obj.rowData["Numero"];
    };
    // Seleccionar la fila
    obj.cellSelect = function (evt, obj) {
        ID_COSTE_SELECCIONADA = obj.rowData["Numero"];
    };
    // Doble Click
    obj.cellDblClick = function (evt, obj) {
        ID_COSTE_SELECCIONADA = obj.rowData["Numero"];
    };

    var $grid = $("#grid_array_costes_plegar").pqGrid(obj);

    $grid.pqGrid("option", "topVisible", true);
    $grid.pqGrid("option", "showTitle", false);
    $grid.pqGrid("option", "collapsible", false);
    $grid.pqGrid("option", "columnBorders", true);
    $grid.pqGrid("option", "rowBorders", true);
    $grid.pqGrid("option", "oddRowsHighlight", true);
    $grid.pqGrid("option", "numberCell", true);
    $grid.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });

    $grid.on("pqgridcellsave", function (evt, ui) {
        var ID = ui.rowData["Numero"];
        var TITULO = ui.rowData["Comentario"];
        var IMPORTE = ui.rowData["Importe"];
        var DIA_INICIO = ui.rowData["Dia Inicio"];
        var DIA_FIN = ui.rowData["Dia Fin"];
        var HORA_INICIO = ui.rowData["Hora Inicio"];
        var HORA_FIN = ui.rowData["Hora Fin"];
        var TIPO = ui.rowData["Tipo"];
        var url = "select/modificarCostesTarea.php?Id=" + ID + "&diainicio=" + DIA_INICIO + "&Importe=" + IMPORTE + "&diafin=" + DIA_FIN + "&horainicio=" + HORA_INICIO + "&horafin=" + HORA_FIN + "&Titulo=" + encodeURIComponent(TITULO) + "&Tipo=" + encodeURIComponent(TIPO) + "&usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=" + ID_SELECCIONADA;

        $.ajax({
            url: url
        }).done(function () {
            $("#grid_array_costes_plegar").pqGrid("refreshDataAndView");
            refresh_tools();
        });
    });

    function initRow() {
        if (ID_SELECCIONADA != '-1') {
            iniciotareacoste();
        }
    }

    function closeRow() {
        cierrotareacoste();
    }

    function getRowIndx() {
        var arr = $grid.pqGrid("selection", {
            type: 'row',
            method: 'getSelection'
        });
        if (arr && arr.length > 0) {
            var rowIndx = arr[0].rowIndx;
            return rowIndx;
        } else {
            return null;
        }
    }

}

function iniciotareacoste() {
    var ajax = nuevoAjax();
    var lista;
    ajax.open("POST", "select/iniciar_tarea_coste.php?tarea=" + ID_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado), false);
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {
            $("#grid_array_costes_plegar").pqGrid("refreshDataAndView");
            refresh_tools();
        }
    }
    ajax.send(null);
}

function cierrotareacoste() {
    var ajax = nuevoAjax();
    var lista;
    ajax.open("POST", "select/cerrar_tarea_coste.php?tarea_coste=" + ID_COSTE_SELECCIONADA + "&usuario=" + encodeURIComponent(usuariovalidado), false);
    ajax.onreadystatechange = function () {
        if (ajax.readyState != 4) {
            // NO ESTA LISTO!!!!!!!!!!
        } else {
            $("#grid_array_costes_plegar").pqGrid("refreshDataAndView");
            refresh_tools();
        }
    }
    ajax.send(null);
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 15/12/2020
 * @description muestra una ventana con información de la tarea
 * @param tarea
 */
function pop_up_InformacionTarea(tarea) {

    var pantallaInfoTarea = "";
    pantallaInfoTarea += '<div class="POPUP-DIALOG" id="miseccioncomentariostarea" style="display: none;width:100%;overflow: hidden;">';
    pantallaInfoTarea += '		<table border="0" width="100%" style="table-layout: fixed;">';
    pantallaInfoTarea += '			<tr>';
    pantallaInfoTarea += '				<td>';
    pantallaInfoTarea += '					<div class="POPUP-TITLE-SMALL" id="POPUP-TOP-TITLE-COMENTARIOS">Datos tarea</div>';
    pantallaInfoTarea += '					<div class="POPUP-DATOS-TAREA" id="informacionDatosTarea"></div>';
    pantallaInfoTarea += '				</td>';
    pantallaInfoTarea += '			</tr>';
    pantallaInfoTarea += '			<tr>';
    pantallaInfoTarea += '				<td>';
    pantallaInfoTarea += '					<div class="POPUP-TITLE-SMALL" id="POPUP-TOP-TITLE-COMENTARIOS">Observaciones</div>';
    pantallaInfoTarea += '					<div class="POPUP-OBSERVACIONES" id="observacionesTarea"></div>';
    pantallaInfoTarea += '				</td>';
    pantallaInfoTarea += '			</tr>';
    pantallaInfoTarea += '			<tr>';
    pantallaInfoTarea += '				<td>';
    pantallaInfoTarea += '					<div class="POPUP-TITLE-SMALL" >Documentos adjuntos</div>';
    pantallaInfoTarea += '					<div class="POPUP-DATOS-TAREA-ADJUNTOS" id="informacionDatosTareaAdjuntos"></div>';
    pantallaInfoTarea += '				</td>';
    pantallaInfoTarea += '			</tr>	';
    pantallaInfoTarea += '		</table>';
    pantallaInfoTarea += '	</div>';

    var dialogo = document.getElementById('dialog-cancelar');
    dialogo.innerHTML = pantallaInfoTarea;

    var titulo = "Información de la tarea";
    $.ajax({
        url: "visibilidad/obtener_titulo.php",
        type: "POST",
        async: false,
        data: {id: tarea},
        success: function (data) {
            if (data != '') {
                titulo = "TAREA " + data;
            }
        }
    });

    $("#dialog-cancelar").dialog({
        resizable: false,
        title: titulo,
        height: 700,
        width: 1100,
        dialogClass: 'no-close',
        modal: true,
        open: function (event, ui) {

            $.ajax({
                url: "select/obtener_observaciones.php",
                type: "POST",
                data: {id: tarea},
                success: function (data) {
                    var arr = data.split('@@@');
                    $('#observacionesTarea').html(arr[0]);
                    $('#informacionDatosTarea').html(arr[1]);
                    $('#informacionDatosTareaAdjuntos').html(arr[2]);
                }
            });

            $('#miseccioncomentariostarea').css('display', 'block');

        },
        buttons: {
            "Cerrar": function () {
                $(this).dialog("close");
            }
        }
    });

}
/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 31/07/2017
 * @description muestra una ventana con los comentarios de la tarea
 * @param tarea
 */
function pop_up_Comentarios(tarea, periodo, fechaCreacion, usuarioEvaluado) {

    if ($("#gridComentariosTarea").html().length > 0) {
        $("#gridComentariosTarea").pqGrid("destroy");
        $("#gridComentariosTarea").empty();
    }

    if ($("#gridComentariosFichasTarea").html().length > 0) {
        $("#gridComentariosFichasTarea").pqGrid("destroy");
        $("#gridComentariosFichasTarea").empty();
    }

    $.ajax({
        url: "visibilidad/obtener_titulo.php",
        type: "POST",
        data: {id: tarea},
        success: function (data) {
            if (data != '') {
                $("#POPUP-TOP-TITLE-COMENTARIOS").html("TAREA " + data);
            }
        }
    });
    $.ajax({
        url: "select/obtener_observaciones.php",
        type: "POST",
        data: {id: tarea},
        success: function (data) {
            $('#observacionesTarea').html(data);
        }
    });

    $('#miseccioncomentariostarea').css('display', 'block');
    CargaGridComentarios(tarea, 'gridComentariosTarea');
    CargaGridHorasEvaluacion(tarea, periodo, fechaCreacion, usuarioEvaluado);

}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 15/09/2017
 * @description muestra una ventana con los colaboradores de la tarea
 * @param tarea
 */
function pop_up_Colaborador(tarea) {


    if ($("#gridColaboradorTarea").html().length > 0) {
        $("#gridColaboradorTarea").pqGrid("destroy");
        $("#gridColaboradorTarea").empty();
    }

    $.ajax({
        url: "visibilidad/obtener_titulo.php",
        type: "POST",
        data: {id: tarea},
        success: function (data) {
            if (data != '') {
                $("#POPUP-TOP-TITLE-COLABORA").html("COLABORADORES: " + data);
            }
        }
    });


    $('#miseccioncolaboradortarea').css('display', 'block');
    CargaGridColaboradores(tarea);

}


/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 15/09/2017
 * @description carga el grid de los colaboradores de la tarea
 * @param tarea
 */
function CargaGridColaboradores(tarea) {

    var target = document.getElementById('micuerpo');
    var spinner = new Spinner(opts).spin(target);

    var ID_COLABORADOR = -1;
    var asignados = [];
    $.ajax({
        cache: false,
        url: "select/getUsuariosOficina.php?&id_pda=" + tarea + "&todos='si'",
        success: function (response) {
            asignados = response.split("|");
        }
    });



    var accesos = [];
    $.ajax({
        url: "select/getTiposTipos.php?tipo=105&orden=C",
        success: function (response) {
            accesos = response.split(",");
        }
    });

    var AporteOrganizacion = [];
    $.ajax({
        url: "select/getTiposTipos.php?tipo=102&id=" + tarea + "&orden=C",
        success: function (response) {
            AporteOrganizacion = response.split(",");
        }
    });

    var AporteEmpresa = [];
    $.ajax({
        url: "select/getTiposTipos.php?tipo=103&id=" + tarea + "&orden=C",
        success: function (response) {
            AporteEmpresa = response.split(",");
        }
    });

    var AporteJefe = [];
    $.ajax({
        url: "select/getTiposTipos.php?tipo=104&id=" + tarea + "&orden=C",
        success: function (response) {
            AporteJefe = response.split(",");
        }
    });

    // CREAMOS EL OBJETO GRID Y LE DAMOS LOS PARAMETROS GENERALES
    var obj = {
        height: '559',
        // height : '100%' ,
        width: '100%',
        hoverMode: 'row',
        selectionModel: {
            type: 'cell'
        },
        scrollModel: {
            autoFit: true
        },
        editModel: {
            clicksToEdit: 2,
            saveKey: '13',
            onSave: null
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        }
    };

    // A CONTINUACIÓN AL OBJETO GRID LE DEFINIMOS LAS COLUMNAS (colModel)
    obj.colModel = [{
            title: "Id",
            width: 40,
            dataType: "string",
            dataIndx: "Id",
            editable: false,
            hidden: false
        }, {
            title: "Colaborador",
            width: 110,
            dataType: "string",
            dataIndx: "Colaborador_Nombre",
            editable: true,
            hidden: false,
            editor: {
                type: 'select',
                options: function (ui) {
                    return asignados;
                }
            }
        },
        {
            title: "Fecha",
            width: 50,
            dataType: "date",
            dataIndx: "FechaAlta",
            editable: false
        }, {
            title: "Acceso",
            width: 40,
            dataType: "string",
            dataIndx: "Acceso",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    return accesos;
                }
            }
        }, {
            title: "Aporte Propio",
            width: 40,
            dataType: "string",
            dataIndx: "AporteOrganizacion",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    return AporteOrganizacion;
                }
            }
        }, {
            title: "Aporte Empresa",
            width: 40,
            dataType: "string",
            dataIndx: "AporteEmpresa",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    return AporteEmpresa;
                }
            }
        }, {
            title: "Aporte Jefe",
            width: 40,
            dataType: "string",
            dataIndx: "AporteJefe",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    return AporteJefe;
                }
            }
        }, {
            title: "Funciones",
            width: 700,
            dataType: "string",
            dataIndx: "Funciones",
            editable: true,
            hidden: false
        }

    ];

    // BARRA DE TAREAS
    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

        // BOTON NUEVO COLABORADOR
        $("<span>Añadir Colaborador</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-plus"
            }
        }).click(function (evt) {
            insertar_colaborador( );
        });

        // BOTON ELIMINAR COLABORADOR
        $("<span>Borrar Colaborador</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-minus"
            }
        }).click(function (evt) {
            borrar_colaborador( );
        });

    };

    // HACEMOS CONSULTA PARA RECIBIR EL ARRAY JSON Y ASIGNARLO A data
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        rPP: 20,
        sortIndx: ["Colaborador_Nombre"],
        sortDir: ["up"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],

        curPage: 1,
        url: "select/consultaColaboradorTarea.php?tarea=" + tarea + "&usuario=" + encodeURIComponent(usuariovalidado),
        getData: function (dataJSON, textStatus, jqXHR) {
            spinner.stop( );
            var data = dataJSON.data;
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    // EVENTOS
    obj.rowSelect = function (evt, obj) {
        ID_COLABORADOR = obj.rowData["Id"];
    };
    obj.cellSelect = function (evt, obj) {
        ID_COLABORADOR = obj.rowData["Id"];
    };
    obj.cellDblClick = function (evt, obj) {
        ID_COLABORADOR = obj.rowData["Id"];
    };


    // INICIALIZACION
    var $grid = $("#gridColaboradorTarea").pqGrid(obj);

    // OPCIONES DEL GRID
    $grid.pqGrid("option", "topVisible", false);
    $grid.pqGrid("option", "showTitle", false);
    $grid.pqGrid("option", "showBottom", true);
    $grid.pqGrid("option", "columnBorders", true);
    $grid.pqGrid("option", "rowBorders", true);
    $grid.pqGrid("option", "oddRowsHighlight", true);
    $grid.pqGrid("option", "scrollModel", {
        horizontal: false
    }, {
        vertical: true
    });
    $grid.pqGrid("option", "collapsible.on", false);

    // GRABAR MODIFICACIONES
    $grid.on("pqgridcellsave", function (evt, ui) {

        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);

        var Campo = ui.dataIndx;
        var Id = ui.rowData[ "Id" ];
        var Colaborador_Nombre = ui.rowData[ "Colaborador_Nombre" ];
        var Funciones = ui.rowData[ "Funciones" ];
        var Acceso = ui.rowData[ "Acceso" ];
        var AporteOrganizacion = ui.rowData[ "AporteOrganizacion" ];
        var AporteEmpresa = ui.rowData[ "AporteEmpresa" ];
        var AporteJefe = ui.rowData[ "AporteJefe" ];

        var url = "select/modificar_colaborador.php?usuario=" + encodeURIComponent(usuariovalidado) + "&Id=" + Id + "&Id_Tarea=" + ID_SELECCIONADA + "&Colaborador_Nombre=" + encodeURIComponent(Colaborador_Nombre) + "&Funciones=" + encodeURIComponent(Funciones) + "&Acceso=" + encodeURIComponent(Acceso) + "&AporteOrganizacion=" + AporteOrganizacion + "&AporteEmpresa=" + AporteEmpresa + "&AporteJefe=" + AporteJefe + "&Campo=" + Campo;

        // alert(Id + " --> " + Colaborador_Nombre);

        $.ajax({
            cache: false,
            async: false,
            url: url
        }).done(function () {
            spinner.stop( );
            Id = -1;
            $grid.pqGrid("refreshDataAndView");
        });

    });

    // AÑADE UNA NUEVA LINEA DE COLABORADOR
    function insertar_colaborador() {

        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);
        // alertame( "FILA: " + id + "\n\nUSUARIO: " + usuariovalidado + "\n\nID PDA: " + ID_SELECCIONADA );
        var url = "select/insertar_colaborador.php?usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=" + tarea;
        $.ajax({
            cache: false,
            url: url
        }).done(function () {
            spinner.stop( );
            $grid.pqGrid("refreshDataAndView");
        });
    }
    // BORRA UNA LINEA DE COLABORADOR
    function borrar_colaborador() {

        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);
        var url = "select/borrar_colaborador.php?usuario=" + encodeURIComponent(usuariovalidado) + "&Id=" + ID_COLABORADOR;
        $.ajax({
            cache: false,
            url: url
        }).done(function () {
            spinner.stop( );
            $grid.pqGrid("refreshDataAndView");
        });


    }


    // ************************************GRID
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 18/07/2017
 * @description muestra una ventana con las planificaciones de la tarea
 * @param tarea
 */
function pop_up_Planificador(tarea, tipo) {


    if ($("#gridPlanificacionTarea").html().length > 0) {
        $("#gridPlanificacionTarea").pqGrid("destroy");
        $("#gridPlanificacionTarea").empty();
    }

    $.ajax({
        url: "visibilidad/obtener_titulo.php",
        type: "POST",
        data: {id: tarea},
        success: function (data) {
            if (data != '') {
                $("#POPUP-TOP-TITLE").html("PLANIFICACIÓN: " + data);
            }
        }
    });

    $('#miseccionplanificadortarea').css('display', 'block');
    CargaGridPlanificaciones(tarea, tipo);

}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 18/07/2017
 * @description carga el grid de planificacion de la tarea
 * @param tarea
 */
function CargaGridPlanificaciones(tarea, tipo) {

    var target = document.getElementById('micuerpo');
    var spinner = new Spinner(opts).spin(target);

    var ID_PLANIFICACION = -1;

    var asignados = [];
    $.ajax({
        cache: false,
        url: "select/getUsuariosOficina.php?&id_pda=" + tarea,
        success: function (response) {
            asignados = response.split("|");
        }
    });

    var prioridades = ['Normal', 'Alta'];

    var situaciones = [];
    $.ajax({
        url: "select/getTiposTipos.php?tipo=13&id=" + tarea,
        success: function (response) {
            situaciones = response.split(",");
        }
    });

    var tipoplanificada = [];
    $.ajax({
        url: "select/getTiposTipos.php?tipo=16&oficina=TI",
        cache: false,
        async: false,
        success: function (response) {
            tipoplanificada = response.split(",");
        }
    });

    var dateEditor = function (ui) {
        var $cell = ui.$cell,
                rowData = ui.rowData,
                dataIndx = ui.dataIndx,
                cls = ui.cls,
                dc = $.trim(rowData[dataIndx]);
        $cell.css('padding', '0');
        var $inp = $("<input  type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' />")
                .appendTo($cell)
                .val(dc).datepicker({
            changeMonth: true,
            changeYear: true,
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            firstDay: 1,
            dateFormat: "dd/mm/yy",
            onClose: function () {
                $inp.focus();
            }
        });
    }

    var dateEditor2 = function (ui) {
        var $cell = ui.$cell;
        var dataIndx = ui.dataIndx;
        var rowData = ui.rowData;
        var cls = ui.cls;
        var dc = $.trim(rowData[dataIndx]);
        var input = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor'  autocomplete='off' />")
                .appendTo($cell)
                .val(dc).datetimepicker({
            datepicker: true,
            timepicker: false,
            format: 'd/m/Y H:i:s',
            minTime: '07:00:00',
            weeks: true,
            step: 15,
            dayOfWeekStart: 1,
        });
    }

    // CREAMOS EL OBJETO GRID Y LE DAMOS LOS PARAMETROS GENERALES
    var obj = {
        height: '559',
        width: '100%',
        hoverMode: 'row',
        selectionModel: {
            type: 'cell'
        },
        scrollModel: {
            autoFit: true
        },
        editModel: {
            clicksToEdit: 2,
            saveKey: '13',
            onSave: null
        },
        pageModel: {
            type: 'remote',
            rPP: 20,
            strRpp: "{0}"
        },
        cellSave: function (event, ui) {
            cellSave(event, ui);
        }
    };

    // A CONTINUACIÓN AL OBJETO GRID LE DEFINIMOS LAS COLUMNAS (colModel)
    obj.colModel = [{
            title: "ID",
            dataType: "string",
            dataIndx: "Id",
            editable: false,
            hidden: true
        },
        {
            title: "Fecha",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "string",
            dataIndx: "Fecha",
            editable: true,
            editor: {
                type: dateEditor2
            },
            validations: [{
                    type: 'regexp',
                    value: '[0-9]{2}/[0-9]{2}/[0-9]{4}',
                    msg: 'No en formato dd/mm/yyyy '
                }]
        }, {
            title: "Año",
            width: 70,
            minWidth: 50,
            maxWidth: 50,
            dataType: "string",
            dataIndx: "Anyo",
            editable: false
        }, {
            title: "Mes",
            width: 70,
            minWidth: 50,
            maxWidth: 50,
            dataType: "string",
            dataIndx: "Mes",
            editable: false
        }, {
            title: "Semana",
            width: 75,
            minWidth: 75,
            maxWidth: 75,
            dataType: "string",
            dataIndx: "Semana",
            editable: false
        }, {
            title: "Situacion",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "string",
            dataIndx: "Situacion",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    return situaciones;
                }
            }
        }, {
            title: "Asignado A",
            width: 130,
            minWidth: 130,
            maxWidth: 130,
            dataType: "string",
            dataIndx: "Asignado_Nombre",
            editable: true,
            hidden: false,
            editor: {
                type: 'select',
                options: function (ui) {
                    return asignados;
                }
            }
        }, {
            title: "Prioridad",
            width: 60,
            minWidth: 60,
            maxWidth: 60,
            dataType: "string",
            dataIndx: "Prioridad",
            editable: true,
            hidden: false,
            align: "center",
            editor: {
                type: 'select',
                options: prioridades
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Prioridad", $(this).val());
                        }
                    }]
            }
        }, {
            title: "No Planificado",
            width: 90,
            minWidth: 90,
            maxWidth: 90,
            dataType: "string",
            dataIndx: "No_Planificado",
            editable: true,
            hidden: false,
            editor: {
                type: 'select',
                options: function (ui) {
                    return tipoplanificada;
                }
            }
        }, {
            title: "Tiempo",
            width: 60,
            minWidth: 60,
            maxWidth: 60,
            dataType: "float",
            dataIndx: "Tiempo_Estimado",
            editable: true,
            hidden: false,
            align: "right"
                    /*
                     * ,validations: [{ type: 'regexp', value: '[0-9]{1}:[0-9]{1}', msg: 'No en formato horas:minutos ' }],
                     */
        }, {
            title: "Observaciones",
            width: 400,
            dataType: "string",
            dataIndx: "Observaciones",
            editable: true,
            hidden: false
        }, {
            title: "Comentario adicional",
            width: 250,
            dataType: "string",
            dataIndx: "Comentario_Adicional",
            editable: true
        }

    ];

    // BARRA DE TAREAS
    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));

        // BOTON NUEVA PLANIFICACION
        $("<span>Crear Planificación</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-plus"
            }
        }).click(function (evt) {
            insertar_planificacion( );
        });

        // BOTON ELIMINAR PLANIFICACION
        $("<span>Borrar Planificación</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-minus"
            }
        }).click(function (evt) {
            borrar_planificacion( );
        });

    };

    // HACEMOS CONSULTA PARA RECIBIR EL ARRAY JSON Y ASIGNARLO A data
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        rPP: 20,
        sortIndx: ["Anyo", "Semana"],
        sortDir: ["down", "down"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],

        curPage: 1,
        url: "select/consultaPlanificadorTarea.php?tarea=" + tarea + "&tipo=" + tipo + "&usuario=" + encodeURIComponent(usuariovalidado) + "&evaluador=" + evaluador + "&planificador_comun=" + planificador_comun + "&oficinausuariovalidado=" + encodeURIComponent(oficinausuariovalidado),
        getData: function (dataJSON, textStatus, jqXHR) {
            spinner.stop( );
            var data = dataJSON.data;
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    // EVENTOS
    obj.rowSelect = function (evt, obj) {
        // ID_PLANIFICACION = obj.rowData["Id"];
    };
    obj.cellSelect = function (evt, obj) {
        // ID_PLANIFICACION = obj.rowData["Id"];
    };
    obj.cellDblClick = function (evt, obj) {
        ID_PLANIFICACION = obj.rowData["Id"];
    };
    obj.cellClick = function (evt, obj) {
        ID_PLANIFICACION = obj.rowData["Id"];
    };

    // INICIALIZACION
    var $grid = $("#gridPlanificacionTarea").pqGrid(obj);

    // OPCIONES DEL GRID
    $grid.pqGrid("option", "topVisible", false);
    $grid.pqGrid("option", "showTitle", false);
    $grid.pqGrid("option", "showBottom", true);
    $grid.pqGrid("option", "columnBorders", true);
    $grid.pqGrid("option", "rowBorders", true);
    $grid.pqGrid("option", "oddRowsHighlight", true);
    $grid.pqGrid("option", "scrollModel", {
        horizontal: false
    }, {
        vertical: true
    });
    $grid.pqGrid("option", "collapsible.on", false);

    // GRABAR MODIFICACIONES
    function cellSave(evt, ui) {
        var target = document.getElementById('miseccionplanificadortarea');
        var spinner = new Spinner(opts).spin(target);

        var Campo = ui.dataIndx;
        var plan_fecha = ui.rowData[ "Fecha" ];
        var plan_asignado = ui.rowData[ "Asignado_Nombre" ];
        var plan_observaciones = ui.rowData[ "Observaciones" ];
        var plan_comentario_adicional = ui.rowData[ "Comentario_Adicional" ];
        var plan_situacion = ui.rowData[ "Situacion" ];
        var no_planificado = ui.rowData[ "No_Planificado" ];
        var prioridad = ui.rowData[ "Prioridad" ];

        var tiempo_estimado = ui.rowData[ "Tiempo_Estimado" ];
        if (String(tiempo_estimado).indexOf(':') > -1) {
            var temp = tiempo_estimado.split(":");
            tiempo_estimado = (parseInt(temp[0]) * 60) + (parseInt(temp[1] * 0.6));
        } else {
            tiempo_estimado = tiempo_estimado * 60;
        }

        var url = "select/modificar_planificacion.php?usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=" + tarea + "&Id=" + ID_PLANIFICACION + "&Fecha=" + plan_fecha + "&Asignado_Nombre=" + encodeURIComponent(plan_asignado) + "&Observaciones=" + encodeURIComponent(plan_observaciones) + "&Comentario_Adicional=" + encodeURIComponent(plan_comentario_adicional) + "&Situacion=" + encodeURIComponent(plan_situacion) + "&Tiempo_Estimado=" + encodeURIComponent(tiempo_estimado) + "&No_Planificado=" + encodeURIComponent(no_planificado) + "&Prioridad=" + encodeURIComponent(prioridad);
        $.ajax({
            cache: false,
            async: false,
            url: url
        }).done(function () {
            spinner.stop( );
            $grid.pqGrid("refreshDataAndView");
        });
    }

    // AÑADE UNA NUEVA LINEA DE PLANIFIACION
    function insertar_planificacion() {
        $("#Esperando").css("display", "block");
        // alertame( "FILA: " + id + "\n\nUSUARIO: " + usuariovalidado + "\n\nID PDA: " + ID_SELECCIONADA );
        var url = "select/insertar_planificacion.php?usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=" + tarea;
        $.ajax({
            cache: false,
            url: url
        }).done(function () {
            $("#Esperando").css("display", "none");
            $grid.pqGrid("refreshDataAndView");
        });
    }
    // BORRA UNA LINEA DE PLANIFIACION
    function borrar_planificacion() {
        $("#Esperando").css("display", "block");
        var url = "select/borrar_planificacion.php?usuario=" + encodeURIComponent(usuariovalidado) + "&Id=" + ID_PLANIFICACION;
        $.ajax({
            cache: false,
            url: url
        }).done(function () {
            $("#Esperando").css("display", "none");
            $grid.pqGrid("refreshDataAndView");
        });


    }


    // ************************************GRID
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * 24/07/2017
 */
function cargaGridPlanificacionesTareas() {

    //var estadostarea = [];

    var target = document.getElementById('micuerpo');
    var spinner = new Spinner(opts).spin(target);

    var ID_PLANIFICACION = -1;
    var prioridades = ['Normal', 'Alta'];

    // ++ GESTION DE SEMANAS
    var semana_act = Semana_ISO8601(new Date());
    var semana_ant = semana_act - 1;
    var semana_sig = semana_act + 1;
    //console.log("ANT: " + semana_ant + ". ACT: " + semana_act + ". SIG: " + semana_sig);
    // -- GESTION DE SEMANAS

    var tareasvivas = 0;
    var planificador_comun = 0;

    var dateEditor = function (ui) {
        var $cell = ui.$cell,
                rowData = ui.rowData,
                dataIndx = ui.dataIndx,
                cls = ui.cls,
                dc = $.trim(rowData[dataIndx]);
        $cell.css('padding', '0');
        var $inp = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' />")
                .appendTo($cell)
                .val(dc).datepicker({
            changeMonth: true,
            changeYear: true,
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            firstDay: 1,
            dateFormat: "dd/mm/yy",
            onClose: function () {
                $inp.focus();
            }
        });
    }

    var dateEditor2 = function (ui) {
        var $cell = ui.$cell;
        var dataIndx = ui.dataIndx;
        var rowData = ui.rowData;
        var cls = ui.cls;
        var dc = $.trim(rowData[dataIndx]);
        var input = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' autocomplete='off'  />")
                .appendTo($cell)
                .val(dc).datetimepicker({
            datepicker: true,
            timepicker: false,
            format: 'd/m/Y H:i:s',
            minTime: '07:00:00',
            weeks: true,
            step: 15,
            dayOfWeekStart: 1,
        });
    }

    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '100%',
        height: '100%',
        title: "",
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
// editModel: {
// clicksToEdit: 2
// ,saveKey: $.ui.keyCode.ENTER
// ,onSave: null
// },
        editModel: {
            cellBorderWidth: 1,
            clicksToEdit: 2,
            pressToEdit: true,
            filterKeys: true,
            keyUpDown: true,
            saveKey: $.ui.keyCode.ENTER,
            onSave: null,
            onBlur: 'save',
            allowInvalid: false,
            invalidClass: 'pq-cell-red-tr pq-has-tooltip',
            warnClass: 'pq-cell-blue-tr pq-has-tooltip'
        },
        pageModel: {
            type: 'remote',
            rPP: 50,
            strRpp: "{0}",
            rPPOptions: [50, 100, 1000]
        },
        filterModel: {
            on: true,
            mode: "AND",
            header: true
        },
        cellSave: function (event, ui) {
            cellSave(event, ui);
        },
        scrollModel: {
            pace: 'fast',
            autoFit: true,
            theme: true
        },
        toggle: function (event, ui) {
            gridMaximice(event, ui);
        },
        groupModel: {
            dataIndx: ["Anyo", "Semana"],
            collapsed: [false],
            // title: ["<b style='font-weight:bold;'>{0} ({1} PLANIFICACIONES AÑO)</b>", "{0} - {1}","<b style='font-weight:bold;'>{0} ({1} PLANIFICACIONES MES)</b>", "{0} - {1}", "{0} - {1}","<b style='font-weight:bold;'>{0} ({1} PLANIFICACIONES SEMANA)</b>", "{0} - {1}"],
            title: ["<b>Año {0}</b>", "<b style='font-weight:bold;'>Semana {0}</b>"],
            dir: ["down", "down"]
        }
    };

    // BARRA DE TAREAS
    obj.render = function (evt, obj) {
        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));
        // BOTON ELIMINAR PLANIFICACION
        $("<span>Borrar Planificación</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-minus"
            }
        }).click(function (evt) {
            borrar_planificacion( );
        });

        $("<span>Exportar</span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-extlink"
            }
        }).click(function (evt) {
            $GridPlanificacionesTareas.pqGrid("exportExcel", {url: "./export/excel.php", sheetName: "Planificador"});
        });


        $("<span id='btn_semana_ant'>&#9664;&nbsp;&#9664; Semana " + semana_ant + "</span>").appendTo($toolbar).button({
        }).click(function (evt) {
            semana_a_mostrar = semana_ant;
            semana_act--;
            semana_ant--;
            semana_sig--;
            $("#btn_semana_quitar").removeClass("pq_btn_deshabilitado");
            $("#btn_semana_quitar").addClass("pq_btn_rojo");
            setDataModel();
        });

        $("<span id='btn_semana_act'>Semana " + semana_act + "</span>").appendTo($toolbar).button({
        }).click(function (evt) {
            semana_a_mostrar = semana_act;
            semana_ant = semana_act - 1;
            semana_sig = semana_act + 1;
            $("#btn_semana_quitar").removeClass("pq_btn_deshabilitado");
            $("#btn_semana_quitar").addClass("pq_btn_rojo");
            setDataModel();
        });

        $("<span id='btn_semana_sig'>Semana " + semana_sig + " &#9658;&nbsp;&#9658;</span>").appendTo($toolbar).button({
        }).click(function (evt) {
            semana_a_mostrar = semana_sig;
            semana_act++;
            semana_ant++;
            semana_sig++;
            $("#btn_semana_quitar").removeClass("pq_btn_deshabilitado");
            $("#btn_semana_quitar").addClass("pq_btn_rojo");
            setDataModel();
        });

        // T#28847 - JUAN ANTONIO ABELLAN
        if (ambito == 0) {
            $("<span  id='btn_todas_planificaciones'>Todos los asignados</span>").appendTo($toolbar).button({
            }).click(function (evt) {
                //semana_a_mostrar = -1;
                //semana_act = Semana_ISO8601(new Date());
                //semana_ant = semana_act -1;
                //semana_sig = semana_act +1;
                //$grid.pqGrid( "option" , "dataModel.url", "select/consultaPlanificadorTarea.php?tarea=-1&usuario=" + encodeURIComponent(usuariovalidado)  + "&evaluador=" + evaluador + "&planificador_comun=1" + "&oficinausuariovalidado=" + encodeURIComponent(oficinausuariovalidado) + "&semana="+ semana_a_mostrar );
                //$grid.pqGrid( "refreshDataAndView" );
                $("#btn_semana_act").html("<span class='ui-button-text'>Semana " + semana_act + "</span> ");
                $("#btn_semana_ant").html("<span class='ui-button-text'>&#9664;&nbsp;&#9664; Semana " + semana_ant + "&nbsp;</span> ");
                $("#btn_semana_sig").html("<span class='ui-button-text'>Semana " + semana_sig + " &#9658;&nbsp;&#9658;</span> ");
                $("#btn_todas_planificaciones").addClass("pq_btn_deshabilitado");
                //$("#btn_todas_planificaciones").removeClass("pq_btn_rojo");
                $("#btn_semana_quitar").removeClass("pq_btn_deshabilitado");
                $("#btn_semana_quitar").addClass("pq_btn_rojo");
                planificador_comun = 1;
                setDataModel();
            });
        }


        // T#37272 18/06/2020 Alberto
        $("<span  id='btn_solotareaspendientes'>Solo tareas pendientes</span>").appendTo($toolbar).button({
        }).click(function (evt) {
            $("#btn_solotareaspendientes").addClass("pq_btn_deshabilitado");
            $("#btn_semana_quitar").removeClass("pq_btn_deshabilitado");
            $("#btn_semana_quitar").addClass("pq_btn_rojo");
            tareasvivas = 1;
            setDataModel();
        });

        // T#42070 26/11/2020 Alberto
        $("<span  id='btn_solotareaspendientes'>Copiar planificación</span>").appendTo($toolbar).button({
        }).click(function (evt) {
            copiarPlanificacion();
        });

        $("<span class='pq_btn_deshabilitado' id='btn_semana_quitar'>&#10008;&nbsp;Quitar filtros</span>").appendTo($toolbar).button({
        }).click(function (evt) {
            semana_a_mostrar = -1;
            semana_act = Semana_ISO8601(new Date());
            semana_ant = semana_act - 1;
            semana_sig = semana_act + 1;
            $("#btn_semana_quitar").addClass("pq_btn_deshabilitado");
            $("#btn_semana_quitar").removeClass("pq_btn_rojo");
            $("#btn_todas_planificaciones").removeClass("pq_btn_deshabilitado");
            $("#btn_solotareaspendientes").removeClass("pq_btn_deshabilitado");
            planificador_comun = 0;
            tareasvivas = 0;
            setDataModel();
        });

    };

    function setDataModel() {
        $("#btn_semana_act").html("<span class='ui-button-text'>Semana " + semana_act + "</span> ");
        $("#btn_semana_ant").html("<span class='ui-button-text'>&#9664;&nbsp;&#9664; Semana " + semana_ant + "&nbsp;</span> ");
        $("#btn_semana_sig").html("<span class='ui-button-text'>Semana " + semana_sig + " &#9658;&nbsp;&#9658;</span> ");

        $GridPlanificacionesTareas.pqGrid("option", "dataModel.url", "select/consultaPlanificadorTarea.php?tarea=-1&usuario=" + encodeURIComponent(usuariovalidado) + "&evaluador=" + evaluador + "&oficinausuariovalidado=" + encodeURIComponent(oficinausuariovalidado) + "&tipo=0" + "&semana=" + semana_a_mostrar + "&planificador_comun=" + planificador_comun + "&tareasvivas=" + tareasvivas);
        $GridPlanificacionesTareas.pqGrid("refreshDataAndView");
    }
    function copiarPlanificacion() {

        var optionsOficina;
        $(oficinas).each(function (index, value) {
            if (index == 0) {
                optionsOficina += "<option value='" + value + "' selected='selected'>" + value + "</option>";
            } else {
                optionsOficina += "<option value='" + value + "' >" + value + "</option>";
            }
        });

        var pantallaMasivos = "";
        pantallaMasivos += "<div id='popup_masivos' style='padding: 0px !important'>";
        pantallaMasivos += "  <div style=' display: inline-block; width:100%;  height: auto;margin: 5px; padding: 5px; background-color: #eceff1;text-align:justify' >";
        pantallaMasivos += "	<h4 style='color:red'>";
        pantallaMasivos += "	  Esta función copiará planificaciones de una semana o día origen en una semana o día de destino, no obstante podrá editar, eliminar o crear nuevas planificaciones en la semana de destino, o de igual manera volver a ejecutar este proceso para añadir nuevos registros.";
        pantallaMasivos += "	</h4>";
        pantallaMasivos += "  </div>";
        pantallaMasivos += "  <div style=' display: inline-block; width:100%;  height: auto;margin: 5px; padding: 5px; background-color: #eceff1;' >";
        pantallaMasivos += "	<select id='selectPlanMasivoOficina' name='selectPlanMasivoOficina' style='width: 32.5%;height: 25px;'>" + optionsOficina + "</select>";
        pantallaMasivos += "  	<input type='text' readonly='readonly' name='semanaOrigen'		align='center' class='' id='semanaOrigen'		value='' placeholder='Semana origen*' style='width:32.5%;height: 25px;'/>";
        pantallaMasivos += "  	<input type='text' readonly='readonly' name='semanaDestino'		align='center' class='' id='semanaDestino'		value='' placeholder='Semana destino*' style='width:32.5%;height: 25px;' >";
        pantallaMasivos += "  </div>"
        pantallaMasivos += "  <div style=' display: inline-block; width:100%;  height: auto;margin: 5px; padding: 5px; background-color: #eceff1;' >";
        if (evaluador == 1) {
            pantallaMasivos += "  <input type='checkbox' id='planMasivoTodosUsuarios' 	name='planMasivoTodosUsuarios' />Todos los usuarios <br>";
            pantallaMasivos += "  <input type='text' name='usuarioOrigen'  align='center' class='' id='usuarioOrigen'  value='' placeholder='Usuario origen*' style='width:49.4%'/>"
            pantallaMasivos += "  <input type='text' name='usuarioDestino' align='center' class='' id='usuarioDestino' value='' placeholder='Usuario destino (opcional)' style='width:49%'  />";
        } else {
            pantallaMasivos += "  <input type='checkbox' id='planMasivoTodosUsuarios' 	name='planMasivoTodosUsuarios' disabled='true'/>Todos los usuarios<br>";
            pantallaMasivos += "  <input type='text' name='usuarioOrigen'  align='center' class='' id='usuarioOrigen'  value='" + nombreusuariovalidado + "' placeholder='Usuario origen*' style='width:49.4%' disabled='true'/>"
            pantallaMasivos += "  <input type='text' name='usuarioDestino' align='center' class='' id='usuarioDestino' value='' placeholder='Usuario destino (opcional)' style='width:49%' disabled='true' />";
        }
        pantallaMasivos += "  </div>"
        pantallaMasivos += "  <div id='masivo_estados' style=' display: inline-block;width: calc(100% );  height: auto;margin: 5px; padding: 5px; background-color: #eceff1;' >";
        pantallaMasivos += "  	Opciones: ";
        pantallaMasivos += "  	<br><input type='checkbox' id='planMasivoSoloNuevas'			name='planMasivoSoloNuevas' checked='true' />No copiar si ya existe planificación en día/semana destino (solo nuevas)";
        pantallaMasivos += "  	<br><input type='checkbox' id='planMasivoSoloTareasPendientes' 	name='planMasivoSoloTareasPendientes' checked='true'/>No copiar si la tarea está completada";
        pantallaMasivos += "  	<br><input type='checkbox' id='planMasivoSoloPlanifPendientes' 	name='planMasivoSoloPlanifPendientes' />No copiar si la planificación está completada";
        pantallaMasivos += "  	<br><input type='checkbox' id='planMasivoSoloDiaSeleccionado' 	name='planMasivoSoloDiaSeleccionado' />No copiar semana, solo día seleccionado";
        pantallaMasivos += "  	<br><input type='checkbox' id='planMasivoNoCopiarObservaciones' name='planMasivoNoCopiarObservaciones' />No copiar observaciones";
        pantallaMasivos += "  	<br><input type='checkbox' id='planMasivoMantenerSituacion' 	name='planMasivoMantenerSituacion' />Copiar situaciones";
        pantallaMasivos += "  	<br><input type='checkbox' id='planMasivoCopiarTiempos'			name='planMasivoCopiarTiempos' />Copiar tiempo planificado";
        pantallaMasivos += "  </div>";
        pantallaMasivos += "</div>";

        var dialogo = document.getElementById('dialog-cancelar');
        dialogo.innerHTML = pantallaMasivos;

        $('#selectPlanMasivoOficina').change(function () {
            $("#usuarioOrigen").val('');
            $("#usuarioDestino").val('');
        });

        $('#planMasivoTodosUsuarios').change(function () {
            if (this.checked) {
                $("#usuarioOrigen").val('');
                $("#usuarioDestino").val('');
                $('#usuarioOrigen').attr('disabled', 'disabled');
                $('#usuarioDestino').attr('disabled', 'disabled');
            } else {
                $('#usuarioOrigen').removeAttr('disabled');
                $('#usuarioDestino').removeAttr('disabled');
            }
        });

        jQuery('#semanaOrigen').datetimepicker({
            datepicker: true,
            timepicker: false,
            format: 'd-m-Y W',
            weeks: true,
            dayOfWeekStart: 1,
            minDate: 1,
            mask: false,
            disabledWeekDays: [6, 0]
        });

        jQuery('#semanaDestino').datetimepicker({
            datepicker: true,
            timepicker: false,
            format: 'd-m-Y W',
            weeks: true,
            dayOfWeekStart: 1,
            minDate: 0,
            disabledWeekDays: [6, 0]
        });

        // AUTOCOMPLETE USUARIO ORIGEN
        $("#usuarioOrigen").autocomplete({
            type: 'post',
            source: function (request, response) {
                $.get("select/getUsuarioAutocompleteOficina.php", {
                    buscar: request.term,
                    tipo: 1,
                    oficinas: oficinas,
                    oficinaTarea: $('#selectPlanMasivoOficina').find(":selected").text(),
                    externo: ''
                }, function (data) {
                    tags = data.split("|");
                    response(tags);
                });
            },
            appendTo: dialogo
        });

        $('#usuarioOrigen').change(function () {
            var target = document.getElementById('dialog-cancelar');
            var spinner = new Spinner(opts).spin(target);
            var url = "select/comprobarExisteNombre.php";
            $.ajax({
                url: url,
                cache: false,
                async: true,
                method: "GET",
                data: {
                    buscar: 'usuarios',
                    nombre: $("#usuarioOrigen").val( ),
                    oficina: $('#selectPlanMasivoOficina').find(":selected").text()
                }
            }).done(function (data) {
                spinner.stop();
                if (parseInt(data) == 0) {
                    $("#usuarioOrigen").val('');
                    $("#usuarioOrigen").focus( );
                    $("#usuarioOrigen").addClass("rojo");
                    mensaje('Cambios masivos de usuarios', 'El usuario origen no existe en el departamento.');
                } else {
                    $("#usuarioOrigen").removeClass("rojo");
                }
            });
        });

        // AUTOCOMPLETE USUARIO DESTINO
        $("#usuarioDestino").autocomplete({
            type: 'post',
            source: function (request, response) {
                $.get("select/getUsuarioAutocompleteOficina.php", {
                    buscar: request.term,
                    tipo: 1,
                    oficinas: oficinas,
                    oficinaTarea: $('#selectPlanMasivoOficina').find(":selected").text(),
                    externo: ''
                }, function (data) {
                    tags = data.split("|");
                    response(tags);
                });
            },
            appendTo: dialogo
        });

        $('#usuarioDestino').change(function () {
            var target = document.getElementById('dialog-cancelar');
            var spinner = new Spinner(opts).spin(target);
            var url = "select/comprobarExisteNombre.php";
            $.ajax({
                url: url,
                cache: false,
                async: true,
                method: "GET",
                data: {
                    buscar: 'usuarios',
                    nombre: $("#usuarioDestino").val( ),
                    oficina: $('#selectPlanMasivoOficina').find(":selected").text()
                }
            }).done(function (data) {
                spinner.stop();
                if (parseInt(data) == 0) {
                    $("#usuarioDestino").val('');
                    $("#usuarioDestino").focus( );
                    $("#usuarioDestino").addClass("rojo");
                    mensaje('Cambios masivos de usuarios', 'El usuario destino no existe en el departamento.');
                } else {
                    $("#usuarioDestino").removeClass("rojo");
                }
            });
        });


        $("#dialog-cancelar").dialog({
            resizable: false,
            title: 'Copia de planificación',
            height: 600,
            width: 650,
            dialogClass: 'no-close',
            modal: true,
            open: function (event, ui) {
                //alert();
            },
            buttons: {
                "Cerrar": function () {
                    $(this).dialog("close");
                },
                "Aceptar": function () {

                    $("#masivo_opciones").removeClass("rojo");
                    $("#masivo_estados").removeClass("rojo");

                    //var correcto = true;

                    var selectPlanMasivoOficina = $('#selectPlanMasivoOficina').find(":selected").text();
                    var semanaOrigen = $("#semanaOrigen").val( );
                    var semanaDestino = $("#semanaDestino").val( );

                    var usuarioOrigen = $("#usuarioOrigen").val( );
                    var usuarioDestino = $("#usuarioDestino").val( );

                    var planMasivoSoloTareasPendientes = $('#planMasivoSoloTareasPendientes').is(":checked");
                    var planMasivoSoloPlanifPendientes = $('#planMasivoSoloPlanifPendientes').is(":checked");
                    var planMasivoTodosUsuarios = $('#planMasivoTodosUsuarios').is(":checked");
                    var planMasivoSoloDiaSeleccionado = $('#planMasivoSoloDiaSeleccionado').is(":checked");
                    var planMasivoMantenerSituacion = $('#planMasivoMantenerSituacion').is(":checked");
                    var planMasivoCopiarTiempos = $('#planMasivoCopiarTiempos').is(":checked");
                    var planMasivoSoloNuevas = $('#planMasivoSoloNuevas').is(":checked");
                    var planMasivoNoCopiarObservaciones = $('#planMasivoNoCopiarObservaciones').is(":checked");

                    // El usuario de origen no debe estar vacío
                    if (usuarioOrigen == '' && planMasivoTodosUsuarios == false) {
                        mensaje('Cambios masivos de usuarios', 'Has de indicar un usuario origen.');
                        $("#usuarioOrigen").addClass("rojo");
                        // Las fechas no pueden estar vacías	
                    } else if (semanaOrigen == '' || semanaDestino == '') {
                        mensaje('Cambios masivos de usuarios', 'Las fechas no pueden estar en blanco.');
                        $("#semanaOrigen").addClass("rojo");
                        $("#semanaDestino").addClass("rojo");
                    } else {
                        $("#masivo_opciones").removeClass("rojo");
                        $("#masivo_estados").removeClass("rojo");
                        if (confirm('¿Está seguro de hacer la copia masiva?')) {
                            var target = document.getElementById('contenedor');
                            var spinner = new Spinner(opts).spin(target);
                            var url = "select/copiarPlanificacion.php";
                            $.ajax({
                                url: url,
                                cache: false,
                                async: true,
                                method: "POST",
                                data: {
                                    oficina: selectPlanMasivoOficina,
                                    semanaOrigen: semanaOrigen,
                                    semanaDestino: semanaDestino,
                                    usuarioOrigen: usuarioOrigen,
                                    usuarioDestino: usuarioDestino,
                                    tareasPendientes: planMasivoSoloTareasPendientes,
                                    planifPendientes: planMasivoSoloPlanifPendientes,
                                    todosUsuarios: planMasivoTodosUsuarios,
                                    diaSeleccionado: planMasivoSoloDiaSeleccionado,
                                    mantenerSituacion: planMasivoMantenerSituacion,
                                    copiarTiempos: planMasivoCopiarTiempos,
                                    soloNuevas: planMasivoSoloNuevas,
                                    noCopiarObservaciones: planMasivoNoCopiarObservaciones,
                                    usuario: usuariovalidado
                                }
                            }).done(function (data) {
                                spinner.stop();
                                if (parseInt(data) == 0) {
                                    mensaje('Planificador', 'No se han encotrado registros que cumplan las condiciones.');
                                } else if (parseInt(data) > 0) {
                                    $GridPlanificacionesTareas.pqGrid("refreshDataAndView");
                                    mensaje('Planificador', 'La copia se ha realizado correctamente, se han insertado ' + data + ' registros.');
                                } else {
                                    mensaje('Error', 'Error no controlado');
                                }
                            });
                        }
                    }

                }
            }
        });
    }

    // EVENTOS
    obj.rowSelect = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Tarea"];
        ID_PLANIFICACION = obj.rowData["Id"];
        /*
         $.ajax({
         url: "select/getTiposTipos.php?tipo=5&id=" + ID_SELECCIONADA,
         success: function(response) {
         estadostarea = response.split(",");
         }
         });
         */
    };

    obj.cellSelect = function (evt, obj) {
        ID_SELECCIONADA = obj.rowData["Tarea"];
        ID_PLANIFICACION = obj.rowData["Id"];
        /*
         $.ajax({
         url: "select/getTiposTipos.php?tipo=5&id=" + ID_SELECCIONADA,
         success: function(response) {
         estadostarea = response.split(",");
         }
         });
         */
    };

    obj.cellDblClick = function (evt, obj) {
        ID_PLANIFICACION = obj.rowData["Id"];
        ID_SELECCIONADA = obj.rowData["Tarea"];
        /*
         $.ajax({
         url: "select/getTiposTipos.php?tipo=5&id=" + ID_SELECCIONADA,
         success: function(response) {
         estadostarea = response.split(",");
         }
         });
         */
    };

    groupIndx = ["Semana"];

    // Columnas
    obj.colModel = [{
            title: "Id",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Id"
        },
        {
            title: "Fecha",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            // dataType: "date",
            dataIndx: "Fecha",
            editor: {
                type: dateEditor2
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Fecha", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Año ISO",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "string",
            dataIndx: "Anyo",
            editable: false,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Anyo", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Mes",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "string",
            dataIndx: "Mes",
            editable: false,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Mes", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Semana ISO",
            width: 60,
            minWidth: 60,
            maxWidth: 60,
            dataType: "string",
            dataIndx: "Semana",
            editable: false,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Semana", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Situación",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "string",
            dataIndx: "Situacion",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    var tarea = ui.rowData["Tarea"];
                    return obtenerTipos(tarea, 13);
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Situacion", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Estado Tarea",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "string",
            dataIndx: "EstadoTarea",
            editable: true,
            hidden: false,
            editor: {
                type: 'select',
                options: function (ui) {
                    var tarea = ui.rowData["Tarea"];
                    return obtenerTipos(tarea, 5);
                }
            },
        }, {
            title: "Prioridad",
            width: 60,
            minWidth: 60,
            maxWidth: 60,
            dataType: "string",
            dataIndx: "Prioridad",
            editable: true,
            hidden: false,
            align: "center",
            editor: {
                type: 'select',
                options: prioridades
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Prioridad", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Información de la Tarea",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "integer",
            editable: false,
            hidden: false,
            // dataIndx: "Tarea",
            render: function (ui) {
                var rowData = ui.rowData;
                var tarea = rowData["Tarea"];
                var usuarioEvaluado = rowData["Asignado_Usuario"];
                var btn = '';
                //btn = "<input type='button' style='width:70px;' value='Mostrar' title=' Ver información de la tarea " + tarea + "' onclick='pop_up_Comentarios(" + tarea + ",\"\",\"\",\"" + usuarioEvaluado + "\");' />";
                btn = "<input type='button' style='width:70px;' value='Mostrar' title=' Ver información de la tarea " + tarea + "' onclick='pop_up_InformacionTarea(" + tarea + ");' />";
                return btn;
            }
        },
        {
            title: "Planificación de la Tarea",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "integer",
            editable: false,
            hidden: false,
            // dataIndx: "Tarea",
            render: function (ui) {
                var tarea = ui.rowData["Tarea"];
                return "<center><input type='button' style='width:70px;' value='Mostrar' title=' Ver planificación de tarea " + tarea + "' onclick='pop_up_Planificador(" + tarea + ",0);' /></center>";
            }
        },
        {
            title: "Tarea",
            width: 100,
            minWidth: 100,
            maxWidth: 100,
            dataType: "integer",
            editable: false,
            hidden: false,
            dataIndx: "Tarea",
            render: function (ui) {
                var tarea = ui.rowData["Tarea"];
                var id = ui.rowData["Id"];
                var ret = "<input type='button' style='width:70px;' value='" + tarea + "' title='Abrir tarea " + tarea + "' onclick='cambia_menu(3);' />";
                if (ui.rowData["Iniciada"] == 0) {
                    ret += "<img src='imagenes/play.png' onclick='ID_SELECCIONADA=" + tarea + ";ID_PLAN_SELECCIONADA=" + id + " ;IniciarTarea();' title='Iniciar tarea " + tarea + "' style='width:18px;height:18px;cursor: pointer;' />";
                } else {
                    ret += "<img src='imagenes/stop.png' onclick='PararTarea();' title='Parar tarea' style='width:18px;height:18px;cursor: pointer;'>"
                }
                return ret;
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Tarea", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Título",
            width: 400,
            minWidth: 200,
            maxWidth: 600,
            dataType: "text",
            editable: false,
            dataIndx: "Título",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Título", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Solicitado",
            width: 150,
            minWidth: 150,
            maxWidth: 150,
            dataType: "string",
            dataIndx: "Solicitado",
            editable: false,
            hidden: false,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Solicitado", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Asignado",
            width: 130,
            minWidth: 130,
            maxWidth: 130,
            dataType: "string",
            dataIndx: "Asignado_Nombre",
            editable: false,
            hidden: false,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Asignado_Nombre", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Fecha Objetivo",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "string",
            dataIndx: "Fecha_Objetivo",
            editable: false,
            hidden: false
        }, {
            title: "Tiempo Estimado",
            width: 60,
            minWidth: 60,
            maxWidth: 60,
            dataType: "float",
            dataIndx: "Tiempo_Estimado",
            editable: true,
            hidden: false,
            align: "right",
            summary: {
                type: ["sum", "sum"],
                title: ["<b>{0}</b>", "<b>{0}</b>"]
            }
            /*
             * ,validations: [{ type: 'regexp', value: '[0-9]{1}:[0-9]{1}', msg: 'No en formato horas:minutos ' }],
             */
        }, {
            title: "Tiempo Fichado Día",
            width: 60,
            minWidth: 60,
            maxWidth: 60,
            dataType: "float",
            dataIndx: "Tiempo_Real_Dia",
            editable: true,
            hidden: false,
            align: "right",
            summary: {
                type: ["sum", "sum"],
                title: ["<b>{0}</b>", "<b>{0}</b>"]
            }
        }, {
            title: "Tiempo Fichado Semana",
            width: 60,
            minWidth: 60,
            maxWidth: 60,
            dataType: "float",
            dataIndx: "Tiempo_Real_Semana",
            editable: true,
            hidden: false,
            align: "right",
            summary: {
                type: ["sum", "sum"],
                title: ["<b>{0}</b>", "<b>{0}</b>"]
            }
        }, {
            title: "Tiempo Estimado Tarea",
            width: 60,
            minWidth: 60,
            maxWidth: 60,
            dataType: "float",
            dataIndx: "Tiempo_Estimado_Tarea",
            editable: false,
            hidden: false,
            align: "right",
            summary: {
                type: ["sum", "sum"],
                title: ["<b>{0}</b>", "<b>{0}</b>"]
            }
        }, {
            title: "Tiempo Fichado Total",
            width: 60,
            minWidth: 60,
            maxWidth: 60,
            dataType: "float",
            dataIndx: "Tiempo_Real",
            editable: false,
            hidden: false,
            align: "right",
            summary: {
                type: ["sum", "sum"],
                title: ["<b>{0}</b>", "<b>{0}</b>"]
            }
        }, {
            title: "No Planificado",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "string",
            dataIndx: "No_Planificado",
            editable: false,
            hidden: false,
            align: "center",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("No_Planificado", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Observaciones",
            // width : 400 ,
            minWidth: 200,
            maxWidth: 1000,
            dataType: "string",
            dataIndx: "Observaciones",
            editable: true,
            hidden: false,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Observaciones", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Comentario adicional",
            width: 250,
            minWidth: 250,
            maxWidth: 250,
            dataType: "string",
            dataIndx: "Comentario_Adicional",
            editable: true,
            hidden: true,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Comentario_Adicional", $(this).val());
                        }
                    }]
            }
        }
    ];

    // Datos
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Anyo", "Semana", "Tarea"],
        sortDir: ["down", "down", "down"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],
        url: "select/consultaPlanificadorTarea.php?tarea=-1" + "&usuario=" + encodeURIComponent(usuariovalidado) + "&evaluador=" + evaluador + "&planificador_comun=0" + "&oficinausuariovalidado=" + encodeURIComponent(oficinausuariovalidado),
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            spinner.stop( );
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };





    $GridPlanificacionesTareas = $("#miseccionplanificadortareas_grid_array").pqGrid(obj);
    // Mostrar la parte de arriba
    // $grid.pqGrid("option", "topVisible", true);
    // $grid.pqGrid("option", "showTitle", false);
    // $grid.pqGrid("option", "collapsible", false);
    // Mostrar la parte de abajo
    // $grid.pqGrid("option", "bottomVisible", true);
    // Lineas de separacion verticales
    // $grid.pqGrid("option", "columnBorders", true);
    // Lineas de separacion horizontales
    // $grid.pqGrid("option", "rowBorders", true);
    // Sombreado en las filas
    // $grid.pqGrid("option", "oddRowsHighlight", true);
    // Numero de celda
    // $grid.pqGrid("option", "numberCell", true);
    // Comprimir altura
    // $grid.pqGrid("option", "flexHeight", true);
    // Comprimir Anchura
    // $grid.pqGrid("option", "flexWidth", true);
    // $grid.pqGrid("option", "freezeCols",1);
    // Barras de scroll
    $GridPlanificacionesTareas.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });
    // Permite cambiar el tamaño
    // $grid.pqGrid("option", "resizable", true);

    // GRABAR MODIFICACIONES
    function cellSave(evt, ui) {

        ID_PLANIFICACION = ui.rowData[ "Id" ];

        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);

        var Campo = ui.dataIndx;
        var tarea = ui.rowData[ "Tarea" ];

        if (Campo == 'EstadoTarea') {

            var estadoTarea = ui.rowData[ "EstadoTarea" ];
            var url = "select/modificar_planificacion.php?usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=" + tarea + "&estadoTarea=" + estadoTarea;

            $.ajax({
                cache: false,
                async: true,
                url: url
            }).done(function (data) {

                if (data != '') {
                    mensaje('Campo obligatorio', data, 'alert.png');
                }

                spinner.stop( );
                $GridPlanificacionesTareas.pqGrid("refreshDataAndView");
            });
        } else {

            var plan_fecha = ui.rowData[ "Fecha" ];
            var plan_asignado = ui.rowData[ "Asignado_Nombre" ];
            var plan_observaciones = ui.rowData[ "Observaciones" ];
            var plan_comentario_adicional = ui.rowData[ "Comentario_Adicional" ];
            var plan_situacion = ui.rowData[ "Situacion" ];
            var noplanificado = ui.rowData["No_Planificado"];
            var prioridad = ui.rowData["Prioridad"];

            var tiempo_estimado = ui.rowData[ "Tiempo_Estimado" ];
            if (String(tiempo_estimado).indexOf(':') > -1) {
                var temp = tiempo_estimado.split(":");
                tiempo_estimado = (parseInt(temp[0]) * 60) + (parseInt(temp[1] * 0.6));
            } else {
                tiempo_estimado = tiempo_estimado * 60;
            }


            if (plan_fecha == '') {
                mensaje('Error', 'la fecha no puede estar vacia');
                spinner.stop( );
                $GridPlanificacionesTareas.pqGrid("refreshDataAndView");
            } else {

                var url = "select/modificar_planificacion.php?usuario=" + encodeURIComponent(usuariovalidado) + "&tarea=" + tarea + "&Id=" + ID_PLANIFICACION + "&Fecha=" + plan_fecha + "&Asignado_Nombre=" + encodeURIComponent(plan_asignado) + "&Observaciones=" + encodeURIComponent(plan_observaciones) + "&Comentario_Adicional=" + encodeURIComponent(plan_comentario_adicional) + "&Situacion=" + encodeURIComponent(plan_situacion) + "&Tiempo_Estimado=" + encodeURIComponent(tiempo_estimado) + "&No_Planificado=" + encodeURIComponent(noplanificado) + "&Prioridad=" + encodeURIComponent(prioridad);

                $.ajax({
                    cache: false,
                    async: true,
                    url: url
                }).done(function () {
                    spinner.stop( );
                    $GridPlanificacionesTareas.pqGrid("refreshDataAndView");
                });
            }
        }

        ID_PLANIFICACION = -1;
    }

    function obtenerTipos(tarea, tipo) {
        var arr = [];
        $.ajax({
            url: "select/getTiposTipos.php?tipo=" + tipo + "&id=" + tarea,
            cache: false,
            async: false,
            success: function (response) {
                arr = response.split(",");

            }
        });

        return arr;
    }

    function getRowIndx() {
        alert('function getRowIndx()');
        var arr = $GridPlanificacionesTareas.pqGrid("selection", {
            type: 'row',
            method: 'getSelection'
        });
        if (arr && arr.length > 0) {
            var rowIndx = arr[0].rowIndx;
            return rowIndx;
        } else {

            return null;
        }
    }

    // BORRA UNA LINEA DE PLANIFICACION
    function borrar_planificacion() {
        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);

        var url = "select/borrar_planificacion.php?usuario=" + encodeURIComponent(usuariovalidado) + "&Id=" + ID_PLANIFICACION;

        $.ajax({
            cache: false,
            url: url
        }).done(function () {
            spinner.stop( );
            $GridPlanificacionesTareas.pqGrid("refreshDataAndView");
        });
    }

    function filter(dataIndx, value) {
        $GridPlanificacionesTareas.pqGrid("filter", {
            data: [{
                    dataIndx: dataIndx,
                    value: value
                }]
        });
    }
    // ************************************GRID
    // ****************************************
}

function Semana_ISO8601(fecha) {
    var tdt = new Date(fecha.valueOf());
    var dayn = (fecha.getDay() + 6) % 7;
    tdt.setDate(tdt.getDate() - dayn + 3);
    var firstThursday = tdt.valueOf();
    tdt.setMonth(0, 1);
    if (tdt.getDay() !== 4)
    {
        tdt.setMonth(0, 1 + ((4 - tdt.getDay()) + 7) % 7);
    }
    return 1 + Math.ceil((firstThursday - tdt) / 604800000);
}

/***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author alberto
 * @date 12/12/2017
 * @description controla que desaparezcan los slides cuando se maximiza el portal
 * @param event
 * @param ui
 * @returns
 */
function gridMaximice(event, ui) {
    if (ui.state == 'max') {
        // Hide Slides
        $("#resumen_horas").hide();
        $("#resumen_frecuentes").hide();
        $("#resumen_tareas").hide();
    } else {
        // Show Slides
        $("#resumen_horas").show(2000);
        $("#resumen_frecuentes").show(2000);
        $("#resumen_tareas").show(2000);
    }
}

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds) {
            break;
        }
    }
}

/*
 * funcion para obtener fechas pasando el parametro 0 la fecha es la de hoy, si el valor es diferente a 0 se sumara a la fecha de hoy y sacara la fecha.
 * 
 * Juan
 */
function obtenerFecha(num) {

    var hoy = new Date();
    hoy.setDate(hoy.getDate() + num);

    var dd = hoy.getDate();
    var mm = hoy.getMonth() + 1;
    var aaaa = hoy.getFullYear();

    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }

    var fecha = dd + '-' + mm + '-' + aaaa;

    return fecha;


}

/***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************
 * @author juan
 * @date 04/06/2018
 * @description Nuevo grid para la implementacion de nuevas tareas por medio de un bot de correo
 */
function CargaGridEntradaCorreo() {

    var ID_CORREO = -1;
    var tipos = [];
    var tiposcatalogacion = [];
    var usuariosoficina = [];
    var estado = ['Aceptado', 'Rechazado'];
    var prioridad = [];
    var selectestado = '';
    var correcto = false;
    var opcionvercorreo = true;
    var usuarios = [];
    var tipoplanificada = [];
    var selectplanta = [];
    var selectarea = [];
    var solicitado = [];
    var selectactividad = [];

    if (usuariovalidado == 'juanantonio.abellan') {
        opcionvercorreo = false;
    }

    $.ajax({
        cache: false,
        url: "select/getUsuariosLDAP.php",
        success: function (response) {

            usuarios = response.split("|");
        }
    });

    var autoCompleteEditor = function (ui) {
        var $cell = ui.$cell, rowData = ui.rowData, dataIndx = ui.dataIndx, width = ui.column.width, cls = ui.cls;
        var dc = $.trim(rowData[ dataIndx ]);

        var $inp = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-ac-editor' />").width(width - 6).appendTo($cell).val(dc);

        $inp.autocomplete({
            source: usuarios,
            minLength: 0
        }).focus(function () {
            $( this ).data( "autocomplete" ).search( $( this ).val( ) );
        });
    }

    var dateEditor = function (ui) {
        var $cell = ui.$cell,
                rowData = ui.rowData,
                dataIndx = ui.dataIndx,
                cls = ui.cls,
                dc = $.trim(rowData[dataIndx]);
        $cell.css('padding', '0');
        var $inp = $("<input  type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' />")
                .appendTo($cell)
                .val(dc).datepicker({
            changeMonth: true,
            changeYear: true,
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            firstDay: 1,
            dateFormat: "dd/mm/yy",
            onClose: function () {
                $inp.focus();
            }
        });
    }

    var dateEditor2 = function (ui) {
        var $cell = ui.$cell;
        var dataIndx = ui.dataIndx;
        var rowData = ui.rowData;
        var cls = ui.cls;
        var dc = $.trim(rowData[dataIndx]);
        var input = $("<input type='text' name='" + dataIndx + "' class='" + cls + " pq-date-editor' autocomplete='off'  />")
                .appendTo($cell)
                .val(dc).datetimepicker({
            datepicker: true,
            timepicker: false,
            format: 'd/m/Y',
            weeks: true,
            dayOfWeekStart: 1,
        });
    }

    // ************************************GRID
    // Datos generales.
    var obj = {
        width: '100%',
        height: '100%',
        title: "",
        hoverMode: 'row',
        selectionModel: {
            type: 'row'
        },
        editModel: {
            cellBorderWidth: 1,
            clicksToEdit: 2,
            pressToEdit: true,
            filterKeys: true,
            keyUpDown: true,
            saveKey: $.ui.keyCode.ENTER,
            onSave: null,
            onBlur: 'save',
            allowInvalid: false,
            invalidClass: 'pq-cell-red-tr pq-has-tooltip',
            warnClass: 'pq-cell-blue-tr pq-has-tooltip'
        },
        pageModel: {
            type: 'remote',
            rPP: 100,
            strRpp: "{0}",
            rPPOptions: [20, 100, 1000, 10000]
        },
        filterModel: {
            on: true,
            mode: "AND",
            header: true
        },
        scrollModel: {
            pace: 'fast',
            autoFit: true,
            theme: true
        },
        toggle: function (event, ui) {
            gridMaximice(event, ui);
        },
        cellSave: function (event, ui) {
            cellSave(event, ui);
        }
    };

    // EVENTOS
    obj.cellDblClick = function (event, obj) {
        cargardatos(obj);
    }
    obj.cellClick = function (event, obj) {
        cargardatos(obj);
    }

    // Columnas
    obj.colModel = [{
            title: "Id",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            dataType: "integer",
            editable: false,
            hidden: true,
            dataIndx: "Id"
        },
        {
            title: "Traspaso",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            dataType: "string",
            dataIndx: "Traspaso",
            editable: false,
            hidden: true,
        },
        // //////////////////////
        {
            title: "Solicitado",
            width: 130,
            minWidth: 130,
            maxWidth: 130,
            align: "left",
            dataType: "string",
            hidden: false,
            editable: true,
            dataIndx: "Solicitado",
            filter: {type: 'textbox', condition: 'contain'},
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Solicitado", $(this).val());
                        }
                    }]
            },
            editor: {
                type: autoCompleteEditor
            },
            validations: [{
                    type: 'minLen',
                    value: 5,
                    msg: "Required"
                }, {
                    type: function (ui) {
                        var value = ui.value;
                        if ($.inArray(ui.value, usuarios) == -1) {
                            ui.msg = value + " not found in list";
                            return false;
                        }
                    }
                }]
        }, {
            title: "Asunto",
            width: 255,
            minWidth: 275,
            maxWidth: 275,
            dataType: "string",
            dataIndx: "Asunto",
            editable: true,
            align: "left",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Asunto", $(this).val());
                        }
                    }]
            },
            render: function (ui) {
                if ((ui.rowData["Estado"] == 'Pendiente') || (ui.rowData["Estado"] == 'Rechazado')) {
                    var ruta;

                    if (ui.rowData["Estado"] == 'Pendiente') {
                        ruta = './mails/CorreosBOT/' + ui.rowData["RutaCorreo"] + '.eml';
                        //ruta = '//sp-berner.local/GestionDocumentalWeb/Tareas/CorreosBOT/'+  rowData["RutaCorreo"] + '.eml';
                    } else {
                        ruta = './mails/CorreosBOT/Rechazados/' + ui.rowData["RutaCorreo"] + '.eml';
                        //ruta = '//sp-berner.local/GestionDocumentalWeb/Tareas/CorreosBOT/Rechazados/' +  rowData["RutaCorreo"] + '.eml';
                    }

                    return "<button  style='width: 65px; height: 20px; margin-right:5px;' title='Ver correo " + ui.rowData["Id"] + " ' > " +
                            "<a style='width: 100%; height: 100px;  text-decoration: none;' href='" + ruta + "' > Ver Correo</a>" +
                            "</button>" + ui.rowData["Asunto"];
                }
            }
        }, {
            title: "Fecha Solicitud",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            align: "center",
            dataType: "string",
            dataIndx: "FechaSolicitud",
            editable: false,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("FechaSolicitud", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Fecha Objetivo",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            align: "center",
            dataType: "string",
            dataIndx: "FechaObjetivo",
            editable: true,
            editor: {
                type: dateEditor2
            },
            validations: [{
                    type: 'regexp',
                    value: '^$|[0-9]{2}/[0-9]{2}/[0-9]{4}',
                    msg: 'No en formato dd/mm/yyyy '
                }],
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("FechaObjetivo", $(this).val());
                        }
                    }]
            }
        },{
            title: "Fecha Planificada",
            width: 70,
            minWidth: 70,
            maxWidth: 70,
            align: "center",
            dataType: "string",
            dataIndx: "FechaPlanificada",
            editable: true,
            editor: {
                type: dateEditor2
            },
            validations: [{
                    type: 'regexp',
                    value: '^$|[0-9]{2}/[0-9]{2}/[0-9]{4}',
                    msg: 'No en formato dd/mm/yyyy '
                }],
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("FechaPlanificada", $(this).val());
                        }
                    }]
            }
        },{
            title: "Prioridad",
            width: 87,
            minWidth: 87,
            maxWidth: 87,
            align: "center",
            dataType: "string",
            editable: true,
            dataIndx: "Prioridad",
            editor: {
                type: 'select',
                options: function (ui) {
                    return prioridad;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Prioridad", $(this).val());
                        }
                    }]
            }
        },{
            title: "Mensaje Correo",
            width: 300,
            minWidth: 300,
            maxWidth: 300,
            align: "center",
            dataType: "string",
            dataIndx: "MensajeCorreo",
            editable: false,
            render: function (ui) {

                var men = limpiarTextoOffice(ui.rowData["MensajeCorreo"]);
                var termina = men.indexOf("-->");

                if (termina == -1) {
                    termina = 0;
                } else {
                    termina = termina + 3;
                }

                men = men.slice(termina, termina + 37);
                return men + "  <img src='imagenes/puntitos.png' width=\"18\" height=\"18\" style='float: right;'/>&nbsp; <span style='clear;'></span>";
            }
        },
        {
            title: "Asignado",
            width: 135,
            minWidth: 135,
            maxWidth: 135,
            align: "center",
            dataType: "string",
            editable: true,
            dataIndx: "Asignado",
            editor: {
                type: 'select',
                options: function (ui) {
                    return usuariosoficina;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Asignado", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Oficina",
            width: 400,
            minWidth: 200,
            maxWidth: 600,
            dataType: "string",
            editable: false,
            hidden: true,
            dataIndx: "Oficina",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Oficina", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Motivo Rechazo O Aceptacion",
            width: 165,
            minWidth: 165,
            maxWidth: 165,
            dataType: "string",
            dataIndx: "MotivoRechazoOAceptacion",
            editable: true,
        }, {
            title: "Tipo",
            width: 72,
            minWidth: 72,
            maxWidth: 72,
            align: "center",
            dataType: "string",
            dataIndx: "Tipo",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    return tipos;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Tipo", $(this).val());
                        }
                    }]
            }
        },{
            title: "Actividad",
            width: 72,
            minWidth: 72,
            maxWidth: 72,
            align: "center",
            dataType: "string",
            dataIndx: "Actividad",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    return selectactividad;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Actividad", $(this).val());
                        }
                    }]
            }
        },{
            title: "Catalogacion",
            width: 85,
            minWidth: 85,
            maxWidth: 85,
            align: "center",
            dataType: "string",
            dataIndx: "Catalogacion",
            editable: true,
            editor: {
                type: 'select',
                options: function (ui) {
                    return tiposcatalogacion;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Catalogacion", $(this).val());
                        }
                    }]
            }
        }
        , {
            title: "Horas",
            width: 50,
            minWidth: 50,
            maxWidth: 50,
            align: "center",
            dataType: "float",
            dataIndx: "Horas",
            editable: true,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Horas", $(this).val());
                        }
                    }]
            }
        }, {
            title: "No Planificado",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            dataType: "string",
            dataIndx: "NoPlanificado",
            editable: true,
            hidden: false,
            editor: {
                type: 'select',
                options: function (ui) {
                    return tipoplanificada;
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("NoPlanificado", $(this).val());
                        }
                    }]
            }
        }
        , {
            title: "Obligadas",
            dataType: "string",
            dataIndx: "Obligadas",
            hidden: true,
            editable: false,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Obligadas", $(this).val());
                        }
                    }]
            }
        }
        , {
            title: "Area Solicitante",
            width: 90,
            minWidth: 90,
            maxWidth: 90,
            dataType: "string",
            dataIndx: "AreaSolicitante",
            editor: {
                type: 'select',
                options: function (ui) {
                    return selectarea;
                }
            },
            hidden: false,
            editable: true,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("AreaSolicitante", $(this).val());
                        }
                    }]
            }
        }
        , {
            title: "Planta",
            width: 90,
            minWidth: 90,
            maxWidth: 90,
            dataType: "string",
            dataIndx: "Planta",
            editor: {
                type: 'select',
                options: function (ui) {
                    return selectplanta;
                }
            },
            hidden: false,
            editable: true,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Planta", $(this).val());
                        }
                    }]
            }
        }
        ,
        {
            title: "Estado",
            width: 80,
            minWidth: 80,
            maxWidth: 80,
            align: "center",
            dataType: "text",
            editable: false,
            dataIndx: "Estado",
            editor: {
                type: 'select',
                options: function (ui) {
                    return estado;
                }
            },
            render: function (ui) {
                if (ui.rowData["Estado"] == 'Aceptado' || ui.rowData["Estado"] == 'Rechazado') {
                    disabled = " disabled='disabled' ";
                    $grid.pqGrid({editable: false});
                }

                if (ui.rowData["Estado"] == 'Pendiente') {
                    $grid.pqGrid({editable: true});
                }
            },
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Estado", $(this).val());
                        }
                    }]
            }
        }, {
            title: "Correo Solicitado",
            width: 195,
            minWidth: 195,
            maxWidth: 195,
            dataType: "string",
            dataIndx: "SolicitadoEmail",
            editable: false,
            hidden: opcionvercorreo,
            align: "center",
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("SolicitadoEmail", $(this).val());
                        }
                    }]
            }
        },
        {
            title: "Usuario",
            width: 152,
            minWidth: 152,
            maxWidth: 152,
            align: "center",
            dataType: "string",
            dataIndx: "Usuario",
            editable: false,
            filter: {
                type: 'textbox',
                condition: 'contain',
                listeners: [{
                        change: function (evt, ui) {
                            filter("Usuario", $(this).val());
                        }
                    }]
            }
        }
    ];

    // BARRA DE TAREAS
    obj.render = function (evt, obj) {

        var $toolbar = $("<div class='pq-grid-toolbar pq-grid-toolbar-search'></div>").appendTo($(".pq-grid-top", this));
        // BOTON ELIMINAR PLANIFICACION
        $("<span data-texto='Aceptado' > Ver Aceptados </span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-check"
            }
        });

        $("<span data-texto='Rechazado'> Ver Rechazados </span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-circle-close"
            }
        });

        $("<span data-texto='Pendiente'> Ver Pendientes </span>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-folder-open"
            }
        });

        $("<button class='botoncorreo' data-tipobtn='Aceptado' style='margin-left:15px;' >Aceptar Tarea</button>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-check "
            },
        }).click(function (evt) {
            //aceptar tarea//
            comprobacionModEntradaCorreoTarea('Aceptado', $(this).attr("idcorreo"),encodeURIComponent(usuariovalidado));    
        });

        $("<button class='botoncorreo' data-tipobtn='Rechazado' >Rechazar Tarea</button>").appendTo($toolbar).button({
            icons: {
                primary: "ui-icon-closethick"
            },
        }).click(function (evt) {
            //rechazar tarea//
            comprobacionModEntradaCorreoTarea('Rechazado', $(this).attr("idcorreo"),encodeURIComponent(usuariovalidado));    
        });
    };

    // Datos
    obj.dataModel = {
        sorting: "remote",
        paging: "remote",
        curPage: 1,
        rPP: 20,
        sortIndx: ["Id"],
        sortDir: ["down"],
        location: "remote",
        dataType: "JSON",
        method: "GET",
        rPPOptions: [5, 10, 20, 30, 40, 50],
        url: "select/consultaEntradaCorreo.php?usuario=" + encodeURIComponent(usuariovalidado) + "&estado='Pendiente'",
        getData: function (dataJSON, textStatus, jqXHR) {
            var data = dataJSON.data;
            // spinner.stop( );
            return {
                curPage: dataJSON.curPage,
                totalRecords: dataJSON.totalRecords,
                data: dataJSON.data
            };
        }
    };

    var $grid = $("#miseccionentradacorreo_grid_array").pqGrid(obj);
    $grid.pqGrid("option", "scrollModel", {
        horizontal: true
    }, {
        vertical: true
    });

    $grid.on("click", ".pq-grid-toolbar > span", function () {
        var tipoconsulta = $(this).data("texto");
        selectestado = "'" + tipoconsulta + "'";
        $grid.pqGrid("option", "dataModel.url", "select/consultaEntradaCorreo.php?usuario=" + encodeURIComponent(usuariovalidado) + "&estado=" + selectestado);
        $grid.pqGrid("refreshDataAndView");
    });
   

    // GRABAR MODIFICACIONES
    function cellSave(evt, ui) {
        ID_CORREO = ui.rowData[ "Id" ];
        var target = document.getElementById('micuerpo');
        var spinner = new Spinner(opts).spin(target);

        var Campo = ui.dataIndx;
        var correo_id = ui.rowData[ "Id" ];
        var correo_estado = ui.rowData[ "Estado" ];
        var correo_asignado = ui.rowData[ "Asignado" ];
        var correo_prioridad = ui.rowData[ "Prioridad" ];
        var MotivoRechazoOAceptacion = ui.rowData[ "MotivoRechazoOAceptacion" ];
        var correo_tipo = ui.rowData[ "Tipo" ];
        var correo_actividad = ui.rowData[ "Actividad" ];
        var correo_usuario = ui.rowData[ "Usuario" ];
        var correo_horas = ui.rowData[ "Horas" ];
        var correo_categoria = ui.rowData[ "Catalogacion" ];
        var correo_planificado = ui.rowData["NoPlanificado"];
        var correo_fechaobjetivo = ui.rowData["FechaObjetivo"];
        var correo_areasolicitado = ui.rowData["AreaSolicitante"];
        var correo_planta = ui.rowData["Planta"];
        var correo_solicitado = ui.rowData["Solicitado"];
        var correo_asunto = ui.rowData["Asunto"];
        var fechaPlanificada = ui.rowData["FechaPlanificada"];

        if (correo_fechaobjetivo == ' ' || correo_fechaobjetivo == '') {
            correo_fechaobjetivo = 'NULL';
        }

        if (fechaPlanificada == ' ' || fechaPlanificada == '') {
            fechaPlanificada = 'NULL';
        }
        
        var url = "select/modificar_entradacorreo.php?Id=" + encodeURIComponent(correo_id)
                + "&Estado=" + encodeURIComponent(correo_estado)
                + "&Asignado=" + encodeURIComponent(correo_asignado)
                + "&Prioridad=" + encodeURIComponent(correo_prioridad)
                + "&MotivoRechazoOAceptacion=" + encodeURIComponent(MotivoRechazoOAceptacion)
                + "&Tipo=" + encodeURIComponent(correo_tipo)
                + "&Usuario=" + encodeURIComponent(correo_usuario)
                + "&Horas=" + encodeURIComponent(correo_horas)
                + "&Catalogacion=" + encodeURIComponent(correo_categoria)
                + "&planificado=" + encodeURIComponent(correo_planificado)
                + "&fechaobjetivo=" + encodeURIComponent(correo_fechaobjetivo)
                + "&areasolicitante=" + encodeURIComponent(correo_areasolicitado)
                + "&planta=" + encodeURIComponent(correo_planta)
                + "&solicitado=" + encodeURIComponent(correo_solicitado)
                + "&asunto=" + encodeURIComponent(correo_asunto)
                + "&Actividad=" + encodeURIComponent(correo_actividad)
                + "&FechaPlanificada=" + encodeURIComponent(fechaPlanificada)
                + "&tipoajax=" + 'guardarCelda';

        $.ajax({
            cache: false,
            async: true,
            url: url
        }).done(function (data) {
            spinner.stop( );
            $grid.pqGrid("refreshDataAndView");
            console.log(data);

        });

        ID_CORREO = -1;
    }


    function obtenerSituaciones(tarea) {
        var situaciones = [];
        $.ajax({
            url: "select/getTiposTipos.php?tipo=13&id=" + tarea,
            cache: false,
            async: false,
            success: function (response) {
                situaciones = response.split(",");

            }
        });

        return situaciones;
    }

    function getRowIndx() {
        alert('function getRowIndx()');
        var arr = $grid.pqGrid("selection", {
            type: 'row',
            method: 'getSelection'
        });
        if (arr && arr.length > 0) {
            var rowIndx = arr[0].rowIndx;
            return rowIndx;
        } else {

            return null;
        }
    }

    function filter(dataIndx, value) {
        $grid.pqGrid("filter", {
            data: [{
                    dataIndx: dataIndx,
                    value: value
                }]
        });
    }

    // ****************************************


    function creartareacorreobot(numerocorreo, correou) {
        var url = "select/insertar_incidencia_correo.php?usuario=" + encodeURIComponent(usuariovalidado) + "&idcorreo=" + numerocorreo + "&correou=" + correou;
        $.ajax({
            cache: false,
            url: url
        }).done(function () {
            $grid.pqGrid("refreshDataAndView");
            correcto = false;
        });
    }

    function cargardatos(obj) {
        var celda = obj.column.dataIndx;
        var idcorreo = obj.rowData["Id"];
        var oficinacorreo = obj.rowData["Oficina"];
        $("button.botoncorreo").attr("idcorreo", obj.rowData["Id"]);

        if (celda == 'Tipo') {
            $.ajax({
                url: "select/getTiposTipos.php?tipo=0&oficina=" + oficinacorreo,
                async: false,
                success: function (response) {
                    tipos = response.split(",");
                }
            });
        }

        if (celda == 'Actividad') {
            $.ajax({
                url: "select/getTiposTipos.php?tipo=106&oficina=" + oficinacorreo,
                async: false,
                success: function (response) {
                    selectactividad = response.split(",");
                }
            });
        }

        if (celda == 'Catalogacion') {
            $.ajax({
                url: "select/getTiposTipos.php?tipo=1&oficina=" + oficinacorreo,
                async: false,
                success: function (response) {
                    tiposcatalogacion = response.split(",");
                }
            });
        }

        if (celda == 'Prioridad') {
            $.ajax({
                url: "select/getTiposTipos.php?tipo=4&oficina=" + oficinacorreo,
                async: false,
                success: function (response) {
                    prioridad = response.split(",");
                }
            });
        }

        if (celda == 'Asignado') {
            $.ajax({
                url: "select/getUsuariosOficina.php?todos=si&idbotcorreo=" + idcorreo,
                async: false,
                success: function (response) {
                    response = '|'+ response;
                    usuariosoficina = response.split("|");
                }
            });
        }
        if (celda == 'NoPlanificado') {
            $.ajax({
                url: "select/getTiposTipos.php?tipo=16&oficina=TI",
                cache: false,
                async: false,
                success: function (response) {
                    tipoplanificada = response.split(",");
                }
            });
        }
        
        if (celda == 'Planta') {
            $.ajax({
                url: "select/getTiposTipos.php?tipo=15&oficina=TI",
                cache: false,
                async: false,
                success: function (response) {
                    selectplanta = response.split(",");
                }
            });
        }
        
        if (celda == 'AreaSolicitante') {
            $.ajax({
                url: "select/getTiposTipos.php?tipo=14&oficina=TI",
                cache: false,
                async: false,
                success: function (response) {
                    selectarea = response.split(",");
                }
            });
        }

        if (celda == 'MensajeCorreo') {

            var dialogo = document.getElementById('dialog-cancelar');

            $("#dialog-cancelar").dialog({
                resizable: false,
                dialogClass: 'no-close',
                title: 'Mensaje correo',
                width: $(document).width( ),
                height: $(document).height( ),
                left: '0px',
                top: '20px',
                modal: true,
                open: function (event, ui) {
                    var estadotiny = 0;

                    if (obj.rowData["Estado"] == "Aceptado" || obj.rowData["Estado"] == "Rechazado") {
                        estadotiny = 1;
                    }

                    $('#dialog-cancelar').html("<textarea id='EditorMedida' name='EditorMedida' style=' width: 100%; height:80%;'></textarea>");
                    $('#EditorMedida').val(obj.rowData["MensajeCorreo"]);

                    var alto = $(document).height( ) - 240;

                    // Inicializar Editor TinyMCE para editar el mensaje del correo.
                    tinymce.init({
                        mode: "textareas",
                        selector: '#EditorMedida',
                        theme: 'modern',
                        readonly: estadotiny,
                        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | l      ink image jbimages | print preview media fullpage | forecolor backcolor"
                    });

                },
                buttons: {
                    "Cerrar": function () {
                        $(this).dialog("close");
                    },
                    "Guardar": function () {

                        var ed = tinymce.get('EditorMedida');
                        var mensajetiny = ed.getContent();

                        $.ajax({
                            url: "select/guardarMensajeCorreo.php",
                            type: "POST",
                            async: false,
                            data: {
                                "id": idcorreo,
                                "mensajetiny": mensajetiny
                            },
                            success: function (response) {
                                mensaje("Info", "Se ha guardado con exito");
                                $("#dialog-cancelar").dialog("close");
                                $grid.pqGrid("refreshDataAndView");
                            }
                        });
                    }
                }
            });
        }
    }

    function comprobacionModEntradaCorreoTarea(tipo, numero, usuariovalida){
        $.ajax({
            url: "select/modificar_entradacorreo.php",
            type: "GET",
            async: false,
            data: {
                "Id": numero,
                "estadocomprobacion": tipo,
                "Usuario": usuariovalida,
                "tipoajax": 'comprobacionCorreo'
            },
            success: function (response) {
                
                console.log(response);

                if (response == 1){
                    mensaje("Info", "Se ha creado la tarea con exito.");
                }else{ 
                    if(response == 2){
                    mensaje("Info", "Se ha rechazado la tarea con exito.");
                    }else{
                        mensaje("Error", response); 
                        
                    }
                }
                $grid.pqGrid("refreshDataAndView");
            }
        });
    }


}
/***
 * Alberto
 * @param texto
 * @returns
 */
function limpiarTextoOffice(texto) {
    var tmp = document.createElement("DIV");
    tmp.innerHTML = texto;
    var textoLimpio = tmp.textContent || tmp.innerText;
    textoLimpio = textoLimpio.replace(/\n\n/g, "<br />").replace(/.*<!--.*-->/g, "");
    for (i = 0; i < 10; i++) {
        if (textoLimpio.substr(0, 6) == "<br />") {
            textoLimpio = textoLimpio.replace("<br />", "");
        }
    }
    return textoLimpio;
}

/***
 * Juan
 * @param idtarea
 * @returns
 */
function crearPlanificacionRapida(idtarea) {
    var target = document.getElementById('micuerpo');
    var spinner = new Spinner(opts).spin(target);

    $.ajax({
        cache: false,
        async: true,
        type: 'POST',
        url: "select/CreacionPlanificadorRapida.php",
        data: {
            idtarea: idtarea
        },
    }).done(function (data) {
        if (data == 1) {
            mensaje('Error', 'El campo asignado es obligatorio para poder crear la planificacion rapida.');
        }
        if (data == 2) {
            mensaje('Error', 'Ya tiene una planificación.');
        }
        if (data == 3) {
            mensaje('Correcto', 'Se ha planificado correctamente.');
        }

        spinner.stop( );
        //$grid.pqGrid( "refreshDataAndView" ); //Alberto: Esto no hace falta y da error, lo quito
    });


}

/***
 * Alberto
 * @date 29/04/2020
 * @returns
 */
function actualizar_banner_tareas() {
    $.ajax({
        cache: false,
        async: true,
        type: 'POST',
        url: "select/consultaBannerTareas.php",
        data: {
            usuario: usuariovalidado
        },
    }).done(function (data) {

        var res = data.split("@@@");
        //document.getElementById("resumen_tareas").style.right="calc(2% + 839px)";
        if (isNaN(res[0])) {
            //no hacemos nada será un error
        } else {
            // Cantidad de cuñas
            if (res[0] > 0) {
                $("#btnPlanificador").addClass("rojo");
            } else {
                $("#btnPlanificador").removeClass("rojo");
            }

            // Banner de contadores de tareas
            if (res[1] == '0') {
                document.getElementById("resumen_tareas").style.display = "none";
                document.getElementById("resumen_frecuentes").style.right = "calc(2% + 702px)";
            } else {
                document.getElementById("resumen_tareas").style.display = "block";
                document.getElementById("resumen_frecuentes").style.right = "calc(2% + 839px)";
                $("#resumen_tareas").html(res[1]);
            }

            // Título de tarea actual
            if (res[3] != '') {
                $("#cabecera_titulo_tarea").html(res[3]);
                titulo = $("#cabecera_titulo_tarea_inner").text();
                document.title = titulo.toUpperCase();
                /*				
                 if(usuariovalidado == 'alberto.ruiz'){
                 b = titulo.split(" - ");
                 document.title = b[1].toUpperCase();
                 }
                 */
            } else {
                $("#cabecera_titulo_tarea").html('');
                document.title = "GESTIÓN DE TAREAS";
            }

            // Avisos
            var avi = res[2].split('###');
            if (avi[0] != '' && !isNaN(avi[0])) {

                var mensaje = avi[2];
                $("#dialog-notificar").html('<p style="text-align:justify"><span class="ui-icon-circle-close" style="float:left; margin:0 7px 20px 0;"></span>' + "<img src='imagenes/planificador.png' width=\"50\" height=\"50\" />" + mensaje);
                $("#dialog-notificar").dialog({
                    title: avi[1],
                    resizable: false,
                    height: "auto",
                    width: 650,
                    modal: true,
                    buttons: {
                        "CERRAR": function () {
                            $(this).dialog("close");
                        },
                        "VER TAREA": function () {
                            ID_SELECCIONADA = avi[0];
                            cambia_menu(3);
                        },
                        "DESCARTAR": function () {
                            $.ajax({
                                cache: false,
                                async: true,
                                method: "POST",
                                url: "select/descartarAlerta.php",
                                data: {
                                    id: avi[3],
                                    postponer: 0
                                }
                            }).done(function (data) {
                                actualizar_banner_tareas();
                            });

                            $(this).dialog("close");
                        },
                        "RECORDAR MAÑANA": function () {
                            $.ajax({
                                cache: false,
                                async: true,
                                method: "POST",
                                url: "select/descartarAlerta.php",
                                data: {
                                    id: avi[3],
                                    postponer: 1
                                }
                            }).done(function (data) {
                                actualizar_banner_tareas();
                            });

                            $(this).dialog("close");
                        }
                    }
                });
            }

        }





    });
}
