<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model\Node,
        Goteo\Model\Home,
        Goteo\Model\Project,
        Goteo\Model\Banner,
        Goteo\Model\Call,
        Goteo\Model\Post,
        Goteo\Model\Promote,
        Goteo\Model\User,
        Goteo\Library\Text,
        Goteo\Library\Feed;

    class Index extends \Goteo\Core\Controller {
        
        public function index () {

            if (NODE_ID != GOTEO_NODE) {
                return $this->node_index();
            }

            if (isset($_GET['error'])) {
                throw new \Goteo\Core\Error('418', Text::html('fatal-error-teapot'));
            }

            // orden de los elementos en portada
            $order = Home::getAll();

            // entradas de blog
            if (isset($order['posts'])) {
                // entradas en portada
                $posts     = Post::getAll();

                foreach ($posts as $id=>$title) {
                    $posts[$id] = Post::get($id);
                }
            }

            // Proyectos destacados
            if (isset($order['promotes'])) {
                $promotes  = Promote::getAll(true);

                foreach ($promotes as $key => &$promo) {
                    try {
                        $promo->projectData = Project::getMedium($promo->project, LANG);
                    } catch (\Goteo\Core\Error $e) {
                        unset($promotes[$key]);
                    }
                }
            }

            // capital riego
            if (isset($order['drops'])) {
                $calls     = Call::getActive(3); // convocatorias en modalidad 1; inscripcion de proyectos
                $campaigns = Call::getActive(4); // convocatorias en modalidad 2; repartiendo capital riego
                
                $drops = (!empty($calls) || !empty($campaigns)) ? true : false;
            }

            // padrinos
            if (isset($order['patrons'])) {
                $patrons  =  User::getVips(true);

                foreach ($patrons as $userId => $user) {
                    try {
                        $patrons[$userId] = User::getMini($userId);
                    } catch (\Goteo\Core\Error $e) {
                        unset($patrons[$key]);
                    }
                }
            }

            // actividad reciente
            if (isset($order['feed'])) {
                $feed = array();

                $feed['goteo']     = Feed::getAll('goteo', 'public', 15);
                $feed['projects']  = Feed::getAll('projects', 'public', 15);
                $feed['community'] = Feed::getAll('community', 'public', 15);
            }
            
            // Banners siempre
            $banners   = Banner::getAll();

            foreach ($banners as $id => &$banner) {
                try {
                    $banner->project = Project::get($banner->project, LANG);
                } catch (\Goteo\Core\Error $e) {
                    unset($banners[$id]);
                }
            }

            return new View('view/index.html.php',
                array(
                    'banners'   => $banners,
                    'posts'     => $posts,
                    'promotes'  => $promotes,
                    'calls'     => $calls,
                    'campaigns' => $campaigns,
                    'feed'      => $feed,
                    'drops'     => $drops,
                    'patrons'   => $patrons,
                    'order'     => $order
                )
            );
            
        }

        public function node_index () {
            
            $node = Node::get(NODE_ID);

            // orden de los elementos en portada
            $order = Home::getAll(NODE_ID);

            // entradas de blog
            if (isset($order['posts'])) {
                // entradas en portada
                $posts     = Post::getList('home', NODE_ID);

                foreach ($posts as $id=>$title) {
                    $posts[$id] = Post::get($id);
                }
            }

            // Proyectos destacados
            if (isset($order['promotes'])) {
                $promotes  = Promote::getAll(true, NODE_ID);

                foreach ($promotes as $key => &$promo) {
                    try {
                        $promo->projectData = Project::getMedium($promo->project, LANG);
                    } catch (\Goteo\Core\Error $e) {
                        unset($promotes[$key]);
                    }
                }
            }

            return new View('view/node/index.html.php',
                array(
                    'node'   => $node,
                    'posts'     => $posts,
                    'promotes'  => $promotes,
                    'order'     => $order                )
            );

        }

    }
    
}