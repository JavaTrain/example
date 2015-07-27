<?php


require_once(__DIR__.'/../Framework/Loader.php');

Loader::register();

Loader::addNamespacePath('Framework\\', __DIR__.'/../Framework');
Loader::addNamespacePath('Blog\\', __DIR__.'/../src/Blog');


//$app = new \Framework\Application(__DIR__.'/../app/config/config.php');
//
//$app->run();

echo"<form method='post'>
<input type='text' name='name'>
<input type='text' age='name'>
<input type='submit'>
</form>";

$req = new Framework\Request\Request();
echo $req->post('name');
