 <div>
    <h3> Upload:  </h3>
    <!--URL: <span style="color:#777">{{Modulo}}</span> / <span style="color:#777">{{Processo}}</span> /imagem?id=
    -->
    <form id="form" name="form" method="POST" enctype='multipart/form-data'>
        <!--<div>Imagem Upada: 
            <select id="tipoImagem">
                <option value="1" texto="Publicacao"> Publicacao </option> 
                <option value="2" texto="VolumePublicacao"> VolumePublicacao </option> 
                <option value="3" texto="Aluno"> Aluno </option> 
            </select> 
        </div>
        <div> Imagem <input onchange="javascript:upload()" name="imagem" type="file" /> </div>    
        -->
        
        <div> Arquivo <input onchange="javascript:upload()" name="arquivo" type="file" /> </div>
    </form>
</div>
    
<div id="exibicao"> 
    <h3> Resultado: </h3>
    <div id="resultado"> 
        <pre>

        </pre>
    </div>
</div>
  
<script> 

    /*function upload() {
        var processo;
        var id = $('#tipoImagem').val();
        switch(id){
            case '1':
                processo = 'Publicacao';
                break;
            case '2':
                processo = 'VolumePublicacao';
                break;
            case '3':
                processo = 'Aluno';
                break;
        }
    
        GCS.conectar('post','http://inedu.dev.codevip.com.br/api/Teste/Upload/imagem?id='+id,null, acao, 1,'form');
    }*/
    
    function upload() {
        GCS.conectar('post','http://192.168.0.95:8080/teste.php',null, acao, 1,'form');
    }
    
    function acao(dados){
        console.log(dados);
        $('#exibicao pre').html(dados);
    }

</script>


