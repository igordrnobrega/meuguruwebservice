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
        $return = array(
            'eventos'     => array(),
            'segmentos' => array(),
        );
        $eventos = array();

        $sql = 'select evento.ID, evento.post_title, evento.post_content, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'where detalhes.meta_value != "" ' .
            'and evento.post_status = "publish" ' .
            'and evento.post_type = "feiras" ';

        $orderSql = 'select evento.ID ' .
            'from imp_posts evento ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'where detalhes.meta_value != "" ' .
            'and evento.post_status = "publish" ' .
            'and detalhes.meta_key = "dataInicial" ' .
            'and evento.post_type = "feiras" ' .
            'order by STR_TO_DATE(detalhes.meta_value, "%d/%m/%Y") desc';

        try {
            $sqlResult      = $app['db']->fetchAll($sql);
            $sqlResultOrder = $app['db']->fetchAll($orderSql);

            $return['eventos'] = array(
                '42200'
            );

            foreach ($sqlResultOrder as $key => $value) {
                array_push($return['eventos'], $value['ID']);
            }

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    $value['guid'] = $this->checkImg($value['guid']);
                    if(!in_array($value['name'], $return['segmentos'], true)){
                        array_push($return['segmentos'], $value['name']);
                    }
                    array_push($eventos, $value);
                } else if($value['ID'] == $id){
                    if(!$this->removeLixoWp($value['meta_key'])) {
                        $eventos[$count][$value['meta_key']] = $value['meta_value'];
                    }
                } else {
                    $count++;
                    $id = $value['ID'];
                    if(!in_array($value['name'], $return['segmentos'], true)){
                        array_push($return['segmentos'], $value['name']);
                    }
                    $value['guid'] = $this->checkImg($value['guid']);
                    array_push($eventos, $value);
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
        sort($return['segmentos']);

        foreach ($return['eventos'] as $keyO => $valueO) {
            foreach ($eventos as $key => $value) {
                if($valueO == $value['ID']) {
                    $return['eventos'][$keyO] = $value;
                }
            }
        }

        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($return), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);

    }

    // PARAMETROS sem
    public function getFornecedoresAction(Request $request, Application $app) {
        $return = array(
            'fornecedores'  => array(),
            'segmentos'     => array()
        );

        $sql = 'select evento.ID, evento.post_title, evento.post_content, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'where detalhes.meta_value != "" ' .
            'and evento.post_status = "publish" ' .
            'and itt.taxonomy = "fornecedores" ';

        $sqlAnunciantes = 'select evento.ID ' .
            'from imp_posts evento ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'where detalhes.meta_key = "anuncianteFornecedores" ' .
            'and detalhes.meta_value = "Sim"';

        try {
            $sqlResult      = $app['db']->fetchAll($sql);
            $sqlResultAnun  = $app['db']->fetchAll($sqlAnunciantes);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    array_push($return['fornecedores'], $value);
                } else if($value['ID'] == $id){
                    $return['fornecedores'][$count][$value['meta_key']] = $value['meta_value'];
                } else {
                    $count++;
                    $id = $value['ID'];
                    if(!in_array($value['name'], $return['segmentos'], true)){
                        array_push($return['segmentos'], $value['name']);
                    }
                    array_push($return['fornecedores'], $value);
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
        sort($return['segmentos']);


        foreach ($sqlResultAnun as $key => $value) {
            echo array_search($value, $return['fornecedores']);
        }

        die;

        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($return), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }

    // PARAMETROS sem
    public function getLocaisAction(Request $request, Application $app) {
        $return = array(
            'locais'    => array(),
            'segmentos' => array()
        );

        $sql = 'select evento.ID, evento.post_title, evento.post_content, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'and detalhes.meta_value != "" ' .
            'and evento.post_status = "publish" ' .
            'and itt.taxonomy = "pavilhao" ';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    $value['guid'] = $this->checkImg($value['guid']);
                    if(!in_array($value['name'], $return['segmentos'], true)){
                        array_push($return['segmentos'], $value['name']);
                    }
                    array_push($return['locais'], $value);
                } else if($value['ID'] == $id){
                    $return['locais'][$count][$value['meta_key']] = $value['meta_value'];
                } else {
                    $count++;
                    $id = $value['ID'];
                    $value['guid'] = $this->checkImg($value['guid']);
                    if(!in_array($value['name'], $return['segmentos'], true)){
                        array_push($return['segmentos'], $value['name']);
                    }
                    array_push($return['locais'], $value);
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
        sort($return['segmentos']);

        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($return), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }

    // PARAMETROS sem
    public function getProdutosAction(Request $request, Application $app) {
        $return = array(
            'produtos'  => array(),
            'segmentos' => array()
        );

        $sql = 'select evento.ID, evento.post_title, evento.post_content, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'and detalhes.meta_value != "" ' .
            'and evento.post_status = "publish" ' .
            'and segmento.name not like "Destaque%" ' .
            'and itt.taxonomy = "produtos" ';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    $value['guid'] = $this->checkImg($value['guid']);
                    if(!in_array($value['name'], $return['segmentos'], true)){
                        array_push($return['segmentos'], $value['name']);
                    }
                    array_push($return['produtos'], $value);
                } else if($value['ID'] == $id){
                    if($value['meta_key'] == 'NomedaLoja') {
                        $return['produtos'][$count][$value['meta_key']] = $this->getPost($value['meta_value'], 'post_title', $app);
                        $postMetas = $this->getPostMeta($value['meta_value'], $app);
                        foreach ($postMetas as $keyPM => $valuePM) {
                            if(!$this->removeLixoWp($valuePM['meta_key'])) {
                                $return['produtos'][$count]['loja'][$valuePM['meta_key']] = $valuePM['meta_value'];
                            }
                        }
                    } else {
                        $return['produtos'][$count][$value['meta_key']] = $value['meta_value'];
                    }
                } else {
                    $count++;
                    $id = $value['ID'];
                    $value['guid'] = $this->checkImg($value['guid']);
                    if(!in_array($value['name'], $return['segmentos'], true)){
                        array_push($return['segmentos'], $value['name']);
                    }
                    array_push($return['produtos'], $value);
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
        sort($return['segmentos']);
        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($return), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }

    // PARAMETROS sem
    public function getServicosAction(Request $request, Application $app) {
        $return = array(
            'servicos'  => array(),
            'segmentos' => array()
        );

        $sql = 'select evento.ID, evento.post_title, evento.post_content, imagem.guid, segmento.name, detalhes.meta_key, detalhes.meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'where detalhes.meta_value != "" ' .
            'and evento.post_status = "publish" ' .
            'and evento.post_type = "servicos" ';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    $value['guid'] = $this->checkImg($value['guid']);
                    if(!in_array($value['name'], $return['segmentos'], true)){
                        array_push($return['segmentos'], $value['name']);
                    }
                    array_push($return['servicos'], $value);
                } else if($value['ID'] == $id){
                    $return['servicos'][$count][$value['meta_key']] = $value['meta_value'];
                } else {
                    $count++;
                    $id = $value['ID'];
                    $value['guid'] = $this->checkImg($value['guid']);
                    if(!in_array($value['name'], $return['segmentos'], true)){
                        array_push($return['segmentos'], $value['name']);
                    }
                    array_push($return['servicos'], $value);
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
        sort($return['segmentos']);

        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($return), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }

    // PARAMETROS sem
    public function getNoticiasAction(Request $request, Application $app) {
        $return = array(
            'noticias'  => array(),
            'segmentos' => array()
        );

        $sql = 'select evento.ID, evento.post_title, evento.post_content, evento.post_date, imagem.guid, segmento.name, GROUP_CONCAT(DISTINCT detalhes.meta_key SEPARATOR "/-/") as meta_key, GROUP_CONCAT(DISTINCT detalhes.meta_value SEPARATOR "/-/") as meta_value ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'where detalhes.meta_value != "" ' .
            'and evento.post_status = "publish" ' .
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

                if(!in_array($value['name'], $return['segmentos'], true)){
                    array_push($return['segmentos'], $value['name']);
                }

                if(sizeof($meta_key) == sizeof($meta_value)) {
                    foreach ($meta_key as $keyM => $valueM) {
                        $sqlResult[$key][$valueM] = $meta_value[$keyM];
                    }
                }
                unset($sqlResult[$key]['$meta_key']);
                unset($sqlResult[$key]['$meta_value']);
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
        sort($return['segmentos']);
        $return['noticias'] = $sqlResult;

        // Useful to return the newly added details
        // HTTP_CREATED = 200

        return new Response(json_encode($return), 200, ['Content-Type' => 'application/json', 'Access-Control-Allow-Origin' => '*']);
    }

    // PARAMETROS sem
    public function getEstandesAction(Request $request, Application $app) {
        $return = array(
            'estandes'  => array(),
            'segmentos' => array(),
            'posicoes'  => array()
        );

        $sql = 'select evento.ID, evento.post_title, evento.post_content, imagem.guid, detalhes.meta_key, detalhes.meta_value, segmento.name, itt.taxonomy ' .
            'from imp_posts evento ' .
            'inner join imp_posts imagem on evento.ID = imagem.post_parent ' .
            'inner join imp_term_relationships itr on evento.ID = itr.object_id ' .
            'inner join imp_term_taxonomy itt on itr.term_taxonomy_id = itt.term_taxonomy_id ' .
            'inner join imp_terms segmento on segmento.term_id = itt.term_id ' .
            'inner join imp_postmeta detalhes on detalhes.post_id = evento.ID ' .
            'where detalhes.meta_value != "" ' .
            'and evento.post_status = "publish" ' .
            'and segmento.name not like "Destaque%" ' .
            'and evento.post_type = "projetos" ';

        try {
            $sqlResult = $app['db']->fetchAll($sql);

            $count = 0;
            $id = 0;
            foreach ($sqlResult as $key => $value) {
                if($id === 0) {
                    $id = $value['ID'];
                    $value['guid'] = $this->checkImg($value['guid']);
                    array_push($return['estandes'], $value);
                } else if($value['ID'] == $id){
                    if($value['taxonomy'] == 'tipos_projetos') {
                        $return['estandes'][$count]['posicao'] = $value['name'];
                        if(!in_array($value['name'], $return['posicoes'], true)){
                            array_push($return['posicoes'], $value['name']);
                        }
                    } else if($value['taxonomy'] == 'categorias_projetos'){
                        $return['estandes'][$count]['categoria'] = $value['name'];
                        if(!in_array($value['name'], $return['segmentos'], true)){
                            array_push($return['segmentos'], $value['name']);
                        }
                    }
                    if($value['meta_value'] != '') {
                        $return['estandes'][$count][$value['meta_key']] = $value['meta_value'];
                    }
                } else {
                    $count++;
                    $id = $value['ID'];
                    $value['guid'] = $this->checkImg($value['guid']);
                    array_push($return['estandes'], $value);
                }
            }

        } catch (\PDOException $e) {
            return $e->getMessage();
        }
        sort($return['segmentos']);
        sort($return['posicoes']);

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

    protected function checkImg($url) {
        return str_replace('/plugin', '', $url);

    }
}