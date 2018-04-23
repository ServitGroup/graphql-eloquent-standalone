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
                    'id' =>[ 'type'=> Types::int()],
                    'firstName' =>[ 'type'=> Types::string()],
                    'lastName' =>[ 'type'=> Types::string()],
                    'email' =>[ 'type'=> Types::string()],
                    'files'=>[
                        'type' => Types::listOf(Types::type('file')),
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
