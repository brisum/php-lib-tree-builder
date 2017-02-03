<?php

namespace Brisum\Lib\TreeBuilder;

class ObjectBuilder implements TreeBuilderInterface
{
	/**
	 * @var string
	 */
	protected $fieldId;

	/**
	 * @var string
	 */
	protected $fieldParentId;

	/**
	 * @var string|null
	 */
	protected $fieldOrderBy;

	/**
	 * Object constructor.
	 * @param string $fieldId
	 * @param string $fieldParentId
	 * @param string|null $fieldOrderBy
	 */
	public function __construct($fieldId, $fieldParentId, $fieldOrderBy = null)
	{
		$this->fieldId = $fieldId;
		$this->fieldParentId = $fieldParentId;
		$this->fieldOrderBy = $fieldOrderBy;
	}

	/**
	 * Build tree
	 *
	 * @param array $list
	 * @return array
	 */
	public function build(array $list)
	{
		$fieldId = $this->fieldId;
		$fieldParentId = $this->fieldParentId;
		$fieldOrderBy = $this->fieldOrderBy;
		$map = array();
		$tree = array();

		foreach ($list as $item) {
			if (isset($map[$item->$fieldId])) {
				$map[$item->$fieldId]['item'] = $item;
				$branch = &$map[$item->$fieldId];
			} else {
				$branch = array('item' => $item, 'children' => array());
				$map[$item->$fieldId] = &$branch;
			}

			$itemKey = $fieldOrderBy ? "{$item->$fieldOrderBy}-{$item->$fieldId}" : $item->$fieldId;
			if (0 == $item->$fieldParentId) {
				$tree[$itemKey] = &$branch;
			} else {
				$map[$item->$fieldParentId]['children'][$itemKey] = &$branch;
			}
			unset($branch);
		}

		// Adding children with lost parent to tree
		foreach ($map as $branch) {
			if (!isset($branch['item']) && is_array($branch['children'])) {
				foreach ($branch['children'] as &$childBranch) {
					$childItem = $childBranch['item'];
					$itemKey = $fieldOrderBy ? "{$childItem->$fieldOrderBy}-{$childItem->$fieldId}" : $childItem->$fieldId;
					$tree[$itemKey] = &$childBranch;
				}
			}
		}

		return $tree;
	}
}
