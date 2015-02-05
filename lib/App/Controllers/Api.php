<?php
namespace App\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Api {
    /**
     * @var Silex\Application
     */
    private $app;

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    public function initialAction(Request $request, Application $app) {
        return 'Meu Guru API';
    }

    // PARAMETROS estado cidade promotor local evento segmento inicio(opt) termino(opt)
    public function pesquisarEventosAction(Request $request, Application $app) {
        $parameters = $request->query->all();

        if(
            !array_key_exists('estado', $parameters) ||
            !array_key_exists('cidade', $parameters) ||
            !array_key_exists('promotor', $parameters) ||
            !array_key_exists('local', $parameters) ||
            !array_key_exists('evento', $parameters) ||
            !array_key_exists('segmento', $parameters)
        )
        {
            return 'Não foi possível completar a requisição por falta de paramêtros. Consulte manual.';
        }

        $table = 'imp_posts';

        $sqlResultEstado    = null;
        $sqlResultCidade    = null;
        $sqlResulPromotor   = null;
        $sqlResulLocal      = null;
        $sqlResultNome      = null;
        $sqlResulSegmento   = null;

        $sqlResulInicio;
        $sqlResulTermino;

        var_dump($parameters);
        // Padrão pros caso estado cidade promotor local inicio termino

        foreach ($parameters as $key => $value) {

            $sqlPadrao =    'select post_id ' .
                            'from imp_postmeta ' .
                            'where ' .
                            'meta_key = "%KEY%" and '.
                            'meta_value %METAEXPRESSION%;';

            if($value) {
                print($key);
                print_r($value);
                switch ($key) {
                    case 'estado':
                        $sqlPadrao = str_replace('%KEY%', 'estadoFeira', $sqlPadrao);

                        $replace = '= "' . mysql_escape_string($value) . '" ORDER BY post_id';
                        $sqlPadrao = str_replace('%METAEXPRESSION%', $replace, $sqlPadrao);

                        try {
                            $sqlResultEstado = $app['db']->fetchAll($sqlPadrao);
                        } catch (\PDOException $e) {
                            return $e->getMessage();
                        }

                        break;

                    case 'cidade':
                        $sqlPadrao = str_replace('%KEY%', 'cidadeFeira', $sqlPadrao);

                        $replace = 'like "%' . mysql_escape_string($value) . '%" ORDER BY post_id';
                        $sqlPadrao = str_replace('%METAEXPRESSION%', $replace, $sqlPadrao);

                        try {
                            $sqlResultCidade = $app['db']->fetchAll($sqlPadrao);
                        } catch (\PDOException $e) {
                            return $e->getMessage();
                        }

                        break;

                    case 'promotor':
                        $sqlPadrao = str_replace('%KEY%', 'promotorFeira', $sqlPadrao);

                        $replace = 'like "%' . mysql_escape_string($value) . '%" ORDER BY post_id';
                        $sqlPadrao = str_replace('%METAEXPRESSION%', $replace, $sqlPadrao);

                        try {
                            $sqlResulPromotor = $app['db']->fetchAll($sqlPadrao);
                        } catch (\PDOException $e) {
                            return $e->getMessage();
                        }

                        break;

                    case 'local':
                        $sqlPadrao = str_replace('%KEY%', 'localFeira', $sqlPadrao);

                        $replace = 'like "%' . mysql_escape_string($value) . '%" ORDER BY post_id';
                        $sqlPadrao = str_replace('%METAEXPRESSION%', $replace, $sqlPadrao);

                        try {
                            $sqlResulLocal = $app['db']->fetchAll($sqlPadrao);
                        } catch (\PDOException $e) {
                            return $e->getMessage();
                        }

                        break;

                    case 'evento':
                        $sql = 'SELECT ID FROM imp_posts WHERE post_title like "%' . mysql_escape_string($value) . '%" ORDER BY ID';

                        try {
                            $sqlResultNome = $app['db']->fetchAll($sql);
                        } catch (\PDOException $e) {
                            return $e->getMessage();
                        }

                        break;
                    case 'segmento':
                        $sql =  'SELECT ID FROM imp_posts ' .
                                'INNER JOIN imp_term_relationships itr ON ID = object_id ' .
                                'INNER JOIN imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
                                'INNER JOIN imp_terms segmento ON segmento.term_id = itt.term_id ' .
                                'WHERE segmento.slug = "' . mysql_escape_string($value) . '";';

                        try {
                            $sqlResulSegmento = $app['db']->fetchAll($sql);
                        } catch (\PDOException $e) {
                            return $e->getMessage();
                        }

                        break;
                 }
            }
        }

        if(array_key_exists('inicio', $parameters) || array_key_exists('termino', $parameters)) {

        } else if(array_key_exists('inicio', $parameters) || !array_key_exists('termino', $parameters)) {

        }else if (!array_key_exists('inicio', $parameters) || array_key_exists('termino', $parameters)){

        }

        print('ESTADO');
        var_dump($sqlResultEstado);
        print_r('NOME');
        var_dump($sqlResultNome);
        print_r('CIDADE');
        var_dump($sqlResultCidade);
        print_r('SEGMENTO');
        var_dump($sqlResulSegmento);
        print_r('PROMOTOR');
        var_dump($sqlResulPromotor);
        print_r('LOCAL');
        var_dump($sqlResulLocal);
        // print_r('INICIO');
        // var_dump($sqlResulInicio);
        // print_r('TERMINO');
        // var_dump($sqlResulTermino);

        die;


        $sql = '';

        $sql =  'SELECT * FROM ' . $table .
                ' WHERE ';

        if(array_key_exists('s', $parameters)) {
            $sql .= 'post_title LIKE "%' . $parameters['s'] . '%" AND ';
        }

        $sql .= 'post_type = "feiras";';



        return $app->json($sqlResult);
    }

    // PARAMETROS sem
    public function getSegmentosAction(Request $request, Application $app) {

        $sql =  'select slug, name from imp_terms t ' .
                'inner join imp_term_taxonomy tt on t.term_id = tt.term_id ' .
                'where tt.taxonomy = "segmento" ' .
                'order by name;';

        try {
            $sqlResult = $app['db']->fetchAll($sql);
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return $app->json($sqlResult);
    }

    // PARAMETROS sem
    public function getFornecedoresAction(Request $request, Application $app) {

        $sql =  'select name from imp_terms t ' .
                'inner join imp_term_taxonomy tt on t.term_id = tt.term_id ' .
                'where tt.taxonomy = "fornecedores" ' .
                'order by name;';

        try {
            $sqlResult = $app['db']->fetchAll($sql);
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return $app->json($sqlResult);
    }

    // PARAMETROS sem
    public function getLocaisAction(Request $request, Application $app) {

        $sql =  'select post_title from imp_posts where post_type = "pavilhao" order by post_title;';

        try {
            $sqlResult = $app['db']->fetchAll($sql);
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return $app->json($sqlResult);
    }
}