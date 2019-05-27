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

use Api\ApiUserController;
use Api\ApiPostsController;
use Api\ApiAuthController;
use Api\ApiChatController;
use Api\ApiFriendsController;
//use Api\ApiCommentsController;
//use Api\ApiPhotosController;
//use Api\ApiAdminController;

//
//$app->get(
//    '/',
//    function () use ($app) {
//        $userRepository = new \Repository\UserRepository($app['db']);
//
//        return $app['twig']->render(
//            'user/view.html.twig',
//            ['user' => $userRepository
//                ->getUserById(
//                    $app['security.token_storage']
//                    ->getToken()
//                    ->getUser()
//                    ->getID()
//                ), ]
//        );
//    }
//)
//    ->bind('homepage');

$app->mount('/user', new UserController());
$app->mount('/posts', new PostsController());
$app->mount('/auth', new AuthController());
$app->mount('/chat', new ChatController());
$app->mount('/friend', new FriendsController());
$app->mount('/comment', new CommentsController());
$app->mount('/photo', new PhotosController());
$app->mount('/admin', new AdminController());

$app->mount('/api/user', new ApiUserController());
$app->mount('/api/posts', new ApiPostsController());
$app->mount('/api/auth', new ApiAuthController());
$app->mount('/api/chat', new ApiChatController());
$app->mount('/api/friend', new ApiFriendsController());


$app->get('/api/protected_resource', function() use ($app){
    return $app->json(['hello' => 'world']);
});
//$token = $app['security.token_storage']->getToken();
//var_dump($token);
//$userId = $app['security.token_storage']->getToken()->getUser()->getID();
//var_dump($userId);
//$app->mount('/api/comment', new ApiCommentsController());
//$app->mount('/api/photo', new ApiPhotosController());
//$app->mount('/api/admin', new ApiAdminController());
