<?php

namespace JamAuth\Utils\Recipe;

interface Recipe{
    
    /**
     * Returns a food
     * 
     * @param string $raw
     * @param string $salt
     */
    public function cook($raw, $salt);
    
    /**
     * Checks if two given foods are same
     * 
     * @param string $foodA
     * @param string $foodB
     */
    public function isSameFood($foodA, $foodB);
    
    /**
     * Get the name of the recipe
     */
    public function getName();
    
    /**
     * Check if this recipe requires salt or not
     */
    public function needSalt();
    
}