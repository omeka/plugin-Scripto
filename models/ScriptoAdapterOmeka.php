<?php
/**
 * Omeka adapter for Scripto.
 */
class ScriptoAdapterOmeka implements Scripto_Adapter_Interface
{
    /**
     * @var Omeka_Db
     */
    private $_db;
    
    /**
     * Set the database object on construction.
     */
    public function __construct()
    {
        $this->_db = get_db();
    }
    
    /**
     * Indicate whether the document exists in Omeka.
     * 
     * @param int|string $documentId The unique document ID
     * @return bool True: it exists; false: it does not exist
     */
    public function documentExists($documentId)
    {
        return $this->_validDocument($this->_getItem($documentId));
    }
    
    /**
     * Indicate whether the document page exists in Omeka.
     * 
     * @param int|string $documentId The unique document ID
     * @param int|string $pageId The unique page ID
     * @return bool True: it exists; false: it does not exist
     */
    public function documentPageExists($documentId, $pageId)
    {
        $item = $this->_getItem($documentId);
        if (false == $this->_validDocument($item)) {
            return false;
        }
        // The Omeka file ID must match the Scripto page ID.
        $files = $item->Files;
        foreach ($files as $file) {
            if ($pageId == $file->id) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get all the pages belonging to the document.
     * 
     * @param int|string $documentId The unique document ID
     * @return array An array containing page identifiers as keys and page names 
     * as values, in sequential page order.
     */
    public function getDocumentPages($documentId)
    {
        $item = $this->_getItem($documentId);
        $documentPages = array();
        foreach ($item->Files as $file) {
            // The page name is either the Dublin Core title of the file or the 
            // file's original filename.
            $titles = $file->getElementTexts('Dublin Core', 'Title');
            if (empty($titles)) {
                $pageName = $file->original_filename;
            } else {
                $pageName = $titles[0]->text;
            }
            $documentPages[$file->id] = $pageName;
        }
        return $documentPages;
    }
    
    /**
     * Get the URL of the specified document page file.
     * 
     * @param int|string $documentId The unique document ID
     * @param int|string $pageId The unique page ID
     * @return string The page file URL
     */
    public function getDocumentPageFileUrl($documentId, $pageId)
    {
        $file = $this->_getFile($pageId);
        return $file->getWebPath('original');
    }
    
    /**
     * Get the first page of the document.
     * 
     * @param int|string $documentId The document ID
     * @return int|string
     */
    public function getDocumentFirstPageId($documentId)
    {
        $item = $this->_getItem($documentId);
        return $item->Files[0]->id;
    }
    
    /**
     * Get the title of the document.
     * 
     * @param int|string $documentId The document ID
     * @return string
     */
    public function getDocumentTitle($documentId)
    {
        $item = $this->_getItem($documentId);
        $titles = $item->getElementTexts('Dublin Core', 'Title');
        if (empty($titles)) {
            return '';
        }
        return $titles[0]->text;
    }
    
    /**
     * Get the name of the document page.
     * 
     * @param int|string $documentId The document ID
     * @param int|string $pageId The unique page ID
     * @return string
     */
    public function getDocumentPageName($documentId, $pageId)
    {
        $file = $this->_getFile($pageId);
        
        // The page name is either the Dublin Core title of the file or the 
        // file's original filename.
        $titles = $file->getElementTexts('Dublin Core', 'Title');
        if (empty($titles)) {
            $pageName = $file->original_filename;
        } else {
            $pageName = $titles[0]->text;
        }
        return $pageName;
    }
    
    /**
     * Indicate whether the document transcription has been imported.
     * 
     * @param int|string $documentId The document ID
     * @return bool True: has been imported; false: has not been imported
     */
    public function documentTranscriptionIsImported($documentId)
    {}
    
    /**
     * Indicate whether the document page transcription has been imported.
     * 
     * @param int|string $documentId The document ID
     * @param int|string $pageId The page ID
     */
    public function documentPageTranscriptionIsImported($documentId, $pageId)
    {}
    
    /**
     * Import a document page's transcription into Omeka.
     * 
     * @param int|string $documentId The document ID
     * @param int|string $pageId The page ID
     * @param string $text The text to import
     * @return bool True: success; false: fail
     */
    public function importDocumentPageTranscription($documentId, $pageId, $text)
    {
        $file = $this->_getFile($pageId);
        $element = $file->getElement('Scripto', 'Transcription');
        $file->deleteElementTextsByElementId(array($element->id));
        $isHtml = false;
        if ('html' == get_option('scripto_import_type')) {
            $isHtml = true;
        }
        $text = Scripto::removeNewPPLimitReports($text);
        $file->addTextForElement($element, $text, $isHtml);
        $file->save();
    }
    
    /**
     * Import an entire document's transcription into Omeka.
     * 
     * @param int|string The document ID
     * @param string The text to import
     * @return bool True: success; false: fail
     */
    public function importDocumentTranscription($documentId, $text)
    {
        $item = $this->_getItem($documentId);
        $element = $item->getElement('Scripto', 'Transcription');
        $item->deleteElementTextsByElementId(array($element->id));
        $isHtml = false;
        if ('html' == get_option('scripto_import_type')) {
            $isHtml = true;
        }
        $text = Scripto::removeNewPPLimitReports($text);
        $item->addTextForElement($element, $text, $isHtml);
        $item->save();
    }
    
    /**
     * Return an Omeka item object.
     * 
     * @param int $itemId
     * @return Item|null
     */
    private function _getItem($itemId)
    {
        return $this->_db->getTable('Item')->find($itemId);
    }
    
    /**
     * Return an Omeka file object.
     * 
     * @param int $fileId
     * @return File|int
     */
    private function _getFile($fileId)
    {
        return $this->_db->getTable('File')->find($fileId);
    }
    
    /**
     * Check if the provided item exists in Omeka and is a valid Scripto 
     * document.
     * 
     * @param Item $item
     * @return bool
     */
    private function _validDocument($item)
    {
        // The item must exist.
        if (!($item instanceof Item)) {
            return false;
        }
        // The item must have at least one file assigned to it.
        if (!isset($item->Files[0])) {
            return false;
        }
        return true;
    }
}
