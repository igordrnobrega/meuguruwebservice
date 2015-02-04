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

    public function eventSearchAction(Request $request, Application $app) {
        $parameters = $request->query->all();

        $sql = "SELECT * FROM portal_term_taxonomy;";

        try {
            $sqlResult = $app['db']->fetchAll($sql);
        } catch (\PDOException $e) {
            return 'error';
        }

        return json_encode($sqlResult);
    }

    // public function signinAction(Request $request, Application $app)
    // {
    //     $request->getSession()->start();
    //     // $app['session']->set('expire', time() + 60);

    //     $this->cpf  = preg_replace('/[^0-9]/', '', $app->escape($request->get('nu_cpf')));
    //     $nome       = $app->escape($request->get('tx_nome'));
    //     $tel        = preg_replace('/[^0-9]/', '', $app->escape($request->get('tx_telefone')));
    //     $email      = $app->escape($request->get('tx_email'));

    //     if(
    //         !$nome
    //         || !$email
    //         || !$this->cpf
    //         || (strlen($this->cpf) < 11)
    //         || (strlen($nome) < 4)
    //         || (strlen($email) < 6)
    //     ) {
    //         return false;
    //     }

    //     if($tel == null) {
    //         $sql = "INSERT INTO participante (nu_cpf, tx_nome, tx_email) VALUES ('" . $this->cpf . "', '" . $nome . "', '" . $email . "');";
    //     } else {
    //         $sql = "INSERT INTO participante (nu_cpf, tx_nome, tx_telefone, tx_email) VALUES ('" . $this->cpf . "', '" . $nome . "', '" . $tel . "', '" . $email . "');";
    //     }

    //     $app['session']->set('start', time());
    //     $app['session']->set('expire', time() + (4 * 3600));
    //     $app['session']->set('user', $this->cpf);

    //     $this->postFB();

    //     try {
    //         $app['db']->exec($sql);
    //         $resultGetEtapa = $this->getEtapa($app, 1);
    //         $this->setEtapaParticipante($app, $resultGetEtapa['id_etapa']);
    //         $sql = "update etapa_participante set fg_acerto = 1 where nu_cpf ='" . $app['session']->get('user') . "'";
    //         $app['db']->exec($sql);
    //     } catch (\PDOException $e) {
    //         return $this->etapaAction($request, $app);
    //     }

    //     return $app['twig']->render('mobile/passos.html', array(
    //             'response'  => $resultGetEtapa,
    //             'etapa'     => $app['session']->get('etapa'),
    //             'session'   => $app['session']->get('user')
    //         )
    //     );
    // }

    // public function etapaAction(Request $request, Application $app) {
    //     if ($app["mobile_detect"]->isMobile() || APPLICATION_ENV === 'development') {
    //         if($app['session']->get('expire') < time() || $app['session'] == null) {
    //             $this->connectFB();
    //             $this->postFB();
    //             return $app['twig']->render('mobile/login.html');
    //         } else {
    //             $this->cpf            = $app['session']->get('user');
    //             $sqlEtapaParticipante = $this->getEtapaParticipante($app);

    //             if ($sqlEtapaParticipante) {
    //                 if(
    //                     (int) $sqlEtapaParticipante['nu_numero_etapa'] === 6
    //                     && (int) $sqlEtapaParticipante['fg_acerto'] === 1
    //                 ) {
    //                     return $app['twig']->render('mobile/last.html', array(
    //                         'etapa' => $sqlEtapaParticipante['nu_numero_etapa']
    //                     ));
    //                 }
    //             }

    //             if($app['session']->get('hash') == $app->escape($request->get('hash'))) {
    //                 $this->codigoEtapa  = $app->escape($request->get('hash'));

    //                 return $this->retornoAction($app);
    //             }

    //             return $this->getEtapaDica($app);
    //         }
    //     }

    //     return $app['twig']->render('desktop/index.html');
    // }

    // public function retornoAction(Application $app){
    //     $this->cpf            = $app['session']->get('user');
    //     $sqlEtapaParticipante = $this->getEtapaParticipante($app);
    //     $resultGetEtapa;
    //     if ($sqlEtapaParticipante != 0 || $sqlEtapaParticipante != null) {
    //         if($sqlEtapaParticipante['nu_numero_etapa'] == 6 && $sqlEtapaParticipante['fg_acerto'] == 1){
    //             return $app['twig']->render('mobile/last.html', array(
    //                     'etapa'     => $sqlEtapaParticipante['nu_numero_etapa']
    //                 )
    //             );
    //         }
    //         if($sqlEtapaParticipante['fg_acerto'] == 1){
    //             $resultGetEtapa = $this->getEtapa($app, ($sqlEtapaParticipante['nu_numero_etapa'] + 1) );
    //             $this->setEtapaParticipante($app, $resultGetEtapa['id_etapa']);
    //         }else{
    //             $resultGetEtapa = $this->getEtapa($app, $sqlEtapaParticipante['nu_numero_etapa'] );
    //         }
    //     }

    //     return $app['twig']->render('mobile/passos.html', array(
    //             'session'   => $app['session']->get('user'),
    //             'etapa'     => $app['session']->get('etapa'),
    //             'response'  => $resultGetEtapa
    //     ));
    // }

    // public function respostaAction(Request $request, Application $app) {
    //     $resposta = $app->escape($request->get('tx_resposta'));
    //     $resposta = strtolower($resposta);
    //     $search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
    //     $replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
    //     $resposta = str_replace($search, $replace, $resposta);
    //     if($resposta == $app['session']->get('resposta')){
    //         $sql = "update etapa_participante set fg_acerto = 1 where nu_cpf ='" . $app['session']->get('user') . "'";
    //         $app['db']->exec($sql);
    //         $this->getEtapa($app, ($app['session']->get('etapa') + 1));
    //         return $app->json(true);
    //     }else{
    //         return $app->json(false);
    //     }
    // }

    // public function getEtapaDica($app) {
    //     $etapa = ($app['session']->get('etapa') - 1)
    //         ? ($app['session']->get('etapa') - 1)
    //         : 1;

    //     $resultGetEtapa = $this->getEtapaError($app, $etapa);
    //     $pieces = explode(':', $resultGetEtapa['tx_dica']);

    //     if($resultGetEtapa['id_etapa'] === 1) {
    //         $pieces[1] = 'Pronto? Então vamos começar! A primeira dica é: “Você vai precisar de muita energia para essa caçada. Já se alimentou direitinho?';
    //     }

    //     return $app['twig']->render('mobile/error.html', array(
    //             'etapa'     => $resultGetEtapa['id_etapa'],
    //             'dica'      => $pieces[1]
    //         )
    //     );
    // }

    // public function setEtapaParticipante(Application $app, $idEtapa) {
    //     if ($idEtapa == 1) {
    //         $sql = "INSERT INTO etapa_participante (id_etapa, nu_cpf) VALUES ('" . $idEtapa . "', '" . $this->cpf . "');";
    //     }else{
    //         $sql = "UPDATE etapa_participante id_etapa set id_etapa =" . $idEtapa . ", fg_acerto = 0 where nu_cpf = '" . $this->cpf . "';";
    //     }
    //     $app['db']->exec($sql);
    // }

    // public function getEtapa(Application $app, $nuEtapa) {
    //     if($this->codigoEtapa != null){
    //         $sql = "select * from etapa where tx_codigo = '" . $this->codigoEtapa . "'";
    //     }else{
    //         $sql = "select * from etapa where nu_numero_etapa = '" . $nuEtapa . "'";
    //     }
    //     $sqlResult = $app['db']->fetchAssoc($sql);
    //     $app['session']->set('etapa', $sqlResult['id_etapa']);
    //     $app['session']->set('resposta', $sqlResult['tx_resposta']);
    //     $app['session']->set('dica', $sqlResult['tx_dica']);
    //     if($nuEtapa == 1){
    //         $app['session']->set('hash', 'c4ca4238a0');
    //     }else{
    //         $app['session']->set('hash', $sqlResult['tx_codigo']);
    //     }

    //     return $sqlResult;
    // }

    // public function getEtapaError(Application $app, $nuEtapa) {

    //     $sql = "select * from etapa where nu_numero_etapa = '" . $nuEtapa . "'";

    //     $sqlResult = $app['db']->fetchAssoc($sql);

    //     return $sqlResult;
    // }

    // public function getEtapaParticipante(Application $app){
    //     $sql = "select * from etapa_participante ep inner join etapa e on ep.id_etapa = e.id_etapa where nu_cpf = '" . $this->cpf . "'";
    //     $sqlResult = $app['db']->fetchAssoc($sql);

    //     return $sqlResult;
    // }

    // public function getParticipante(Application $app)
    // {
    //     $sql = "select * from participante where nu_cpf = '" . $this->cpf . "'";
    //     $sqlResult = $app['db']->fetchAssoc($sql);

    //     return $sqlResult;
    // }

    // public function connectFB() {
    //     $facebook = new \Facebook($this->config);

    //     $fbUser = $facebook->getUser();

    //     // PRODUCAO
    //     $location = $facebook->getLoginUrl( array(
    //                    'scope' => 'publish_actions',
    //                    'redirect_uri' => 'http://pascoaemconjunto.com.br/jogar'
    //                    ));

    //     // DESENVOLVIMENTO
    //     // $location = $facebook->getLoginUrl( array(
    //     //                'scope' => 'publish_actions',
    //     //                'redirect_uri' => 'http://dev.cnbpascoa.fermento.com.br/jogar'
    //     //                ));

    //     if ($fbUser) {
    //         try {
    //             $fb_user_profile = $facebook->api('/me');
    //             $permissions = $facebook->api("/me/permissions");
    //             if(!array_key_exists('publish_stream', $permissions['data'][0])) {
    //             header( "Location: " . $facebook->getLoginUrl(array("scope" => "publish_stream")) );
    //             exit;
    //     }

    //         } catch (FacebookApiException $e) {
    //             $fbUser = null;
    //             print '<script language="javascript" type="text/javascript"> top.location.href="'. $location .'"; </script>';
    //             die();
    //         }

    //     } else {
    //         print '<script language="javascript" type="text/javascript"> top.location.href="'. $location .'"; </script>';
    //         die();
    //     }
    // }

    // public function getNameFB(){
    //     $facebook = new \Facebook($this->config);

    //     $fb_user_profile = $facebook->api('/me');

    //     return $fb_user_profile['name'];
    // }

    // public function postFB(){

    //     $facebook = new \Facebook($this->config);
    //     $fbUser = $facebook->getUser();

    //     $facebook->api("/me/feed", "post", $this->facebook);

    // }
}