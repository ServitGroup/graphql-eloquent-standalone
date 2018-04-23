<?php

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class UserQuery extends ObjectType {

    public function __construct()
    {
        $config = [
            'name' => 'UserQuery',
            'fields' => [
                'users' => [
                    'name' => 'users',
                    'type' => Types::listOf(Types::type('user')),
                    'description' => 'user list',
                    'args' => [
                        'limit' => ['type' => Types::int(), 'defaultValue' => 3]
                    ],
                    'resolve' => function ($root, $args) {
                        return User::take($args['limit'])->get();
                    }
                ],
                'user' => [
                    'type' => Types::type('user'),
                    'description' => 'Returns user by id (in range of 1-5)',
                    'args' => [
                        'id' => Types::nonNull(Types::id())
                    ],
                    'resolve' => function ($root, $args) {
                        return User::find($args['id']);
                    }
                ],
            ],
        ];
        parent::__construct($config);
    }

}