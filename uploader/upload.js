function AdjuntaDocumentos(id){

    var input = document.getElementById('adjuntos'),
        formdata = false;
    
	var usuario_logeado = usuariovalidado;
	
    //Revisamos si el navegador soporta el objeto FormData
    if(window.FormData){
        formdata = new FormData();
        document.getElementById('btnAdjunto').style.display = 'none';		
    }    
    //Aplicamos la subida de im�genes al evento change del input file
    if(input.addEventListener){
        input.addEventListener('change', function(evt){
            var i = 0, len = this.files.length, img, reader, file;
            
            document.getElementById('response').innerHTML = 'Subiendo...';
            
            //Si hay varias im�genes, las obtenemos una a una
            for( ; i < len; i++){
                file = this.files[i];
                                
				//Si el navegador soporta el objeto FileReader
				if(window.FileReader){
					reader = new FileReader();
					//Llamamos a este evento cuando la lectura del archivo es completa
					//Despu�s agregamos la imagen en una lista
					reader.onloadend = function(e){
						//alert(e.target.result);
						//Lo metemos en la lista!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					};
					//Comienza a leer el archivo
					//Cuando termina el evento onloadend es llamado
					reader.readAsDataURL(file);
				}
				
				//Si existe una instancia de FormData
				if(formdata)
					//Usamos el m�todo append, cuyos par�metros son:
						//name : El nombre del campo
						//value: El valor del campo (puede ser de tipo Blob, File e incluso string)
					formdata.append('adjuntos[]', file);
               
            }
            
            //Por �ltimo hacemos uso del m�todo proporcionado por jQuery para hacer la petici�n ajax
            //Como datos a enviar, el objeto FormData que contiene la informaci�n de las im�genes
            if(formdata){
			
                $.ajax({
                   url : 'uploader/upload.php?id='+id+'&usuario='+usuario_logeado,
                   type : 'POST',
                   data : formdata,
                   processData : false, 
                   contentType : false, 
                   success : function(res){
				       agregar_linea(res);
                       document.getElementById('response').innerHTML = "";
                   }                
                });
            }
        }, false);
    }
};