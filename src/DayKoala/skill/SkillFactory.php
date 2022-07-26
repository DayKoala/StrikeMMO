<?php

namespace DayKoala\skill;

final class SkillFactory{

    private static $instance = null;

    public static function getInstance() : self{
        return self::$instance ?? (self::$instance = new self());
    }

    private array $skills = [];

    public function exists(Skill|String $skill) : Bool{
        return isset($this->skills[$skill instanceof Skill ? $skill->getName() : $skill]);
    }

    public function register(Skill $skill, Bool $override = false) : Void{
        if(isset($this->skills[$skill->getName()]) and $override === false){
           return;
        }
        $this->skills[$skill->getName()] = $skill;
    }

    public function unregister(Skill|String $skill) : Void{
        if($skill instanceof Skill){
           $skill = $skill->getName();
        }
        if(isset($this->skills[$skill])) unset($this->skills[$skill]);
    }

    public function getAll() : ?Array{
        return $this->skills;
    }

    private function __construct(){}

}