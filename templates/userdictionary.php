<?php
	/** @var \MecabSweet\Screen\FullTextSearch $this */
?>
<h3><?php $this->i18n->_e('Preliminary Knowledge') ?></h3>

<p>
	<?php $this->i18n->_e('MeCab is dictionary based ') ?>
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
				<?php $this->i18n->_e('If enabled, you can access admin screen for user dictionary. Add, Delete or Edit your terms and they will be compiled to MeCab ready format.') ?>
			</p>
		</td>
	</tbody>
</table>

