<?php

namespace ImAMadDev\kit;

use ImAMadDev\claim\utils\ClaimType;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\{EOTWManager, ClaimManager, KitManager};
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\kit\classes\IEnergyClass;
use ImAMadDev\ability\Ability;
use ImAMadDev\kit\ClassCreatorSession;

use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\Server;
use pocketmine\entity\{effect\EffectInstance, effect\VanillaEffects};
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};

class KitListener implements Listener {
	
	public function onEntityDamageEventRogue(EntityDamageEvent $event) : void {
		$player = $event->getEntity();
		if($event instanceof EntityDamageByEntityEvent && !$event->isCancelled()){
            $attacker = $event->getDamager();
			if($player instanceof HCFPlayer && $attacker instanceof HCFPlayer){
				if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($attacker->getPosition()), "Spawn") !== false or stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false or $player->isInvincible() or $attacker->isInvincible()) {
					return;
				}
				if($attacker->isRogue() && $attacker->getInventory()->getItemInHand()->getId() === ItemIds::GOLD_SWORD){
					if($attacker->getCooldown()->has('backstab')) {
                        $attacker->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Rogue BackStab" . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $player->getCooldown()->get('backstab')));
						return;
					}
                    if ($attacker->getDirectionVector()->dot($player->getDirectionVector()) > 0) {
                        $player->setHealth($player->getHealth() / 2);
                        $attacker->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 5 * 20, 3));
                        $attacker->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 5 * 20, 3));
                        $player->sendTitle(TextFormat::RED . TextFormat::BOLD . "Backstabbed!");
                        $attacker->getCooldown()->add('backstab', 15);
                        $attacker->getInventory()->setItemInHand(VanillaItems::AIR());
                        $attacker->sendMessage(TextFormat::GRAY . "You hit " . TextFormat::RED . $player->getName() . TextFormat::GRAY . " who is now at " . TextFormat::RED . round($player->getHealth()) . " health.");
                    } else {
                        $attacker->sendMessage(TextFormat::RED . "BackStab failed!");
                    }
				}
			}
		}
	}
	
	public function onEntityDamageEventArcher(EntityDamageEvent $event) : void {
		$player = $event->getEntity();
		if($event->isCancelled()) {
			return;
		}
		if($event instanceof EntityDamageByEntityEvent){
			$attacker = $event->getDamager();
			if($player instanceof HCFPlayer and $attacker instanceof HCFPlayer){
				if($event->getCause() === EntityDamageEvent::CAUSE_PROJECTILE) {
					if($attacker->isArcher() && !$player->isArcher()) {
						if($attacker->getInventory()->getItemInHand()->getId() === ItemIds::BOW) {
							if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($attacker->getPosition()), "Spawn") !== false or stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false or $player->isInvincible() or $attacker->isInvincible()) {
								return;
							}
							$player->getArcherMark()->setDistance($attacker->getPosition()->distance($player->getPosition()));
							if($player->getEffects()->has(VanillaEffects::INVISIBILITY())) {
								$player->getEffects()->remove(VanillaEffects::INVISIBILITY());
							}
							if($player->isInvisible()) {
								$player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::INVISIBLE, false);
							}
							foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
								$onlinePlayer->showPlayer($player);
							}
                            $attacker->sendMessage(TextFormat::YELLOW . "You have hit " . TextFormat::AQUA . $player->getName() . TextFormat::YELLOW . " and have archer tagged");
                            $player->sendMessage(TextFormat::YELLOW . "You have been archer tagged by " . TextFormat::AQUA . $attacker->getName());
                            $attacker->sendMessage(TextFormat::YELLOW .  "You marked " . TextFormat::GOLD . $player->getName() . TextFormat::GRAY . " for 10 seconds " . TextFormat::GRAY . "[Damage: +" . TextFormat::RED . $player->getArcherMark()->getDamage() . TextFormat::GRAY . "]");
						}
					}
				}
				if($player->getArcherMark()->getDamage() > 0){
					$baseDamage = $event->getBaseDamage();
					$event->setBaseDamage($baseDamage + $player->getArcherMark()->getDamage());
				}
			}
		}
	}
    
    public function handleCustomClassUse(PlayerItemUseEvent $event) : void
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if ($player instanceof HCFPlayer) {
            $class = $player->getClass();
            if ($class instanceof IEnergyClass) {
            	if($class->isClickItem($item)) {
            		$class->itemConsumed($player, $item);
            	}
            }
        }
    }

    /** @noinspection PhpParamsInspection */
    public function onChat(PlayerChatEvent $event) : void
    {
        $player = $event->getPlayer();
        if (KitManager::getInstance()->hasSession($player)){
            $message = $event->getMessage();
            $args = explode(" ", $message);
            if($args[0] == "help"){
                $player->sendMessage(TextFormat::DARK_AQUA . "Commands: " . TextFormat::EOL .
                    "- permission (string: permiso del kit)" . TextFormat::EOL .
                    "- inventory [Todo tu inventario sera escogido para este kit]" . TextFormat::EOL .
                    "- description (string: la descripcion que aparecera en el kit)" . TextFormat::EOL .
                    "- icon [el item en tu mano sera el icono del kit]" . TextFormat::EOL .
                    "- countdown (string: tiempo de refresco del kit) [ejemplo: 1d,2h,30m se lee como 1 dia 2 hora y 30 minutos]" . TextFormat::EOL .
                    "- slot (int: slot) [esto es para los win10 en que slot del cofre aparecera el icono del kit]" . TextFormat::EOL .
                    "- customname (string: name) [Aqui podras colocar un nombre customizado para el icono del kit]" . TextFormat::EOL .
                    "- cancel [cancelar la sesion]" . TextFormat::EOL .
                    "- save [guardar el kit]"
                );
                $event->cancel();
            }
            if ($args[0] == "permission"){
                if (isset($args[1])){
                    KitManager::getInstance()->getSession($player)->setPermission($args[1]);
                    $player->sendMessage(TextFormat::GREEN . "You have put the kit permission to: $args[1]");
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the permission");
                }
                $event->cancel();
            }
            if ($args[0] == "inventory"){
                KitManager::getInstance()->getSession($player)->copyInventory();
                $player->sendMessage(TextFormat::GREEN . "You have save the kit contents");
                $event->cancel();
            }
            if ($args[0] == "description"){
                if (isset($args[1])){
                    $desc = $args;
                    array_shift($desc);
                    $description = implode(" ", $desc);
                    KitManager::getInstance()->getSession($player)->setDescription($description);
                    $player->sendMessage(TextFormat::GREEN . "You have put the kit description to: $description");
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the description");
                }
                $event->cancel();
            }
            if ($args[0] == "icon"){
                KitManager::getInstance()->getSession($player)->setIcon();
                $player->sendMessage(TextFormat::GREEN . "You have save the kit icon");
                $event->cancel();
            }
            if ($args[0] == "countdown"){
                if (isset($args[1])){
                    if(!preg_match("/(^[1-9][0-9]{0,2}[mhd])(,[1-9][0-9]{0,2}[mhd]){0,2}$/", $args[1])) {
                        $player->sendMessage(TextFormat::RED . "Unknown countdown type {$args[1]}");
                        return;
                    }
                    $time = HCFUtils::strToSeconds($args[1]);
                    $timeFormated = (time() + $time);
                    KitManager::getInstance()->getSession($player)->setCountdown((int)$time);
                    $player->sendMessage(TextFormat::GREEN . "You have put the kit countdown to: " . HCFUtils::getTimeString($timeFormated));
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the permission");
                }
                $event->cancel();
            }
            if ($args[0] == "slot"){
                if (isset($args[1])){
                    KitManager::getInstance()->getSession($player)->setSlot((int)$args[1]);
                    $player->sendMessage(TextFormat::GREEN . "You have put the kit slot to: " . (int)$args[1]);
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the slot");
                }
                $event->cancel();
            }
            if ($args[0] == "customname"){
                if (isset($args[1])){
                    $name = $args;
                    array_shift($name);
                    $name = implode(" ", $name);
                    KitManager::getInstance()->getSession($player)->setCustomName($name);
                    $player->sendMessage(TextFormat::GREEN . "You have put the kit icon custom name to: " . $name);
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the icon custom name");
                }
                $event->cancel();
            }
            if ($args[0] == "cancel"){
                KitManager::getInstance()->closeSession($player);
                $player->sendMessage(TextFormat::GREEN . "You have closed your kit creator session");
                $event->cancel();
            }
            if($args[0] == "save"){
                KitManager::getInstance()->getSession($player)->save();
                KitManager::getInstance()->closeSession($player);
                $player->sendMessage(TextFormat::GREEN . "You have save the kit");
                $event->cancel();
            }
        }
	}
	
	
	/** @noinspection PhpParamsInspection */
    public function handleClass(PlayerChatEvent $event) : void
    {
        $player = $event->getPlayer();
        if (KitManager::getInstance()->hasClassSession($player)){
            $message = $event->getMessage();
            $args = explode(" ", $message);
            if($args[0] == "help"){
                $player->sendMessage(TextFormat::DARK_AQUA . "Commands: " . TextFormat::EOL .
                    "- energy (int: si la clase tendra energia como el bard, configurar esto)" . TextFormat::EOL .
                    "- armor [Toda tu armadura sera escogida para esta clase]" . TextFormat::EOL .
                    "- addeffect (string: agrega un efecto formato: nombreDelEfecto:nivel:visibleOno ejemplo: resistance:1:true)" . TextFormat::EOL .
                    "- Nota: para el efecto el nivel debe ser 1 menor al que quieres ejemplo si quieres 2 pon 1" . TextFormat::EOL .
                    "- Nota2: true es si quieres que sea visible y false si no" . TextFormat::EOL .
                    "- name (string: si quieres cambiar el nombre de la clase)" . TextFormat::EOL .
                    "- additem [abre una ventana para crear un item, primero debes poner la energia]" . TextFormat::EOL .
                    "- cancel [cancelar la creación]" . TextFormat::EOL .
                    "- save [guardar la clase]"
                );
                $event->cancel();
            }
            if ($args[0] == "name"){
                if (isset($args[1])){
                    KitManager::getInstance()->getClassSession($player)->setName($args[1]);
                    $player->sendMessage(TextFormat::GREEN . "You have put the name of the class to: $args[1]");
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the name");
                }
                $event->cancel();
            }
            if ($args[0] == "energy"){
                if (isset($args[1]) and is_numeric($args[1])){
                    KitManager::getInstance()->getClassSession($player)->setEnergy((int)$args[1]);
                    $player->sendMessage(TextFormat::GREEN . "You have put the energy of the class to: $args[1]");
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the name");
                }
                $event->cancel();
            }
            if ($args[0] == "additem"){
            	KitManager::getInstance()->getClassSession($player)->createItem();
                $event->cancel();
            }
            if ($args[0] == "armor"){
                KitManager::getInstance()->getClassSession($player)->copyInventory();
                $player->sendMessage(TextFormat::GREEN . "You have save the class armor");
                $event->cancel();
            }
            if ($args[0] == "addeffect"){
                if (isset($args[1])){
                	$effect = ClassCreatorSession::stringToEffect($args[1]);
                	if($effect == null) { 
                		$player->sendMessage(TextFormat::RED . "Invalid Effect!");
                		return;
					} 
					$name = is_string($effect->getType()->getName()) ? $effect->getType()->getName() : $effect->getType()->getName()->getText();
                    KitManager::getInstance()->getClassSession($player)->addEffect($name, $effect->getAmplifier(), $effect->isVisible());
                    $player->sendMessage(TextFormat::GREEN . "You have added new effect: " . $args[1]);
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: invalid arguments");
                }
                $event->cancel();
            }
            if ($args[0] == "cancel"){
                KitManager::getInstance()->closeClassSession($player);
                $player->sendMessage(TextFormat::GREEN . "You have closed your class creator session");
                $event->cancel();
            }
            if($args[0] == "save"){
                KitManager::getInstance()->getClassSession($player)->save();
                KitManager::getInstance()->closeClassSession($player);
                $player->sendMessage(TextFormat::GREEN . "You have save the class");
                $event->cancel();
            }
        }
	}

}