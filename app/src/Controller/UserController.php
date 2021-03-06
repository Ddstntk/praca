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
namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
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
class UserController implements ControllerProviderInterface
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
        $controller->get('/profile', [$this, 'profileAction'])
            ->bind('user_profile');
        $controller->get('/view/{id}', [$this, 'viewAction'])
            ->bind('user_view');
        $controller->get('/index', [$this, 'indexAction'])
            ->bind('users_index_paginated');
        $controller->match('/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->bind('user_edit');
        $controller->match('/password', [$this, 'changePassword'])
            ->method('GET|POST')
            ->bind('password_change');

        return $controller;
    }

    /**
     * Index action
     *
     * @param Application $app  Application
     * @param int         $page Page
     *
     * @return mixed
     */
    public function indexAction(Application $app, $page = 1)
    {
        $userRepository = new UserRepository($app['db']);
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        return $app['twig']->render(
            'user/index.html.twig',
            ['paginator' => $userRepository->findAllPaginated($friendsRepository, $userId, $page)]
        );
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

        return $app['twig']->render(
            'user/view.html.twig',
            ['user' => $userRepository->getUserById($id)]
        );
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
        var_dump($id);

        return $app['twig']->render(
            'user/view.html.twig',
            ['user' => $userRepository->getUserById($id)]
        );
    }

    /**
     * Edit action
     *
     * @param Application $app     Application
     * @param Request     $request HttpRequest
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function editAction(Application $app, Request $request)
    {
        $user = [];
        $userTmp = [];
        $id = $app['security.token_storage']->getToken()->getUser()->getID();
        $userRepository = new UserRepository($app['db']);
        $userTmp = $userRepository->getUserById($id);
        $form = $app['form.factory']->createBuilder(
            EditType::class,
            $user,
            [   'placeholders' => $userTmp,
                'user_repository' => new UserRepository($app['db']), ]
        )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !empty($form)) {
            $userTmp = $form->getData();


            foreach ($userTmp as $key => $value) {
                if (isset($value)) {
                    $user[$key] = $value;
                }
            }

            if (sizeof($user)) {
                $user['PK_idUsers'] = $id;
                $userRepository->save($user);

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.account_successfully_edited',
                    ]
                );
            }

            return $app->redirect(
                $app['url_generator']
                ->generate('user_profile'),
                301
            );
        }


        return $app['twig']->render(
            'user/edit.html.twig',
            array('form' => $form->createView())
        );
    }

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
        $user = [];
        $userTmp = [];
        $id = $app['security.token_storage']->getToken()->getUser()->getID();
        $userRepository = new UserRepository($app['db']);

        $form = $app['form.factory']->createBuilder(
            PswdType::class,
            $user,
            ['user_repository' => new UserRepository($app['db'])]
        )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !empty($form)) {
            $user = $form->getData();

            $password = $user['password'];
            $user['password'] = $app['security.encoder.bcrypt']
                                ->encodePassword($password, '');
            $user['PK_idUsers'] = $id;
//            var_dump($user);
            if (sizeof($user)) {
                $userRepository->save($user);

                $app['session']->getFlashBag()->add(
                    'messages',
                    [
                        'type' => 'success',
                        'message' => 'message.account_successfully_edited',
                    ]
                );
            }

//            var_dump($user);
        }



        return $app['twig']->render(
            'user/pswd.html.twig',
            array('form' => $form->createView())
        );
    }
}
