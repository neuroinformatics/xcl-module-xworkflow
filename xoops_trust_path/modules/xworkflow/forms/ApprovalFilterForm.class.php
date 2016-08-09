<?php

require_once dirname(dirname(__FILE__)).'/class/AbstractFilterForm.class.php';

/**
 * approval filter form.
 */
class Xworkflow_ApprovalFilterForm extends Xworkflow_AbstractFilterForm
{
    const SORT_KEY_APPROVAL_ID = 1;
    const SORT_KEY_UID = 2;
    const SORT_KEY_GID = 3;
    const SORT_KEY_DIRNAME = 4;
    const SORT_KEY_DATANAME = 5;
    const SORT_KEY_STEP = 6;
    const SORT_KEY_DEFAULT = SORT_KEY_APPROVAL_ID;

    /**
     * sort keys.
     *
     * @var string[]
     */
    public $mSortKeys = array(
        self::SORT_KEY_APPROVAL_ID => 'approval_id',
        self::SORT_KEY_UID => 'uid',
        self::SORT_KEY_GID => 'gid',
        self::SORT_KEY_DIRNAME => 'dirname',
        self::SORT_KEY_DATANAME => 'dataname',
        self::SORT_KEY_STEP => 'step',
    );

    /**
     * get default sort key.
     *
     * @return int[]
     */
    public function getDefaultSortKey()
    {
        return array(self::SORT_KEY_DIRNAME, self::SORT_KEY_DATANAME, self::SORT_KEY_STEP);
    }

    /**
     * fetch.
     */
    public function fetch()
    {
        parent::fetch();
        $root = &XCube_Root::getSingleton();
        if (($value = $root->mContext->mRequest->getRequest('approval_id')) !== null) {
            $this->mNavi->addExtra('approval_id', $value);
            $this->_mCriteria->add(new Criteria('approval_id', $value));
        }
        if (($value = $root->mContext->mRequest->getRequest('uid')) !== null) {
            $this->mNavi->addExtra('uid', $value);
            $this->_mCriteria->add(new Criteria('uid', $value));
        }
        if (($value = $root->mContext->mRequest->getRequest('gid')) !== null) {
            $this->mNavi->addExtra('gid', $value);
            $this->_mCriteria->add(new Criteria('gid', $value));
        }
        if (($value = $root->mContext->mRequest->getRequest('dirname')) !== null) {
            $this->mNavi->addExtra('dirname', $value);
            $this->_mCriteria->add(new Criteria('dirname', $value));
        }
        if (($value = $root->mContext->mRequest->getRequest('dataname')) !== null) {
            $this->mNavi->addExtra('dataname', $value);
            $this->_mCriteria->add(new Criteria('dataname', $value));
        }
        if (($value = $root->mContext->mRequest->getRequest('step')) !== null) {
            $this->mNavi->addExtra('step', $value);
            $this->_mCriteria->add(new Criteria('step', $value));
        }
        foreach (array_keys($this->mSort) as $k) {
            $this->_mCriteria->addSort($this->getSort($k), $this->getOrder($k));
        }
    }
}
