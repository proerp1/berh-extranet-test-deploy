<?php
class Benefit extends AppModel {
    public $name = 'Benefit';
    public $useTable = 'benefits';
    public $primaryKey = 'id';

    public $belongsTo = array(
        'Supplier' => array(
            'className' => 'Supplier',
            'foreignKey' => 'supplier_id'
        ),
        'BenefitType' => array(
            'className' => 'BenefitType',
            'foreignKey' => 'benefit_type_id'
        )
    );

    var $virtualFields = array(
        'complete_name' => "CONCAT(CONCAT(Benefit.code, ' - '), Benefit.name)"
    );
}
