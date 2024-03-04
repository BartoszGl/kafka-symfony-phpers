<?php

namespace App;

class KafkaErrorCallback
{
    public function format(): void
    {
        throw new \Exception();
    }
}
