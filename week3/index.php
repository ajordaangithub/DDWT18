<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */







/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* set credentials */
$cred = set_cred('ddwt18', 'ddwt18');

/* Connect to DB */
$db = connect_db('localhost', 'ddwt18_week3', 'ddwt18', 'ddwt18');

/* Create Router instance */
$router = new \Bramus\Router\Router();


/* authenticate credentials */
$router->before('GET|POST|PUT|DELETE', '/api/.*', function() use($cred) {
    if (!check_cred($cred)){
        $feedback = [
            'type' => 'danger',
            'message' => 'Authentication failed. Please check the credentials.'
        ];
        echo json_encode($feedback);
        exit();
    }
});

// Add routes here
$router->mount('/api', function() use ($router, $db, $cred) {

    /*set content type to json */
    http_content_type("application/json");

    /* GET for reading all series */
    $router->get('/series', function() use($db) {
        $series = get_series($db);
        $series_json = json_encode($series);
        echo $series_json;
    });
    /* GET for reading individual series */
    $router->get('/series/(\d+)', function($id) use($db) {
        $series_info = get_serieinfo($db, $id);
        $series_info_json = json_encode($series_info);
        echo $series_info_json;
    });
    /*DELETE for removing series */
    $router->delete('/series/(\d+)', function($id) use($db) {
        $feedback = remove_serie($db, $id);
        $feedback_json = json_encode($feedback);
        echo $feedback_json;
    });
    /*POST for creating series */
    $router->post('/series', function() use($db) {
        $feedback = add_serie($db, $_POST);
        $feedback_json = json_encode($feedback);
        echo $feedback_json;
    });
    /*PUT for updating series */
    $router->put('/series/(\d+)', function($id) use($db) {
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        $serie_info = $_PUT + ["serie_id" => $id];
        $feedback = update_serie($db, $serie_info);
        $feedback_json = json_encode($feedback);
        echo $feedback_json;
    });
});


/* route for page not found */
$router->set404(function() {
 header('HTTP/1.1 404 Not Found');
 echo 'Error 404: this page does not exist';
});

/* Run the router */
$router->run();
