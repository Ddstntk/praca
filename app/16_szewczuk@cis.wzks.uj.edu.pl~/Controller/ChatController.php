<?php
/**
 * Chat controller.
 *
 * @copyright (c) 2018 Konrad Szewczuk
 */
namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Repository\ChatRepository;
use Form\MessageType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class PostsController.
 */
class ChatController implements ControllerProviderInterface
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
        $controller->get('/view/{id}', [$this, 'indexAction'])->bind('chat_index_paginated');
        $controller->match('/send', [$this, 'sendAction'])
            ->method('POST|GET')
            ->bind('messages_send');
        return $controller;
    }


    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return string Response
     */
    public function indexAction(Application $app, $page = 1, $id)
    {
        $chatRepository = new ChatRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();
        return $app['twig']->render(
            'chat/index.html.twig',
            ['paginator' => $chatRepository->findAllPaginated($page, $userId, $id),
                'user' => $userId]
        );
    }


    /**
     * Add action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function sendAction(Application $app, Request $request)
    {
        $post = [];

        $form = $app['form.factory']->createBuilder(
            MessageType::class,
            $post
        )->getForm();
        $form->handleRequest($request);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();
        if ($form->isSubmitted() && $form->isValid()) {
            $postsRepository = new ChatRepository($app['db']);
            $postsRepository->save($form->getData(), $userId);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );

            return $app->redirect($app['url_generator']->generate('chat_index_paginated', array('id' => 1)), 301);
        }


        return $app['twig']->render(
            'chat/send.html.twig',
            [
                'post' => $post,
                'form' => $form->createView(),
            ]
        );
    }


}