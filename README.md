## Getting Started

1. Run `docker compose build --no-cache` to build fresh images
2. Run `docker compose up --pull always -d --wait` to start the project
3. Run `docker compose down --remove-orphans` to stop the Docker containers.

This repository is made as part of Kafka research. It connects symfony messenger 
and Kafka using 3 libraries:

1. enqueue/enqueue-bundle,
2. enqueue/rdkafka,
3. sroze/messenger-enqueue-transport

## Topic, consumer and producer config
Unfortunately Symfony messenger can not use Kafka directly. 
To communicate with Kafka and change its internal topic, consumer and producer configuration
we have to use enqueue, you can look at its config in config/packages/enqueue.yaml.
Inside of this file you can find description of each
basic configuration parameters that can be modified using enqueue bundle. To find full list of
those parameters visit (https://github.com/confluentinc/librdkafka/blob/master/CONFIGURATION.md)

To point to the topic that consumer will consume from we have to modify
config/packages/messenger.yaml
You can see consumed topic name under framework.messenger.transports.kafka.options.queue.name

## Producer

Our producer can be found under src/Controller/KafkaTestController.php. It is a part of our controller.

## Consumer 

Consumer can be found under src/MessageHandler/KafkaTestOneMessageCommandHandler.php or src/MessageHandler/KafkaTestCommandHandler.php







