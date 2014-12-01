<?php
/** @var \MecabSweet\UI\Screen\TaxonomyAdd $this */

?>
<h2>
	<span class="dashicons dashicons-plus-alt"></span>
	<?php echo $this->is_editing() ? $this->i18n->__('Edit Word') : $this->i18n->__('Add Word') ?>
	<small>
		<?php $this->i18n->_p('or <a href="%s">Back to list</a>', $this->url('mecab-dic-registered')) ?>
	</small>
</h2>

<form method="post" id="morphem-editor" action="<?php echo home_url('/mecab-terms/'.($this->is_editing() ? 'edit/'.$this->get_morpheme_attr('term_id') : 'add'), force_ssl_admin() ? 'https' : 'http') ?>">
	<?php wp_nonce_field('mecab_term_edit') ?>
	<table class="form-table">
		<tr>
			<th><label for="morpheme"><?php $this->i18n->_e('Surface') ?></label></th>
			<td>
				<input type="text" class="regular-text" name="morpheme" id="morpheme" value="<?php $this->morpheme_attr('morpheme') ?>" placeholder="ex. 学校" />
			</td>
		</tr>
		<tr>
			<th><label for="ruby"><?php $this->i18n->_e('Ruby') ?></label></th>
			<td>
				<input type="text" class="regular-text" name="ruby" id="ruby" value="<?php $this->morpheme_attr('ruby') ?>" placeholder="ex. ガッコウ" />
				<p class="description">
					<?php $this->i18n->_e('Must be Katakana.') ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><label for="pronunciation"><?php $this->i18n->_e('Pronunciation') ?></label></th>
			<td>
				<input type="text" class="regular-text" name="pronunciation" id="pronunciation" value="<?php $this->morpheme_attr('pronunciation') ?>" placeholder="ex. ガッコー" />
				<p class="description">
					<?php $this->i18n->_e('Must be Katakana.') ?>
					<?php $this->i18n->_e('For example, <strong>学校</strong>\'s pronunciation shouldn\'t be <em>ガッコウ</em> but <strong>ガッコー</strong>.') ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><label for="cost"><?php $this->i18n->_e('Cost') ?></label></th>
			<td>
				<input type="number" name="cost" id="cost" value="<?php $this->morpheme_attr('cost') ?>" placeholder="<?php $this->i18n->_e('Enter cost') ?>" />
				<p class="description">
					<?php $this->i18n->_e('When MeCab splits strings to tokens, it calculate each word\'s superiority with cost.') ?>
					<?php $this->i18n->_e('Low cost means high priority. Good practice is assigning same cost as similar word with tool below.') ?>
				</p>
				<input type="text" id="cost-calc" placeholder="<?php $this->i18n->_e('Search similar word\'s cost') ?>" />
				<a id="cost-exec" class="button" href="<?php echo home_url('/mecab-terms/info/', force_ssl_admin() ? 'https' : 'http') ?>"><?php $this->i18n->_e('Search') ?></a>
				<div id="token-result" class="token-container"></div>
			</td>
		</tr>
		<tr>
			<th><label for="part_of_speech"><?php $this->i18n->_e('Part of Speech') ?></label></th>
			<td>
				<select name="part_of_speech" id="part_of_speech">
				<?php foreach( \MeCabSweet\Constants\PartOfSpeech::get_non_conjugatives() as $pos ): ?>
					<option<?php selected($pos == $this->get_morpheme_attr('part_of_speech')) ?>><?php echo esc_html($pos) ?></option>
				<?php endforeach; ?>
				</select>
				<span class="description"><?php $this->i18n->_e('Currently, conjugatives are not supported.') ?></span>
				<br />
				<input type="text" class="regular-text" name="option_1" value="<?php $this->morpheme_attr('option_1') ?>" placeholder="ex. 固有名詞" />
				<span class="description"><?php $this->i18n->_e('If no idea, enter <strong>一般</strong>.') ?></span>
				<br />
				<input type="text" class="regular-text" name="option_2" value="<?php $this->morpheme_attr('option_2') ?>" placeholder="ex. 人名" />
				<br />
				<input type="text" class="regular-text" name="option_3" value="<?php $this->morpheme_attr('option_3') ?>" placeholder="ex. 小説家" />
			</td>
		</tr>


	</table>
	<?php submit_button($this->is_editing() ? null : $this->i18n->__('Add Word')) ?>
</form>

<hr />


<h3><span class="dashicons dashicons-upload"></span> <?php $this->i18n->_e('Import from CSV') ?></h3>