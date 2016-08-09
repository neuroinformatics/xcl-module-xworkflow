<?php

require_once dirname(dirname(__FILE__)).'/class/AbstractListAction.class.php';

/**
 * approval list action.
 */
class Xworkflow_ApprovalListAction extends Xworkflow_AbstractListAction
{
    /**
     * clients.
     *
     * @var {string, string}[]
     */
    private $_mClients = array();

    /**
     * get header.
     *
     * return {Trustdirname}_ApprovalHandler
     */
    protected function &_getHandler()
    {
        $handler = &$this->mAsset->getObject('handler', 'Approval');

        return $handler;
    }

    /**
     * get filter form.
     * 
     * return {Trustdirname}_ApprovalFilterForm
     */
    protected function &_getFilterForm()
    {
        $filter = &$this->mAsset->getObject('filter', 'Approval', false);
        $filter->prepare($this->_getPageNavi(), $this->_getHandler());

        return $filter;
    }

    /**
     * get default view.
     * 
     * @return Enum
     */
    public function getDefaultView()
    {
        $cnameUtils = ucfirst($this->mAsset->mTrustDirname).'_Utils';
        $this->mClients = $cnameUtils::getClients();
        if (count($this->mClients) > 0) {
            $handler = $this->_getHandler();
            foreach ($this->mClients as $dirname => $modules) {
                foreach ($modules as $dataname => $info) {
                    $cri = new CriteriaCompo();
                    $cri->add(new Criteria('dirname', $dirname));
                    $cri->add(new Criteria('dataname', $dataname));
                    $cri->setSort('step', 'ASC');
                    $objs = $handler->getObjects($cri);
                    $this->mObjects[$dirname][$dataname] = $objs;
                }
            }
        }

        return $this->_getFrameViewStatus('INDEX');
    }

    /**
     * get base url.
     * 
     * @return string
     */
    protected function _getBaseUrl()
    {
        return Legacy_Utils::renderUri($this->mAsset->mDirname, 'approval');
    }

    /**
     * execute view index.
     * 
     * @param XCube_RenderTarget &$render
     */
    public function executeViewIndex(&$render)
    {
        $render->setTemplateName($this->mAsset->mDirname.'_approval_list.html');
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('clients', $this->mClients);
        $render->setAttribute('groups', $this->_getGroupList());
        $render->setAttribute('query', 'dirname=%s&dataname=%s');
    }
}
