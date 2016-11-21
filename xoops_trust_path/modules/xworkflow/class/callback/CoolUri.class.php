<?php

use Xworkflow\Core\XoopsUtils;

/**
 * cool uri delegate.
 */
class Xworkflow_CoolUriDelegate
{
    /**
     * get normal uri.
     *
     * @see Module.{trustdirname}.Global.Event.GetNormalUri
     *
     * @param string &$uri
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     * @param string $action
     * @param string $query
     */
    public static function getNormalUri(&$uri, $dirname, $dataname = null, $target_id = 0, $action = null, $query = null)
    {
        $sUri = '/%s/index.php?action=%s%s';
        $lUri = '/%s/index.php?action=%s%s&%s=%d';
        switch ($dataname) {
        case 'history':
            $key = 'progress_id';
            break;
        default:
            $key = $dataname.'_id';
            break;
        }
        $table = isset($dataname) ? $dataname : 'index';
        if (isset($dataname)) {
            if ($target_id > 0) {
                if (isset($action)) {
                    $uri = sprintf($lUri, $dirname, ucfirst($dataname), ucfirst($action), $key, $target_id);
                } else {
                    $uri = sprintf($lUri, $dirname, ucfirst($dataname), 'View', $key, $target_id);
                }
            } else {
                if (isset($action)) {
                    $uri = sprintf($sUri, $dirname, ucfirst($dataname), ucfirst($action));
                } else {
                    $uri = sprintf($sUri, $dirname, ucfirst($dataname), 'List');
                }
            }
            $uri = isset($query) ? $uri.'&'.$query : $uri;
        } else {
            if ($target_id > 0) {
                if (isset($action)) {
                    die();
                } else {
                    $handler = &XoopsUtils::getModuleHandler($table.'Object', $dirname);
                    $key = $handler->getPrimaryKey();
                    $uri = sprintf($lUri, $dirname, ucfirst($table), 'View', $key, $target_id);
                }
                $uri = isset($query) ? $uri.'&'.$query : $uri;
            } else {
                if (isset($action)) {
                    die();
                } else {
                    $uri = sprintf('/%s/', $dirname);
                    $uri = isset($query) ? $uri.'index.php?'.$query : $uri;
                }
            }
        }
    }
}
