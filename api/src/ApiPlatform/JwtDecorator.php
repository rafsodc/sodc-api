<?php
declare(strict_types=1);

namespace App\ApiPlatform;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model;
use ArrayObject;

final class JwtDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    )
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['RefreshToken'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'jwtrefreshtoken',
                ],
            ],
        ]);
        $schemas['Credentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'johndoe@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'password',
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'JWT Token',
            description: 'Creates a user token',
            post: new Model\Operation(
                operationId: 'postCredentialsItem',
                tags: ['Authentication'],
                responses: [
                    '200' => [
                        'description' => 'User token created',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Creates a user token.',
                requestBody: new Model\RequestBody(
                    description: 'The login data',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $openApi->getPaths()->addPath('/token/login', $pathItem);

        $pathItem = new Model\PathItem(
            ref: 'JWT Token Refresh',
            description: 'JWT',
            post: new Model\Operation(
                operationId: 'postRefreshToken',
                tags: ['Authentication'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token from Refresh Token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get JWT token from Refresh Token.',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token from Refresh Token',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/RefreshToken',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath('/token/refresh', $pathItem);

        $pathItem = new Model\PathItem(
            ref: 'JWT Token Logout',
            description: 'Invalidates tokens',
            post: new Model\Operation(
                operationId: 'getLogout',
                tags: ['Authentication'],
                responses: [
                '200' => [
                    'description' => 'Invalidates tokens',
                    'content' => [
                        'application/json' => [
                            ],
                        ],
                    ],
                ],
                summary: 'Invalidates tokens',
                requestBody: new Model\RequestBody(
                    description: 'Invalidates tokens',
                    content: new ArrayObject([
                        'application/json' => [
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath('/token/logout', $pathItem);

        return $openApi;
    }
}
