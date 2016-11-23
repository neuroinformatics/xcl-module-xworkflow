<?php

use Xworkflow\Core\LanguageManager;
use Xworkflow\Core\XoopsUtils;

require_once XOOPS_ROOT_PATH.'/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH.'/legacy/class/Legacy_Validator.class.php';

/**
 * approval edit form.
 */
class Xworkflow_ApprovalEditForm extends XCube_ActionForm
{
    /**
     * get token name.
     *
     * @return string
     */
    public function getTokenName()
    {
        $dirname = $this->mContext->mModule->mXoopsModule->get('dirname');

        return 'module.'.$dirname.'.ApprovalEditForm.TOKEN';
    }

    /**
     * prepare.
     */
    public function prepare()
    {
        $dirname = $this->mContext->mModule->mXoopsModule->get('dirname');
        $langman = new LanguageManager($dirname, 'main');
        // Set form properties
        $this->mFormProperties['approval_id'] = new XCube_IntProperty('approval_id');
        $this->mFormProperties['uid'] = new XCube_IntProperty('uid');
        $this->mFormProperties['gid'] = new XCube_IntProperty('gid');
        $this->mFormProperties['dirname'] = new XCube_StringProperty('dirname');
        $this->mFormProperties['dataname'] = new XCube_StringProperty('dataname');
        $this->mFormProperties['step'] = new XCube_IntProperty('step');
        // Set field properties
        $this->mFieldProperties['approval_id'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['approval_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['approval_id']->addMessage('required', $langman->get('ERROR_REQUIRED'), $langman->get('LANG_APPROVAL_ID'));
        $this->mFieldProperties['step'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['step']->setDependsByArray(array('required', 'min'));
        $this->mFieldProperties['step']->addMessage('required', $langman->get('ERROR_REQUIRED'), $langman->get('LANG_STEP'));
        $this->mFieldProperties['step']->addMessage('min', $langman->get('ERROR_MIN'), $langman->get('LANG_STEP'), '1');
        $this->mFieldProperties['step']->addVar('min', '1');
    }

    /**
     * load.
     *
     * @param XoopsSimpleObject &$obj
     */
    public function load(&$obj)
    {
        $this->set('approval_id', $obj->get('approval_id'));
        $this->set('uid', $obj->get('uid'));
        $this->set('gid', $obj->get('gid'));
        $this->set('dirname', $obj->get('dirname'));
        $this->set('dataname', $obj->get('dataname'));
        $this->set('step', $obj->get('step'));
    }

    /**
     * update.
     *
     * @param XoopsSimpleObject &$obj
     */
    public function update(&$obj)
    {
        $target = $this->_getTarget();
        // $obj->set('approval_id', $this->get('approval_id'));
        $obj->set('uid', $this->get('uid'));
        $obj->set('gid', $this->get('gid'));
        $obj->set('dirname', $target[0]);
        $obj->set('dataname', $target[1]);
        $obj->set('step', $this->get('step'));
    }

    /**
     * validate uid.
     *
     * @param string $dirname
     */
    public function validateUid()
    {
        $dirname = $this->mContext->mModule->mXoopsModule->get('dirname');
        $langman = new LanguageManager($dirname, 'main');
        $uid = $this->get('uid');
        $gid = $this->get('gid');
        if ($uid === null || $gid === null) {
            $this->addErrorMessage(XCube_Utils::formatString($langman->get('ERROR_INTRANGE'), $langman->get('LANG_APPROVED_BY')));
        } elseif ($uid != 0 && $gid != 0) {
            $this->addErrorMessage(XCube_Utils::formatString($langman->get('ERROR_INTRANGE'), $langman->get('LANG_APPROVED_BY')));
        }
    }

    /**
     * validate gid.
     *
     * @param string $dirname
     */
    public function validateGid()
    {
        $dirname = $this->mContext->mModule->mXoopsModule->get('dirname');
        $langman = new LanguageManager($dirname, 'main');
        $gid = $this->get('gid');
        if ($gid != null && $gid != 0) {
            $member_handler = &xoops_gethandler('member');
            if ($member_handler->getGroup($this->get('gid')) === false) {
                $this->addErrorMessage(XCube_Utils::formatString($langman->get('ERROR_INTRANGE'), $langman->get('LANG_GID')));
            }
        }
    }

    /**
     * validate step.
     *
     * @param string $dirname
     */
    public function validateStep()
    {
        $dirname = $this->mContext->mModule->mXoopsModule->get('dirname');
        $langman = new LanguageManager($dirname, 'main');
        $handler = XoopsUtils::getModuleHandler('ApprovalObject', $dirname);
        $target = $this->_getTarget();
        $cri = new CriteriaCompo();
        $cri->add(new Criteria('dirname', $target[0]));
        $cri->add(new Criteria('dataname', $target[1]));
        $cri->add(new Criteria('step', $this->get('step')));
        $objs = $handler->getObjects($cri);
        if (count($objs) > 0 && $objs[0]->get('approval_id') != $this->get('approval_id')) {
            $this->addErrorMessage($langman->get('ERROR_DUPLICATED_STEP'));
        }
    }

    /**
     * get target.
     *
     * @return string[]
     */
    protected function _getTarget()
    {
        $root = XCube_Root::getSingleton();

        return explode('|', $root->mContext->mRequest->getRequest('target'));
    }
}
