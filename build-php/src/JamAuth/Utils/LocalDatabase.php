<?php
namespace JamAuth\Utils;

use JamAuth\JamAuth;
use SQLite3;

class LocalDatabase{
    
    private $plugin;
    private $db, $stmt, $s = null;
    
    public function __construct(JamAuth $plugin){
        $this->plugin = $plugin;
        $this->loadDatabase();
    }
    
    private function loadDatabase(){
        $dir = $this->plugin->getDataFolder()."data/jamauth.db";
        $new = false;
        if(!is_file($dir)){
            $new = true;
            $this->plugin->sendInfo($this->plugin->getTranslator()->translate("main.createDb"));
        }
        $this->db = new SQLite3($dir);
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS users
            (username TEXT PRIMARY KEY, time INTEGER, food TEXT, salt TEXT, extData TEXT)"
        );
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS rules
            (name TEXT PRIMARY KEY, content TEXT)"
        );
        $stmts = [
            "register" =>
            "INSERT INTO users (username, time, food, salt, extData)
             VALUES (:username, :time, :food, :salt, :extData)",
            
            "fetch" =>
            "SELECT food, salt, extData
             FROM users
             WHERE username = :username",
            
            "getRule" =>
            "SELECT content
             FROM rules
             WHERE name = :name",
            
            "setRule" =>
            "INSERT or REPLACE INTO rules (name, content)
             VALUES (:name, :content)",
            
            "getCount" =>
            "SELECT COUNT(*)
             AS count
             FROM users"
        ];
        foreach($stmts as $key => $stmt){
            $this->stmt[$key] = $this->db->prepare($stmt);
        }
        if($new){
            $this->setRule("record", time());
        }
        if($this->s != null){
            $this->setRule("secret", $this->s);
            unset($this->s);
        }
    }
    
    public function register($pn, $time, $food, $salt, $data = []){
        $stmt = $this->stmt["register"];
        
        $stmt->bindValue(":username", $pn, SQLITE3_TEXT);
        $stmt->bindValue(":time", $time, SQLITE3_INTEGER);
        $stmt->bindValue(":food", $food, SQLITE3_TEXT);
        $stmt->bindValue(":salt", $salt, SQLITE3_TEXT);
        
        $stmt->reset();
        $res = $stmt->execute();
        if($res === false){
            return false;
        }
        return true;
    }
    
    public function fetchUser($pn){
        $stmt = $this->stmt["fetch"];
        
        $stmt->bindValue(":username", $pn, SQLITE3_TEXT);
        
        $stmt->reset();
        $res = $stmt->execute();
        if(($val = $res->fetchArray(SQLITE3_ASSOC))){
            return $val;
        }
        return null;
    }
    
    public function getRule($name){
        $stmt = $this->stmt["getRule"];
        
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        
        $stmt->reset();
        $res = $stmt->execute();
        if(($val = $res->fetchArray(SQLITE3_ASSOC))){
            return $val["content"];
        }
        return null;
    }
    
    public function setRule($name, $cont = ""){
        $stmt = $this->stmt["setRule"];
        
        $stmt->bindValue(":content", $cont, SQLITE3_TEXT);
        $stmt->bindValue(":name", $name, SQLITE3_TEXT);
        
        $stmt->reset();
        $res = $stmt->execute();
        if($res === false){
            return false;
        }
        return null;
    }
    
    public function getAccountCount(){
        $stmt = $this->stmt["getCount"];
        
        $stmt->reset();
        $res = $stmt->execute();
        if($res === false){
            return false;
        }
        return $res->fetchArray(SQLITE3_ASSOC)["count"];
    }
    
    public function truncate(){
        $this->plugin->sendInfo($this->plugin->getTranslator()->translate("main.deleteDb"));
        
        $s = $this->getRule("secret");
        if($s != null){
            $this->s = $s;
        }
        
        unset($this->stmt);
        unset($this->db);
        unlink($this->plugin->getDataFolder()."data/jamauth.db");
        $this->loadDatabase();
    }
}