<?php

namespace theohdg2\AntiLoseXP;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;

class AntiLoseXp extends PluginBase implements Listener
{
    private array $xp;
    public $respawn = [];
    public $respawnP = [];

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->getScheduler()->scheduleRepeatingTask(new class($this) extends Task{

            private AntiLoseXp $a;

            public function __construct(AntiLoseXp $antiLoseXp)
            {
                $this->a = $antiLoseXp;
            }

            public function onRun(): void
            {
                foreach ($this->a->respawn as $playerName => $array){
                    $p = $this->a->respawnP[$playerName];
                    if($p instanceof Player && $p->isConnected()){
                        $p->getXpManager()->setXpAndProgress($array[0],$array[1]);
                        unset($this->a->respawn[$playerName]);
                        unset($this->a->respawnP[$playerName]);
                    }
                }
            }
        },20);
    }

    public function onDeath(PlayerDeathEvent $event){
        $this->xp[$event->getPlayer()->getName()] = $event->getPlayer()->getXpManager()->getXpLevel().":".$event->getPlayer()->getXpManager()->getXpProgress();
        $event->setXpDropAmount(0);
    }
   public function onRespawn(PlayerRespawnEvent $event){
        if(isset($this->xp[$event->getPlayer()->getName()])){
            $this->respawn[$event->getPlayer()->getName()] = explode(":",$this->xp[$event->getPlayer()->getName()]);
            $this->respawnP[$event->getPlayer()->getName()] = $event->getPlayer();
        }
    }
}