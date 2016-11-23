<?php

use Xworkflow\Core\Functions;
use Xworkflow\Core\LanguageManager;
use Xworkflow\Core\XoopsUtils;

require_once dirname(dirname(__FILE__)).'/class/AbstractEditAction.class.php';

/**
 * approval edit action.
 */
class Xworkflow_ApprovalEditAction extends Xworkflow_AbstractEditAction
{
    /**
     * get handler.
     *
     * return {Trustdirname}_ApprovalHandler
     */
    protected function &_getHandler()
    {
        $handler = &$this->mAsset->getObject('handler', 'ApprovalObject');

        return $handler;
    }

    /**
     * has permission.
     *
     * @return bool
     */
    public function hasPermission()
    {
        return XoopsUtils::isAdmin(XoopsUtils::getUid(), $this->mAsset->mDirname);
    }

    /**
     * prepare.
     *
     * @return bool
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->mObject->isNew() && $this->mObject->countProgressItem() > 0) {
            $langman = new LanguageManager($this->mAsset->mDirname, 'main');
            $this->mRoot->mController->executeRedirect(XoopsUtils::renderUri($this->mAsset->mDirname, 'approval'), 1, $langman->get('ERROR_ITEM_REMAINS'));
        }
        if ($this->mObject->isNew()) {
            $this->mObject->set('dirname', $this->mRoot->mContext->mRequest->getRequest('dirname'));
            $this->mObject->set('dataname', $this->mRoot->mContext->mRequest->getRequest('dataname'));
        }
    }

    /**
     * setup action form.
     */
    protected function _setupActionForm()
    {
        $this->mActionForm = &$this->mAsset->getObject('form', 'Approval', false, 'edit');
        $this->mActionForm->prepare($this->mAsset->mDirname);
    }

    /**
     * execute view input.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewInput(&$render)
    {
        $render->setTemplateName($this->mAsset->mDirname.'_approval_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('uids', $this->_getUserIds());
        $render->setAttribute('groups', $this->_getGroupList());
        $render->setAttribute('object', $this->mObject);
        $render->setAttribute('clients', Functions::getClients());
    }

    /**
     * execute view success.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewSuccess(&$render)
    {
        $this->mRoot->mController->executeForward(XoopsUtils::renderUri($this->mAsset->mDirname, 'approval'));
    }

    /**
     * execute view error.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewError(&$render)
    {
        $langman = new LanguageManager($this->mAsset->mDirname, 'main');
        $this->mRoot->mController->executeRedirect(XoopsUtils::renderUri($this->mAsset->mDirname, 'approval'), 1, $langman->get('ERROR_DBUPDATE_FAILED'));
    }

    /**
     * execute view cancel.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewCancel(&$render)
    {
        $this->mRoot->mController->executeForward(XoopsUtils::renderUri($this->mAsset->mDirname, 'approval'));
    }

    /**
     * get user ids.
     *
     * @return int[]
     */
    protected function _getUserIds()
    {
        $memberHandler = &xoops_gethandler('member');

        return $memberHandler->getUsersByGroup(XOOPS_GROUP_USERS, false);
    }
}
