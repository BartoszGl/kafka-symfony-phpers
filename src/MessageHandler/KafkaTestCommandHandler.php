<?php

namespace App\MessageHandler;

use App\Message\KafkaTestCommand;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;

#[AsMessageHandler]
final class KafkaTestCommandHandler implements BatchHandlerInterface
{

    use BatchHandlerTrait;


    #[NoReturn] public function __invoke(KafkaTestCommand $message, Acknowledger $ack = null): mixed
    {
        return $this->handle($message, $ack);
    }

    private function process(array $jobs): void
    {
        foreach ($jobs as [$message, $ack]) {
            try {
                sleep(1);
                dump('ok');
                $result = 'ok';
                $ack->ack($result);
            } catch (\Throwable $e) {
                $ack->nack($e);
            }
        }
    }

    // Optionally, you can either redefine the `shouldFlush()` method
    // of the trait to define your own batch size...
    private function shouldFlush(): bool
    {
        return 10 <= \count($this->jobs);
    }
}
