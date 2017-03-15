<?php
namespace Fabrication;

/**
 * Fabrication Bootstrap
 *
 * Bootstrapping or booting refers to a group of metaphors that share a common
 * meaning: a self-sustaining process that proceeds by itself.
 *
 * @author  David Stevens <mail.davro@gmail.com>
 */
define('WORKSPACE', 1);             // Workspace (framework application)
define('WORKSPACE_REMOTE', 1);      // Workspace (login/registration system)
define('WORKSPACE_ADMIN', 'root')  ; // Workspace (unix username)

if (php_sapi_name() != 'cli' && isset($_SERVER['HTTP_HOST'])) {
    if ($_SERVER['HTTP_HOST'] == PROJECT_DOMAIN || $_SERVER['HTTP_HOST'] == 'www.'.PROJECT_DOMAIN) {
        define('WORKSPACE_ENVIRONMENT', 'prod');
    } else {
        define('WORKSPACE_ENVIRONMENT', 'dev');
    }
} else {
    define('WORKSPACE_ENVIRONMENT', 'dev');
}

// Define project hostname based on the environment.
if (WORKSPACE_ENVIRONMENT == 'prod') {
    define('PROJECT_HOSTNAME', PROJECT_DOMAIN);
} else {
    define('PROJECT_HOSTNAME', 'project-' . PROJECT_DOMAIN);
}
//------------------------------------------------------------------------------
// Template Engine
//------------------------------------------------------------------------------
define('TEMPLATE_ENGINE_ROOT_DIR', realpath(PROJECT_ROOT_DIR . '/../project-fabrication'));
define('TEMPLATE_ENGINE_W3C', 1);

//------------------------------------------------------------------------------
// Framework.
//------------------------------------------------------------------------------
if (!defined('FRAMEWORK_ROOT_DIR')) {
    define('FRAMEWORK_ROOT_DIR', realpath(PROJECT_ROOT_DIR . '/../project-fabrication-framework'));
}
define('FRAMEWORK_ROOT_PHAR', 1);
define('FRAMEWORK_VERSION', '0.0.1');

if (! defined('FRAMEWORK_DISPATCHER')) {
    define('FRAMEWORK_DISPATCHER', 1);
}
define('FRAMEWORK_ENVIRONMENT', WORKSPACE_ENVIRONMENT);

// Bootstrap and Remote settings.
define('FRAMEWORK_BOOTSTRAP', 1);
define('FRAMEWORK_BOOTSTRAP_DEBUG', 0);
define('FRAMEWORK_BOOTSTRAP_DEBUG_EXIT', 1);
define('FRAMEWORK_REMOTE', 1);
define('FRAMEWORK_REMOTE_DEBUG', 1);
define('FRAMEWORK_REMOTE_DEBUG_ALLOW', '127.0.0.1');

// @todo change to be less brittle, handle subdomains.
define('FRAMEWORK_LAYOUT_INHERIT', 1);

// Fabrication Framework Bootstrap
if (FRAMEWORK_BOOTSTRAP) {
    // Register framework autoloading class with spl.
    spl_autoload_register(array('Fabrication\Bootstrap', 'autoloader'));
}

// Classes required for the autoloader bootstrap to setup.
// @todo Only load Debug if in debug enviroment.
require_once(dirname(__FILE__) . '/Debug.php');
require_once(dirname(__FILE__) . '/Configuration.php');
require_once(dirname(__FILE__) . '/utilities/AssetFinder.php');
require_once(dirname(__FILE__) . '/http/Request.php');
require_once(dirname(__FILE__) . '/Specification.php');
require_once(dirname(__FILE__) . '/Fabrication.php');

/**
 * Fabrication bootstrap.
 *
 * @author  David Stevens <mail.davro@gmail.com>
 */
class Bootstrap
{
    /**
     * Fabrication autoloader method registered with the spl autoload register.
     *
     * @param   string  $className  The class name called.
     * @throws  \Exception
     */
    public static function autoloader($className)
    {
        if (! defined('FRAMEWORK_ROOT_DIR')) {
            define('FRAMEWORK_ROOT_DIR', dirname(dirname(__FILE__)));
        }
        
        if (defined('FRAMEWORK_BOOTSTRAP_DEBUG') && FRAMEWORK_BOOTSTRAP_DEBUG) {
            if (defined('FRAMEWORK_BOOTSTRAP_DEBUG_EXIT')
                    && FRAMEWORK_BOOTSTRAP_DEBUG_EXIT) {
                exit;
            }
        }
        try {
            $data = Bootstrap::classPaths();
            
            if (isset($data[$className])) {
                $classPath = $data[$className];
                if (!file_exists($classPath)) {
                    throw new \Exception("Failed to autoload {$className} from cache.");
                }
                require_once($classPath);
            }
        } catch (\Exception $e) {
            //var_dump($e->getMessage());
            exit(0);
        }
    }
    
    /**
     * The class paths of the framework and the current project.
     *
     * List of autoloaded application classes from cache.
     *
     * @return  array   List of known items.
     */
    public static function classPaths()
    {
        $cachePathAutoload = PROJECT_ROOT_DIR . '/cache/applications.autoload.cache.php';
        
        $cache = [];
        if (! file_exists($cachePathAutoload)) {
            Fabrication::getInstance()->context();
        }
        
        $contents = file_get_contents($cachePathAutoload);
        $cache = unserialize($contents);
            
        return $cache;
    }
}

//------------------------------------------------------------------------------
// Framework: Memory Caching
//------------------------------------------------------------------------------
if (isset($_SERVER['REQUEST_URI'])) {
    $requestUri = $_SERVER['HTTP_HOST'] . ':' . $_SERVER['REQUEST_URI'];

    // Setup services and retrive memcache object and serve cache.
    Fabrication::services();

    $memory = Fabrication::getInstance('memory');
    $memoryCache = $memory->get($requestUri);
    
    if ($memoryCache && FRAMEWORK_ENVIRONMENT == Fabrication::environmentProduction) {
        print $memoryCache;
        exit;
    }
}

//------------------------------------------------------------------------------
// Framework: Create Instance and Dispatch
//------------------------------------------------------------------------------
if (defined('FRAMEWORK_DISPATCHER')) {
    if (FRAMEWORK_DISPATCHER) {
        $framework = Fabrication::createInstance();
        $framework->dispatch();
    }
}
