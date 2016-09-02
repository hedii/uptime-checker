<?php

namespace Hedii\UptimeChecker;

use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

class UptimeChecker
{
    /**
     * @var \GuzzleHttp\Client
     */
    public $client;

    /**
     * An array of lists of status codes.
     *
     * @var array
     */
    private $status_codes = [
        'success' => [200, 201, 202, 203, 204, 205, 206, 207, 210, 226]
    ];

    /**
     * The number of seconds to wait while trying to connect to a server.
     *
     * @var int
     */
    private $connectionTimeout = 10;

    /**
     * The timeout of the request in seconds.
     *
     * @var int
     */
    private $requestTimeout = 30;

    /**
     * The request uri.
     *
     * @var \GuzzleHttp\Psr7\Uri|String
     */
    private $uri;

    /**
     * Whether the request is successful or not.
     *
     * @var bool
     */
    private $success;

    /**
     * The http response status code.
     *
     * @var int
     */
    private $status;

    /**
     * The http response message.
     *
     * @var string
     */
    private $message;

    /**
     * The transfer time in seconds.
     *
     * @var float
     */
    private $transferTime;

    /**
     * UptimeChecker constructor.
     *
     * @param \GuzzleHttp\Client|null $client
     */
    public function __construct(Client $client = null)
    {
        if ($client) {
            $this->client = $client;
        } else {
            $this->client = new Client([
                'connect_timeout' => $this->connectionTimeout,
                'timeout' => $this->requestTimeout
            ]);
        }
    }

    /**
     * Connection timeout getter.
     *
     * @return int
     */
    public function getConnectionTimeout()
    {
        return $this->connectionTimeout;
    }

    /**
     * Connection timeout setter.
     *
     * @param float $timeout
     * @return $this
     */
    public function setConnectionTimeout(float $timeout)
    {
        $this->connectionTimeout = $timeout;

        return $this;
    }

    /**
     * Request timeout getter.
     *
     * @return int
     */
    public function getRequestTimeout()
    {
        return $this->requestTimeout;
    }

    /**
     * Request timeout setter.
     *
     * @param float $timeout
     * @return $this
     */
    public function setRequestTimeout(float $timeout)
    {
        $this->requestTimeout = $timeout;

        return $this;
    }

    /**
     * Perform the uptime check.
     *
     * @param string $url
     * @return array
     */
    public function check(string $url)
    {
        try {
            $response = $this->client->request('GET', $url, [
                'on_stats' => function (TransferStats $stats) {
                    $this->uri = $stats->getEffectiveUri();
                    $this->transferTime = $stats->getTransferTime();
                }
            ]);

            $this->success = in_array($response->getStatusCode(), $this->status_codes['success']) ? true : false;
            $this->status = $response->getStatusCode();
            $this->message = $response->getReasonPhrase();

            return $this->report();
        } catch (\Exception $e) {
            $this->success = false;
            $this->status = $e->getCode();
            $this->message = trim($e->getMessage());

            return $this->report();
        }
    }

    /**
     * Build an array with the check report info.
     *
     * @return array
     */
    private function report()
    {
        return [
            'uri' => (string) $this->uri,
            'success' => $this->success,
            'status' => $this->status,
            'message' => $this->message,
            'transfer_time' => $this->transferTime
        ];
    }
}

