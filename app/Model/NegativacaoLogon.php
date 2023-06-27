<?php
class NegativacaoLogon extends AppModel
{
    public $name = 'NegativacaoLogon';
    public $belongsTo = [
        'Customer',
        'Billing',
        'Product',
    ];
}
