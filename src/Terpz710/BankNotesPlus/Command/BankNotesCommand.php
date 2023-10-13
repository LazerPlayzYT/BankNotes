<?php

namespace Terpz710\BankNotesPlus\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use Terpz710\BankNotesPlus\Main;

class BankNotesCommand extends Command {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("banknotesplus", "Convert in-game money into bank notes", "/banknotesplus {amount}");
        $this->setPermission("banknotesplus.cmd");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$this->testPermission($sender)) {
            return true;
        }

        if ($sender instanceof Player) {
            if (count($args) === 1 && is_numeric($args[0]) && $args[0] > 0) {
                $amount = (float)$args[0];

                if ($this->plugin->hasEnoughMoney($sender, $amount)) {
                    
                    $this->plugin->convertToBankNote($sender, $amount);
                    $sender->sendMessage(TF::GREEN . "You have converted $" . $amount . " into a bank note.");
                } else {
                    $sender->sendMessage(TF::RED . "You don't have enough money to convert into a bank note.");
                }
            } else {
                $sender->sendMessage(TF::RED . "Usage: /banknotesplus {amount}");
            }
        } else {
            $sender->sendMessage(TF::RED . "This command can only be used by players.");
        }
        return true;
    }
}
