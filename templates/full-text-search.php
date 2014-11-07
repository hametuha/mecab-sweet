<?php
	/** @var \MecabSweet\Screen\FullTextSearch $this */
?>
<h3><i class="dashicons dashicons-welcome-learn-more"></i> <?php $this->i18n->_e('Preliminary Knowledge') ?></h3>

<p>
	<?php $this->i18n->_e('First of all, WordPress can search posts. Yes, that\'s right. On it\'s searching process, WordPress use <code>LIKE</code> clause. But <code>LIKE</code> doesn\'t use index which is the key feature of MySQL to shorten query time.') ?><br />
	<?php $this->i18n->_e('MySQL has a special index for long texts like post content, named Full Text Index. However content should be separated with divider(white space, comma, period), despite that normal Japanese texts aren\'t written in such way.') ?><br />
	<?php $this->i18n->_e('MeCab Sweet make a extra table for Full Text Index, separate your text and save it their. Thus search process will be much shorten.') ?><br />
	<?php $this->i18n->_e('This take effects if the amount of your posts is larger than 2,000. If you feel it doubtful, google &quot;MySQL FullText Performance&quot;.') ?>
</p>

<hr />

<table class="mecab-status">
	<caption><?php $this->i18n->_e('Current Full Text Search Status') ?></caption>
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
			<?php if( $this->option->fulltext_search ): ?>
				<span class="on">Enabled</span>
			<?php else: ?>
				<span class="off">Disabled</span>
			<?php endif; ?>
		</td>
		<td class="description">
			<p class="description">
				<?php $this->i18n->_e('If enabled, a table for indexing will be created.') ?>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->i18n->_e('Database') ?></th>
		<td class="status">
			<?php if( $this->models->search_index->table_exists() ): ?>
				<span class="on">OK</span>
			<?php else: ?>
				<span class="off">NO</span>
			<?php endif; ?>
		</td>
		<td class="description">
			<p class="description">
				<?php $this->i18n->_e('This indicates if indexing table exists.') ?>
			</p>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php $this->i18n->_e('Index status') ?></th>
		<td class="status">
			<?php if( !$this->models->search_index->table_exists() ): ?>
				NAN
			<?php else: ?>
				<?php $status = $this->models->search_index->index_status(); ?>
				<span class="<?php echo $status->indexed < $status->total ? 'off' : 'on' ?>">
					<?php echo number_format_i18n($status->indexed) ?>
					/
					<?php echo number_format_i18n($status->total) ?>
				</span>
			<?php endif; ?>
		</td>
		<td class="description">
			<p class="description">
				<?php $this->i18n->_e('Index record count. If indexed record is less, you should build index.') ?>
			</p>
		</td>
	</tr>
	</tbody>
</table>

<hr />

<?php if( $this->option->fulltext_search ): ?>


	<?php if( !$this->models->search_index->table_exists() ): ?>
		<p class="alert">
			<?php $this->i18n->_e('Table doesn\'t exist. You can\'t build index. Something might be wrong...') ?>
		</p>
	<?php else: ?>

		<h3><span class="dashicons dashicons-update"></span> <?php $this->i18n->_e('Build Index') ?></h3>

		<p><?php $this->i18n->_e('Rebuild index of every test. This will take some minutes. If you are new to this plugin or recompile user dictionary, it is required.') ?></p>

		<form id="mecab-index-building-form" method="post" action="<?php echo admin_url('admin-ajax.php') ?>">
			<input type="hidden" name="action" value="mecab_index_build" />
			<input type="hidden" name="offset" value="0" />
			<?php wp_nonce_field('mecab_index_build') ?>
			<?php submit_button($this->i18n->__('Build Index')) ?>
		</form>

		<hr />

		<h3><span class="dashicons dashicons-performance"></span> <?php $this->i18n->_e('Performance Check') ?></h3>

		<p><?php $this->i18n->_e('Enter search term and compare the performance of <code>LIKE</code> VS <code>FullText</code>. If <code>LIKE</code> is faster, abondon this plugin!') ?></p>

		<form id="mecab-performance" action="<?php echo admin_url('admin-ajax.php') ?>" method="get">
			<input type="hidden" name="action" value="mecab_performance_check">
			<table class="form-table">
				<tr>
					<th><label for="mecab-s"><?php $this->i18n->_e('Search Query') ?></label></th>
					<td>
						<input type="text" id="mecab-s" class="regular-text" name="s" value="WordPress" />
					</td>
				</tr>
			</table>
			<?php submit_button($this->i18n->__('Test')) ?>
		</form>

		<hr />


		<div class="result-window">
			<h4><?php $this->i18n->_e('Console Result') ?></h4>
			<div class="indicator">
				<div id="indicator-bar"></div>
			</div>
			<p id="form-message">

			</p>
		</div>

	<?php endif ?>
<?php endif; ?>