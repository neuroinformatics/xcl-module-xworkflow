<?php

use Xworkflow\Core\XoopsUtils;

/**
 * workflow delegate.
 */
class Xworkflow_WorkflowDelegate implements Legacy_iWorkflowDelegate
{
    /**
     * add item.
     *
     * 'Legacy_Workflow.AddItem' delegate function
     *
     * @param string $title
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     * @param string $url
     */
    public static function addItem($title, $dirname, $dataname, $target_id,  $url)
    {
        $handler = &XoopsUtils::getModuleHandler('ItemObject', LEGACY_WORKFLOW_DIRNAME);
        $handler->addItem($title, $dirname, $dataname, $target_id, $url);
    }

    /**
     * delete item.
     *
     * 'Legacy_Workflow.DeleteItem' delegate function
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     */
    public static function deleteItem($dirname, $dataname, $target_id)
    {
        $handler = &XoopsUtils::getModuleHandler('ItemObject', LEGACY_WORKFLOW_DIRNAME);
        $handler->deleteItem($dirname, $dataname, $target_id);
    }

    /**
     * get history.
     *
     * 'Legacy_Workflow.GetHistory' delegate function
     *
     * @param mix[]  &$historyArr
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     */
    public static function getHistory(&$historyArr, $dirname, $dataname, $target_id)
    {
        static $keys = array('step', 'uid', 'result', 'comment', 'posttime');
        $handler = &XoopsUtils::getModuleHandler('ItemObject', LEGACY_WORKFLOW_DIRNAME);
        $objs = $handler->getHistory($dirname, $dataname, $target_id);
        foreach ($objs as $obj) {
            if (!is_array($historyArr)) {
                $historyArr = array();
            }
            foreach ($keys as $key) {
                if (!array_key_exists($key, $historyArr)) {
                    $historyArr[$key] = array();
                }
                $historyArr[$key][] = $obj->get($key);
            }
        }
    }
}
