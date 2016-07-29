#Autoloadr

PHP Autoloaders  

## Universal Autoloadr : 
   - PSR 0 
   - PSR 4 
   - Directories names can be lowercase, still following PSR directives
    
    
### Exemple : 
    
        define('CURRENT_DIR', realpath(dirname(__FILE__)));
                    
        $UniLoad = new \Autoloadr\Universal();
        
        $UniLoad->add_prefix(CURRENT_DIR . '/vendor/knot/src');
        $UniLoad->add_prefix(CURRENT_DIR . '/vendor/scoutr/src');
        
        $UniLoad->register();
        
        $K = new \Knot\Knot();
        $S = new \Scoutr\Scout();


## ClassMap Autoloadr : 

    - TODO
    

