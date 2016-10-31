<?php
/*
 * ServerAuth Recipe is strongly not recommended
 * Please manually improve the recipe using recipe.data in config.yml
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
    
    public function isCookedWith($food, $raw, $salt = null){
        if(isset($this->data["hashEquals"])){
            return hash_equals($food, $raw);
        }else{
            return $food == $this->cook($raw);
        }
    }
    
    public function getName(){
        return self::RECIPE_NAME;
    }
}