<?php

namespace MeCabSweet\Pattern;


use MeCabSweet\Data\SearchIndex;
use MeCabSweet\Data\Terms;

/**
 * ModelAccessor
 *
 * @package MeCabSweet\Pattern
 * @property-read SearchIndex $search_index
 * @property-read Terms $terms
 */
final class ModelAccessor extends Singleton
{

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return mixed|null
	 */
	public function __get($name){
		switch( $name ){
			case 'search_index':
				return SearchIndex::get_instance();
				break;
			case 'terms':
				return Terms::get_instance();
				break;
			default:
				return null;
				break;
		}
	}

} 