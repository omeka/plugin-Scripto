<?php
require_once 'Scripto/Adapter/Interface.php';

class ScriptoAdapterOmeka implements Scripto_Adapter_Interface
{
    private $_db;
    
    public function __construct()
    {
        $this->_db = get_db();
    }
    
    /**
     * Indicate whether the document exists in the external system.
     * 
     * @param int|string $documentId The unique document ID
     * @return bool True: it exists; false: it does not exist
     */
    public function documentExists($documentId)
    {
        return $this->_validDocument($this->_getItem($documentId));
    }
    
    /**
     * Indicate whether the document page exists in the external system.
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
            
            // Exclude files that are not images.
            if (!$file->has_derivative_image) {
                continue;
            }
            
            // The page name is either the Dublin Core title of the file or the 
            // file's original filename.
            $titles = $file->getElementTextsByElementNameAndSetName('Title', 'Dublin Core');
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
     * Get the URL of the specified document page image.
     * 
     * @param int|string $documentId The unique document ID
     * @param int|string $pageId The unique page ID
     * @return string The page image URL
     */
    public function getDocumentPageImageUrl($documentId, $pageId)
    {
        $file = $this->_getFile($pageId);
        return $file->getWebPath('archive');
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
        $titles = $item->getElementTextsByElementNameAndSetName('Title', 'Dublin Core');
        if (empty($titles)) {
            return '';
        }
        return $titles[0]->text;
    }
    
    /**
     * Indicate whether the document transcription has been imported.
     * 
     * @param int|string $documentId The document ID
     * @return bool True: has been imported; false: has not been imported
     */
    public function documentTranscriptionIsImported($documentId)
    {
        
    }
    
    /**
     * Indicate whether the document page transcription has been imported.
     * 
     * @param int|string $documentId The document ID
     * @param int|string $pageId The page ID
     */
    public function documentPageTranscriptionIsImported($documentId, $pageId)
    {
        
    }
    
    /**
     * Import a document page's transcription into the external system.
     * 
     * @param int|string $documentId The document ID
     * @param int|string $pageId The page ID
     * @param string $text The text to import
     * @return bool True: success; false: fail
     */
    public function importDocumentPageTranscription($documentId, $pageId, $text)
    {
        
    }
    
    /**
     * Import an entire document's transcription into the external system.
     * 
     * @param int|string The document ID
     * @param string The text to import
     * @return bool True: success; false: fail
     */
    public function importDocumentTranscription($documentId, $text)
    {
        
    }
    
    // Returns an Omeka item object.
    private function _getItem($itemId)
    {
        return $this->_db->getTable('Item')->find($itemId);
    }
    
    private function _getFile($fileId)
    {
        return $this->_db->getTable('File')->find($fileId);
    }
    
    // Checks if an Omeka item is a valid Scripto document.
    private function _validDocument($item)
    {
        // The item must exist.
        if (!($item instanceof Item)) {
            return false;
        }
        // The item's item type must match the configured item type.
        if ($item->item_type_id !== (int) get_option('scripto_document_item_type_id')) {
            return false;
        }
        // The item must have at least one file assigned to it.
        if (!isset($item->Files[0])) {
            return false;
        }
        return true;
    }
}
