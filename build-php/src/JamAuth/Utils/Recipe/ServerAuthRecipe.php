<?php
/*
 * ServerAuth Recipe is strongly not recommended
 * Please manually improve the recipe using rules in jamauth.db
 */
namespace JamAuth\Utils\Recipe;

class ServerAuthRecipe implements Recipe{
    
    const RECIPE_NAME = "ServerAuth";
    private $data;
    
    public function __construct($data = []){
        $this->data = $data;
    }
    
    public function cook($raw, $salt = null){
        //TODO Implement advanced modification
        return hash($this->data["hashAlgo"], $raw);
    }
    
    public function isSameFood($foodA, $foodB){
        if(isset($this->data["hashEquals"])){
            return hash_equals($foodA, $foodB);
        }else{
            return $foodA == $foodB;
        }
    }
    
    public function getName(){
        return self::RECIPE_NAME;
    }
    
    public function needSalt(){
        return false;
    }
}