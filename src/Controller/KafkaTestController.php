<?php

namespace App\Controller;

use App\Message\KafkaTestOneMessageCommand;
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
        $j = 1;
        while ($j <= 10) {
            $i = 1;

            while ($i <= 10) {
                $message = new KafkaTestOneMessageCommand(
                    'name' . $i, 'surname' . $i
                );

                // Here message will be dispatched to Kafka, it is basically our producer
                $this->messageBus->dispatch((new Envelope($message))
                    ->with(new TransportConfiguration([
                        // Decide to which topic given message goes
                        'topic' => 'yet_another_topic',
                        'metadata' => [
                            // Keys with the same id always goes to the same partition
                            // when key is null we use sticky partitioner
                            'key' => 'foo.bar_' . $i,
                        ]
                    ])));

                $i++;
            }
            sleep(1);

            $j++;
        }

        return $this->json([
            'message' => 'Messages sent',
        ]);
    }
}
