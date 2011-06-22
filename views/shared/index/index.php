<?php
$head = array('title' => html_escape('Scripto'));
head($head);
?>
<h1><?php echo $head['title']; ?></h1>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-index" class="scripto">
<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
Logged in as <?php echo $this->scripto->getUserName(); ?> 
(<a href="<?php echo html_escape(uri('scripto/logout')); ?>">logout</a>) 
 | <a href="<?php echo html_escape(uri('scripto/watchlist')); ?>">Your watchlist</a> 
<?php else: ?>
<a href="<?php echo html_escape(uri('scripto/login')); ?>">Log in to Scripto</a> 
<?php endif; ?>
 | <a href="<?php echo html_escape(uri('scripto/recent-changes')); ?>">Recent changes</a>
</p>

<!-- your contributions -->
<?php if (!$this->scripto->isLoggedIn()): ?>
<?php if ($this->homePageText): ?>
<?php echo $this->homePageText ?>
<?php else: ?>
<h2>Welcome to Scripto!</h2>
<p>By using this plugin you are helping to transcribe items 
in <i><?php echo settings('site_title'); ?></i>. All items with files can be 
transcribed. For these purposes an item is a <em>document</em>, and an item's 
files are its <em>pages</em>. To begin transcribing documents, 
<a href="<?php echo html_escape(uri('items')); ?>">browse items</a> or 
<a href="<?php echo html_escape(uri('scripto/recent-changes')); ?>">view recent changes</a> 
to Scripto. You may <a href="<?php echo html_escape(uri('scripto/login')); ?>">log in</a> to 
access your account and enable certain Scripto features. Login may not be 
required by the administrator.</p>
<?php endif; ?>
<?php else: ?>
<h2>Your Contributions</h2>
<?php if (empty($this->documentPages)): ?>
<p>You have no contributions.</p>
<?php else: ?>
<table>
    <thead>
    <tr>
        <th>Document Page Name</th>
        <th>Most Recent Contribution</th>
        <th>Document Title</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->documentPages as $documentPage): ?>
    <?php
    // document page name
    $documentPageName = ScriptoPlugin::truncate($documentPage['document_page_name'], 60);
    $urlTranscribe = uri(array(
        'action' => 'transcribe', 
        'item-id' => $documentPage['document_id'], 
        'file-id' => $documentPage['document_page_id']
    ), 'scripto_action_item_file');
    if (1 == $documentPage['namespace_index']) {
        $urlTranscribe .= '#discussion';
    } else {
        $urlTranscribe .= '#transcription';
    }
    
    // document title
    $documentTitle = ScriptoPlugin::truncate($documentPage['document_title'], 60, 'Untitled');
    $urlItem = uri(array(
        'controller' => 'items', 
        'action' => 'show', 
        'id' => $documentPage['document_id']
    ), 'id');
    ?>
    <tr>
        <td><a href="<?php echo html_escape($urlTranscribe); ?>"><?php if (1 == $documentPage['namespace_index']): ?>Talk: <?php endif; ?><?php echo $documentPageName; ?></a></td>
        <td><?php echo gmdate('H:i:s M d, Y', strtotime($documentPage['timestamp'])); ?></td>
        <td><a href="<?php echo html_escape($urlItem); ?>"><?php echo $documentTitle; ?></a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
<?php endif; ?>
</div><!-- #scripto-index -->
</div>
<?php foot(); ?>