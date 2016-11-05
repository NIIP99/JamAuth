<?php
namespace JamAuth\Importer;

class SimpleAuthYAML extends DataImporter{
    
    public function read(){
        $dir = rtrim(getcwd(), DIRECTORY_SEPARATOR)."/plugins/SimpleAuth/players/";
        if(!is_dir($dir)){
            $this->plugin->sendInfo("SimpleAuth Data Missing");
            return false;
	}
        //Indexing
        foreach(glob($dir."*", GLOB_ONLYDIR) as $dir){
            $names = glob($dir."/"."*.{yml}", GLOB_BRACE);
            foreach($names as $name){
                $data = yaml_parse_file($name);
                
            }
        }
    }
}