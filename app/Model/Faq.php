<?php
class Faq extends AppModel
{
    public $name = 'Faq';

    // Relacionamento com a categoria
    public $belongsTo = [
        'CategoriaFaq' => [
            'className'  => 'CategoriaFaq',
            'foreignKey' => 'categoria_faq_id'
        ]
    ];

    // Validações
    public $validate = [
        'categoria_faq_id' => [
            'numeric' => [
                'rule' => 'numeric',
                'message' => 'Selecione uma categoria válida.',
                'required' => true
            ]
        ],
        'sistema_destino' => [
            'valid' => [
                'rule' => ['inList', ['sig', 'cliente', 'todos']],
                'message' => 'Selecione onde esta FAQ será exibida.',
                'required' => true
            ]
        ],
        'pergunta' => [
            'notEmpty' => [
                'rule' => 'notBlank',
                'message' => 'A pergunta não pode estar vazia.'
            ]
        ],
        'resposta' => [
            'notEmpty' => [
                'rule' => 'notBlank',
                'message' => 'A resposta não pode estar vazia.'
            ]
        ]
    ];
}
