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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;


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
//        $controller->get('/login', [$this, 'loginAction']);
        $controller->post('/login', [$this, 'loginAction']);

//        $controller->post('/login/authenticate', [$this, 'checkCredentials']);
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
//    public function loginAction(Application $app, Request $request)
//    {
////        echo("requestowanie");
////
////        $app->before(function (Request $request) {
////            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
////                $data = json_decode($request->getContent(), true);
////                $request->request->replace(is_array($data) ? $data : array());
////            }
////            echo("requestowanie");
////            var_dump($request);
////        });
//
//        $user = ['email' => $app['session']->get('_security.last_username')];
//        $form = $app['form.factory']
//            ->createBuilder(LoginType::class, $user)->getForm();
//        $app['session']->set('userid', $user);
//        http_response_code(200);
//    }

    public function loginAction(Application $app, Request $request)
    {
        $vars = json_decode($request->getContent(), true);

        try {
            if (empty($vars['_username']) || empty($vars['_password'])) {
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $vars['_username']));
            }

            /**
             * @var $user User
             */
//            $user = $app['users']->loadUserByUsername($vars['_username']);
            $userRepository = new UserRepository($app['db']);
            $user = $userRepository->loadUserByEmail($vars['_username']);

//            var_dump($user);
//            var_dump($vars["_password"]);
//            var_dump($app['security.encoder.digest']->isPasswordValid($user["password"], $vars['_password'], ''));
            if (! $app['security.encoder.bcrypt']->isPasswordValid($user["password"], $vars['_password'], '')) {
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $vars['_username']));
            } else {
                $response = [
//                    'token' => $app['security.jwt.encoder']->encode(['name' => $user['email']]),
                    'token' => $app['jwt_auth']->generateToken($user['id'])
                ];
            }
        } catch (UsernameNotFoundException $e) {
            $response = [
                'success' => false,
                'error' => 'Invalid credentials',
            ];
        }

        return $app->json($response, Response::HTTP_OK);
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
        $vars = json_decode($request->getContent(), true);

        try {
            if (empty($vars['_username']) || empty($vars['_password'])) {
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $vars['_username']));
            }

            /**
             * @var $user User
             */
//            $user = $app['users']->loadUserByUsername($vars['_username']);
            $userRepository = new UserRepository($app['db']);
            $user = $userRepository->loadUserByEmail($vars['_username']);

//            var_dump($user);
//            var_dump($vars["_password"]);
//            var_dump($app['security.encoder.digest']->isPasswordValid($user["password"], $vars['_password'], ''));
            if (! $app['security.encoder.bcrypt']->isPasswordValid($user["password"], $vars['_password'], '')) {
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $vars['_username']));
            } else {
                $response = [
//                    'token' => $app['security.jwt.encoder']->encode(['name' => $user['email']]),
                    'token' => $app['jwt_auth']->generateToken($user['id'])
                ];
            }
        } catch (UsernameNotFoundException $e) {
            $response = [
                'success' => false,
                'error' => 'Invalid credentials',
            ];
        }

        return $app->json($response, Response::HTTP_OK);
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
//            $response = new JsonResponse(array('result' => true));

//            return $app->redirect(
//                $app['url_generator']
//                ->generate('posts_index_paginated'),
//                301
//            );
        }
//        else{
//            $response = new JsonResponse(array('result' => false));
//        }
//        $response->headers->set('Content-Type', 'application/json');
//        return $response;
//
//        return $app['twig']->render(
//            'user/add.html.twig',
//            array('form' => $form->createView())
//        );
        http_response_code(200);
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
        http_response_code(200);
//        return $app['twig']->render('auth/logout.html.twig', []);
    }
}
