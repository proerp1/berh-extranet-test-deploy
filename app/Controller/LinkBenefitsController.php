<?php
use League\Csv\Reader;
class LinkBenefitsController extends AppController
{
    public $components = ['Paginator', 'Permission'];
    public $uses = ['CustomerUser', 'CustomerUserItinerary', 'Benefit', 'LinkBenefit', 'LinkBenefitLog'];

    public $paginate = [
        'LinkBenefit' => ['limit' => 10, 'order' => ['LinkBenefit.id' => 'desc']],
    ];

    public function beforeFilter()
    {
        parent::beforeFilter(); 
    }

    public function index()
    {
        $this->Paginator->settings = $this->paginate;
        $condition = ["and" => [], "or" => []];
    
        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['UserCreated.name LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('LinkBenefit', $condition);

        $action = 'Associar Cartão';

        $this->set(compact('data', 'action'));
    }

    public function logs($id)
    {
        $this->Paginator->settings = $this->paginate;
        $condition = ["and" => ['LinkBenefitLog.link_benefit_id' => $id], "or" => []];

        $data = $this->Paginator->paginate('LinkBenefitLog', $condition);

        $action = 'Associar Cartão';

        $breadcrumb = ['Associar Cartão' => ['action' => 'index'], 'Logs' => ''];
        $this->set(compact('data', 'action', 'breadcrumb'));
    }

    public function upload_csv()
    {
        if ($this->request->is('post') && !empty($this->request->data['file']['name']) && $this->request->data['file']['type'] == 'text/csv') {
        
            $uploadedFile = $this->request->data['file'];

            $ret = $this->parse($uploadedFile['tmp_name'], $uploadedFile['name']);

            if (!$ret['success']) {
                $this->Flash->set(__($ret['error']), ['params' => ['class' => "alert alert-danger"]]);    
            }

            $this->redirect(['controller' => 'link_benefits', 'action' => 'index']);
        } else {
            $this->Flash->set(__('Arquivo Inválido, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['controller' => 'link_benefits', 'action' => 'index']);
        }
    }

    private function parse($tmpFile, $fileName)
    {
        $file = file_get_contents($tmpFile, FILE_IGNORE_NEW_LINES);
        $encoding = mb_detect_encoding($file, mb_list_encodings(), true);

        if ($encoding != 'UTF-8') {
            $file = mb_convert_encoding($file, 'UTF-8', 'Windows-1252');
        }

        // clear tabs from the file
        $file = str_replace("\t", '', $file);

        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 1) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $this->LinkBenefit->save([
            'LinkBenefit' => [
                'file_name' => $this->request->data['file'],
                'user_creator_id' => CakeSession::read("Auth.User.id")
            ]
        ]);

        $line = 0;
        $update = [];
        $log = [];
        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                if($line == 0){
                    $line++;
                }
                continue;
            }

            $cpf = trim($row[0]);
            $code = trim($row[1]);
            $number = trim($row[2]);
            $id_operadora = trim($row[3]);
            $matricula = trim($row[4]);

            $user = $this->CustomerUser->find('first', [
                'conditions' => [
                    'CustomerUser.cpf' => $cpf
                ],
                'recursive' => -1
            ]);

            if (empty($user)) {
                $log[] = [
                    'link_benefit_id' => $this->LinkBenefit->id,
                    'description' => 'Beneficiário '.$cpf.' não encontrado.'
                ];
                continue;
            }

            if ($code) {
                $benefit = $this->Benefit->find('first', [
                    'fields' => [
                        'Benefit.id',
                    ],
                    'conditions' => [
                        'Benefit.code' => $code,
                        'Supplier.id' => $id_operadora
                    ],
                ]);

                if (empty($benefit)) {
                    $log[] = [
                        'link_benefit_id' => $this->LinkBenefit->id,
                        'description' => 'Benefício '.$code.' não existente.'
                    ];
                    continue;
                }

                $itineraries = $this->CustomerUserItinerary->find('first', [
                    'conditions' => [
                        'CustomerUserItinerary.customer_user_id' => $user['CustomerUser']['id'],
                        'CustomerUserItinerary.benefit_id' => $benefit['Benefit']['id'],
                    ],
                    'recursive' => -1
                ]);

                if (empty($itineraries)) {
                    $log[] = [
                        'link_benefit_id' => $this->LinkBenefit->id,
                        'description' => 'Benefício '.$code.' ainda não foi vinculado ao beneficiáro '.$user['CustomerUser']['name']
                    ];
                    continue;
                }

                $update[] = [
                    'CustomerUserItinerary' => [
                        'id' => $itineraries['CustomerUserItinerary']['id'],
                        'card_number' => $number,
                        'matricula' => $matricula
                    ]
                ];
            } elseif ($id_operadora) {
                $benefits = $this->Benefit->find('all', [
                    'fields' => [
                        'Benefit.id',
                    ],
                    'conditions' => [
                        'Supplier.id' => $id_operadora
                    ],
                ]);

                if (empty($benefits)) {
                    $log[] = [
                        'link_benefit_id' => $this->LinkBenefit->id,
                        'description' => 'ID Operadora '.$id_operadora.' não existente.'
                    ];
                    continue;
                }

                foreach ($benefits as $benefit) {
                    $itineraries = $this->CustomerUserItinerary->find('first', [
                        'conditions' => [
                            'CustomerUserItinerary.customer_user_id' => $user['CustomerUser']['id'],
                            'CustomerUserItinerary.benefit_id' => $benefit['Benefit']['id'],
                        ],
                        'recursive' => -1
                    ]);

                    if (empty($itineraries)) {
                        continue;
                    }

                    $update[] = [
                        'CustomerUserItinerary' => [
                            'id' => $itineraries['CustomerUserItinerary']['id'],
                            'card_number' => $number,
                        ]
                    ];
                }
            }
        }

        $this->LinkBenefitLog->saveMany($log);

        if (!empty($update)) {
            $this->CustomerUserItinerary->saveMany($update);
        }

        return ['success' => true];
    }
}
