<?php

namespace Autoloadr;

/**
 * Class Autoloadr
 *
 * @author Alex Robert
 * @package Autoloadr
 */
abstract  class Autoloadr implements AutoloadrInterface{

    /**
     * @inheritdoc
     */
    public function register($prepend = true){
        spl_autoload_register(array($this, 'load_class'), true, $prepend);
    }


    /**
     * @inheritdoc
     */
    public function unregister(){
        spl_autoload_unregister(array($this, 'load_class'));
    }


}