<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Library\Page,
        Goteo\Library\Mail,
        Goteo\Library\Text;

    class Review extends \Goteo\Core\Controller {

        /*
         *  Muy guarro para poder moverse mientras desarrollamos
         */
        public function index ($section = null) {

            $page = Page::get('review');

            $message = \str_replace('%USER_NAME%', $_SESSION['user']->name, $page->content);

            return new View (
                'view/review/index.html.php',
                array(
                    'message' => $message,
                    'menu'    => self::menu()
                )
            );

        }

        /*
         * Sección, Mi actividad
         * Opciones:
         *      'projects' los proyectos que tengo actualmente asignados para revisar
         * 
         */
        public function activity ($option = 'summary', $action = 'view') {

            $user = $_SESSION['user'];

            $projects = Model\Review::assigned($user->id);
            // resumen de los proyectos que tengo actualmente asignados


            return new View (
                'view/review/index.html.php',
                array(
                    'menu'    => self::menu(),
                    'message' => $message,
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action,
                    'reviews'=> $reviews,
                    'status'  => $status,
                    'errors'  => $errors,
                    'success' => $success
                )
            );

        }

        /*
         * Seccion, Mis proyectos asignados
         * Opciones:
         *      'summary' resumen de la revision del proyecto de trabajo: comentarios de administrador para esta revision y enlaces del proyecto
         *      'evaluate' marcar los puntos de criterios y comentarios de evaluacion y mejoras
         *                  para cada seccion de criterios
         *      'comments' resumen de comentarios de todos los revisores
         *
         */
        public function projects ($option = 'summary', $action = 'list', $id = null) {
            
            $user    = $_SESSION['user'];

            $errors = array();

            if ($action == 'select' && !empty($_POST['project'])) {
                // otro proyecto de trabajo
                $project = Model\Project::getMini($_POST['project']);
            } else {
                // si tenemos ya proyecto, mantener los datos actualizados
                if (!empty($_SESSION['project']->id)) {
                    $project = Model\Project::getMini($_SESSION['project']->id);
                }
            }

            $projects = Model\Review::assigned($user->id);

            // si no hay proyectos asignados no tendria que estar aqui
            if (count($projects) == 0) {
                $errors[] = 'No tienes asignada ninguna revisión de proyectos';
                $action = 'list';
            }
            
            if (empty($project)) {
                $project = $projects[0];
            }

            // aqui necesito tener un proyecto de trabajo,
            // si no hay ninguno ccoge el último
            if (empty($_SESSION['project'])) {
                 $_SESSION['project'] = $project;
            }

            // tenemos proyecto de trabajo, comprobar si el proyecto esta en estado de revision
            if ($project->status != 2) {
                $errors[] = 'este proyecto no esta en revision';
                //Text::get('review-project-review-wrongstatus');
                $action = 'list';
            } else {
                // cargamos la instancia de revision 
                $review = Model\Review::get($project->id);
                if (!$review instanceof Model\Review) {
                    // si no hay es que el admin no la ha inicializado
                    $errors[] = 'No se ha encontrado ninguna revision para este proyecto';
                    //Text::get('review-project-review-fail');
                    $option = 'summary';
                    $action = 'list';
                } elseif (!$review->active) {
                    $errors[] = 'Lo sentimos, la revision para este proyecto esta desactivada';
                    //Text::get('review-project-review-inactive');
                    $action = 'list';
                }

            }

            // view data basico para esta seccion
            $viewData = array(
                    'menu'    => self::menu(),
                    'message' => $message,
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action,
                    'project'=> $project,
                    'review'=> $review,
                    'errors'  => $errors,
                    'success' => $success
                );

            return new View ('view/review/index.html.php', $viewData);
        }

        /*
         * Seccion, Mi historial
         * Opciones:
         *      'summary' resumen de los revisados anteriormente
         *
         */
        public function history ($option = 'summary', $action = 'view') {

            // tratamos el post segun la opcion y la acion

            // sacamos las revisiones realizadas

            $reviews = array();

            $viewData = array(
                    'menu'    => self::menu(),
                    'message' => $message,
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action,
                    'errors'  => $errors,
                    'success' => $success,
                    'reviews'    => $reviews
                );

            return new View (
                'view/review/index.html.php',
                $viewData
            );
        }


        private static function menu() {
            // todos los textos del menu review
            $menu = array(
                'activity' => array(
                    'label'   => 'Mi actividad',
                    'options' => array (
                        'summary' => 'Resumen'
                    )
                ),
                'projects' => array(
                    'label' => 'Mis proyectos',
                    'options' => array (
                        'summary'  => 'Resumen',
                        'evaluate' => 'Puntuar',
                        'report'   => 'Informe'
                    )
                ),
                'history' => array(
                    'label'   => 'Mi historial',
                    'options' => array (
                        'summary'  => 'Resumen'
                    )
                )
            );

            return $menu;

        }



        }

}