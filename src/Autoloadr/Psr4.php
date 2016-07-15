<?php

namespace Autoloadr;

/**
 * Class \Autoloadr\Psr4
 * This is a PSR-4 autoloader.
 *
 * @author alex.robert
 * @package Autoloadr
 */
Class Psr4 {

   private $_base_dir = null;


   public function __construct($base_dir = null) {
      if ($base_dir === null) {
         $base_dir = str_replace(DIRECTORY_SEPARATOR . __NAMESPACE__, '', dirname(__FILE__)) ;
      }
      $this->_base_dir = $base_dir;
   }


   /**
    * Register a new Autoloader.
    * $base_dir is the path to the source directory from where to look for the classes
    * if $base_dir is not provided, the source directory is the directory from where the Autoloader is loaded.
    * @param mixed $base_dir
    */
   static public function register($base_dir = null) {
      $loader = new self($base_dir);
      spl_autoload_register([$loader, 'autoload']);
   }


   /**
    * The autoload function shall never been called has it's registered with spl_autoload_register
    * @param string $class
    */
   public function autoload($class) {
      $path_parts = explode('\\', $class);
      $class_name = array_pop($path_parts);
      $real_path  = implode(DIRECTORY_SEPARATOR, $path_parts);
      $file_name  = $class_name . '.php';
      $full_path  = $this->_base_dir . DIRECTORY_SEPARATOR . $real_path . DIRECTORY_SEPARATOR . $file_name;
      $base_path  = $this->_base_dir . DIRECTORY_SEPARATOR . $file_name;

      if(file_exists($full_path)){
         require $full_path;
      }else if(file_exists($base_path)){
         require $base_path;
      }
   }

}