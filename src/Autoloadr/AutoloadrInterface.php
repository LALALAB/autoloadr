<?php

namespace Autoloadr;


/**
 * Interface AutoloadrInterface
 *
 * @author Alex Robert
 * @package Autoloadr
 */
interface AutoloadrInterface {


    /**
     * Register this as a new autolaoder instance
     *
     * @param bool $prepend
     *
     * @return mixed
     */
    public function register($prepend);


    /**
     * Remove this from the autload stack
     *
     * @return mixed
     */
    public function unregister();


    /**
     * Find the class to autload
     *
     * @param string $class_name
     *
     * @return mixed path to the $class_name or a boolean
     */
    public function find_class($class_name);


    /**
     * Actually load the class
     *
     * @param string $class_name
     *
     * @return mixed
     */
    public function load_class($class_name);



}