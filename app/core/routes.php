<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Application\Entity\ErrorEntity;

/*========== Index ==========*/
$app->mount(
    '/',
    new Application\ControllerProvider\IndexControllerProvider()
);

/*========== API ==========*/
$app->mount(
    '/api',
    new Application\ControllerProvider\ApiControllerProvider()
);

/*========== Members Area ==========*/
$app->mount(
    '/members-area',
    new Application\ControllerProvider\MembersAreaControllerProvider()
);

/******** My ********/
$app->mount(
    '/members-area/my',
    new Application\ControllerProvider\MembersArea\MyControllerProvider()
);

/******** Users ********/
$app->mount(
    '/members-area/users',
    new Application\ControllerProvider\MembersArea\UsersControllerProvider()
);

/******** Posts ********/
$app->mount(
    '/members-area/posts',
    new Application\ControllerProvider\MembersArea\PostsControllerProvider()
);

/******** Errors ********/
$app->mount(
    '/members-area/errors',
    new Application\ControllerProvider\MembersArea\ErrorsControllerProvider()
);

/******** Statistics ********/
$app->mount(
    '/members-area/statistics',
    new Application\ControllerProvider\MembersArea\StatisticsControllerProvider()
);

/******** Settings ********/
$app->mount(
    '/members-area/settings',
    new Application\ControllerProvider\MembersArea\SettingsControllerProvider()
);

/*** Set Locale ***/
$app->match('/set-locale/{locale}', function ($locale) use ($app) {
    $cookie = new Cookie(
        'locale',
        $locale,
        new \DateTime('now + 1 year')
    );

    $response = Response::create(null, 302, array(
        'Location' => isset($_SERVER['HTTP_REFERER'])
            ? $_SERVER['HTTP_REFERER']
            : $app['baseUrl'],
    ));

    $response->headers->setCookie($cookie);

    return $response;
})
->bind('set-locale')
->assert('locale', implode('|', array_keys($app['locales'])));

/***** Errors *****/
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // Send my email
    if ($app['error_options']['send_by_email']) {
        $app['application.mailer']
            ->swiftMessageInitializeAndSend(array(
                'subject' => $app['name'].' - '.$app['translator']->trans('An error occured').' ('.$code.')',
                'to' => $app['email'],
                'body' => 'emails/error.html.twig',
                'templateData' => array(
                    'e' => $e,
                    'code' => $code,
                ),
            ))
        ;
    }

    if (
        isset($app['orm.em']) &&
        $app['error_options']['save_into_the_database']
    ) {
        $errorEntity = new ErrorEntity();
        $errorEntity
            ->setCode($code)
            ->setMessage($e->getMessage())
            ->setException(json_encode($e))
        ;
        $app['orm.em']->persist($errorEntity);
        $app['orm.em']->flush();
    }

    // 404.html, or 40x.html, or 4xx.html, or default.html
    $templates = array(
        'contents/errors/'.$code.'.html.twig',
        'contents/errors/'.substr($code, 0, 2).'x.html.twig',
        'contents/errors/'.substr($code, 0, 1).'xx.html.twig',
        'contents/errors/default.html.twig',
    );

    return new Response(
        $app['twig']->resolveTemplate($templates)->render(
            array(
                'code' => $code,
                'e' => $e,
            )
        ),
        $code
    );
});
