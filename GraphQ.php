<?php 

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Schema;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\NonNull;

class GraphQL
{
    protected static $app;

    protected static $types = [];
    protected static $querys = [];
    protected static $mutations = [];
    protected static $schemas = [];
    private static $max_scan_depth = 2;
    
    protected $typesInstances = [];


    public  function __construct($app)
    {
        self::$app = $app;
    }


    public static function execute($querystring,$variables=[],$appContext=null) 
    {
        self::buildSchemas();
        $result = GraphQLbase::execute(
            self::getSchemas(),
            $querystring,
            null,
            $appContext,
            (array) $variables
        );
        return $result['data'];
    }

    // public static function schema($schema = null)
    // {
    //     if ($schema instanceof Schema) {
    //         return $schema;
    //     }

    //     $this->clearTypeInstances();

    //     $schemaName = is_string($schema) ? $schema:config('graphql.schema', 'default');

    //     if (!is_array($schema) && !isset($this->schemas[$schemaName])) {
    //         throw new SchemaNotFound('Type '.$schemaName.' not found.');
    //     }

    //     $schema = is_array($schema) ? $schema : $this->schemas[$schemaName];

    //     if ($schema instanceof Schema) {
    //         return $schema;
    //     }

    //     $schemaQuery = array_get($schema, 'query', []);
    //     $schemaMutation = array_get($schema, 'mutation', []);
    //     $schemaSubscription = array_get($schema, 'subscription', []);
    //     $schemaTypes = array_get($schema, 'types', []);

    //     //Get the types either from the schema, or the global types.
    //     $types = [];
    //     if (sizeof($schemaTypes)) {
    //         foreach ($schemaTypes as $name => $type) {
    //             $objectType = $this->objectType($type, is_numeric($name) ? []:[
    //                 'name' => $name
    //             ]);
    //             $this->typesInstances[$name] = $objectType;
    //             $types[] = $objectType;
                
    //             $this->addType($type, $name);
    //         }
    //     } else {
    //         foreach ($this->types as $name => $type) {
    //             $types[] = $this->type($name);
    //         }
    //     }

    //     $query = $this->objectType($schemaQuery, [
    //         'name' => 'Query'
    //     ]);

    //     $mutation = $this->objectType($schemaMutation, [
    //         'name' => 'Mutation'
    //     ]);

    //     $subscription = $this->objectType($schemaSubscription, [
    //         'name' => 'Subscription'
    //     ]);

    //     return new Schema([
    //         'query' => $query,
    //         'mutation' => !empty($schemaMutation) ? $mutation : null,
    //         'subscription' => !empty($schemaSubscription) ? $subscription : null,
    //         'types' => $types
    //     ]);
    // }

    // public static function objectType($type, $opts = [])
    // {
    //     // If it's already an ObjectType, just update properties and return it.
    //     // If it's an array, assume it's an array of fields and build ObjectType
    //     // from it. Otherwise, build it from a string or an instance.
    //     $objectType = null;
    //     if ($type instanceof ObjectType) {
    //         $objectType = $type;
    //         foreach ($opts as $key => $value) {
    //             if (property_exists($objectType, $key)) {
    //                 $objectType->{$key} = $value;
    //             }
    //             if (isset($objectType->config[$key])) {
    //                 $objectType->config[$key] = $value;
    //             }
    //         }
    //     } elseif (is_array($type)) {
    //         $objectType = $this->buildObjectTypeFromFields($type, $opts);
    //     } else {
    //         $objectType = $this->buildObjectTypeFromClass($type, $opts);
    //     }

    //     return $objectType;
    // }



    public static function clearType($name=null)
    {
        if($name){
            if (isset(self::$types[$name])) {
                unset(self::$types[$name]);
            }
        } else {
            self::$types = [];
        }
    }

    public static function getType($name=null)
    {
        if($name) return self::$types[$name];
        return self::$types;
    }

    public static function clearSchema($name=null)
    {
        if($name){
            if (isset(self::$schemas[$name])) {
                unset(self::$schemas[$name]);
            }
        } else {
            self::$schemas = [];
        }
    }
    public static function buildSchemas() 
    {
        self::$schemas = []; 
        self::$schemas = new Schema([
            'query' => self::getQuery(),
            'mutation'=> self::getMutation()
        ]);
        return self::$schemas;
    }   

    public static function getSchemas()
    {
        return self::$schemas;
    }


    public static function clearQuery($name=null)
    {
        self::$querys = [];
    }

    public static function getQuery() {
        $query = new objectType([
            'name' => 'Query',
            'fields'=> self::$querys
        ]);
        return $query;
    }
    
    public static function loadQuery($path)
    {   
        try {
            
            if(is_dir($path)){
                $dir = new \RecursiveDirectoryIterator($path);
                $iterator = new \RecursiveIteratorIterator($dir);
                foreach ($iterator as $file) {
                    $fname = $file->getFilename();
                    if (preg_match('%\.php$%', $fname)) {
                        $queryclass = basename($fname,".php");
                        $q =  new $queryclass();
                        self::$querys += $q->getFields();
                    }
                }
            }
        } catch (Exception $e) {
            return;            
        }
    }
    
    public static function getMutation() 
    {
        $mutations = new objectType([
            'name' => 'Mutation',
            'fields'=> self::$mutations
        ]);
        return $mutations;
    }

    public static function loadMutation($path)
    {   
        try {
            if(is_dir($path)){
                $dir = new \RecursiveDirectoryIterator($path);
                $iterator = new \RecursiveIteratorIterator($dir);
                foreach ($iterator as $file) {
                    $fname = $file->getFilename();
                    if (preg_match('%\.php$%', $fname)) {
                        $queryclass = basename($fname,".php");
                        $q =  new $queryclass();
                        self::$mutations += $q->getFields();
                    }
                }
            }
        } catch (Exception $e) {
            return;
        }
    }

    public static function clearMutation($name = null)
    {
        self::$mutations= [];
    }

    public static function require_all($dir, $depth = 0)
    {
        if ($depth > self::$max_scan_depth || empty($dir)) {
            return;
        }
        $scan = glob("$dir".DIRECTORY_SEPARATOR."*");
        foreach ($scan as $path) {
            if (preg_match('/\.php$/', $path)) {
                require_once $path;
            } elseif (is_dir($path)) {
                self::require_all($path, $depth + 1);
            }
        }
    }

//-------------------------------------------------------------------
    public static function type($type)
    {
        if (isset(self::$types[$type])) {
            return self::$types[$type];
        } else {
            $class = ucfirst($type) . 'Type';
            self::$types[$type] = new $class();
            return self::$types[$type];
        }
    }


    /**
     * @param $name
     * @param null $objectKey
     * @return array
     */
    public static function htmlField($name, $objectKey = null)
    {
        return HtmlField::build($name, $objectKey);
    }



    // Let's add internal types as well for consistent experience

    public static function boolean()
    {
        return Type::boolean();
    }

    /**
     * @return \GraphQL\Type\Definition\FloatType
     */
    public static function float()
    {
        return Type::float();
    }

    /**
     * @return \GraphQL\Type\Definition\IDType
     */
    public static function id()
    {
        return Type::id();
    }

    /**
     * @return \GraphQL\Type\Definition\IntType
     */
    public static function int()
    {
        return Type::int();
    }

    /**
     * @return \GraphQL\Type\Definition\StringType
     */
    public static function string()
    {
        return Type::string();
    }

    /**
     * @param Type $type
     * @return ListOfType
     */
    public static function listOf($type)
    {
        return new ListOfType($type);
    }

    /**
     * @param Type $type
     * @return NonNull
     */
    public static function nonNull($type)
    {
        return new NonNull($type);
    }
//-------------------------------------------------------------------


}
