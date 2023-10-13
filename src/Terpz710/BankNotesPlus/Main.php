<?php

namespace Terpz710\BankNotesPlus;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use Terpz710\BankNotesPlus\Command\BankNotesCommand;
use Terpz710\libEco\libEco;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("banknotesplus", new BankNotesCommand($this));
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getNamedTag()->getTag("Amount") !== null) {
            $amount = $item->getNamedTag()->getFloat("Amount");

            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);

            if (libEco::isInstall()) {
                libEco::addMoney($player, $amount);
                $player->sendMessage(TF::GREEN . "You have claimed $" . $amount . " from the bank note.");
            } else {
                $player->sendMessage(TF::RED . "libEco is not available. Unable to add money to your balance.");
            }
        }
    }

    public function hasEnoughMoney(Player $player, float $amount): bool {
        $balance = 0;

        if (libEco::isInstall()) {
            libEco::myMoney($player, function(float $money) use (&$balance) {
                $balance = $money;
            });
        }

        return $balance >= $amount;
    }

    public function convertToBankNote(Player $player, float $amount): void {
        $bankNote = VanillaItems::PAPER();
        $bankNote->setCustomName(TF::GOLD . "$" . $amount . " Bank Note");
        $bankNote->setLore([
            "Value: $" . $amount,
            "Right-click to redeem"
        ]);
        $bankNote->getNamedTag()->setFloat("Amount", $amount);

        $player->getInventory()->addItem($bankNote);
    }
}
