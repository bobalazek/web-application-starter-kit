<?php

use Symfony\Component\HttpFoundation\Response;

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

/******** Roles ********/
$app->mount(
    '/members-area/roles',
    new Application\ControllerProvider\MembersArea\RolesControllerProvider()
);

/******** Posts ********/
$app->mount(
    '/members-area/posts',
    new Application\ControllerProvider\MembersArea\PostsControllerProvider()
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
    $cookie = new \Symfony\Component\HttpFoundation\Cookie(
        'locale',
        $locale,
        new \DateTime('now + 1 year')
    );

    $response = \Symfony\Component\HttpFoundation\Response::create(null, 302, array(
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

    if ($code == '403') {
        return $app->redirect(
            $app['url_generator']->generate(
                'index'
            )
        );
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
            )
        ),
        $code
    );
});
