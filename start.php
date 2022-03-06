<?php

include './vendor/autoload.php';
include './utils/DataManager.php';
include './utils/MainLogger.php';

use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;

use Psr\Http\Message\ServerRequestInterface;

function getRandStr($length = 6): string{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for($i = 0; $i < $length; $i++){
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

$logger = new utils\MainLogger();
$logger::setServerPath(__DIR__);

$http = new HttpServer(
	function(ServerRequestInterface $request): Response{

		$path = $request->getUri()->getPath();
	    $clearPath = '/' . implode('/', array_filter(explode('/', $path)));

		$logger = new utils\MainLogger();

		if($clearPath == '/'){

			return Response::html(file_get_contents($logger::getServerPath() . '/html/index.html'));

		}

		if($clearPath == '/create'){

			return Response::html(file_get_contents($logger::getServerPath() . '/html/create.html'));

		}

		if($clearPath == '/creates' and $request->getMethod() == "POST"){

			$title = $request->getParsedBody()['title'];

			if($title == ""){

				return Response::html(file_get_contents($logger::getServerPath() . '/html/create_fail.html'));

			}

			$array =  [];

			foreach($request->getParsedBody() as $name => $values){

				if($name == "title"){

					continue;

				}
				
				if(substr($name, 0, 4) != "vote"){

					return Response::html(file_get_contents($logger::getServerPath() . '/html/create_fail.html'));

				}
				
				if($values == ""){
					
					return Response::html(file_get_contents($logger::getServerPath() . '/html/create_fail.html'));

				}
				
				$array[$values] = 0;

			}

			if (empty($array)){

				return Response::html(file_get_contents($logger::getServerPath() . '/html/create_fail.html'));

			}

			var_dump($array);
			
			$page = new utils\DataManager($logger::getServerPath() . '/datas/page.json', utils\DataManager::JSON);
			$db['page'] = $page->getAll();

			$randURL = getRandStr(8);

			$db['page'][$randURL] = [
				'title' => $title,
				'vote' => $array
			];
			
			$page->setAll($db['page']);
			$page->save();

			$a = [];
            exec('php ' . $logger::getServerPath() . '/html/create_success.php ' . $randURL, $a);

            return Response::html(implode("\n", $a));

			//return Response::html(file_get_contents('html/create_fail.html'));

		}

		if (substr($clearPath, 0, 6) == '/page/'){

			$path_ = explode('/', $clearPath)[2];

			$page = new utils\DataManager($logger::getServerPath() . '/datas/page.json', utils\DataManager::JSON);
			$db['page'] = $page->getAll();

			if (empty($path_)){

				return Response::html(file_get_contents('html/404.html'));

			}

			if (!isset($db['page'][$path_])){

				return Response::html(file_get_contents('html/404.html'));

			}

			if ($request->getMethod() == "POST"){

				if (!isset($request->getParsedBody()['vote'])){

					// todo..

					return Response::html(file_get_contents('html/404.html'));

				}

				$db['page'][$path_]['vote'][$request->getParsedBody()['vote']] += 1;
				$db['page'][$path_]['ip'][$request->getServerParams()['REMOTE_ADDR']] = true;

				$page->setAll($db['page']);
				$page->save();

				return Response::html(file_get_contents('html/page_success.html'));

				/**$a = [];
            	exec('php ' . $logger::getServerPath() . '/html/page.php ' . $path_ . ' OK', $a);

            	return Response::html(implode("\n", $a));*/

			}

			if (isset($db['page'][$path_]['ip'][$request->getServerParams()['REMOTE_ADDR']])){

				$a = [];
            	exec('php ' . $logger::getServerPath() . '/html/page.php ' . $path_ . ' OK', $a);

            	return Response::html(implode("\n", $a));

			}

			$a = [];
            exec('php ' . $logger::getServerPath() . '/html/page.php ' . $path_, $a);

            return Response::html(implode("\n", $a));

		}

		return Response::html(file_get_contents('html/404.html'));

	}
);

$httpSocket = new SocketServer('0.0.0.0:80');

$http->listen($httpSocket);

$httpSocket->on('error', function(Exception $e){
	$logger::text($logger::COLOR_RED . 'HTTP SERVER FAIL, ' . $e->getMessage() . $logger::FORMAT_RESET);
});

$logger::text($logger::COLOR_GREEN . 'HTTP SERVER SUCCESS' . $logger::FORMAT_RESET);

?>