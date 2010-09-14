<?php
use Nette\Debug,
	Nette\Environment,
	Nette\Application\Route,
	Nette\Application\SimpleRouter;

// Step 1: Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require LIBS_DIR.'/Nette/loader.php';

// Step 2: Configure environment
// 2a) enable Nette\Debug for better exception and error visualisation
Debug::enable();

// 2b) load configuration from config.ini file
Environment::loadConfig();

// Step 3: Configure application
// 3a) get and setup a front controller
$application=Environment::getApplication();

// 3b) establish database connection
$application->onStartup[]='Connection::initialize';
$application->onShutdown[]='Connection::disconnect';

if (is_file(APP_DIR.'/extensions.php')) {
	include_once APP_DIR.'/extensions.php';
	}

// Step 4: Setup application router
$router=$application->getRouter();

// mod_rewrite detection
if (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) {
	$router[]=new Route('index.php', array(
			'presenter' => 'Default',
			'action' => 'default',
			),
		Route::ONE_WAY
		);
	$router[]=new Route('<presenter>/<action>/<id>', array(
			'presenter' => 'Default',
			'action' => 'default',
			'id' => NULL,
			)
		);
	}
else {
	$router[]=new SimpleRouter('Perso:default');
}

// Step 5: Run the application!
$application->run();
