/*
    |--------------------------------------------------------------------------
    | Cargado de estilos en cabecera principal
    |--------------------------------------------------------------------------
    |
    | Esta seccion totalmente independiente del resto del proyecto exepto 
    | por la pantalla que usamos del menu principal del portal de tareas por
    | este motivo y para mantener todo el proyecto separado inyectamos el css desde
    | aqui.
    |
    */

    (function () {
      var linkElement = document.createElement("link");
      linkElement.rel = "stylesheet";
      linkElement.href = "teletrabajo/assets/css/teletrabajo.css";
    
      document.head.appendChild(linkElement);
    })();
    
    /*
        |--------------------------------------------------------------------------
        | Cargado de fecha del dia de hoy
        |--------------------------------------------------------------------------
        |
        | No se podra imputar tareas que no sean del dia de hoy
        | 
        |
        */
    
    function today() {
      let fecha = document.getElementById("fecha");
      const date = new Date();
    
      let day = date.getDate();
      let month = date.getMonth() + 1;
      let year = date.getFullYear();
    
      let currentDate = `${day}/${month}/${year}`;
      // let currentDate = `${year}/${month}/${day}`;
      fecha.value = currentDate;
      getTodayData(currentDate);
    }
    
    /*
        |--------------------------------------------------------------------------
        | Envio de datos
        |--------------------------------------------------------------------------
        |
        | Envio de datos por POST hacia teletrabajoData.php para ser guardados
        | en base de datos.
        | 
        |const resultado               = document.getElementById('resultado');
        |const planificada             = document.getElementById("planificada");
        |planificada            : planificada.value,
        |resultado              : resultado.value,
        */
    
    function sendDataClickTeletrabajo() {
      const fecha = document.getElementById("fecha");
      const horaInicio = document.getElementById("horaInicio");
      const horaFinalizacion = document.getElementById("horaFinalizacion");
      const totalHoras = document.getElementById("totalHoras");
      const tipologiaTareaRealizada = document.getElementById(
        "tipologiaTareaRealizada"
      );
      const periodicidad = document.getElementById("periodicidad");
      const objetivoTarea = document.getElementById("objetivoTarea");
      const resultadoAlcanzado = document.getElementById("resultadoAlcanzado");
    
      const data = {
        fecha: fecha.value,
        horaInicio: horaInicio.value,
        horaFinalizacion: horaFinalizacion.value,
        totalHoras: totalHoras.value,
        tipologiaTareaRealizada: tipologiaTareaRealizada.value,
        periodicidad: periodicidad.value,
        objetivoTarea: objetivoTarea.value,
        resultadoAlcanzado: resultadoAlcanzado.value,
      };
    
      let valida = validacionesTeletrabajo(data);
      ftotalHoras(horaInicio, horaFinalizacion);
    
      if (valida) {
        const xhr = new XMLHttpRequest();
    
        xhr.open("POST", "teletrabajo/Insertar_teletrabajo.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");
    
        const jsonData = JSON.stringify(data);
    
        xhr.onload = function () {
          if (xhr.status === 200) {
            let obj = JSON.parse(xhr.responseText);
            obj = JSON.parse(obj);
            var res = [];
    
            for (var i in obj) res.push(obj[i]);
    
            res = res[0];
    
            renderTable(res);
          } else {
            console.log("Error:", xhr.status);
          }
        };
    
        xhr.send(jsonData);
      } else {
        alert("Todos los campos son necesarios para imputar una nueva tarea.");
      }
    }
    
    /*
        |--------------------------------------------------------------------------
        | Comprobaciones fechas Inicio y Finalización.
        |--------------------------------------------------------------------------
        |
        | Comprobacion fecha tienen contenido antes de calcular horas totales.
        | Comprobacion que las fecha de inicio no sea superior a la de finalización.
        | 
        |
        |
        */
    
    document.getElementById("horaInicio").addEventListener("change", function () {
      let horaFinalizacion = document.getElementById("horaFinalizacion");
      if (horaFinalizacion.value != "") {
        let horaInicio = document.getElementById("horaInicio");
        ftotalHoras(horaInicio, horaFinalizacion);
        today();
      }
    });
    document
      .getElementById("horaFinalizacion")
      .addEventListener("change", function () {
        let horaInicio = document.getElementById("horaInicio");
        if (horaInicio.value != "") {
          let horaFinalizacion = document.getElementById("horaFinalizacion");
          ftotalHoras(horaInicio, horaFinalizacion);
          today();
        }
      });
    
    /*
        |--------------------------------------------------------------------------
        | Calculo Total horas.
        |--------------------------------------------------------------------------
        |
        | Calculo dadas las dos fechas del tiempo en horas de la tarea.
        | funciones invocadoras[
        | Event listener de fechas, 
        | sendDataClick(),
        | ];
        */
    
    function ftotalHoras(horaInicio, horaFinalizacion) {
      let totalHoras = document.getElementById("totalHoras");
      const horaInicioArray = horaInicio.value.split(":");
      const horaFinalizacionArray = horaFinalizacion.value.split(":");
      if (
        horaFinalizacionArray[0] < horaInicioArray[0] ||
        (horaFinalizacionArray[0] == horaInicioArray[0] &&
          horaFinalizacionArray[1] <= horaInicioArray[1])
      ) {
        horaInicio.value = "";
        horaFinalizacion.value = "";
        alert(
          "La hora de inicio de la tarea no puede ser superior ni igual a la hora de finalización"
        );
      } else {
        hora = (horaFinalizacionArray[0] - horaInicioArray[0]) * 60;
        minuto = horaFinalizacionArray[1] - horaInicioArray[1];
        tiempo = (hora + minuto) / 60;
        totalHoras.value = tiempo.toFixed(2);
      }
    }
    
    /*
        |--------------------------------------------------------------------------
        | Validaciones inputs.
        |--------------------------------------------------------------------------
        |
        | Todos los campos son required. 
        | Return desde funcion invocadora: sendDataClick()
        | 
        |
        |
        */
    function validacionesTeletrabajo(obj) {
      let rtn = true;
      Object.keys(obj).forEach((key) => {
        console.log(key, obj[key]);
        if (obj[key] == "" || obj[key] == undefined) {
          rtn = false;
        }
      });
      return rtn;
    }
    
        /*
        |--------------------------------------------------------------------------
        | Renderizar tabla
        |--------------------------------------------------------------------------
        |
        | Mostramos todos los datos.
        | 
        | 
        |
        |
        */
    
    function renderTable(data) {
      const tableContainer = document.getElementById("tableContainer");
    
      if (tableContainer.hasChildNodes()) {
        while (tableContainer.firstChild) {
          tableContainer.removeChild(tableContainer.firstChild);
        }
      }
    
      const table = document.createElement("table");
      table.classList.add("displayTableResults");
    
      const tbody = document.createElement("tbody");
    
      const headerRow = document.createElement("tr");
      if (data !== undefined && data.length > 0) {
        const keys = Object.keys(data[0]);
    
        keys.forEach((key) => {
        //Cambios campos planificada y resultado quedan fuera.
          if (key !== "Planificada" && key !== "Resultado") {
            const th = document.createElement("th");
            th.textContent = key;
            headerRow.appendChild(th);
          }
        });
    
        const actionHeader = document.createElement("th");
        actionHeader.textContent = "Actions";
        headerRow.appendChild(actionHeader);
    
        table.appendChild(tbody);
    
        tbody.appendChild(headerRow);
    
        data.forEach((item) => {
          const row = document.createElement("tr");
    
          keys.forEach((key) => {
            //Cambios campos planificada y resultado quedan fuera.
            if (key !== "Planificada" && key !== "Resultado") {
              const td = document.createElement("td");
              td.textContent = item[key];
              row.appendChild(td);
            }
          });
    
          const button = document.createElement("button");
          button.textContent = "Borrar";
          button.className = "action-button";
          button.classList.add("redbackcolor");
    
          button.addEventListener("click", () => {
            handleButtonClick(item, row);
          });
    
          const actionCell = document.createElement("td");
          actionCell.appendChild(button);
          row.appendChild(actionCell);
    
          tbody.appendChild(row);
        });
    
        tableContainer.appendChild(table);
      }
    }
    
    /*
      |--------------------------------------------------------------------------
      | Pregunta seguridad y borrado visual del row 
      |--------------------------------------------------------------------------
      |
      |  ¿Esta seguro que deseas eliminar esta linea?
      |  Borrado visual del nodo child especifico. 
      | 
      |
      |
      */
    
    function handleButtonClick(rowData, row) {
      if (confirm("¿Esta seguro que deseas eliminar esta linea?")) {
        deleteTodayData(rowData);
        row.parentNode.removeChild(row);
      }
      //alert(`Button clicked for row: ${JSON.stringify(rowData)}`);
    }
    
    /*
      |--------------------------------------------------------------------------
      | Llamada de datos 
      |--------------------------------------------------------------------------
      |
      | Mostramos todos los datos 
      | 
      | 
      |
      */
    
    function getTodayData(currentDate) {
      const data = {
        fecha: currentDate,
      };
      const xhr = new XMLHttpRequest();
    
      xhr.open("POST", "teletrabajo/Obtener_teletrabajo.php", true);
      xhr.setRequestHeader("Content-Type", "application/json");
    
      const jsonData = JSON.stringify(data);
    
      xhr.onload = function () {
        if (xhr.status === 200) {
          let obj = JSON.parse(xhr.responseText);
          obj = JSON.parse(obj);
          var res = [];
    
          for (var i in obj) res.push(obj[i]);
    
          res = res[0];
    
          renderTable(res);
        } else {
          console.log("Error:", xhr.status);
        }
      };
    
      xhr.send(jsonData);
    }
    
    /*
        |--------------------------------------------------------------------------
        | Llamada de datos 
        |--------------------------------------------------------------------------
        |
        | Mostramos todos los datos 
        | 
        | 
        |
        |
        */
    
    function deleteTodayData(rowData) {
      const xhr = new XMLHttpRequest();
 
      xhr.open("POST", "teletrabajo/Borrar_teletrabajo.php", true);
      xhr.setRequestHeader("Content-Type", "application/json");
    
      const jsonData = JSON.stringify(rowData);
    
      xhr.onload = function () {
        if (xhr.status === 200) {
          console.log(xhr.responseText);
          let obj = JSON.parse(xhr.responseText);
          //obj = JSON.parse(obj);
          var res = [];
    
          for (var i in obj) res.push(obj[i]);
    
          res = res[0];
        } else {
          console.log("Error:", xhr.status);
        }
      };
    
      xhr.send(jsonData);
    }
    