<?php
namespace JamAuth\Importer;

class SimpleAuthYAML extends DataImporter{
    
    private $dir;
    
    public function prepare(){
        $this->dir = rtrim(getcwd(), DIRECTORY_SEPARATOR)."/plugins/SimpleAuth/players/";
        if(!is_dir($this->dir)){
            return "SimpleAuth data directory '".$this->dir."' is not found";
	}
        
        //Tweaks are required
        $c = 0;
        foreach(glob($this->dir."*", GLOB_ONLYDIR) as $dir){
            $c += count(glob($dir."/"."*.{yml}", GLOB_BRACE));
        }
        $this->setTotal($c);
        return true;
    }
    
    public function import(){
        $i = 0;
        foreach(glob($this->dir."*", GLOB_ONLYDIR) as $dir){
            $names = glob($dir."/"."*.{yml}", GLOB_BRACE);
            foreach($names as $name){
                $i++;
                $yaml = yaml_parse_file($name);
                $data["name"] = basename($name, true);
                $data["food"] = $yaml["hash"];
                $data["time"] = $yaml["registerdate"];
                $this->write($data);
                $this->setPace($i);
            }
        }
        $this->finalize();
    }
    
    public function getReaderName(){
        return "SimpleAuth";
    }
    
    public function getReaderType(){
        return "YAML";
    }
}