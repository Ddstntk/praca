<?php
/**
 * PHP Version 5.6
 * Friends controller.
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
use Repository\FriendsRepository;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class FriendsController.
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
class FriendsController implements ControllerProviderInterface
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
        $controller->get('/invite/{friendId}', [$this, 'inviteAction'])
            ->bind('friend_invite');
        $controller->get('/add/{friendId}', [$this, 'addFriend'])
            ->bind('friend_add');
        $controller->get('/index', [$this, 'indexAction'])
            ->bind('friends_index_paginated');
        $controller->get('/invites', [$this, 'indexInvites'])
            ->bind('invites_index_paginated');
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('friends_delete');

        return $controller;
    }

    /**
     * Invite action
     *
     * @param Application $app      Application
     * @param Friend      $friendId Id
     * @param int         $page     Page
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function inviteAction(Application $app, $friendId, $page = 1)
    {
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();
        $areInvited = $friendsRepository->areInvited($userId, $friendId);
        var_dump($areInvited);
        if (!$areInvited) {
            $friendsRepository -> invite($userId, $friendId);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.user_invited',
                ]
            );
        }

        return $app['twig']->render(
            'friends/index.html.twig',
            ['paginator' => $friendsRepository->findAllPaginated($userId, $page)]
        );
    }

    /**
     * Add friend
     *
     * @param Application $app      Application
     * @param Friend      $friendId Id
     * @param int         $page     Page
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addFriend(Application $app, $friendId, $page = 1)
    {
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        $friendsRepository -> addFriend($userId, $friendId);

        return $app['twig']->render(
            'friends/index.html.twig',
            ['paginator' => $friendsRepository->findAllPaginated($userId, $page)]
        );
    }

    /**
     * Index friends action
     *
     * @param Application $app  Application
     * @param int         $page Page
     *
     * @return mixed
     */
    public function indexAction(Application $app, $page = 1)
    {

        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        return $app['twig']->render(
            'friends/index.html.twig',
            ['paginator' => $friendsRepository->findAllPaginated($userId, $page)]
        );
    }

    /**
     * Index invites
     *
     * @param Application $app  Application
     * @param int         $page Page
     *
     * @return mixed
     */
    public function indexInvites(Application $app, $page = 1)
    {
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        return $app['twig']->render(
            'friends/invites.html.twig',
            ['paginator' => $friendsRepository
                ->findAllInvitesPaginated($userId, $page), ]
        );
    }

    /**
     * Delete friend action
     *
     * @param Application $app Application
     * @param Friend      $id  Id
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteAction(Application $app, $id)
    {
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();
        $friendsRepository -> delete($userId, $id);

        return $app['twig']->render(
            'friends/index.html.twig',
            ['paginator' => $friendsRepository->findAllPaginated($userId, 1)]
        );
    }

    /**
     * Edit actions
     *
     * @param Application $app     Application
     * @param Request     $request Request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
}
