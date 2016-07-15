<?php

namespace Autoloadr;


/**
 * Class Autoloadr\Universal
 * This is a universal Autoloader
 *
 * @author alex.robert
 * @package Autoloadr
 */
Class Universal{


   /**
    * @var array
    */
   private static $_load_paths = [];

   /**
    * @var null
    */
   private static $_Instance   = null;


   /**
    * @var bool
    */
   private static $_is_registered   = false;


   /**
    * Register the given $directories for autloading class in them.
    * @param mixed $directories
    */
   static public function register($directories) {
      self::_add_directories($directories);
      if(!static::$_is_registered) {
         $Li = self::_instance();
         spl_autoload_register([$Li, 'autoload']);
         static::$_is_registered = true;
      }
   }


   /**
    * @param $directories
    */
   static private function _add_directories($directories){
      if (is_array($directories)) {
         foreach ($directories as $dir) {
            self::_add_directory($dir);
         }
      }
   }


   /**
    * @param string $dir
    */
   static private function _add_directory($dir) {
      $real_dir = realpath($dir);
      if (is_dir($real_dir)) {
         self::$_load_paths[] = $real_dir;
      }
   }


   /**
    * The autoload function shall never been called has it's registered with spl_autoload_register
    * @param string $class
    */
   public function autoload($class) {
      $loaded = false;

      $path_parts = explode('\\', $class);
      $class_name = array_pop($path_parts);
      $real_path  = implode(DIRECTORY_SEPARATOR, $path_parts);


      $dash_pos   = strpos($class_name, '_');
      $dash_class = $dash_pos !== false && $dash_pos < (strlen($class_name)-1);

      $file_name  = $dash_class ? array_pop(explode('_', $class_name)) . '.php' : $class_name . '.php';
      $real_path .= $dash_class ? DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_slice(explode('_', $class_name), 0, count(explode('_', $class_name)) - 1)) : '';
      $real_path .= $real_path  ? DIRECTORY_SEPARATOR : '';


      foreach (self::$_load_paths as $path) {
         $base_path = $path . DIRECTORY_SEPARATOR;
         if (file_exists($base_path . strtolower($real_path) . $file_name)) {
            $loaded = true;
            require $base_path . strtolower($real_path) . $file_name;
            break;
         } else if (file_exists($base_path . $real_path . $file_name)) {
            $loaded = true;
            require $base_path . $real_path . $file_name;
            break;
         }
      }
   }



   final static protected function _instance(){

      if(!isset(self::$_Instance)){
         $class = get_called_class();
         static::$_Instance = new $class();
      }
      return static::$_Instance;
   }


   final protected function __construct($base_dir = null) {}

}