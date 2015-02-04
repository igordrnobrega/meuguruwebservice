<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// API
$app->get('/', 'App\\Controllers\\Api::initialAction');
$app->get('/event/search', 'App\\Controllers\\Api::eventSearchAction');



// $app->get('/jogar', 'App\\Controllers\\Index::indexAction');
// $app->post('/signin', 'App\\Controllers\\Index::signinAction');
// $app->get('/signin', 'App\\Controllers\\Index::signinAction');
// $app->get('/etapa/{hash}', 'App\\Controllers\\Index::etapaAction');
// $app->post('/resp', 'App\\Controllers\\Index::respostaAction');

// // BACKEND
// $app->get('/4c91d222b139eeccd6eb342f512ec180', 'App\\Controllers\\Backend::indexAction');
// $app->get('/retirado', 'App\\Controllers\\Backend::retiraAction');
