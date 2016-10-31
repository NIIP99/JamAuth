<?php
namespace JamAuth\Utils\Recipe;

class JamAuthRecipe implements Recipe{
    
    const RECIPE_NAME = "JamAuth";
    private $data;
    
    public function __construct($data = []){
        $this->data = $data;
    }
    
    public function cook($raw, $salt){
        $halfCooked = hash("sha256", $raw.$salt);
        $cookTime = $this->data["hashLimit"];
        for($i = 0; $i < $cookTime; $i++){
            $food = hash("sha256", $halfCooked);
        }
        return $food;
    }
    
    public function isCookedWith($food, $raw, $salt){
        return hash_equals($food, $this->cook($raw, $salt));
    }
    
    public function getName(){
        return self::RECIPE_NAME;
    }
}