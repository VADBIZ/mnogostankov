<?php

/*
 * OpenCart CLI  - v1.0.0.2 (20/07/2021)
 * Require OpenCart 3.0.x.x, 2.0.x.x - 2.3.x.x
 *
 * Email: support@costaslabs.com
 * Website: http://ocmod.costaslabs.com
 *
 */

// App Name
define('CLI_APPLICATION', 'csvprice_pro_cron');

if (file_exists(dirname(__FILE__) . '/csvprice_pro_cron_config.php')) {
    include_once(dirname(__FILE__) . '/csvprice_pro_cron_config.php');
}

if (!defined('OPENCART_ADMIN_DIR')) {
    define('OPENCART_ADMIN_DIR', dirname(__DIR__) . '/admin');
}


if (!defined('CLI_APPLICATION_DEBUG')) {
    define('CLI_APPLICATION_DEBUG', 0);
}

$admin_dir = getOpenCartAdminDirectory();

// Config file
require_once($admin_dir . '/config.php');

// Get OpenCart version
$version = getOpenCartVersion($admin_dir);
define('VERSION', $version);

if (version_compare(VERSION, '2.0.0.0') <= 0) {
    die("Error: CLI OpenCart version is not supported");
}

if (!isset($_SERVER['SERVER_PORT'])) {
    $_SERVER['SERVER_PORT'] = 80;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Set debug options
if (CLI_APPLICATION_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
if (version_compare(VERSION, '2.2.0.0') >= 0) {
    $config->load('default');
    $config->load('admin');
}
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0'");
foreach ($query->rows as $setting) {
    if (!$setting['serialized']) {
        $config->set($setting['key'], $setting['value']);
    } else {
        if (version_compare(VERSION, '2.1.0.0') >= 0) {
            $config->set($setting['key'], json_decode($setting['value'], true));
        } else {
            $config->set($setting['key'], unserialize($setting['value']));
        }
    }
}

// Url
if (version_compare(VERSION, '3.0.0.0') >= 0) {
    if ($config->get('url_autostart')) {
        $registry->set('url', new Url($config->get('site_url'), $config->get('site_ssl')));
    }
} else {
    $url = new Url(HTTP_SERVER, $config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER);
    $registry->set('url', $url);
}

// log File
$log = new Log(CLI_APPLICATION . '.log');
$registry->set('log', $log);

// Added from 3.0.0.0
if (version_compare(VERSION, '3.0.0.0') >= 0) {
    date_default_timezone_set($config->get('date_timezone'));
}

// Error handler
set_error_handler(function ($code, $message, $file, $line) use ($log, $config) {
    // error suppressed with @
    if (error_reporting() === 0) {
        return false;
    }
    switch ($code) {
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'Notice';
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $error = 'Warning';
            break;
        case E_Error:
        case E_USER_Error:
            $error = 'Fatal Error';
            break;
        default:
            $error = 'Unknown';
            break;
    }
    if ($config->get('error_display')) {
        echo '<b>' . $error . '</b>: ' . $message . ' in <b>' . $file . '</b> on line <b>' . $line . '</b>';
    }
    if ($config->get('error_log')) {
        $log->write('PHP ' . $error . ':  ' . $message . ' in ' . $file . ' on line ' . $line);
    }
    return true;
});

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$registry->set('response', $response);

// Cache
if (version_compare(VERSION, '3.0.0.0') >= 0) {
    $registry->set('cache', new Cache($config->get('cache_engine'), $config->get('cache_expire')));
} elseif (version_compare(VERSION, '2.2.0.0') >= 0) {
    $registry->set('cache', new Cache($config->get('cache_type'), $config->get('cache_expire')));
} else {
    $registry->set('cache', new Cache('file'));
}

// Session
if (version_compare(VERSION, '3.0.0.0') >= 0) {
    // Session 3.0.2.0
    $session = new Session($config->get('session_engine'), $registry);
    $registry->set('session', $session);
    if ($config->get('session_autostart')) {
        if (isset($_COOKIE[$config->get('session_name')])) {
            $session_id = $_COOKIE[$config->get('session_name')];
        } else {
            $session_id = '';
        }
        $session->start($session_id);
        setcookie($config->get('session_name'), $session->getId(), ini_get('session.cookie_lifetime'), ini_get('session.cookie_path'), ini_get('session.cookie_domain'));
    }
} else {
    // Session 2.3.0.0
    if ($config->get('session_autostart')) {
        $session = new Session();
        $session->start();
        $registry->set('session', $session);
    }
}

// Language config
$languages = array();

$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language`");

foreach ($query->rows as $result) {
    $languages[$result['code']] = $result;
}
$config->set('config_language_id', $languages[$config->get('config_admin_language')]['language_id']);

// Language registry
$language = new Language($languages[$config->get('config_admin_language')]['directory']);
$language->load($languages[$config->get('config_admin_language')]['directory']);

$registry->set('language', $language);

// Default Store
$config->set('config_store_id', 0);

if (version_compare(VERSION, '3.0.0.0') >= 0) {
    // Document
    $registry->set('document', new Document());
    // OpenBay Pro
    //$registry->set('openbay', new Openbay($registry));
}

// Event
$event = new Event($registry);
$registry->set('event', $event);

if (version_compare(VERSION, '3.0.0.0') >= 0) {
    // Event Register
    if ($config->has('action_event')) {
        foreach ($config->get('action_event') as $key => $value) {
            foreach ($value as $priority => $action) {
                $event->register($key, new Action($action), $priority);
            }
        }
    }
    // Route
    $controller = new Router($registry);
} else {
    // Event
    $event = new Event($registry);
    $registry->set('event', $event);

    $query = $db->query("SELECT * FROM " . DB_PREFIX . "event WHERE `status` = 1");

    foreach ($query->rows as $result) {
        $event->register($result['trigger'], new Action($result['action']));
    }
    // Front Controller
    $controller = new Front($registry);
}

// Dispatch
$controller->dispatch(new Action('csvprice_pro/app_cli'), new Action('error/not_found'));

// Output
$response->output();


// Get OpenCart directory
function getOpenCartAdminDirectory()
{
    $root_dir = realpath(str_replace(array('csvprice_pro_cli.php', 'cli'), array('', ''), dirname(__FILE__)));

    // Admin directory
    $admin_dir = '';
    if (file_exists($root_dir . '/admin/config.php')) {
        $admin_dir = $root_dir . '/admin';
    } else if(file_exists($root_dir) && is_dir($root_dir)) {
        foreach (new DirectoryIterator($root_dir) as $dir_info) {
            if (!$dir_info->isDot() && $dir_info->isDir()) {
                $path = $dir_info->getPathname();
                if (file_exists($path . '/config.php')) {
                    $admin_dir = $path;
                    break;
                }
            }
        }
    }

    if (empty($admin_dir)) {
        if (file_exists(OPENCART_ADMIN_DIR . '/config.php')) {
            $admin_dir = OPENCART_ADMIN_DIR;
        }
    }

    if (!$admin_dir) {
        die('Error: CLI cannot access to config.php');
    }

    return $admin_dir;
}

// Get OpenCart version
function getOpenCartVersion($admin_dir = '')
{

    if (!$admin_dir)
        die('Error: CLI cannot access to index.php');

    $index_content = file_get_contents($admin_dir . '/index.php');

    $matches = array();

    preg_match('~define\s*\(\s*\'VERSION\'\s*,\s*\'(.*?)\'\s*\)\s*;~', $index_content, $matches);

    if (!empty($matches[1])) {
        return $matches[1];
    } else {
        die('Error: Cannot find OpenCart version.');
    }
}

?>