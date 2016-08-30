## Autoladr

### PSR-4 reminder :

- The term "class" refers to classes, interfaces, traits, and other similar structures.
- A fully qualified class name has the following form:  

	`\<NamespaceName>(\<SubNamespaceNames>)*\<ClassName>`  

- When loading a file that corresponds to a fully qualified class name : 
> 1. A contiguous series of one or more leading namespace and sub-namespace names, not including the leading namespace separator, in the fully qualified class name (a "namespace prefix") corresponds to at least one "base directory".
> 2. The contiguous sub-namespace names after the "namespace prefix" correspond to a subdirectory within a "base directory", in which the namespace separators represent directory separators. The subdirectory name MUST match the case of the sub-namespace names.
> 3. The terminating class name corresponds to a file name ending in .php. The file name MUST match the case of the terminating class name.
> 4. Autoloader implementations MUST NOT throw exceptions, MUST NOT raise errors of any level, and SHOULD NOT return a value.

### Universal Autoloadr : 

> Work as a common "ClassLoader" (PSR-0) as well  
> 2.: The subdirectory name can be lowercase. It still MUST match the namespace names.  
> 4.: No exeption, but use Loggr for debuging  


- underscore in class_names: `
    - `\namespace\package\Class_Name` matches `/prefixe/path/to/namespace/package/Class/Name.php`
    - `\Mustache_Engine`              matches `/prefixe/path/to/vendor/mustache/src/Mustache/Engine.php`

- underscore in namespace:   
     - `\namespace\my_package\Class_Name` matches `/prefixe/path/to/namespace/my_package/Class/Name.php`

- Lower/Upper case : 
    - `\Ahoy\Core\Request` matches `/path/to/project/ahoy/core/Request.php`  
	   or `/path/to/project/Ahoy/Core/Request.php`

 
#### Exemple 
              
        $Loadr = new \Autoloadr\Universal();
        
        $Loadr->add_prefix(_ROOT_DIR_ . '/vendor/knot/src');
        $Loadr->add_prefix(_ROOT_DIR_ . '/vendor/scoutr/src');
        $Loadr->add_prefix(_ROOT_DIR_ . '/vendor/mustache/src');

        $Loadr->register();
        
        $K = new Knot\Knot();
        $S = new Scoutr\Scout();
        $M = new Mustache_Engine();