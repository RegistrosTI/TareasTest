<table class="displayTable teletrabajo-table">
    <tr>
        <th>Fecha:</th>
        <th>Hora inicio tarea</th>
        <th>Hora finalización tarea</th>
        <th>Total Horas</th>
        <th>Tipologia de la tareas realizada</th>
        <!-- <th>Planificada</th> -->
        <th>Periodicidad</th>
        <th>Enunciado - Objetivo tarea realizada</th>
        <!-- <th>Resultado</th> -->
        <th>Breve descripción del resultado alcanzado</th>
        <th>Guardar</th>
    </tr>
    <tr>
        <td><input class="teletrabajo-input" id="fecha" name="fecha" type="text" placeholder="Fecha" disabled></td>
        <td><input class="teletrabajo-input"id="horaInicio" name="horaInicio" type="time" placeholder="Hora inicio"></td>
        <td><input class="teletrabajo-input"id="horaFinalizacion" name="horaFinalizacion" type="time" placeholder="Hora finalización"></td>
        <td><input class="teletrabajo-input"id="totalHoras" name="totalHoras" type="text" placeholder="Total Horas" disabled></td>
        <td>
            <select class="teletrabajo-select" id="tipologiaTareaRealizada" name="tipologiaTareaRealizada">
                <option class="teletrabajo-option" value="Gestión">Gestión</option>
                <option class="teletrabajo-option" value="Reunión externa">Reunión externa</option>
                <option class="teletrabajo-option" value="Reunión interna">Reunión interna</option>
                <option class="teletrabajo-option" value="Llamada telefónica">Llamada telefónica</option>
            </select>
        </td>
        <!-- Baja del campo por petición Personas -->
        <!-- <td>
            <select class="teletrabajo-select" id="planificada" name="planificada">
                <option class="teletrabajo-option" value="SI">SI</option>
                <option class="teletrabajo-option" value="NO">NO</option>
            </select>
        </td> -->
        <td>
            <select class="teletrabajo-select" id="periodicidad" name="periodicidad">
                <option class="teletrabajo-option" value="Anual">Anual</option>
                <option class="teletrabajo-option" value="Mensual">Mensual</option>
                <option class="teletrabajo-option" value="Semanal">Semanal</option>
                <option class="teletrabajo-option" value="Diaria">Diaria</option>
                <option class="teletrabajo-option" value="Puntual">Puntual</option>
            </select>
        </td>
        <td><input class="teletrabajo-input" id="objetivoTarea" name="objetivoTarea" type="text" placeholder="Enunciado - Objetivo tarea realizada" maxlength="50"></td>
        <!-- Baja del campo por petición Personas -->
        <!-- <td>
            <select class="teletrabajo-select" id="resultado" name="resultado">
                <option class="teletrabajo-option" value="Finalizado">Finalizado</option>
                <option class="teletrabajo-option" value="Pendiente">Pendiente</option>
            </select>
        </td> -->
        <td><input class="teletrabajo-input" id="resultadoAlcanzado" name="resultadoAlcanzado" type="text" placeholder="Breve descripción del resultado alcanzado" maxlength="80"></td>
        <td><input id="btnTeletrabajo" class="teletrabajo-input greenbackcolor" type="button" value="+" onclick="sendDataClickTeletrabajo()"></td>
    </tr>
</table>

<div class="display">
    <span>No se puede visualizar en pantallas pequeñas. disculpe las molestias</span>
</div>

<div id="tableContainer"></div>


 <script type="text/javascript" src="teletrabajo/assets/js/teletrabajo.js"></script>
    