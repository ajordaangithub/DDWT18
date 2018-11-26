<?php
/**
 * Model
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

/* Enable error reporting */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Check if the route exist
 * @param string $route_uri URI to be matched
 * @param string $request_type request method
 * @return bool
 *
 */
function new_route($route_uri, $request_type){
    echo $route_uri;
    echo $request_type;
    $route_uri_expl = array_filter(explode('/', $route_uri));
    print_r($route_uri_expl);
    $current_path_expl = array_filter(explode('/',parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    print_r($current_path_expl);
    echo $_SERVER['REQUEST_URI'];
    echo PHP_URL_PATH;
    if ($route_uri_expl == $current_path_expl && $_SERVER['REQUEST_METHOD'] == strtoupper($request_type)) {
        return True;
    }
}

/**
 * Creates a new navigation array item using url and active status
 * @param string $url The url of the navigation item
 * @param bool $active Set the navigation item to active or inactive
 * @return array
 */
function na($url, $active){
    return [$url, $active];
}

/**
 * Creates filename to the template
 * @param string $template filename of the template without extension
 * @return string
 */
function use_template($template){
    $template_doc = sprintf("views/%s.php", $template);
    return $template_doc;
}

/**
 * Creates breadcrumb HTML code using given array
 * @param array $breadcrumbs Array with as Key the page name and as Value the corresponding url
 * @return string html code that represents the breadcrumbs
 */
function get_breadcrumbs($breadcrumbs) {
    $breadcrumbs_exp = '<nav aria-label="breadcrumb">';
    $breadcrumbs_exp .= '<ol class="breadcrumb">';
    foreach ($breadcrumbs as $name => $info) {
        if ($info[1]){
            $breadcrumbs_exp .= '<li class="breadcrumb-item active" aria-current="page">'.$name.'</li>';
        }else{
            $breadcrumbs_exp .= '<li class="breadcrumb-item"><a href="'.$info[0].'">'.$name.'</a></li>';
        }
    }
    $breadcrumbs_exp .= '</ol>';
    $breadcrumbs_exp .= '</nav>';
    return $breadcrumbs_exp;
}

/**
 * Creates navigation HTML code using given array
 * @param array $navigation Array with as Key the page name and as Value the corresponding url
 * @return string html code that represents the navigation
 */
function get_navigation($navigation){
    $navigation_exp = '<nav class="navbar navbar-expand-lg navbar-light bg-light">';
    $navigation_exp .= '<a class="navbar-brand">Series Overview</a>';
    $navigation_exp .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
    $navigation_exp .= '<span class="navbar-toggler-icon"></span>';
    $navigation_exp .= '</button>';
    $navigation_exp .= '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
    $navigation_exp .= '<ul class="navbar-nav mr-auto">';
    foreach ($navigation as $name => $info) {
        if ($info[1]){
            $navigation_exp .= '<li class="nav-item active">';
            $navigation_exp .= '<a class="nav-link" href="'.$info[0].'">'.$name.'</a>';
        }else{
            $navigation_exp .= '<li class="nav-item">';
            $navigation_exp .= '<a class="nav-link" href="'.$info[0].'">'.$name.'</a>';
        }

        $navigation_exp .= '</li>';
    }
    $navigation_exp .= '</ul>';
    $navigation_exp .= '</div>';
    $navigation_exp .= '</nav>';
    return $navigation_exp;
}

/**
 * Pritty Print Array
 * @param $input
 */
function p_print($input){
    echo '<pre>';
    print_r($input);
    echo '</pre>';
}

/**
 * Creats HTML alert code with information about the success or failure
 * @param bool $type True if success, False if failure
 * @param string $message Error/Success message
 * @return string
 */
function get_error($feedback){
    $error_exp = '
        <div class="alert alert-'.$feedback['type'].'" role="alert">
            '.$feedback['message'].'
        </div>';
    return $error_exp;
}

/**
 * Connect Model to the SQL database
 * @param $host host adress
 * @param $db database name
 * @param $user username to acces the db
 * @param $pass password to acces the db
 * @return PDO
 */

function connect_db($host, $db, $user, $pass){
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
$pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
echo sprintf("Failed to connect. %s",$e->getMessage());
}
return $pdo;
}

/**
 * Creates array with all series data from the SQL database
 * @param $pdo from the connect_db function
 * @return $series_exp array with all series info
 */

function get_series($pdo){
$stmt = $pdo->prepare('SELECT * FROM series');
$stmt->execute();
$series = $stmt->fetchAll();
$series_exp = Array();
/* Create array with htmlspecialchars */
foreach ($series as $key => $value){
foreach ($value as $user_key => $user_input) {
$series_exp[$key][$user_key] = htmlspecialchars($user_input);
}
}
return $series_exp;
}

/**
 * Returns the total number of series in the DB by simply counting array length
 * @param $array array of which the length to be counted
 * @return int array length
 */
function count_series($array) {
    return (count($array));
}

/**
 * Returns HTML table with info for all series
 * @param $series array with all series
 * @return $table_exp table in html format
 */

function get_serie_table($series){
    $table_exp =
        '
<table class="table table-hover">
<thead
<tr>
<th scope="col">Series</th>
<th scope="col"></th>
</tr>
</thead>
<tbody>';
    foreach($series as $key => $value){
        $table_exp .=
            '
<tr>
<th scope="row">'.$value['name'].'</th>
<td><a href="/DDWT18/week1/serie/?serie_id='.$value['id'].'" role="button" class="btn btn-primary">More info</a></td>
</tr>
';
    }
    $table_exp .=
        '
</tbody>
</table>
';
    return $table_exp;
}


/**
 * Get single serie info from pdo array.
 * Implemented like this because the pdo array keys do not match the serie IDs
 * This caused issues after removing a serie, because the SQL database uses auto increment for the serie IDs
 * @param $serie_id id of the serie to be returned
 * @param $series array with all series
 * @return array with fields (name, creator etc) as keys and corresponding values for single serie
 */
function get_series_info($serie_id, $series){
    foreach ($series as $key => $value) {
        if ($value['id'] === $serie_id) {
            return $series[$key];
        }
    }
}

/**
 * Adds a new serie to the database. Also generates corresponding errors when something goes wrong
 * @param $serie_info _POST array with single serie info
 * @param $pdo pdo database object
 * @return array feedback messages
 */
function add_series($serie_info, $pdo){
    foreach ($serie_info as $key => $value) {
        if (empty($serie_info[$key])) {
            return [
                'type' => 'danger',
                'message' => 'There was an error. Not all fields were filled in.'
            ];
        }
    }


    if (is_numeric($serie_info['Seasons']) === false) {
        return [
            'type' => 'danger',
            'message' => 'There was an error. You should enter a number in the field Seasons'
        ];
    }

    $stmt = $pdo->prepare('SELECT * FROM series WHERE name = ?');
    $stmt->execute([$serie_info['Name']]);
    $serie = $stmt->rowCount();
    if ($serie){
        return [
            'type' => 'danger',
            'message' => 'This series was already added.'
        ];
    }


    $stmt = $pdo->prepare("INSERT INTO series (name, creator, seasons, abstract) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $serie_info['Name'],
        $serie_info['Creator'],
        $serie_info['Seasons'],
        $serie_info['Abstract']
    ]);
    $inserted = $stmt->rowCount();
    if ($inserted == 1) {
        return [
            'type' => 'success',
            'message' => sprintf("Series '%s' added to Series Overview.", $serie_info['Name'])
        ];
    }
    else {
        return [
            'type' => 'danger',
            'message' => 'There was an error. The series was not added. Try it again.'
        ];
    }
}

/**
 * Updates series info after it has been edited. Also generates corresponding error messages when something goes wrong
 * @param $serie_info  _POST array with single serie info
 * @param $pdo pdo database object
 * @return feedback messages
 */

function update_series($serie_info, $pdo) {
    /*check empty fields */
    if (
        empty($serie_info['Name']) or
        empty($serie_info['Creator']) or
        empty($serie_info['Seasons']) or
        empty($serie_info['Abstract']) or
        empty($serie_info['serie_id'])
    ) {
        return [
            'type' => 'danger',
            'message' => 'There was an error. Not all fields were filled in.'
        ];
    }
    /* Check data type */
    if (!is_numeric($serie_info['Seasons'])) {
        return [
            'type' => 'danger',
            'message' => 'There was an error. You should enter a number in the field
Seasons.'
        ];
    }
    /* Get current series name */
    $stmt = $pdo->prepare('SELECT * FROM series WHERE id = ?');
    $stmt->execute([$serie_info['serie_id']]);
    $serie = $stmt->fetch();
    $current_name = $serie['name'];
    /* Check if serie already exists */
    $stmt = $pdo->prepare('SELECT * FROM series WHERE name = ?');
    $stmt->execute([$serie_info['Name']]);
    $serie = $stmt->fetch();
    if ($serie_info['Name'] == $serie['name'] and $serie['name'] != $current_name){
        return [
            'type' => 'danger',
            'message' => sprintf("The name of the series cannot be changed. %s already exists.",
                $serie_info['Name'])
        ];
    }
    /* Update Serie */
    $stmt = $pdo->prepare("UPDATE series SET name = ?, creator = ?, seasons = ?, abstract = ? WHERE id = ?");
    $stmt->execute([
        $serie_info['Name'],
        $serie_info['Creator'],
        $serie_info['Seasons'],
        $serie_info['Abstract'],
        $serie_info['serie_id']
    ]);
    $updated = $stmt->rowCount();
    if ($updated == 1) {
        return [
            'type' => 'success',
            'message' => sprintf("Series '%s' was edited!", $serie_info['Name'])
        ];
    }
    else {
        return [
            'type' => 'warning',
            'message' => 'The series was not edited. No changes were detected'
        ];
    }

}

/**
 * Completely removes a serie from the database
 * @param $pdo pdo database object
 * @param $serie_id id of the serie to be removed
 * @param $serie_info array with all series info
 * @return array feedback messages
 */

function remove_serie($pdo, $serie_id, $serie_info ) {
    $stmt = $pdo->prepare("DELETE FROM series WHERE id = ?");
    $stmt->execute([$serie_id]);
    $deleted = $stmt->rowCount();
    if ($deleted == 1) {
        return [
            'type' => 'success',
            'message' => sprintf("Series '%s' was removed!", $serie_info['name'])
        ];
    }
    else {
        return [
            'type' => 'warning',
            'message' => 'An error occurred. The series was not removed.'
        ];
    }
}

