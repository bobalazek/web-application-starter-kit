<?php

use Application\Doctrine\ORM\DoctrineManagerRegistry;
use Application\EventListener;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator;
use Symfony\Component\Translation\Loader\YamlFileLoader as TranslationYamlFileLoader;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\YamlFileLoader as MappingYamlFileLoader;

/***** Config *****/
if (!file_exists(APP_DIR.'/configs/global.php')) {
    throw new \Exception('No global config file found. Please create one (app/configs/global.php)!');
}

$app->register(
    new Igorw\Silex\ConfigServiceProvider(
        APP_DIR.'/configs/global.php'
    )
);

if (file_exists(APP_DIR.'/configs/global-local.php')) {
    $app->register(
        new Igorw\Silex\ConfigServiceProvider(
                APP_DIR.'/configs/global-local.php'
        )
    );
}

/* Environment */
$app['environment'] = getenv('APPLICATION_ENVIRONMENT') ?: 'development';

/* Environment Config */
if (file_exists(APP_DIR.'/configs/environments/'.$app['environment'].'.php')) {
    $app->register(
        new Igorw\Silex\ConfigServiceProvider(
            APP_DIR.'/configs/environments/'.$app['environment'].'.php'
        )
    );
}

/* Environment variables */
if (getenv('APPLICATION_DATABASE_PASSWORD')) {
    $app['database_options']['default']['password'] = getenv('APPLICATION_DATABASE_PASSWORD');
}

/***** Session *****/
$app->register(new Silex\Provider\SessionServiceProvider(), array(
    'session.storage.save_path' => STORAGE_DIR.'/sessions',
));

/* Flashbag */
$app['flashbag'] = function ($app) {
    return $app['session']->getFlashBag();
};

/***** Http Cache *****/
$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => STORAGE_DIR.'/cache/http/',
));

/***** Translation *****/
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallback' => 'en_US',
));

$app['translator']->addLoader(
    'yaml',
    new TranslationYamlFileLoader()
);

/*** Application Translator ***/
$app['application.translator'] = function ($app) {
    return new Application\Translator($app);
};

/*** Application Mailer ***/
$app['application.mailer'] = function ($app) {
    return new Application\Mailer($app);
};

/*** Paginator ***/
$app['application.paginator'] = function ($app) {
    return new Application\Paginator($app);
};

/***** Form *****/
$app->register(new Silex\Provider\FormServiceProvider());

/***** Twig / Templating Engine *****/
$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path' => APP_DIR.'/templates',
        'twig.form.templates' => array(
            'twig/form.html.twig',
        ),
        'twig.options' => array(
            'cache' => STORAGE_DIR.'/cache/template',
            'debug' => $app['debug'],
        ),
    )
);

/*** Twig Extensions ***/
$app['twig'] = $app->share($app->extend('twig', function (\Twig_Environment $twig, $app) {
    $twig->addExtension(new Application\Twig\PaginatorExtension($app));
    $twig->addExtension(new Application\Twig\DateExtension());
    $twig->addExtension(new Application\Twig\FormExtension());
    $twig->addExtension(new Application\Twig\FileExtension());
    $twig->addExtension(new Application\Twig\UiExtension());
    $twig->addExtension(
        new Cocur\Slugify\Bridge\Twig\SlugifyExtension(
            Cocur\Slugify\Slugify::create()
        )
    );

    return $twig;
}));

/***** Doctrine Database & Doctrine ORM *****/
if (
    isset($app['database_options']) &&
    is_array($app['database_options'])
) {
    $app->register(
        new Silex\Provider\DoctrineServiceProvider(),
        array(
            'dbs.options' => $app['database_options'],
        )
    );

    $app->register(
        new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(),
        array(
            'orm.em.options' => array(
                'mappings' => array(
                    array(
                        'type' => 'annotation',
                        'namespace' => 'Application\Entity',
                        'path' => SRC_DIR.'/Application/Entity',
                        'use_simple_annotation_reader' => false,
                    ),
                ),
            ),
        )
    );

    AnnotationRegistry::registerLoader(
        array(
            require VENDOR_DIR.'/autoload.php',
            'loadClass',
        )
    );

    $entityManagerConfig = Setup::createAnnotationMetadataConfiguration(
        array(APP_DIR.'/src/Application/Entity'),
        $app['debug']
    );

    $entityManager = EntityManager::create(
        $app['dbs.options']['default'],
        $entityManagerConfig
    );

    PersistentObject::setObjectManager(
        $entityManager
    );

    $app['orm.proxies_dir'] = STORAGE_DIR.'/cache/proxy';

    $app['orm.manager_registry'] = function ($app) {
        return new DoctrineManagerRegistry(
            'manager_registry',
            array('default' => $app['orm.em']->getConnection()),
            array('default' => $app['orm.em'])
        );
    };

    $app['form.extensions'] = $app->extend(
        'form.extensions',
        function ($extensions) use ($app) {
            $extensions[] = new DoctrineOrmExtension(
                $app['orm.manager_registry']
            );

            return $extensions;
        }
    );
}

/***** Validator *****/
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['validator.mapping.class_metadata_factory'] = function ($app) {
    return new ClassMetadataFactory(
        new MappingYamlFileLoader(
            APP_DIR.'/configs/validation.yml'
        )
    );
};

$app['validator.unique_entity'] = function ($app) {
    return new UniqueEntityValidator(
        $app['orm.manager_registry']
    );
};

$app['security.validator.user_password'] = function ($app) {
    return new UserPasswordValidator(
        $app['security'],
        $app['security.encoder_factory']
    );
};

$app['validator.validator_service_ids'] = array(
    'doctrine.orm.validator.unique' => 'validator.unique_entity',
    'security.validator.user_password' => 'security.validator.user_password',
);

/***** Url Generator *****/
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/***** Http Cache *****/
$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => STORAGE_DIR.'/cache/http/',
));

/***** Users Provider *****/
$app['users.provider'] = function () use ($app) {
    return new Application\Provider\UsersProvider($app);
};

/***** Security *****/
$securityFirewalls = array();

/*** Members Area ***/
$securityFirewalls['members-area'] = array(
    'pattern' => '^/',
    'anonymous' => true,
    'form' => array(
        'login_path' => '/members-area/login',
        'check_path' => '/members-area/login/check',
        'failure_path' => '/members-area/login',
        'default_target_path' => '/members-area',
        'use_referer' => true,
        'username_parameter' => 'username',
        'password_parameter' => 'password',
        'csrf_protection' => true,
        'csrf_parameter' => 'csrf_token',
        'with_csrf' => true,
        'use_referer' => true,
    ),
    'logout' => array(
        'logout_path' => '/members-area/logout',
        'target' => '/members-area',
        'invalidate_session' => true,
        'csrf_parameter' => 'csrf_token',
    ),
    'remember_me' => $app['remember_me_options'],
    'switch_user' => array(
        'parameter' => 'switch_user',
        'role' => 'ROLE_ALLOWED_TO_SWITCH',
    ),
    'users' => $app['users.provider'],
);

$app->register(
    new Silex\Provider\SecurityServiceProvider(),
    array(
        'security.firewalls' => $securityFirewalls,
    )
);

$app['security.access_rules'] = array(
    array('^/members-area/login', 'IS_AUTHENTICATED_ANONYMOUSLY'),
    array('^/members-area/register', 'IS_AUTHENTICATED_ANONYMOUSLY'),
    array('^/members-area/reset-password', 'IS_AUTHENTICATED_ANONYMOUSLY'),
    array('^/members-area', 'ROLE_USER'),
    array('^/members-area/oauth', 'ROLE_USER'),
);

$app['security.role_hierarchy'] = array(
    'ROLE_SUPER_ADMIN' => array('ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH'),
    'ROLE_ADMIN' => array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH'),
);

/* Voters */
$app['security.voters'] = $app->extend(
    'security.voters',
    function ($voters) {
        // Add your own voters here!

        return $voters;
    }
);

/***** Remember Me *****/
$app->register(new Silex\Provider\RememberMeServiceProvider());

/***** Swiftmailer / Mailer *****/
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app['swiftmailer.options'] = $app['swiftmailer_options'];

/* Emogrifier */
$app['mailer.css_to_inline_styles_converter'] = $app->protect(function ($twigTemplatePathOrContent, $twigTemplateData = array(), $isTwigTemplate = true) use ($app) {
    $emogrifier = new \Pelago\Emogrifier();
    $emogrifier->setHtml(
        $isTwigTemplate
        ? $app['twig']->render($twigTemplatePathOrContent, $twigTemplateData)
        : $app['twig']->render(
            'emails/blank.html.twig',
            array_merge(
                $twigTemplateData,
                array(
                    'content' => $twigTemplatePathOrContent,
                )
            )
        )
    );

    return $emogrifier->emogrify();
});

/*** Profiler ***/
if ($app['show_profiler']) {
    $app->register(new Silex\Provider\HttpFragmentServiceProvider());
    $app->register(new Silex\Provider\ServiceControllerServiceProvider());
    $app->register(new Silex\Provider\WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => STORAGE_DIR.'/cache/profiler',
        'profiler.mount_prefix' => '/_profiler',
    ));
}

/*** Listeners ***/
if (isset($app['orm.em'])) {
    $app['dispatcher']->addSubscriber(
        new EventListener\AuthenticationEventsListener($app)
    );

    $app['dispatcher']->addSubscriber(
        new EventListener\SecurityEventsListener($app)
    );
}
