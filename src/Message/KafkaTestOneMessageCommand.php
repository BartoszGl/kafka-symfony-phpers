<?php

namespace App\Message;

class KafkaTestOneMessageCommand
{
    public function __construct(public readonly string $name, public readonly string $surname)
    {
    }
}
