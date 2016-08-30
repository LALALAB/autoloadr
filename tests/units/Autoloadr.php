<?php

namespace Autoloadr\Tests\Units;


require_once __DIR__ . '/../../vendor/bin/atoum';
use \mageekguy\atoum;


abstract class Autoloadr extends atoum\test {




    public function test_register(){

        $Loader = $this->newTestedInstance();

        $this->assert('Register autoloadr')
            ->given(  $registered = $Loader->register(),
                      $in_stack   = false
            )
        ;

        foreach(spl_autoload_functions() as $func){
            if(  isset(class_implements($func[0])['Autoloadr\AutoloadrInterface']) ){
                $this
                     ->given($in_stack = true)
                     ->assert('Loader as a load_class defined as Autoload function')
                     ->string($func[1])->isEqualTo('load_class')
                ;
            }
        }

        $this->assert('Loader is registered')
             ->boolean($in_stack)
                ->isTrue()
             ->boolean($registered)
                ->isTrue()
        ;

    }


    public function test_unregister(){

        $Loader = $this->newTestedInstance();
        $Loader->register();


        $this->assert('Unregister function')
             ->given(  $unregistered =  $Loader->unregister() )
             ->boolean($unregistered)->isTrue()
        ;

    }





}