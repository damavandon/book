<?php

class Meto_Book_DB implements METOBG\BOOK\Interface_DB {

    public function insert()
    {
        
    }
    public function update()
    {
        
    }
    public function delete()
    {
        
    }
    public function get()
    {
        
    }
    public function bulk_insert()
    {
        
    }
    public function bulk_update()
    {
        
    }
    public function bulk_delete()
    {
        
    }
    public function bulk_get()
    {
        
    }
    
    public static function number_of_books(){
        global $wpdb;
        $sql="SELECT COUNT(ID) FROM `{$wpdb->posts}` WHERE  `post_type`='metobook' AND `post_status`='publish'";
        $result=$wpdb->get_row($sql,ARRAY_A);
        if(count($result)!=0){
            $result=$result['COUNT(ID)'];
            return $result;
        }
        return 0;
    }
}