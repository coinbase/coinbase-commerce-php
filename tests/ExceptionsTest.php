<?php

namespace CoinbaseCommerce\Tests;

use CoinbaseCommerce\Exceptions\ApiException;
use CoinbaseCommerce\Exceptions\InvalidRequestException;
use CoinbaseCommerce\Exceptions\AuthenticationException;
use CoinbaseCommerce\Exceptions\ParamRequiredException;
use CoinbaseCommerce\Exceptions\ResourceNotFoundException;
use CoinbaseCommerce\Exceptions\RateLimitExceededException;
use CoinbaseCommerce\Exceptions\InternalServerException;
use CoinbaseCommerce\Exceptions\ServiceUnavailableException;
use CoinbaseCommerce\Exceptions\ValidationException;
use CoinbaseCommerce\Resources\Charge;

class ExceptionsTest extends BaseTest
{
    protected $fixtures = [];

    public function getFixtures()
    {
        if (empty($this->fixtures)) {
            $this->fixtures = [
                [
                    'response' => [
                        'statusCode' => 400,
                        'body' => [
                            'error' => [
                                'type' => 'invalid_request',
                                'message' => 'Pricing type is not included in the list'
                            ]
                        ]
                    ],
                    'exceptionClass' => InvalidRequestException::getClassName()
                ],
                [
                    'response' => [
                        'statusCode' => 401,
                        'body' => [
                            'error' => [
                                'type' => 'authorization_error',
                                'message' => 'You are not authorized to do that.'
                            ]
                        ]
                    ],
                    'exceptionClass' => AuthenticationException::getClassName()
                ],
                [
                    'response' => [
                        'statusCode' => 404,
                        'body' => [
                            'error' => [
                                'type' => 'not_found',
                                'message' => 'Not foun'
                            ]
                        ]
                    ],
                    'exceptionClass' => ResourceNotFoundException::getClassName()
                ],
                [
                    'response' => [
                        'statusCode' => 429,
                        'body' => [
                            'error' => [
                                'type' => 'rate_limit_exceeded',
                                'message' => 'Rate limit exceeded'
                            ]
                        ]
                    ],
                    'exceptionClass' => RateLimitExceededException::getClassName()
                ],
                [
                    'response' => [
                        'statusCode' => 500,
                        'body' => [
                            'error' => [
                                'type' => 'internal_server_error',
                                'message' => 'Internal server error'
                            ]
                        ]
                    ],
                    'exceptionClass' => InternalServerException::getClassName()
                ],
                [
                    'response' => [
                        'statusCode' => 503,
                        'body' => ''
                    ],
                    'exceptionClass' => ServiceUnavailableException::getClassName()
                ],
                [
                    'response' => [
                        'statusCode' => 500,
                        'body' => [
                            'error' => [
                                'type' => 'internal_server_error',
                                'message' => 'Internal server error'
                            ]
                        ]
                    ],
                    'exceptionClass' => InternalServerException::getClassName()
                ],
                [
                    'response' => [
                        'statusCode' => 400,
                        'body' => [
                            'error' => [
                                'type' => 'validation_error',
                                'message' => 'Validation error'
                            ]
                        ]
                    ],
                    'exceptionClass' => ValidationException::getClassName()
                ],
                [
                    'response' => [
                        'statusCode' => 400,
                        'body' => [
                            'error' => [
                                'type' => 'param_required',
                                'message' => 'Validation error'
                            ]
                        ]
                    ],
                    'exceptionClass' => ParamRequiredException::getClassName()
                ],
                [
                    'response' => [
                        'statusCode' => 502,
                        'body' => ''
                    ],
                    'exceptionClass' => ApiException::getClassName()
                ]
            ];
        }

        return $this->fixtures;
    }

    public function testApiExceptions()
    {
        Charge::setClient($this->apiClient);

        foreach ($this->getFixtures() as $fixture) {
            try {
                $this->appendRequest($fixture['response']['statusCode'], $fixture['response']['body']);

                Charge::create(
                    [
                        'name' => 'Test Name',
                        'description' => 'Test Description'
                    ]
                );
            } catch (\Exception $exception) {
                $this->assertInstanceOf($fixture['exceptionClass'], $exception);
                $this->assertEquals($fixture['response']['statusCode'], $exception->getCode());
            }
        }
    }
}
