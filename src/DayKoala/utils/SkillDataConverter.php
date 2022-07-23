<?php

namespace DayKoala\utils;

use DayKoala\skill\SkillFactory;

final class SkillDataConverter{

    public const TAG_SKILL = "skill";

    public const TAG_LEVEL = "level";
    public const TAG_EXPERIENCE = "experience";

    private static array $converted = [];

    public static function convert() : Void{
        $skills = SkillFactory::getInstance()->getAll();
        if($skills === null){
           return;
        }
        foreach($skills as $skill) self::$converted[$skill->getName()] = [self::TAG_SKILL => $skill->getName(), self::TAG_LEVEL => 1, self::TAG_EXPERIENCE => 0];
    }

    public static function get() : ?Array{
        if(self::$converted === null) self::convert();
        
        return self::$converted ?? [];
    }

    public static function same(Array $data) : Bool{
        if(self::$converted === null) self::convert();

        return (self::$converted == $data);
    }

    public static function implement(Array $data) : Array{
        if(self::same($data)) return $data;

        if(self::$converted === null) return [];

        foreach(self::$converted as $name => $args){
           if(isset($data[$name]) === false) $data[$name] = $args;
        }
        return $data;
    }

    public static function subtract(Array $data) : Array{
        if(self::same($data)) return $data;

        if(self::$converted === null) return [];

        foreach(self::$data as $name => $args){
           if(isset(self::$converted[$name]) === false) unset($data[$name]);
        }
        return $data;
    }

    private function __construct(){}

}