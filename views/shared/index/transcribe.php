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
        jQuery('#scripto-transcription-page-edit').prop('disabled', true).text('Editing transcription...');
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
                jQuery('#scripto-transcription-page-edit').prop('disabled', false).text('Edit transcription');
                jQuery('#scripto-transcription-page-html').html(data);
            }
        );
    });
    
    // Handle edit talk page.
    jQuery('#scripto-talk-page-edit').click(function() {
        jQuery('#scripto-talk-page-edit').prop('disabled', true).text('Editing discussion...');
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
                jQuery('#scripto-talk-page-edit').prop('disabled', false).text('Edit discussion');
                jQuery('#scripto-talk-page-html').html(data);
            }
        );
    });
    
    // Handle default transcription/talk visibility.
    if (window.location.hash == '#discussion') {
        jQuery('#scripto-transcription').hide();
        jQuery('#scripto-page-show').text('show transcription');
    } else {
        jQuery('#scripto-talk').hide();
        jQuery('#scripto-page-show').text('show discussion');
    }
    
    // Handle transcription/talk visibility.
    jQuery('#scripto-page-show').click(function() {
        event.preventDefault();
        if ('show discussion' == jQuery('#scripto-page-show').text()) {
            window.location.hash = '#discussion';
            jQuery('#scripto-transcription').hide();
            jQuery('#scripto-talk').show();
            jQuery('#scripto-page-show').text('show transcription');
        } else {
            window.location.hash = '#transcription';
            jQuery('#scripto-talk').hide();
            jQuery('#scripto-transcription').show();
            jQuery('#scripto-page-show').text('show discussion');
        }
    });
    
    // Toggle show transcription edit.
    jQuery('#scripto-transcription-edit-show').toggle(function(event) {
        event.preventDefault();
        jQuery(this).text('hide edit');
        jQuery('#scripto-transcription-edit').slideDown('fast');
    }, function(event) {
        event.preventDefault();
        jQuery(this).text('edit');
        jQuery('#scripto-transcription-edit').slideUp('fast');
    });
    
    // Toggle show talk edit.
    jQuery('#scripto-talk-edit-show').toggle(function(event) {
        event.preventDefault();
        jQuery(this).text('hide edit');
        jQuery('#scripto-talk-edit').slideDown('fast');
    }, function(event) {
        event.preventDefault();
        jQuery(this).text('edit');
        jQuery('#scripto-talk-edit').slideUp('fast');
    });
    
    <?php if ($this->scripto->isLoggedIn()): ?>
    
    // Handle default un/watch page.
    <?php if ($this->doc->isWatchedPage()): ?>
    jQuery('#scripto-page-watch').text('Unwatch').css('float', 'none');
    <?php else: ?>
    jQuery('#scripto-page-watch').text('Watch').css('float', 'none');
    <?php endif; ?>
    
    // Handle un/watch page.
    jQuery('#scripto-page-watch').click(function() {
        if ('Watch' == jQuery(this).text()) {
            jQuery(this).prop('disabled', true).text('Watching...');
            jQuery.post(
                <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
                {
                    page_action: 'watch', 
                    page: 'transcription', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-page-watch').prop('disabled', false).text('Unwatch');
                }
            );
        } else {
            jQuery(this).prop('disabled', true).text('Unwatching...');
            jQuery.post(
                <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
                {
                    page_action: 'unwatch', 
                    page: 'transcription', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-page-watch').prop('disabled', false).text('Watch');
                }
            );
        }
    });
    
    <?php endif; // end isLoggedIn() ?>
    
    <?php if ($this->scripto->canProtect()): ?>
    
    // Handle default un/protect transcription page.
    <?php if ($this->doc->isProtectedTranscriptionPage()): ?>
    jQuery('#scripto-transcription-page-protect').text('Unprotect').css('float', 'none');
    <?php else: ?>
    jQuery('#scripto-transcription-page-protect').text('Protect').css('float', 'none');
    <?php endif; ?>
    
    // Handle un/protect transcription page.
    jQuery('#scripto-transcription-page-protect').click(function() {
        if ('Protect' == jQuery(this).text()) {
            jQuery(this).prop('disabled', true).text('Protecting...');
            jQuery.post(
                <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
                {
                    page_action: 'protect', 
                    page: 'transcription', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-transcription-page-protect').prop('disabled', false).text('Unprotect');
                }
            );
        } else {
            jQuery(this).prop('disabled', true).text('Unprotecting...');
            jQuery.post(
                <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
                {
                    page_action: 'unprotect', 
                    page: 'transcription', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-transcription-page-protect').prop('disabled', false).text('Protect');
                }
            );
        }
    });
    
    // Handle default un/protect talk page.
    <?php if ($this->doc->isProtectedTalkPage()): ?>
    jQuery('#scripto-talk-page-protect').text('Unprotect').css('float', 'none');
    <?php else: ?>
    jQuery('#scripto-talk-page-protect').text('Protect').css('float', 'none');
    <?php endif; ?>
    
    // Handle un/protect talk page.
    jQuery('#scripto-talk-page-protect').click(function() {
        if ('Protect' == jQuery(this).text()) {
            jQuery(this).prop('disabled', true).text('Protecting...');
            jQuery.post(
                <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
                {
                    page_action: 'protect', 
                    page: 'talk', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-talk-page-protect').prop('disabled', false).text('Unprotect');
                }
            );
        } else {
            jQuery(this).prop('disabled', true).text('Unprotecting...');
            jQuery.post(
                <?php echo js_escape(uri('scripto/index/page-action')); ?>, 
                {
                    page_action: 'unprotect', 
                    page: 'talk', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-talk-page-protect').prop('disabled', false).text('Protect');
                }
            );
        }
    });
    
    <?php endif; // end canProtect() ?>
});
</script>
<h1><?php echo'Scripto | Transcribe'; ?></h1>
<div id="primary">
<?php echo flash(); ?>

<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
Logged in as <a href="<?php echo uri('scripto'); ?>"><?php echo $this->scripto->getUserName(); ?></a> 
(<a href="<?php echo uri('scripto/logout'); ?>">logout</a>) 
 | <a href="<?php echo uri('scripto/watchlist'); ?>">Your watchlist</a> 
<?php else: ?>
<a href="<?php echo uri('scripto/login'); ?>">Log into Scripto</a>
<?php endif; ?>
 | <a href="<?php echo uri('scripto/recent-changes'); ?>">Recent changes</a> 
 | <a href="<?php echo uri(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => 0), 'scripto_history'); ?>">Transcription history</a>
 | <a href="<?php echo uri(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => 1), 'scripto_history'); ?>">Discussion history</a>
</p> 

<h2><?php if ($this->doc->getTitle()): ?><?php echo $this->doc->getTitle(); ?><?php else: ?>Untitled<?php endif; ?></h2>
<h3><?php echo $this->doc->getPageName(); ?></h3>

<?php echo display_file($this->file); ?>

<!-- pagination -->
<p>
<?php if (isset($this->paginationUrls['previous'])): ?><a href="<?php echo $this->paginationUrls['previous']; ?>">&#171; previous page</a><?php else: ?>&#171; previous page<?php endif; ?>
 | <?php if (isset($this->paginationUrls['next'])): ?><a href="<?php echo $this->paginationUrls['next']; ?>">next page &#187;</a><?php else: ?>next page &#187;<?php endif; ?>
 | <a href="#" id="scripto-page-show"></a> 
 | <a href="http://www.mediawiki.org/wiki/Help:Formatting" target="_blank">wiki formatting help</a>
</p>

<!-- transcription -->
<div id="scripto-transcription">
    <?php if ($this->doc->canEditTranscriptionPage()): ?>
    <div id="scripto-transcription-edit" style="display: none;">
        <div><?php echo $this->formTextarea('scripto-transcription-page-wikitext', $this->doc->getTranscriptionPageWikitext(), array('cols' => '76', 'rows' => '16')); ?></div>
        <div>
            <?php echo $this->formButton('scripto-transcription-page-edit', 'Edit transcription', array('style' => 'display:inline; float:none;')); ?> 
            <?php if ($this->scripto->isLoggedIn()): ?><?php echo $this->formButton('scripto-page-watch'); ?> <?php endif; ?>
            <?php if ($this->scripto->canProtect()): ?><?php echo $this->formButton('scripto-transcription-page-protect'); ?> <?php endif; ?>
        </div>
    </div><!-- #scripto-transcription-edit -->
    <?php else: ?>
    <p style="color: red;">You don't have permission to transcribe this page.</p>
    <?php endif; ?>
    <h2>Current Transcription<?php if ($this->doc->canEditTranscriptionPage()): ?> [<a href="#" id="scripto-transcription-edit-show">edit</a>]<?php endif; ?></h2>
    <div id="scripto-transcription-page-html"><?php echo $this->transcriptionPageHtml; ?></div>
</div><!-- #scripto-transcription -->

<!-- discussion -->
<div id="scripto-talk">
    <?php if ($this->doc->canEditTalkPage()): ?>
    <div id="scripto-talk-edit" style="display: none;">
        <div><?php echo $this->formTextarea('scripto-talk-page-wikitext', $this->doc->getTalkPageWikitext(), array('cols' => '76', 'rows' => '16')); ?></div>
        <div>
            <?php echo $this->formButton('scripto-talk-page-edit', 'Edit discussion', array('style' => 'display:inline; float:none;')); ?> 
            <?php if ($this->scripto->canProtect()): ?><?php echo $this->formButton('scripto-talk-page-protect'); ?> <?php endif; ?>
        </div>
    </div><!-- #scripto-talk-edit -->
    <?php else: ?>
    <p style="color: red;">You don't have permission to discuss this page.</p>
    <?php endif; ?>
    <h2>Current Discussion<?php if ($this->doc->canEditTalkPage()): ?> [<a href="#" id="scripto-talk-edit-show">edit</a>]<?php endif; ?></h2>
    <div id="scripto-talk-page-html"><?php echo $this->talkPageHtml; ?></div>
</div><!-- #scripto-talk -->

</div>
<?php foot(); ?>