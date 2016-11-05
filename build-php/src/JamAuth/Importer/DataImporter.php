<?php
namespace JamAuth\Importer;

use JamAuth\JamAuth;

abstract class DataImporter{
    
    protected $plugin;
    
    public function __construct(JamAuth $plugin){
        $this->plugin = $plugin;
    }
    
    public function write($data){
        
    }
}