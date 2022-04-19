<?php
namespace METOBG\BOOK;


interface Interface_DB{

    public  function insert();
    public  function update();
    public  function delete();
    public  function get();
    public  function bulk_insert();
    public  function bulk_update();
    public  function bulk_delete();
    public  function bulk_get();
}