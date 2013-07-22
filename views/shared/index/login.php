<?php
$titleArray = array(__('Scripto'), __('Login'));
$title = implode(' | ', $titleArray);
$head = array('title' => html_escape($title));
echo head($head);
?>
<?php if (!is_admin_theme()): ?>
<h1><?php echo $head['title']; ?></h1>
<?php endif; ?>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-login" class="scripto">
<!-- navigation -->
<p><a href="<?php echo html_escape(url('scripto/index/recent-changes')); ?>"><?php echo __('Recent changes'); ?></a></p>
<p><?php echo __(
    'Log in to Scripto using your MediaWiki username and password to access your account ' 
  . 'and enable certain Scripto features. Login may not be required by the administrator.'
); ?></p>

<!-- login -->
<form action="<?php echo html_escape(url('scripto/index/login')); ?>" method="post">
<div class="field">
    <label for="scripto_mediawiki_username"><?php echo __('Username'); ?></label>
        <div class="inputs">
        <?php echo $this->formText('scripto_mediawiki_username', null, array('size' => 18)); ?>
    </div>
</div>
<div class="field">
    <label for="scripto_mediawiki_password"><?php echo __('Password'); ?></label>
        <div class="inputs">
        <?php echo $this->formPassword('scripto_mediawiki_password', null, array('size' => 18)); ?>
    </div>
</div>
<?php echo $this->formHidden('scripto_redirect_url', $this->redirectUrl); ?>
<?php echo $this->formSubmit('scripto_mediawiki_login', __('Login'), array('style' => 'display:inline; float:none;')); ?>
</form>
</div><!-- #scripto-login -->
</div>
<?php echo foot(); ?>
