<?php

namespace DayKoala\skill;

trait SkillTrait{

    private int $max = 1;
    private int $multiplier = 1000;

    public function getMax() : Int{
        return $this->max;
    }

    public function setMax(Int $max) : Void{
        $this->max = $max < 1 ? 1 : $max;
    }

    public function getMultiplier() : Int{
        return $this->multiplier;
    }

    public function setMultitplier() : Void{
        $this->multiplier = $multiplier < 1000 ? 1000 : $multiplier;
    }

    public function multiply(Int $amount) : Int{
        return ($this->multiplier * $amount);
    }

}