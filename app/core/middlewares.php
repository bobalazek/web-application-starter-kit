<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*** Database check ***/
$app->before(function () use ($app) {
    if (isset($app['databaseOptions']) &&
        is_array($app['databaseOptions'])) {
        try {
            $app['db']->connect();
        } catch (PDOException $e) {
            return new Response(
                'Whoops, your database is configured wrong.
                Please check that again! Message: '.$e->getMessage()
            );
        }
    }
});

/*** User check ***/
$app->before(function () use ($app) {
    $app['user'] = null;
    $token = $app['security']->getToken();

    if ($token &&
        ! $app['security.trust_resolver']->isAnonymous($token) &&
        $token->getUser() instanceof \Application\Entity\UserEntity) {
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
        $app['languageCode'] = $app['locales'][$localeCookie]['languageCode'];
        $app['languageName'] = $app['locales'][$localeCookie]['languageName'];
        $app['countryCode'] = $app['locales'][$localeCookie]['countryCode'];
        $app['flagCode'] = $app['locales'][$localeCookie]['flagCode'];

        if ($localeFromQueryOrHeaders) {
            $app['forceLocale'] = $app['locale'];
        }
    }

    $app['application.translator']->setLocale($app['locale']);
});

/*** Set Variables ****/
$app->before(function () use ($app) {
    if (!$app['session']->isStarted()) {
        $app['session']->start();
    }

    if (! isset($app['user'])) {
        $app['user'] = null;
    }

    $app['sessionId'] = $app['session']->getId();
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
}, \Silex\Application::EARLY_EVENT);

/*** Set Logut path ***/
$app->before(function (Request $request) use ($app) {
    $csrfToken = $app['form.csrf_provider']->generateCsrfToken('logout');
    $app['logoutUrl'] = $app['url_generator']->generate('members-area.logout').'?csrf_token='.$csrfToken;
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
