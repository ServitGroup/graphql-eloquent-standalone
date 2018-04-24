<?php
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class FileQuery extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => [
                'status' => GraphQL::boolean(),

                //==== move to  UserQuery.php
                // 'users'=>[
                //     'name'=>'users',
                //     'type'=>GraphQL::listOf(GraphQL::type('user')),
                //     'description' => 'user list',
                //     'args' => [
                //         'limit' =>[ 'type'=> GraphQL::int(), 'defaultValue'=>3 ]
                //     ],
                //     'resolve' => function($root,$args){
                //         return User::take($args['limit'])->get();
                //     }
                // ],
                // 'user' => [
                //     'type' => GraphQL::type('user'),
                //     'description' => 'Returns user by id (in range of 1-5)',
                //     'args' => [
                //         'id' => GraphQL::nonNull(GraphQL::id())
                //     ],
                //     'resolve' => function($root,$args) {
                //         return User::find($args['id']);
                //     }
                // ],
                //==== move to  UserQuery.php
                
                'file'=>[
                    'type'=>GraphQL::type('file'),
                    'args'=>[
                        'id'=> GraphQL::nonNull(GraphQL::id())
                    ],
                    'resolve' => function($root,$args) {
                        return File::find($args['id']);
                    }
                ],
                'files'=>[
                    'name'=>'files',
                    'description'=> 'files',
                    'type'=> GraphQL::listOf(GraphQL::type('file')),
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
