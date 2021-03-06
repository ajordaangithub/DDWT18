<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt18_week2', 'ddwt18','ddwt18');



/* redundant code here */
$nbr_series = count_series($db);
$nbr_users = count_users($db);
$right_column = use_template('cards');
$navigation_array = Array(
    1 => Array(
        'name' => 'Home',
        'url' => '/DDWT18/week2/'
    ),
    2 => Array(
        'name' => 'Overview',
        'url' => '/DDWT18/week2/overview/'
    ),
    3 => Array(
        'name' => 'My Account',
        'url' => '/DDWT18/week2/myaccount/'
    ),
    4 => Array(
        'name' => 'Register',
        'url' => '/DDWT18/week2/register/'
    ),
    5 => Array(
        'name' => 'Add Serie',
        'url' => '/DDWT18/week2/add/'
    ),
    6 => Array (
        'name' => 'Log in',
        'url' => '/DDWT18/week2/login'
    ));

/* Landing page */
if (new_route('/DDWT18/week2/', 'get')) {
    /* handle login error */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Home' => na('/DDWT18/week2/', True)
    ]);
    $navigation = get_navigation($navigation_array, 1);

    /* Page content */
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT18/week2/overview/', 'get')) {
    if (isset($_GET['error_msg'])) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Overview' => na('/DDWT18/week2/overview', True)
    ]);
    $navigation = get_navigation($navigation_array, 2);

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_serie_table($db, get_series($db));

    /* Choose Template */
    include use_template('main');
}

/* Single Serie */
elseif (new_route('/DDWT18/week2/serie/', 'get')) {

    /* Get series from db and error msg */
    if (isset($_GET['error_msg'])) {
        $serie_id = get_json_serieid($_GET['error_msg']);
        $error_msg = get_error($_GET['error_msg']);
    }

    else {
        $serie_id = $_GET['serie_id'];
    }

    $serie_info = get_serieinfo($db, $serie_id);

    /* Page info */
    $display_buttons = get_user_id() === $serie_info['user'] ? True : False;
    $page_title = $serie_info['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Overview' => na('/DDWT18/week2/overview/', False),
        $serie_info['name'] => na('/DDWT18/week2/serie/?serie_id='.$serie_id, True)
    ]);
    $navigation = get_navigation($navigation_array, 2);

    /* Page content */
    $added_by = get_user_name($db, $serie_info['user']);
    $page_subtitle = sprintf("Information about %s", $serie_info['name']);
    $page_content = $serie_info['abstract'];
    $nbr_seasons = $serie_info['seasons'];
    $creators = $serie_info['creator'];

    /* Choose Template */
    include use_template('serie');
}

/* Add serie GET */
elseif (new_route('/DDWT18/week2/add/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Add Series' => na('/DDWT18/week2/new/', True)
    ]);
    $navigation = get_navigation($navigation_array, 5);

    /* Page content */
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT18/week2/add/';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    };


    /* Choose Template */
    include use_template('new');
}

/* Add serie POST */
elseif (new_route('/DDWT18/week2/add/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Add serie to database */
    $feedback = add_serie($db, $_POST, get_user_id());
    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT18/week2/add/?error_msg=%s',
        json_encode($feedback)));
}

/* Edit serie GET */
elseif (new_route('/DDWT18/week2/edit/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    $navigation = get_navigation($navigation_array, 2);
    /* Get serie info from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        sprintf("Edit Series %s", $serie_info['name']) => na('/DDWT18/week2/new/', True)
    ]);

    /* Page content */
    $page_subtitle = sprintf("Edit %s", $serie_info['name']);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT18/week2/edit/';

    /* Get error msg from POST route */


    /* Choose Template */
    include use_template('new');
}

/* Edit serie POST */
elseif (new_route('/DDWT18/week2/edit/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    /* Update serie in the database */
    $feedback = update_serie($db, $_POST, get_user_id());
    $feedback['serie_id'] = $_POST['serie_id'];
    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT18/week2/serie/?error_msg=%s',
        json_encode($feedback)));
}

/* Remove serie */
elseif (new_route('/DDWT18/week2/remove/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    $serie_id = $_POST['serie_id'];
    $feedback = remove_serie($db, $serie_id);
    redirect(sprintf('/DDWT18/week2/overview/?error_msg=%s',
        json_encode($feedback)));
}

/* Myaccount GET */
elseif (new_route('/DDWT18/week2/myaccount/', 'get')) {
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    /* page info */
    $user_id = get_user_id();
    $user = get_user_name($db, $user_id);
    $page_title = 'My account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'My Account' => na('/DDWT18/week2/myaccount', True)
    ]);
    $navigation = get_navigation($navigation_array, 3);

    /* page content */
    $page_subtitle = sprintf("My account page");
    $page_content = 'View your account';

    /* Choose Template */
    include use_template('account');
}

/* Register page  GET*/
elseif (new_route('/DDWT18/week2/register/', 'get')) {
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    /* page info */
    $page_title = 'Register a new account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Register a new account' => na('/DDWT18/week2/register', True)
    ]);
    $navigation = get_navigation($navigation_array, 4);

    /* page content */
    $page_subtitle = sprintf("And add your favorite series");
    $page_content = '';

    /* Choose Template */
    include use_template('register');
}

/* register serie POST */
elseif (new_route('/DDWT18/week2/register/', 'post')) {
    /* Register user */
    $error_msg = register_user($db, $_POST);

    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT18/week2/register/?error_msg=%s',
        json_encode($error_msg)));
}

/* Login page  GET*/
elseif (new_route('/DDWT18/week2/login/', 'get')) {
    /* Check if logged in */
    if ( check_login() ) {
        redirect('/DDWT18/week2/myaccount/');
    }
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    /* page info */
    $page_title = 'Login';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Login' => na('/DDWT18/week2/login', True)
    ]);
    $navigation = get_navigation($navigation_array, 6);

    /* page content */
    $page_subtitle = sprintf("And add your favorite series");
    $page_content = '';

    /* Choose Template */
    include use_template('login');
}

/* login serie POST */
elseif (new_route('/DDWT18/week2/login/', 'post')) {
    /* Login user */
    $feedback = login_user($db, $_POST);

    /* Redirect to login GET route */
    redirect(sprintf('/DDWT18/week2//?error_msg=%s',
        json_encode($feedback)));
}

elseif (new_route('/DDWT18/week2/logout/', 'get')) {
    /* Logout user */
    $feedback = logout_user();


    /* Redirect to landing page */
    redirect(sprintf('/DDWT18/week2/?error_msg=%s',
        json_encode($feedback)));
}


else {
    http_response_code(404);
}

