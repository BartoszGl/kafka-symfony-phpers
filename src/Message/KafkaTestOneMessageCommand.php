<?php

namespace App\Message;

use FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations as SerDe;

/**
 * @SerDe\AvroType("record")
 * @SerDe\AvroName("user")
 * @SerDe\AvroNamespace("example.namespace")
 */
readonly class KafkaTestOneMessageCommand
{
    public function __construct(
        /**
         * @SerDe\AvroType("string")
         */
        public string $name,
        /**
         * @SerDe\AvroType("string")
         */
        public string $surname,
    )
    {
    }
}
