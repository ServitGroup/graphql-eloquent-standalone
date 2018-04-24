<?php
use \GraphQL\Schema;
use \GraphQL\GraphQL as GraphQLbase;
use GraphQL\Type\Definition\ObjectType;
use \GraphQL\Error\FormattedError;
try {

    GraphQL::require_all(__DIR__.'/graphql');
    GraphQL::includeDir(__DIR__.'/graphql');
    // GraphQL::includeDir(__DIR__.'/graphql/query');
    // GraphQL::includeDir(__DIR__.'/graphql/mutation');

    $appContext = new AppContext();
    $appContext->viewer = User::find(1);
    $appContext->rootUrl = 'http://localhost:8080';
    $appContext->request = $_REQUEST;

    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $raw = file_get_contents('php://input') ?: '';
        $data = json_decode($raw, true);
    } else {
        $data = $_REQUEST;
    }
    $data += ['query' => null, 'variables' => null];

    if (null === $data['query']) {
        $data['query'] = '{user(id:1){id,firstName,lastName,email}}';
    }

    $useryqry = (new UserQuery())->getFields();  // type of Objectype
    $queryqry = (new FileQuery())->getFields();  // type of Objectype
    $query = new ObjectType(['name'=>'Query','fields'=> $useryqry    + $queryqry]);
    $schema = new Schema([
        'query' => $query
    ]);
        
    $result = GraphQLbase::execute(
        $schema,
        $data['query'],
        null,
        $appContext,
        (array) $data['variables']
    );

    if (!empty($_GET['debug']) && !empty($phpErrors)) {
        $result['extensions']['phpErrors'] = array_map(
            ['GraphQL\Error\FormattedError', 'createFromPHPError'],
            $phpErrors
        );
    }
    $httpStatus = 200;
} catch (\Exception $error) {
    $httpStatus = 500;
    if (!empty($_GET['debug'])) {
        $result['extensions']['exception'] = FormattedError::createFromException($error);
    } else {
        $result['errors'] = [FormattedError::create('Unexpected Error')];
    }
}

// header('Content-Type: application/json', true, $httpStatus);
echo json_encode($result);
