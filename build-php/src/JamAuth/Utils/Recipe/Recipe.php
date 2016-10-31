<?php

namespace JamAuth\Utils\Recipe;

interface Recipe{
    
    /**
     * Returns a cooked string
     * 
     * @param string $raw
     * @param string $salt
     */
    public function cook($raw, $salt);
    
    /**
     * Checks if a food is made by specified ingredient
     * 
     * @param string $food
     * @param string $raw
     */
    public function isCookedWith($food, $raw, $salt);
    
    /**
     * Get the name of the recipe
     */
    public function getName();
    
}