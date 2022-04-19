<?php
namespace METOBG\BOOK;

interface Interface_Meto_Book{
    
 #get
public  function  get_name();
public  function  get_title();
public  function  get_genres();
public  function  get_price();
public  function  get_author_first_name();
public  function  get_author_last_name();
public  function  get_publisher_name();
public  function  get_publishe_date();
public  function  get_publishe_place();
#set
public  function  set_name(string $name);
public  function  set_title(string $title);
public  function  set_genres(string $genres);
public  function  set_price(string $price);
public  function  set_author_first_name(string $author_first_name);
public  function  set_author_last_name(string $author_last_name);
public  function  set_publisher_name(string $publisher_name);
public  function  set_publishe_date(string $publishe_date);
public  function  set_publishe_place(string $publishe_place);

#custom
public  function get_custom_property($custom_name);
public  function set_custom_property($custom_name);
}