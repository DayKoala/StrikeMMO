<?php

namespace DayKoala\skill;

use pocketmine\event\Event;

abstract class Skill{

    use SkillTrait;

    public function __construct(

        private string $name,
        private array $modifiers,

    ){

        $this->name = $name;
        $this->modifiers = $modifiers;

    }

    abstract public function execute(Event $event);

    public function getName() : String{
        return $this->name;
    }

    public function hasModifiers() : Bool{
        return (Bool) $this->modifiers;
    }

    public function getModifiers() : Array{
        return $this->modifiers;
    }

}