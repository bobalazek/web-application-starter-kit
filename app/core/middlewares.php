<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Application\Entity\UserEntity;

/*** User check ***/
$app->before(function (Request $request, Application $app) {
    $app['user'] = null;
    $token = $app['security.token_storage']->getToken();

    if (
        $token &&
        !$app['security.trust_resolver']->isAnonymous($token) &&
        $token->getUser() instanceof UserEntity
    ) {
        $app['user'] = $token->getUser();

        $app['user']->setTimeLastActive(new \Datetime());
        $app['orm.em']->persist($app['user']);
        $app['orm.em']->flush();
    }
});

/*** Language / Locale check ***/
$app->before(function (Request $request, Application $app) {
    $localeCookie = $request->cookies->has('locale')
        ? $request->cookies->get('locale')
        : false
    ;

    // If locale is passed tought the query
    if ($request->query->get('locale', false)) {
        $localeCookie = $request->query->get('locale', false);
    }

    if ($request->headers->get('Locale', false)) {
        $localeCookie = $request->headers->get('Locale', false);
    }

    if (
        $localeCookie &&
        array_key_exists($localeCookie, $app['locales'])
    ) {
        $app['locale'] = $localeCookie;
        $app['language_code'] = $app['locales'][$localeCookie]['language_code'];
        $app['language_name'] = $app['locales'][$localeCookie]['language_name'];
        $app['country_code'] = $app['locales'][$localeCookie]['country_code'];
        $app['flag_code'] = $app['locales'][$localeCookie]['flag_code'];
    }

    $app['translator']->setLocale($app['locale']);
});

/*** Set Variables ****/
$app->before(function (Request $request, Application $app) {
    if (!$app['session']->isStarted()) {
        $app['session']->start();
    }

    if (!isset($app['user'])) {
        $app['user'] = null;
    }

    $app['sessionId'] = $app['session']->getId();
    $app['host'] = $request->getHost();
    $app['hostWithScheme'] = $request->getScheme().'://'.$app['host'];
    $app['baseUri'] = $request->getBaseUrl();
    $app['baseUrl'] = $request->getSchemeAndHttpHost().$request->getBaseUrl();
    $app['currentUri'] = $request->getRequestURI();
    $app['currentUrl'] = $request->getUri();
    $app['currentUriRelative'] = rtrim(str_replace($app['baseUri'], '', $app['currentUri']), '/');
    $app['currentUriArray'] = array_filter(
        explode(
            '/',
            str_replace($app['baseUri'], '', $app['currentUri'])
        ),
        'strlen'
    );

    // Settings
    $settingsCollection = $app['orm.em']
        ->getRepository('Application\Entity\SettingEntity')
        ->findAll()
    ;

    if ($settingsCollection) {
        $settingsArray = [];

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
}, Application::EARLY_EVENT);

/*** Set Logut path ***/
$app->before(function (Request $request, Application $app) {
    $app['logoutUrl'] = $app['url_generator']
        ->generate('members-area.logout').
        '?_csrf_token='.
        $app['csrf.token_manager']->getToken('logout')
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
