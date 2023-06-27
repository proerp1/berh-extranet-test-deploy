<?php

class ApiCheckTokenComponent extends Component {
    public $components = array('Session');

    /*
    *    campos post:
    *        'api_token' string
    */
    public function check($request)
    {
        $CustomerToken = ClassRegistry::init('CustomerToken');

        if (!isset($request['api_token'])) {
            return ['error' => 'Missing field api_token'];
        }

        $user = $CustomerToken->find('first', [
            'conditions' => [
                'CustomerToken.token' => $request['api_token'],
                'CustomerToken.expire_date > current_date()',
                'CustomerToken.status_id' => 1,
            ],
            'fields' => ['CustomerToken.id', 'CustomerToken.customer_id', 'Customer.documento', 'Customer.tipo_pessoa'],
        ]);

        if (!$user) {
            return ['error' => 'Token inválido'];
        }

        return $user;
    }

    public function checkFilledParams($request, $requiredFields)
    {
        if (count(array_intersect_key($request, array_fill_keys($requiredFields, ''))) == count($requiredFields)) {
            return true;
        }

        return false;
    }
}
