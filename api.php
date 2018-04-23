<?php
require_once __DIR__ . '../../vendor/autoload.php';

use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/../db/data.db',
    'prefix' => '',
], 'default');
$capsule->bootEloquent();
$capsule->setAsGlobal();
$connection = $capsule->getConnection('default');

require __DIR__.'/types/UserType.php';


class User extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function files() {
      return $this->hasMany('File', 'userId');
    }
}
class File extends Model
{
    protected $table = 'file';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
class Version extends Model
{
    protected $table = 'version';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function user(){
      return $this->belongsTo('User','userId');
    }
}
class Folder extends Model
{
    protected $table = 'folder';
    protected $primaryKey = 'id';
    public $timestamps = false;
}

// $u = User::find(1);
// $u->files;
// dump($u);
// $v = Version::find(1);
// $v->user;
// dump($v);
// exit();
try {
    $userType = new UserType();
    dump($userType);
    // $userType = new ObjectType([
    //       'name' => 'user',
    //       'fields' => function () use (&$fileType) {
    //             return [
    //             'id' => ['type' => Type::id()],
    //             'firstName' => ['type' => Type::string()],
    //             'lastName' => ['type' => Type::string()],
    //             'email' => ['type' => Type::string()],
    //         //     'files' => [
    //         //           'type' => Type::listOf($fileType),
    //         //           'resolve' => function ($root, $args) {
    //         //               return $root->files;
    //         //             },
    //         //     ],
    //         ];
    //     },
    // ]);
    // dump($userType);

    $versionType = new ObjectType([
        'name' => 'version',
        'description' => 'Object of type version',
        'fields' => [
            'id' => ['type' => Type::id()],
            'name' => ['type' => Type::string()],
            'mimetype' => ['type' => Type::string()],
            'url' => ['type' => Type::string()],
            'size' => ['type' => Type::int()],
            'created' => ['type' => Type::int()],

            //resolve version user
            'user' => [
                'type' => $userType,
                'resolve' => function ($root, $args) {
                   return $root->user;
                },
            ],
        ],
    ]);

    $fileType = new ObjectType([
        'name' => 'file',
        'description' => 'Object of type File',
        'fields' => [
            'id' => ['type' => Type::id()],
            'name' => ['type' => Type::string()],
            'folderId' => ['type' => Type::id()],

            //resolve file user
            'user' => [
                'type' => $userType,
                'resolve' => function ($root, $args) {
                    $user = User::find($root['userId']);
                    return $user;
                },
            ],
            'versions' => [
                'type' => Type::listOf($versionType),
                'resolve' => function ($root, $args) {
                    $versions = Version::where('fileId', $root['id'])->get();
                    return $versions;
                },
            ],
        ],
    ]);

    $queryType = new ObjectType([
        'name' => 'Query',
        'fields' => [
            'user' => [
                'type' => $userType,
                'args' => [
                  'id' => ['type' => Type::nonNull(Type::int())],
                ],
                'resolve' => function ($root, $args) {
                  $user = User::find($args['id']);
                  return $user;
                },
            ],

            //get single file
            'file' => [
                'type' => $fileType,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                ],
                'resolve' => function ($root, $args) {
                    $f = File::find($args['id']);
                    return $f;
                },
            ],

            //get single version
            'version' => [
                'type' => $versionType,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                ],
                'resolve' => function ($root, $args) {
                    $v = version::find($args['id']);
                    return $v;
                },
            ],
        ],
    ]);

    // dump($queryType);

    //mutations
    $mutationType = new ObjectType([
        'name' => 'mutation',
        'fields' => [

            //add a user
            'addUser' => [
                'type' => $userType,
                'args' => [
                    'firstName' => ['type' => Type::nonNull(Type::string())],
                    'lastName' => ['type' => Type::nonNull(Type::string())],
                    'email' => ['type' => Type::nonNull(Type::string())],
                ],
                'resolve' => function ($root, $args) {
                    $user = new User();
                    $user->firstNmae = $args['firstNmae'];
                    $user->lastName = $args['lastName'];
                    $user->email = $args['email'];
                    return $user;
                },
            ],

            //delete a user
            'deleteUser' => [
                'type' => $userType,
                'args' => [
                    'id' => ['type' => Type::nonNull(Type::id())],
                ],
                'resolve' => function ($root, $args) {
                    User::delete($args['id']);
                    return null;
                },
            ],
        ],
    ]);

    // See docs on schema options:
    // http://webonyx.github.io/graphql-php/type-system/schema/#configuration-options
    $schema = new Schema([
        'query' => $queryType,
        'mutation' => $mutationType,
    ]);

    // See docs on server options:
    // http://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
    $server = new StandardServer([
        'schema' => $schema,
    ]);

    $server->handleRequest();

} catch (\Exception $e) {
    StandardServer::send500Error($e);
}
