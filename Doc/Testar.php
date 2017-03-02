<?php
if(APLICACAO_PRODUCAO == 1){
    echo "<h1> PAGINA DESABILITADA POR MOTIVOS DE SEGURANÇA </h1>";
}else{
    include('Funcoes/ListarProcesso.php');

    $percorrerArquivo = new PercorrerArquivo();

    $percorrerArquivo->percorrer(diretorio);

    $array = RepositorioProcesso::get();
    $modulo = RepositorioProcesso::getModulo();

    ksort($modulo);
    foreach ($array as $key => $valor){
        ksort($array[$key]);
    }

    ?>

    <div id="funcionalidade">
        <div id="select">

        </div>
        <div id="opcoes">

        </div>
    </div>
    <div id="conteudo">
        <h2> </h2>
        <div>
            <h3> Processo: </h3>

            <form id="form" name="form" method="POST" action="javascript:enviar();"  enctype='multipart/form-data'>


            </form>
        </div>
        <div id="comentario">
            <h3> Comentário: </h3>
            <div>
                <pre>
                </pre>
            </div>
        </div>

        <div id="exibicao">
            <h3> Resultado: </h3>
            <div id="resultado">
                <pre>
                </pre>
            </div>
        </div>
    </div>
    <script>
    var DGlobal = JSON.parse('<?php echo  json_encode($array) ?>');
    var Modulo = JSON.parse('<?php echo  json_encode($modulo) ?>');

    var processo = '';
    var ModuloAtual = Modulo[0];

    function enviar(){
        var dados = $('#form').serialize();
        console.log(processo,ModuloAtual,processo ,DGlobal[processo].url);
        //$('#resultado pre').html('');
        GCS.conectar('post',
                     'http://<?php echo URL_PROJETO ?>/api/Teste/'+DGlobal[processo].url+'?ambTeste=1',
                     dados,
                     acao,1,'form');
    }

    function acao(dados){
        console.log(dados);
        $('#exibicao pre').html(dados);
    }

    var conteudo;

    function montar(proc){
        processo = proc;
        conteudo = '';
        $('#conteudo h2').html(proc);
        $('#exibicao pre').html('');
        $('#comentario pre').html(DGlobal[ModuloAtual][processo].comentario);

        $.each(DGlobal[ModuloAtual][processo].campos, function(indice, valor) {
            if(valor['array'] == 1){
                conteudo += '<div>'+valor['nome']+': <input type="type" name="'+valor['nome']+'[]" /> </div>';
            }else if(valor['json'] == 1){
                conteudo += '<div>'+valor['nome']+': <textarea name="'+valor['nome']+'"> </textarea> </div>';
            }else if(String(valor['type']) != 'undefined')
                conteudo += '<div>'+valor['nome']+': <input type="'+valor['type']+'" name="'+valor['nome']+'" /> </div>';
            else if(valor['chave'] === 'entidade')
                conteudo += '<div>'+valor['nome']+': <input type="hidden" name="'+valor['nome']+'" /> </div>';
            else conteudo += '<div>'+valor['nome']+': <input type="text" name="'+valor['nome']+'" /> </div>';
        });
        conteudo += '<div> <input type="submit" id="test" value="Execultar"/> </div>';
        $('#form').html(conteudo);
    }

    function montarFuncinalidade(){
        var conteudo = '<div><select id="modulo" onchange="javascript:mudarModulo();">';
        $.each(Modulo, function(indice, valor) {
           conteudo += '<option value="'+indice+'" >'+valor+'</option>';
        });
        conteudo += '</select> </div>';
        $('#select').html(conteudo);
        montarOpcoes();
    }

    function  montarOpcoes(){
        var conteudo = '';
         $.each(DGlobal[ModuloAtual], function(indice, valor) {
           conteudo += '<a href="javascript:montar(\''+indice+'\');" >'+indice+'</a> <br/>';
        });
        $('#opcoes').html(conteudo);
    }

    function mudarModulo(){
        ModuloAtual = Modulo[$('#modulo').val()];
         montarOpcoes();
    }

    montarFuncinalidade();
    </script>
    <?php
}
