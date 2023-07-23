<?php
class AtendimentosController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission', 'Email'];
    public $uses = ['Atendimento', 'Department', 'Status', 'Customer'];

    public $paginate = [
        'limit' => 10, 'order' => ['Atendimento.created' => 'desc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index()
    {
        $this->Permission->check(21, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;

        $condition = ["and" => ['Customer.cod_franquia' => CakeSession::read("Auth.User.resales")], "or" => []];

        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = array_merge($condition['or'], ['Atendimento.subject LIKE' => "%".$_GET['q']."%", 'Atendimento.message LIKE' => "%".$_GET['q']."%"]);
        }

        if (isset($_GET["t"]) and $_GET["t"] != "") {
            $condition['and'] = array_merge($condition['and'], ['Department.id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('Atendimento', $condition);
        $departments = $this->Department->find('all', ['order' => 'Department.name']);
        $atendidos = $this->Atendimento->find('count', ['conditions' => ['Atendimento.status_id' => 35]]);
        $pendentes = $this->Atendimento->find('count', ['conditions' => ['Atendimento.status_id' => 34]]);

        $action = "Atendimentos";
        $this->set(compact('departments', 'data', 'atendidos', 'pendentes', 'action'));
    }

    public function view($id)
    {
        $this->Permission->check(21, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Atendimento->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->Atendimento->validates();
            $this->request->data['Atendimento']['user_updated_id'] = CakeSession::read("Auth.User.id");

            if ($this->request->data['Atendimento']['answer'] != "") {
                $this->request->data['Atendimento']['date_answer'] = date("Y-m-d H:i:s");
                $this->request->data['Atendimento']['user_answer_id'] = CakeSession::read("Auth.User.id");
                    
                $atendimento = $this->Atendimento->find("first", ["conditions" => ["Atendimento.id" => $this->Atendimento->id] ]);

                $this->envia_email($atendimento);
            }

            if ($this->Atendimento->save($this->request->data)) {
                $this->Flash->set(__('O atendimento foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('O atendimento não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $this->request->data = $this->Atendimento->read();
        
        $departments = $this->Department->find('list', ['order' => 'Department.name']);
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 9], 'order' => 'Status.name']);
        $customers = $this->Customer->find('list', ['order' => ['Customer.nome_secundario']]);

        $this->set("action", $this->request->data['Atendimento']['subject']);
        $this->set("form_action", "view/".$id);
        $this->set(compact('departments', 'id', 'statuses', 'customers'));
    }

    public function add()
    {
        $this->Permission->check(21, "leitura") ? "" : $this->redirect("/not_allowed");
        
        if ($this->request->is(['post', 'put'])) {
            $this->Atendimento->validates();
            $this->request->data['Atendimento']['user_updated_id'] = CakeSession::read("Auth.User.id");
            $this->request->data['Atendimento']['mostrar_cliente'] = 0;
            $this->request->data['Atendimento']['name_atendente'] = CakeSession::read('Auth.User.name');
            $this->request->data['Atendimento']['data_atendimento'] = date('Y-m-d H:i:s');

            if ($this->request->data['Atendimento']['answer'] != "") {
                $this->request->data['Atendimento']['date_answer'] = date("Y-m-d H:i:s");
                $this->request->data['Atendimento']['user_answer_id'] = CakeSession::read("Auth.User.id");
                    
                $atendimento = $this->Atendimento->find("first", ["conditions" => ["Atendimento.id" => $this->Atendimento->id] ]);

                // $this->envia_email($atendimento);
            }
            
            $this->Atendimento->create();
            if ($this->Atendimento->save($this->request->data)) {
                $this->Flash->set(__('O atendimento foi alterado com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->set(__('O atendimento não pode ser alterado, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        // debug($this->Atendimento->validationErrors);die();
        $departments = $this->Department->find('list', ['order' => 'Department.name']);
        $statuses = $this->Status->find('list', ['conditions' => ['Status.categoria' => 9], 'order' => 'Status.name']);
        $customers = $this->Customer->find('list', ['fields' => ['Customer.codigo_associado'], 'order' => ['Customer.codigo_associado']]);

        $this->set("action", 'Novo Atendimento por Telefone');
        $this->set("form_action", "add");
        $this->set(compact('departments', 'id', 'statuses', 'customers'));

        $this->render('view');
    }

    public function envia_email($data)
    {
        $dados = ['viewVars' => ['nome'  => $data['Customer']['nome_primario'],
            'email' => $data['Customer']['email'],
            'pergunta' => $data["Atendimento"]["message"],
            'resposta' => $data['Atendimento']['answer'],
            'link'  => 'http://159.203.161.218/cliente'
        ],
            'template' => 'resposta',
            'subject'  => 'Resposta',
            'config'   => 'default'
        ];

        if (!$this->Email->send($dados)) {
            $this->Flash->set(__('Email não pôde ser enviado com sucesso'), ['params' => ['class' => "alert alert-danger"]]);
            $this->redirect(['action' => 'index']);
        }
    }
}
