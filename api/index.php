<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';


$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;
$config['determineRouteBeforeAppMiddleware']= true;
$config['db']['host']   = "localhost";
$config['db']['user']   = "root";
$config['db']['pass']   = "";
$config['db']['dbname'] = "android_demo";


$app = new \Slim\App(["settings" => $config]);;

$container = $app->getContainer();

$container['view'] = new \Slim\Views\PhpRenderer("../templates/");

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    try
	{
	$pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
	catch(PDOException $e)
	{
		 $c->logger->addInfo("PDO error");
		 echo ("Error connecting to database". $e);
		 exit();
	}
	
	
	
	return $pdo;
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Method not found');
    };
};

$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

$container['csrf'] = function ($c) {
    return new \Slim\Csrf\Guard;
	
	
	/*
		 $guard->setFailureCallable(function ($request, $response, $next) {
        $request = $request->withAttribute("csrf_status", false);
        return $next($request, $response);
    });
	
	*/
};

$container['upload_directory'] = '../uploads';


$app->get('/get_category',function($request,$response){
	
		$func=new GetCateg();
		$func->set_db($this->db);
		$response->getBody()->write($func->get_table());
		return $response;
	
	
});

$app->get('/get_product',function($request,$response){
	
	
		$func=new GetProd();
		$func->set_db($this->db);
		$response->getBody()->write($func->get_table());
		return $response;
	
	
	
	
});

$app->get('/get_banners',function($request,$response){
	
		$func=new GetBanner();
		$func->set_db($this->db);
		$response->getBody()->write($func->get_table());
		return $response;

});

$app->post('/register_user',function($request,$response){
	
	$info=$request->getParsedBody();
	$func=new SetUser();
	$func->set_db($this->db);
	$mess=$func->set_data($info);
	
	if($mess == "Success")
	{
		$arr=array();
		$arr['status']="true";
		//$arr['user_id']=123;
		$arr['message']=$mess;
		$response=$response->withJson($arr, 200);
	}
    else
	{
		$arr=array();
		$arr['status']="false";
		//$arr['user_id']=123;
		$arr['message']=$mess;
		$response=$response->withJson($arr, 400);
		//$response=$response->withJson($mess, 400);
	}
	
	return $response;
});

$app->get('/login_user',function($request,$response){
	
	$info=$request->getParams();
	$func=new Login();
	$func->set_db($this->db);
	$mess=$func->check_login($info);
	
	if($mess['message'] == "Success")
	{
		$arr=array();
		$arr['status']="true";
		//$arr['user_id']=123;
		//$arr['message']=$mess['message'];
		$arr['userid']=$mess['id'];
		$response=$response->withJson($arr, 200);
	}
    else
	{
		$arr=array();
		$arr['status']="false";
		//$arr['user_id']=123;
		$arr['message']=$mess['message'];
		$response=$response->withJson($arr, 400);
	}
	return $response;
});

$app->get('/profile_user/{id}',function($request,$response,$args){
	
	$id=$args['id'];
	$func=new GetUser();
	$func->set_db($this->db);
	$mess=$func->get_table($id);
	
	if($mess['message']== "Success")
	{
		$arr=array();
		$arr['status']="true";
		$arr['data']=$mess;
		$response=$response->withJson($arr, 200);
	}
	else
	{
		$arr=array();
		$arr['status']="false";
		$arr['message']=$mess['message'];
		$response=$response->withJson($arr, 400);
		
	}
	
	return $response;
	});

/*$app->get('/', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
	
	$arr['name']="satyam";
	$response = $this->view->render($response, "home.html",$arr);
    //$response->getBody()->write("Hello, $name");

    return $response;
});

*/

$app->run();

?>