<?php

namespace DayKoala\event;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerEvent;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;

use pocketmine\event\entity\EntityDamageEvent;

use DayKoala\scheduler\EventHolder;

use DayKoala\skill\Skill;

final class EventManager extends Listener{

    private static $instance = null;

    public static function getInstance() : self{
        return self::$instance ?? (self::$instance = new self());
    }

    private array $handlers;

    public function execute(PlayerEvent|BlockPlaceEvent|BlockBreakEvent|EntityDamageEvent $event){
        if(isset($this->handlers[$event::class]) === false){
           return;
        }
        foreach($this->handlers[$event::class] as $handler) $handler->execute($event);
    }

    public function hasHandler(Skill $skill) : Bool{
        return in_array($skill->getName(), array_values($this->handlers));
    }

    public function addHandler(Skill $skill) : Void{
        if($this->hasHandler($skill)){
           return;
        }
        foreach($skill->getModifiers() as $modifier) $this->handlers[$modifier] = $skill;
    }

    public function removeHandler(Skill $skill) : Void{
        if(!$this->hasHandler($skill)){
           return;
        }
        foreach($skill->getModifiers() as $modifier):
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