<pre><?php

function autoloader($className)
{
    $path = str_replace('_', '/' ,$className).".php";
    require_once('app/'.$path);
}

if ( ! spl_autoload_register( 'autoloader' ) ) {
    throw new Exception('Autoloader failed');
}

// include configs

foreach( glob('config/*.php') as $fn ) {
    include $fn;
}


// find route, if it exists

$found = false;

$Uri = $_SERVER['REQUEST_URI'];

foreach ( array_keys( $routes ) as $routeRegex ) {
    if ( preg_match( $routeRegex, $Uri ) ) {
        $found = true;
        $controller = $routes[ $routeRegex ]['controller'];
        $method = $routes[ $routeRegex ]['method'];
        $theController = new $controller;
        $theController->$method();
        break;
    }
}

if ( ! $found ) {
    echo "No route";
}


?>
