<?php
use League\Csv\Reader;
class LinkBenefitsController extends AppController
{
    public $components = ['Paginator', 'Permission'];
    public $uses = ['CustomerUser', 'CustomerUserItinerary', 'Benefit', 'LinkBenefit'];

    public function beforeFilter()
    {
        parent::beforeFilter(); 
    }

    public function index()
    {
        $condition = ["and" => [], "or" => []];
    
        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['UserCreated.name LIKE' => "%".$_GET['q']."%"]);
        }

        $data = $this->Paginator->paginate('LinkBenefit', $condition);

        $action = 'Associar Cartão';

        $this->set(compact('data', 'action'));
    }

    public function upload_csv()
    {
        if ($this->request->is('post') && !empty($this->request->data['file']['name']) && $this->request->data['file']['type'] == 'text/csv') {
        
            $uploadedFile = $this->request->data['file'];

            $ret = $this->parse($uploadedFile['tmp_name'], $uploadedFile['name']);

            if (!$ret['success']) {
                $this->Flash->set(__($ret['error']), ['params' => ['class' => "alert alert-danger"]]);    
            }

            $this->redirect($this->referer());
        } else {
            $this->Flash->set(__('Arquivo Inválido, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect($this->referer());
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

        if ($numLines < 2) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $line = 0;
        $update = [];
        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                if($line == 0){
                    $line++;
                }
                continue;
            }

            $cpf = $row[0];
            $code = $row[1];
            $number = $row[2];

            $user = $this->CustomerUser->find('first', [
                'conditions' => [
                    'CustomerUser.cpf' => $cpf
                ],
                'recursive' => -1
            ]);

            if (empty($user)) {
                return ['success' => false, 'error' => 'Beneficiário não encontrado.'];
            }

            $benefit = $this->Benefit->find('first', [
                'conditions' => [
                    'Benefit.code' => $code
                ],
                'recursive' => -1
            ]);

            if (empty($benefit)) {
                return ['success' => false, 'error' => 'Benefício '.$code.' não existente.'];
            }

            $itineraries = $this->CustomerUserItinerary->find('first', [
                'conditions' => [
                    'CustomerUserItinerary.customer_user_id' => $user['CustomerUser']['id'],
                    'CustomerUserItinerary.benefit_id' => $benefit['Benefit']['id'],
                ],
                'recursive' => -1
            ]);

            if (empty($itineraries)) {
                return ['success' => false, 'error' => 'Benefício '.$code.' ainda não foi vinculado ao beneficiáro '.$user['CustomerUser']['name']];
            }

            $update[] = [
                'CustomerUserItinerary' => [
                    'id' => $itineraries['CustomerUserItinerary']['id'],
                    'card_number' => $number,
                ]
            ];
        }

        $this->CustomerUserItinerary->saveMany($update);

        $this->LinkBenefit->save([
            'LinkBenefit' => [
                'file_name' => $this->request->data['file'],
                'user_creator_id' => CakeSession::read("Auth.User.id")
            ]
        ]);

        return ['success' => true];
    }
}
