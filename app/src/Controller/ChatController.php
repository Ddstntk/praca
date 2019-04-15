<?php
/**
 * PHP Version 5.6
 * Chat controller.
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

use Repository\UserRepository;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Repository\ChatRepository;
use Repository\FriendsRepository;
use Form\MessageType;
use Form\ChatType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ChatController.
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
class ChatController implements ControllerProviderInterface
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
        $controller->get('/view', [$this, 'indexAction'])
            ->bind('chat_index_paginated');
        $controller->get('/all', [$this, 'indexChats'])
            ->bind('chat_index');
        $controller->match('/send', [$this, 'sendAction'])
            ->method('POST|GET')
            ->bind('messages_send');
        $controller->match('/new', [$this, 'newChat'])
            ->method('POST|GET')
            ->bind('conversation_new');
        $controller->match('/set/{id}', [$this, 'setChat'])
            ->method('POST|GET')
            ->bind('set_chat');

        return $controller;
    }

    /**
     * Create new chat
     *
     * @param Application $app     Application
     * @param Request     $request Request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function newChat(Application $app, Request $request)
    {
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        $conversation = [];
        $friendList = [];
        $friendsRepository = new friendsRepository($app['db']);

        $friends = $friendsRepository -> friendsNames($userId);

        foreach ($friends as $k) {
            $fullname = $k['name'].' '.$k['surname'];
            $friendList[$fullname] = $k['PK_idUsers'];
        }

        $form = $app['form.factory']->createBuilder(
            ChatType::class,
            $conversation,
            array(
                'data' => $friendList,
                )
        )->getForm();
        $form->handleRequest($request);

        $id = 2;

        if ($form->isSubmitted()) {
            $chatRepository = new ChatRepository($app['db']);
            $chatRepository->addChat($form->getData(), $id, $userId);

            $app['session']->getFlashBag()->add(
                'conversations',
                [
                    'type' => 'success',
                    'message' => 'message.chat_created',
                ]
            );

            return $app->redirect(
                $app['url_generator']
                ->generate('chat_index'),
                301
            );
        }

        return $app['twig']->render(
            'chat/new.html.twig',
            [
            //                'conversation' => $conversation,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Index messages action
     *
     * @param Application      $app     Application
     * @param SessionInterface $session Session
     * @param int              $page    Page num
     *
     * @return mixed
     */
    public function indexAction(Application $app, SessionInterface $session, $page = 1)
    {
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        $chatRepository = new ChatRepository($app['db']);
        $id = $session->get('chat');
        if (!$id) {
            $idArr = $chatRepository
                ->findLastChat($userId);
            if (!$idArr) {
                return $app['twig']->render(
                    'chat/index.html.twig',
                    ['paginator' => $chatRepository
                        ->findAllPaginated(1234, 1234, $page),
                    'user' => $userId, ]
                );
            }
            $id = $idArr[0]["FK_idConversations"];
        }

        return $app['twig']->render(
            'chat/index.html.twig',
            ['paginator' => $chatRepository
                ->findAllPaginated($userId, $id, $page),
                'user' => $userId, ]
        );
    }

    /**
     * Set displayed chat
     *
     * @param Application      $app     Application
     * @param SessionInterface $session Session
     * @param Chat             $id      Id
     *
     * @return mixed
     */
    public function setChat(Application $app, SessionInterface $session, $id)
    {
        $session->set('chat', $id);
        $chatRepository = new ChatRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        return $app->redirect($app['url_generator']->generate('chat_index'), 301);
    }
    /**
     * Index chats action
     *
     * @param Application $app Application
     *
     * @return mixed
     */
    public function indexChats(Application $app)
    {
        $chatRepository = new ChatRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        return $app['twig']->render(
            'chat/all.html.twig',
            ['chats' => $chatRepository->findAllChats($userId),
                'user' => $userId, ]
        );
    }

    /**
     * Send action
     *
     * @param Application      $app     Application
     * @param Request          $request Request
     * @param SessionInterface $session Session
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function sendAction(Application $app, Request $request, SessionInterface $session)
    {
        $post = [];
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        $chatRepository = new ChatRepository($app['db']);
        $id = $session->get('chat');
        if (!$id) {
            $idArr = $chatRepository->findLastChat($userId);
            if ($idArr) {
                $id = $idArr[0]["FK_idConversations"];
            }
        }
        $form = $app['form.factory']->createBuilder(
            MessageType::class,
            $post
        )->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postsRepository = new ChatRepository($app['db']);
            $postsRepository->save($form->getData(), $userId, $id);
        }


        return $app['twig']->render(
            'chat/send.html.twig',
            [
                'id' => $id,
                'post' => $post,
                'form' => $form->createView(),
            ]
        );
    }
}
