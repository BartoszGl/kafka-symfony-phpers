<?php

namespace App\Message;

final class KafkaTestCommand
{
     public function __construct(public readonly string $name)
     {
     }
}
