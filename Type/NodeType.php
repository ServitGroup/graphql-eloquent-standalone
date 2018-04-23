<?php
use GraphQL\Type\Definition\InterfaceType;

class NodeType extends InterfaceType
{
    public function __construct()
    {
        $config = [
            'name' => 'Node',
            'fields' => [
                'id' => Types::id()
            ],
            'resolveType' => [$this, 'resolveNodeType']
        ];
        parent::__construct($config);
    }

    public function resolveNodeType($object)
    {
        if ($object instanceof User) {
            return Types::type('user');
        } else if ($object instanceof Image) {
            return Types::type('image');
        } else if ($object instanceof Story) {
            return Types::type('story');
        }
    }
}
