<?php
App::uses('AppModel', 'Model');

class PaymentImportLog extends AppModel {
    public $useTable = 'payment_import_log';

    public $belongsTo = array(
        'Order' => array(
            'className' => 'Order',
            'foreignKey' => 'order_id'
        ),
        'CustomerUser' => array(
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        ),
        'Supplier' => array(
            'className' => 'Supplier',
            'foreignKey' => 'supplier_id'
        )
    );
}