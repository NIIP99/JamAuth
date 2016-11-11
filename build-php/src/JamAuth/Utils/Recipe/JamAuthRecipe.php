<?php
namespace JamAuth\Utils\Recipe;

class JamAuthRecipe implements Recipe{
    
    const RECIPE_NAME = "JamAuth";
    private $cookLoop = 26;
    
    public function __construct($data = []){
        if(isset($data["cookLoop"])){
            $this->cookLoop = $data["cookLoop"];
        }
    }
    
    public function cook($raw, $salt){
        $halfCooked = hash("sha256", $raw.$salt);
        $loop = $this->cookLoop;
        for($i = 0; $i < $loop; $i++){
            $food = hash("sha256", $halfCooked);
        }
        return $food;
    }
    
    public function isSameFood($foodA, $foodB){
        return hash_equals($foodA, $foodB);
    }
    
    public function getName(){
        return self::RECIPE_NAME;
    }
    
    public function needSalt(){
        return true;
    }
}