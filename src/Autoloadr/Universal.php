<?php

namespace Autoloadr;


/**
 * Class Autoloadr\Universal
 * This is a universal Autoloader
 *
 * @author Alex Robert
 * @package Autoloadr
 */
Class Universal extends Autoloadr implements AutoloadrInterface{


    /**
     * @var array
     */
    private $_prefix_paths = [];



    /**
     * @param $directories
     */
    public function add_prefixes($directories){
        if (is_array($directories)) {
            foreach ($directories as $dir) {
                $this->add_prefix($dir);
            }
        }
    }


    /**
     * @param string $dir
     */
     public function add_prefix($dir) {
        $real_dir = realpath($dir);
        if (is_dir($real_dir)) {
            $this->_prefix_paths[] = $real_dir;
        }
    }


    /**
     * @inheritdoc
     */
    public function find_class($class_name){

        $path_parts = explode('\\', $class_name);
        $class_name = array_pop($path_parts);
        $real_path  = implode(DIRECTORY_SEPARATOR, $path_parts);


        $dash_pos   = strpos($class_name, '_');
        $dash_class = $dash_pos !== false && $dash_pos < (strlen($class_name)-1);

        $file_name  = $dash_class ? array_pop(explode('_', $class_name)) . '.php' : $class_name . '.php';
        $real_path .= $dash_class ? DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_slice(explode('_', $class_name), 0, count(explode('_', $class_name)) - 1)) : '';
        $real_path .= $real_path  ? DIRECTORY_SEPARATOR : '';


        foreach ($this->_prefix_paths as $path) {
            $base_path = $path . DIRECTORY_SEPARATOR;

            $lower_path = $base_path . strtolower($real_path) . $file_name;
            $upper_path = $base_path . $real_path . $file_name;
            
            if (file_exists($lower_path)) {
                return $lower_path;
            } else if (file_exists($upper_path)) {
                return $upper_path;
            }

        }

        return false;
    }


    /**
     * @inheritdoc
     */
    public function load_class($class_name) {
        $require_path = $this->find_class($class_name);
        if($require_path){
            require_once $require_path;
        }
    }

}