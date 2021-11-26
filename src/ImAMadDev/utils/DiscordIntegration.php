<?php 

namespace ImAMadDev\utils;

use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Embed;

final class DiscordIntegration {
	
	public const KOTH_WEBHOOK = "https://discord.com/api/webhooks/913379535750299658/OSxyQYoQzdFswS3YakR87BiUkGcv-20Jpwy9z9j7fqEOpy3XrX6ocmYfID6eO49Af7JU";
	
	public const ALERT_WEBHOOK = "https://discord.com/api/webhooks/870715467319369749/C6VHD3WGT_G6-nULLbUpColU9RvRfLH-4FfeUJbM31f2Zk-Sw3j6j1HIawlPdm08b29Q";
	
	public static function sendToDiscord(string $title, string $message, string $chat, string $username, array $fields = [], string $tag = '869246176560558080', $avatar = "https://i.ibb.co/C1wxnMt/Staliabot.jpg"){
		$webHook = new Webhook($chat);
		$msg = new Message();
		$msg->setUsername($username);
		$msg->setContent($message);
		if($avatar !== null){
			$msg->setAvatarURL($avatar);
		}
		if(!empty($fields)) {
			$embed = new Embed();
			$embed->setColor(0xffa965);
			$embed->setTitle($title);
			if(!empty($fields)) {
				foreach($fields as $field) {
					$embed->addField($field, "_ _");
				}
			} else {
				$embed->setDescription($message);
			}
			$msg->addEmbed($embed);
		}
		$webHook->send($msg);
	}
}
