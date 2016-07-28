<?php



abstract class VetorAssociativo{
    
    public static function get(&$dados, $string, $valorPadrao=false){
        if(is_string($string))
            $var = explode(',',$string);
        else $var = $string;
        $aux = $dados;
        foreach($var as $v){
            if(isset($aux[$v])){
                $aux = $aux[$v];
            }else return $valorPadrao;
        }
        return $aux;
    }
    
    public static function set(&$dados, $valor, $string=false){
        if($string === false){
            $dados = $valor;
            return ;
        }
        $var = explode(',',$string);
        if(is_array($var)){
            if(count($var)==1)$dados[$var[0]] = $valor;            
            else if(count($var)==2)$dados[$var[0]][$var[1]] = $valor;            
            else if(count($var)==3)$dados[$var[0]][$var[1]][$var[2]] = $valor;
            else if(count($var)==4)$dados[$var[0]][$var[1]][$var[2]][$var[3]] = $valor;
            else if(count($var)==5)$dados[$var[0]][$var[1]][$var[2]][$var[3]][$var[4]] = $valor;
        }else $dados[$var] = $valor;        
    }

    public static function limpar(&$dados, $string = false){
        if($string === false){
            $dados = null;
            return ;
        }
       
        $var = explode(',',$string);

        if(is_array($var)){
            if(count($var)==1)unset($dados[$var[0]]);            
            else if(count($var)==2)unset($dados[$var[0]][$var[1]]);            
            else if(count($var)==3)unset($dados[$var[0]][$var[1]][$var[2]]);
            else if(count($var)==4)unset($dados[$var[0]][$var[1]][$var[2]][$var[3]]);
            else if(count($var)==5)unset($dados[$var[0]][$var[1]][$var[2]][$var[3]][$var[4]]);
        }else unset($dados[$var]);        
    }
    
    
}


abstract class XML{
    public static function ler($caminho){
        return file_exists($caminho) ? simplexml_load_file($caminho) : false;
    }   
}

abstract class RepositorioProcesso{
    private static $processos;
    private static $modulos;
    
    public static function add($chave, $dados){        
        VetorAssociativo::set(self::$processos, $dados, $chave);
    }

    public  static function liberar($chave){
        VetorAssociativo::limpar(self::$processos, $chave);
    }
    
    public static function get(){
        return self::$processos;
    }
    
    public static function getModulo(){
        return self::$modulos;
    }
    
    public static function addModulo($mod){
        if(is_null(self::$modulos))self::$modulos = array();
        if(!in_array($mod, self::$modulos))
            self::$modulos[] = $mod;
    }
}

abstract class LerXmlProcesso{
    private static $xml;
    private static $codigo;
    
    public  static function lerXml($processo, $etapa=false){
        self::$codigo = str_replace('.xml','',$processo.'/'.$etapa);
        
        $dir = new \DirectoryIterator(diretorio);
        foreach ($dir as $file){
            
            if ($file->isDot()) continue;
            if($etapa) self::$xml = XML::ler(diretorio.'/'.$file->getFilename().'/'.$processo.'/'.$etapa);
            else self::$xml = XML::ler(diretorio.'/'.$file->getFilename().'/'.$processo.'/'.$processo);
            
            if (self::$xml){   
                break;
            }
        }   
        if(!self::$xml){
            if($etapa) self::$xml = XML::ler(diretorio.'/'.$processo.'/'.$etapa);
            else self::$xml = XML::ler(diretorio.'/'.$processo.'/'.$processo);
        }
       
        if(self::$xml){
            self::lerProcesso();
            self::$xml = null;
            return true;
        }
        return false;
    }
    
    private static function lerProcesso(){
        RepositorioProcesso::add(self::$codigo, array('campos' => array(), 'url' => self::$codigo));
        self::lerCampos();
        self::lerComentario();
    }
    
    
    private static function lerCampos(){
        $campos = array();
        foreach(self::$xml->entrada->campo as $campo){
            $campos[trim($campo['chave'])] =   array( 'regra' => trim($campo['regra']),
                                                        'chave' => trim($campo['chave']),
                                                        'teste' => trim($campo['teste']),
                                                        'array' => trim($campo['array']),
                                                        'type' => trim($campo['type']),
                                                        'json' => trim($campo['json']),
                                                        'nome' => trim($campo['nome']));  
        }
        $modulos = explode(';', self::$xml['modulo']);
        //var_dump($modulos);
        foreach($modulos as $mod){
            RepositorioProcesso::addModulo($mod);
            RepositorioProcesso::add($mod.','.self::$codigo.',campos', $campos);
        }
    }
    
    private static function lerComentario(){
        if(isset(self::$xml->comentario))
            RepositorioProcesso::add(self::$codigo.',comentario', trim(self::$xml->comentario));
        else 
            RepositorioProcesso::add(self::$codigo.',comentario', '');
    }
}



class PercorrerArquivo{
    
    public function percorrer($diretorio){
        $dir = new \DirectoryIterator($diretorio);
        foreach ($dir as $file){
            if ($file->isDot()) continue;
                
            $arquivo = $diretorio.'/'.$file->getFilename();
           
            if(is_file($arquivo)){
                $array = explode('/',$arquivo);
                
                LerXmlProcesso::lerXml($array[count($array)-2], $array[count($array)-1]);
                
            }else if(is_dir($arquivo))
                $this->percorrer($arquivo);
            
        }        
    }
    
}

