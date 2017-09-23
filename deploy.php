<?php

namespace Deployer;

require 'recipe/composer.php';

include dirname(__FILE__).'/deployer/config.php';
include dirname(__FILE__).'/deployer/tasks.php';
include dirname(__FILE__).'/deployer/hosts.php';

// Include the local stuff if present
if (file_exists(dirname(__FILE__).'/deployer/local.php')) {
    include dirname(__FILE__).'/deployer/local.php';
}
