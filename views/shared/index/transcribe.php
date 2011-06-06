<?php
$head = array('title' => html_escape('Scripto | Transcribe'));
head($head);
?>
<?php echo js('OpenLayers'); ?>
<?php echo js('jquery'); ?>
<script type="text/javascript">
jQuery(document).ready(function() {
    
    // Handle edit transcription page.
    jQuery('#scripto-transcription-page-edit').click(function() {
        jQuery('#scripto-transcription-page-edit').prop('disabled', true).text('Editing...');
        jQuery.post(
            <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
            {
                page_action: 'edit', 
                page: 'transcription', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>, 
                wikitext: jQuery('#scripto-transcription-page-wikitext').val()
            }, 
            function(data) {
                jQuery('#scripto-transcription-page-edit').prop('disabled', false).text('Edit');
                jQuery('#scripto-transcription-page-html').html(data);
            }
        );
    });
    
    // Handle edit talk page.
    jQuery('#scripto-talk-page-edit').click(function() {
        jQuery('#scripto-talk-page-edit').prop('disabled', true).text('Editing...');
        jQuery.post(
            <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
            {
                page_action: 'edit', 
                page: 'talk', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>, 
                wikitext: jQuery('#scripto-talk-page-wikitext').val()
            }, 
            function(data) {
                jQuery('#scripto-talk-page-edit').prop('disabled', false).text('Edit');
                jQuery('#scripto-talk-page-html').html(data);
            }
        );
    });
    
    // Handle watch transcription page.
    jQuery('#scripto-transcription-page-watch').click(function() {
        var watchButton = jQuery('#scripto-transcription-page-watch');
        var unwatchButton = jQuery('#scripto-transcription-page-unwatch');
        watchButton.prop('disabled', true).text('Watching...');
        jQuery.post(
            <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
            {
                page_action: 'watch', 
                page: 'transcription', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>
            }, 
            function(data) {
                watchButton.hide().prop('disabled', false).text('Watch');
                unwatchButton.show();
            }
        );
    });
    
    // Handle unwatch transcription page.
    jQuery('#scripto-transcription-page-unwatch').click(function() {
        var unwatchButton = jQuery('#scripto-transcription-page-unwatch');
        var watchButton = jQuery('#scripto-transcription-page-watch');
        unwatchButton.prop('disabled', true).text('Unwatching...');
        jQuery.post(
            <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
            {
                page_action: 'unwatch', 
                page: 'transcription', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>
            }, 
            function(data) {
                unwatchButton.hide().prop('disabled', false).text('Unwatch');
                watchButton.show();
            }
        );
    });
    
    <?php if ($this->scripto->canProtect()): ?>
    // Handle protect transcription page.
    jQuery('#scripto-transcription-page-protect').click(function() {
        var protectButton = jQuery('#scripto-transcription-page-protect');
        var unprotectButton = jQuery('#scripto-transcription-page-unprotect');
        protectButton.prop('disabled', true).text('Protecting...');
        jQuery.post(
            <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
            {
                page_action: 'protect', 
                page: 'transcription', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>
            }, 
            function(data) {
                protectButton.hide().prop('disabled', false).text('Protect');
                unprotectButton.show();
            }
        );
    });
    
    // Handle unprotect transcription page.
    jQuery('#scripto-transcription-page-unprotect').click(function() {
        var unprotectButton = jQuery('#scripto-transcription-page-unprotect');
        var protectButton = jQuery('#scripto-transcription-page-protect');
        unprotectButton.prop('disabled', true).text('Unprotecting...');
        jQuery.post(
            <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
            {
                page_action: 'unprotect', 
                page: 'transcription', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>
            }, 
            function(data) {
                unprotectButton.hide().prop('disabled', false).text('Unprotect');
                protectButton.show();
            }
        );
    });
    
    // Handle protect talk page.
    jQuery('#scripto-talk-page-protect').click(function() {
        var protectButton = jQuery('#scripto-talk-page-protect');
        var unprotectButton = jQuery('#scripto-talk-page-unprotect');
        protectButton.prop('disabled', true).text('Protecting...');
        jQuery.post(
            <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
            {
                page_action: 'protect', 
                page: 'talk', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>
            }, 
            function(data) {
                protectButton.hide().prop('disabled', false).text('Protect');
                unprotectButton.show();
            }
        );
    });
    
    // Handle unprotect talk page.
    jQuery('#scripto-talk-page-unprotect').click(function() {
        var unprotectButton = jQuery('#scripto-talk-page-unprotect');
        var protectButton = jQuery('#scripto-talk-page-protect');
        unprotectButton.prop('disabled', true).text('Unprotecting...');
        jQuery.post(
            <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
            {
                page_action: 'unprotect', 
                page: 'talk', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>
            }, 
            function(data) {
                unprotectButton.hide().prop('disabled', false).text('Unprotect');
                protectButton.show();
            }
        );
    });
    <?php endif; ?>
    
    // Handle default transcription/talk visibility.
    if (window.location.hash == '#discussion') {
        jQuery('#scripto-transcription').hide();
        jQuery('#scripto-talk-show').css('font-weight', 'bold');
    } else {
        jQuery('#scripto-talk').hide();
        jQuery('#scripto-transcription-show').css('font-weight', 'bold');
    }
    
    // Handle show transcription.
    jQuery('#scripto-transcription-show').click(function(event) {
        event.preventDefault();
        window.location.hash = '#transcription';
        jQuery('#scripto-talk').hide();
        jQuery('#scripto-transcription').show();
        jQuery('#scripto-talk-show').css('font-weight', 'inherit');
        jQuery('#scripto-transcription-show').css('font-weight', 'bold');
    });
    
    // Handle show talk.
    jQuery('#scripto-talk-show').click(function(event) {
        event.preventDefault();
        window.location.hash = '#discussion';
        jQuery('#scripto-transcription').hide();
        jQuery('#scripto-talk').show();
        jQuery('#scripto-transcription-show').css('font-weight', 'inherit');
        jQuery('#scripto-talk-show').css('font-weight', 'bold');
    });
    
    // Handle show transcription edit.
    jQuery('#scripto-transcription-edit-show').click(function() {
        jQuery(this).hide();
        jQuery('#scripto-transcription-edit').slideDown();
    });
    
    // Handle show talk edit.
    jQuery('#scripto-talk-edit-show').click(function() {
        jQuery(this).hide();
        jQuery('#scripto-talk-edit').slideDown();
    });
});
</script>
<h1><?php echo'Scripto | Transcribe'; ?></h1>
<div id="primary">
<?php echo flash(); ?>

<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
Logged in as <strong><a href="<?php echo uri('scripto'); ?>"><?php echo $this->scripto->getUserName(); ?></a></strong> 
(<a href="<?php echo uri('scripto/logout'); ?>">logout</a>)
<?php else: ?>
<a href="<?php echo uri('scripto/login'); ?>">Log into Scripto</a>
<?php endif; ?>
 | <a href="<?php echo uri('scripto/recent-changes'); ?>">Recent changes</a> 
 | <a href="<?php echo uri(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => 0), 'scripto_history'); ?>">Transcription history</a>
 | <a href="<?php echo uri(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => 1), 'scripto_history'); ?>">Discussion history</a>
</p> 

<h2><?php if ($this->doc->getTitle()): ?><?php echo $this->doc->getTitle(); ?><?php else: ?>Untitled<?php endif; ?></h2>
<h3><?php echo $this->pages[$this->doc->getPageId()]; ?></h3>

<?php echo display_file($this->file, array('image_size' => $this->imageSize, 
                                           'page_file_url' => $this->pageFileUrl)); ?>

<!-- pagination -->
<p>
<?php if (isset($this->paginationUrls['previous'])): ?><a href="<?php echo $this->paginationUrls['previous']; ?>">&#171; previous page</a><?php else: ?>&#171; previous page<?php endif; ?>
 | <?php if (isset($this->paginationUrls['next'])): ?><a href="<?php echo $this->paginationUrls['next']; ?>">next page &#187;</a><?php else: ?>next page &#187;<?php endif; ?>
 | <a id="scripto-transcription-show" href="#transcription">show transcription</a> 
 | <a id="scripto-talk-show" href="#discussion">show discussion</a>
</p>

<!-- transcription -->
<div id="scripto-transcription">
    <?php if ($this->doc->canEditTranscriptionPage()): ?>
    <div id="scripto-transcription-edit" style="display: none;">
        <div><?php echo $this->formTextarea('scripto-transcription-page-wikitext', $this->doc->getTranscriptionPageWikitext(), array('cols' => '76', 'rows' => '16')); ?></div>
        <div>
            <?php echo $this->formButton('scripto-transcription-page-edit', 'Edit', array('style' => 'display:inline; float:none;')); ?> 
            <?php if ($this->scripto->isLoggedIn()): ?>
            <?php
            if ($this->doc->isWatchedTranscriptionPage()) {
                $transcriptionWatchStyle = 'display: none; float: none;';
                $transcriptionUnwatchStyle = 'display: inline; float: none;';
            } else {
                $transcriptionWatchStyle = 'display: inline; float: none';
                $transcriptionUnwatchStyle = 'display: none; float: none;';
            }
            ?>
            <?php echo $this->formButton('scripto-transcription-page-watch', 'Watch', array('style' => $transcriptionWatchStyle)); ?> 
            <?php echo $this->formButton('scripto-transcription-page-unwatch', 'Unwatch', array('style' => $transcriptionUnwatchStyle)); ?> 
            <?php endif; ?>
            <?php if ($this->scripto->canProtect()): ?>
            <?php
            if ($this->doc->isProtectedTranscriptionPage()) {
                $transcriptionProtectStyle = 'display: none; float: none;';
                $transcriptionUnprotectStyle = 'display: inline; float: none;';
            } else {
                $transcriptionProtectStyle = 'display: inline; float: none';
                $transcriptionUnprotectStyle = 'display: none; float: none;';
            }
            ?>
            <?php echo $this->formButton('scripto-transcription-page-protect', 'Protect', array('style' => $transcriptionProtectStyle)); ?> 
            <?php echo $this->formButton('scripto-transcription-page-unprotect', 'Unprotect', array('style' => $transcriptionUnprotectStyle)); ?> 
            <?php endif; ?>
        </div>
    </div><!-- #scripto-transcription-edit -->
    <?php else: ?>
    <p style="color: red;">You don't have permission to transcribe this page.</p>
    <?php endif; ?>
    <?php echo $this->formButton('scripto-transcription-edit-show', 'Edit transcription', array('style' => 'display:inline; float:none;')); ?>
    <h2>Current Transcription</h2>
    <div id="scripto-transcription-page-html"><?php echo $this->transcriptionPageHtml; ?></div>
</div><!-- #scripto-transcription -->

<!-- discussion -->
<div id="scripto-talk">
    <?php if ($this->doc->canEditTalkPage()): ?>
    <div id="scripto-talk-edit" style="display: none;">
        <div><?php echo $this->formTextarea('scripto-talk-page-wikitext', $this->doc->getTalkPageWikitext(), array('cols' => '76', 'rows' => '16')); ?></div>
        <div>
            <?php echo $this->formButton('scripto-talk-page-edit', 'Edit', array('style' => 'display:inline; float:none;')); ?> 
            <?php if ($this->scripto->canProtect()): ?>
            <?php
            if ($this->doc->isProtectedTalkPage()) {
                $talkProtectStyle = 'display: none; float: none;';
                $talkUnprotectStyle = 'display: inline; float: none;';
            } else {
                $talkProtectStyle = 'display: inline; float: none';
                $talkUnprotectStyle = 'display: none; float: none;';
            }
            ?>
            <?php echo $this->formButton('scripto-talk-page-protect', 'Protect', array('style' => $talkProtectStyle)); ?> 
            <?php echo $this->formButton('scripto-talk-page-unprotect', 'Unprotect', array('style' => $talkUnprotectStyle)); ?> 
            <?php endif; ?>
        </div>
    </div><!-- #scripto-talk-edit -->
    <?php else: ?>
    <p style="color: red;">You don't have permission to discuss this page.</p>
    <?php endif; ?>
    <?php echo $this->formButton('scripto-talk-edit-show', 'Edit discussion', array('style' => 'display:inline; float:none;')); ?>
    <h2>Current Discussion</h2>
    <div id="scripto-talk-page-html"><?php echo $this->talkPageHtml; ?></div>
</div><!-- #scripto-talk -->

</div>
<?php foot(); ?>