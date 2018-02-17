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

use pocketmine\Player;

use pocketmine\item\Item;

use pocketmine\inventory\Inventory;

use pocketmine\event\player\PlayerEvent;
use pocketmine\event\Cancellable;

class PlayerBrewingEvent extends PlayerEvent implements Cancellable{
	
	public static $handlerList = null;
	public static $eventPool = [];
	public static $nextEvent = 0;
    
	protected $player; 
	protected $potion;
    protected $inventory;
	
	public function __construct(Player $player, Item $potion, Inventory $inventory){
		$this->player = $player;
		$this->potion = $potion;
        $this->inventory = $inventory;
	}
	
	public function getPlayer() : Player{
		return $this->player;
	}
    
    public function getInventory() : Inventory{
		return $this->inventory;
	}
	
	public function getPotion() : Item{
		return $this->potion;
	}
}