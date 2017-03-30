<?php
namespace Fabrication\Tests;

//------------------------------------------------------------------------------
// Project.
//------------------------------------------------------------------------------
define('PROJECT_ROOT_DIR',			realpath(dirname(dirname(__FILE__))));
define('PROJECT_NAME',				'Project Testing');
define('PROJECT_DOMAIN',			'project-testing');
define('PROJECT_DATABASE',			'project-testing');

//------------------------------------------------------------------------------
// Framework.
//------------------------------------------------------------------------------
define('FRAMEWORK_ROOT_DIR',		realpath(PROJECT_ROOT_DIR . '/../project-fabrication-framework'));
define('FRAMEWORK_DISPATCHER',      0);

include dirname(__FILE__) . '/../library/Fabrication/bootstrap.php';
