<h2>Transcribe This Item</h2>
<ol>
    <?php foreach ($doc->getPages() as $pageId => $pageName): ?>
    <li><a href="<?php echo uri(array('action' => 'transcribe', 
                                      'item-id' => $item->id, 
                                      'file-id' => $pageId), 
                                'scripto_action_item_file'); ?>" id="scripto-transcribe-item"><?php echo $pageName; ?></a></li>
    <?php endforeach; ?>
</ol>