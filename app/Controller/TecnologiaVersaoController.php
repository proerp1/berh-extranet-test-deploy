<?php
class TecnologiaVersaoController extends AppController
{
    public $helpers = ['Html', 'Form'];
    public $components = ['Paginator', 'Permission'];
    public $uses = ['Tecnologia', 'TecnologiaVersao'];

    public $paginate = [
        'limit' => 10, 'order' => ['TecnologiaVersao.name' => 'asc']
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function index($id)
    {
        $this->Permission->check(16, "leitura") ? "" : $this->redirect("/not_allowed");
        $this->Paginator->settings = $this->paginate;
    
        $condition = ["and" => ['TecnologiaVersao.tecnologia_id' => $id], "or" => []];
    
        if (isset($_GET['q']) and $_GET['q'] != "") {
            $condition['or'] = [
                'Tecnologia.name LIKE' => "%" . $_GET['q'] . "%",
                'Tecnologia.id LIKE' => "%" . $_GET['q'] . "%"
            ];
        }
        
        $data = $this->Paginator->paginate('TecnologiaVersao', $condition);
    
        $action = 'TecnologiaVersao';
        $breadcrumb = ['Cadastros' => '', 'Tecnologia' => '', 'Versões' => ''];
        $this->set(compact('data', 'action', 'breadcrumb', 'id'));
    }
    
    
    public function add($tecnologia_id)
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        if ($this->request->is(['post', 'put'])) {
            $this->TecnologiaVersao->create();
            if ($this->TecnologiaVersao->validates()) {
                $this->request->data['TecnologiaVersao']['tecnologia_id'] = $tecnologia_id;
                $this->request->data['TecnologiaVersao']['user_creator_id'] = CakeSession::read("Auth.User.id");
                if ($this->TecnologiaVersao->save($this->request->data)) {
                    $this->Flash->set(__('A versão foi salva com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                    $this->redirect(['action' => 'index', $tecnologia_id]);
                } else {
                    $this->Flash->set(__('A versão não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
                }
            } else {
                $this->Flash->set(__('A versão não pode ser salva, Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $action = 'TecnologiaVersao';
        $breadcrumb = ['Cadastros' => '', 'Tecnologia Versao' => '', 'Nova Versao' => ''];
        $this->set("form_action", "../tecnologia_versao/add/".$tecnologia_id);
        $this->set(compact('action', 'breadcrumb', 'tecnologia_id'));
    }

    public function edit($tecnologia_id, $id = null)
    {
        $this->Permission->check(16, "escrita") ? "" : $this->redirect("/not_allowed");
        $this->TecnologiaVersao->id = $id;
        if ($this->request->is(['post', 'put'])) {
            $this->TecnologiaVersao->validates();
            $this->request->data['TecnologiaVersao']['user_updated_id'] = CakeSession::read("Auth.User.id");
            if ($this->TecnologiaVersao->save($this->request->data)) {
                $this->Flash->set(__('A TecnologiaVersao foi alterada com sucesso'), ['params' => ['class' => "alert alert-success"]]);
                $this->redirect(['action' => 'index', $tecnologia_id]);
            } else {
                $this->Flash->set(__('A TecnologiaVersao não pode ser alterada , Por favor tente de novo.'), ['params' => ['class' => "alert alert-danger"]]);
            }
        }

        $temp_errors = $this->TecnologiaVersao->validationErrors;
        $this->request->data = $this->TecnologiaVersao->read();
        $this->TecnologiaVersao->validationErrors = $temp_errors;

        $action = 'TecnologiaVersao';
        $breadcrumb = ['Cadastros' => '', 'Tecnologia' => '', 'Alterar Versao' => ''];
        $this->set("form_action", "../tecnologia_versao/edit/".$tecnologia_id);
        $this->set(compact( 'id', 'action', 'breadcrumb', 'tecnologia_id'));
        
        $this->render("add");
    }

    public function delete($id)
    {
        $this->Permission->check(16, "excluir") ? "" : $this->redirect("/not_allowed");
        $this->TecnologiaVersao->id = $id;
        $this->request->data = $this->TecnologiaVersao->read();

        $this->request->data['TecnologiaVersao']['data_cancel'] = date("Y-m-d H:i:s");
        $this->request->data['TecnologiaVersao']['usuario_id_cancel'] = CakeSession::read("Auth.User.id");

        if ($this->TecnologiaVersao->save($this->request->data)) {
            $this->Flash->set(__('A versão foi excluida com sucesso'), ['params' => ['class' => "alert alert-success"]]);
            $this->redirect(['action' => 'index']);
        }
    }

    public function get($tecnologia_id) {
        $this->layout = 'ajax';
        $this->autoRender = false;

        $conditions = ['TecnologiaVersao.tecnologia_id' => $tecnologia_id];
        $fields = ['TecnologiaVersao.id', 'TecnologiaVersao.nome'];

        $versoes = [
            'cadastro' => $this->TecnologiaVersao->find('list', ['fields' => $fields, 'conditions' => array_merge($conditions, ['TecnologiaVersao.tipo' => 'cadastro'])]),
            'credito' => $this->TecnologiaVersao->find('list', ['fields' => $fields, 'conditions' => array_merge($conditions, ['TecnologiaVersao.tipo' => 'credito'])]),
        ];

        echo json_encode($versoes);
    }
}
