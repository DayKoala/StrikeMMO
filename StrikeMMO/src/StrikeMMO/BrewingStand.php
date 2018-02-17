<?php

/**
 *  ____    _            _   _             __  __   __  __    ___  
 * / ___|  | |_   _ __  (_) | | __   ___  |  \/  | |  \/  |  / _ \ 
 * \___ \  | __| | '__| | | | |/ /  / _ \ | |\/| | | |\/| | | | | |
 *  ___) | | |_  | |    | | |   <  |  __/ | |  | | | |  | | | |_| |
 * |____/   \__| |_|    |_| |_|\_\  \___| |_|  |_| |_|  |_|  \___/ 
 *
 * This plugin is about copyright by @DayKoala
 *
 */

namespace StrikeMMO;

use pocketmine\tile\BrewingStand;

use pocketmine\inventory\BrewingInventory;
use pocketmine\inventory\InventoryHolder;

use pocketmine\item\Item;

use pocketmine\level\Level;

use pocketmine\level\format\FullChunk;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;

use pocketmine\network\protocol\ContainerSetDataPacket;

use pocketmine\Server;

class BrewingStand extends BrewingStand{
    
    const MAX_BREW_TIME = 400;
	
    protected $inventory;
    
    public static $ingredients = [
		Item::NETHER_WART => 0,
		Item::GLOWSTONE_DUST => 0,
		Item::REDSTONE => 0,
		Item::FERMENTED_SPIDER_EYE => 0,
		Item::MAGMA_CREAM => 0,
		Item::SUGAR => 0,
		Item::GLISTERING_MELON => 0,
		Item::SPIDER_EYE => 0,
		Item::GHAST_TEAR => 0,
		Item::BLAZE_POWDER => 0,
		Item::GOLDEN_CARROT => 0,
		//Item::RAW_FISH => Fish::FISH_PUFFERFISH,
		Item::PUFFER_FISH,
		Item::RABBIT_FOOT => 0,
		Item::GUNPOWDER => 0,
    ];
    
    public function __construct(FullChunk $chunk, CompoundTag $nbt){
	parent::__construct($chunk, $nbt);
    }
    
    public function onUpdate(){    
        if($this->closed === true){
	   return false;
        }
        $this->timings->startTiming();
        $ret = false;
        $ingredient = $this->inventory->getIngredient();
        $canBrew = false;
        for($i = 1; $i <= 3; $i++){
	   if($this->inventory->getItem($i)->getId() === Item::POTION or
	      $this->inventory->getItem($i)->getId() === Item::SPLASH_POTION){
	      $canBrew = true;
	   }
         }
         if($ingredient->getId() !== Item::AIR and $ingredient->getCount() > 0){
	    if($canBrew){
	       if(!$this->checkIngredient($ingredient)){
		  $canBrew = false;
	       }
	    }
	    if($canBrew){
	       for($i = 1; $i <= 3; $i++){
		  $potion = $this->inventory->getItem($i);
		  $recipe = Server::getInstance()->getCraftingManager()->matchBrewingRecipe($ingredient, $potion);
		  if($recipe !== null){
		     $canBrew = true;
	             break;
		  }
		  $canBrew = false;
		}
	     }
	   }else{
	      $canBrew = false;
	   }
	   if($canBrew){
	      $this->namedtag->CookTime = new ShortTag("CookTime", $this->namedtag["CookTime"] - 1);
	      foreach($this->getInventory()->getViewers() as $player){
		 $windowId = $player->getWindowId($this->getInventory());
	         if($windowId > 0){
		    $pk = new ContainerSetDataPacket();
		    $pk->windowid = $windowId;
		    $pk->property = 0; //Brew
	            $pk->value = $this->namedtag["CookTime"];
		    $player->dataPacket($pk);
		  }
	       }
	       if($this->namedtag["CookTime"] <= 0){
		  $this->namedtag->CookTime = new ShortTag("CookTime", self::MAX_BREW_TIME);
                  $ingredientCount = null;
		  for($i = 1; $i <= 3; $i++){
		     $recipe = Server::getInstance()->getCraftingManager()->matchBrewingRecipe($ingredient, $potion);
		     if($recipe !== null and $potion->getId() !== Item::AIR){
                        foreach($this->inventory->getViewers() as $player){
                           Server::getInstance()->getPluginManager()->callEvent($event = new PlayerBrewingEvent($player, $recipe->getResult(), $this->inventory));
                           if(!$event->isCancelled()){
                              $this->inventory->setItem($i, $recipe->getResult());
                              if($ingredientCount == null){
                                 $ingredient->count--;
				 if($ingredient->getCount() <= 0) $ingredient = Item::get(Item::AIR);
				    $this->inventory->setIngredient($ingredient);
                                    $ingredientCount = true;
                              }
                           }
                        }
                     }
		  }
	       }
	       $ret = true;
	     }else{
	        $this->namedtag->CookTime = new ShortTag("CookTime", self::MAX_BREW_TIME);
	        foreach($this->getInventory()->getViewers() as $player){
		   $windowId = $player->getWindowId($this->getInventory());
		   if($windowId > 0){
		      $pk = new ContainerSetDataPacket();
		      $pk->windowid = $windowId;
		      $pk->property = 0; //Brew
		      $pk->value = 0;
		      $player->dataPacket($pk);
		   }
		}
	     }
	     $this->lastUpdate = microtime(true);
	     $this->timings->stopTiming();
	     return $ret;
    }
}
