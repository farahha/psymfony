<?php

use Symfony\Component\HttpFoundation\Request;

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';
include_once __DIR__.'/../var/bootstrap.php.cache';

$kernel = new AppKernel('prod', false); // Appel du noyau Kernel
$kernel->loadClassCache(); // On charge les classes présentes dans le cache pour gagner en temps

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter : Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals(); // Récuperation de la requête
$response = $kernel->handle($request); // Exécution de la requête et récupération de la réponse
$response->send(); // Envoi de la réponse à la requête demandée
$kernel->terminate($request, $response); // fin du cycle requête/reponse
