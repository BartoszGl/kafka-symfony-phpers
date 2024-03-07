<?php

namespace App\MessageHandler;

use App\Message\KafkaTestOneMessageCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class KafkaTestOneMessageCommandHandler
{
    public function __invoke(KafkaTestOneMessageCommand $oneMessageCommand)
    {
        dump($oneMessageCommand);
    }
}
