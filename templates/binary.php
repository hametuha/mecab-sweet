<?php
/** @var \MeCabSweet\Pattern\AdminScreen $this */
?>

<button class="dictionary-compiler"
        data-endpoint="<?php echo home_url('/mecab-terms/binary/', force_ssl_admin() ? 'https' : 'http') ?>"
        data-nonce="<?php echo wp_create_nonce('mecab_term_edit') ?>"
        data-loading="<?php echo esc_attr($this->i18n->__('Processing...')) ?>">
	<?php $this->i18n->_e('Make Dictionary') ?>
</button>
<img src="<?php echo $this->base_url ?>assets/img/ajax-loader-sm.gif" alt="Loading..." >