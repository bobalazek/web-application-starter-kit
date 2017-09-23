README
======
**Web Application Starter Kit**

[![Build Status](https://travis-ci.org/bobalazek/web-application-starter-kit.svg?branch=master)](https://travis-ci.org/bobalazek/web-application-starter-kit)
[![Latest Stable Version](http://img.shields.io/packagist/v/bobalazek/web-application-starter-kit.svg?style=flat-square)](https://packagist.org/packages/bobalazek/web-application-starter-kit)
[![Total Downloads](http://img.shields.io/packagist/dt/bobalazek/web-application-starter-kit.svg?style=flat-square)](https://packagist.org/packages/bobalazek/web-application-starter-kit)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/bobalazek/web-application-starter-kit)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bobalazek/web-application-starter-kit/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/bobalazek/web-application-starter-kit/?branch=develop)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/26bba6b1-138f-4ef6-8a80-1e512d27bd61/mini.png)](https://insight.sensiolabs.com/projects/26bba6b1-138f-4ef6-8a80-1e512d27bd61)

A simple web application boilerplate for small or mid scale applications. Included:
* Users system
    * Login
    * Logout
    * Register
    * Reset password
    * Basic profile
    * Settings / edit profile
    * Change password
* Basic user roles system - ability to assign roles for each user
* Administration
    * Users
        * View
        * Edit
        * Switch / impersonate
        * Remove
    * User actions
    * Posts
* Tools
    * Email preview - you are able to view the email templates while working on them (without the need of sending test emails to yourself for every change)
    * Database backup - backup and restore your database schema
* Errors / exceptions tracking - save the exceptions / errors directly to the database and / or send emails when an error happened
* Statistics
* Settings

Requirements & Tools & Helpers
-------------------
* PHP > 7.0
* [Composer](https://getcomposer.org/)
* [Bower](http://bower.io/)
* [PHP Coding Standards Fixer](http://cs.sensiolabs.org/) (optional)

Setup / Development
-------------------
* Navigate your your web directory: `cd /var/www`
* Create a new project: `composer create-project bobalazek/web-application-starter-kit myapp --no-scripts`
* Navigate inside the application `cd myapp`
* Configure database (and maybe other stuff if you want) - copy/clone [app/configs/global-local.php.example](https://github.com/bobalazek/web-application-starter-kit/blob/master/app/configs/global-local.php.example) into `app/configs/global-local.php` and set the config there. Alternatively, you can also do the same with [.env.example](https://github.com/bobalazek/web-application-starter-kit/blob/master/.env.example), if you only want to change the basics (database configuration & environment).
* Run the following commands:
    * `composer install`
    * `bin/console orm:schema-tool:update --force` (to install the database schema)
    * `bower update` (to install the front-end dependencies - you will need to install [Bower](http://bower.io/) first - if you haven't already)
    * `bin/console application:database:hydrate-data` (to hydrate some data)
* You are done! Start developing!

Database
-------------------
* We use the Doctrine database
* Navigate to your project directory: `cd /var/www/myapp`
* Check the entities: `bin/console orm:info` (optional)
* Update the schema: `bin/console orm:schema-tool:update --force`
* Database updated!

Deployment
-------------------
* We use [Deployer](https://deployer.org/)
* Set your configuration inside `deployer/config.php` and `deployer/hosts.php`
* Run `dep deploy qa` (or whatever environment you want)
* The app was deployed to your server!

Application name
-------------------
You should replace the name for your actual application inside the following files:

* README.md
* bower.json
* composer.json
* phpunit.xml
* app/configs/global.php

Administrator login
-------------------
With the `bin/console application:database:hydrate-data` command, you will, per default hydrate 2 users (which you can change inside the `app/fixtures/users.php` file):

* Admin User (with admin permissions)
    * Username: `admin` or `admin@myapp.com`
    * Password: `test`
* Test User (with the default user permissions)
    * Username: `test` or `test@myapp.com`
    * Password: `test`

Commands
--------------------
* `bin/console application:environment:prepare` - Will create the global-local.php and development-local.php files (if they do not exist)
* `bin/console application:database:hydrate-data [-r|--remove-existing-data]` - Will hydrate the tables with some basic data, like: 2 users and 6 roles (the `--remove-existing-data` flag will truncate all tables before re-hydrating them)
* `bin/console application:storage:prepare` - Will prepare all the storage (var/) folders, like: cache, logs, sessions, etc.
* `bin/console application:translations:prepare` - Prepares all the untranslated string into a separate (app/locales/{locale}/messages_untranslated.yml) file. Accepts an locale argument (defaults to 'en_US' - usage: `bin/console application:translations:prepare --locale de_DE` or `bin/console application:translations:prepare -l de_DE` )

Other commands
----------------------
* `php-cs-fixer fix .` - if you want your code fixed before each commit. You will need to install [PHP Coding Standards Fixer](http://cs.sensiolabs.org/)

Modules / Components
-------------------
In case you want to create a new component / module in this system, do the following (in this case, the posts inside the members area):

* Create a new Controller Provider (like [src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php) - plural)
    * Bind with the following routes:
        * Overview / list:
            * Route name: [members-area.posts](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L22)
            * Route pattern / url: [(blank)](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L19)
            * Route controller method: [PostsController::indexAction](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L20)
        * New:
            * Route name: [members-area.posts.new](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L28)
            * Route pattern / url: [/new](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L25)
            * Route controller method: [PostsController::newAction](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L26)
        * Detail:
            * Route name: `members-area.posts.detail`
            * Route pattern / url: `/{id}`
            * Route controller method: `PostsController::detailAction`
        * Edit:
            * Route name: [members-area.posts.edit](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L34)
            * Route pattern / url: [/{id}/edit](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L31)
            * Route controller method: [PostsController::editAction](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L32)
        * Remove:
            * Route name: [members-area.posts.remove](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L40)
            * Route pattern / url: [/{id}/remove](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L37)
            * Route controller method: [PostsController::removeAction](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/ControllerProvider/MembersArea/PostsControllerProvider.php#L38)
* Create a new Controller ([src/Application/Controller/MembersArea/PostsController.php](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/Controller/MembersArea/PostsController.php) - plural)
    * With the following methods:
        * [PostsController::listAction](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/Controller/MembersArea/PostsController.php#L22)
        * [PostsController::newAction](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/Controller/MembersArea/PostsController.php#L76)
        * `PostsController::detailAction`
        * [PostsController::editAction](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/Controller/MembersArea/PostsController.php#L143)
        * [PostsController::removeAction](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/Controller/MembersArea/PostsController.php#L217)
* Mount the routes of the Controller Provider to the routes ([app/core/routes.php](https://github.com/bobalazek/web-application-starter-kit/blob/master/app/core/routes.php#L38))
* Create a new Entity ([src/Application/Entity/PostEntity.php](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/Entity/PostEntity.php) - singular)
* Create a new Repository ([src/Application/Repository/PostRepository.php](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/Repository/PostRepository.php) - singular)
* Create a new Form Type ([src/Application/Form/Type/PostType.php](https://github.com/bobalazek/web-application-starter-kit/blob/master/src/Application/Form/Type/PostType.php) - singular)
* Create templates:
    * app/templates/contents/members-area/posts/ (plural)
        * [list.html.twig](https://github.com/bobalazek/web-application-starter-kit/blob/master/app/templates/contents/members-area/posts/list.html.twig)
        * `detail.html.twig`
        * [new.html.twig](https://github.com/bobalazek/web-application-starter-kit/blob/master/app/templates/contents/members-area/posts/new.html.twig)
        * [edit.html.twig](https://github.com/bobalazek/web-application-starter-kit/blob/master/app/templates/contents/members-area/posts/edit.html.twig)
        * [remove.html.twig](https://github.com/bobalazek/web-application-starter-kit/blob/master/app/templates/contents/members-area/posts/remove.html.twig)
        * [_form.html.twig](https://github.com/bobalazek/web-application-starter-kit/blob/master/app/templates/contents/members-area/posts/_form.html.twig) (just include that inside the edit and new template, so you don't need to write the same form twice - if it's more complex)

File structure
----------------------
* app/
    * configs/ => All basic config stuff (+ validation)
    * core/ => The core files such as providers, routes, middlewares and definitions
    * fixtures/ => Used for hydrating the database
    * locales/ => Used for translations
    * templates/ => All twig templates
* bin/
    * console
* src/
    * Application/
        * Command/
        * Controller/
        * ControllerProvider/
        * Doctrine/ => Some Doctrine fixes for Silex
        * Entity/ => All entities / models
        * Form/
        * Provider/
        * Repository/
        * Tool/
        * Twig/
* web/
    * assets/
        * images/
        * javascripts/
        * uploads/ => Used for uploads
        * vendor/ => Bower dependencies
    * index.php

Preview
----------------------

### Login ###
![Dashboard preview](doc/assets/images/login-preview.png)

### Register ###
![Dashboard preview](doc/assets/images/register-preview.png)

### Reset password ###
![Dashboard preview](doc/assets/images/reset-password-preview.png)

### Dashboard ###
![Dashboard preview](doc/assets/images/dashboard-preview.png)

### Profile ###
![Profile preview](doc/assets/images/profile-preview.png)

### Profile settings ###
![Profile settings preview](doc/assets/images/profile-settings-preview.png)

### Statistics ###
![Statistics preview](doc/assets/images/statistics-preview.png)

### Users ###
![Users preview](doc/assets/images/users-preview.png)

### Users edit ###
![Users edit preview](doc/assets/images/users-edit-preview.png)

License
----------------------
Web Application Starter Kit is licensed under the MIT license.
