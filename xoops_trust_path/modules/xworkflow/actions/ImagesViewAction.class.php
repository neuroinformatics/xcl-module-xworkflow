<?php

use Xworkflow\Core\CacheUtils;

/**
 * images view action.
 */
class Xworkflow_ImagesViewAction extends Xworkflow_AbstractAction
{
    /**
     * image path.
     *
     * @var string
     */
    protected $mImagePath = '';

    /**
     * image mime-type.
     *
     * @var string
     */
    protected $mImageMime = '';

    /**
     * get action name.
     *
     * @return string
     */
    protected function _getActionName()
    {
        return _IMAGE;
    }

    /**
     * get id.
     *
     * @return string
     */
    protected function _getId()
    {
        $req = $this->mRoot->mContext->mRequest;
        $dataId = $req->getRequest(_REQUESTED_DATA_ID);
        if (isset($_SERVER['PATH_INFO']) && preg_match('/^\/([a-z0-9]+)(?:\/([a-z0-9][a-zA-Z0-9\._\-]*))?(?:\/([a-z0-9]+))?$/', $_SERVER['PATH_INFO'], $matches)) {
            if (isset($matches[2])) {
                $dataId = $matches[2];
            }
        }

        return isset($dataId) ? trim($dataId) : @trim($req->getRequest('file'));
    }

    /**
     * get default view.
     *
     * @return Enum
     */
    public function getDefaultView()
    {
        $fname = $this->_getId();
        if (empty($fname)) {
            return $this->_getFrameViewStatus('ERROR');
        }
        $fpath = XOOPS_TRUST_PATH.'/modules/'.$this->mAsset->mDirname.'/images/'.$fname;
        if (!file_exists($fpath)) {
            $fpath = XOOPS_TRUST_PATH.'/modules/'.$this->mAsset->mTrustDirname.'/images/'.$fname;
            if (!file_exists($fpath)) {
                return $this->_getFrameViewStatus('ERROR');
            }
        }
        $info = getimagesize($fpath);
        if ($info === false) {
            return $this->_getFrameViewStatus('ERROR');
        }
        $this->mImagePath = $fpath;
        $this->mImageMime = $info['mime'];

        return $this->_getFrameViewStatus('SUCCESS');
    }

    /**
     * execute.
     *
     * @return Enum
     */
    public function execute()
    {
        return $this->getDefaultView();
    }

    /**
     * execute view success.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewSuccess(&$render)
    {
        $mtime = filemtime($this->mImagePath);
        $etag = md5($this->mImagePath.filesize($this->mImagePath).$this->mAsset->mDirname.$mtime);
        CacheUtils::check304($mtime, $etag);
        CacheUtils::outputFile($mtime, $etag, $this->mImageMime, $this->mImagePath);
    }

    /**
     * execute view error.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewError(&$render)
    {
        CacheUtils::errorExit(404);
    }
}
