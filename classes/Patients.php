<?php
class Patients extends ResourceList {
    protected $item_type = 'Patient';
    protected $storage = array('type' => 'json', 'location' => 'data/patients.json');
}