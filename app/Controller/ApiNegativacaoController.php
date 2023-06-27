<?php
class ApiNegativacaoController extends AppController
{
    public $components = ['ApiCheckToken'];
    public $uses = ['NaturezaOperacao', 'CadastroPefin', 'MotivoBaixa'];

    public function beforeFilter()
    {
        $this->Auth->allow();
    }

    public function getPefinNatureza()
    {
        $this->autoRender = false;
        $this->layout = 'api';
        $this->response->type('json');

        $check = $this->ApiCheckToken->check($this->request->data);
        if (!empty($check['error'])) {
            return json_encode(['success' => false, 'message' => $check['error']]);
        }

        $pefinNaturezas = $this->NaturezaOperacao->find('all', [
            'order' => ["NaturezaOperacao.nome" => "asc"],
            'fields' => ['NaturezaOperacao.id', 'NaturezaOperacao.nome']
        ]);

        return json_encode($pefinNaturezas);
    }
    
    /*
    *    campos post:
    *        'api_token' string
    */
    public function getList()
    {
        $this->autoRender = false;
        $this->layout = 'api';
        $this->response->type('json');

        $check = $this->ApiCheckToken->check($this->request->data);
        if (!empty($check['error'])) {
            return json_encode(['success' => false, 'message' => $check['error']]);
        }

        $this->CadastroPefin->unbindModel(
            [
                'hasMany' => [
                    'CadastroPefinErros'
                ]
            ]
        );

        $pefins = $this->CadastroPefin->find('all', [
            'fields' => [
                'CadastroPefin.id',
                'CadastroPefin.nome',
                'Status.name',
            ],
            'conditions' => ['CadastroPefin.customer_id' => $check['CustomerToken']['customer_id']],
            'order' => ['CadastroPefin.created' => 'desc']
        ]);

        return json_encode($pefins);
    }

    /*
    *    campos post:
    *        'api_token' string
    *        'natureza_operacao_id' integer
    *        'tipo_pessoa' string | 2 = 'Fisica' ou 1 = 'Juridica'
    *        'documento' string
    *        'nome' string
    *        'cep' string
    *        'endereco' string
    *        'numero' string
    *        'complemento' string | null
    *        'bairro' string
    *        'cidade' string
    *        'estado' string
    *        'data_compra' date
    *        'nosso_numero' integer | null se natureza for cheque
    *        'numero_titulo' integer | null se natureza for cheque
    *        'venc_divida' string
    *        'valor' float | 1.234,12

    *        'num_banco' string | se natureza for cheque
    *        'num_agencia' string | se natureza for cheque
    *        'num_conta_corrente' string | se natureza for cheque
    *        'num_cheque' string | se natureza for cheque
    *        'alinea' integer | 12, 13 ou 14 | se natureza for cheque

    */
    public function save()
    {
        $this->autoRender = false;
        $this->layout = 'api';
        $this->response->type('json');

        $request = $this->request->data;

        $requiredFields = ['api_token', 'natureza_operacao_id', 'tipo_pessoa', 'documento', 'nome', 'cep', 'endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'data_compra', 'nosso_numero', 'numero_titulo', 'venc_divida', 'valor', 'num_banco', 'num_agencia', 'num_conta_corrente', 'num_cheque', 'alinea'];

        if ($request['natureza_operacao_id'] == 23) {
            unset($requiredFields[14]);
            unset($requiredFields[15]);

            unset($this->CadastroPefin->validate['nosso_numero']);
            unset($this->CadastroPefin->validate['numero_titulo']);
        }

        if ($request['natureza_operacao_id'] != 23) {
            unset($requiredFields[18]);
            unset($requiredFields[19]);
            unset($requiredFields[20]);
            unset($requiredFields[21]);
            unset($requiredFields[22]);

            unset($this->CadastroPefin->validate['num_banco']);
            unset($this->CadastroPefin->validate['num_agencia']);
            unset($this->CadastroPefin->validate['num_conta_corrente']);
            unset($this->CadastroPefin->validate['num_cheque']);
            unset($this->CadastroPefin->validate['alinea']);
        }

        //verifica se os parametros foram enviados
        if (!$this->ApiCheckToken->checkFilledParams($request, $requiredFields)) {
            return json_encode(['success' => false, 'message' => "Missing parameters"]);
        }
        //fim

        $check = $this->ApiCheckToken->check($request);
        if (!empty($check['error'])) {
            return json_encode(['success' => false, 'message' => $check['error']]);
        }

        unset($request['api_token']);
        $save['CadastroPefin'] = $request;
        $save['CadastroPefin']['status_id'] = 22;
        $save['CadastroPefin']['product_id'] = 2; //provisorio
        $save['CadastroPefin']['customer_flag'] = 1; //provisorio
        $save['CadastroPefin']['customer_id'] = $check['CustomerToken']['customer_id'];
        $save['CadastroPefin']['customer_user_id'] = $check['CustomerToken']['id'];
        $save['CadastroPefin']['ip'] = $_SERVER['REMOTE_ADDR'];

        $valor = str_replace('.', '', $save['CadastroPefin']['valor']);
        $valor = str_replace(',', '.', $valor);

        // verifica se já existe uma negativação cadastrada com esse valor e número do título
        $data = $this->CadastroPefin->find('count', [
            'conditions' => [
                'CadastroPefin.numero_titulo' => $save['CadastroPefin']['numero_titulo'],
                'CadastroPefin.customer_id' => $check['CustomerToken']['customer_id'],
                'CadastroPefin.valor' => $valor
            ]
        ]);
        
        if ($data > 0 and $save['CadastroPefin']['natureza_operacao_id'] != 23) {
            return json_encode(['success' => false, 'message' => 'Já existe uma negativação cadastrada com esse valor e número do título!']);
        }

        $this->CadastroPefin->create();
        if ($this->CadastroPefin->save($save)) {
            $id = $this->CadastroPefin->id;
            return json_encode(['success' => true, 'message' => "A negativação foi salva com sucesso", 'negativacao_id' => $id]);
        } else {
            $mensagem = '';
            foreach ($this->CadastroPefin->validationErrors as $key => $value) {
                $mensagem .= ucfirst($key).': '.implode(', ', $value).'.<br>';
            }

            return json_encode(['success' => false, 'message' => $mensagem]);
        }
    }
    
    /*
    *    campos post:
    *        'api_token' string
    *        'negativacao_id' integer
    */
    public function view()
    {
        $this->autoRender = false;
        $this->layout = 'api';
        $this->response->type('json');

        $request = $this->request->data;

        if (count($request) < 2) {
            return json_encode(['success' => false, 'message' => "Missing parameters"]);
        }

        $check = $this->ApiCheckToken->check($request);
        if (!empty($check['error'])) {
            return json_encode(['success' => false, 'message' => $check['error']]);
        }

        $this->CadastroPefin->unbindModel(
            [
                'hasMany' => [
                    'CadastroPefinErros'
                ]
            ]
        );

        $pefin = $this->CadastroPefin->find('first', [
            'fields' => [
                'NaturezaOperacao.id',
                'NaturezaOperacao.nome',
                'CadastroPefin.id',
                'CadastroPefin.tipo_pessoa',
                'CadastroPefin.documento',
                'CadastroPefin.nome',
                'CadastroPefin.cep',
                'CadastroPefin.endereco',
                'CadastroPefin.numero',
                'CadastroPefin.bairro',
                'CadastroPefin.cidade',
                'CadastroPefin.estado',
                'CadastroPefin.data_compra',
                'CadastroPefin.nosso_numero',
                'CadastroPefin.numero_titulo',
                'CadastroPefin.venc_divida',
                'CadastroPefin.valor',
                'CadastroPefin.num_banco',
                'CadastroPefin.num_agencia',
                'CadastroPefin.num_conta_corrente',
                'CadastroPefin.num_cheque',
                'CadastroPefin.alinea',
                'Status.name'
            ],
            'conditions' => [
                'CadastroPefin.id' => $request['negativacao_id'],
                'CadastroPefin.customer_id' => $check['CustomerToken']['customer_id'],
            ]
        ]);

        if (empty($pefin)) {
            return json_encode(['success' => false, 'message' => 'Nenhum registro encontrado!']);
        }

        return json_encode($pefin);
    }

    public function getPefinMotivoBaixa()
    {
        $this->autoRender = false;
        $this->layout = 'api';
        $this->response->type('json');

        $check = $this->ApiCheckToken->check($this->request->data);
        if (!empty($check['error'])) {
            return json_encode(['success' => false, 'message' => $check['error']]);
        }

        $pefinMotivoBaixa = $this->MotivoBaixa->find('all', [
            'order' => ["MotivoBaixa.nome" => "asc"],
            'fields' => ['MotivoBaixa.id', 'MotivoBaixa.nome']
        ]);

        return json_encode($pefinMotivoBaixa);
    }

    public function baixar()
    {
        $this->autoRender = false;
        $this->layout = 'api';
        $this->response->type('json');

        $request = $this->request->data;

        if (count($request) < 3) {
            return json_encode(['success' => false, 'message' => "Missing parameters"]);
        }

        $check = $this->ApiCheckToken->check($request);
        if (!empty($check['error'])) {
            return json_encode(['success' => false, 'message' => $check['error']]);
        }

        if (!isset($request['negativacao_id'])) {
            return json_encode(['success' => false, 'message' => "O campo negativacao_id é obrigatório!"]);
            
        }

        if (!isset($request['motivo_baixa_id'])) {
            return json_encode(['success' => false, 'message' => "O campo Motivo da Baixa é obrigatório!"]);
            
        }

        $this->CadastroPefin->unbindModel(
            [
                'hasMany' => [
                    'CadastroPefinErros'
                ]
            ]
        );

        $pefin = $this->CadastroPefin->find('first', [
            'fields' => [
                'NaturezaOperacao.id',
                'NaturezaOperacao.nome',
                'CadastroPefin.id',
                'CadastroPefin.tipo_pessoa',
                'CadastroPefin.documento',
                'CadastroPefin.nome',
                'CadastroPefin.cep',
                'CadastroPefin.endereco',
                'CadastroPefin.numero',
                'CadastroPefin.bairro',
                'CadastroPefin.cidade',
                'CadastroPefin.estado',
                'CadastroPefin.data_compra',
                'CadastroPefin.nosso_numero',
                'CadastroPefin.numero_titulo',
                'CadastroPefin.venc_divida',
                'CadastroPefin.valor',
                'CadastroPefin.num_banco',
                'CadastroPefin.num_agencia',
                'CadastroPefin.num_conta_corrente',
                'CadastroPefin.num_cheque',
                'CadastroPefin.alinea',
                'Status.name'
            ],
            'conditions' => [
                'CadastroPefin.id' => $request['negativacao_id'],
                'CadastroPefin.status_id' => '25',
                'CadastroPefin.customer_id' => $check['CustomerToken']['customer_id'],
            ]
        ]);

        if (empty($pefin)) {
            return json_encode(['success' => false, 'message' => 'Nenhum registro encontrado ou incluso na Serasa!']);
        } else {

            $this->CadastroPefin->id = $request['negativacao_id'];

            $this->request->data['CadastroPefin']['motivo_baixa_id'] =  $request['motivo_baixa_id'];
            $this->request->data['CadastroPefin']['data_solic_baixa'] = date('Y-m-d H:i:s');
            $this->request->data['CadastroPefin']['status_id'] = 33;

            if ($this->CadastroPefin->save($this->request->data)) {
                return json_encode(['success' => true, 'message' => "A negativação foi baixa com sucesso"]);                
            } else {
                return json_encode(['success' => false, 'message' => "A negativação não pode ser alterada, Por favor tente de novo."]);
            }

        }
        
    }
}
