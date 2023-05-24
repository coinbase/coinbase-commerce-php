<?php
namespace CoinbaseCommerce;

use CoinbaseCommerce\Exceptions\InvalidResponseException;
use CoinbaseCommerce\Exceptions\SignatureVerificationException;
use CoinbaseCommerce\Resources\Event;

class Webhook
{
    /**
     * @throws SignatureVerificationException
     * @throws InvalidResponseException
     */
    public static function buildEvent(string $payload, string $sigHeader, string $secret): Event
    {
        $data = null;

        $data = \json_decode($payload, true);

        if (json_last_error()) {
            throw new InvalidResponseException('Invalid payload provided. No JSON object could be decoded.', $payload);
        }

        if (!isset($data['event'])) {
            throw new InvalidResponseException('Invalid payload provided.', $payload);
        }

        self::verifySignature($payload, $sigHeader, $secret);

        return new Event($data['event']);
    }

    /**
     * @throws SignatureVerificationException
     */
    public static function verifySignature(string $payload, string $sigHeader, string $secret): void
    {
        $computedSignature = \hash_hmac('sha256', $payload, $secret);

        if (!Util::hashEqual($sigHeader, $computedSignature)) {
            throw new SignatureVerificationException($computedSignature, $payload);
        }
    }
}
