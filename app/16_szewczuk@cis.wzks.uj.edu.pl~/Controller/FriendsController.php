<?php
/**
 * Friends controller.
 *
 * @copyright (c) 2018 Konrad Szewczuk
 *
 * @link http://cis.wzks.uj.edu.pl/~16_szewczuk/web/index_dev.php/
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
 * Class BookmarksController.
 */
class FriendsController implements ControllerProviderInterface
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
        $controller->get('/add/{friendId}', [$this, 'addFriend'])->bind('friend_add');
        //        $controller->get('/remove', [$this, 'viewAction'])->bind('friend_remove');
        return $controller;
    }

    /**
     * Add friend
     *
     * @param  Application $app
     * @param  Request     $request
     * @return Response
     */

    public function addFriend(Application $app, $friendId)
    {
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        $friendsRepository -> addFriend($userId, $friendId);

        return $app['twig']->render(
            'posts/index.html.twig'
        );
    }
    //
    //    /**
    //     * Index action.
    //     *
    //     * @param \Silex\Application $app Silex application
    //     *
    //     * @return string Response
    //     */
    //    public function indexAction(Application $app, $page = 1)
    //    {
    //        $userRepository = new UserRepository($app['db']);
    //
    //        return $app['twig']->render(
    //            'user/index.html.twig',
    //            ['paginator' => $userRepository->findAllPaginated($page)]
    //        );
    //    }
    //
    //    /**
    //     * View action.
    //     *
    //     * @param  \Silex\Application $app Silex application
    //     * @param  string             $id  Element Id
    //     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
    //     */
    //    public function viewAction(Application $app, $id)
    //    {
    //        $userRepository = new UserRepository($app['db']);
    //        return $app['twig']->render(
    //            'user/view.html.twig',
    //            ['user' => $userRepository->getUserById($id)]
    //        );
    //    }
    //
    //    /**
    //     * Profile action.
    //     *
    //     * @param  \Silex\Application $app   Silex application
    //     * @param  string             $email Element Email
    //     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
    //     */
    //    public function profileAction(Application $app)
    //    {
    //        $userRepository = new UserRepository($app['db']);
    //
    //        $id = $app['security.token_storage']->getToken()->getUser()->getID();
    //        var_dump($id);
    //        return $app['twig']->render(
    //            'user/view.html.twig',
    //            ['user' => $userRepository->getUserById($id)]
    //        );
    //    }



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
