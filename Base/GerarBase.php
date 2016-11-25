<?php

function padrao($varlo1,$valor2){
    if(!is_null($varlo1) && !empty($varlo1) && $varlo1 !==false)
        return rtrim($varlo1);
    else return $valor2;    
}

function gerar($pasta, &$mapeamentoEntidade){
  
    $xml = simplexml_load_file($pasta.'XMLBDClass.xml');

$qtdClasse = count($xml->classe);
$sql = '';
$mapXml = '';
$arrayPHP = array();
$dependencias = '';
for($i=0;$i<$qtdClasse;$i++){
    $atributos = array();
    $metodos = array();
    $parametrosTabela = array();
    $php = '';
    $nomeArq = rtrim($xml->classe[$i]["nome"]).'.php';
    $mapeamentoEntidade[] = '"'.rtrim($xml->classe[$i]["nome"]).'" :'.'"\\'.rtrim($xml->classe[$i]["namespace"]).'\\'.rtrim($xml->classe[$i]["nome"]).'"'; 
    $constant = array( 'valor' => rtrim($xml->classe[$i]->const['valor']),
                       'propriedade' =>  rtrim($xml->classe[$i]->const['atributoClasse']),
                       'atributo' =>  rtrim($xml->classe[$i]->const['atributo']));
    $sql .= '
        
CREATE TABLE `'.rtrim($xml->classe[$i]["tabela"]).'`( ';
    $qtdPropriedade = count($xml->classe[$i]->propriedades);
    $mapXml .= '
        
<classe nome="'.rtrim($xml->classe[$i]["nome"]).'" tabela="'.rtrim($xml->classe[$i]["tabela"]).'"> ';
    
    $php .= '<?php
        
namespace '.rtrim($xml->classe[$i]["namespace"]).';
use Rubeus\ORM\Persistente as Persistente;

    class '.rtrim($xml->classe[$i]["nome"]).' extends Persistente{';
    
    $extDep = 0;
    
    for($j=0;$j<$qtdPropriedade;$j++){
        
        $parametrosTabela[] = "
    `".rtrim($xml->classe[$i]->propriedades[$j]["coluna"])."` ".
                padrao(rtrim($xml->classe[$i]->propriedades[$j]['type']),'INT')." ".
                (rtrim($xml->classe[$i]->propriedades[$j]['Null']) == 'true' ? 'NOT NULL':'')." ".
                (rtrim($xml->classe[$i]->propriedades[$j]['autoIncrement']) == 'true' ? 'AUTO_INCREMENT':'')." ";
        
        
        $atributos[] = '
        private $'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).' = false;';
        
        if(rtrim($xml->classe[$i]->propriedades[$j]['primaryKey']) == 'true'){
            $parametrosTabela[] = '
     PRIMARY KEY (`'.rtrim($xml->classe[$i]->propriedades[$j]["coluna"]).'`)';
        }
        
        if(isset($xml->classe[$i]->propriedades[$j]['tabela'])){
            $parametrosTabela[] = '
    INDEX `'.rtrim($xml->classe[$i]["tabela"]).'_fk_'.rtrim($xml->classe[$i]->propriedades[$j]['coluna']).'_idx`'.
                                        '(`'.rtrim($xml->classe[$i]->propriedades[$j]["coluna"]).'` ASC),
    CONSTRAINT `'.rtrim($xml->classe[$i]["tabela"]).'_fk_'.rtrim($xml->classe[$i]->propriedades[$j]['coluna']).'` 
         FOREIGN KEY (`'.rtrim($xml->classe[$i]->propriedades[$j]["coluna"]).'`) REFERENCES `'.str_replace('.','`.`',rtrim($xml->classe[$i]->propriedades[$j]['tabela'])).'` (`id`)
     ON DELETE NO ACTION
     ON UPDATE NO ACTION';
            
            $mapXml .= '
    <nparaum coluna="'.rtrim($xml->classe[$i]->propriedades[$j]["coluna"]).'" '.
                           ' atributo="'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).'" />  ';  
         
        if(rtrim($xml->classe[$i]->propriedades[$j]['dependencia'])==1){
            if($extDep==0){
                 $dependencias .= '
    <classe nome="'.rtrim($xml->classe[$i]["nome"]).'">';
                 $extDep = 1;  
            }
            $dependencias .= '
        <'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).' value="'.rtrim($xml->classe[$i]->propriedades[$j]["classe"]).'" />';
            $dependencia = 'if(!$this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).')
                $this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).' = $this->getDependencia(\''.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).'\');';
        }else{
            if($constant['propriedade'] == rtrim($xml->classe[$i]->propriedades[$j]["atributo"])){
                $dependencia = 'if(!$this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).'){
                    $this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).' = new '.rtrim($xml->classe[$i]->propriedades[$j]["classe"]).'();
                    $this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).'->set('.$constant['atributo'].','.$constant['valor'].');
                }';
            }else{
                 $dependencia = 'if(!$this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).')
                    $this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).' = new '.rtrim($xml->classe[$i]->propriedades[$j]["classe"]).'();';
            }
        }
        
        $metodos[] = ' 
            
        public function get'.ucfirst(rtrim($xml->classe[$i]->propriedades[$j]["atributo"])).'() {
            '.$dependencia.' 
            return $this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).';
        }

        public function set'.ucfirst(rtrim($xml->classe[$i]->propriedades[$j]["atributo"])).'($'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).') {
            if($'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).' instanceof '.rtrim($xml->classe[$i]->propriedades[$j]["classe"]).')
                $this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).' = $'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).';
            else $this->get'.ucfirst(rtrim($xml->classe[$i]->propriedades[$j]["atributo"])).'()->setId($'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).');
        }';
        
            
        }else{
            if(rtrim($xml->classe[$i]->propriedades[$j]['primaryKey']) == 'true'){
                $mapXml .= '
    <id coluna="id" atributo="id">
        <gerador classe="sequence"/>
    </id>  ';
            }else{
                $mapXml .= '
    <propriedades coluna="'.rtrim($xml->classe[$i]->propriedades[$j]["coluna"]).'" '.
                            'atributo="'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).'" /> ';
            }
            $metodos[] = ' 
                
        public function get'.ucfirst(rtrim($xml->classe[$i]->propriedades[$j]["atributo"])).'() {
            return $this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).';
        }

        public function set'.ucfirst(rtrim($xml->classe[$i]->propriedades[$j]["atributo"])).'($'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).') {
            $this->'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).' = $'.rtrim($xml->classe[$i]->propriedades[$j]["atributo"]).';
        }';
        }
    }
    
    $qtdAcao = count($xml->classe[$i]->acao);
    $construtor = ''; 
    if($qtdAcao > 0){
        $construtor = '
            
        public function __construct($id = false) {
            parent::__construct($id);';
        for($k = 0; $k < $qtdAcao; $k++){
            $construtor .= '
            $this->setAcaoLog(\''.rtrim($xml->classe[$i]->acao[$k]['classe']).'\' , \''.rtrim($xml->classe[$i]->acao[$k]['metodo']).'\', \''
                    .rtrim($xml->classe[$i]->acao[$k]['acao']).'\', \''.rtrim($xml->classe[$i]->acao[$k]['execulcao']).'\');';
        }
        $construtor .= "
        }"; 
    }
    
    
    $sql .= implode($parametrosTabela,',').') ENGINE=InnoDB DEFAULT CHARSET=latin1;';
    $mapXml .= '
</classe>';
    $php .= implode($atributos, '').$construtor.implode($metodos, '').'
        
    }';
    
    if($extDep==1){
                 $dependencias .= '
    </classe>';
    }
    
    $arrayPHP[] = array('arquivo' =>  $nomeArq, 'conteudo' => $php);
    if(rtrim($xml->classe[$i]['id']) == ''){
        $xml->classe[$i]->addAttribute('id',  rand(100000000, 999999999));
    }
}
header( "Content-type: text; charset: iso-8859-1");

echo $sql;

file_put_contents($pasta.'sql.sql', $sql);
echo '
    
--------------------------------------------------------------------------------
    
';
//echo $mapXml;
file_put_contents($pasta.'mapeamento.xml', '<?xml version="1.0" encoding="UTF-8"?>
<root>'.$mapXml.'
</root>');
/*echo '
     
--------------------------------------------------------------------------------
    
';*/

for($i=0;$i<$qtdClasse;$i++){
    //echo $arrayPHP[$i]['conteudo'];
    file_put_contents($pasta.$arrayPHP[$i]['arquivo'], $arrayPHP[$i]['conteudo']);
}
    
/*
echo '
    
--------------------------------------------------------------------------------
    
';*/
$xml->saveXML($pasta.'XMLBDClass.xml');
}
 
$mapeamentoEntidade = array();



function lerDiretorio($diretorio, &$mapeamentoEntidade){
    $dir = new \DirectoryIterator($diretorio);
    $arDir = array();
    foreach ($dir as $file){
        if (!$file->isDot()){
            $arquivo = $diretorio.'/'.$file->getFilename();
            if($file->getFilename() == 'XMLBDClass.xml'){
                gerar($diretorio.'/', $mapeamentoEntidade);
            }else if(is_dir($arquivo)){
                $arDir[] = $arquivo;
            }
        }
    }
    if(count($arDir) == 0) return false;
    foreach ($arDir as $ar){
        lerDiretorio($ar, $mapeamentoEntidade);
    }

}


$pasta = __DIR__.'/../../../../';
lerDiretorio($pasta, $mapeamentoEntidade);

echo str_replace('\\','\\\\',implode(',',$mapeamentoEntidade));
