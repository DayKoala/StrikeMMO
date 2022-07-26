<?php

namespace DayKoala\provider;

use pocketmine\player\Player;

interface Provider{

    public function exists(String $player) : Bool;

    public function get(String $player) : ?Array;

    public function create(Player $player) : Void;

    public function delete(String $player) : Void;

    public function update(Player $player) : Void;

    public function hasSkill(String $player, String $skill) : Bool;

    public function mySkillLevel(String $player, String $skill) : Int;

    public function setSkillLevel(String $player, String $skill, Int $amount) : Void;

    public function addSkillLevel(String $player, String $skill, Int $amount) : Void;

    public function reduceSkillLevel(String $player, String $skill, Int $amount) : Void;

    public function mySkillExperience(String $player, String $skill) : Int|Float;
    
    public function setSkillExperience(String $player, String $skill, Int|Float $amount) : Void;

    public function addSkillExperience(String $player, String $skill, Int|Float $amount) : Void;

    public function reduceSkillExperience(String $player, String $skill, Int|Float $amount) : Void;

    public function getAll() : ?Array;

    public function getName() : String;

    public function save() : Void;

}