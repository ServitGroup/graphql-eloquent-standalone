<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
try {
    GraphQL::require_all(__DIR__.'/graphql');
    GraphQL::loadQuery(__DIR__.'/graphql/query');
    GraphQL::loadMutation(__DIR__.'/graphql/mutation');
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
    $rs = GraphQL::execute($data['query'], $data['variables'], $appContext);
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
// dump($rs);
echo json_encode($rs);

