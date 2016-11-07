<?php
namespace JamAuth\Importer;

class SimpleAuthMySQL extends DataImporter{
    
    public function import(){
        $yml = rtrim(getcwd(), DIRECTORY_SEPARATOR)."/plugins/SimpleAuth/config.yml";
        if(!is_file($yml)){
            $this->plugin->sendInfo("SimpleAuth Data Missing");
            return false;
	}
        $conf = yaml_parse_file($yml);
        if(isset($conf["dataProviderSettings"])){
            $dat = $conf["dataProviderSettings"];
        }else{
            $this->plugin->sendInfo("SimpleAuth Data Missing");
            return false;
        }
        $db = new \mysqli($dat["host"], $dat["user"], $dat["password"], $dat["database"], $dat["port"]);
        if(mysqli_connect_error()){
            return false;
        }
	$res = $db->query("SELECT COUNT(*) AS total FROM players");
        $this->setTotal($res->fetchAssoc()["total"]);
        $res->finalize();
        
        //Indexing
        $res = $db->query("SELECT name, registerdate, lastip, hash FROM players");
	$i = 0;
        while(is_array($row = $res->fetchAssoc())){
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
        return "MySQL";
    }
}