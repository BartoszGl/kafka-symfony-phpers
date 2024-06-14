<?php

namespace App;

use App\Message\KafkaTestOneMessageCommand;
use Enqueue\MessengerAdapter\EnvelopeItem\TransportConfiguration;
use Doctrine\Common\Annotations\AnnotationReader;
use FlixTech\AvroSerializer\Integrations\Symfony\Serializer\AvroSerDeEncoder;
use FlixTech\AvroSerializer\Objects\DefaultRecordSerializerFactory;
use FlixTech\AvroSerializer\Objects\RecordSerializer;
use FlixTech\AvroSerializer\Objects\Schema\Generation\AnnotationReader as FlixAnnotationReader;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaGenerator;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AvroMessageSerializer implements SerializerInterface
{
    private Serializer $symfonySerializer;
    private RecordSerializer $recordSerializer;
    private AvroSerDeEncoder $encoder;
    private SchemaGenerator $generator;

    public function __construct()
    {
        $this->recordSerializer = DefaultRecordSerializerFactory::get(
            'http://kafka-schema-registry:8081'
        );

        $normalizer = new ObjectNormalizer();
        $this->encoder = new AvroSerDeEncoder($this->recordSerializer);

        $this->generator = new SchemaGenerator(
            new FlixAnnotationReader(
                new AnnotationReader()
            )
        );

        $this->symfonySerializer = new Serializer([$normalizer], [$this->encoder]);
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $envelope = $this->createExampleCommandEnvelope($encodedEnvelope['body']);
        $stamps = $this->extractStamps($encodedEnvelope['headers']['stamps']);

        return $envelope->with(...$stamps);
    }

    public function encode(Envelope $envelope): array
    {
        $schema = $this->generator->generate(KafkaTestOneMessageCommand::class);
        $avroSchema = $schema->parse();

        $topicStamp = $envelope->last(TransportConfiguration::class);

        $serialized = $this->symfonySerializer->serialize(
            $envelope->getMessage(),
            AvroSerDeEncoder::FORMAT_AVRO,
            [
                AvroSerDeEncoder::CONTEXT_ENCODE_SUBJECT => $topicStamp->getTopic(),
                AvroSerDeEncoder::CONTEXT_ENCODE_WRITERS_SCHEMA => $avroSchema
            ]
        );

        $allStamps = $this->collectAllSendableStamps($envelope);

        return [
            'body' => $serialized,
            'headers' => ['stamps' => base64_encode(serialize($allStamps))]
        ];
    }

    private function extractStamps(?string $stampsHeader): array
    {
        if (!empty($stampsHeader)) {
            return unserialize(base64_decode($stampsHeader));
        }

        return [];
    }

    private function createExampleCommandEnvelope(?string $data): Envelope
    {
        $message = $this->symfonySerializer->deserialize(
            $data,
            KafkaTestOneMessageCommand::class,
            AvroSerDeEncoder::FORMAT_AVRO
        );

        return (new Envelope($message));
    }

    private function collectAllSendableStamps(Envelope $envelope): array
    {
        $envelope = $envelope->withoutStampsOfType(NonSendableStampInterface::class);

        $allStamps = [];
        foreach ($envelope->all() as $stamps) {
            $allStamps = array_merge($allStamps, $stamps);
        }
        return $allStamps;
    }
}
