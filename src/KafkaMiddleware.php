<?php

namespace App;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class KafkaMiddleware implements MiddlewareInterface
{

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        return $stack->next()->handle($envelope, $stack);
    }
}
