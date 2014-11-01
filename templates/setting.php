<?php
	/** @var \MeCabSweet\Screen\Setting $this */
?>
<h3><?php $this->i18n->_e('Settings') ?></h3>
<form action="<?php echo $this->url('mecab-setting') ?>" method="post">
	<?php wp_nonce_field('mecab-setting') ?>
	<table class="form-table">
		<tr>
			<th><?php $this->i18n->_e('Full Text Search') ?></th>
			<td>
				<label>
					<input type="checkbox" name="fulltext_search" value="1"<?php checked($this->option->fulltext_search) ?> />
					<?php $this->i18n->_e('Enable') ?>
				</label>
				<p class="description">
					<?php $this->i18n->_e('If enabled, create extra table for full-text search indexing. This feature take effect when the amount of your posts are more than 2,000.') ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php $this->i18n->_e('User Dictionary') ?></th>
			<td>
				<label>
					<input type="checkbox" name="user_dic" value="1"<?php checked($this->option->user_dic) ?> />
					<?php $this->i18n->_e('Enable') ?>
				</label>
				<p class="description">
					<?php $this->i18n->_p('If enabled, use user dictionary CSV specified on next field. About user dictionary, please see <a href="%s" target="_blank">MeCab Manual</a>.', 'http://mecab.googlecode.com/svn/trunk/mecab/doc/dic.html') ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><label for="user_dic_path"><?php $this->i18n->_e('User Dictionary Path') ?></label></th>
			<td>
				<input type="text" class="regular-text" id="user_dic_path" name="user_dic_path" value="<?php echo esc_attr($this->option->user_dic_path) ?>" placeholder="<?php echo esc_attr($this->i18n->__('Specify CSV file path on your server.')) ?>" />
				<p class="description">
					<?php $this->i18n->_e('Is this too complicated? You can install empty CSV.') ?>
					<a class="button" id="mecab-csv-installer" href="<?php echo wp_nonce_url(admin_url('admin-ajax.php?action=mecab_install_csv'), 'mecab_install_csv') ?>"><?php $this->i18n->_e('Install') ?></a>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php $this->i18n->_e('Dictionary Management Tool') ?></th>
			<td>
				<label>
					<input type="checkbox" name="taxonomy" value="1"<?php checked($this->option->taxonomy) ?> />
					<?php $this->i18n->_e('Enable') ?>
				</label>
				<p class="description">
					<?php $this->i18n->_e('MeCab expects user dictionary as CSV. To avoid annoying CSV uploading, you can get GUI tool for making one.') ?>
				</p>
			</td>
		</tr>
	</table>
	<?php submit_button() ?>
</form>
