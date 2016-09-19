<?php

namespace Slack;

use GuzzleHttp\Message\Request;
use Slack\Message\MessageInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Slack\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;


class Notifier
{
    /**
     * client
     *
     * @var \Slack\Client $client
     */
    protected $client;

    /**
     * serializer
     *
     * @var mixed
     */
    protected $serializer;

    /**
     * __construct
     *
     * @param mixed $client
     * @param mixed $serializer
     */
    public function __construct(Client $client, $serializer = null)
    {
        if (!$serializer) {
            $converter = new CamelCaseToSnakeCaseNameConverter(['icon_emoji', 'icon_url']);
            $normalizer = new GetSetMethodNormalizer(null,$converter);

            $serializer = new Serializer(
                array($normalizer),
                array(new JsonEncoder())
            );
        }

        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * notify
     *
     * @param MessageInterface $message
     * @param boolean $debug
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function notify(MessageInterface $message, $debug = false)
    {
        $payload = $this->serializer->serialize($message, 'json');

        /** @var Request $request */
        $request = $this->client->createRequest('POST',
            '/services/hooks/incoming-webhook',
            [
                'body' => $payload,
                'debug' => $debug
            ]
        );

        return $this->client->send($request);
    }
}
