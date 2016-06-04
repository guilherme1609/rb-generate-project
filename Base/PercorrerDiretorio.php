<?php

class PercorrerDiretorio{
    
    public function lerDiretorio($diretorio, &$mapeamentoEntidade){
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
        if(count($arDir) == 0){
            return false;
        }
        foreach ($arDir as $ar){
            lerDiretorio($ar, $mapeamentoEntidade);
        }
    }
}
