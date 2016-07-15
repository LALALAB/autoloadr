<?php
namespace Component;


/**
 * Classe générique permettant d'extraire récurssivement des chemins de fichiers d'un répertoire donné.
 * Il est ensuite possible d'appliquer n'importe quelle action sur les chemins de fichier (et donc les fichiers)
 * récupérés. 
 * 
 * @package Application
 * @author Alexandre Robert <alex.robert@live.fr>
 * @version 1.0
 */
Class FileCrawler{
    
    /**
     * @var string 
     */
    private $_extrPath = '';
    
    /**
     * Will not extract any datas in folders whose names are in this array
     * @var type 
     */
    private $_ignoredDirectories = array();
    
    
    /**
     * File extension to extract 
     * @var array 
     */
    private $_allowedTypes = array();
    
    
    /**
     * @var array 
     */    
    private $_rawFiles  = array();
    
    /**
     * @var array 
     */
    private $_rawDirectories = array();
    
    
    /**
     * @var int Internal loop counter for RawFile Extrator function
     */
    private $_iLoop = 0;
    
    
    /**
     * @return string 
     */    
    public function get_path() {
        return $this->_extrPath;
    }

    /**
     * @param string $extrPath 
     */
    public function set_path($extrPath) {
        $this->_extrPath = $extrPath;
        if(substr($this->_extrPath, -1) != DIRECTORY_SEPARATOR){
          $this->_extrPath = $this->_extrPath . DIRECTORY_SEPARATOR;
        }
    }

    /**
     * @return array 
     */    
    public function get_ignored_directories() {
        return $this->_ignoredDirectories;
    }

    /**
     * @param array $ignoredDirectories 
     */
    public function set_ignored_directories($ignoredDirectories) {
        $this->_ignoredDirectories = $ignoredDirectories;
    }

    /**
     * @return array 
     */
    public function get_allowed_types() {
        return $this->_allowedTypes;
    }

    /**
     * @param array $allowed_types 
     */
    public function set_allowed_types($allowed_types) {
        $this->_allowedTypes = $allowed_types;
    }

    
    /**
     * @param string $path 
     */
    function __construct($path = false, $extensions = false, $ignored = false) {
        $this->_extrPath = $path ? $path 
                : $this->_extrPath;
        $this->_allowedTypes = $extensions ? $extensions 
                : $this->_allowedTypes;
        $this->_ignoredDirectories = $ignored ? $ignored
                : $this->_ignoredDirectories;
    }
    
    
    /**
     * Parse the source folders to retrieve new/unprocessed XML files
     * @param type $path 
     * @return array The collection of raw file to process
     */
    public function get_files(){
      $this->crawl();
      return $this->_rawFiles;
    }
    
    
    public function get_directories(){
      $this->crawl();
      return $this->_rawDirectories;
    }
    
    private function crawl($path = false){
      if(!$path){
            $path = $this->_extrPath;
            $this->_rawFiles = array();
            $this->_iLoop = 0;
        }
        
        if(is_dir($path)){
            $Folder = opendir($path);
            while($hdl = readdir($Folder)) {
                $item = $path.$hdl;
                if($hdl != '.' && $hdl != '..'&& substr($hdl,0,1) != '.' && $hdl != 'index.php') {
                  //$this->_tab_output($hdl);
                    if(is_dir($item) && !in_array($hdl, $this->_ignoredDirectories)){
                        $this->_iLoop ++;
                        $this->_rawDirectories[] = $path.$hdl.'/';
                        $this->crawl($path.$hdl.'/');
                    }else if($this->_check_ext($item)){
                        $this->_rawFiles[] = $item;
                    }
                }
            }
        }
        $this->_iLoop --;
        if($this->_iLoop == -1){
            return $this->_rawFiles;
        }              
    }
    

    /**
     * Usually used to check that we are not attempting to parse a no XML file. 
     * But will check any given extensions ... 
     * @param type $file
     * @param type $ext
     * @return type 
     */
     private function _check_ext($file){
         if(file_exists($file) && is_file($file)){
             $finf = pathinfo($file);
             if(in_array($finf['extension'], $this->_allowedTypes)){
                 return true;
             }
         }
         return false;
     }

    
     /**
      * @param string $handler
      * @param bool $extraSpace 
      */
     private function _tab_output($handler, $extraSpace = false){
         $output = $extraSpace ? '&nbsp;&nbsp;' : '';
         for($i=0; $i < $this->_iLoop; $i++){
             $output .= '&nbsp;&nbsp;&nbsp;&nbsp;';
         }
         $output .= $handler . ' <br />';
         echo $output;
     }
     
}