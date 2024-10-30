<?php
class TreeSet {



    public function reindexTree($tree) {

        foreach($tree[0] as $id){
            $ordered_tree[]= $this -> reindexBranch($id,$tree,true);
        }
		
		$objTmp = (object) array('aFlat' => array());
		array_walk_recursive($ordered_tree, create_function('&$v, $k, &$t', '$t->aFlat[] = $v;'), $objTmp);

		$new_array = $objTmp->aFlat;

      return $new_array;
    }

    public function reindexBranch($id,$tree,$isRoot = false) {

		$branch = $tree[$id];
		
		$items[] = $branch['item'];
		
        unset($branch['item']);

        if(count($branch) > 0){
            foreach($branch as $id){
                $children= $this -> reindexBranch($id,$tree);
				$items[]=$children;

				
            }
        }   

        return $items;
    }


    /**
     * Draw folder tree structure
     *
     * First step in a recursive drawing operation
     *
     * @param array $tree
     * @return string
     */
    public function drawTree($tree,$array_checked=false,$format_item_fn=false) {
		
		if ($array_checked) {
			global $checked_branches;
			$checked_branches = $array_checked;
		}

        foreach($tree[0] as $id){
            $html .= $this -> drawBranch($id,$tree,$format_item_fn,true);
        }

        return '<ul class="categories tree">'.$html.'</ul>';
		
		unset($checked_branches);
    }

       
    /**
     * Recursively draw tree branches
     *
     * @param int $id
     * @param array $tree
     * @param boolean $isRoot
     * @return string
     */
	 
    public function drawBranch($id,$tree,$format_item_fn=false,$isRoot = false) {

		$branch = $tree[$id];
		
		$item = $branch['item'];
		
		
		
		if (!$format_item_fn) {
			$html = '<li id=cat-'.$item->ID.' class="category"><span class="text">' . $item->name.'</span>';
		}else {
			if (is_array($format_item_fn)) {
				$html = $format_item_fn[0]->$format_item_fn[1]($item);
			}else{
				$html = $format_item_fn($item);
			}
		}
		
		
        unset($branch['item']);

        if(count($branch) > 0){
            $html .= '<ul>';
            foreach($branch as $id){
                $html .= $this -> drawBranch($id,$tree,$format_item_fn);
            }
            $html .= '</ul>';
        }   
        $html .= '</li>';
        return $html;
    }
   
    /**
     * Build tree structure from array
     *
     * Reorganizes flat array into a tree like structure
     *
     * @param array $categories
     * @return array
     */
	 
    public function buildTree($items) {
	
        $tree = array(0 => array());
        foreach($items as $item){
		
            $tree[$item->ID]['item'] = $item;
            if(!is_null($item->parent)){
                if(!isset($tree[$item->parent])){
                    $tree[$item->parent] = array();
                }
                $tree[$item->parent][$this -> findFreeIndex($tree[$item->parent],1)] = $item->ID;
            } else {
                $tree[0][$this -> findFreeIndex($tree[0],1)] = $item->ID;
            }
        }
		
        ksort($tree,SORT_ASC);


        return $tree;
    }
	/**
	 * Determine next un-used array index
	 *
	 * @param array $array
	 * @param int $startInd
	 * @return int
	 */
    protected function findFreeIndex($array,$startInd = 0) {
        return (isset($array[$startInd]) ? $this -> findFreeIndex($array,$startInd + 1) : $startInd);
    }
}
?>