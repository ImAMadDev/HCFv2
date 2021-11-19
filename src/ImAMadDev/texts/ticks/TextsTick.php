<?php

namespace ImAMadDev\texts\ticks;

use pocketmine\scheduler\Task;

use ImAMadDev\manager\TextsManager;

class TextsTick extends Task{
	
	public TextsManager $manager;
	
	public function __construct(TextsManager $manager){
		$this->manager = $manager;
	}
	
	public function onRun() : void {
		foreach($this->manager->getTexts() as $text) {
			$text->onTick();
		}
	}
	
}
