<?php
class CustomerPosition extends AppModel {
    public $name = 'CustomerPosition';
    public $useTable = 'customer_positions';
    public $primaryKey = 'id';

    public $hasMany = array(
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_position_id'
        )
    );
}
