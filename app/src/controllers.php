<?php
/**
 * PHP Version 5.6
 * Routing and controllers.
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

use Controller\UserController;
use Controller\PostsController;
use Controller\AuthController;
use Controller\ChatController;
use Controller\FriendsController;
use Controller\CommentsController;
use Controller\PhotosController;
use Controller\AdminController;

$app->get(
    '/',
    function () use ($app) {
        $userRepository = new \Repository\UserRepository($app['db']);

        return $app['twig']->render(
            'user/view.html.twig',
            ['user' => $userRepository
                ->getUserById(
                    $app['security.token_storage']
                    ->getToken()
                    ->getUser()
                    ->getID()
                ), ]
        );
    }
)
    ->bind('homepage');

$app->mount('/user', new UserController());
$app->mount('/posts', new PostsController());
$app->mount('/auth', new AuthController());
$app->mount('/chat', new ChatController());
$app->mount('/friend', new FriendsController());
$app->mount('/comment', new CommentsController());
$app->mount('/photo', new PhotosController());
$app->mount('/admin', new AdminController());
