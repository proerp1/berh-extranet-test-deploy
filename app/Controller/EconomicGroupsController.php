<?php
use League\Csv\Reader;

App::uses('AppController', 'Controller');

class EconomicGroupsController extends AppController
{
    public $components = ['Paginator', 'Permission'];
    public $uses = ['EconomicGroup', 'Status', 'Customer'];

    public function index($id)
    {
        $this->Paginator->settings = $this->paginate;

        $condition = ['and' => ['EconomicGroup.customer_id' => $id], 'or' => []];

        if (!empty($_GET['q'])) {
            $condition['or'] = array_merge($condition['or'], ['EconomicGroup.name LIKE' => '%' . $_GET['q'] . '%', 'EconomicGroup.razao_social LIKE' => '%' . $_GET['q'] . '%']);
        }

        if (!empty($_GET['t'])) {
            $condition['and'] = array_merge($condition['and'], ['EconomicGroup.status_id' => $_GET['t']]);
        }

        $data = $this->Paginator->paginate('EconomicGroup', $condition);
        $status = $this->Status->find("all", ["conditions" => ["Status.categoria" => 1]]);

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $action = 'Grupos Econômicos';

        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Grupos Econômicos' => ''
        ];
        $this->set(compact('data', 'action', 'id', 'breadcrumb', 'status'));
    }

    public function add($id)
    {
        if ($this->request->is('post')) {
            $this->request->data['EconomicGroup']['customer_id'] = $id;
            $this->request->data['EconomicGroup']['user_creator_id'] = CakeSession::read("Auth.CustomerUser.id");

            $this->EconomicGroup->create();
            if ($this->EconomicGroup->save($this->request->data)) {
                $this->Flash->set('grupo econômico adicionado com sucesso.', ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->set('Falha ao adicionar grupo econômico. Por favor, tente novamente.', ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $statuses = $this->Status->find("list", ["conditions" => ["Status.categoria" => 1]]);

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $action = 'Novo grupo econômico';
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Grupos Econômicos' => ['controller' => 'group_economics', 'action' => 'index'],
            'Novo grupo econômico' => '',
        ];
        $this->set(compact('action', 'breadcrumb', 'statuses', 'id'));
    }

    public function edit($id, $economicGroupId = null)
    {
        $economicGroup = $this->EconomicGroup->findById($economicGroupId);
        if (!$economicGroup) {
            $this->Flash->set('Grupo econômico não encontrado.', ['params' => ['class' => 'alert alert-danger']]);
            $this->redirect(['action' => 'index']);
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->EconomicGroup->id = $economicGroupId;
            $this->request->data['EconomicGroup']['user_updated_id'] = CakeSession::read("Auth.CustomerUser.id");

            if ($this->EconomicGroup->save($this->request->data)) {
                $this->Flash->set('Grupo econômico atualizado com sucesso.', ['params' => ['class' => 'alert alert-success']]);
                $this->redirect(['action' => 'index', $id]);
            } else {
                $this->Flash->set('Falha ao atualizar grupo econômico. Por favor, tente novamente.', ['params' => ['class' => 'alert alert-danger']]);
            }
        }

        $temp_errors = $this->EconomicGroup->validationErrors;
        $this->request->data = $economicGroup;
        $this->EconomicGroup->validationErrors = $temp_errors;

        $statuses = $this->Status->find("list", ["conditions" => ["Status.categoria" => 1]]);

        $this->Customer->id = $id;
        $this->Customer->recursive = -1;
        $cliente = $this->Customer->read();

        $action = 'Novo grupo econômico';
        $breadcrumb = [
            $cliente['Customer']['nome_secundario'] => ['controller' => 'customers', 'action' => 'edit', $id],
            'Grupos Econômicos' => ['controller' => 'group_economics', 'action' => 'index'],
            $this->request->data['EconomicGroup']['name'] => '',
        ];

        $this->set(compact('action', 'breadcrumb', 'id', 'economicGroupId', 'statuses'));
        $this->render('add');
    }

    public function delete($id = null)
    {
        $this->EconomicGroup->id = $id;
        $this->request->data = $this->EconomicGroup->read();

        $this->request->data['EconomicGroup']['data_cancel'] = date('Y-m-d H:i:s');
        $this->request->data['EconomicGroup']['usuario_id_cancel'] = CakeSession::read('Auth.CustomerUser.id');

        if ($this->EconomicGroup->save($this->request->data)) {
            $this->Flash->set(__('O usuário foi excluido com sucesso'), ['params' => ['class' => 'alert alert-success']]);
            $this->redirect(['action' => 'index', $this->request->data['EconomicGroup']['customer_id']]);
        }
    }

    public function upload($customerId)
    {
        $file = file_get_contents($this->request->data['file']['tmp_name'], FILE_IGNORE_NEW_LINES);
        $file = mb_convert_encoding($file, 'UTF-8', 'ISO-8859-1');
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 1) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $line = 0;
        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                if ($line == 0) {
                    $line++;
                }
                continue;
            }

            $this->EconomicGroup->create();
            $this->EconomicGroup->save([
                'EconomicGroup' => [
                    'customer_id' => $customerId,
                    'status_id' => 1,
                    'name' => $row[0],
                    'document' => preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', preg_replace('/\D/', '', $row[1])),
                    'razao_social' => $row[2],
                    'cep' => preg_replace('/(\d{5})(\d{3})/', '$1-$2', preg_replace('/\D/', '', $row[3])),
                    'endereco' => $row[4],
                    'numero' => $row[5],
                    'complemento' => $row[6],
                    'cidade' => $row[7],
                    'bairro' => $row[8],
                    'estado' => $row[9],
                    'cepentrega' => preg_replace('/(\d{5})(\d{3})/', '$1-$2', preg_replace('/\D/', '', $row[10])),
                    'enderecoentrega' => $row[11],
                    'numeroentrega' => $row[12],
                    'complementoentrega' => $row[13],
                    'cidadeentrega' => $row[14],
                    'bairroentrega' => $row[15],
                    'estadoentrega' => $row[16],
                ],
            ]);

            $line++;
        }

        $this->Flash->set(__('Grupos econômicos incluídos com sucesso'), ['params' => ['class' => "alert alert-success"]]);
        $this->redirect(['action' => 'index/' . $customerId]);
    }
}
