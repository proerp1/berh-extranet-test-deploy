<?php

use League\Csv\Reader;

class ItineraryCSVParser extends Controller
{
    public $uses = [
        'Customer', 'CustomerUser', 'CustomerUserItinerary', 'CustomerDepartment',
        'MaritalStatus', 'Benefit', 'SalaryRange', 'CustomerUserAddress',
        'CustomerUserBankAccount', 'CustomerPosition', 'CSVImport', 'CSVImportLine',
        'BankCode'
    ];

    public function parse($tmpFile, $fileName, $customerId, $userId, $importedByCustomer = false, $shouldDeleteItinerary)
    {
        $file = file_get_contents($tmpFile, FILE_IGNORE_NEW_LINES);
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 2) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $docs = [];
        $customer = null;
        $line = 0;
        $csvImportFileId = 0;
        $hasPartialError = false;

        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                if($line == 0){
                    $line++;
                }
                continue;
            }

            // Processamento da primeira linha, para identificar o cliente
            if (!empty($row[0]) && $line == 1) {
                $line++;
                $cnpj = preg_replace('/\D/', '', $row[0]);
                $file_error = false;
                $file_error_message = '';
                if ($cnpj == '') {
                    // return ['success' => false, 'error' => 'O CNPJ do cliente não foi informado.'];
                    $file_error = true;
                    $file_error_message = 'O CNPJ do cliente não foi informado.';
                    continue;
                }

                if ($customer != null) {
                    $customer = $this->Customer->find('first', [
                        'conditions' => [
                            'cnpj' => $cnpj
                        ]
                    ]);

                    if ($customer['Customer']['id'] != $customerId) {
                        // return ['success' => false, 'error' => 'O CNPJ do cliente não corresponde ao CNPJ do cliente do arquivo.'];
                        $file_error_message = 'O CNPJ do cliente não corresponde ao CNPJ do cliente do arquivo.';
                        $file_error = true;
                    }
                }

                $this->CSVImport->create();
                $this->CSVImport->save([
                    'customer_id' => $customerId,
                    'user_id' => $userId,
                    'file_name' => $fileName,
                    'status_id' => $file_error ? 89 : 88,
                    'message' => $file_error_message,
                    'imported_by_customer' => $importedByCustomer,
                ]);
                $csvImportFileId = $this->CSVImport->id;

                if($file_error){
                    return ['success' => false, 'file_id' => $csvImportFileId];
                }
            }
            // fim processamento da primeira linha

            $cpf = preg_replace('/\D/', '', $row[3]);
            $currentUserId = 0;
            // se o usuário já foi importado, já pula para o processamento do itinerário
            if (isset($docs[$cpf])) {
                $currentUserId = $docs[$cpf];
            } else {
                // se não, importa o usuário primeiro
                $retUser = $this->processUser($row, $customerId, $shouldDeleteItinerary);

                if($retUser['success'] == false){
                    $this->CSVImportLine->create();
                    $this->CSVImportLine->save([
                        'CSVImportLine' => [
                            'csv_import_id' => $csvImportFileId,
                            'cpf' => $cpf,
                            'benefit_code' => $row[13],
                            'status_id' => $retUser['success'] ? 88 : 89,
                            'message' => $retUser['message'],
                        ]
                    ]);

                    $hasPartialError = true;

                    continue;
                }

                $docs[$cpf] = $retUser['userId'];
                $currentUserId = $docs[$cpf];
            }

            // processa o itinerário
            $retItinerary = $this->processItinerary($row, $currentUserId, $customerId);
            if($retItinerary['success'] == false){
                $this->CSVImportLine->create();
                $this->CSVImportLine->save([
                    'CSVImportLine' => [
                        'csv_import_id' => $csvImportFileId,
                        'cpf' => $cpf,
                        'benefit_code' => $row[13],
                        'status_id' => $retItinerary['success'] ? 88 : 89,
                        'message' => $retItinerary['message'],
                    ]
                ]);

                $hasPartialError = true;

                continue;
            }

            // se não parou nos erros, então o usuário foi importado com sucesso
            $this->CSVImportLine->create();
            $this->CSVImportLine->save([
                'CSVImportLine' => [
                    'csv_import_id' => $csvImportFileId,
                    'cpf' => $cpf,
                    'benefit_code' => $row[13],
                    'status_id' => 88,
                    'message' => 'Importado com sucesso',
                    'user_id' => $retUser['userId']
                ]
            ]);
        }

        if($hasPartialError){
            $this->CSVImport->$csvImportFileId;
            $this->CSVImport->save([
                'id' => $csvImportFileId,
                'status_id' => 90
            ]);
        }

        return ['success' => true, 'file_id' => $csvImportFileId];
    }

    private function processUser($row, $customerId, $shouldDeleteItinerary)
    {
        $cpf = preg_replace('/\D/', '', $row[3]);

        $existingUser = $this->CustomerUser->find('first', [
            'conditions' => [
                'CustomerUser.cpf' => $cpf,
                'CustomerUser.customer_id' => $customerId,
            ]
        ]);

        $customerDepartment = $this->getOrCreateCustomerDepartment($row[8], $customerId);

        $maritalStatusId = $this->getMaritalStatusId($row[52]);

        $salaryRangeId = $this->getSalaryRangeId($row[22]);

        $customerPositionId = $this->getCustomerPositionId($row[56], $customerId);

        // Por enquanto vão permitir e-mail vazio
        // if ($row[53] == '') {
        //     return ['success' => false, 'message' => 'E-mail não é válido', 'userId' => 0, 'cpf' => $cpf, 'benefit_code' => $row[13]];
        // }

        $userData = [
            'cpf' => $cpf,
            'email' => $row[53],
            'tel' => '(' . $row[54] . ') ' . $row[55],
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

        $encoding = mb_detect_encoding($userData['name'], "UTF-8, ISO-8859-1, Windows-1252", true);
        $endereco = $row[24];
        $bairro = $row[27];
        $cidade = $row[28];

        $enderecoTrabalho = $row[31];
        $bairroTrabalho = $row[34];
        $cidadeTrabalho = $row[35];

        $enderecoEntrega = $row[38];
        $bairroEntrega = $row[41];
        $cidadeEntrega = $row[42];

        if($encoding != 'UTF-8'){
            $userData['name'] = mb_convert_encoding($userData['name'], "UTF-8", "Windows-1252");
            $userData['nome_mae'] = mb_convert_encoding($userData['nome_mae'], "UTF-8", "Windows-1252");
            $endereco = mb_convert_encoding($endereco, "UTF-8", "Windows-1252");
            $bairro = mb_convert_encoding($bairro, "UTF-8", "Windows-1252");
            $cidade = mb_convert_encoding($cidade, "UTF-8", "Windows-1252");
            $enderecoTrabalho = mb_convert_encoding($enderecoTrabalho, "UTF-8", "Windows-1252");
            $bairroTrabalho = mb_convert_encoding($bairroTrabalho, "UTF-8", "Windows-1252");
            $cidadeTrabalho = mb_convert_encoding($cidadeTrabalho, "UTF-8", "Windows-1252");
            $enderecoEntrega = mb_convert_encoding($enderecoEntrega, "UTF-8", "Windows-1252");
            $bairroEntrega = mb_convert_encoding($bairroEntrega, "UTF-8", "Windows-1252");
            $cidadeEntrega = mb_convert_encoding($cidadeEntrega, "UTF-8", "Windows-1252");
        }


        if (!$existingUser) {
            $this->CustomerUser->create();
            $this->CustomerUser->save($userData);
            if(isset($this->CustomerUser->validationErrors['email'])){
                return ['success' => false, 'message' => $this->CustomerUser->validationErrors['email'][0], 'userId' => 0, 'cpf' => $cpf, 'benefit_code' => $row[13]];
            }
            $userId = $this->CustomerUser->id;
        } else {
            $userId = $existingUser['CustomerUser']['id'];

            if($shouldDeleteItinerary == 1){
                $this->CustomerUserItinerary->unbindModel([
                    'belongsTo' => ['Benefit', 'CustomerUser']
                ]);
                
                $this->CustomerUserItinerary->updateAll(
                    ['CustomerUserItinerary.data_cancel' => 'NOW()'],
                    ['CustomerUserItinerary.customer_user_id' => $userId]
                );

                $this->CustomerUserItinerary->bindModel([
                    'belongsTo' => ['Benefit', 'CustomerUser']
                ]);
            }
        }

        if ($row[23] != '' && $row[24] != '') {
            $address = $this->CustomerUserAddress->find('first', [
                'conditions' => [
                    'customer_user_id' => $userId,
                    'zip_code' => $row[23],
                ]
            ]);

            if (!$address) {
                $addressData = [
                    'customer_id' => $customerId,
                    'customer_user_id' => $userId,
                    'zip_code' => $row[23],
                    'address_line' => $endereco,
                    'address_number' => $row[25],
                    'address_complement' => $row[26],
                    'neighborhood' => $bairro,
                    'city' => $cidade,
                    'state' => $row[29],
                    'address_type_id' => 1
                ];

                $this->CustomerUserAddress->create();
                $this->CustomerUserAddress->save($addressData);
            }
        }

        if ($row[30] != '' && $row[31] != '') {
            $address = $this->CustomerUserAddress->find('first', [
                'conditions' => [
                    'customer_user_id' => $userId,
                    'zip_code' => $row[30],
                ]
            ]);

            if (!$address) {
                $addressData = [
                    'customer_id' => $customerId,
                    'customer_user_id' => $userId,
                    'zip_code' => $row[30],
                    'address_line' => $enderecoTrabalho,
                    'address_number' => $row[32],
                    'address_complement' => $row[33],
                    'neighborhood' => $bairroTrabalho,
                    'city' => $cidadeTrabalho,
                    'state' => $row[36],
                    'address_type_id' => 2
                ];

                $this->CustomerUserAddress->create();
                $this->CustomerUserAddress->save($addressData);
            }
        }

        if ($row[37] != '' && $row[38] != '') {
            $address = $this->CustomerUserAddress->find('first', [
                'conditions' => [
                    'customer_user_id' => $userId,
                    'zip_code' => $row[37],
                ]
            ]);

            if (!$address) {
                $addressData = [
                    'customer_id' => $customerId,
                    'customer_user_id' => $userId,
                    'zip_code' => $row[37],
                    'address_line' => $enderecoEntrega,
                    'address_number' => $row[39],
                    'address_complement' => $row[40],
                    'neighborhood' => $bairroEntrega,
                    'city' => $cidadeEntrega,
                    'state' => $row[43],
                    'address_type_id' => 3
                ];

                $this->CustomerUserAddress->create();
                $this->CustomerUserAddress->save($addressData);
            }
        }

        if ($row[44] != '') {
            $bankCode = $this->BankCode->find('first', [
                'conditions' => [
                    'code' => $row[46]
                ]
            ]);
            $bank_id = !empty($bankCode['BankCode']['id']) ? $bankCode['BankCode']['id'] : null;

            $bankAccount = $this->CustomerUserBankAccount->find('first', [
                'conditions' => [
                    'customer_user_id' => $userId,
                    'bank_code_id' => $bank_id,
                    'acc_number' => $row[49],
                ]
            ]);

            if (!$bankAccount && $bank_id != null) {
                $bankAccountData = [
                    'customer_id' => $customerId,
                    'customer_user_id' => $userId,
                    'account_type_id' => $row[44],
                    'bank_code_id' => $bank_id,
                    'acc_number' => $row[49],
                    'acc_digit' => $row[50],
                    'branch_number' => $row[47],
                    'branch_digit' => $row[48],
                    'pix_type'=> $row[57],
                    'pix_id'=> $row[58],
                ];

                $this->CustomerUserBankAccount->create();
                $this->CustomerUserBankAccount->save($bankAccountData);
            }
        }

        return ['success' => true, 'message' => null, 'userId' => $userId];
    }

    private function processItinerary($row, $userId, $customerId)
    {
        $benefit = $this->Benefit->find('first', [
            'conditions' => [
                'Benefit.code' => $row[13]
            ]
        ]);

        if (!$benefit) {
            return ['success' => false, 'message' => 'Benefício não encontrado.'];
        }

        $itineraryData = [
            'benefit_id' => $benefit['Benefit']['id'],
            'customer_id' => $customerId,
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

    public function parse_working_days_update($tmpFile, $customerId)
    {
        $file = file_get_contents($tmpFile, FILE_IGNORE_NEW_LINES);
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $numLines = substr_count($file, "\n");

        if ($numLines < 2) {
            return ['success' => false, 'error' => 'Arquivo inválido.'];
        }

        $line = 0;

        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                if($line == 0){
                    $line++;
                }
                continue;
            }

            $cpf = preg_replace('/\D/', '', $row[0]);
            $working_days = $row[1];

            $existingUser = $this->CustomerUser->find('first', [
                'conditions' => [
                    "REPLACE(REPLACE(CustomerUser.cpf, '-', ''), '.', '')" => $cpf,
                    'CustomerUser.customer_id' => $customerId,
                ]
            ]);

            if(empty($existingUser)){
                $line++;
                continue;
            }

            $this->CustomerUserItinerary->updateAll(
                ['CustomerUserItinerary.working_days' => $working_days],
                ['CustomerUserItinerary.customer_user_id' => $existingUser['CustomerUser']['id']]
            );

            $line++;
        }
    }

    private function getOrCreateCustomerDepartment($departmentName, $customerId)
    {
        $customerDepartment = $this->CustomerDepartment->find('first', [
            'conditions' => [
                'name' => $departmentName,
                'customer_id' => $customerId
            ]
        ]);

        if (!$customerDepartment) {
            $this->CustomerDepartment->create();
            $this->CustomerDepartment->save([
                'name' => $departmentName,
                'customer_id' => $customerId
            ]);
            $customerDepartment = $this->CustomerDepartment->find('first', ['conditions' => ['CustomerDepartment.id' => $this->CustomerDepartment->id]]);
        }

        return $customerDepartment;
    }

    private function getMaritalStatusId($status)
    {
        $maritalStatus = $this->MaritalStatus->find('first', [
            'conditions' => [
                'status' => $status,
            ]
        ]);

        return $maritalStatus ? $maritalStatus['MaritalStatus']['id'] : null;
    }

    private function getSalaryRangeId($range)
    {
        $salaryRange = $this->SalaryRange->find('first', [
            'conditions' => [
                'range' => $range
            ]
        ]);

        return $salaryRange ? $salaryRange['SalaryRange']['id'] : null;
    }

    private function getCustomerPositionId($positionName, $customerId)
    {
        if ($positionName == '') {
            return null;
        }

        $position = $this->CustomerPosition->find('first', [
            'conditions' => [
                'name' => $positionName,
                'customer_id' => $customerId
            ]
        ]);

        if (!$position) {
            $this->CustomerPosition->create();
            $this->CustomerPosition->save([
                'name' => $positionName,
                'customer_id' => $customerId
            ]);

            $position = $this->CustomerPosition->find('first', ['conditions' => ['CustomerPosition.id' => $this->CustomerPosition->id]]);
        }

        return $position['CustomerPosition']['id'];
    }
}
