<?php
namespace JamAuth\Utils;

use pocketmine\Player;

use JamAuth\JamAuth;
use SQLite3;

class LocalDatabase{
    
    private $plugin;
    private $db;
    private $registerStmt, $fetchStmt;
    
    public function __construct(JamAuth $plugin){
        $this->db = new SQLite3($plugin->getDataFolder()."data/jamauth.db");
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS users
            (username TEXT PRIMARY KEY, time INTEGER, food TEXT, salt TEXT, extData TEXT)"
        );
        $stmts = [
            "registerStmt" =>
            "INSERT INTO users (username, time, food, salt, extData)
             VALUES (:username, :time, :food, :salt, :extData)",
            
            "fetchStmt" =>
            "SELECT food, salt, extData
             FROM users
             WHERE username = :username"
        ];
        foreach($stmts as $key => $stmt){
            $this->{$key} = $this->db->prepare($stmt);
        }
    }
    
    public function register($pn, $time, $food, $salt, $data = []){
        $stmt = $this->registerStmt;
        
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
        $stmt = $this->fetchStmt;
        
        $stmt->bindValue(":username", $pn, SQLITE3_TEXT);
        
        $stmt->reset();
        $res = $stmt->execute();
        if(($val = $res->fetchArray(SQLITE3_ASSOC))){
            return $val;
        }
        return null;
    }
    
}