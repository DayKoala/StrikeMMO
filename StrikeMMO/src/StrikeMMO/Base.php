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

use pocketmine\plugin\PluginBase;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\NBT;

use pocketmine\block\Block;

use pocketmine\tile\Tile;

use pocketmine\item\Item;

use pocketmine\Player; 

use pocketmine\utils\Config;

use pocketmine\scheduler\PluginTask;

class Base extends PluginBase implements Listener{
    
    public $booster, $data, $base;
    
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Helper($this), 20, 20);
        @mkdir($this->getDataFolder());
               $this->data = (new Config($this->getDataFolder() ."Data.json", Config::JSON))->getAll();
               $this->booster = (new Config($this->getDataFolder() ."Booster.json", Config::JSON))->getAll();
               $this->base = ["mining" => ["level" => 1, "exp" => 0], "lumberjack" => ["level" => 1, "exp" => 0], "excavation" => ["level" => 1, "exp" => 0], 
                              "repair" => ["level" => 1, "exp" => 0], "harvest" => ["level" => 1, "exp" => 0], "swords" => ["level" => 1, "exp" => 0],
                              "axes" => ["level" => 1, "exp" => 0], "acrobatics" => ["level" => 1, "exp" => 0], "alchemy" => ["level" => 1, "exp" => 0]];
    }
    
    public function onDisable(){
        $data = (new Config($this->getDataFolder() ."Data.json", Config::JSON));
	$data->setAll($this->data); 
	$data->save();
        
        $booster = (new Config($this->getDataFolder() ."Booster.json", Config::JSON));
	$booster->setAll($this->booster); 
	$booster->save();
    }
    
    public function getPlayerMMOStats($player){
        if($player instanceof Player){
           $player = $player->getName();
	}
	$player = strtolower($player);
        if(!isset($this->data[$player])){
           $this->data[$player] = $this->base;
        }
        return isset($this->data[$player]) ? $this->data[$player] : $this->base;
    }
    
    public function getPlayerMMOXP($player, $type){
        if($player instanceof Player){
           $player = $player->getName();
	}
	$player = strtolower($player);
        $stats = $this->getPlayerMMOStats($player);
        return $stats[strtolower($type)]["exp"];
    }
    
    public function getPlayerMMOLevel($player, $type){
        if($player instanceof Player){
           $player = $player->getName();
	}
	$player = strtolower($player);
        $stats = $this->getPlayerMMOStats($player);
        return $stats[strtolower($type)]["level"];
    }
    
    public function getPlayerMMONextLevelXP($player, $type){
        if($player instanceof Player){
           $player = $player->getName();
	}
	$player = strtolower($player);
        $next = ($this->getPlayerMMOLevel($player, $type) * 100);
        return $next;
    }
    
    public $levels = [
        50 => true,
        100 => true,
        150 => true
    ];
        
    public function addPlayerMMOXP($player, $type, $xp){
        if($player instanceof Player){
           $player = $player->getName();
	}
	$player = strtolower($player);
        $stats = $this->getPlayerMMOStats($player, $type);
        $stats[strtolower($type)]["exp"] = ($this->getPlayerMMOXP($player, $type) + $xp);
        if($this->getPlayerMMOXP($player, $type) >= $this->getPlayerMMONextLevelXP($player, $type)){
           $stats[strtolower($type)]["exp"] = 0;
           $stats[strtolower($type)]["level"] = ($this->getPlayerMMOLevel($player, $type) + 1);
           if(isset($this->levels[$stats[strtolower($type)]["level"]])){
              $this->getServer()->broadcastMessage("§l§6StrikeMMO §r§6The player ". strtoupper($player) ." has reached the level ". $stats["level"]);
           }
        }
        return $this->data[$player] = $stats;
    }
    
    public function getAll($type){
        $ret = [];
        foreach($this->data as $player => $stats){
           $ret[$player] = $this->getPlayerMMOLevel($player, $type);
        }
        return $ret;
    }
    
    public function getTopList($page, $type){
        $all = $this->getAll($type);
        arsort($all);
        
        $ret = [];
	$n = 1;
	$max = ceil(count($all) / 5);
	$page = min($max, max(1, $page));
	foreach($all as $p => $l){
           $p = strtolower($p);
	   $current = ceil($n / 5);
	   if($current == $page){
	      $ret[$n] = $p;
	   }elseif($current > $page){
              break;
           }
	   ++$n;
        }
		return $ret;
    }
    
    public function sendTopList($page, $type){
        $top = $this->getTopList($page, $type);
        $message = "§l§6-== ". strtoupper($type) ." TOP ==-\n";
        foreach($top as $n => $player){
           $message .= "     §l§f#". $n ." §r§7". strtoupper($player) ." §eEXP(". $this->getPlayerMMOXP($player, $type) ."/". $this->getPlayerMMONextLevelXP($player, $type) .")";
           $message .= " : ". $this->getPlayerMMOLevel($player, $type) ."\n";
        }
        $message = substr($message, 0, -1);
        return $message;
    }
    
    public function getBoosterTime($player, $type){
        $type = strtolower($type);
        if($player instanceof Player){
           $player = $player->getName();
        }
        $player = strtolower($player);
        if(isset($this->booster[$player .":". $type])){
           return $this->booster[$player .":". $type];
        }
        return 0;
    }
    
    public function setBoosterTime($player, $type, $time, $true = false){
        $type = strtolower($type);
        if($player instanceof Player){
           $player = $player->getName();
        }
        $player = strtolower($player);
        if(isset($this->booster[$player .":". $type])){
           $timeB = $this->booster[$player .":". $type];
           if($true){
              if($timeB < 1){
                 return false;
              }
           }
        }
        if(isset($this->booster[$player .":". $type])){
           unset($this->booster[$player .":". $type]);
        }
        return $this->booster[$player .":". $type] = $time;
    }
    
    public function hasBooster($player, $type){
        $type = strtolower($type);
        if($player instanceof Player){
           $player = $player->getName();
        }
        $player = strtolower($player);
        if(isset($this->booster[$player .":". $type])){
           $time = $this->booster[$player .":". $type];
           if($time > 0){
              return true;
           }
        }
    }
    
    public $abillitys = [
        "mining",
        "lumberjack",
        "excavation",
        "repair",
        "harvest",
        "swords",
        "axes",
        "acrobatics",
        "alchemy"
    ];
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        if($sender instanceof Player){
           switch(strtolower($command->getName())){
              case "mcstats":
                  if(isset($args[0])){
                     $player = $args[0];
                     if($this->getServer()->getPlayer($args[0]) !== null){
                        $player = $this->getServer()->getPlayer($args[0])->getName();
                     }
                  }else{
                     $player = $sender->getName();
                  }
                  $player = strtolower($player);
                  if(isset($this->data[$player])){
                     $message = "§l§6MMOSTATS : ". strtoupper($player) ."\n§f\n";
                     $message .= "§l§6-== Works ==-\n§f\n";
                     foreach($this->abillitys as $all){
                        if($all == "swords"){
                           $message .= "§f\n§l§6-== Damage ==-\n§f\n";
                        }
                        $message .= "     §7". ucfirst($all) ." §eEXP(". $this->getPlayerMMOXP($player, $all) ."/". $this->getPlayerMMONextLevelXP($player, $all) .")";
                        if($this->hasBooster($player, $all)){
                           $message .= " > ". $this->getBoosterTime($player, $all);
                        }
                        $message .= " : ". $this->getPlayerMMOLevel($player, $all) ."\n";
                        if($all == "acrobatics"){
                           $message .= "§f\n§l§6-== Others ==-\n§f\n";
                        }
                     }
                     $sender->sendMessage($message);
                     return true;
                  }
                  $sender->sendMessage("§cuse: /mmostats (player)");
                  return false;
              break;
              case "mctop":
                  if(isset($args[0]) and isset($this->data[strtolower($sender->getName())][strtolower($args[0])])){
                     $page = 1;
                     if(isset($args[1]) and is_numeric($args[1]) and $args[1] > 0){
                        $page = $args[1];
                     }
                     $sender->sendMessage($this->sendTopList($page, strtolower($args[0])));
                     return true;
                  }
                  $sender->sendMessage("§cuse: /mmotop (abillity) (page)");
                  return false;
              break;
              case "booster":
                  $economy = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
                  if($economy){
                     if(isset($args[0]) and isset($args[1]) and isset($this->data[strtolower($sender->getName())][strtolower($args[0])]) and is_numeric($args[1]) and $args[1] > 0){
                        $price = ($args[1] * 1000);
                        if($args[1] > 60){
                           $sender->sendMessage("§eMax time is 60 minutes");
                           return false;
                        }
                        if($economy->myMoney($sender) < $price){
                           $sender->sendMessage("§cinsufficient money, price is ". $price);
                           return false;
                        }
                        $item = Item::get(384, 0, 1);
                        $item->setCustomName("§r§l§6BOOSTER OF ". strtoupper($args[0]) ." TIME ". $args[1] ." SEC");
                        if(!$sender->getInventory()->canAddItem($item)){
                           $sender->sendMessage("§eThe booster can not be added, your inventory is full");
                           return false;
                        }
                        $economy->reduceMoney($sender, $price);
                        $sender->getInventory()->addItem($item);
                        $sender->sendMessage("§aThe booster was successfully purchased");
                        return true;
                     }
                  }
                  $sender->sendMessage("§cuse: /booster (abillity) (time)");
                  return false;
              break;
           }
        }else{
           $sender->sendMessage("§cOnly in game!");
           return false;
        }
    }
    
    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        if($player == null){
           return false;
        }
        if(!isset($this->data[strtolower($player->getName())])){
           $this->data[strtolower($player->getName())] = $this->base;
           return true;
        }
    }
    
    public function onDamage(EntityDamageEvent $event){
        if($event->isCancelled()){
           return false;
        }
        $type = null;
        switch($event->getCause()){
           case EntityDamageEvent::CAUSE_FALL:
               $entity = $event->getEntity();
               if($entity instanceof Player){
                  if(($entity->getHealth() - $event->getFinalDamage()) <= 0){
                     return false;
                  }
                  $type = "acrobatics";
               }
           break;
           case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
               $entity = $event->getDamager();
               if($entity instanceof Player){
                  $item = $entity->getInventory()->getItemInHand();
                  if($item->isAxe()){
                     $type = "axes";
                  }elseif($item->isSword()){
                     $type = "swords";
                  }else return false;
               }
           break;
        }
        if($type !== null){
           $this->addPlayerMMOXP($entity, $type, $this->checkEXP($entity, $type, mt_rand(1, 15)));
           $tip = "§7". ucfirst($type) ." §eEXP(". $this->getPlayerMMOXP($entity, $type) ."/". $this->getPlayerMMONextLevelXP($entity, $type) .")";
           $tip .= " : ". $this->getPlayerMMOLevel($entity, $type) ."\n";
           $entity->sendTip($tip);
        }
    }
    
    public function onBreak(BlockBreakEvent $event){
        $player = $event->getPlayer();
        if($event->isCancelled()){
           return false;    
        }
        $type = null;
        $item = $event->getItem();
        $block = $event->getBlock();
        switch($block->getId()){
           case 1:
               if($item->isPickaxe()){
                  $type = "mining";
               }
           break;
           case 2:
           case 3:
           case 12:
           case 13:
               if($item->isShovel()){
                  $type = "excavation";
               }
           break;
           case 17:
           case 18:
               if($item->isAxe()){
                  $type = "lumberjack";
               }
           break;
           case 59;
           case 104:
           case 105:
           case 141:
           case 142:
               $type = "harvest";
           break;
        }
        if($type !== null){
           $drop = null;
           $level = $this->getPlayerMMOLevel($player, $type);
           $rand = mt_rand(0, 10);
           switch($type){
              case "lumberjack":
                  if($block->getId() == 18){
                     if($level >= 30 and $rand == 0){
                        $drop = [Item::get(260, 0, 1)];
                     }elseif($level >= 50 and $rand == 1){
                        $drop = [Item::get(322, 0, 1)];
                     }elseif($item->hasEnchantment(16)){
                        $drop = [Item::get($block->getId(), 0, 1)];
                     }
                  }
              break;
              case "harvest":
                  if($level >= 30 and $rand == 2){
                     foreach($event->getDrops() as $drops){
                        $block->getLevel()->dropItem(Item::get($drops->getId(), $drops->getDamage(), mt_rand(1, ($drops->getCount() * $level))));
                     }
                  }
              break;
           }
           $this->addPlayerMMOXP($player, $type, $this->checkEXP($player, $type, mt_rand(1, 10)));
           $tip = "§7". ucfirst($type) ." §eEXP(". $this->getPlayerMMOXP($player, $type) ."/". $this->getPlayerMMONextLevelXP($player, $type) .")";
           $tip .= " : ". $this->getPlayerMMOLevel($player, $type) ."\n";
           $player->sendTip($tip);
           if($drop !== null){
              $event->setDrops($drop);
           }
        }
    }
    
    private $armors = [298, 299, 300, 301, 314, 315, 316, 317, 306, 307, 308, 309, 310, 311, 312, 313];
    private $tools = [268, 269, 270, 271, 290, 283, 284, 285, 286, 294, 272, 273, 274, 275, 291, 256, 257, 258, 267, 292, 293, 279, 277, 278, 276];
                               
    public function onTap(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        if($event->isCancelled()){
           return false;
        }
        $item = $event->getItem();
        if($item->getId() == 384){
           if($item->hasCustomName()){
              $id = explode(" ", $item->getCustomName());
              if($this->hasBooster($player, $id[2])){
                 $player->sendMessage("§cYou already used a booster of this type");
                 return false;
              }
              $this->setBoosterTime($player, $id[2], ($id[4] * 60));
              $item->setCount($item->getCount() - 1);
              $player->getInventory()->setItemInHand($item);
              $player->sendMessage("§aThe booster was successfully used");
              return true;
           }
        }
        $block = $event->getBlock();
        if($block->getId() == 117){
           if($block->getLevel()->getTile($block) instanceof BrewingStand){
              foreach($block->getLevel()->getTile($block)->getInventory()->getViewers() as $players){
                 if($players !== null){
                    $player->sendMessage("§cHas a player using");
                    $event->setCancelled(true);
                    return false;
                 }
              }
           }else{
              $nbt = new CompoundTag("", [
                     new ListTag("Items", []),
                     new StringTag("id", Tile::BREWING_STAND),
                     new IntTag("x", $block->x),
                     new IntTag("y", $block->y),
                     new IntTag("z", $block->z),
              ]);
              $nbt->Items->setTagType(NBT::TAG_Compound);  
              new BrewingStand($block->getLevel()->getChunk($block->x >> 4, $block->z >> 4), $nbt);
           }
        }
        if($block->getId() == Block::ANVIL){
           $event->setCancelled(true);
           if($item->isTool() or $item->isArmor()){
              $type = "repair";
              $level = $this->getPlayerMMOLevel($player, $type);
              $repair = null;
              for($index = 0; $index < $level; $index++){
                 if($item->getId() == $this->armors[$index] or $item->getId() == $this->tools[$index]){
                    $repair = true;
                 }
              }
              if($repair){
                 if($item->getDamage() == 0){
                    $player->sendTip("§eAlready repaired");
                    return false;
                 }
                 if($player->getXpLevel() < $item->getRepairCost()){
                    $player->sendTip("§cYou do not have xp enough");
                    return false;
                 }
                 $player->takeXpLevel($item->getRepairCost());
                 $rand = (mt_rand(1, ($level + 1)) * 10);
                 if($rand >= $item->getDamage()){
                    $item->setDamage(0);
                 }else{
                    $item->setDamage($item->getDamage() - $rand);
                 }
                 $player->getInventory()->setItemInHand($item);
                 $this->addPlayerMMOXP($player, $type, $this->checkEXP($player, $type, mt_rand(1, 10)));
                 $tip = "§7". ucfirst($type) ." §eEXP(". $this->getPlayerMMOXP($player, $type) ."/". $this->getPlayerMMONextLevelXP($player, $type) .")";
                 $tip .= " : ". $this->getPlayerMMOLevel($player, $type) ."\n";
                 $player->sendTip($tip);
              }
           }
        }
    }
    
    public function onBrew(PlayerBrewingEvent $event){
        $player = $event->getPlayer();
        $inventory = $event->getInventory();
        
        $type = "alchemy";
        $erro = true;
        $level = $this->getPlayerMMOLevel($player, $type);
        switch($event->getPotion()->getDamage()){
           case 5:
           case 7:
           case 9:
               if(!$level >= 2){
                  $erro = true;
               }
           break;
           case 12:
           case 14:
           case 17:
               if(!$level >= 5){
                  $erro = true;
               }
           break;
           case 19:
           case 21:
           case 23:
               if(!$level >= 7){
                  $erro = true;
               }
           break;
           case 25:
           case 28:
           case 31:
           case 34:
           case 36:
               if(!$level >= 9){
                  $erro = true;
               }
           break;
           case 6:
           case 8:
           case 10:
               if(!$level >= 11){
                  $erro = true;
               }
           break;
           case 13:
           case 15:
           case 17:
               if(!$level >= 13){
                  $erro = true;
               }
           break;
           case 13:
           case 15:
           case 18:
               if(!$level >= 15){
                  $erro = true;
               }
           break;
           case 20:
           case 22:
           case 24:
               if(!$level >= 17){
                  $erro = true;
               }
           break;
           case 26:
           case 29:
           case 32:
           case 35:
           case 37:
               if(!$level >= 19){
                  $erro = true;
               }
           break;
           case 8:
           case 11:
           case 13:
               if(!$level >= 21){
                  $erro = true;
               }
           break;
           case 16:
           case 20:
           case 22:
               if(!$level >= 23){
                  $erro = true;
               }
           break;
           case 24:
           case 27:
           case 30:
               if(!$level >= 25){
                  $erro = true;
               }
           break;
           case 24:
           case 27:
           case 30:
               if(!$level >= 27){
                  $erro = true;
               }
           break;
           case 33:
               if(!$level >= 29){
                  $erro = true;
               }
           break;
           default:
            $erro = false;
           break;
        }  
        if($erro){ 
           $event->setCancelled(true);
           $player->removeWindow($inventory);
           $player->sendMessage("§cYou dont have level to make this potion");
        }else{
           $this->addPlayerMMOXP($player, $type, $this->checkEXP($player, $type, mt_rand(1, 20)));
           $tip = "§7". ucfirst($type) ." §eEXP(". $this->getPlayerMMOXP($player, $type) ."/". $this->getPlayerMMONextLevelXP($player, $type) .")";
           $tip .= " : ". $this->getPlayerMMOLevel($player, $type) ."\n";
           $player->sendTip($tip);
        }
    }
    
    public function checkEXP($player, $type, $exp){
        $type = strtolower($type);
        if($player instanceof Player){
           $player = $player->getName();
        }
        $player = strtolower($player);
        if($this->hasBooster($player, $type)){
           return ($exp * 3);
        }
        return $exp;
    }
}
                               
class Helper extends PluginTask{
    
    private $plugin;
    
    public function __construct(Base $plugin){
	$this->plugin = $plugin;
        parent::__construct($plugin);
    }
    
    public function getPlugin(){
        return $this->plugin;
    }
    
    public function onRun($timer){
        if(!count($this->getPlugin()->booster)){
           return false;
        }
        foreach($this->getPlugin()->booster as $player => $time){
           $args = explode(":", $player);
           $this->getPlugin()->setBoosterTime($args[0], $args[1], ($time - 1), true); 
        }
    }
}
