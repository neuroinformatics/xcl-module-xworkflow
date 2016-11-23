<?php

namespace Xworkflow\Core;

/**
 * static functions.
 */
class Functions
{
    /**
     * get workflow clients.
     *
     * @param string $dirname
     * @param string $dataname
     *
     * @return string
     */
    public static function getClients()
    {
        static $clients = null;
        if ($clients !== null) {
            return $clients;
        }
        $clients = array();
        $list = array();
        \XCube_DelegateUtils::call('Legacy_WorkflowClient.GetClientList', new \XCube_Ref($list));
        foreach ($list as $item) {
            $clients[$item['dirname']][$item['dataname']] = array(
                'label' => isset($item['label']) ? $item['label'] : $item['dataname'],
                'hasGroupAdmin' => isset($item['hasGroupAdmin']) ? $item['hasGroupAdmin'] : false,
            );
        }

        return $clients;
    }

    /**
     * check whether group tables are extended.
     *
     * @return bool
     */
    public static function isExtendedGroup()
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        $db = &\XoopsDatabaseFactory::getDatabaseConnection();
        $table = $db->prefix('groups');
        $sql = sprintf('SHOW COLUMNS FROM `%s` LIKE \'index_id\'', $table);
        $result = $db->queryF($sql);
        if (!$result) {
            return false;
        }
        $cache = ($db->getRowsNum($result) > 0);
        $db->freeRecordSet($result);

        return $cache;
    }

    /**
     * check whether user is group administrator.
     *
     * @param int $uid
     * @param int $gid
     *
     * @return bool
     */
    public static function isGroupAdmin($uid, $gid)
    {
        if (!self::isExtendedGroup()) {
            return false;
        }
        $uids = self::getGroupAdminUserIds($gid);

        return in_array($uid, $uids);
    }

    /**
     * get group admin user ids.
     *
     * @param int $gid
     *
     * @return int[]
     */
    public static function getGroupAdminUserIds($gid)
    {
        $uids = array();
        if (!self::isExtendedGroup()) {
            return $uids;
        }
        if ($gid == 0) {
            return $uids;
        }
        $db = &\XoopsDatabaseFactory::getDatabaseConnection();
        $table = $db->prefix('groups');
        $table2 = $db->prefix('groups_users_link');
        // check group admin
        $sql = sprintf('SELECT `l`.`uid` FROM `%s` AS `l` INNER JOIN `%s` AS `g` ON `l`.`groupid`=`g`.`groupid` WHERE `l`.`activate`=0 AND `l`.`groupid`=%u AND `l`.`is_admin`=1 AND `g`.`activate`=1', $table2, $table, $gid);
        if (!($result = $db->query($sql))) {
            return $uids;
        }
        while ($row = $db->fetchRow($result)) {
            $uids[] = $row[0];
        }
        $db->freeRecordSet($result);

        return $uids;
    }

    /**
     * get admin group ids.
     *
     * @param int $uid
     *
     * @return array
     */
    public static function getAdminGroupIds($uid)
    {
        $gids = array();
        if (!self::isExtendedGroup()) {
            return $gids;
        }
        if ($uid == XoopsUtils::UID_GUEST) {
            return $gids;
        }
        $db = &\XoopsDatabaseFactory::getDatabaseConnection();
        $table = $db->prefix('groups');
        $table2 = $db->prefix('groups_users_link');
        $sql = sprintf('SELECT `g`.`groupid` FROM `%s` AS `g` INNER JOIN `%s` AS `l` ON `g`.`groupid`=`l`.`groupid` WHERE `g`.`activate`=1 AND `l`.`activate`=0 AND `l`.`uid`=%u AND `l`.`is_admin`=1', $table, $table2, $uid);
        if (!($result = $db->query($sql))) {
            return $gids;
        }
        while ($row = $db->fetchRow($result)) {
            $gids[] = $row[0];
        }
        $db->freeRecordSet($result);

        return $gids;
    }

    /**
     * get target group id.
     *
     * @param string $dirname
     * @param string $dataname
     * @param string $target_id
     *
     * @return string
     */
    public static function getTargetGroupId($dirname, $dataname, $target_id)
    {
        $gid = false;
        $trustDirname = XoopsUtils::getTrustDirname();
        \XCube_DelegateUtils::call(ucfirst($trustDirname).'_WorkflowClient.GetTargetGroupId', new \XCube_Ref($gid), $dirname, $dataname, $target_id);

        return $gid;
    }
}
