<?php

namespace ImAMadDev\events\ticks;

use ImAMadDev\events\Events;

use pocketmine\scheduler\Task;

class EventCooldownTick extends Task {
	
	private Events $events;
	
	public function __construct(Events $event) {
		$this->events = $event;
	}
	
	public function onRun(): void{
		$this->events->onTick();
	}
	
}