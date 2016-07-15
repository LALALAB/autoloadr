<?php




require 'components/file_utils/FileCrawler.class.php';


/**
 * Class Factory :
 *
 * @author  Alexandre Robert <alex.robert@live.fr>
 * @version 1.0
 *
 * More a "Class Loader" than a factory. But "factory" is cooler.
 */
Class Factory {


   /**
    * @var array
    */
   static private $autoload_map = [];

   /**
    * @var array
    */
   static private $instances = [];
   
   
   static private $_tmp_class = './tmp/cache/model/';
   
   
   /**
    *
    * @param boolean $force force autoload map re-generation
    */
   static public function init() {

      if (!file_exists('./tmp/config/autoload-map.php')) {
         self::_make_autoload_map();
      }

      self::$autoload_map = include('./tmp/config/autoload-map.php');
   }


   static public function autoload($class) {

      if (isset(self::$autoload_map[$class])) {
         require(self::$autoload_map[$class]);

         return true;
      } else {

         if (\Config::get('engine.force_model') || !file_exists(self::$_tmp_class . $class . '.class.php')) {
            $Klass = false;
            $Resource = \Resource::find(['conditions' => 'php_class="' . $class . '"']);
            if ($Resource) {
               $Klass = self::_make_class_model($Resource);
            } else {
               $x_table = strtolower(\Utils\String::decamelize($class));
               $x_table = preg_replace('#^x#', 'x_', $x_table);
               $Xref = \RelationM2m::find(['conditions' => 'x_table_name="' . $x_table . '"']);
               if ($Xref) {
                  $Klass = self::_make_cross_class($Xref, $class);
               } else {
               }
            }
            if ($Klass) {
               include self::$_tmp_class . $class . '.class.php';

               return true;
            }
         } else {
            if (file_exists(self::$_tmp_class . $class . '.class.php')) {
               include self::$_tmp_class . $class . '.class.php';

               return true;
            }
         }
      }

      return false;
   }


   /**
    * @param $class
    * @return Object
    */
   static public function instance($class) {
      return self::_instance($class);
   }


   /**
    * @param $class
    * @return null|Object
    */
   static public function get_copy($class) {
      $Object = self::_instance($class);
      if($Object){
         $Clone = clone $Object;
         return $Clone;
      }
      return null;
   }


   /**
    * @param $class
    * @return Object
    */
   static private function _instance($class) {
      if (class_exists($class, true)) {
         if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class;
         }
         return self::$instances[$class];
      }
      return null;
   }


   static private function _make_class_model($Resource) {
      $PhpWriter = new \Component\PhpWriter();

      $extends = '\Record';
      if ($Resource->extend_id) {
         $ExtendObject = \Resource::find($Resource->extend_id);
         if ($ExtendObject) {
            $extends = $ExtendObject->php_class;
         }
      }

      $PhpWriter->make_class($Resource->php_class, $extends);
      self::_make_class_model_belongs_to_relations($Resource, $PhpWriter);
      self::_make_class_model_has_many_relations($Resource, $PhpWriter);


      return $PhpWriter->draw(self::$_tmp_class . $Resource->php_class . '.class.php');
   }


   static private function _make_cross_class($Xref, $class) {

      $PhpWriter = new \Component\PhpWriter();
      $PhpWriter->make_class($class, 'CrossRecord');
      $PhpWriter->free_code("\t static \$table_name = '" . $Xref->x_table_name . "';", false);
      $PhpWriter->free_code("\t static \$belongs_to = array(", false);
      $PhpWriter->free_code("\t\t array('" . \Utils\String::singularize($Xref->object_1_tablename) . "'),", false);
      $PhpWriter->free_code("\t\t array('" . \Utils\String::singularize($Xref->object_2_tablename) . "')", false);
      $PhpWriter->free_code("\t );", false);

      return $PhpWriter->draw(self::$_tmp_class . $class . '.class.php');
   }


   /**
    *
    * @param \Resource            $Resource
    * @param \Component\PhpWriter $PhpWriter
    */
   static private function _make_class_model_belongs_to_relations($Resource, &$PhpWriter) {
      $relations = $Resource->get_o2m_belongs_to_relations(); //\RelationO2m::all(array('conditions'=>array('child_id = "'.$Resource->gid.'"')));

      if ($relations) {
         $PhpWriter->free_code("\t static \$belongs_to = array(", false);
         foreach ($relations as $R) {
            $PhpWriter->free_code("\t\t array('" . $R['name'] . "', 'class_name'=>'" . \Utils\String::classify($R['parent_name']) . "', 'primary_key'=>'gid', 'foreign_key'=>'" . $R['child_reference_field'] . "'),", false);
         }
         $PhpWriter->free_code("\t );", false);
      }
   }


   /**
    *
    * @param \Resource            $Resource
    * @param \Component\PhpWriter $PhpWriter
    */
   static private function _make_class_model_has_many_relations($Resource, &$PhpWriter) {
      $relations = $Resource->get_o2m_has_many_relations(); //\RelationO2m::all(array('conditions'=>array('parent_id = "'.$Resource->gid.'"')));
      if ($relations) {
         $PhpWriter->free_code("\t static \$has_many = array(", false);
         foreach ($relations as $R) {
            //child_field not active recrd complient
            $PhpWriter->free_code("\t\t array('" . \Utils\String::pluralize($R['child_name']) . "', 'primary_key'=>'gid', 'child_field'=>'" . $R['child_reference_field'] . "'),", false);
         }
      }

      $Xelations = \RelationM2m::all(['conditions' => ['object_1_id="' . $Resource->gid . '" OR object_2_id="' . $Resource->gid . '"']]);
      if ($Xelations) {
         if (!$relations) {
            $PhpWriter->free_code("\t static \$has_many = array(", false);
         }
         foreach ($Xelations as $X) {
            $through = $X->x_table_name;
            $many_what = $Resource->table_name == $X->object_1_tablename ? $X->object_2_tablename : $X->object_1_tablename;
            $PhpWriter->free_code("\t\t array('" . $through . "'), array('" . $many_what . "', 'through'=>'" . $through . "'),", false);
         }
      }

      if ($Xelations || $relations) {
         $PhpWriter->free_code("\t );", false);
      }
   }


   static private function _make_autoload_map() {
      $ar_core = [];
      $C = new \Component\FileCrawler();
      $C->set_allowed_types(['php']);
      $C->set_ignored_directories(['tmp']);
      $C->set_path('./');
      $ar_core = $C->get_files();

      $load_map = "";
      foreach ($ar_core as $php_file) {
         if (preg_match('/(\.class|\.int)\.php$/ui', $php_file)) {
            $namespace = "\\";
            preg_match('/(?!\/\/)namespace\s(.+);/i', file_get_contents($php_file), $matches);
            if ($matches[1]) {
               if (substr($matches[1], 0, 1) == "\\") {
                  $namespace = $matches[1] . "\\";
               } else {
                  $namespace = "\\" . $matches[1] . "\\";
               }

            }
            $file_info = pathinfo($php_file);
            $map_line_full = $namespace . str_replace(['.class', '.int'], '', $file_info['filename']) . "' => '" . $file_info['dirname'] . DIRECTORY_SEPARATOR . $file_info['basename'] . "', \n";
            $load_map .= "\t'" . $map_line_full;
            $load_map .= "\t'" . substr($map_line_full, 1);
         }
      }
      $map_file = "<?php \nreturn array(\n" . $load_map . "); \n?>";
      file_put_contents('./tmp/config/autoload-map.php', $map_file);
   }
}


spl_autoload_register(['\Factory', 'autoload']);
