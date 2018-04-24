<?php

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class UserType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'User',
            'description' => 'Our blog authors',
            'fields' => function() {
                return [
                    'id' =>[ 'type'=> GraphQL::int()],
                    'firstName' =>[ 'type'=> GraphQL::string()],
                    'lastName' =>[ 'type'=> GraphQL::string()],
                    'email' =>[ 'type'=> GraphQL::string()],
                    'files'=>[
                        'type' => GraphQL::listOf(GraphQL::type('file')),
                        'description' => 'files',
                        'resolve' => function($user,$args){
                            return $user->files;
                        }
                    ]
                ];    
            },
        ];
        parent::__construct($config);
    }
}
