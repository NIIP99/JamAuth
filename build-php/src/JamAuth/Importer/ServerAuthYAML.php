<?php
namespace JamAuth\Importer;

class ServerAuthYAML extends DataImporter{
    
    private $dir;
    
    public function prepare(){
        $this->dir = rtrim(getcwd(), DIRECTORY_SEPARATOR)."/plugins/ServerAuth/users/";
        if(!is_dir($this->dir)){
            return "ServerAuth data directory '".$this->dir."' is not found";
	}
        
        $c = iterator_count(new \FilesystemIterator($this->dir, \FilesystemIterator::SKIP_DOTS));
        $this->setTotal($c);
        return true;
    }
    
    public function import(){
        $i = 0;
        $names = glob($this->dir."/"."*.{yml}", GLOB_BRACE);
        foreach($names as $name){
            $i++;
            $yaml = yaml_parse_file($name);
            $data["name"] = basename($name, true);
            $data["food"] = $yaml["password"];
            $data["time"] = $yaml["firstlogin"];
            $this->write($data);
            $this->setPace($i);
        }
        $this->plugin->sendInfo($this->plugin->getTranslator()->translate("import.end"));
    }
    
    public function getReaderName(){
        return "SimpleAuth";
    }
    
    public function getReaderType(){
        return "YAML";
    }
}