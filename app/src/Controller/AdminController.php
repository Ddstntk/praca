<?php
/**
 * PHP Version 5.6
 * Admin controller.
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
use Form\AccessType;
use Repository\FriendsRepository;
use Repository\PostsRepository;
use Repository\CommentsRepository;
use Form\MessageType;
use Form\ChatType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Security\Core\User\User;

/**
 * Class AdminController.
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
class AdminController implements ControllerProviderInterface
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
        $controller->get('/', [$this, 'mainAction'])->bind('admin_main');
        $controller->match('/users', [$this, 'manageUsers'])
            ->method('POST|GET')
            ->bind('admin_users');
        $controller->match('/users/{id}/edit', [$this, 'adminEditUserView'])
            ->method('POST|GET')
            ->bind('admin_user_edit');
        $controller->match('/posts', [$this, 'managePosts'])
            ->method('POST|GET')
            ->bind('admin_posts');
        $controller->match('/comments/{postId}', [$this, 'manageComments'])
            ->method('POST|GET')
            ->bind('admin_comments');
        $controller->match('/users/{id}/delete', [$this, 'deleteUsers'])
            ->method('POST|GET')
            ->bind('admin_user_delete');
        $controller->match('/users/{id}/confirm', [$this, 'confirmUser'])
            ->method('POST|GET')
            ->bind('admin_user_confirm');
        $controller->match('/posts/{id}/delete', [$this, 'deletePosts'])
            ->method('POST|GET')
            ->bind('admin_posts_delete');
        $controller->match('/comments/{id}/delete', [$this, 'deleteComments'])
            ->method('POST|GET')
            ->bind('admin_comments_delete');

        return $controller;
    }

    /**
     * Main page
     *
     * @param Application $app Application
     *
     * @return mixed
     */
    public function mainAction(Application $app)
    {

        return $app['twig']->render(
            'admin/index.html.twig'
        );
    }

    /**
     * User menagement
     *
     * @param Application $app Application
     *
     * @return mixed
     */
    public function manageUsers(Application $app)
    {
        $userRepository = new UserRepository($app['db']);

        return $app['twig']->render(
            'user/index_simple.html.twig',
            ['users' => $userRepository->findAll()]
        );
    }

    /**
     * Edit user action
     *
     * @param Application $app     Application
     * @param Request     $request Request
     * @param User        $id      Id
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function adminEditUserView(Application $app, Request $request, $id)
    {
        $userRepository = new UserRepository($app['db']);
        //        $userRepository->access($id);

        $user = [];
        $userTmp = [];

        $form = $app['form.factory']->createBuilder(
            AccessType::class,
            $user
        )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && !empty($form)) {
            $userTmp = $form->getData();


            //                $user['PK_idUsers'] = $id;
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
                        'message' => 'message.user_successfully_edited',
                    ]
                );
            }
        }

        return $app['twig']->render(
            'user/admin_view.html.twig',
            ['user' => $userRepository->getUserById($id),
                'form' => $form->createView(), ]
        );
    }

    /**
     * Confirm user deletion
     *
     * @param Application $app Application
     * @param User        $id  Id
     *
     * @return mixed User
     */
    public function confirmUser(Application $app, $id)
    {
        $userRepository = new UserRepository($app['db']);

        return $app['twig']->render(
            'user/admin_confirm.html.twig',
            ['user' => $userRepository->getUserById($id)]
        );
    }
    /**
     * Delete user action
     *
     * @param Application $app Application
     * @param User        $id  Id
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function deleteUsers(Application $app, $id)
    {
        $userRepository = new UserRepository($app['db']);

        $user = $userRepository->getUserById($id);
        if (!$user) {
            $app['session']-> getFlashBag()
                -> add(
                    'messages',
                    ['type' => 'warning', 'message' => 'message.user_not_found']
                );
        } else {
            $userRepository->delete($id);
            $app['session']->getFlashBag()
            ->add(
                'messages',
                ['type' => 'success',
                    'message' => 'message.user_successfully_deleted', ]
            );
        }

            return $app['twig']->render(
                'user/index_simple.html.twig',
                ['users' => $userRepository->findAll()]
            );
    }


    /**
     * Post menagement
     *
     * @param Application $app Application
     *
     * @return mixed
     */
    public function managePosts(Application $app)
    {
        $postRepository = new PostsRepository($app['db']);

        return $app['twig']->render(
            'posts/index_simple.html.twig',
            ['posts' => $postRepository->findAll()]
        );
    }

    /**
     * Delete post action
     *
     * @param Application $app Application
     * @param User        $id  Id
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deletePosts(Application $app, $id)
    {
        $postRepository = new PostsRepository($app['db']);
        $postRepository->delete($id);

        return $app['twig']->render(
            'posts/index_simple.html.twig',
            ['posts' => $postRepository->findAll()]
        );
    }

    /**
     *  Comments menagement
     *
     * @param Application $app    Application
     * @param Post        $postId Id
     * @param int         $page   Page
     *
     * @return mixed
     */
    public function manageComments(Application $app, $postId, $page = 1)
    {
        $commentsRepository = new CommentsRepository($app['db']);
        $postsRepository = new PostsRepository($app['db']);

        return $app['twig']->render(
            'comments/index_simple.html.twig',
            [
                'xd' => $postsRepository->findOneById($postId),
                'paginator' => $commentsRepository->findAllPaginated($page, $postId),
                ]
        );
    }

    /**
     * Delete comment action
     *
     * @param Application $app Application
     * @param Comment     $id  Id
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteComments(Application $app, $id)
    {
        $commentsRepository = new CommentsRepository($app['db']);
        $commentsRepository -> delete($id);
        $postRepository = new PostsRepository($app['db']);

        return $app['twig']->render(
            'posts/index_simple.html.twig',
            ['posts' => $postRepository->findAll()]
        );
    }
}
