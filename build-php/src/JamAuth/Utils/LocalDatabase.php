<?php
namespace JamAuth\Utils;

use pocketmine\Player;

use JamAuth\JamAuth;
use SQLite3;

class LocalDatabase{
    
    private $plugin;
    private $db, $stmt;
    
    public function __construct(JamAuth $plugin){
        $this->plugin = $plugin;
        $this->loadDatabase();
    }
    
    private function loadDatabase(){
        $dir = $this->plugin->getDataFolder()."data/jamauth.db";
        if(!is_file($dir)){
            $this->plugin->sendInfo($this->plugin->getTranslator()->translate("main.createDb"));
        }
        $this->db = new SQLite3($dir);
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS users
            (username TEXT PRIMARY KEY, time INTEGER, food TEXT, salt TEXT, extData TEXT)"
        );
        $stmts = [
            "register" =>
            "INSERT INTO users (username, time, food, salt, extData)
             VALUES (:username, :time, :food, :salt, :extData)",
            
            "fetch" =>
            "SELECT food, salt, extData
             FROM users
             WHERE username = :username"
        ];
        foreach($stmts as $key => $stmt){
            $this->stmt[$key] = $this->db->prepare($stmt);
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
    
    public function truncate(){
        $this->plugin->sendInfo($this->plugin->getTranslator()->translate("main.deleteDb"));
        unset($this->stmt);
        unset($this->db);
        unlink($this->plugin->getDataFolder()."data/jamauth.db");
        $this->loadDatabase();
    }
}