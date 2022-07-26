<?php

namespace DayKoala\provider;

use pocketmine\player\Player;

use DayKoala\skill\Skill;
use DayKoala\skill\SkillFactory;

class ProviderManager{

    public const MIN_LEVEL = 1;
    public const MIN_EXPERIENCE = 0;

    public function __construct(

        protected Provider $provider

    ){

        $this->provider = $provider;

    }

    public function exists(Player|String $player) : Bool{
        return $this->provider->exists($player instanceof Player ? $player->getName() : $player);
    }

    public function get(Player|String $player) : ?Array{
        return $this->provider->get($player instanceof Player ? $player->getName() : $player);
    }

    public function delete(Player|String $player) : Void{
        $this->provider->delete($player instanceof Player ? $player->getName() : $player);
    }

    public function hasSkill(Player|String $player, Skill|String $skill) : Bool{
        return $this->provider->hasSkill(
            $player instanceof Player ? $player->getName() : $player,
            $skill instanceof Skill ? $skill->getName() : $skill
        );
    }

    public function mySkillLevel(Player|String $player, Skill|String $skill) : Int{
        return $this->provider->mySkillLevel(
            $player instanceof Player ? $player->getName() : $player,
            $skill instanceof Skill ? $skill->getName() : $skill
        );
    }

    public function setSkillLevel(Player|String $player, Skill|String $skill, Int $amount) : Void{
        if($amount < self::MIN_LEVEL){
           return;
        }
        if($skill instanceof String){
           $skill = SkillFactory::getInstance()->get($skill);
           if($skill === null)
              return;
        }
        if($amount > $skill->getMax()){
           $amount = $skill->getMax();
        }

        if($player instanceof Player) $player = $player->getName();

        if($this->provider->mySkillLevel($player, $skill->getName()) > $amount){
           $this->provider->setSkillExperience(
               $player, $skill->getName(), self::MIN_EXPERIENCE
           );
        }
        $this->provider->setSkillLevel(
            $player, $skill->getName(), $amount
        );
    }

    public function addSkillLevel(Player|String $player, Skill|String $skill, Int $amount) : Void{
        if($amount < self::MIN_LEVEL){
           return;
        }
        if($skill instanceof String){
           $skill = SkillFactory::getInstance()->get($skill);
           if($skill === null)
              return;
        }
        
        if($player instanceof Player) $player = $player->getName();

        $amount = $this->provider->mySkillLevel($player, $skill->getName()) + $amount;
        if($amount > $skill->getMax()){
           $amount = $skill->getMax();
        }
        $this->provider->setSkillLevel(
            $player, $skill->getName(), $amount
        );
    }

    public function reduceSkillLevel(Player|String $player, Skill|String $skill, Int $amount) : Void{
        if($amount < self::MIN_LEVEL){
           return;
        }
        if($skill instanceof String){
           $skill = SkillFactory::getInstance()->get($skill);
           if($skill === null)
              return;
        }
        
        if($player instanceof Player) $player = $player->getName();

        $amount = $this->mySkillLevel($player, $skill->getName()) + $amount;
        if($amount < self::MIN_LEVEL){
           $amount = self::MIN_LEVEL;        
        }
        $this->provider->setSkillLevel(
            $player, $skill->getName(), $amount
        );
        $this->provider->setSkillExperience(
            $player, $skill->getName(), self::MIN_EXPERIENCE
        );
    }

    public function mySkillExperience(Player|String $player, Skill|String $skill) : Int|Float{
        return $this->provider->mySkillExperience(
            $player instanceof Player ? $player->getName() : $player,
            $skill instanceof Skill ? $skill->getName() : $skill
        );
    }

    public function setSkillExperience(Player|String $player, Skill|String $skill, Int|Float $amount) : Void{
        if($amount < self::MIN_EXPERIENCE){
           return;
        }
        if($skill instanceof String){
           $skill = SkillFactory::getInstance()->get($skill);
           if($skill === null)
              return;
        }
        
        if($player instanceof Player) $player = $player->getName();

        $level = $this->provider->mySkillLevel($player, $skill->getName());
        if($level === $skill->getMax()){
           return;
        }
        $experience = $skill->multiply($level);
        if($amount > $experience){
           for($next = ($level + 1); $next <= $skill->getMax(); $next++){
              if($next === $skill->getMax()){
                 $amount = 0;
                 break;
              }
              $experience = $skill->multiply($next);
              if($experience <= $amount){
                 $amount -= $experience;
                 continue;
              }
              break;
            }
            $this->provider->setSkillLevel(
                $player, $skill->getName(), $next
            );
        }elseif($amount === $experience){
            $this->provider->addSkillLevel(
                $player, $skill->getName(), 1
            );
            $amount = 0;
        }
        $this->provider->setSkillExperience(
            $player, $skill->getName(), $amount
        );
    }

    public function addSkillExperience(Player|String $player, Skill|String $skill, Int|Float $amount) : Void{
        if($amount < self::MIN_EXPERIENCE){
           return;
        }
        if($skill instanceof String){
           $skill = SkillFactory::getInstance()->get($skill);
           if($skill === null)
              return;
        }
        
        if($player instanceof Player) $player = $player->getName();

        $level = $this->provider->mySkillLevel($player, $skill->getName());
        if($level === $skill->getMax()){
           return;
        }
        $amount += $this->provider->mySkillExperience($player, $skill->getName());
        $experience = $skill->multiply($level);
        if($amount > $experience){
           for($next = ($level + 1); $next <= $skill->getMax(); $next++){
              if($next === $skill->getMax()){
                 $amount = 0;
                 break;
              }
              $experience = $skill->multiply($next);
              if($experience <= $amount){
                 $amount -= $experience;
                 continue;
              }
              break;
            }
            $this->provider->setSkillLevel(
                $player, $skill->getName(), $next
            );
        }elseif($amount === $experience){
            $this->provider->addSkillLevel(
                $player, $skill->getName(), 1
            );
            $amount = 0;
        }
        $this->provider->setSkillExperience(
            $player, $skill->getName(), $amount
        );
    }

    public function reduceSkillExperience(Player|String $player, Skill|String $skill, Int|Float $amount) : Void{
        if($amount < self::MIN_EXPERIENCE){
           return;
        }
        if($skill instanceof String){
           $skill = SkillFactory::getInstance()->get($skill);
           if($skill === null)
              return;
        }
         
        if($player instanceof Player) $player = $player->getName();
 
        $amount -= $this->provider->mySkillExperience($player, $skill->getName());
        if($amount < self::MIN_EXPERIENCE){
           $amount = 0;
        }
        $this->provider->setSkillExperience(
            $player, $skill->getName(), $amount
        );
    }

    public function getProvider() : Provider{
        return $this->provider;
    }

}