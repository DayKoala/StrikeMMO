<?php

namespace DayKoala\event;

use pocketmine\event\Listener;
use pocketmine\event\Event;

use DayKoala\skill\Skill;

final class EventManager extends Listener{

    public const PRIORITY_DEFAULT = 0;
    public const PRIORITY_MAX = 1;

    private static $instance = null;

    public static function getInstance() : self{
        return self::$instance ?? (self::$instance = new self());
    }

    private array $handlers;

    public function execute(Event $event){
        if(isset($this->handlers[$event::class]) === false){
           return;
        }
        foreach($this->handlers as $handler):
            if($handler->getModifierPriority($event::class) === self::PRIORITY_MAX){
               $handler->execute($event);
            }else{
               // Add request.
            }
        endforeach;
    }

    public function hasHandler(Skill $skill) : Bool{
        return in_array($skill->getName(), array_values($this->handlers));
    }

    public function addHandler(Skill $skill) : Void{
        if($this->hasHandler($skill)){
           return;
        }
        foreach(array_values($skill->getModifiers()) as $modifier) $this->handlers[$modifier] = $skill;
    }

    public function removeHandler(Skill $skill) : Void{
        if(!$this->hasHandler($skill)){
           return;
        }
        foreach(array_values($skill->getModifiers()) as $modifier):
           if(isset($this->handlers[$modifier])){
              unset($this->handlers[$modifier]);
           }
        endforeach;
    }

    public function close() : Void{
        $this->handlers = [];
    }

    private function __construct(){}

}