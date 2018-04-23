<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use GraphQL\Type\Definition\ObjectType;

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';
require_once './AppContext.php';
require_once './Types.php';
require_once './Type/UserType.php';
require_once './Type/FileType.php';
require_once './Type/UserQuery.php';
require_once './Type/QueryType.php';
require_once './graphql.php';