<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Console\Command;

class SendMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-message {from} {to} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Üzenet küldése két felhasználó között e-mail cím alapján';

    public function __construct(private readonly MessageRepositoryInterface $messageRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fromEmail = $this->argument('from');
        $toEmail = $this->argument('to');
        $body = $this->argument('message');

        $sender = User::where('email', $fromEmail)->first();
        if (! $sender) {
            $this->error("A küldő nem található: {$fromEmail}");

            return Command::FAILURE;
        }

        $receiver = User::where('email', $toEmail)->first();
        if (! $receiver) {
            $this->error("A címzett nem található: {$toEmail}");

            return Command::FAILURE;
        }

        try {
            $this->messageRepository->send($sender, $receiver, $body);
        } catch (\LogicException $e) {
            $this->error($e->getMessage());

            return Command::FAILURE;
        }

        $this->info("Üzenet sikeresen elküldve tőle: {$fromEmail}, neki: {$toEmail}");

        return Command::SUCCESS;
    }
}
