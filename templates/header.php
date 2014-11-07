<?php
	/** @var \MeCabSweet\Pattern\AdminScreen $this */
?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/ja_JP/all.js#xfbml=1&appId=264573556888294";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<h2 class="nav-tab-wrapper">
	<span>
		<img src="<?php echo $this->base_url ?>assets/img/icon.png" width="20" height="20" alt="Mecab">
		<?php $this->i18n->_e('MeCab Sweet'); ?>
	</span>
	<?php
	$pages = array(
		'mecab-setting' => $this->i18n->__('Setting'),
		'mecab-fulltext-search' => $this->i18n->__('Full Text Search'),
		'mecab-user-dic' => $this->i18n->__('User Dictionary'),
	);
	if( $this->option->taxonomy ){
		$pages['mecab-dic-registered'] = $this->i18n->__('Registered Words');
		$pages['mecab-dic-add'] = $this->i18n->__('Add Word');
	}
	foreach( $pages as $key => $val ):
		?>
		<a class="nav-tab<?php if( $this->is_page($key) ) echo ' nav-tab-active'; ?>" href="<?php echo $this->url($key);?>">
			<?php echo esc_html($val) ?>
		</a>
	<?php endforeach; ?>
</h2>

<br class="clear" />

<?php if( $this->is_page('mecab-setting') ): ?>
<div class="mecab-jumbotron">
	<img class="eyecatch" src="<?php echo $this->base_url ?>assets/img/mecab-cropped.jpg" width="150" height="150" alt="Mecab" />
	<p>
		<?php $this->i18n->_p('<strong>MeCab Sweet</strong> provides an suite of utility features powered by %1$s (an open-source morphological analyzer) and %2$s (MeCab\'s PHP extension).',
			'<a href="http://mecab.googlecode.com/svn/trunk/mecab/doc/index.html" target="_blank">MeCab</a>',
			'<a href="http://pecl.opendogs.org" target="_blank">php-mecab</a>') ?>
		<?php $this->i18n->_e('Don\'t you know morphem? Click button and see example.'); ?>
	</p>
	<p>
		<a class="button example-toggle" href="#"><i class="dashicons dashicons-visibility"></i> <?php $this->i18n->_e('See Power of MeCab') ?></a>
	</p>
	<div class="example">

<pre><?php
echo <<<HTML
<span class="variable">\$mecab</span> = new <span class="method">Mecab</span>();
<span class="variable">\$nodes</span> = <span class="variable">\$mecab</span>-><span class="method">parseToNode</span>(<span class="string">'すもももももももものうち'</span>);
<span class="variable">\$parse_result</span> = <span class="method">array</span>();
foreach( <span class="variable">\$nodes</span> as <span class="variable">\$node</span> ){
	if( 0 == <span class="variable">\$node</span>-><span class="method">getStat</span>() ){
		<span class="variable">\$parse_result</span>[] = <span class="method">sprintf</span>(<span class="string">'%s（%s）'</span>, <span class="variable">\$node</span>-><span class="method">getSurface</span>(), <span class="variable">\$node</span>-><span class="method">getFeature</span>());
	}
}
<span class="method">print_r</span>(<span class="variable">\$parse_result</span>);
HTML;
?>

<span class="comment">--------------------------
-> Array (
    [0] => すもも（名詞,一般,*,*,*,*,すもも,スモモ,スモモ）
    [1] => も（助詞,係助詞,*,*,*,*,も,モ,モ）
    [2] => もも（名詞,一般,*,*,*,*,もも,モモ,モモ）
    [3] => も（助詞,係助詞,*,*,*,*,も,モ,モ）
    [4] => もも（名詞,一般,*,*,*,*,もも,モモ,モモ）
    [5] => の（助詞,連体化,*,*,*,*,の,ノ,ノ）
    [6] => うち（名詞,非自立,副詞可能,*,*,*,うち,ウチ,ウチ）
)</span>
</pre>
	</div>
</div>

<hr />
<?php endif; ?>