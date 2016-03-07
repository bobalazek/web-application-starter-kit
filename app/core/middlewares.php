<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Application\Entity\UserEntity;

/*** Database check ***/
$app->before(function () use ($app) {
    if (
        isset($app['database_options']) &&
        is_array($app['database_options'])
    ) {
        try {
            $app['db']->connect();
        } catch (PDOException $e) {
            return new Response(
                'Whoops, your database is configured wrong. '.
                'Please check that again! Message: '.$e->getMessage()
            );
        }
    }
});

/*** User check ***/
$app->before(function () use ($app) {
    $app['user'] = null;
    $token = $app['security']->getToken();

    if (
        $token &&
        !$app['security.trust_resolver']->isAnonymous($token) &&
        ($token->getUser() instanceof UserEntity)
    ) {
        $app['user'] = $token->getUser();
    }
});

/*** Language / Locale check ***/
$app->before(function (Request $request) use ($app) {
    $localeCookie = $request->cookies->has('locale')
        ? $request->cookies->get('locale')
        : false
    ;
    $localeFromQueryOrHeaders = false;

    // If locale is passed tought the query
    if ($request->query->get('locale', false)) {
        $localeCookie = $request->query->get('locale', false);
        $localeFromQueryOrHeaders = true;
    }

    if ($request->headers->get('Locale', false)) {
        $localeCookie = $request->headers->get('Locale', false);
        $localeFromQueryOrHeaders = true;
    }

    if ($localeCookie &&
        array_key_exists($localeCookie, $app['locales'])) {
        $app['locale'] = $localeCookie;
        $app['language_code'] = $app['locales'][$localeCookie]['language_code'];
        $app['language_name'] = $app['locales'][$localeCookie]['language_name'];
        $app['country_code'] = $app['locales'][$localeCookie]['country_code'];
        $app['flag_code'] = $app['locales'][$localeCookie]['flag_code'];

        if ($localeFromQueryOrHeaders) {
            $app['forceLocale'] = $app['locale'];
        }
    }

    $app['application.translator']->setLocale($app['locale']);
});

/*** Set Variables ****/
$app->before(function () use ($app) {
    if (!isset($app['user'])) {
        $app['user'] = null;
    }

    $app['host'] = $app['request']->getHost();
    $app['hostWithScheme'] = $app['request']->getScheme().'://'.$app['host'];
    $app['baseUri'] = $app['request']->getBaseUrl();
    $app['baseUrl'] = $app['request']->getSchemeAndHttpHost().$app['request']->getBaseUrl();
    $app['currentUri'] = $app['request']->getRequestURI();
    $app['currentUrl'] = $app['request']->getUri();
    $app['currentUriRelative'] = rtrim(str_replace($app['baseUri'], '', $app['currentUri']), '/');
    $app['currentUriArray'] = array_filter(
        explode(
            '/',
            str_replace($app['baseUri'], '', $app['currentUri'])
        ),
        'strlen'
    );

    if (
        isset($app['database_options']) &&
        is_array($app['database_options'])
    ) {
        // Settings
        $settingsCollection = $app['orm.em']
            ->getRepository('Application\Entity\SettingEntity')
            ->findAll()
        ;

        if ($settingsCollection) {
            $settingsArray = array();

            foreach ($settingsCollection as $settingsSingle) {
                $key = $settingsSingle->getKey();
                $value = $settingsSingle->getValue();

                $settingsArray[$key] = $value;
            }

            $app['settings'] = array_merge(
                $app['settings'],
                $settingsArray
            );
        }
    }
}, Application::EARLY_EVENT);

/*** Set Logut path ***/
$app->before(function (Request $request) use ($app) {
    $app['logoutUrl'] = $app['url_generator']
        ->generate('members-area.logout').
        '?_csrf_token='.
        $app['form.csrf_provider']->getToken('logout')
    ;
});

/*** SOAP ***/
$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, PATCH, DELETE, OPTIONS');
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set(
        'Access-Control-Allow-Headers',
        'Locale'
    );
});
