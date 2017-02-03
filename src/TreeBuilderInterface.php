<?php

namespace Brisum\Lib\TreeBuilder;

interface TreeBuilderInterface
{
	/**
	 * @param array $list
	 * @return array
	 */
	function build(array $list);
}