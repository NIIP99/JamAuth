<?php
namespace JamAuth\Importer;

class SimpleAuthSQLite extends DataImporter{
    
    private $db;
    
    public function prepare(){
        $file = rtrim(getcwd(), DIRECTORY_SEPARATOR)."/plugins/SimpleAuth/players.db";
        if(!is_file($file)){
            $this->plugin->sendInfo("SimpleAuth Data Missing");
            return false;
	}
        $this->db = new \SQLite3($file);
	$res = $this->db->query("SELECT COUNT(*) AS total FROM players");
        $this->setTotal($res->fetchArray(SQLITE3_ASSOC)["total"]);
        $res->finalize();
    }
    
    public function import(){
        $res = $this->db->query("SELECT name, registerdate, lastip, hash FROM players");
	$i = 0;
        while(is_array($row = $res->fetchArray(SQLITE3_ASSOC))){
            $i++;
            $data["name"] = $row["name"];
            $data["food"] = $row["hash"];
            $data["time"] = $row["registerdate"];
            $this->write($data);
            $this->setPace($i);
	}
        $this->db->close();
    }
    
    public function getReaderName(){
        return "SimpleAuth";
    }
    
    public function getReaderType(){
        return "SQLite";
    }
    
}