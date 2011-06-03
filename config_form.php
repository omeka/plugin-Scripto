<script type="text/javascript">
jQuery(document).ready(function () {
    jQuery('#scripto_document_item_type_id').change(function() {
        var itemTypeId = jQuery('#scripto_document_item_type_id option:selected').val();
        var uri = <?php echo js_escape($url); ?> + itemTypeId;
        jQuery.getJSON(uri, function(data) {
            var options = [];
            jQuery.each(data, function(elementId, elementName) {
                options.push('<option value="' + elementId + '">' + elementName + '</option>');
            });
            jQuery('#scripto_transcription_element_id').html(options.join(''));
        });
    });
});
</script>
<p>This plugin requires you to download and install <a href="http://www.mediawiki.org/wiki/MediaWiki">MediaWiki</a>, 
a popular free web-based wiki software application that Scripto uses to manage 
transcription data. Once you have successfully installed MediaWiki, you can 
complete the following form and install the plugin.</p>
<div class="field">
    <label for="scripto_mediawiki_api_url" class="required">MediaWiki API URL</label>
        <div class="inputs">
        <?php echo __v()->formText('scripto_mediawiki_api_url', get_option('scripto_mediawiki_api_url'), array('size' => 50)); ?>
        <p class="explanation">URL to your <a href="http://www.mediawiki.org/wiki/API:Quick_start_guide#What_you_need_to_access_the_API">MediaWiki installation API</a>.</p>
    </div>
</div>
<div class="field">
    <label for="scripto_mediawiki_db_name" class="required">MediaWiki database name</label>
        <div class="inputs">
        <?php echo __v()->formText('scripto_mediawiki_db_name', get_option('scripto_mediawiki_db_name')); ?>
        <p class="explanation">Name of your MediaWiki database.</p>
    </div>
</div>
<div class="field">
    <label for="scripto_document_item_type_id" class="required">Item type</label>
        <div class="inputs">
        <?php echo __v()->formSelect('scripto_document_item_type_id', get_option('scripto_document_item_type_id'), array(), $itemTypes); ?>
        <p class="explanation">You must select an existing Omeka item type 
        before using this plugin. Scripto will only recognize items belonging to 
        this item type, and items that have at least one image file, as 
        transcribable documents. It will assume image files belonging to an item 
        are in logical order, first to last page.</p>
    </div>
</div>
<div class="field">
    <label for="scripto_transcription_element_id" class="required">Transcription element</label>
        <div class="inputs">
        <?php echo __v()->formSelect('scripto_transcription_element_id', get_option('scripto_transcription_element_id'), array(), $itemTypeElements); ?>
        <p class="explanation">You must select an element belonging to the 
        selected item type. Scripto will export transcriptions from MediaWiki to 
        this element.</p>
    </div>
</div>