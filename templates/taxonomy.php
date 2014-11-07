<?php
/** @var \MecabSweet\Screen\Taxonomy $this */
?>
<h2>
	<span class="dashicons dashicons-translation"></span> <?php $this->i18n->_e('Registered Words') ?>
	<a href="<?php echo $this->url('mecab-dic-add') ?>" class="button"><?php $this->i18n->_e('Add New Word') ?></a>
</h2>

<p>
	<?php $this->i18n->_e('These words will be added to your system dictionary as user dictionary.') ?>
</p>

