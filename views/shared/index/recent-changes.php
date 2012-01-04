<?php
$head = array('title' => html_escape('Scripto'));
head($head);
?>
<h1><?php echo $head['title']; ?></h1>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-recent-changes" class="scripto">
<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
Logged in as <a href="<?php echo html_escape(uri('scripto')); ?>"><?php echo $this->scripto->getUserName(); ?></a> 
(<a href="<?php echo html_escape(uri('scripto/logout')); ?>">logout</a>) 
 | <a href="<?php echo html_escape(uri('scripto/watchlist')); ?>">Your watchlist</a> 
<?php else: ?>
<a href="<?php echo html_escape(uri('scripto/login')); ?>">Log in to Scripto</a>
<?php endif; ?>
</p>

<!-- recent changes -->
<h2>Recent Changes</h2>
<?php if (empty($this->recentChanges)): ?>
<p>There are no recent changes.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Changes</th>
            <th>Document Page Name</th>
            <th>Changed on</th>
            <th>Changed</th>
            <th>Changed By</th>
            <th>Document Title</th>
        </tr>
    </thead>
    <tbody>
    <?php $types = array('new' => 'Created', 'edit' => 'Edited'); ?>
    <?php foreach ($this->recentChanges as $recentChange): ?>
    <?php
    $changes = ucfirst($recentChange['action']);
	$urlDiff = uri(array(
		'item-id' => $recentChange['document_id'], 
		'file-id' => $recentChange['document_page_id'], 
		'namespace-index' => $recentChange['namespace_index'], 
		'old-revision-id' => $recentChange['old_revision_id'], 
		'revision-id' => $recentChange['revision_id'], 
	), 'scripto_diff');
	$urlHistory = uri(array(
		'item-id' => $recentChange['document_id'], 
		'file-id' => $recentChange['document_page_id'], 
		'namespace-index' => $recentChange['namespace_index'], 
	), 'scripto_history');
	if ($recentChange['new'] || in_array($recentChange['action'], array('protected', 'unprotected'))) {
		$changes .= ' (diff | <a href="' . html_escape($urlHistory) . '">hist</a>)';
	} else {
		$changes .= ' (<a href="' . html_escape($urlDiff) . '">diff</a> | <a href="' . html_escape($urlHistory) . '">hist</a>)';
	}
    
    // document page name
    $documentPageName = ScriptoPlugin::truncate($recentChange['document_page_name'], 30);
    $urlTranscribe = uri(array(
        'action' => 'transcribe', 
        'item-id' => $recentChange['document_id'], 
        'file-id' => $recentChange['document_page_id']
    ), 'scripto_action_item_file');
    if (1 == $recentChange['namespace_index']) {
        $urlTranscribe .= '#discussion';
    } else {
        $urlTranscribe .= '#transcription';
    }
    
    // document title
    $documentTitle = ScriptoPlugin::truncate($recentChange['document_title'], 30, 'Untitled');
    $urlItem = uri(array(
        'controller' => 'items', 
        'action' => 'show', 
        'id' => $recentChange['document_id']
    ), 'id');
    
    // length changed
    $lengthChanged = $recentChange['new_length'] - $recentChange['old_length'];
    if (0 <= $lengthChanged) {
        $lengthChanged = "+$lengthChanged";
    }
    ?>
    <tr>
        <td><?php echo $changes; ?></td>
        <td><a href="<?php echo html_escape($urlTranscribe); ?>"><?php if (1 == $recentChange['namespace_index']): ?>Talk: <?php endif; ?><?php echo $documentPageName; ?></a></td>
        <td><?php echo date('H:i:s M d, Y', strtotime($recentChange['timestamp'])); ?></td>
        <td><?php echo $lengthChanged; ?></td>
        <td><?php echo $recentChange['user']; ?></td>
        <td><a href="<?php echo html_escape($urlItem); ?>"><?php echo $documentTitle; ?></a></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
</div><!-- #scripto-recent-changes -->
</div>
<?php foot(); ?>