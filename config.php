<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/db/data.db',
    'prefix' => '',
], 'default');
$capsule->bootEloquent();
$capsule->setAsGlobal();
$connection = $capsule->getConnection('default');

class User extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function files() {
      return $this->hasMany('File','user_id');
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