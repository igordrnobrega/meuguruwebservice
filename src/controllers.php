<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// API
$app->get('/', 'App\\Controllers\\Api::initialAction');
$app->get('/event/search', 'App\\Controllers\\Api::eventSearchAction');
$app->get('/segmentos', 'App\\Controllers\\Api::getSegmentosAction');
$app->get('/fornecedores', 'App\\Controllers\\Api::getFornecedoresAction');