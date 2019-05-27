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
namespace Api;

    use Silex\Application;
    use Silex\Api\ControllerProviderInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Form\Extension\Core\Type\FormType;
    use Symfony\Component\Form\Extension\Core\Type\HiddenType;
    use Repository\FriendsRepository;
    use Symfony\Component\Security\Core\User\User;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;

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
class ApiFriendsController implements ControllerProviderInterface
{
    /**
     * Routing settings
     * Routing settings
     *
     * @param Application $app Application
     *
     * @return mixed|\Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/invite/{friendId}', [$this, 'apiInviteAction']);
        $controller->get('/add/{friendId}', [$this, 'apiAddFriend']);
        $controller->get('/index', [$this, 'apiIndexAction']);
        $controller->get('/invites', [$this, 'apiIndexInvites']);
        $controller->match('/{id}/delete', [$this, 'apiDeleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*');

        return $controller;
    }

//$response = new JsonResponse(array('name' => $friendsRepository->findAllPaginated($userId, $page)));
//$response->headers->set('Content-Type', 'application/json');
//return $response;


    /**
     * Invite action
     *
     * @param Application $app Application
     * @param Friend $friendId Id
     * @param int $page Page
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function apiInviteAction(Application $app, $friendId, $page = 1)
    {
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();
        $areInvited = $friendsRepository->areInvited($userId, $friendId);
        if (!$areInvited[0]['total_results']) {
            $response = new JsonResponse(array('result' => $friendsRepository->invite($userId, $friendId)));
        } else {
            $response = new JsonResponse(array('result' => false));
        }

        //POWINIEN ZWRÓCIĆ WYNIK AKCJI


        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Add friend
     *
     * @param Application $app Application
     * @param Friend $friendId Id
     * @param int $page Page
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function apiAddFriend(Application $app, $friendId, $page = 1)
    {
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();
        $friendsRepository->addFriend($userId, $friendId);
//        $response = new JsonResponse(array('result' => $friendsRepository->addFriend($userId, $friendId)));
        $response = new JsonResponse(array('result' => 2));

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Index friends action
     *
     * @param Application $app Application
     * @param int $page Page
     *
     * @return mixed
     */
    public function apiIndexAction(Application $app, $page = 1)
    {

        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        $response = new JsonResponse(array('friendsIndexed' => $friendsRepository->getFriends($userId, $page)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

//        return $app['twig']->render(
//            'friends/index.html.twig',
//            ['paginator' => $friendsRepository->findAllPaginated($userId, $page)]
//        );
    }

    /**
     * Index invites
     *
     * @param Application $app Application
     * @param int $page Page
     *
     * @return mixed
     */
    public function apiIndexInvites(Application $app, $page = 1)
    {
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();

        $response = new JsonResponse(array('friendsIndexed' => $friendsRepository->findAllInvitesPaginated($userId, $page)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

//        return $app['twig']->render(
//            'friends/invites.html.twig',
//            ['paginator' => $friendsRepository
//                ->findAllInvitesPaginated($userId, $page), ]
//        );
    }

    /**
     * Delete friend action
     *
     * @param Application $app Application
     * @param Friend $id Id
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function apiDeleteAction(Application $app, $id)
    {
        $friendsRepository = new FriendsRepository($app['db']);
        $userId = $app['security.token_storage']->getToken()->getUser()->getID();


        $response = new JsonResponse(array('result' => $friendsRepository->delete($userId, $id)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

//        return $app['twig']->render(
//            'friends/index.html.twig',
//            ['paginator' => $friendsRepository->findFriends($userId)]
//        );
    }
//
//    /**
//     * Edit actions
//     *
//     * @param Application $app Application
//     * @param Request $request Request
//     *
//     * @return \Symfony\Component\HttpFoundation\RedirectResponse
//     */
//    public function apiEditAction(Application $app, Request $request)
//    {
//        $user = [];
//
//        $form = $app['form.factory']->createBuilder(
//            EditType::class,
//            $user
//        )->getForm();
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $userRepository = new UserRepository($app['db']);
//
//            $user = $form->getData();
//            $password = $user['password'];
//            $user['password'] = $app['security.encoder.bcrypt']->encodePassword(
//                $password,
//                ''
//            );
//            $userRepository->save($user);
//
//            $app['session']->getFlashBag()->add(
//                'messages',
//                [
//                    'type' => 'success',
//                    'message' => 'message.element_successfully_added',
//                ]
//            );
//
//            return $app->redirect(
//                $app['url_generator']
//                    ->generate('user_profile'),
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
}