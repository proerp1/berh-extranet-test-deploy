<?php
use League\Csv\Reader;

class ItineraryCSVParser extends Controller
{
    public $uses = ['Customer', 'CustomerUser', 'CustomerUserItinerary', 'CustomerDepartment', 
                    'MaritalStatus', 'Benefit', 'SalaryRange', 'CustomerUserAddress', 
                    'CustomerUserBankAccount', 'CustomerPosition'];

    public function parse($fileName, $customerId)
    {
        $file = file_get_contents($fileName, FILE_IGNORE_NEW_LINES);

        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $num_lines = substr_count($file, "\n");

        if($num_lines < 2){
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $docs = [];
        $retRows = [];
        $customer = null;
        $line = 0;
        $has_error = false;
        foreach ($csv->getRecords() as $row) {
            if($line == 0){
                $line++;
                continue;
            }

            if(isset($row[0]) && $row[0] == ''){
                return ['success' => false, 'error' => 'O CNPJ do cliente não foi informado.'];
            }
            if(isset($row[0]) && $row[0] != '' && $customer != null){
                $cnpj = preg_replace('/\D/', '', $row[0]);
                $customer = $this->Customer->find('first', [
                    'conditions' => [
                        'cnpj' => $cnpj
                    ]
                ]);
    
                if($customer['Customer']['id'] != $customerId){
                    return ['success' => false, 'error' => 'O CNPJ do cliente não corresponde ao CNPJ do cliente do arquivo.'];
                }
            }

            $cpf = preg_replace('/\D/', '', $row[3]);
            $currentUserId = 0;
            if(in_array($cpf, $docs)){
                $currentUserId = $docs[$cpf];
            } else {
                $userId = $this->processUser($row, $customerId);
                $docs[$cpf] = $userId['userId'];
                $currentUserId = $docs[$cpf];
            }
            
            $ret = $this->processItinerary($row, $currentUserId);
            if(!$ret['success']){
                $has_error = true;
            }
            $retRows = $ret;

        }

        return ['success' => true, 'error' => false, 'rows' => $retRows, 'has_inner_error' => $has_error];
    }

    private function processUser($row, $customerId){
        // Process each row
        $cpf = $row[3];
        $cpf = preg_replace('/\D/', '', $row[3]);

        // Check if the user with the given CPF already exists
        $existingUser = $this->CustomerUser->find('first', [
            'conditions' => [
                'cpf' => $cpf
            ]
        ]);

        $customerDepartment = $this->CustomerDepartment->find('first', [
            'conditions' => [
                'name' => $row[8],
                'customer_id' => $customerId
            ]
        ]);

        if(!$customerDepartment){
            $this->CustomerDepartment->create();
            $this->CustomerDepartment->save([
                'name' => $row[8],
                'customer_id' => $customerId
            ]);
            $customerDepartment = $this->CustomerDepartment->find($this->CustomerDepartment->id);
        }

        $maritalStatus = $this->MaritalStatus->find('first', [
            'conditions' => [
                'status' => $row[52],
            ]
        ]);

        $maritalStatusId = null;
        if($maritalStatus){
            $maritalStatusId = $maritalStatus['MaritalStatus']['id'];
        }

        $salaryRange = $this->SalaryRange->find('first', [
            'conditions' => [
                'range' => $row[22]
            ]
        ]);

        $salaryRangeId = null;
        if($salaryRange){
            $salaryRangeId = $salaryRange['SalaryRange']['id'];
        }

        $customerPositionId = null;
        if($row[56] != ''){
            $position = $this->CustomerPosition->find('first', [
                'conditions' => [
                    'name' => $row[56],
                    'customer_id' => $customerId
                ]
            ]);

            if(!$position){
                $this->CustomerPosition->create();
                $this->CustomerPosition->save([
                    'name' => $row[56],
                    'customer_id' => $customerId
                ]);

                $position = $this->CustomerPosition->find($this->CustomerPosition->id);
            }

            $customerPositionId = $position['CustomerPosition']['id'];
        }

        if($row[53] == ''){
            return ['success' => false, 'error' => 'E-mail não é válido', 'userId' => 0, 'cpf' => $cpf, 'benefit_code' => $row[13]];
        }

        if (!$existingUser) {
            // User doesn't exist, create new Customer User
            $userData = [
                'cpf' => $cpf,
                'email' => $row[53],
                'tel' => '('.$row[54].') '.$row[55],
                'name' => $row[2],
                'rg' => $row[4],
                'emissor_rg' => $row[5],
                'customer_id' => $customerId,
                'status_id' => 1,
                'data_nascimento' => $row[6],
                'nome_mae' => $row[7],
                'customer_departments_id' => $customerDepartment['CustomerDepartment']['id'],
                'numero_sic' => $row[21],
                'sexo' => $row[51],
                'marital_status_id' => $maritalStatusId,
                'customer_salary_id' => $salaryRangeId,
                'customer_positions_id' => $customerPositionId,
            ];
            $this->CustomerUser->create();
            $this->CustomerUser->save($userData);
            
            $userId = $this->CustomerUser->id;
        } else {
            $userId = $existingUser['CustomerUser']['id'];
        }

        if($row[23] != '' && $row[24] != ''){
            $address = $this->CustomerUserAddress->find('first', [
                'conditions' => [
                    'customer_user_id' => $userId,
                    'zip_code' => $row[23],
                ]
            ]);

            if(!$address){
                $addressData = [
                    'customer_id' => $customerId,
                    'customer_user_id' => $userId,
                    'zip_code' => $row[23],
                    'address_line' => $row[24],
                    'address_number' => $row[25],
                    'address_complement' => $row[26],
                    'neighborhood' => $row[27],
                    'city' => $row[28],
                    'state' => $row[29],
                ];
    
                $this->CustomerUserAddress->create();
                $this->CustomerUserAddress->save($addressData); 
            }
        }

        if($row[44] != ''){
            $bankAccount = $this->CustomerUserBankAccount->find('first', [
                'conditions' => [
                    'customer_user_id' => $userId,
                    'bank_code' => $row[46],
                    'acc_number' => $row[49],
                ]
            ]);

            if(!$bankAccount){
                $bankAccountData = [
                    'customer_id' => $customerId,
                    'customer_user_id' => $userId,
                    'account_type_id' => $row[44],
                    'bank_code' => $row[46],
                    'acc_number' => $row[49],
                    'acc_digit' => $row[50],
                    'branch_number' => $row[47],
                    'branch_digit' => $row[48],
                ];
    
                $this->CustomerUserBankAccount->create();
                $this->CustomerUserBankAccount->save($bankAccountData); 
            }
        }

        return ['success' => false, 'error' => 'E-mail não é válido', 'userId' => $userId, 'cpf' => $cpf, 'benefit_code' => $row[13]];
    }

    private function processItinerary($row, $userId){
        $benefit = $this->Benefit->find('first', [
            'conditions' => [
                'Benefit.code' => $row[13]
            ]
        ]);

        if(!$benefit){
            return ['success' => false, 'error' => 'Benefício não encontrado.', 'userId' => $userId, 'benefit_code' => $row[13]];
        }


        $itineraryData = [
            'benefit_id' => $benefit['Benefit']['id'],
            'customer_id' => $row['CustomerUser']['customer_id'],
            'customer_user_id' => $userId,
            'working_days' => $row[9],
            'card_number' => $row[14],
            'unit_price' => $row[15],
            'quantity' => $row[18],

        ];
        $this->CustomerUserItinerary->create();
        $this->CustomerUserItinerary->save($itineraryData);

        return ['success' => true, 'error' => false, 'userId' => $userId, 'benefit_code' => $row[13]];
    }
}