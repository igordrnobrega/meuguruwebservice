<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// API
$app->get('/', 'App\\Controllers\\Api::initialAction');

// LISTAS
$app->get('/eventos', 'App\\Controllers\\Api::getEventosAction');
$app->get('/fornecedores', 'App\\Controllers\\Api::getFornecedoresAction');
$app->get('/locais', 'App\\Controllers\\Api::getLocaisAction');
$app->get('/produtos', 'App\\Controllers\\Api::getProdutosAction');
$app->get('/servicos', 'App\\Controllers\\Api::getServicosAction');
$app->get('/noticias', 'App\\Controllers\\Api::getNoticiasAction');
$app->get('/estandes', 'App\\Controllers\\Api::getEstandesAction');

$app->get('/favoritos', 'App\\Controllers\\Api::getFavoritosAction');