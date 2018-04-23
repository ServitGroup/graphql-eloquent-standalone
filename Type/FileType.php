<?php

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class FileType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'File',
            'description' => 'File',
            'fields' => function() {
                return [
                    'id'=>['type'=>Types::id()],
                    'name'=>['type'=>Types::string()],
                    'folder_id'=>['type'=>Types::string()],
                    'user_id'=>['type'=>Types::int()],
                    'TEXT'=>['type'=>Types::string()],
                ];
            }
        ];
        parent::__construct($config);
    }
}
