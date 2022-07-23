<?php

namespace DayKoala\provider;

use pocketmine\player\Player;

use DayKoala\skill\Skill;

interface Provider{

    public function exists(Player|String $player) : Bool;

    public function get(Player|String $player) : ?Array;

    public function create(Player $player) : Void;

    public function delete(Player|String $player) : Void;

    public function update(Player|String $player) : Void;

    public function hasSkill(Player|String $player, Skill|String $skill) : Bool;

    public function mySkillLevel(Player|String $player, Skill|String $skill) : Int;

    public function setSkillLevel(Player|String $player, Skill|String $skill, Int $amount) : Void;

    public function addSkillLevel(Player|String $player, Skill|String $skill, Int $amount) : Void;

    public function reduceSkillLevel(Player|String $player, Skill|String $skill, Int $amount) : Void;

    public function mySkillExperience(Player|String $player, Skill|String $skill) : Int|Float;
    
    public function setSkillExperience(Player|String $player, Skill|String $skill, Int|Float $amount) : Void;

    public function addSkillExperience(Player|String $player, Skill|String $skill, Int|Float $amount) : Void;

    public function reduceSkillExperience(Player|String $player, Skill|String $skill, Int|Float $amount) : Void;

    public function getName() : String;

    public function getAll() : ?Array;

    public function save();

}