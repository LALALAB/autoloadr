<?php

namespace Autoloadr;

/**
 * Class Autoloadr
 *
 * @author Alex Robert
 * @package Autoloadr
 */
abstract  class Autoloadr{

    /**
     * @inheritdoc
     */
    public function register($prepend = true){
        return spl_autoload_register(array($this, 'load_class'), true, $prepend);
    }


    /**
     * @inheritdoc
     */
    public function unregister(){
        return spl_autoload_unregister(array($this, 'load_class'));
    }


}