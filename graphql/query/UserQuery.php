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
                    'type' => GraphQL::listOf(GraphQL::type('user')),
                    'description' => 'user list',
                    'args' => [
                        'limit' => ['type' => GraphQL::int(), 'defaultValue' => 3]
                    ],
                    'resolve' => function ($root, $args) {
                        return User::take($args['limit'])->get();
                    }
                ],
                'user' => [
                    'type' => GraphQL::type('user'),
                    'description' => 'Returns user by id (in range of 1-5)',
                    'args' => [
                        'id' => GraphQL::nonNull(GraphQL::id())
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