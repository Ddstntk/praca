<?php
/**
 * Posts controller.
 *
 * @copyright (c) 2018 Konrad Szewczuk
 */
namespace Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Repository\PostsRepository;
use Form\PostType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class PostsController.
 */
class PostsController implements ControllerProviderInterface
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
        //        $controller->get('/', [$this, 'indexAction'])->bind('posts_index_paginated');
        $controller->get('/page/{page}', [$this, 'indexAction'])
            ->assert('page', '[1-9]\d*')
            ->value('page', 1)
            ->bind('posts_index_paginated');
        $controller->match('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('posts_add');
        //        $controller->match('/{id}/delete', [$this, 'deleteAction'])
        //            ->method('GET|POST')
        //            ->assert('id', '[1-9]\d*')
        //            ->bind('posts_delete');
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
        $postsRepository = new PostsRepository($app['db']);

        return $app['twig']->render(
            'posts/index.html.twig',
            ['paginator' => $postsRepository->findAllPaginated($page)]
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
    public function addAction(Application $app, Request $request)
    {
        $post = [];

        $form = $app['form.factory']->createBuilder(
            PostType::class,
            $post
        )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postsRepository = new PostsRepository($app['db']);
            $postsRepository->save($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );

            return $app->redirect($app['url_generator']->generate('posts_index'), 301);
        }


        return $app['twig']->render(
            'posts/add.html.twig',
            [
                'post' => $post,
                'form' => $form->createView(),
            ]
        );
    }

    //
    //    /**
    //     * Delete action.
    //     *
    //     * @param \Silex\Application                        $app     Silex application
    //     * @param int                                       $id      Record id
    //     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
    //     *
    //     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
    //     */
    //    public function deleteAction(Application $app, $id, Request $request)
    //    {
    //        $tagsRepository = new TagsRepository($app['db']);
    //        $tag = $tagsRepository->findOneById($id);
    //
    //        if (!$tag) {
    //            $app['session']->getFlashBag()->add(
    //                'messages',
    //                [
    //                    'type' => 'warning',
    //                    'message' => 'message.record_not_found',
    //                ]
    //            );
    //
    //            return $app->redirect($app['url_generator']->generate('tags_index'));
    //        }
    //
    //        $form = $app['form.factory']->createBuilder(FormType::class, $tag)->add('id', HiddenType::class)->getForm();
    //        $form->handleRequest($request);
    //
    //        if ($form->isSubmitted() && $form->isValid()) {
    //            $tagsRepository->delete($form->getData());
    //
    //            $app['session']->getFlashBag()->add(
    //                'messages',
    //                [
    //                    'type' => 'success',
    //                    'message' => 'message.element_successfully_deleted',
    //                ]
    //            );
    //
    //            return $app->redirect(
    //                $app['url_generator']->generate('tags_index'),
    //                301
    //            );
    //        }
    //
    //        return $app['twig']->render(
    //            'tags/delete.html.twig',
    //            [
    //                'tag' => $tag,
    //                'form' => $form->createView(),
    //            ]
    //        );
    //    }
}