<?php
class Faq extends AppModel
{
    public $name = 'Faq';

    public $actsAs = [
        'Containable', // ✅ necessário para funcionar o 'contain' no controller
        'Upload.Upload' => [
            'file' => [
                'fields' => [
                    'dir' => 'file_dir'
                ]
            ]
        ]
    ];

    // Relacionamento com a categoria
    public $belongsTo = [
        'CategoriaFaq' => [
            'className'  => 'CategoriaFaq',
            'foreignKey' => 'categoria_faq_id'
        ]
    ];

    // Relacionamento com os fornecedores
    public $hasMany = [
        'FaqRelacionamento' => [
            'className' => 'FaqRelacionamento',
            'foreignKey' => 'faq_id',
            'dependent' => true
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
