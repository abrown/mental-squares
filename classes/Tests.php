<?php
class Tests extends ResourceList {
    protected $item_type = 'Test';
    protected $storage = array('type' => 'json', 'location' => 'data/tests.json');
    
    /**
     * Override GET to change date format
     * @return ResourceList
     */
    public function GET(){
        $list = parent::GET();
        foreach($list->items as $id => $test){
            $test->modified = date('g:ia F j, Y', $test->modified);
        }
        return $list;
    }
}