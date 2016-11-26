<?php

namespace Xworkflow\Core;

/**
 * static functions.
 */
class Functions
{
    // flags for `groups`.`activate`.
    const GROUP_ACT_NOT_CERTIFIED = 0;
    const GROUP_ACT_CERTIFIED = 1;
    const GROUP_ACT_OPEN_REQUIRED = 2;
    const GROUP_ACT_PUBLIC = 3;
    const GROUP_ACT_CLOSE_REQUIRED = 4;
    const GROUP_ACT_DELETE_REQUIRED = 5;

    // flags for `groups_users_link`.`activate`.
    const LINK_ACT_CERTIFIED = 0;
    const LINK_ACT_JOIN_REQUIRED = 1;
    const LINK_ACT_LEAVE_REQUIRED = 2;

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
        $sql = 'SELECT `g`.`groupid` FROM `'.$table.'` AS `g` INNER JOIN `'.$table2.'` AS `l` ON `g`.`groupid`=`l`.`groupid` WHERE `g`.`activate`!='.self::GROUP_ACT_NOT_CERTIFIED.' AND `l`.`activate`!='.self::LINK_ACT_JOIN_REQUIRED.' AND `l`.`uid`='.$uid.' AND `l`.`is_admin`=1';
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
     * get group admin user ids.
     *
     * @param int $gid
     *
     * @return array
     */
    public static function getGroupAdminUserIds($gid)
    {
        $uids = array();
        if (!self::isExtendedGroup()) {
            return $uids;
        }
        $db = &\XoopsDatabaseFactory::getDatabaseConnection();
        $table = $db->prefix('groups_users_link');
        $table2 = $db->prefix('groups');
        $sql = 'SELECT `l`.`uid` FROM `'.$table.'` AS `l` INNER JOIN `'.$table2.'` AS `g` ON `l`.`groupid`=`g`.`groupid` WHERE `g`.`activate`!='.self::GROUP_ACT_NOT_CERTIFIED.' AND `l`.`activate`!='.self::LINK_ACT_JOIN_REQUIRED.' AND `g`.`groupid`='.$gid.' AND `l`.`is_admin`=1';
        if (!($result = $db->query($sql))) {
            return $gids;
        }
        while ($row = $db->fetchRow($result)) {
            $uids[] = $row[0];
        }
        $db->freeRecordSet($result);

        return $uids;
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
