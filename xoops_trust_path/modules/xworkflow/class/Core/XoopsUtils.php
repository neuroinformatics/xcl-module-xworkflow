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
     * get trust dirname path.
     *
     * @return string
     */
    public static function getTrustDirnamePath()
    {
        return XOOPS_TRUST_PATH.'/modules/'.self::getTrustDirname();
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
     * get module constant string.
     *
     * @param string $type
     * @param string $dirname
     * @param string $name
     *
     * @return string
     */
    public static function getModuleConstant($type, $dirname, $name)
    {
        return constant(self::getModuleConstantName($type, $dirname, $name));
    }

    /**
     * get module constant name.
     *
     * @param string $type
     * @param string $dirname
     * @param string $name
     *
     * @return string
     */
    public static function getModuleConstantName($type, $dirname, $name)
    {
        static $mTypes = array(
            'modinfo' => 'MI',
            'admin' => 'AD',
            'main' => 'MD',
            'blocks' => 'MB',
        );

        return '_'.$mTypes[$type].'_'.strtoupper($dirname).'_'.$name;
    }

    /**
     * get module handler.
     *
     * @param string $name
     * @param string $dirname
     *
     * @return &object
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
     * get module configs.
     *
     * @param string $dirname
     * @param string $key
     *
     * @return mixed
     */
    public static function getModuleConfig($dirname, $key)
    {
        $configHandler = &xoops_gethandler('config');
        $configArr = $configHandler->getConfigsByDirname($dirname);

        return $configArr[$key];
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
