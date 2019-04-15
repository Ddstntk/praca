<?php
/**
 * Bookmarks controller.
 *
 * @copyright (c) 2016 Tomasz Chojna
 *
 * @link http://epi.chojna.info.pl
 */
namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Repository\UserRepository;
use Form\SignupType;
use Form\EditType;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * Class BookmarksController.
 */
class UserController implements ControllerProviderInterface
{
    /**
     * Routing settings.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Silex\ControllerCollection Result
     */

    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/profile', [$this, 'profileAction'])->bind('user_profile');
        $controller->get('/view/{id}', [$this, 'viewAction'])->bind('user_view');
        $controller->get('/index', [$this, 'indexAction'])->bind('users_index_paginated');
        $controller->match('/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->bind('user_edit');
        return $controller;
    }

    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return string Response
     */
    public function indexAction(Application $app, $page = 1)
    {
        $userRepository = new UserRepository($app['db']);

        return $app['twig']->render(
            'user/index.html.twig',
            ['paginator' => $userRepository->findAllPaginated($page)]
        );
    }

    /**
     * View action.
     *
     * @param  \Silex\Application $app Silex application
     * @param  string             $id  Element Id
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
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
     * Profile action.
     *
     * @param  \Silex\Application $app   Silex application
     * @param  string             $email Element Email
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
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
     * @param Application $app
     * @param Request     $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\DBAL\DBALException
     */

    public function editAction(Application $app, Request $request)
    {
        $user = [];

        $form = $app['form.factory']->createBuilder(
            EditType::class,
            $user
        )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository = new UserRepository($app['db']);

            $user = $form->getData();
            $password = $user['password'];
            $user['password'] = $app['security.encoder.bcrypt']->encodePassword(
                $password,
                ''
            );
            $userRepository->save($user);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );

            return $app->redirect($app['url_generator']->generate('user_profile'), 301);
        }


        return $app['twig']->render(
            'user/edit.html.twig',
            array('form' => $form->createView())
        );
    }
}
