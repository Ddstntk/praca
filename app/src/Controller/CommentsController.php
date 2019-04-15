<?php
/**
 * PHP Version 5.6
 * Comments controller.
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
use Repository\CommentsRepository;
use Repository\PostsRepository;
use Form\CommentType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class PostsController.
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
class CommentsController implements ControllerProviderInterface
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
        $controller->get('/post/{postId}', [$this, 'indexAction'])
            ->assert('page', '[1-9]\d*')
            ->value('page', 1)
            ->bind('comments_index_paginated');
        $controller->match('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('comments_add');

        return $controller;
    }

    /**
     * Index action
     *
     * @param Application $app    Application
     * @param Post        $postId Id
     * @param int         $page   Page
     *
     * @return mixed
     */
    public function indexAction(Application $app, $postId, $page = 1)
    {
        $commentsRepository = new CommentsRepository($app['db']);
        $postsRepository = new PostsRepository($app['db']);

        $post = [];

        $form = $app['form.factory']->createBuilder(
            CommentType::class,
            $post
        )->getForm();

        return $app['twig']->render(
            'comments/index.html.twig',
            [
                'paginator' => $commentsRepository->findAllPaginated($postId, $page),
                'xd' => $postsRepository->findOneById($postId),
                'post' => $post,
                'form' => $form->createView(), ]
        );
    }


    /**
     * Add comment action
     *
     * @param Application $app     Application
     * @param Request     $request Request
     * @param int         $page    Page
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addAction(Application $app, Request $request, $page = 1)
    {
        $post = [];
        $form = $app['form.factory']->createBuilder(
            CommentType::class,
            $post
        )->getForm();
        $form->handleRequest($request);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();
        $x = $request->headers->get('referer');
        var_dump($x);

        if (preg_match("/\/(\d+)$/", $x, $matches)) {
            $id = $matches[1];
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $postsRepository = new CommentsRepository($app['db']);
            $postsRepository->save($form->getData(), $id, $userId);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.comment_successfully_added',
                ]
            );

            return $app->redirect(
                $app['url_generator']
                ->generate(
                    'comments_index_paginated',
                    array("postId" => $id)
                ),
                301
            );
        }
    }
}
