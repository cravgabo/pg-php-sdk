<?php

namespace Paymentez\Resources;

use GuzzleHttp\Exception\RequestException;

use Paymentez\Exceptions\{
    ResponseException,
    PaymentezErrorException
};


class Cash extends Resource
{
    const GENERATE_ORDER_ENDPOINT = 'order';

    const ENDPOINTS = [
        self::GENERATE_ORDER_ENDPOINT => 'order/'
    ];

    public function generateOrder(array $carrier,
                             array $user,
                             array $order): self
    {
        $this->getRequestor()->validateRequestParams([
            'id' => 'string'
        ], $carrier);

        $this->getRequestor()->validateRequestParams([
            'id' => 'numeric',
            'email' => 'string'
        ], $user);

        $this->getRequestor()->validateRequestParams([
            'dev_reference' =>  'string',
            'amount' => 'numeric',
            'expiration_days' => 'numeric',
            'recurrent' => 'bool',
            'description' => 'string'
        ], $order);

        try {
            $response = $this->getRequestor()->post(self::ENDPOINTS[self::GENERATE_ORDER_ENDPOINT],
                [
                    'carrier' => $carrier,
                    'user' => $user,
                    'order' => $order
                ], [],
                false);
        } catch (RequestException $clientException) {
            ResponseException::launch($clientException);
        }

        if ($response->getStatusCode() == 200) {
            $this->setData(json_decode($response->getBody()));
            return $this;
        }

        throw new PaymentezErrorException("Can't generate cash order.");
    }
}