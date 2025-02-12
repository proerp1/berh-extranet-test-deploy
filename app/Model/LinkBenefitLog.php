<?php

class LinkBenefitLog extends AppModel
{
    public $name = 'LinkBenefitLog';

    public $belongsTo = [
        'UserCreated' => [
            'className' => 'User',
            'foreignKey' => 'user_creator_id',
        ],
        'LinkBenefit'
    ];
}
