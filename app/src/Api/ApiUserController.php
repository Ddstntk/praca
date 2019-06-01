<?php
/**
 * PHP Version 5.6
 * User controller.
 *
 * @category  Social_Network
 *
 * @author    Konrad Szewczuk <konrad3szewczuk@gmail.com>
 *
 * @copyright 2018 Konrad Szewczuk
 *
 * @license   https://opensource.org/licenses/MIT MIT license
 *
 * @link      cis.wzks.uj.edu.pl/~16_szewczuk
 */
namespace Api;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Repository\UserRepository;
use Repository\FriendsRepository;
use Form\PswdType;
use Form\SignupType;
use Form\EditType;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserController
 *
 * @category  Social_Network
 *
 * @author    Konrad Szewczuk <konrad3szewczuk@gmail.com>
 *
 * @copyright 2018 Konrad Szewczuk
 *
 * @license   https://opensource.org/licenses/MIT MIT license
 *
 * @link      cis.wzks.uj.edu.pl/~16_szewczuk
 */
class ApiUserController implements ControllerProviderInterface
{
    /**
     * Routing settings
     *
     * @param Application $app Application
     *
     * @return mixed|\Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/id', [$this, 'idAction']);
        $controller->get('/profile', [$this, 'profileAction']);
        $controller->get('/view/{id}', [$this, 'viewAction']);
        $controller->get('/config', [$this, 'dashboardAction']);
        $controller->match('/config/set', [$this, 'dashboardSetAction'])
        ->method('GET|POST');
        $controller->match('/index', [$this, 'indexAction'])
        ->method('GET|POST');
        $controller->match('/edit', [$this, 'editAction'])
            ->method('GET|POST');
        $controller->match('/password', [$this, 'changePassword'])
            ->method('GET|POST');
        return $controller;
    }

    /**
     * Id action
     *
     * @param Application $app  Application
     * @param int         $page Page
     *
     * @return mixed
     */
    public function idAction(Application $app, $page = 1)
    {
        $userRepository = new UserRepository($app['db']);
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        $response = new JsonResponse(array('userId' => $userId));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    /**
     * Index action
     *
     * @param Application $app  Application
     * @param int         $page Page
     *
     * @return mixed
     */
    public function indexAction(Application $app, Request $request, $page = 1)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }
        $filter = $request->request->get('filter');
        $userRepository = new UserRepository($app['db']);
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();
        $page = $request->request->get('page');
        ;
        $usersPaginated = $userRepository->findAllPaginated($friendsRepository, $userId, $page);
//        var_dump($usersPaginated);
        $result = [];

        if($filter){
            foreach ($usersPaginated['data'] as $rkey => $user){
//                var_dump($filter);
//
//                var_dump($user['name']);
//                var_dump((strpos($user['name'], $filter) !== false));
//                var_dump($user['surname']);
//                var_dump((strpos($user['surname'], $filter) !== false));
                if ((stripos($user['name'], $filter) !== false) || (stripos($user['surname'], $filter) !== false)){
                    $result['data'][] = $user;
//                    var_dump("Wchodzę bo prawda");
                }
            }
        } else {
            $result = $usersPaginated;
        }

//        var_dump($result);
        $response = new JsonResponse(array('usersIndexed' => $result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

//        return $app['twig']->render(
//            'user/index.html.twig',
//            ['paginator' => $userRepository->findAllPaginated($friendsRepository, $userId, $page)]
//        );
    }

    /**
     * View action
     *
     * @param Application $app Application
     * @param User        $id  Id
     *
     * @return mixed
     */
    public function viewAction(Application $app, $id)
    {
        $userRepository = new UserRepository($app['db']);

        $response = new JsonResponse(array('userView' => $userRepository->getUserById($id)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

//        return $app['twig']->render(
//            'user/view.html.twig',
//            ['user' => $userRepository->getUserById($id)]
//        );
    }

    /**
     * Dashboard action
     *
     * @param Application $app Application
     *
     * @return mixed
     */
    public function dashboardAction(Application $app)
    {
        $userRepository = new UserRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        $response = new JsonResponse(array('userDashboard' => $userRepository->getDashboardConfig($userId)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Dashboard set action
     *
     * @param Application $app  Application
     * @param int         $page Page
     *
     * @return mixed
     */
    public function dashboardSetAction(Application $app, Request $request)
    {
        $userRepository = new UserRepository($app['db']);

        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        $body = $request->request->get('body');
        $data = json_encode($body);


        $userRepository->setDashboardConfig($userId, $data);

        $response = 1;
        return $response;
    }

    /**
     * Profile action
     *
     * @param Application $app Application
     *
     * @return mixed
     */
    public function profileAction(Application $app)
    {
        $userRepository = new UserRepository($app['db']);

        $id = $app['security.token_storage']->getToken()->getUser()->getID();
//        var_dump($id);

        $response = new JsonResponse(array('userView' => $userRepository->getUserById($id)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
//
//        return $app['twig']->render(
//            'user/view.html.twig',
//            ['user' => $userRepository->getUserById($id)]
//        );
    }
//
//    /**
//     * Edit action
//     *
//     * @param Application $app     Application
//     * @param Request     $request HttpRequest
//     *
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse
//     *
//     * @throws \Doctrine\DBAL\ConnectionException
//     * @throws \Doctrine\DBAL\DBALException
//     */
//    public function editAction(Application $app, Request $request)
//    {
//        $user = [];
//        $userTmp = [];
//        $id = $app['security.token_storage']->getToken()->getUser()->getID();
//        $userRepository = new UserRepository($app['db']);
//        $userTmp = $userRepository->getUserById($id);
//        $form = $app['form.factory']->createBuilder(
//            EditType::class,
//            $user,
//            [   'placeholders' => $userTmp,
//                'user_repository' => new UserRepository($app['db']), ]
//        )->getForm();
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid() && !empty($form)) {
//            $userTmp = $form->getData();
//
//
//            foreach ($userTmp as $key => $value) {
//                if (isset($value)) {
//                    $user[$key] = $value;
//                }
//            }
//
//            if (sizeof($user)) {
//                $user['PK_idUsers'] = $id;
//                $userRepository->save($user);
//
//                $app['session']->getFlashBag()->add(
//                    'messages',
//                    [
//                        'type' => 'success',
//                        'message' => 'message.account_successfully_edited',
//                    ]
//                );
//            }
//
//            return $app->redirect(
//                $app['url_generator']
//                ->generate('user_profile'),
//                301
//            );
//        }
//
//
//        return $app['twig']->render(
//            'user/edit.html.twig',
//            array('form' => $form->createView())
//        );
//    }

    /**
     * Change password
     *
     * @param Application $app     Application
     * @param Request     $request HttpRequest
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function changePassword(Application $app, Request $request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }
//        var_dump($request);

//        var_dump($request->request->get('password'));


        $user = [];
        $userTmp = [];
        $id = $app['security.token_storage']->getToken()->getUser()->getID();
        $userRepository = new UserRepository($app['db']);

//        $form = $app['form.factory']->createBuilder(
//            PswdType::class,
//            $user,
//            ['user_repository' => new UserRepository($app['db'])]
//        )->getForm();
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid() && !empty($form)) {
//            $user = $form->getData();

            $password = $request->request->get('password');
            $user['password'] = $app['security.encoder.bcrypt']
                                ->encodePassword($password, '');
            $user['PK_idUsers'] = $id;
//            var_dump($app['security.encoder.bcrypt']
//                ->encodePassword($password, ''));
            if (sizeof($user)) {
                $userRepository->save($user);

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.account_successfully_edited',
                    ]
                );
//            }

//            var_dump($user);
        }



//        return $app['twig']->render(
//            'user/pswd.html.twig',
//            array('form' => $form->createView())
//        );

        return http_response_code(200);
    }
}
