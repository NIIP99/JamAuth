<?php
namespace JamAuth\Utils\Recipe;

class SimpleAuthRecipe implements Recipe{
    
    const RECIPE_NAME = "SimpleAuth";
    private $data;
    
    public function __construct($data = []){
        $this->data = $data;
    }
    
    public function cook($raw, $salt){
        return bin2hex(hash("sha512", $raw.$salt, true) ^ hash("whirlpool", $salt.$raw, true));
    }
    
    public function isSameFood($foodA, $foodB){
        return hash_equals($foodA, $foodB);
    }
    
    public function getName(){
        return self::RECIPE_NAME;
    }
}