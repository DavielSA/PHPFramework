<?php
    define("RAIZ",dirname(__FILE__));
    define("_CORE_", RAIZ."/core/");
    define("_MODELS_",RAIZ."/models/");
    define("_CONTROLLERS_",RAIZ."/controllers/");
    define("_VIEW_",RAIZ."/view/");

    //DEBUG MODE=true
    define("DEBUG",true);

    //Manager error exceptions or warnings 
    include_once(RAIZ."/manage_error.php");


    try {
        //Catch all exceptions
        include_once(RAIZ."/master.php");
    } catch (Exception $e) {
        HttpError::e500($e);
    }


