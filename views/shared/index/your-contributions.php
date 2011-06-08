<?php
$head = array('title' => html_escape('Scripto | Your Contributions'));
head($head);
?>
<h1><?php echo $head['title']; ?></h1>
<div id="primary">
<?php echo flash(); ?>

<!-- navigation -->
<p>
Logged in as <a href="<?php echo uri('scripto'); ?>"><?php echo $this->scripto->getUserName(); ?></a> 
(<a href="<?php echo uri('scripto/logout'); ?>">logout</a>)
 | <a href="<?php echo uri('scripto/recent-changes'); ?>">Recent changes</a>
</p>

<table>
    <thead>
    <tr>
        <th>Document Page Name</th>
        <th>Most Recent Contribution</th>
        <th>Document Title</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($documentPages as $documentPage): ?>
    <?php
    // document page name
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
    $urlItem = uri(array(
        'controller' => 'items', 
        'action' => 'show', 
        'id' => $documentPage['document_id']
    ), 'id');
    ?>
    <tr>
        <td><a href="<?php echo $urlTranscribe; ?>"><?php if (1 == $documentPage['namespace_index']): ?>Talk: <?php endif; ?>doc <?php echo $documentPage['document_id']; ?>, page <?php echo $documentPage['document_page_id']; ?></a></td>
        <td><?php echo gmdate('H:i:s M d, Y', strtotime($documentPage['timestamp'])); ?></td>
        <td><a href="<?php echo $urlItem; ?>"><?php echo $documentPage['document_title']; ?></a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php foot(); ?>