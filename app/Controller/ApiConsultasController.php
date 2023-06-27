<?php
class ApiConsultasController extends AppController
{
    public $components = ['ApiCheckToken', 'StringRequest'];
    public $uses = ['Acesso', 'AcessoFeature'];

    public function beforeFilter()
    {
        $this->Auth->allow();
    }

    public function getFeatures()
    {
        $this->autoRender = false;
        $this->layout = 'api';
        $this->response->type('json');

        if ($this->request->is('post')) {
            $check = $this->ApiCheckToken->check($this->request->data);
            if (!empty($check['error'])) {
                return json_encode(['success' => false, 'message' => $check['error']]);
            }

            $features = $this->AcessoFeature->find_feature_permitidas_por_cliente_api($this->request->data['product'], $check['CustomerToken']['customer_id']);

            return json_encode($features);
        }

        $this->response->statusCode(405);
        return json_encode(['success' => false]);
    }

    public function crednetTop()
    {
        $this->autoRender = false;
        $this->layout = 'api';
        $this->response->type('json');

        if ($this->request->is('post')) {
            $requiredFields = [
                'api_token', 
                'tipo_pessoa', // '1',
                'documento', // '000.000.004-34',
                'estado', // 'SP',
                'ddd', // '11',
                'tel', // '1231-2312',
                'cep', // '11231-231',
            ];
            if (!$this->ApiCheckToken->checkFilledParams($this->request->data, $requiredFields)) {
                $this->response->statusCode(400);
                return json_encode(['success' => false, 'message' => "Missing parameters"]);
            }

            $check = $this->ApiCheckToken->check($this->request->data);
            if (!empty($check['error'])) {
                return json_encode(['success' => false, 'message' => $check['error']]);
            }

            if ($this->request->data['tipo_pessoa'] == 1) {
                $this->request->data['product_id'] = 450;
            } else {
                $this->request->data['product_id'] = 451;
            }

            $checkFeatures = $this->checkFeatures($check['CustomerToken']['customer_id']);
            if (!$checkFeatures['success']) {
                $this->response->statusCode(400);
                return json_encode($checkFeatures);
            }

            $where = " a.customer_id = ".$check['CustomerToken']['customer_id'];
            $acesso = $this->Acesso->find_acesso_by_produto($this->request->data['product_id'], $where);

            if (empty($acesso)) {
                $this->response->statusCode(400);
                return json_encode(['success' => false, 'message' => "Você não possui acesso a esse produto."]);
            }

            $request = $this->StringRequest->crednet($this->request->data, $check['CustomerToken']['customer_id'], $check['CustomerToken']['id'], $check['Customer']['tipo_pessoa'], $check['Customer']['documento']);

            return json_encode($request);
        }

        $this->response->statusCode(405);
        return json_encode(['success' => false]);
    }

    public function crednetLight()
    {
        $this->autoRender = false;
        $this->layout = 'api';
        $this->response->type('json');

        if ($this->request->is('post')) {
            $requiredFields = [
                'api_token', 
                'tipo_pessoa', // '1',
                'documento', // '000.000.004-34',
                'estado', // 'SP',
                'ddd', // '11',
                'tel', // '1231-2312',
                'cep', // '11231-231',
            ];
            if (!$this->ApiCheckToken->checkFilledParams($this->request->data, $requiredFields)) {
                $this->response->statusCode(400);
                return json_encode(['success' => false, 'message' => "Missing parameters"]);
            }

            $check = $this->ApiCheckToken->check($this->request->data);
            if (!empty($check['error'])) {
                return json_encode(['success' => false, 'message' => $check['error']]);
            }

            if ($this->request->data['tipo_pessoa'] == 1) {
                $this->request->data['product_id'] = 452;
            } else {
                $this->request->data['product_id'] = 453;
            }

            $checkFeatures = $this->checkFeatures($check['CustomerToken']['customer_id']);
            if (!$checkFeatures['success']) {
                $this->response->statusCode(400);
                return json_encode($checkFeatures);
            }

            $where = " a.customer_id = ".$check['CustomerToken']['customer_id'];
            $acesso = $this->Acesso->find_acesso_by_produto($this->request->data['product_id'], $where);

            if (empty($acesso)) {
                $this->response->statusCode(400);
                return json_encode(['success' => false, 'message' => "Você não possui acesso a esse produto."]);
            }

            $request = $this->StringRequest->crednetLight($this->request->data, $check['CustomerToken']['customer_id'], $check['CustomerToken']['id'], $check['Customer']['tipo_pessoa'], $check['Customer']['documento']);

            return json_encode($request);
        }

        $this->response->statusCode(405);
        return json_encode(['success' => false]);
    }

    public function concentre()
    {
        $this->autoRender = false;
        $this->layout = 'api';
        $this->response->type('json');

        if ($this->request->is('post')) {
            $requiredFields = [
                'api_token', 
                'tipo_pessoa', // '1',
                'documento', // '000.000.004-34',
                'ddd', // '11',
                'tel', // '1231-2312',
            ];

            if (!$this->ApiCheckToken->checkFilledParams($this->request->data, $requiredFields)) {
                $this->response->statusCode(400);
                return json_encode(['success' => false, 'message' => "Missing parameters"]);
            }

            $check = $this->ApiCheckToken->check($this->request->data);
            if (!empty($check['error'])) {
                return json_encode(['success' => false, 'message' => $check['error']]);
            }

            if ($this->request->data['tipo_pessoa'] == 1) {
                $this->request->data['product_id'] = 455;
            } else {
                $this->request->data['product_id'] = 456;
            }

            $checkFeatures = $this->checkFeatures($check['CustomerToken']['customer_id']);
            if (!$checkFeatures['success']) {
                $this->response->statusCode(400);
                return json_encode($checkFeatures);
            }

            $where = " a.customer_id = ".$check['CustomerToken']['customer_id'];
            $acesso = $this->Acesso->find_acesso_by_produto($this->request->data['product_id'], $where);

            if (empty($acesso)) {
                $this->response->statusCode(400);
                return json_encode(['success' => false, 'message' => "Você não possui acesso a esse produto."]);
            }

            $request = $this->StringRequest->concentre($this->request->data, $check['CustomerToken']['customer_id'], $check['CustomerToken']['id'], $check['Customer']['tipo_pessoa'], $check['Customer']['documento']);

            return json_encode($request);
        }

        $this->response->statusCode(405);
        return json_encode(['success' => false]);
    }

    public function checkFeatures($customerId)
    {   
        if (isset($this->request->data['feature_check'])) {
            $features = Set::extract('{n}.Feature.id', $this->AcessoFeature->find_feature_permitidas_por_cliente_api($this->request->data['product_id'], $customerId));

            foreach ($this->request->data['feature_check'] as $feature_check) {
                if ($features && !in_array($feature_check, $features)) {
                    return ['success' => false, 'message' => "Feature {$feature_check} não permitida"];
                }
            }
        }

        return ['success' => true];
    }
}
