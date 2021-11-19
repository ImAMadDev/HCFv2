<?php

namespace ImAMadDev\command\defaults;

use formapi\SimpleForm;
use ImAMadDev\command\Command;
use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use pocketmine\command\CommandSender;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\utils\Internet;
use pocketmine\utils\TextFormat;

class CapeCommand extends Command
{

    public function __construct()
    {
        parent::__construct("cape", "change your cape", "/cape (Minecraft Premium User)", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (isset($args[0])){
            $data = $this->getCape($args[0]);
            if(isset($data['success']) and $data['success'] == false){
                $sender->sendMessage(TextFormat::RED . "Error user cant be found.");
            } else {
                $capes = [];
                if ((!isset($data['minecraft']) or $data['minecraft']['imageUrl'] == null) and
                    (!isset($data['optifine']) or $data['optifine']['imageUrl'] == null) and
                    (!isset($data['minecraftcapes']) or $data['minecraftcapes']['imageUrl'] == null) and
                    (!isset($data['labymob']) or $data['labymob']['imageUrl'] == null) and
                    (!isset($data['5zig']) or $data['5zig']['imageUrl'] == null) and
                    (!isset($data['tlauncher']) or $data['tlauncher']['imageUrl'] == null)) {
                    $sender->sendMessage(TextFormat::RED . "This user doesnt have minecraft cape");
                    return;
                } else {
                    foreach (["minecraft", "optifine", "minecraftcapes", "labymob", "5zig", "tlauncher"] as $cape) {
                        if (!isset($data[$cape])){
                            continue;
                        }
                        if ($data[$cape]['imageUrl'] == null){
                            continue;
                        }
                        $ui = $data[$cape]['frontImageUrl'] ?? '';
                        $capes[$cape] = ['url' => Internet::getURL($data[$cape]['imageUrl'])->getBody(), 'ui' => $ui];
                    }
                }
                if ($sender instanceof HCFPlayer) {
                    $this->sendForm($sender, $data['minecraft']['playerName'], $capes);
                } else {
                    $sender->sendMessage(TextFormat::RED . "You must be a player to do this.");
                }
            }
        }
    }

    private function createPNG(HCFPlayer $player, string $data, string $user) : void
    {
        file_put_contents(HCF::getInstance()->getDataFolder(). $user . ".png", $data);
        $file = HCF::getInstance()->getDataFolder() . $user . ".png";
        $img = @imagecreatefrompng($file);
        if(!$img){
            if(is_file($file)){
                @unlink($file);
            }
            $player->sendMessage(TextFormat::RED . "An unknown error occurred.");
            return;
        }
        $rgba = "";
        for($y = 0; $y < @imagesy($img); $y++){
            for($x = 0; $x < @imagesx($img); $x++){
                $argb = @imagecolorat($img, $x, $y);
                $rgba .= chr(($argb >> 16) & 0xff) . chr(($argb >> 8) & 0xff) . chr($argb & 0xff) . chr(((~($argb >> 24)) << 1) & 0xff);
            }
        }
        $len = strlen($rgba);
        if($rgba !== "" and $len !== 8192){
            $player->sendMessage(TextFormat::RED . "Set Cape failed! Invalid cape detected [bytes=" . $len . "] [supported=8192]");
            return;
        }
        $capeSkin = new Skin($player->getSkin()->getSkinId(), $player->getSkin()->getSkinData(), $rgba, $player->getSkin()->getGeometryName(), $player->getSkin()->getGeometryData());
        $player->setSkin($capeSkin);
        $player->sendSkin();
        @imagedestroy($img);
        @unlink($file);
        $player->sendMessage(TextFormat::GREEN . "You have changed your cape to: $user cape");
    }

    private function sendForm(HCFPlayer $player, string $user, array $datos) : void
    {
        $form = new SimpleForm(function (Player $player, mixed $data = null) use($user){
            if ($data == null) return;
            /** @noinspection PhpParamsInspection */
            $this->createPNG($player, $data, $user);
        });
        $form->setTitle("Capes");
        foreach ($datos as $key) {
            if ($key['ui'] == '') {
                $form->addButton($user, -1, '', $key['url']);
            } else {
                $form->addButton($user, 1, $key['ui'] . ".png", $key['url']);
            }
        }
        $player->sendForm($form);
    }

    private function getCape(string $name) : array
    {
        $url = 'https://api.capes.dev/load/' . $name;
        return (array) json_decode(Internet::getURL($url)->getBody(), true);
    }
}