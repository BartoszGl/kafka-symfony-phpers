<?php

namespace App;

use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Log\Logger;

class ConnectorFactoryClassWithErrorCallbacks extends RdKafkaConnectionFactory
{
    public function __construct($config = 'kafka:')
    {
        $config = $this->addErrorCallbacksToConfig($config);
        parent::__construct($config);
    }

    private function addErrorCallbacksToConfig(array $config): array
    {
        $config['error_cb'] = function ($kafka, $err, $reason) {
            $logger = new Logger();
            $logger->error('dupa zbita');
        };
        $config['dr_msg_cb'] = function ($kafka, $message){
            $logger = new Logger();
            $logger->error('dupa nie zbita');
        };
        return $config;
    }
}
