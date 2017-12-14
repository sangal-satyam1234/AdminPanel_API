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
            ->write('Page not found');
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

$container['upload_directory'] =  '../uploads';

//$app->add($container->get('csrf'));
$app->add(function ($request, $response, $next) {
	
	 
		$route = $request->getAttribute('route');
		if($route)
			$routeName = $route->getName();
		else
		{
			$response = $next($request, $response);
			return $response;
		}
	
	$session=new SessionManager();
	if($session->validateSession() === false )
	{
		
		//$response->write('not logged in');
		
	
		
		
		//var_dump ($routeName);
		
		if($routeName === 'login' || $routeName === 'check_login')
		{ 
			$response = $next($request, $response);
		
		}	
		else
		{
		  session_start();
	// Unset all session values 
	$_SESSION = array();
	session_destroy();
		 
		 $this->flash->addMessage('message','Please Login to continue');
		 
		 $response=$response->withHeader('Location', $this->router->pathFor('login'));
		}
	}	
	
	else
	{
	
		if($routeName === 'login' || $routeName === 'check_login')
		{ 
			$response=$response->withHeader('Location', $this->router->pathFor('home'));
		
		}	
		else
			$response = $next($request, $response);
	
	}

	return $response;
});


$app->get('/login',function(Request $request,Response $response) {
	
		$arr=[
				'a' => "",
				'nameKey' => "",
				'valueKey' => "",
				'name' => "",
				'value' => ""
				];
				
			$messages = $this->flash->getMessages();
			$nameKey = $this->csrf->getTokenNameKey();
			$valueKey = $this->csrf->getTokenValueKey();
			$name = $request->getAttribute($nameKey);
			$value = $request->getAttribute($valueKey);
		 
		 if($messages)
		 {
		 $arr['a']=$this->flash->getFirstMessage('message');
		 }
		 
		 $arr['nameKey']=$nameKey;
		 $arr['valueKey']=$valueKey;
		 $arr['name']=$name;
		 $arr['value']=$value;
		
		$this->view->render($response,"login_page.html",$arr);
		return $response;
	
	
	
})->setName('login')->add($container->get('csrf'));;

$app->post('/check_login',function(Request $request,Response $response) {
	
	$info=$request->getParsedBody();
	$u_name=$info["user"];
	$u_pass=$info["password"];
	
	$u_name = filter_var($u_name, FILTER_SANITIZE_EMAIL);
	$u_pass = filter_var($u_pass, FILTER_SANITIZE_STRING);
	
	
	$myCon=$this->db;
			
			try
			{
			
			
				if (filter_var($u_name, FILTER_VALIDATE_EMAIL) === false) 
				{
					$this->flash->addMessage('message', 'Invalid Email ID');
					throw new Exception("Invalid Input");
				} 
			
			$stmt="SELECT * FROM authentication WHERE auth_email=:field1 ";
			$result=$myCon->prepare($stmt);
			$result->bindValue(':field1',$u_name,PDO::PARAM_STR);
			
			$result->execute();
			$rows=$result->fetch(PDO::FETCH_ASSOC);
			
			if($rows['auth_email'] == NULL )
			{
			 
			  $this->flash->addMessage('message', 'User does not exists');
			 
			 throw new Exception("User not found");
			
			}
			
			$hashed_password=$rows['auth_pass'];
			$user_id=$rows['auth_id'];
			if(password_verify($u_pass,$hashed_password))
			 {
			  
					
					$session=new SessionManager();
					$session->sessionStart($user_id,'Admin', 0, '/', 'localhost', true);
				
					$response=$response->withHeader('Location', $this->router->pathFor('home'));
					
					//header('Location: ../dashboard.php');
					//	exit();
			 
			 
			 
			 
			 
			 
			 }
			else
			{
			  $this->flash->addMessage('message', 'Invalid Password');
			  
			 throw new Exception("Invalid Password");
			}
			
			
			
			}
			catch(PDOException $ex)
			{
			 echo $ex->getMessage();
			}
			catch(Exception $e) {
				
				$response=$response->withHeader('Location', $this->router->pathFor('login'));
				
			}
	
	
	return $response; 
})->setName('check_login');

$app->any('/dashboard',function($request,$response) {
	
	$this->view->render($response,"dashboard.html");
	return $response;
	
})->setName('home');

$app->get('/categories',function($request,$response) {
	
	$arr=[		'nameKey' => "",
				'valueKey' => "",
				'name' => "",
				'value' => ""
				];
			
			$nameKey = $this->csrf->getTokenNameKey();
			$valueKey = $this->csrf->getTokenValueKey();
			$name = $request->getAttribute($nameKey);
			$value = $request->getAttribute($valueKey);
		
		 $arr['nameKey']=$nameKey;
		 $arr['valueKey']=$valueKey;
		 $arr['name']=$name;
		 $arr['value']=$value;
	
	$this->view->render($response,"add_category.html",$arr);
	return $response;
	
})->setName('add_category')->add($container->get('csrf'));;

$app->get('/products',function($request,$response) {
	
	$arr=[		'nameKey' => "",
				'valueKey' => "",
				'name' => "",
				'value' => "",
				'category' => ""
				];
			
			$nameKey = $this->csrf->getTokenNameKey();
			$valueKey = $this->csrf->getTokenValueKey();
			$name = $request->getAttribute($nameKey);
			$value = $request->getAttribute($valueKey);
		
		 $arr['nameKey']=$nameKey;
		 $arr['valueKey']=$valueKey;
		 $arr['name']=$name;
		 $arr['value']=$value;
		 
		 $func=new GetCateg();
		 $func->set_db($this->db);
		 $table=$func->get_table();
		 $arr['category']=$table;
	
	$this->view->render($response,"add_product.html",$arr);
	return $response;
	
})->setName('add_products')->add($container->get('csrf'));;

$app->get('/settings',function($request,$response) {
	
	$arr=[		'nameKey' => "",
				'valueKey' => "",
				'name' => "",
				'value' => "",
				'message' => ""
				];
			
			$nameKey = $this->csrf->getTokenNameKey();
			$valueKey = $this->csrf->getTokenValueKey();
			$name = $request->getAttribute($nameKey);
			$value = $request->getAttribute($valueKey);
			 $arr['nameKey']=$nameKey;
		 $arr['valueKey']=$valueKey;
		 $arr['name']=$name;
		 $arr['value']=$value;
	
	if($this->flash->getMessages())
	{
	$arr['message']=$this->flash->getFirstMessage('message');	
	}
		
	
	$this->view->render($response,"settings.html",$arr);
	return $response;
	
})->setName('settings')->add($container->get('csrf'));;

$app->any('/logout',function($request,$response) {
	
	session_start();
	// Unset all session values 
	$_SESSION = array();
	session_destroy();
	
	$response=$response->withHeader('Location', $this->router->pathFor('login'));
	return $response;
	
})->setName('logout');

$app->get('/get_category',function($request,$response){
	
		$func=new GetCateg();
		$func->set_db($this->db);
		$response->getBody()->write($func->get_table());
		return $response;
	
	
});

$app->post('/change_pass',function($request,$response){
	
	$info=$request->getParsedBody();
	
	$pass1=$info['new_password'];
	$pass2=$info['conf_password'];
	$id=$_SESSION['user_id'];
	
	if($pass1 === $pass2)
	{
		$func=new ChangePass();
		$func->set_db($this->db);
		$m=$func->set_pass($pass1,$id);
		if($m === "success")
		{
		
		$this->flash->addMessage("message","Password Successfully Changed");
		$response=$response->withHeader('Location', $this->router->pathFor('settings'));
		}
		else
		{
			$this->flash->addMessage("message",$m);
			$response=$response->withHeader('Location', $this->router->pathFor('settings'));
		}	
	
	}
	else
	{
	 	$this->flash->addMessage("message","Passwords do not match");
		$response=$response->withHeader('Location', $this->router->pathFor('settings'));
		
	
	}
	
	
	return $response;
	
	
});

$app->post('/post_category',function($request,$response){
	
		$info=$request->getParsedBody();
		$ln=$info['CategName'];
		
		
		$ln = filter_var($ln, FILTER_SANITIZE_STRING);
		
		
		$func=new SetCateg();
		$func->set_db($this->db);
		$mess= $func->set_data($ln);
		
		if($mess === "Success")
		{
			$response=$response->withJson($mess, 200);
			
		}
		else
			$response = $response->withJson($mess, 400);
		
		
		return $response;
	
});

$app->post('/post_product',function($request,$response){
	
	$info=$request->getParsedBody();
	$directory = $this->get('upload_directory');
	$directory=$directory."/products";
	$uploadedFiles = $request->getUploadedFiles();
	// handle single input with single file upload
	$uploadedFile = $uploadedFiles['mypic'];
	if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
						
						
							$extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
							$basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
							$filename = sprintf('%s.%0.8s', $basename, $extension);

							$uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

						
						//$response->write('uploaded ' . $filename . '<br/>');
						
						
	}
    
	$func=new SetProd();
	$func->set_db($this->db);
	$mess=$func->set_data($info,$filename,$directory);
	
	//$mess= $func->set_data($ln);
		
		if($mess === "Success")
		{
			
			$response=$response->withJson($mess, 200);
			
		}
		else
		{
			unlink($directory . DIRECTORY_SEPARATOR . $filename);
			$response = $response->withJson($mess, 400);

		}
		
		return $response;
	
	
	
});

$app->get('/get_product',function($request,$response){
	
	
		$func=new GetProd();
		$func->set_db($this->db);
		$response->getBody()->write($func->get_table());
		return $response;
	
	
	
	
});

$app->get('/banners',function($request,$response){
	
	
	$this->view->render($response,"add_banner.html");
	return $response;
	
});

$app->post('/post_banners',function($request,$response){
	
	$info=$request->getParsedBody();
	$directory = $this->get('upload_directory');
	$directory=$directory."/banners";
	$uploadedFiles = $request->getUploadedFiles();
	// handle single input with single file upload
	$uploadedFile = $uploadedFiles['mypic'];
	if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
						
						
							$extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
							$basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
							$filename = sprintf('%s.%0.8s', $basename, $extension);

							$uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

						
						//$response->write('uploaded ' . $filename . '<br/>');
						
						
	}
    
	$func=new SetBanner();
	$func->set_db($this->db);
	$mess=$func->set_data($info,$filename,$directory);
	
	//$mess= $func->set_data($ln);
		
		if($mess === "Success")
		{
			
			$response=$response->withJson($mess, 200);
			
		}
		else
		{
			unlink($directory . DIRECTORY_SEPARATOR . $filename);
			$response = $response->withJson($mess, 400);

		}
		
		return $response;
	
	
	
	
});

$app->get('/get_banners',function($request,$response){
	
		$func=new GetBanner();
		$func->set_db($this->db);
		$response->getBody()->write($func->get_table());
		return $response;

});

$app->delete('/delete_product/{id}',function($request,$response,$args){
	
	$pid=$args['id'];
	$func=new DeleteProd();
	$func->set_db($this->db);
	$mess=$func->delete_data($pid);
	
	
	if($mess === "Success")
		{
			
			$response=$response->withJson($mess, 200);
			
		}
		else
		{
			
			$response = $response->withJson($mess, 400);

		}
		
		return $response;
	
});

$app->delete('/delete_banner/{id}',function($request,$response,$args){
	
	$pid=$args['id'];
	$func=new DeleteBanner();
	$func->set_db($this->db);
	$mess=$func->delete_data($pid);
	
	
	if($mess === "Success")
		{
			
			$response=$response->withJson($mess, 200);
			
		}
		else
		{
			
			$response = $response->withJson($mess, 400);

		}
		
		return $response;
	
});

$app->delete('/delete_category/{id}',function($request,$response,$args){
	
	$pid=$args['id'];
	$func=new DeleteCategory();
	$func->set_db($this->db);
	$mess=$func->delete_data($pid);
	
	
	if($mess === "Success")
		{
			
			$response=$response->withJson($mess, 200);
			
		}
		else
		{
			
			$response = $response->withJson($mess, 400);

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