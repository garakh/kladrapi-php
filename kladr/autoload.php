<?php

function KladrAutoloader($class){
    $class = str_replace('Kladr\\', 'classes/', $class);
    $class = str_replace('\\', '/', $class);
    $class .= '.php';
    include($class);
}

spl_autoload_register('KladrAutoloader');
