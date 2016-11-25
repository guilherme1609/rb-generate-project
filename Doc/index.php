<!DOCTYPE html>

<html>
    <head>
        <title>Site Rubeus</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script type="text/javascript" src="<?php echo DIR_DOC ?>vendor/rubeus/generate-project/Doc/ConexaoServidor/GCS.js"></script>
        <script type="text/javascript" src="<?php echo DIR_DOC ?>vendor/rubeus/generate-project/Doc/lib/jquery/jquery.js"></script>
        <script type="text/javascript" src="<?php echo DIR_DOC ?>vendor/rubeus/generate-project/Doc/lib/jquery/jquery.form.js"></script>
        <link rel = "stylesheet" type = "text/css" href="<?php echo DIR_DOC ?>vendor/rubeus/generate-project/Doc/css/estilo.css"/>
        <link rel = "stylesheet" type = "text/css" href="<?php echo DIR_DOC ?>vendor/rubeus/generate-project/Doc/css/normalize.css"/>
        <link rel = "stylesheet" type = "text/css" href="<?php echo DIR_DOC ?>vendor/rubeus/generate-project/Doc/css/estiloBarra.css"/>
    </head>
    <body>
        <div id="page"> 
            <?php
                $array = array(
                    array("texto" => "Testar", "link" => "?pag=1"),
                    array("texto" => "Upload", "link" => "?pag=2")

                );
            ?>
            <div class="barra-superiror">
                <?php 
                    for($i = 0; $i < count($array); $i++){?>
                        <div class="menu"><a href="<?php echo $array[$i]['link'];?>"><?php echo $array[$i]['texto'];?></a></div>
                <?php } ?>
            </div>
                    
            <?php            
                switch ($_GET['pag']){
                    case 1:
                        include 'Testar.php';
                        break;
                    case 2:
                        include 'Upload.php';  
                        break;
                    default:
                        include 'Testar.php';
                        break;
                }            
            ?>
        </div>
        <script> 
            
        </script>
    </body>
</html>
