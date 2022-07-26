<?php

namespace DayKoala\provider\type;

use pocketmine\utils\Config;

use pocketmine\player\Player;

class ArchaicYamlProvider implements Provider{

    protected Config $yaml;
    protected array $data;

    public function __construct(String $folder){
        mkdir($folder);

        $this->data = ($this->yaml = new Config($folder .'Data.yml', Config::YAML))->getAll();
    }

    public function exists(String $player) : Bool{
        return isset($this->data[strtolower($player)]);
    }

    public function get(String $player) : ?Array{
        return $this->data[strtolower($player)] ?? [];
    }

    public function create(Player $player) : Void{
        $this->data[strtolower($player)] = [];
    }

    public function delete(String $player) : Void{
        if(isset($this->data[strtolower($player)])) unset($this->data[strtolower($player)]);
    }

    public function update(Player $player) : Void{
        $this->data[strtolower($player)] = [];
    }

    public function hasSkill(String $player, String $skill) : Void{
        return (isset($this->data[strtolower($player)]) and isset($this->data[strtolower($player)][$skill]));
    }

    public function mySkillLevel(String $player, String $skill) : Int{
        return $this->hasSkill($player, $skill) ? $this->data[strtolower($player)][$skill]["level"] : 1;
    }

    public function setSkillLevel(String $player, String $skill, Int $amount) : Void{
        $this->data[strtolower($player)][$skill]["level"] = $amount;
    }

    public function addSkillLevel(String $player, String $level, Int $amount) : Void{
        if($this->hasSkill($player, $skill)) $this->data[strtolower($player)][$skill]["level"] += $amount;
    }

    public function reduceSkillLevel(String $player, String $level, Int $amount) : Void{
        if($this->hasSkill($player, $skill)) $this->data[strtolower($player)][$skill]["level"] -= $amount;
    }

    public function mySkillExperience(String $player, String $skill) : Int|Float{
        return $this->hasSkill($player, $skill) ? $this->data[strtolower($player)][$skill]["experience"] : 0;
    }

    public function setSkillExperience(String $player, String $skill, Int|Float $amount) : Void{
        $this->data[strtolower($player)][$skill]["experience"] = $amount;
    }

    public function addSkillExperience(String $player, String $level, Int|Float $amount) : Void{
        if($this->hasSkill($player, $skill)) $this->data[strtolower($player)][$skill]["experience"] += $amount;
    }

    public function reduceSkillExperience(String $player, String $level, Int|Float $amount) : Void{
        if($this->hasSkill($player, $skill)) $this->data[strtolower($player)][$skill]["experience"] -= $amount;
    }

    public function getAll() : ?Array{
        return $this->data;
    }

    public function getName() : String{
        return "YamlProvider";
    }

    public function save() : Void{
        $this->yaml->setAll($this->data);
        $this->yaml->save();
    }

}