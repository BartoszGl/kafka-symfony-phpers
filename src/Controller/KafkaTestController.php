<?php

namespace App\Controller;

use App\Message\KafkaTestCommand;
use Enqueue\MessengerAdapter\EnvelopeItem\TransportConfiguration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class KafkaTestController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    #[Route('/kafka/test', name: 'app_kafka_test')]
    public function index(): JsonResponse
    {
        $j = 0;
        while ($j < 10) {
            $i = 0;

            while ($i < 10) {
                $message = new KafkaTestCommand('name' . $j . $i);

                $this->messageBus->dispatch((new Envelope($message))->with(new TransportConfiguration([
                    'topic' => 'yet_another_topic',
                    'metadata' => [
                        // Keys with the same id always goes to the same partition
                        // when key is null we should use sticky partitioner
                        'key' => 'foo.bar_' . $i,
                        'timestamp' => (new \DateTimeImmutable())->getTimestamp(),
                        'messageId' => uniqid('kafka_', true),
                    ]
                ])));
                $i++;
            }
            sleep(1);

            $j++;
        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/KafkaTestController.php',
        ]);
    }
}
