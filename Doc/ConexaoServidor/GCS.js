/*
 * responsável por fazer a conexão com o servidor
 * neste caso usando a função da biblioteca JQuery
 */

function  GCS(){
    function conectar(tipo, url, dados, acao,tipoEnvio,idForm){
       
        if(parseInt(tipoEnvio) === 1){
            var options = {
                 success: acao,
                 url: url,
                 type: 'post',                 
                 clearForm: false
            };
            $('#'+idForm).ajaxSubmit(options);
        }else{
            $.ajax({
                type: tipo,
                url: url,
                crossDomain: true,
                cache: false,
                data: dados,
                success: acao
            });
            
        }
    }
    
    return {
        conectar: conectar
    };
}

var GCS = GCS();

