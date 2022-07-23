<?php

namespace DayKoala;

use pocketmine\plugin\PluginBase;

use DayKoala\provider\Provider;
use DayKoala\provider\ArchaicYamlProvider;

use DayKoala\event\EventManager;

class StrikeMMO extends PluginBase{

    private static $instance = null;

    public static function getInstance() : ?self{
        return self::$instance;
    }

    private EventManager $manager;

    private ?Provider $provider = null;

    public function onLoad() : Void{
        self::$instance = $this;
    }

    public function onEnable() : Void{
        $this->getServer()->getPluginManager()->registerEvents($this->manager = EventManager::getInstance(), $this);

        if($this->provider === null) $this->provider = new ArchaicYamlProvider($this->getDataFolder());
    }

    public function onDisable() : Void{
        if($this->provider !== null) $this->provider->save();
    }

    public function getEventManager() : EventManager{
        return $this->manager;
    }

    public function getProvider() : Provider{
        return $this->provider;
    }

    public function setProvider(Provider $provider) : Void{
        $this->provider = $provider;
    }

}