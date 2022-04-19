<?php

 abstract class  Meto_Book_Abstract implements METOBG\BOOK\Interface_Meto_Book{
     /**
* The single instance of the class.
*
* @var Meto_Book_Gallery
* @since 1.0.0
*/
protected static $_instance = null;

/**
* Main Meto_Book_Gallery Instance.
*
* Ensures only one instance of Meto_Book_Gallery is loaded or can be loaded.
*
* @since 1.0.0
* @static
* @see meto_book_gallery()
* @return Meto_Book_Gallery - Main instance.
*/
protected static function instance()
{
   if (is_null(self::$_instance)) {
       self::$_instance = new self();
   }
   return self::$_instance;
}
public abstract function DependencyInjection();

}	