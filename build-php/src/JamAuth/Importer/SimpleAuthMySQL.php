<?php
namespace JamAuth\Importer;

class SimpleAuthMySQL extends DataImporter{
    
    public function read(){
        
    }
    
    public function getReaderName(){
        return "SimpleAuth";
    }
    
    public function getReaderType(){
        return "MySQL";
    }
}