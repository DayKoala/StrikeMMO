<?php

namespace DayKoala\skill;

final class SkillFactory{

    private static $instance = null;

    public static function getInstance() : self{
        return self::$instance ?? (self::$instance = new self());
    }

    private array $skills;

    private function __construct(){

    }

}