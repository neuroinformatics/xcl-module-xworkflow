<?php

use Xworkflow\Core\Functions;
use Xworkflow\Core\LanguageManager;
use Xworkflow\Core\XoopsUtils;

require_once dirname(dirname(__FILE__)).'/class/AbstractViewAction.class.php';

/**
 * item view action.
 */
class Xworkflow_ItemViewAction extends Xworkflow_AbstractViewAction
{
    /**
     * flag for if item is my step.
     *
     * @var int
     */
    public $mMyStep = false;

    /**
     *  get handler.
     *
     * return {Trustdirname}_ItemHandler
     */
    protected function &_getHandler()
    {
        $handler = &$this->mAsset->getObject('handler', 'ItemObject');

        return $handler;
    }

    /**
     * get page title.
     *
     * @return string
     */
    protected function _getPagetitle()
    {
        return $this->mObject->getShow('title');
    }

    /**
     * prepare.
     *
     * @return bool
     */
    public function prepare()
    {
        if (!parent::prepare()) {
            return false;
        }
        $this->mObject->loadHistory();
        if ($this->mObject->checkStep(XoopsUtils::getUid())) {
            $this->mMyStep = true;
            $this->_setupActionForm();
        }

        return true;
    }

    /**
     * setup action form.
     */
    protected function _setupActionForm()
    {
        $this->mActionForm = &$this->mAsset->getObject('form', 'History', false, 'edit');
        $this->mActionForm->prepare();
    }

    /**
     * execute view success.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewSuccess(&$render)
    {
        $render->setTemplateName($this->mAsset->mDirname.'_item_view.html');
        $render->setAttribute('object', $this->mObject);
        $render->setAttribute('approval', $this->mMyStep);
        if ($this->mMyStep == true) {
            $render->setAttribute('actionForm', $this->mActionForm);
        }
        $render->setAttribute('clients', Functions::getClients());
    }

    /**
     * execute view error.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewError(&$render)
    {
        $langman = new LanguageManager($this->mAsset->mDirname, 'main');
        $this->mRoot->mController->executeRedirect(XoopsUtils::renderUri($this->mAsset->mDirname, 'item'), 1, $langman->get('ERROR_CONTENT_IS_NOT_FOUND'));
    }
}
