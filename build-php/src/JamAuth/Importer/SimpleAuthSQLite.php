<?php
namespace JamAuth\Importer;

class SimpleAuthSQLite extends DataImporter{
    
    public function import(){
        $file = rtrim(getcwd(), DIRECTORY_SEPARATOR)."/plugins/SimpleAuth/players.db";
        if(!is_file($file)){
            $this->plugin->sendInfo("SimpleAuth Data Missing");
            return false;
	}
        $db = new \SQLite3($file);
	$res = $db->query("SELECT COUNT(*) AS total FROM players");
        $this->setTotal($res->fetchArray(SQLITE3_ASSOC)["total"]);
        $res->finalize();
        
        //Indexing
        $res = $db->query("SELECT name, registerdate, lastip, hash FROM players");
	$i = 0;
        while(is_array($row = $res->fetchArray(SQLITE3_ASSOC))){
            $i++;
            $data["name"] = $row["name"];
            $data["food"] = $row["hash"];
            $data["time"] = $row["registerdate"];
            $this->write($data);
            $this->setPace($i);
	}
        $db->close();
    }
    
    public function getReaderName(){
        return "SimpleAuth";
    }
    
    public function getReaderType(){
        return "SQLite";
    }
    
}