<?php
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    echo $actual_link."\n";
    echo $_SERVER['REQUEST_URI']."\n";
    echo $_SERVER['SCRIPT_NAME'].";";
    echo $_SERVER['DOCUMENT_ROOT']
?>