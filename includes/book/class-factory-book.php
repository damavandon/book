<?php
//factory-design-pattern

class Meto_Book_Factury{

    private static function include($path){
        include_once $path;
    }
    
    public static function make(string $objectName){
        
        $objectName=strtolower($objectName);
        $path=__DIR__.'/class-'.$objectName.'.php';

        if(is_file($path)){
           self::include($path);
           $object=$objectName::instance();
           $object->DependencyInjection();
         return  $object;
        }
        return null;
    }
    
}