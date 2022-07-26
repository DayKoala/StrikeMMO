<?php

namespace DayKoala;

use pocketmine\plugin\PluginBase;

use DayKoala\provider\Provider;
use DayKoala\provider\ArchaicYamlProvider;

use DayKoala\event\EventManager;

class StrikeMMO extends PluginBase{

    private static ?StrikeMMO $instance = null;
    private static ?ProviderManager $manager = null;

    public static function getInstance() : ?StrikeMMO{
        return self::$instance;
    }

    public static function getProviderManager() : ?ProviderManager{
        return self::$manager;
    }

    private ?Provider $provider = null;

    public function onLoad() : Void{
        self::$instance = $this;
    }

    public function onEnable() : Void{
        if($this->provider === null){
           $this->provider = new ArchaicYamlProvider($this->getDataFolder());
        }
        self::$manager = new ProviderManager($this->provider);

        $this->getServer()->getPluginManager()->registerEvents(EventManager::getInstance(), $this);
    }

    public function onDisable() : Void{
        $this->provider->save();
    }

    public function hasProvider() : Bool{
        return (Bool) $this->provider;
    }

    public function getProvider() : ?Provider{
        return $this->provider;
    }

    public function setProvider(Provider $provider) : Void{
        $this->provider = $provider;
    }

}