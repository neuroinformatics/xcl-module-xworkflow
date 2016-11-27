<?php

namespace Xworkflow\Core;

/**
 * xoops utility class.
 */
class XoopsUtils
{
    const UID_GUEST = 0;

    /**
     * handlers cache.
     *
     * @var array
     */
    private static $mHandlers = array();

    /**
     * configs cache.
     *
     * @var array
     */
    private static $mConfigs = array();

    /**
     * user membership cache.
     *
     * @var array
     */
    private static $mGroupIds = array();

    /**
     * get trust dirname.
     *
     * @return string
     */
    public static function getTrustDirname()
    {
        return basename(dirname(dirname(__DIR__)));
    }

    /**
     * get trust dirname by dirname.
     *
     * @param string $dirname
     *
     * @return string
     */
    public static function getTrustDirnameByDirname($dirname)
    {
        $handler = &xoops_gethandler('module');
        $module = &$handler->getByDirname($dirname);
        if (is_object($module)) {
            $ret = $module->get('trust_dirname');
            if ($ret !== false) {
                return $ret;
            }
        }

        return null;
    }

    /**
     * format page title.
     *
     * @param string $moduleName
     * @param string $pageTitle
     * @param string $action
     *
     * @return string
     **/
    public static function formatPagetitle($moduleName, $pageTitle, $action)
    {
        $format = self::getModuleConfig('legacyRender', 'pagetitle');
        if (is_null($format)) {
            $format = '{modulename} {action} [pagetitle]:[/pagetitle] {pagetitle}';
        }
        $search = array('{modulename}', '{pagetitle}', '{action}');
        $replace = array($moduleName, $pageTitle, $action);
        $ret = str_replace($search, $replace, $format);
        $ret = preg_replace('/\[modulename\](.*)\[\/modulename\]/U', (empty($moduleName) ? '' : '$1'), $ret);
        $ret = preg_replace('/\[pagetitle\](.*)\[\/pagetitle\]/U', (empty($pageTitle) ? '' : '$1'), $ret);
        $ret = preg_replace('/\[action\](.*)\[\/action\]/U', (empty($action) ? '' : '$1'), $ret);

        return $ret;
    }

    /**
     * get module handler.
     *
     * @param string $name
     * @param string $dirname
     *
     * @return Handler\AbstractHandler&
     */
    public static function &getModuleHandler($name, $dirname)
    {
        $key = $dirname.':'.$name;
        if (!array_key_exists($key, self::$mHandlers)) {
            $db = &\XoopsDatabaseFactory::getDatabaseConnection();
            $trustDirname = self::getTrustDirnameByDirname($dirname);
            if (isset($trustDirname)) {
                $className = ucfirst($trustDirname).'\\Handler\\'.ucfirst($name).'Handler';
                if (!class_exists($className)) {
                    $fpath = XOOPS_TRUST_PATH.'/modules/'.$trustDirname.'/class/handler/'.ucfirst($name).'.class.php';
                    require_once $fpath;
                    $className = ucfirst($trustDirname).'_'.ucfirst($name).'Handler';
                }
                self::$mHandlers[$key] = new $className($db, $dirname);
            } else {
                self::$mHandlers[$key] = xoops_getmodulehandler($name, $dirname);
            }
        }

        return self::$mHandlers[$key];
    }

    /**
     * get xoops config.
     *
     * @param string $key
     * @param string $catId
     *
     * @return mixed
     */
    public static function getXoopsConfig($key, $catId = XOOPS_CONF)
    {
        $configHandler = &xoops_gethandler('config');
        $configArr = $configHandler->getConfigsByCat($catId);
        if (defined('XOOPS_CUBE_LEGACY')) {
            switch ($catId) {
            case XOOPS_CONF:
                static $keysMap = array(
                    'user' => array('avatar_minposts', 'maxuname', 'sslloginlink', 'sslpost_name', 'use_ssl', 'usercookie'),
                    'legacyRender' => array('banners'),
                );
                foreach ($keysMap as $dirname => $keys) {
                    foreach ($keys as $key) {
                        $configArr[$key] = self::getModuleConfig($dirname, $key);
                    }
                }
                break;
            case XOOPS_CONF_USER:
            case XOOPS_CONF_METAFOOTER:
                static $configDirname = array(
                    XOOPS_CONF_USER => 'user',
                    XOOPS_CONF_METAFOOTER => 'legacyRender',
                );
                $dirname = $configDirname[$catId];
                if (!array_key_exists($dirname, self::$mConfigs)) {
                    self::$mConfigs[$dirname] = $configHandler->getConfigsByDirname($dirname);
                }
                $configArr = self::$mConfigs[$dirname];
                break;
            }
        }

        return array_key_exists($key, $configArr) ? $configArr[$key] : null;
    }

    /**
     * get module config.
     *
     * @param string $dirname
     * @param string $key
     *
     * @return mixed
     */
    public static function getModuleConfig($dirname, $key)
    {
        if (!array_key_exists($dirname, self::$mConfigs)) {
            $configHandler = &xoops_gethandler('config');
            self::$mConfigs[$dirname] = $configHandler->getConfigsByDirname($dirname);
        }
        if (is_null(self::$mConfigs[$dirname])) {
            // dirname not found
            return null;
        }

        return array_key_exists($key, self::$mConfigs[$dirname]) ? self::$mConfigs[$dirname][$key] : null;
    }

    /**
     * render uri.
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $dataId
     * @param string $action
     * @param string $query
     *
     * @return string
     **/
    public static function renderUri($dirname, $dataname = null, $dataId = 0, $action = null, $query = null)
    {
        $uri = null;
        if (self::getXoopsConfig('cool_uri') == true) {
            if (isset($dataname)) {
                if ($dataId > 0) {
                    if (isset($action)) {
                        $uri = sprintf('/%s/%s/%d/%s', $dirname, $dataname, $dataId, $action);
                    } else {
                        $uri = sprintf('/%s/%s/%d', $dirname, $dataname, $dataId);
                    }
                } else {
                    if (isset($action)) {
                        $uri = sprintf('/%s/%s/%s', $dirname, $dataname, $action);
                    } else {
                        $uri = sprintf('/%s/%s', $dirname, $dataname);
                    }
                }
            } else {
                if ($dataId > 0) {
                    if (isset($action)) {
                        die();
                    } else {
                        $uri = sprintf('/%s/%d', $dirname, $dataId);
                    }
                } else {
                    if (isset($action)) {
                        die();
                    } else {
                        $uri = '/'.$dirname;
                    }
                }
            }
            $uri = (isset($query)) ? XOOPS_URL.$uri.'?'.$query : XOOPS_URL.$uri;
        } else {
            \XCube_DelegateUtils::call('Module.'.$dirname.'.Global.Event.GetNormalUri', new \XCube_Ref($uri), $dirname, $dataname, $dataId, $action, $query);
            $uri = XOOPS_MODULE_URL.$uri;
        }

        return $uri;
    }

    /**
     * get current user id.
     *
     * @return int user id
     */
    public static function getUid()
    {
        global $xoopsUser;

        return is_object($xoopsUser) ? intval($xoopsUser->get('uid')) : self::UID_GUEST;
    }

    /**
     * get module version.
     *
     * @param string $dirname
     *
     * @return float version
     */
    public static function getModuleVersion($dirname)
    {
        $moduleHandler = &xoops_gethandler('module');
        $moduleObj = &$moduleHandler->getByDirname($dirname);
        if (!is_object($moduleObj)) {
            return false;
        }

        return $moduleObj->get('version');
    }

    /**
     * check whether user is administraotr.
     *
     * @param int    $uid
     * @param string $dirname
     *
     * @return bool
     */
    public static function isAdmin($uid, $dirname)
    {
        return self::isSiteAdmin($uid) || self::isModuleAdmin($uid, $dirname);
    }

    /**
     * check whether user is site administraotr.
     *
     * @param int $uid
     *
     * @return bool
     */
    public static function isSiteAdmin($uid)
    {
        if (!array_key_exists($uid, self::$mGroupIds)) {
            $memberHandler = &xoops_gethandler('member');
            self::$mGroupIds[$uid] = $memberHandler->getGroupsByUser($uid, false);
        }

        return in_array(XOOPS_GROUP_ADMIN, self::$mGroupIds[$uid]);
    }

    /**
     * check whether user is site administraotr.
     *
     * @param int    $uid
     * @param string $dirname
     *
     * @return bool
     */
    public static function isModuleAdmin($uid, $dirname)
    {
        $moduleHandler = &xoops_gethandler('module');
        $moduleObj = &$moduleHandler->getByDirname($dirname);
        if (!is_object($moduleObj)) {
            return false;
        }
        $mid = $moduleObj->get('mid');
        if (!array_key_exists($uid, self::$mGroupIds)) {
            $memberHandler = &xoops_gethandler('member');
            self::$mGroupIds[$uid] = $memberHandler->getGroupsByUser($uid, false);
        }
        $gpermHandler = &xoops_gethandler('groupperm');

        return $gpermHandler->checkRight('module_admin', $mid, self::$mGroupIds[$uid], 1, true);
    }
}
