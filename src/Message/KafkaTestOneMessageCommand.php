<?php

namespace App\Message;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("user")
 * @SerDe\AvroNamespace("example.namespace")
 */
class KafkaTestOneMessageCommand
{
    public function __construct(
        /**
         * @SerDe\AvroType("string")
         */
        public readonly string $name,
        /**
         * @SerDe\AvroType("string")
         */
        public readonly string $surname,
    )
    {
    }
}
