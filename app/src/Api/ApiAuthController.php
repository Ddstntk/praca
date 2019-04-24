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
namespace Api;

use Form\LoginType;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Repository\UserRepository;
use Form\SignupType;
use Service\userTokenService;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class AuthController.
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
class ApiAuthController implements ControllerProviderInterface
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
        $controller->get('/login', [$this, 'loginAction']);
        $controller->post('/login/authenticate', [$this, 'checkCredentials']);
        $controller->get('/logout', [$this, 'logoutAction']);
        $controller->get('/signup', [$this, 'signupAction'])
            ->method('POST|GET');

        return $controller;
    }

    /**
     * Login action
     *
     * @param Application $app     Application
     * @param Request     $request Request
     *
     * @return mixed
     */
    public function loginAction(Application $app, Request $request)
    {
        echo("requestowanie");

        $app->before(function (Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());
            }
            echo("requestowanie");
            var_dump($request);
        });
//        echo("requestowanie");
//        var_dump($request);

        $user = ['email' => $app['session']->get('_security.last_username')];
        $form = $app['form.factory']
            ->createBuilder(LoginType::class, $user)->getForm();
        $app['session']->set('userid', $user);

//        return $app['twig']->render(
//            'auth/login.html.twig',
//            [
//                'form' => $form->createView(),
//                'error' => $app['security.last_error']($request),
//            ]
//        );
    }

    /**
     * Login action
     *
     * @param Application $app     Application
     * @param Request     $request Request
     *
     * @return mixed
     */
    public function checkCredentials(Application $app, Request $request)
    {

                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());

        var_dump($request->request->get('email'));

//        $app->before(function (Request $request) {
//            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
//                $data = json_decode($request->getContent(), true);
//                $request->request->replace(is_array($data) ? $data : array());
////            }
//            echo("requestowanie");
////        var_dump($request);
//            echo "początek";
//        var_dump($data);
//        echo "koniec";
//            return $data;
//        });

//
        return $app->redirect(
            $app['url_generator']
                ->generate('posts_index_paginated'),
            301);
    }


    /**
     * Signup Action
     *
     * @param Application $app     Application
     * @param Request     $request Request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function signupAction(Application $app, Request $request)
    {
        $user = [];

        $form = $app['form.factory']->createBuilder(
            SignupType::class,
            $user
        )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository = new UserRepository($app['db']);

            $user = $form->getData();
            $password = $user['password'];
            $user['password'] = $app['security.encoder.bcrypt']
                                ->encodePassword($password, '');
            $user['role_id'] = 2;
            $user['photo'] = 'default.jpg';
            $userRepository->save($user);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.signup_success',
                ]
            );

            return $app->redirect(
                $app['url_generator']
                ->generate('posts_index_paginated'),
                301
            );
        }


        return $app['twig']->render(
            'user/add.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Logout action
     *
     * @param Application $app Application
     *
     * @return mixed
     */
    public function logoutAction(Application $app)
    {
        $app['session']->clear();

        return $app['twig']->render('auth/logout.html.twig', []);
    }
}
