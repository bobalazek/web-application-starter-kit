<?php

use Application\Doctrine\ORM\DoctrineManagerRegistry;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator;
use Symfony\Component\Translation\Loader\YamlFileLoader as TranslationYamlFileLoader;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
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
$app['environment'] = getenv('APPLICATION_ENVIRONMENT') ?: 'dev';

/* Environment Config */
if (file_exists(APP_DIR.'/configs/environments/'.$app['environment'].'.php')) {
    $app->register(
        new Igorw\Silex\ConfigServiceProvider(
            APP_DIR.'/configs/environments/'.$app['environment'].'.php'
        )
    );
}

/***** Session *****/
$app->register(new Silex\Provider\SessionServiceProvider(), [
    'session.storage.save_path' => STORAGE_DIR.'/sessions',
]);

/* Flashbag */
$app['flashbag'] = function ($app) {
    return $app['session']->getFlashBag();
};

/***** Http Cache *****/
$app->register(new Silex\Provider\HttpCacheServiceProvider(), [
    'http_cache.cache_dir' => STORAGE_DIR.'/cache/http/',
]);

/***** Locale ******/
$app->register(new Silex\Provider\LocaleServiceProvider());

/***** Translation *****/
$app->register(new Silex\Provider\TranslationServiceProvider(), [
    'locale' => 'en_US',
    'locale_fallbacks' => ['en_US'],
]);

$app->extend('translator', function ($translator, $app) {
    $translator->addLoader('yaml', new TranslationYamlFileLoader());

    foreach (array_keys($app['locales']) as $locale) {
        $localeMessagesFile = APP_DIR.'/locales/'.$locale.'/messages.yml';
        if (file_exists($localeMessagesFile)) {
            $translator->addResource(
                'yaml',
                $localeMessagesFile,
                $locale
            );
        }

        $localeValidatorsFile = APP_DIR.'/locales/'.$locale.'/validators.yml';
        if (file_exists($localeValidatorsFile)) {
            $translator->addResource(
                'yaml',
                $localeValidatorsFile,
                $locale,
                'validators'
            );
        }
    }

    return $translator;
});

/***** Form *****/
$app->register(new Silex\Provider\FormServiceProvider());

/***** Http Fragment *****/
$app->register(new Silex\Provider\HttpFragmentServiceProvider());

/***** Service controller *****/
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

/***** Twig / Templating Engine *****/
$app->register(
    new Silex\Provider\TwigServiceProvider(),
    [
        'twig.path' => APP_DIR.'/templates',
        'twig.form.templates' => [
            'twig/form.html.twig',
        ],
        'twig.options' => [
            'cache' => STORAGE_DIR.'/cache/template',
            'debug' => $app['debug'],
        ],
    ]
);

/*** Twig Extensions ***/
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    $twig->addExtension(new Application\Twig\DateExtension($app));
    $twig->addExtension(new Application\Twig\FormExtension($app));
    $twig->addExtension(new Application\Twig\FileExtension($app));
    $twig->addExtension(new Application\Twig\UiExtension($app));
    $twig->addExtension(new Application\Twig\PaginatorExtension($app));
    $twig->addExtension(
        new Cocur\Slugify\Bridge\Twig\SlugifyExtension(
            Cocur\Slugify\Slugify::create()
        )
    );

    return $twig;
});

/***** Doctrine Database & Doctrine ORM *****/
if (
    isset($app['database_options']) &&
    is_array($app['database_options'])
) {
    $app->register(
        new Silex\Provider\DoctrineServiceProvider(),
        [
            'dbs.options' => $app['database_options'],
        ]
    );

    $app->register(
        new Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider(),
        [
            'orm.em.options' => [
                'mappings' => [
                    [
                        'type' => 'annotation',
                        'namespace' => 'Application\Entity',
                        'path' => SRC_DIR.'/Application/Entity',
                        'use_simple_annotation_reader' => false,
                    ],
                ],
            ],
            'orm.custom.functions.string' => [
                'cast' => 'Oro\ORM\Query\AST\Functions\Cast',
                'group_concat' => 'Oro\ORM\Query\AST\Functions\String\GroupConcat',
            ],
            'orm.custom.functions.datetime' => [
                'date' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'time' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'timestamp' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'convert_tz' => 'Oro\ORM\Query\AST\Functions\DateTime\ConvertTz',
            ],
            'orm.custom.functions.numeric' => [
                'timestampdiff' => 'Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff',
                'dayofyear' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'dayofweek' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'week' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'day' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'hour' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'minute' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'month' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'quarter' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'second' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'year' => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                'sign' => 'Oro\ORM\Query\AST\Functions\Numeric\Sign',
                'pow' => 'Oro\ORM\Query\AST\Functions\Numeric\Pow',
            ],
        ]
    );

    AnnotationRegistry::registerLoader(
        [
            require VENDOR_DIR.'/autoload.php',
            'loadClass',
        ]
    );

    $entityManagerConfig = Setup::createAnnotationMetadataConfiguration(
        [APP_DIR.'/src/Application/Entity'],
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
            ['default' => $app['orm.em']->getConnection()],
            ['default' => $app['orm.em']]
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

/***** CSRF *****/
$app->register(new Silex\Provider\CsrfServiceProvider());

/***** Validator *****/
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['validator.mapping.class_metadata_factory'] = function ($app) {
    return new LazyLoadingMetadataFactory(
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

$app['validator.validator_service_ids'] = [
    'doctrine.orm.validator.unique' => 'validator.unique_entity',
    'security.validator.user_password' => 'security.validator.user_password',
];

/***** Routing *****/
$app->register(new Silex\Provider\RoutingServiceProvider());

/***** Http Cache *****/
$app->register(new Silex\Provider\HttpCacheServiceProvider(), [
    'http_cache.cache_dir' => STORAGE_DIR.'/cache/http/',
]);

/***** Users Provider *****/
$app['users.provider'] = function () use ($app) {
    return new Application\Provider\UsersProvider($app);
};

/***** Security *****/
$securityFirewalls = [];

/*** Members Area ***/
$securityFirewalls['members-area'] = [
    'pattern' => '^/',
    'anonymous' => true,
    'form' => [
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
    ],
    'logout' => [
        'logout_path' => '/members-area/logout',
        'target' => '/members-area',
        'invalidate_session' => true,
        'csrf_parameter' => 'csrf_token',
    ],
    'remember_me' => $app['remember_me_options'],
    'switch_user' => [
        'parameter' => 'switch_user',
        'role' => 'ROLE_ALLOWED_TO_SWITCH',
    ],
    'users' => $app['users.provider'],
];

$app->register(
    new Silex\Provider\SecurityServiceProvider(),
    [
        'security.firewalls' => $securityFirewalls,
    ]
);

$app['security.access_rules'] = [
    ['^/members-area/login', 'IS_AUTHENTICATED_ANONYMOUSLY'],
    ['^/members-area/register', 'IS_AUTHENTICATED_ANONYMOUSLY'],
    ['^/members-area/reset-password', 'IS_AUTHENTICATED_ANONYMOUSLY'],
    ['^/members-area', 'ROLE_USER'],
];

$app['security.role_hierarchy'] = [
    'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH'],
    'ROLE_ADMIN' => ['ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH'],
];

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
$app['mailer.css_to_inline_styles_converter'] = $app->protect(function ($twigTemplatePathOrContent, $twigTemplateData = [], $isTwigTemplate = true) use ($app) {
    $emogrifier = new \Pelago\Emogrifier();
    $emogrifier->setHtml(
        $isTwigTemplate
        ? $app['twig']->render($twigTemplatePathOrContent, $twigTemplateData)
        : $app['twig']->render(
            'emails/blank.html.twig',
            array_merge(
                $twigTemplateData,
                [
                    'content' => $twigTemplatePathOrContent,
                ]
            )
        )
    );

    return $emogrifier->emogrify();
});

/***** Application *****/
/*** Translator ***/
$app['application.translator'] = function ($app) {
    return new Application\Translator($app);
};

/*** Mailer ***/
$app['application.mailer'] = function ($app) {
    return new Application\Mailer($app);
};

/*** Paginator ***/
$app['application.paginator'] = function ($app) {
    return new Application\Paginator($app);
};

/*** Server Info ***/
$app['application.server_info'] = function ($app) {
    return new Application\ServerInfo($app);
};

/***** Listeners *****/
if (isset($app['orm.em'])) {
    $app->register(
        new Application\Provider\EventsProvider()
    );
}

/***** Profiler *****/
if ($app['show_profiler']) {
    $app->register(new Silex\Provider\WebProfilerServiceProvider(), [
        'profiler.cache_dir' => STORAGE_DIR.'/cache/profiler',
        'profiler.mount_prefix' => '/_profiler',
    ]);
}
