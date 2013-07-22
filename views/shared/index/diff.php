<?php
$titleArray = array(__('Scripto'), __('Revision Difference'));
$titleArray[] = (1 == $this->namespaceIndex) ? __('Discussion') : __('Transcription');
$title = implode(' | ', $titleArray);
$head = array('title' => html_escape($title));
echo head($head);
?>
<style type="text/css">
#scripto-diff tr {border: none !important;}
#scripto-diff td {padding: 2px !important;}
td.diff-marker {width: 10px;}
td.diff-deletedline {background-color: #FFEDED;}
td.diff-addedline {background-color: #EDFFEF;}
ins.diffchange {background-color: #BDFFC8;}
del.diffchange {background-color: #FFBDBD;}
</style>
<?php if (!is_admin_theme()): ?>
<h1><?php echo $head['title']; ?></h1>
<?php endif; ?>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-diff" class="scripto">
<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
<?php echo __('Logged in as %s', '<a href="' . html_escape(url('scripto')) . '">' . $this->scripto->getUserName() . '</a>'); ?> 
(<a href="<?php echo html_escape(url('scripto/index/logout')); ?>"><?php echo __('logout'); ?></a>) 
 | <a href="<?php echo html_escape(url('scripto/watchlist')); ?>"><?php echo __('Your watchlist'); ?></a> 
<?php else: ?>
<a href="<?php echo html_escape(url('scripto/index/login')); ?>"><?php echo __('Log in to Scripto'); ?></a>
<?php endif; ?>
 | <a href="<?php echo html_escape(url('scripto/recent-changes')); ?>"><?php echo __('Recent changes'); ?></a> 
 | <a href="<?php echo html_escape(url(array('controller' => 'items', 'action' => 'show', 'id' => $this->doc->getId()), 'id')); ?>"><?php echo __('View item'); ?></a>
 | <a href="<?php echo html_escape(url(array('controller' => 'files', 'action' => 'show', 'id' => $this->doc->getPageId()), 'id')); ?>"><?php echo __('View file'); ?></a>
 | <a href="<?php echo html_escape(url(array('action' => 'transcribe', 'item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId()), 'scripto_action_item_file')); ?>"><?php echo __('Transcribe page'); ?></a>
 | <a href="<?php echo html_escape(url(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => $this->namespaceIndex), 'scripto_history')); ?>"><?php echo __('View history'); ?></a>
</p> 

<h2><?php if ($this->doc->getTitle()): ?><?php echo $this->doc->getTitle(); ?><?php else: ?><?php echo __('Untitled Document'); ?><?php endif; ?></h2>
<h3><?php echo $this->doc->getPageName(); ?></h3>

<!-- difference -->
<table>
    <thead>
    <tr>
        <th colspan="2"><?php echo __('Revision as of %s', format_date(strtotime($this->oldRevision['timestamp']), Zend_Date::DATETIME_MEDIUM)); ?><br />
        <?php echo __($this->oldRevision['action'] . ' by'); ?> <?php echo $this->oldRevision['user']; ?></th>
        <th colspan="2"><?php echo __('Revision as of %s', format_date(strtotime($this->revision['timestamp']), Zend_Date::DATETIME_MEDIUM)); ?><br />
        <?php echo __($this->revision['action'] . ' by'); ?> <?php echo $this->revision['user']; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php echo $this->diff; ?>
    </tbody>
</table>
<h2><?php echo __('Revision as of %s', format_date(strtotime($this->revision['timestamp']), Zend_Date::DATETIME_MEDIUM)); ?></h2>
<div><?php echo $this->revision['html']; ?></div>
</div><!-- #scripto-diff -->
</div>
<?php echo foot(); ?>
