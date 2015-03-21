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

        $sql = 'select evento.ID, evento.post_title, evento.post_content, imagem.guid, segmento.name, GROUP_CONCAT(DISTINCT detalhes.meta_key ORDER BY detalhes.meta_id ASC SEPARATOR "/*-*/") as meta_key, GROUP_CONCAT(DISTINCT detalhes.meta_value ORDER BY detalhes.meta_id ASC SEPARATOR "/*-*/") as meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'and detalhes.meta_value != "" ' .
            'and itt.taxonomy = "pavilhao" ' .
            'group by evento.ID ' .
            'order by evento.ID desc';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            foreach ($sqlResult as $key => $value) {

                $meta_key   = explode('/*-*/', $value['meta_key']);
                $meta_value = explode('/*-*/', $value['meta_value']);

                echo $meta_key[0];
                if($meta_key[0] == '_edit_lock') {
                    array_slice($meta_key, 0, 1);
                }


                if(sizeof($meta_key) == sizeof($meta_value)) {
                    foreach ($meta_key as $keyM => $valueM) {
                        $sqlResult[$key][$valueM] = $meta_value[$keyM];
                        if($valueM == '_thumbnail_id') {
                            $sqlResult[$key][$valueM] = $this->checkImg($this->getPost($meta_value[$keyM], 'guid', $app));
                        }
                    }
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($sqlResult), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }

    // PARAMETROS sem
    public function getProdutosAction(Request $request, Application $app) {
        $return = array();

        $sql = 'select evento.ID, evento.post_title, evento.post_content, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'and detalhes.meta_value != "" ' .
            'and segmento.name not like "Destaque%" ' .
            'and itt.taxonomy = "produtos" ';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    array_push($return, $value);
                } else if($value['ID'] == $id){
                    if($value['meta_key'] == '_thumbnail_id') {
                        $return[$count][$value['meta_key']] = $this->getPost($value['meta_value'], 'guid', $app);
                    } else if($value['meta_key'] == 'NomedaLoja') {
                        $return[$count][$value['meta_key']] = $this->getPost($value['meta_value'], 'post_title', $app);
                        $postMetas = $this->getPostMeta($value['meta_value'], $app);
                        foreach ($postMetas as $keyPM => $valuePM) {
                            if(!$this->removeLixoWp($valuePM['meta_key'])) {
                                $return[$count]['loja'][$valuePM['meta_key']] = $valuePM['meta_value'];
                            }
                        }
                    } else {
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

    // PARAMETROS sem
    public function getServicosAction(Request $request, Application $app) {
        $return = array();

        $sql = 'select evento.ID, evento.post_title, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'and detalhes.meta_value != "" ' .
            'and evento.post_type = "servicos" ';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    array_push($return, $value);
                } else if($value['ID'] == $id){
                    if($value['meta_key'] == '_thumbnail_id') {
                        $return[$count][$value['meta_key']] = $this->getPost($value['meta_value'], 'guid', $app);
                    } else {
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

    // PARAMETROS sem
    public function getNoticiasAction(Request $request, Application $app) {
        $return = array();

        $sql = 'select evento.ID, evento.post_title, evento.post_date, imagem.guid, segmento.name, GROUP_CONCAT(DISTINCT detalhes.meta_key SEPARATOR "/-/") as meta_key, GROUP_CONCAT(DISTINCT detalhes.meta_value SEPARATOR "/-/") as meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'and detalhes.meta_value != "" ' .
            'and evento.post_type = "noticia" ' .
            'group by evento.ID ' .
            'order by evento.ID desc';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            foreach ($sqlResult as $key => $value) {
                $meta_key = explode('/-/', $value['meta_key']);
                $meta_value = explode('/-/', $value['meta_value']);

                $date = new \DateTime($value['post_date']);
                $sqlResult[$key]['post_date'] = $date->format('d/m/Y');

                if(sizeof($meta_key) == sizeof($meta_value)) {
                    foreach ($meta_key as $keyM => $valueM) {
                        $sqlResult[$key][$valueM] = $meta_value[$keyM];
                    }
                }
                unset($sqlResult[$key]['meta_key']);
                unset($sqlResult[$key]['meta_value']);
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($sqlResult), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }

    // PARAMETROS sem
    public function getEstandesAction(Request $request, Application $app) {
        $return = array();

        $sql = 'select evento.ID, evento.post_title, evento.post_content, imagem.guid, detalhes.meta_key, detalhes.meta_value, segmento.name ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'where detalhes.meta_value != "" ' .
            'and segmento.name not like "Destaque%" ' .
            'and itt.taxonomy = "categorias_projetos" ' .
            'or itt.taxonomy = "tipos_projetos" ' .
            'and evento.post_type = "projetos" ';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    array_push($return, $value);
                } else if($value['ID'] == $id){
                    if($value['meta_key'] == '_thumbnail_id') {
                        $return[$count][$value['meta_key']] = $this->getPost($value['meta_value'], 'guid', $app);
                    } else {
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

    protected function getPostMeta($id, $app) {
        $sql = 'select * from imp_postmeta where post_id = ' . $id . ' and meta_value != ""';

        try {
            $result = $app['db']->fetchAll($sql);
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return $result;

    }

    protected function getPost($id, $colun, $app) {
        $sql = 'select ' . $colun .' from imp_posts where ID = ' . $id;

        try {
            $sqlResult = $app['db']->fetchAssoc($sql);
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        return $sqlResult[$colun];
    }

    protected function removeLixoWp($string) {
        $lixo = array(
            '_edit_last',
            '_yoast_wpseo_focuskw',
            '_yoast_wpseo_linkdex',
            '_yoast_wpseo_title',
            '_edit_lock',
            '_wp_old_slug'
        );

        foreach ($lixo as $key => $value) {
            if($value === $string) {
                return true;
            }
        }

        return false;
    }

    protected function checkImg($url) {
        return str_replace("/plugin", "", $url);
    }
}