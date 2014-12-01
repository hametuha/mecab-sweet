<?php

namespace MeCabSweet\UI\Table;



use MeCabSweet\Expression\Morpheme;
use MeCabSweet\Pattern\ListTable;

class WordList extends ListTable
{


	protected $singular = 'word';

	protected $plural = 'words';

	protected $show_search_form = true;

	/**
	 * Retrieve table data to display
	 *
	 * @return array
	 */
	protected function retrieve() {
		return $this->models->terms->get_terms(array(
			'page' => $this->get_pagenum(),
			'per_page' => $this->per_page,
			'orderby' => $this->input->get('orderby') ?: 'ruby',
			'order' => $this->input->get('order') ?: 'ASC',
			's' => $this->input->get('s'),
		));
	}



	/**
	 * Columns
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb' => '<input type="checkbox" />',
			'morpheme' => $this->i18n->__('Word'),
			'pronunciation' => $this->i18n->__('Pronunciation'),
			'pos' => $this->i18n->__('Part of speech'),
			'cost' => $this->i18n->__('Cost'),
			'updated' => $this->i18n->_sp('Updated')
		);
	}

	/**
	 * Sortable columns
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'morpheme' => array('morpheme', false),
			'cost' => array('cost', true),
			'updated' => array('updated', true),
		);
	}

	public function get_bulk_actions() {
		return array(
			'delete'    => $this->i18n->__('Delete'),
		);
	}


	public function column_cb($item){
		return sprintf('<input type="checkbox" id="word-%1$d" class="word-id-container" name="word[]" value="%1$d" />',$item->term_id);
	}

	/**
	 * Show morpheme
	 *
	 * @param Morpheme $item
	 * @return string
	 */
	public function column_morpheme($item){
		$tag = sprintf('<ruby>%s<rt>%s</rt></ruby>',
			esc_html($item->morpheme), esc_html($item->ruby));
		$tag .= $this->row_actions(array(
			'edit' => sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=mecab-dic-add&term_id='.$item->term_id), $this->i18n->__('Edit')),
			'delete' => sprintf('<a class="submitdelete delete-term" data-term-id="%d" href="#">%s</a>', $item->term_id, $this->i18n->__('Delete')),
		));
		return $tag;
	}

	/**
	 * Show morpheme
	 *
	 * @param Morpheme $item
	 * @return string
	 */
	public function column_pronunciation($item){
		return sprintf('<code><i class="dashicons dashicons-megaphone"></i> %s</code>',  esc_html($item->pronunciation));
	}

	/**
	 * Show cost
	 *
	 * @param Morpheme $item
	 * @return string
	 */
	public function column_cost($item){
		return number_format_i18n($item->cost);
	}

	/**
	 * Show part of speech
	 *
	 * @param Morpheme $item
	 * @return string
	 */
	public function column_pos($item){
		$pos = array();
		$pos[] = sprintf('<strong>%s</strong>', esc_html($item->part_of_speech));
		for($i = 1; $i <= 3; $i++){
			$key = 'option_'.$i;
			$pos[] = $item->{$key} ? esc_html($item->{$key}) : '---';
		}
		return implode(' / ', $pos);
	}

	/**
	 * Show morpheme
	 *
	 * @param Morpheme $item
	 * @return string
	 */
	public function column_updated($item){
		return mysql2date(get_option('date_format'), $item->updated);
	}

} 