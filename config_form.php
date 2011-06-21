<p>This plugin requires you to download and install <a href="http://www.mediawiki.org/wiki/MediaWiki">MediaWiki</a>, 
a popular free web-based wiki software application that Scripto uses to manage 
transcription data. Once you have successfully installed MediaWiki, you can 
complete the following form and install the plugin.</p>
<p>This plugin will assume files belonging to an item are in logical order, 
first to last page.</p>
<div class="field">
    <label for="scripto_mediawiki_api_url" class="required">MediaWiki API URL</label>
        <div class="inputs">
        <?php echo __v()->formText('scripto_mediawiki_api_url', get_option('scripto_mediawiki_api_url'), array('size' => 50)); ?>
        <p class="explanation">URL to your <a href="http://www.mediawiki.org/wiki/API:Quick_start_guide#What_you_need_to_access_the_API">MediaWiki installation API</a>.</p>
    </div>
</div>
<div class="field">
    <label for="scripto_mediawiki_db_name">MediaWiki database name</label>
        <div class="inputs">
        <?php echo __v()->formText('scripto_mediawiki_db_name', get_option('scripto_mediawiki_db_name')); ?>
        <p class="explanation">Name of your MediaWiki database. No changes to 
        the database will be made. This is required to log in to Scripto.</p>
    </div>
</div>
<div class="field">
    <label for="scripto_use_openlayers">Use OpenLayers image viewer?</label>
        <div class="inputs">
        <?php echo __v()->formCheckbox('scripto_use_openlayers', null, array('checked' => (bool) $useOpenlayers)); ?>
        <p class="explanation">Use the <a href="http://openlayers.org/">OpenLayers</a> 
        image viewer when transcribing image files? (Highly recommended.)</p>
    </div>
</div>
<div class="field">
    <label for="scripto_use_google_docs_viewer">Use Google Docs Viewer?</label>
        <div class="inputs">
        <?php echo __v()->formCheckbox('scripto_use_google_docs_viewer', null, array('checked' => (bool) $useGoogleDocsViewer)); ?>
        <p class="explanation">Use Google Docs Viewer when transcribing document 
        files? Document files include PDF, DOC, PPT, XLS, TIFF, PS, and PSD 
        files. By using this service you acknowledge that you have read and 
        agreed to the <a href="http://docs.google.com/viewer/TOS?hl=en">Google 
        Docs Viewer Terms of Service</a>.</p>
    </div>
</div>
<div class="field">
    <label for="scripto_export_type">Export type?</label>
        <div class="inputs">
        <?php echo __v()->formRadio('scripto_export_type', $exportType, null, array('html' => 'HTML', 'plain_text' => 'plain text'), null); ?>
        <p class="explanation">Export transcriptions as HTML or plain text? 
        Exporting will copy document and page transcriptions from MediaWiki to 
        their corresponding items and files in Omeka.</p>
    </div>
</div>