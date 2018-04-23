<?php
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => [
                'status' => Types::boolean(),

                //==== move to  UserQuery.php
                // 'users'=>[
                //     'name'=>'users',
                //     'type'=>Types::listOf(Types::type('user')),
                //     'description' => 'user list',
                //     'args' => [
                //         'limit' =>[ 'type'=> Types::int(), 'defaultValue'=>3 ]
                //     ],
                //     'resolve' => function($root,$args){
                //         return User::take($args['limit'])->get();
                //     }
                // ],
                // 'user' => [
                //     'type' => Types::type('user'),
                //     'description' => 'Returns user by id (in range of 1-5)',
                //     'args' => [
                //         'id' => Types::nonNull(Types::id())
                //     ],
                //     'resolve' => function($root,$args) {
                //         return User::find($args['id']);
                //     }
                // ],
                //==== move to  UserQuery.php
                
                'file'=>[
                    'type'=>Types::type('file'),
                    'args'=>[
                        'id'=> Types::nonNull(Types::id())
                    ],
                    'resolve' => function($root,$args) {
                        return File::find($args['id']);
                    }
                ],
                'files'=>[
                    'name'=>'files',
                    'description'=> 'files',
                    'type'=> Types::listOf(Types::type('file')),
                    'resolve'=> function($root,$args) {
                        return File::get();
                    }
                ]
            ],
        ];
        // dump($config);
        parent::__construct($config);
    }
}
