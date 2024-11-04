<?php
class AtendimentosController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email', 'ExcelGenerator'];
    public $uses = ['Atendimento', 'Department', 'Status', 'Customer'];

    public $paginate = [
        'limit' => 100, 'order' => ['Atendimento.created' => 'desc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(21, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;
    
        $condition = [
            "and" => ['Customer.cod_franquia' => CakeSession::read("Auth.User.resales")],
            "or" => []
        ];
    
        // Filter by search query
        if (isset($this->request->query['q']) && $this->request->query['q'] != "") {
            $condition['or'] = array_merge($condition['or'], [
                'Atendimento.subject LIKE' => "%{$this->request->query['q']}%",
                'Atendimento.message LIKE' => "%{$this->request->query['q']}%",
                'Atendimento.id LIKE' => "%{$this->request->query['q']}%"
            ]);
        }
    
        // Filter by department
        if (isset($this->request->query["t"]) && $this->request->query["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Department.id' => $this->request->query["t"]]);
        }
    
        // Filter by customer
        if (isset($this->request->query['cliente']) && $this->request->query['cliente'] != "") {
            $condition['and'] = array_merge($condition['and'], ['Customer.nome_primario LIKE' => "%{$this->request->query['cliente']}%"]);
        }
    
        // Apply status filter only if a status is selected
        if (isset($this->request->query['status']) && $this->request->query['status'] != "") {
            $condition['and'] = array_merge($condition['and'], ['Atendimento.status_id' => $this->request->query['status']]);
        }
    
        // Export logic
        if (isset($this->request->query['exportar']) && $this->request->query['exportar'] === 'true') {
            $atendimentoData = $this->Atendimento->find('all', [
                'fields' => [
                    'Atendimento.id',
                    'Customer.nome_primario',
                    'Customer.documento',
                    'Department.name',
                    'Atendimento.name_atendente',
                    'Atendimento.subject',
                    'Atendimento.created',
                    'Atendimento.data_finalizacao',
                    'Atendimento.file_atendimento', // File field
                    'Status.name',
                    'Status.label'
                ],
                'conditions' => $condition,
                'contain' => ['Customer', 'Department', 'Status']
            ]);
    
            $this->ExcelGenerator->gerarExcelAtendimentos('Atendimentos', $atendimentoData);
    
            $this->redirect('/private_files/baixar/excel/Atendimentos_xlsx');
            return;
        }
    
        $data = $this->Paginator->paginate('Atendimento', $condition);
    
        // Contagem de atendidos com filtros
        $conditionAtendidos = $condition;
        $conditionAtendidos['and']['Atendimento.status_id'] = 35; // Status de "Atendido"
        $atendidos = $this->Atendimento->find('count', ['conditions' => $conditionAtendidos]);
    
        // Contagem de pendentes com filtros
        $conditionPendentes = $condition;
        $conditionPendentes['and']['Atendimento.status_id'] = 34; // Status de "Pendente"
        $pendentes = $this->Atendimento->find('count', ['conditions' => $conditionPendentes]);
    
        // Data for the view
        $departments = $this->Department->find('all', ['order' => 'Department.name']);
        $action = "Atendimentos";
        $this->set(compact('departments', 'data', 'atendidos', 'pendentes', 'action'));
    }
    
    
    
    public function view($id)
    {
        $this->Permission->check(21, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Atendimento->id = $id;
    
        if ($this->request->is(['post', 'put'])) {
            $this->Atendimento->validates();
            
            // Verifica se o arquivo de atendimento não foi enviado
            if ($this->request->data['Atendimento']['file_atendimento']['name'] == '') {
                unset($this->request->data['Atendimento']['file_atendimento']);
            }
            
            // Atribui o ID do usuário que fez a atualização
            $this->request->data['Atendimento']['user_updated_id'] = CakeSession::read("Auth.User.id");
    
            // Se o status for alterado para "atendido" (35), preenche os campos de finalização
            if ($this->request->data['Atendimento']['status_id'] == 35) {
                $this->request->data['Atendimento']['data_finalizacao'] = date('Y-m-d H:i:s');
                
                // Captura o nome do usuário logado e armazena no campo finalizado_por
                $this->request->data['Atendimento']['finalizado_por'] = CakeSession::read("Auth.User.name");
            }
    
            // Se há uma resposta, preenche as informações de resposta
            if ($this->request->data['Atendimento']['answer'] != "") {
                $this->request->data['Atendimento']['date_answer'] = date("Y-m-d H:i:s");
                $this->request->data['Atendimento']['user_answer_id'] = CakeSession::read("Auth.User.id");
                
                $atendimento = $this->Atendimento->find("first", ["conditions" => ["Atendimento.id" => $this->Atendimento->id]]);
                $this->envia_email($atendimento);
            }
    
            // Salva os dados do atendimento
            if ($this->Atendimento->save($this->request->data)) {
                $this->Flash->set(__('O atendimento foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('O atendimento não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }
    
        // Carrega os dados do atendimento
        $this->request->data = $this->Atendimento->read();
        
        $departments = $this->Department->find('list', ['order' => 'Department.name']);
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 9], 'order' => 'Status.name']);
        $customers = $this->Customer->find('list', ['order' => ['Customer.nome_primario']]);
    
        // Define variáveis para a view
        $this->set("action", $this->request->data['Atendimento']['subject']);
        $this->set("form_action", "view/".$id);
        $this->set(compact('departments', 'id', 'statuses', 'customers'));
    }
    

    public function add()
    {
        $this->Permission->check(21, "escrita") ? "" : $this->redirect("/not_allowed");
        
        if ($this->request->is(['post', 'put'])) {

            $this->Atendimento->create();
            $this->Atendimento->validates();
            $this->request->data['Atendimento']['user_updated_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['Atendimento']['mostrar_cliente'] = isset($this->request->data['Atendimento']['mostrar_cliente']) ? $this->request->data['Atendimento']['mostrar_cliente'] : 0;

            // Salvar os dados...
                        $this->request->data['Atendimento']['name_atendente'] = CakeSession::read('Auth.User.name');
            $this->request->data['Atendimento']['data_atendimento'] = date('Y-m-d H:i:s');

            if ($this->request->data['Atendimento']['answer'] != "") {
                $this->request->data['Atendimento']['date_answer'] = date("Y-m-d H:i:s");
                $this->request->data['Atendimento']['user_answer_id'] = CakeSession::read("Auth.User.id");
                    
                $atendimento = $this->Atendimento->find("first", ["conditions" => ["Atendimento.id" => $this->Atendimento->id] ]);

                // $this->envia_email($atendimento);
            }
            
            
            if ($this->Atendimento->save($this->request->data)) {
                $this->Flash->set(__('O atendimento foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('O atendimento não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }
        $userName = $this->Auth->user('name'); 
        $departments = $this->Department->find('list', ['order' => 'Department.name']);
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 9], 'order' => 'Status.name']);
        $customers = $this->Customer->find('list', ['order' => ['Customer.nome_primario']]);

        $this->set("action", 'Novo Atendimento');
        $this->set("form_action", "add");
        $this->set(compact('departments', 'id', 'statuses', 'customers','userName'));

        $this->render('view');
    }

    public function envia_email($data)
    {
        $dados = ['viewVars' => ['nome'  => $data['Customer']['nome_primario'],
            'email' => $data['Customer']['email'],
            'pergunta' => $data["Atendimento"]["message"],
            'resposta' => $data['Atendimento']['answer'],
            'link'  => 'https://cliente.berh.com.br/'
        ],
            'template' => 'resposta',
            'subject'  => 'BeRH - Resposta: '.$data['Atendimento']['subject'].' ',
            'config'   => 'default'
        ];

        if (!$this->Email->send($dados)) {
            $this->Flash->set(__('Email não pôde ser enviado com sucesso'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
