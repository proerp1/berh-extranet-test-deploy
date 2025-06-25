<?php
class CategoriaFaq extends AppModel
{
    public $name = 'CategoriaFaq';

    public $hasMany = [
        'Faq' => [
            'className' => 'Faq',
            'foreignKey' => 'categoria_faq_id',
            'dependent' => false
        ]
    ];
}


