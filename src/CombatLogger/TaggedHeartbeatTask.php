<?php

namespace CombatLogger;

use pocketmine\scheduler\Task;

class TaggedHeartbeatTask extends Task {

	public function __construct(
        public CombatManager $manager) {}

	public function onRun() : void {
		foreach($this->manager->taggedPlayers as $name => $time) {
			$time--;
			if($time <= 0) {
				$this->manager->setTagged($name, $this->manager->tagged[$name], false);
				return;
			}
			$this->manager->taggedPlayers[$name]--;
		}
	}

}