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
        <th>Document Page</th>
        <th>Contribution Time</th>
        <th>Document Title</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($documentPages as $documentPage): ?>
    <tr>
        <td><a href="<?php echo uri(array('action' => 'transcribe', 'doc' => $documentPage['document_id'], 'page' => $documentPage['document_page_id'])); ?>">doc <?php echo $documentPage['document_id']; ?>, page <?php echo $documentPage['document_page_id']; ?></a></td>
        <td><?php echo gmdate('Y M d, H:i:s', strtotime($documentPage['timestamp'])); ?></td>
        <td><?php echo $documentPage['document_title']; ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php foot(); ?>