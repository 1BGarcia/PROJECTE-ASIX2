<?php

    function autocargar($classname){
        include 'controller/'.$classname.'.php';
    }

    function autoload_models(){
        foreach(glob("model/*.php") as $filename){
            require_once $filename;
        }
    }

    autoload_models();
    spl_autoload_register('autocargar');
?>