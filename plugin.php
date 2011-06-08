<?php
// Debugging.
function d($var, $dump = false, $exit = true)
{
    echo '<pre>';
    $dump ? var_dump($var) : print_r($var);
    echo '</pre>';
    if ($exit) exit;
}

add_plugin_hook('install', 'ScriptoPlugin::install');
add_plugin_hook('uninstall', 'ScriptoPlugin::uninstall');
add_plugin_hook('admin_append_to_plugin_uninstall_message', 'ScriptoPlugin::adminAppendToPluginUninstallMessage');
add_plugin_hook('define_routes', 'ScriptoPlugin::defineRoutes');
add_plugin_hook('config_form', 'ScriptoPlugin::configForm');
add_plugin_hook('config', 'ScriptoPlugin::config');
add_plugin_hook('public_append_to_items_show', 'ScriptoPlugin::appendToItemsShow');
add_plugin_hook('admin_append_to_items_show_primary', 'ScriptoPlugin::appendToItemsShow');

add_filter('admin_navigation_main', 'ScriptoPlugin::adminNavigationMain');
add_filter('public_navigation_main', 'ScriptoPlugin::publicNavigationMain');

/**
 * Contains methods specific to the Scripto plugin.
 */
class ScriptoPlugin
{
    /**
     * The name of the Scripto element set.
     */
    const ELEMENT_SET_NAME = 'Scripto';
    
    /**
     * Install Scripto.
     */
    public static function install()
    {
        $db = get_db();
        
        // Don't install if an element set by the name "Scripto" already exists.
        if ($db->getTable('ElementSet')->findByName(self::ELEMENT_SET_NAME)) {
            throw new Exception('An element set by the name "' . self::ELEMENT_SET_NAME . '" already exists. You must delete that element set to install this plugin.');
        }
        
        // Insert the Scripto element set.
        insert_element_set('Scripto', array(
            array('name' => 'Transcription', 
                  'description' => 'A written representation of an item.')
        ));
    }
    
    /**
     * Uninstall Scripto.
     */
    public static function uninstall()
    {
        $db = get_db();
        
        // Delete the Scripto element set.
        $db->getTable('ElementSet')->findByName(self::ELEMENT_SET_NAME)->delete();
        
        // Delete the Scripto-specific options.
        delete_option('scripto_mediawiki_api_url');
        delete_option('scripto_mediawiki_db_name');
    }
    
    /**
     * Appends a warning message to the uninstall confirmation page.
     */
    public static function adminAppendToPluginUninstallMessage()
    {
        echo '<p><strong>Warning</strong>: This will permanently delete the "' . self::ELEMENT_SET_NAME . '" element set and all transcriptions imported from MediaWiki. You may deactivate this plugin if you do not want to lose data. Uninstalling this plugin will not affect your MediaWiki database in any way.</p>';
    }
    
    /**
     * Define routes.
     * 
     * @param Zend_Controller_Router_Rewrite $router
     */
    public static function defineRoutes($router)
    {
        $router->addConfig(new Zend_Config_Ini(dirname(__FILE__) . '/routes.ini', 'routes'));
    }
    
    /**
     * Render the config form.
     */
    public static function configForm()
    {
        include 'config_form.php';
    }
    
    /**
     * Handle a submitted config form.
     */
    public static function config()
    {
        // Validate the MediaWiki API URL.
        if (!Scripto::isValidApiUrl($_POST['scripto_mediawiki_api_url'])) {
            throw new Omeka_Validator_Exception('Invalid MediaWiki API URL');
        }
        
        set_option('scripto_mediawiki_api_url', $_POST['scripto_mediawiki_api_url']);
        set_option('scripto_mediawiki_db_name', $_POST['scripto_mediawiki_db_name']);
    }
    
    /**
     * Add Scripto to the admin navigation.
     * 
     * @param array $nav
     * @return array
     */
    public static function adminNavigationMain($nav)
    {
        $nav['Scripto'] = uri('scripto');
        return $nav;
    }
    
    /**
     * Add Scripto to the public navigation.
     * 
     * @param array $nav
     * @return array
     */
    public static function publicNavigationMain($nav)
    {
        $nav['Scripto'] = uri('scripto');
        return $nav;
    }
    
    /**
     * add_mime_display_type() callback for image files.
     * 
     * @see Scripto_IndexController::init()
     * @param File $file
     */
    public static function imageViewer($file)
    {
        // OpenLayers doesn't render TIFF files, so render the converted 
        // fullsize image instead.
        if (in_array($file->mime_browser, array('image/tiff', 'image/tif'))) {
            $pageFileUrl = $file->getWebPath('fullsize');
        } else {
            $pageFileUrl = $file->getWebPath('archive');
        }
        $imageSize = ScriptoPlugin::getImageSize($pageFileUrl, 250);
?>
<script type="text/javascript">
// Set the OpenLayers image viewer.
jQuery(document).ready(function() {
    var scriptoMap = new OpenLayers.Map('scripto-map');
    var graphic = new OpenLayers.Layer.Image(
        'Document Page',
        <?php echo js_escape($pageFileUrl); ?>,
        new OpenLayers.Bounds(-<?php echo $imageSize['width']; ?>, -<?php echo $imageSize['height']; ?>, <?php echo $imageSize['width']; ?>, <?php echo $imageSize['height']; ?>),
        new OpenLayers.Size(<?php echo $imageSize['width']; ?>, <?php echo $imageSize['height']; ?>)
    );
    scriptoMap.addLayers([graphic]);
    scriptoMap.zoomToMaxExtent();
});
</script>
<!-- document page viewer -->
<div id="scripto-map" style="height: 300px; border: 1px grey solid; margin-bottom: 12px;"></div>
<?php
    }
    
    /**
     * Append the transcribe link to the items show page.
     */
    public static function appendToItemsShow()
    {
        $item = get_current_item();
        $url = uri(array('action'  => 'transcribe',  
                         'item-id' => $item->id), 'scripto_action_item');
?>
<p><a href="<?php echo $url; ?>" id="scripto-transcribe-item">Transcribe this item.</a></p>
<?php
    }
    
    /**
     * Convenience method to get the Scripto object.
     * 
     * @param string $apiUrl
     * @param string $dbName
     */
    public static function getScripto($apiUrl = null, $dbName = null)
    {
        if (null === $apiUrl) {
            $apiUrl = get_option('scripto_mediawiki_api_url');
        }
        if (null === $dbName) {
            $dbName = get_option('scripto_mediawiki_db_name');
        }
        
        return new Scripto(new ScriptoAdapterOmeka, 
                           array('api_url' => $apiUrl, 'db_name' => $dbName));
    }
    
    /**
     * Get dimensions of the provided image.
     * 
     * @param string $filename URI to file.
     * @param int $width Width constraint.
     * @return array
     */
    public static function getImageSize($filename, $width = null)
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
