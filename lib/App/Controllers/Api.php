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

    // PARAMETROS s (nome), estado, cidade, segmento, promotor, local, inicio, termino
    public function eventSearchAction(Request $request, Application $app) {
        $parameters = $request->query->all();

        $table = 'imp_posts';

        $sql =  'SELECT * FROM ' . $table .
                ' WHERE ';

        if(array_key_exists('s', $parameters)) {
            $sql .= 'post_title LIKE "%' . $parameters['s'] . '%" AND ';
        }

        $sql .= 'post_type = "feiras";';

        try {
            $sqlResult = $app['db']->fetchAll($sql);
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return json_encode($sqlResult);
    }

    // PARAMETROS sem
    public function getSegmentosAction(Request $request, Application $app) {

        $sql =  'select name from imp_terms t ' .
                'inner join imp_term_taxonomy tt on t.term_id = tt.term_id ' .
                'where tt.taxonomy = "segmento" ' .
                'order by name;';

        try {
            $sqlResult = $app['db']->fetchAll($sql);
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return json_encode($sqlResult);
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

        return json_encode($sqlResult);
    }
}