<?php

if (!defined('LEGACY_WORKFLOW_DIRNAME')) {
    define('LEGACY_WORKFLOW_DIRNAME', $mydirname);
}

require_once dirname(dirname(__FILE__)).'/class/Utils.class.php';

/**
 * asset preload base class.
 */
class Xworkflow_AssetPreloadBase extends XCube_ActionFilter
{
    /**
     * dirname.
     *
     * @var string
     */
    public $mDirname = null;

    /**
     * trust dirname.
     *
     * @var string
     */
    public static $mTrustDirname = null;

    /**
     * prepare.
     * 
     * @param string $dirname
     * @param string $trustDirname
     */
    public static function prepare($dirname, $trustDirname)
    {
        $root = &XCube_Root::getSingleton();
        $instance = new self($root->mController);
        $instance->mDirname = $dirname;
        if (self::$mTrustDirname === null) {
            self::$mTrustDirname = $trustDirname;
        }
        $root->mController->addActionFilter($instance);
    }

    /**
     * pre block filter.
     */
    public function preBlockFilter()
    {
        static $isFirst = true;
        $prefix = ucfirst(self::$mTrustDirname);
        if ($isFirst) {
            // global delegates - activate only first module
            $this->mRoot->mDelegateManager->add('Module.'.self::$mTrustDirname.'.Global.Event.GetAssetManager', $prefix.'_AssetPreloadBase::getManager');
            $this->mRoot->mDelegateManager->add('Legacy_Utils.CreateModule', $prefix.'_AssetPreloadBase::getModule');
            $this->mRoot->mDelegateManager->add('Legacy_Utils.CreateBlockProcedure', $prefix.'_AssetPreloadBase::getBlock');
            // cool uri
            $file = XOOPS_TRUST_PATH.'/modules/'.self::$mTrustDirname.'/class/callback/CoolUri.class.php';
            $this->mRoot->mDelegateManager->add('Module.'.self::$mTrustDirname.'.Global.Event.GetNormalUri', $prefix.'_CoolUriDelegate::getNormalUri', $file);
            $isFirst = false;
        }
        if (LEGACY_WORKFLOW_DIRNAME == $this->mDirname) {
            // workflow - activate if primary workflow module
            $file = XOOPS_TRUST_PATH.'/modules/'.self::$mTrustDirname.'/class/callback/Workflow.class.php';
            $this->mRoot->mDelegateManager->add('Legacy_Workflow.AddItem', $prefix.'_WorkflowDelegate::addItem', $file);
            $this->mRoot->mDelegateManager->add('Legacy_Workflow.DeleteItem', $prefix.'_WorkflowDelegate::deleteItem', $file);
            $this->mRoot->mDelegateManager->add('Legacy_Workflow.GetHistory', $prefix.'_WorkflowDelegate::getHistory', $file);
        }
    }

    /**
     * get manager.
     * 
     * @param {Trustdirname}_AssetManager &$obj
     * @param string                      $dirname
     */
    public static function getManager(&$obj, $dirname)
    {
        require_once XOOPS_TRUST_PATH.'/modules/'.self::$mTrustDirname.'/class/AssetManager.class.php';
        $className = ucfirst(self::$mTrustDirname).'_AssetManager';
        $obj = call_user_func_array($className.'::getInstance', array($dirname, self::$mTrustDirname));
    }

    /**
     * get module.
     * 
     * @param Legacy_AbstractModule &$obj
     * @param XoopsModule           $module
     */
    public static function getModule(&$obj, $module)
    {
        if ($module->getInfo('trust_dirname') == self::$mTrustDirname) {
            $mytrustdirname = self::$mTrustDirname;
            require_once XOOPS_TRUST_PATH.'/modules/'.self::$mTrustDirname.'/class/Module.class.php';
            $className = ucfirst(self::$mTrustDirname).'_Module';
            $obj = new $className($module);
        }
    }

    /**
     * get block.
     * 
     * @param Legacy_AbstractBlockProcedure &$obj
     * @param XoopsBlock                    $block
     */
    public static function getBlock(&$obj, $block)
    {
        $moduleHandler = &xoops_gethandler('module');
        $module = &$moduleHandler->get($block->get('mid'));
        if (is_object($module) && $module->getInfo('trust_dirname') == self::$mTrustDirname) {
            require_once XOOPS_TRUST_PATH.'/modules/'.self::$mTrustDirname.'/blocks/'.$block->get('func_file');
            $className = ucfirst($self::$mTrustDirname).'_'.substr($block->get('show_func'), 4);
            $obj = new $className($block);
        }
    }
}
