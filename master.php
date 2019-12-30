<?php

$coreFile = array_diff(scandir(_CORE_), array('.', '..'));
foreach($coreFile as $file) 
    if (pathinfo($file)["extension"]=="php" )
        include(_CORE_.$file);

$modelFile = array_diff(scandir(_MODELS_), array('.', '..'));
foreach($modelFile as $file) 
    if (pathinfo($file)["extension"]=="php" )
        include(_MODELS_.$file);

$controllerFile = array_diff(scandir(_CONTROLLERS_), array('.', '..'));
foreach($controllerFile as $file) 
    if (pathinfo($file)["extension"]=="php" )
        include(_CONTROLLERS_.$file);
        
include_once(RAIZ."/Router.php");
RouterClass::Dispatch();