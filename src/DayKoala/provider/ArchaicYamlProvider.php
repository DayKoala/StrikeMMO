<?php

namespace DayKoala\provider;

use pocketmine\utils\Config;

use pocketmine\player\Player;

use DayKoala\skill\Skill;

use DayKoala\utils\SkillDataConverter as Converter;

class ArchaicYamlProvider implements Provider{

    protected Config $yaml;
    protected array $data;

    public function __construct(String $folder){
        mkdir($folder);

        $this->data = ($this->yaml = new Config($folder .'Data.yml', Config::YAML))->getAll();
    }

    public function exists(Player|String $player) : Bool{
        if($player instanceof Player){
           $player = $player->getName();
        }
        return isset($this->data[strtolower($player)]);
    }

    public function get(Player|String $player) : ?Array{
        if($player instanceof Player){
           $player = $player->getName();
        }
        return $this->data[strtolower($player)] ?? [];
    }

    public function create(Player $player) : Void{
        if(isset($this->data[strtolower($player->getName())])){
           return;
        }
        $this->data[strtolower($player->getName())] = Converter::get();
    }

    public function delete(Player|String $player) : Void{
        if($player instanceof Player){
           $player = $player->getName();
        }
        if(isset($this->data[strtolower($player)])) unset($this->data[strtolower($player)]);
    }

    public function update(Player|String $player) : Void{
        if($player instanceof Player){
           $player = $player->getName();
        }
        if(isset($this->data[strtolower($player)])):

           $data = Converter::implement($this->data[strtolower($player)]);
           
           $this->data[strtolower($player)] = Converter::subtract($data);

        endif;
    }

    public function hasSkill(Player|String $player, Skill|String $skill) : Bool{

        if($player instanceof Player) $player = $player->getName();
        if($skill instanceof Skill) $skill = $skill->getName();

        return ($this->exists($player) and isset($this->data[strtolower($player)][$skill]));
    }

    public function mySkillLevel(Player|String $player, Skill|String $skill) : Int{
        
        if($player instanceof Player) $player = $player->getName();
        if($skill instanceof Skill) $skill = $skill->getName();

        return $this->hasSkill($player, $skill) ? $this->data[strtolower($player)][$skill][Converter::TAG_LEVEL] : 1;
    }

}