<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// API
$app->get('/', 'App\\Controllers\\Api::initialAction');
$app->get('/eventos/pesquisar', 'App\\Controllers\\Api::pesquisarEventosAction');

// LISTAS
$app->get('/eventos', 'App\\Controllers\\Api::getEventosAction');
$app->get('/segmentos', 'App\\Controllers\\Api::getSegmentosAction');
$app->get('/fornecedores', 'App\\Controllers\\Api::getFornecedoresAction');
$app->get('/locais', 'App\\Controllers\\Api::getLocaisAction');
$app->get('/produtos', 'App\\Controllers\\Api::getProdutosAction');