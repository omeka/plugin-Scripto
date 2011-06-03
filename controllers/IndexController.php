<?php
class Scripto_IndexController extends Omeka_Controller_Action
{
    public function indexAction()
    {
        try {
            $scripto = $this->_getScripto();
            $watchlist = array();
            if ($scripto->isLoggedIn()) {
                $watchlist = $scripto->getWatchlist(100);
            }
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
        $this->view->watchlist = $watchlist;
    }
    
    public function loginAction()
    {
        try {
            $scripto = $this->_getScripto();
            if ($this->_getParam('scripto_mediawiki_login')) {
                $scripto->login($this->_getParam('scripto_mediawiki_username'), 
                                $this->_getParam('scripto_mediawiki_password'));
                $this->flash('Successfully logged into Scripto.');
            }
            if ($scripto->isLoggedIn()) {
                $this->_helper->redirector->goto('index');
            }
        } catch (Scripto_Service_Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
    }
    
    public function logoutAction()
    {
        try {
            $scripto = $this->_getScripto();
            $scripto->logout();
            $this->flash('Successfully logged out of Scripto.');
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->_helper->redirector->goto('index');
    }
    
    public function yourContributionsAction()
    {
        try {
            $scripto = $this->_getScripto();
            if (!$scripto->isLoggedIn()) {
                $this->_helper->redirector->goto('index');
            }
            $documentPages = $scripto->getUserDocumentPages(100);
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
        $this->view->documentPages = $documentPages;
    }
    
    public function recentChangesAction()
    {
        try {
            $scripto = $this->_getScripto();
            $recentChanges = $scripto->getRecentChanges(100);
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
        $this->view->recentChanges = $recentChanges;
    }
    
    public function transcribeAction()
    {
        try {
            // Set the document and its pages.
            $scripto = $this->_getScripto();
            $doc = $scripto->getDocument($this->_getParam('item-id'));
            $doc->setPage($this->_getParam('file-id'));
            
            // Set the image URL and dimensions.
            $pageImageUrl = $doc->getPageImageUrl();
            if (!$imageSize = $this->_getImageSize($pageImageUrl, 250)) {
                $this->flashError('Cannot recognize the provided page image.');
                $this->_helper->redirector->goto('index');
            }
            
            $transcriptionPageHtml = Scripto::removeHtmlAttributes($doc->getTranscriptionPageHtml());
            $talkPageHtml = Scripto::removeHtmlAttributes($doc->getTalkPageHtml());
            
            $pages = $doc->getPages();
            
            // Set the pagination.
            $paginationUrls = array();
            foreach ($pages as $pageId => $pageName) {
                if (isset($current)) {
                    $paginationUrls['next'] = $this->view->url(array(
                        'action' => 'transcribe', 
                        'item-id' => $doc->getId(), 
                        'file-id' => $pageId
                    ), 'scripto_action_item_file');
                    break;
                }
                if ($pageId == $doc->getPageId()) {
                    $current = true;
                } else {
                    $paginationUrls['previous'] = $this->view->url(array(
                        'action' => 'transcribe', 
                        'item-id' => $doc->getId(), 
                        'file-id' => $pageId
                    ), 'scripto_action_item_file');
                }
            }
            
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
            $this->_helper->redirector->goto('index');
        }
        
        $this->view->pageImageUrl = $pageImageUrl;
        $this->view->imageSize = $imageSize;
        $this->view->transcriptionPageHtml = $transcriptionPageHtml;
        $this->view->talkPageHtml = $talkPageHtml;
        $this->view->pages = $pages;
        $this->view->paginationUrls = $paginationUrls;
        $this->view->scripto = $scripto;
        $this->view->doc = $doc;
    }
    
    public function historyAction()
    {
        try {
            $scripto = $this->_getScripto();
            $doc = $scripto->getDocument($this->_getParam('item-id'));
            $doc->setPage($this->_getParam('file-id'));
            if (1 == $this->_getParam('namespace-index')) {
                $info = $doc->getTalkPageInfo();
                $history = $doc->getTalkPageHistory(100);
            } else {
                $info = $doc->getTranscriptionPageInfo();
                $history = $doc->getTranscriptionPageHistory(100);
            }
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
            $this->_helper->redirector->goto('index');
        }
        
        $this->view->scripto = $scripto;
        $this->view->doc = $doc;
        $this->view->info = $info;
        $this->view->history = $history;
    }
    
    public function differenceAction()
    {
        try {
            $scripto = $this->_getScripto();
            $doc = $scripto->getDocument($this->_getParam('item-id'));
            $doc->setPage($this->_getParam('file-id'));
            $diff = $scripto->getRevisionDiff($this->_getParam('old-revision-id'), $this->_getParam('revision-id'));
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
            $this->_helper->redirector->goto('index');
        }
        
        $this->view->scripto = $scripto;
        $this->view->doc = $doc;
        $this->view->diff = $diff;
    }
    
    public function pageActionAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        // Only allow AJAX requests.
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setHttpResponseCode(403);
            return;
        }
        
        // Allow only valid pages.
        $pages = array('transcription', 'talk');
        if (!in_array($this->_getParam('page'), $pages)) {
            $this->getResponse()->setHttpResponseCode(400);
            return;
        }
        
        // Only allow valid page actions.
        $pageActions = array('edit', 'watch', 'unwatch', 'protect', 'unprotect');
        if (!in_array($this->_getParam('page_action'), $pageActions)) {
            $this->getResponse()->setHttpResponseCode(400);
            return;
        }
        
        try {
            $scripto = $this->_getScripto();
            $doc = $scripto->getDocument($this->_getParam('item_id'));
            $doc->setPage($this->_getParam('file_id'));
            
            $body = null;
            switch ($this->_getParam('page_action')) {
                case 'edit':
                    if ('talk' == $this->_getParam('type')) {
                        $doc->editTalkPage($this->_getParam('wikitext'));
                        $body = $doc->getTalkPageHtml();
                    } else {
                        $doc->editTranscriptionPage($this->_getParam('wikitext'));
                        $body = $doc->getTranscriptionPageHtml();
                    }
                    break;
                case 'watch':
                    if ('talk' == $this->_getParam('type')) {
                        $doc->watchTalkPage();
                    } else {
                        $doc->watchTranscriptionPage();
                    }
                    break;
                case 'unwatch':
                    if ('talk' == $this->_getParam('type')) {
                        $doc->unwatchTalkPage();
                    } else {
                        $doc->unwatchTranscriptionPage();
                    }
                    break;
                case 'protect':
                    if ('talk' == $this->_getParam('type')) {
                        $doc->protectTalkPage();
                    } else {
                        $doc->protectTranscriptionPage();
                    }
                    break;
                case 'unprotect':
                    if ('talk' == $this->_getParam('type')) {
                        $doc->unprotectTalkPage();
                    } else {
                        $doc->unprotectTranscriptionPage();
                    }
                    break;
                default:
                    $this->getResponse()->setHttpResponseCode(500);
                    return;
             }
            
            $this->getResponse()->setBody($body);
        } catch (Scripto_Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
        }
    }
    
    public function itemTypeElementsAction() {
        $itemTypeElements = array();
        $elements = $this->getDb()->getTable('ItemType')->find($this->_getParam('id'))->Elements;
        foreach ($elements as $element) {
            $itemTypeElements[$element->id] = $element->name;
        }
        $itemTypeElements = array(0 => 'Select Below...') + $itemTypeElements;
        $this->_helper->json($itemTypeElements);
    }
    
    private function _getScripto()
    {
        return new Scripto(new ScriptoAdapterOmeka, 
                           array('api_url' => get_option('scripto_mediawiki_api_url'), 
                                 'db_name' => get_option('scripto_mediawiki_db_name')));
    }
    
    private function _getImageSize($filename, $width = null)
    {
        $size = getimagesize($filename);
        if (!$size) {
            return false;
        }
        if (is_int($width)) {
            $height = round(($width * $size[1]) / $size[0]);
        } else {
            $width = $size[1];
            $height = $size[0];
        }
        return array('width' => $width, 'height' => $height);
    }
}
