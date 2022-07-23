<?php

namespace DayKoala;

class StrikeMMO extends PluginBase{

    private static $instance = null;

    public static function getInstance() : ?self{
        return self::$instance;
    }

    private $provider;

    public function onLoad() : Void{
        self::$instance = $this;
    }

    public function onEnable() : Void{
        
    }

    public function onDisable() : Void{

    }

    public function getProvider() : Provider{
        return $this->provider;
    }

}