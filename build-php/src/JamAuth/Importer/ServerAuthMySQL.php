<?php
namespace JamAuth\Importer;

class ServerAuthSQLite extends DataImporter{
    
    private $db, $dbname;
    
    public function prepare(){
        $yml = rtrim(getcwd(), DIRECTORY_SEPARATOR)."/plugins/ServerAuth/config.yml";
        if(!is_file($yml)){
            return "ServerAuth config.yml is missing";
	}
        $conf = yaml_parse_file($yml);
        if(isset($conf["mysql"])){
            $dat = $conf["mysql"];
        }else{
            return "mysql is absent in ServerAuth config.yml";
        }
        $this->db = new \mysqli($dat["host"], $dat["username"], $dat["password"], $dat["database"], $dat["port"]);
        if(mysqli_connect_error()){
            return "MySQL Connection failed: ".mysqli_connect_error();
        }
        $this->dbname = $dat["table_prefix"]."serverauthdata";
	$c = $this->db->query("SELECT COUNT(*) AS total FROM ".$this->dbname);
        $this->setTotal($c->fetchAssoc()["total"]);
        $c->finalize();
	$res = $this->db->query("SELECT password_hash FROM ".$dat["table_prefix"]."serverauth");
        $this->data["hash"] = $res->fetchAssoc()["password_hash"];
        $res->finalize();
        return true;
        
    }
    
    public function import(){
        $res = $this->db->query("SELECT user, password, firstlogin FROM players");
	$i = 0;
        while(is_array($row = $res->fetchAssoc())){
            $i++;
            $data["name"] = $row["user"];
            $data["food"] = $row["password"];
            $data["time"] = $row["firstlogin"];
            $this->write($data);
            $this->setPace($i);
	}
        $this->db->close();
        $this->finalize();
    }
    
    public function getReaderName(){
        return "ServerAuth";
    }
    
    public function getReaderType(){
        return "MySQL";
    }
    
}