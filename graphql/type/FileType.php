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
                    'id'=>['type'=>GraphQL::id()],
                    'name'=>['type'=>GraphQL::string()],
                    'folder_id'=>['type'=>GraphQL::string()],
                    'user_id'=>['type'=>GraphQL::int()],
                    'TEXT'=>['type'=>GraphQL::string()],
                ];
            }
        ];
        parent::__construct($config);
    }
}
