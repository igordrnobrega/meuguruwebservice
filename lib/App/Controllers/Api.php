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

        $sqlResultEstado    = array();
        $sqlResultCidade    = array();
        $sqlResulPromotor   = array();
        $sqlResulLocal      = array();
        $sqlResultNome      = array();
        $sqlResulSegmento   = array();
        $sqlResulInicio     = array();
        $sqlResulTermino    = array();

        // var_dump($parameters);
        // Padrão para os casos: estado cidade promotor local

        foreach ($parameters as $key => $value) {

            $sqlPadrao =    'select post_id ' .
                            'from imp_postmeta ' .
                            'where ' .
                            'meta_key = "%KEY%" and '.
                            'meta_value %METAEXPRESSION%;';

            if($value) {
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
                        $sql = 'SELECT ID post_id FROM imp_posts WHERE post_title like "%' . mysql_escape_string($value) . '%" ORDER BY ID';

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

        $eventosData = [];
        if($parameters['inicio'] != '' && $parameters['termino'] != '') {
            $sqlInicio = 'SELECT * FROM imp_postmeta where meta_key = "dataInicial" and STR_TO_DATE(meta_value, "%d/%m/%Y") >= "' . $parameters['inicio'] . '"';
            $sqlFim = 'SELECT * FROM imp_postmeta where meta_key = "dataFinal" and STR_TO_DATE(meta_value, "%d/%m/%Y") <= "' . $parameters['termino'] . '"';
            try {
                $sqlResulInicio = $app['db']->fetchAll($sqlInicio);
                $sqlResulTermino = $app['db']->fetchAll($sqlFim);
            } catch (\PDOException $e) {
                return $e->getMessage();
            }
            foreach ($sqlResulInicio as $key => $value) {
                foreach ($sqlResulTermino as $keyT => $valueT) {
                    if($value['post_id'] === $valueT['post_id']) {
                        array_push($eventosData, $valueT['post_id']);
                    }
                }
            }
        } else if($parameters['inicio'] != '' && $parameters['termino'] == '') {
            $sqlInicio = 'SELECT post_id FROM imp_postmeta where meta_key = "dataInicial" and STR_TO_DATE(meta_value, "%d/%m/%Y") >= "' . $parameters['inicio'] . '" limit 30';
            try {
                $eventosData = $app['db']->fetchAll($sqlInicio);
            } catch (\PDOException $e) {
                return $e->getMessage();
            }
        } else if ($parameters['inicio'] == '' && $parameters['termino'] != ''){
            $sqlFim = 'SELECT post_id FROM imp_postmeta where meta_key = "dataFinal" and STR_TO_DATE(meta_value, "%d/%m/%Y") <= "' . $parameters['termino'] . '" limit 30';
            try {
                $eventosData = $app['db']->fetchAll($sqlFim);
            } catch (\PDOException $e) {
                return $e->getMessage();
            }
        }

        $resultUm = array_merge($sqlResultEstado, $sqlResultNome, $sqlResultCidade, $sqlResulSegmento, $sqlResulPromotor, $sqlResulLocal, $eventosData);

        $resultDois = $this->removeValue($resultUm);

        $resultTres = array_count_values($resultDois);

        $return = array();
        foreach ($resultTres as $key => $value) {
            if($value === 1) {
                unset($resultTres[$key]);
            } else {
                array_push($return, $this->getEvent($key, $app));
            }
        }

        if(sizeof($return) == 0) {
            if(sizeof($resultUm) > 10) {
                $resultUm = array_slice($resultUm, 0, 10);
            }
            for ($i = 0; $i < sizeof($resultUm); $i++) {
                array_push($return, $this->getEvent($resultUm[$i]['post_id'], $app));
            }
        }

        return $app->json($return);
    }

    protected function getEvent($id, $app) {
        $return = array();

        $sql = 'select evento.ID, evento.post_title, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'where evento.ID = ' . $id . ' ' .
            'and detalhes.meta_value != "" ' .
            'group by detalhes.meta_value';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            foreach ($sqlResult as $key => $value) {
                if($key === 0) {
                    $return = $value;
                } else {
                    $return[$value['meta_key']] = $value['meta_value'];
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return $return;

    }

    public function getEventosAction(Request $request, Application $app) {
        $return = array();

        $sql = 'select evento.ID, evento.post_title, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'and detalhes.meta_value != "" ' .
            'and evento.post_type = "feiras" ';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    array_push($return, $value);
                } else if($value['ID'] == $id){
                    if(!$this->removeLixoWp($value['meta_key'])) {
                        $return[$count][$value['meta_key']] = $value['meta_value'];
                    }
                } else {
                    $count++;
                    $id = $value['ID'];
                    array_push($return, $value);
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($return), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);

    }

    protected function removeLixoWp($string) {
        $lixo = array(
            'AnuncianteFeira',
            'LocalFeira',
            '_edit_last',
            '_thumbnail_id',
            '_yoast_wpseo_focuskw',
            '_yoast_wpseo_linkdex',
            '_yoast_wpseo_title',
            'interesseAnuncioFeira',
            'meta_key',
            'meta_value',
            'siglaFeira',
            'timestamp',
            '_edit_lock',
            'emailFeira',
            '_wp_old_slug'
        );

        foreach ($lixo as $key => $value) {
            if($value === $string) {
                return true;
            }
        }

        return false;
    }

    protected function removeValue($array) {
        $return = array();
        foreach ($array as $key => $value) {
            array_push($return, $value['post_id']);
        }
        return $return;
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
        $return = array();

        $sql = 'select evento.ID, evento.post_title, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'and detalhes.meta_value != "" ' .
            'and itt.taxonomy = "fornecedores" ';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    array_push($return, $value);
                } else if($value['ID'] == $id){
                    $return[$count][$value['meta_key']] = $value['meta_value'];
                } else {
                    $count++;
                    $id = $value['ID'];
                    array_push($return, $value);
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($return), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }

    // PARAMETROS sem
    public function getLocaisAction(Request $request, Application $app) {
        $return = array();

        $sql = 'select evento.ID, evento.post_title, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'and detalhes.meta_value != "" ' .
            'and itt.taxonomy = "pavilhao" ';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    array_push($return, $value);
                } else if($value['ID'] == $id){
                    $return[$count][$value['meta_key']] = $value['meta_value'];
                } else {
                    $count++;
                    $id = $value['ID'];
                    array_push($return, $value);
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($return), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }
}