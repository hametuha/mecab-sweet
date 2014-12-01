<?php
	/** @var \MecabSweet\UI\Screen\UserDictionary $this */
?>
<h3><i class="dashicons dashicons-welcome-learn-more"></i> <?php $this->i18n->_e('Preliminary Knowledge') ?></h3>

<p>
	<?php $this->i18n->_e('MeCab is dictionary based morphological analyzer. It means that MeCab uses the internal dictionary to separate strings to Japanese words. Several dictionaries exist and they have numerous words. But it\'s also true that unknown words aren\'t recognized.') ?><br />
	<?php $this->i18n->_e('For example, let\'s assume that your WordPress site is about your company whose main products are cosmetics. One of your product is a shampoo named <strong>美女髪</strong>.') ?><br />
	<?php $this->i18n->_e('Ofcourse, we know <strong>美女</strong> and <strong>髪</strong> as common Japanese words. But, on your site, <strong>美女髪</strong> should be recognized as one word which indicates the name of your product, despite that MeCab will split two words <strong>美女</strong> and <strong>髪</strong> because of it\'s dictionary.')  ?><br />
	<?php $this->i18n->_e('To avoid such naughty result, you can register your own user dictionary. According to the MeCab\'s manual, user dictionary can be registered as CSV. This plugin enables you to register your own csv and compile it to MeCab ready binary. Besides that, it will give you very nice sreen to make your own CSV!') ?>
</p>

<hr />

<table class="mecab-status">
	<caption><?php $this->i18n->_e('Current User Dictionary Status') ?></caption>
	<thead>
	<tr>
		<th scope="col"><?php $this->i18n->_e('Name') ?></th>
		<th scope="col"><?php $this->i18n->_e('Status') ?></th>
		<th scope="col"><?php $this->i18n->_e('Description') ?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<th scope="row"><?php $this->i18n->_e('Setting') ?></th>
		<td class="status">
			<?php if( $this->option->user_dic ): ?>
				<span class="on">Enabled</span>
			<?php else: ?>
				<span class="off">Disabled</span>
			<?php endif; ?>
		</td>
		<td class="description">
			<p class="description">
				<?php $this->i18n->_e('If enabled, MeCab will include user dictionary.') ?>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->i18n->_e('Dictionary Command Path') ?></th>
		<td class="status">
			<?php if( !$this->option->dict_index_path ): ?>
				<span class="off">Not set</span>
			<?php elseif( !file_exists($this->option->dict_index_path) ): ?>
				<span class="off">Not found</span>
			<?php elseif( !is_executable($this->option->dict_index_path) ): ?>
				<span class="off">Not Executable</span>
			<?php else: ?>
				<span class="on">OK</span>
			<?php endif; ?>
		</td>
		<td class="description">
			<p class="description">
				<?php $this->i18n->_e('This command will be executed to make user dictionary.') ?><br />
				<code><?php echo esc_html($this->option->dict_index_path) ?></code>
			</p>
			<?php if( $this->dic->sys_dic ): ?>
			<p>
				<?php foreach( array(
					$this->i18n->__('System Dictionary') => $this->dic->sys_dic,
					$this->i18n->__('User Dictionary') => $this->dic->user_dic
				) as $label => $dic ): foreach($dic as $key => $val): ?>
					<?php
						switch($key){
							case 'filename':
								printf('%s: <code>%s</code>', esc_html($label), esc_html($val));
								break;
							case 'charset':
								printf(' <small>%s</small><br />', $val);
								break;
							default:
								// Do nothing
								break;
						}
					?>
				<?php endforeach; endforeach; ?>
			</p>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->i18n->_e('User Dictionary Status') ?></th>
		<td class="status">
			<?php if( ($last_modified = $this->dic->last_compiled('dic') ) ): ?>
				<span class="on"><?php $this->i18n->_p('%s Ago', human_time_diff($last_modified)) ?></span>
			<?php else: ?>
				<span class="off"><?php $this->i18n->_e('Not Yet') ?></span>
			<?php endif; ?>
		</td>
		<td class="description">
			<p class="description">
				<?php $this->i18n->_e('You must compile your user dictionary') ?>
				<?php $this->load_template('binary') ?>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->i18n->_e('CSV Path') ?></th>
		<td class="status">
			<?php if( $this->option->user_dic_path ): ?>
				<span class="on">OK</span>
			<?php else: ?>
				<span class="off">NG</span>
			<?php endif; ?>
		</td>
		<td class="description">
			<p class="description">
				<?php $this->i18n->_e('Path of user dictionary CSV. This CSV will be compiled to binary format.') ?>
			</p>
			<?php if( $this->option->user_dic_path ): ?>
				<code><?php echo esc_html($this->option->user_dic_path) ?></code>
				<?php if( !$this->dic->csv ): ?>
				<br /><strong class="warning"><?php $this->i18n->_e('Path is specified, but CSV doesn\'t exist. If you enabled Dictionary Management Tool, it might be O.K.') ?></strong>
				<?php endif; ?>
			<?php else: ?>
				<strong class="alert"><?php $this->i18n->_e('Not specified.') ?></strong>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->i18n->_e('Permission') ?></th>
		<?php
			try{
				$this->dic->check_permission();
				$message = $this->i18n->__('Everything is O.K.');
				$status = true;
			}catch( Exception $e ){
				$message = $e->getMessage();
				$status = false;
			}
		?>
		<td class="status">
			<?php if( $status ): ?>
				<span class="on">OK</span>
			<?php else: ?>
				<span class="off">NG</span>
			<?php endif; ?>
		</td>
		<td class="description">
			<p class="description">
				<?php echo $message; ?>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->i18n->_e('Dictionary Management Tool') ?></th>
		<td class="status">
			<?php if( $this->option->taxonomy ): ?>
				<span class="on">Enabled</span>
			<?php else: ?>
				<span class="off">Disabled</span>
			<?php endif; ?>
		</td>
		<td class="description">
			<p class="description">
				<?php if( $this->option->taxonomy ): ?>
				<strong><?php $this->i18n->_p('%s words registered', number_format_i18n($this->models->terms->word_count())) ?></strong><br />
				<?php endif; ?>
				<?php $this->i18n->_e('If enabled, you can access admin screen for user dictionary. Add, Delete or Edit your terms and they will be compiled to MeCab ready format.') ?>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->i18n->_e('CSV Compiled') ?></th>
		<td class="status">
			<?php if( ($last_modified = $this->dic->last_compiled()) ): ?>
				<span class="on"><?php $this->i18n->_p('%s Ago', human_time_diff($last_modified)) ?></span>
			<?php else: ?>
				<span class="off"><?php $this->i18n->_e('Not Yet') ?></span>
			<?php endif; ?>
		</td>
		<td class="description">
			<p class="description">
				<?php $this->i18n->_e('CSV file must be compiled by yourself.') ?>
				<?php $this->load_template('compiler') ?>
			</p>
		</td>
	</tr>
	</tbody>
</table>

<hr />

<h3><span class="dashicons dashicons-tag"></span> <?php $this->i18n->_e('Try Tagging') ?></h3>
<p><?php $this->i18n->_e('You can check if user dictionary works fine. Enter some sentences and check their parsed reult.') ?></p>
<form id="check-user-dic" action="<?php echo home_url('/mecab-terms/info', force_ssl_admin() ? 'https' : 'http') ?>" method="get">
	<p>
		<input type="text" class="long-text" name="s" value="" placeholder="<?php $this->i18n->_e('Enter word to check') ?>" />
		<?php submit_button($this->i18n->__('Check'), 'primary', 'submit', false) ?>
	</p>
	<pre class="result-window">
<?php $this->i18n->_e('Here comes tokens.') ?>
	</pre>
</form>
