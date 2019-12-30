<?php
    /**
     *  Define URL router.
     *      PathUrl | Verb | Controller | Method
     *  Allowed Verbs:
     *      get
     *      post
     *      put
     *      patch
     *      delete
     *  Example:
     *      RouterClass::Add("/","GET","Home","Get");
     *      RouterClass::AddAuth("/secureurl","GET","Home","Get",roleid);
     * 
     * 
     *  Define groups Styles or JavaScript files 
     *      RouterClass::AddStyle
     *      RouterClass::AddScript
     *  Example:
     *      RouterClass::AddStyle("MyStyle","/view/style/css/style.css");
     *      RouterClass::AddScript("MyJS","/view/style/js/javascript.js")
     */

    RouterClass::Add("/","GET","Home","Get");


    RouterClass::AddStyle("MyStyle","/view/style/css/style.css");

    RouterClass::AddStyle ("Bootstrap","/view/style/bootstrap441/bootstrap.css");
    RouterClass::AddScript("Bootstrap","/view/style/bootstrap441/jquery-3.4.1.slim.min.js");
    RouterClass::AddScript("Bootstrap","/view/style/bootstrap441/popper.min.js");
    RouterClass::AddScript("Bootstrap","/view/style/bootstrap441/bootstrap.min.js");
    

    //RouterClass::AddScript("MyJS","/view/style/js/javascript.js");
    