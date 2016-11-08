<?php
namespace JamAuth\Importer;

class SimpleAuthMySQL extends DataImporter{
    
    private $db;
    
    public function prepare(){
        $yml = rtrim(getcwd(), DIRECTORY_SEPARATOR)."/plugins/SimpleAuth/config.yml";
        if(!is_file($yml)){
            return "SimpleAuth config.yml is missing";
	}
        $conf = yaml_parse_file($yml);
        if(isset($conf["dataProviderSettings"])){
            $dat = $conf["dataProviderSettings"];
        }else{
            return "dataProviderSettings is absent in SimpleAuth config.yml";
        }
        $this->db = new \mysqli($dat["host"], $dat["user"], $dat["password"], $dat["database"], $dat["port"]);
        if(mysqli_connect_error()){
            return "MySQL Connection failed: ".mysqli_connect_error();
        }
	$res = $this->db->query("SELECT COUNT(*) AS total FROM players");
        $this->setTotal($res->fetchAssoc()["total"]);
        $res->finalize();
        return true;
    }
    
    public function import(){
        $res = $this->db->query("SELECT name, registerdate, lastip, hash FROM players");
	$i = 0;
        while(is_array($row = $res->fetchAssoc())){
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
        return "MySQL";
    }
}