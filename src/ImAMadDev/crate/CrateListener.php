<?php

namespace ImAMadDev\crate;

use ImAMadDev\manager\CrateManager;

use pocketmine\event\Listener;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\{PlayerInteractEvent, PlayerChatEvent};

class CrateListener implements Listener {
	
	public function __construct(){}
	
	public function onPlayerInteractEvent(PlayerInteractEvent $event) : void {
		$block = $event->getBlock();
		$player = $event->getPlayer();
		$item = $player->getInventory()->getItemInHand();
		$crate = CrateManager::getInstance()->getCrateByBlock($block);
		if($crate !== null) {
			if($item->getNamedTag()->getTag(Crate::KEY_TAG) instanceof CompoundTag && $crate->isCrateKey($item)) {
				$crate->open($player, $block);
			} else {
				$crate->getContents($player);
			}
		}
	}

    public function onChat(PlayerChatEvent $event) : void
    {
        $player = $event->getPlayer();
        if (CrateManager::getInstance()->hasSession($player)){
            $message = $event->getMessage();
            $args = explode(" ", $message);
            if($args[0] == "help"){
                $player->sendMessage(TextFormat::DARK_AQUA . "Commands: " . PHP_EOL .
                    "- inventory [Se abrira un cofre y cuando lo cierres ese sera el iventario de la crate]" . PHP_EOL .
                    "- key [El item en tu mano sera la key]" . PHP_EOL .
                    "- down [El bloque que tengas en la mano sera escogido para validar la crate, debes colocarlo 4 bloques debajo de la crate]" . PHP_EOL .
                    "- customname (string: nombre de la crate) [Aqui deberas colocar el nombre de la crate con formato de color, sera el mismo para la key]" . PHP_EOL .
                    "- cancel [cancelar la sesion]" . PHP_EOL .
                    "- save [guardar la crate]"
                );
                $event->cancel();
            }
            if ($args[0] == "inventory"){
                $event->cancel();
                CrateManager::getInstance()->getSession($player)->sendChest();
                $player->sendMessage(TextFormat::GREEN . "You have save the kit contents");
                $event->cancel();
            }
            if ($args[0] == "key"){
                CrateManager::getInstance()->getSession($player)->setKey();
                $player->sendMessage(TextFormat::GREEN . "You have save the crate key");
                $event->cancel();
            }
            if ($args[0] == "down"){
                CrateManager::getInstance()->getSession($player)->setDown();
                $player->sendMessage(TextFormat::GREEN . "You have save the crate DOWN BLOCK");
                $event->cancel();
            }
            if ($args[0] == "customname"){
                if (isset($args[1])){
                    $name = $args;
                    array_shift($name);
                    $name = implode(" ", $name);
                    CrateManager::getInstance()->getSession($player)->setCustomName($name);
                    $player->sendMessage(TextFormat::GREEN . "You have put the key and crate custom name to: " . $name);
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the custom name");
                }
                $event->cancel();
            }
            if ($args[0] == "cancel"){
                CrateManager::getInstance()->closeSession($player);
                $player->sendMessage(TextFormat::GREEN . "You have close your session");
                $event->cancel();
            }
            if($args[0] == "save"){
                CrateManager::getInstance()->getSession($player)->save();
                CrateManager::getInstance()->closeSession($player);
                $player->sendMessage(TextFormat::GREEN . "You have save the crate");
                $event->cancel();
            }
        }
    }
	
}