<?php
namespace JamAuth\Importer;

class ServerAuthSQLite extends DataImporter{
    
    private $db;
    
    public function prepare(){
        
    }
    
    public function import(){
        
    }
    
    public function getReaderName(){
        return "SimpleAuth";
    }
    
    public function getReaderType(){
        return "SQLite";
    }
    
}