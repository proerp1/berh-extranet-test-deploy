<?php

use League\Csv\Reader;

class EconomicGroupCSVParser extends Controller
{
    public $uses = [
        'CustomerUser', 'CustomerUsersEconomicGroup', 'EconomicGroup'
    ];

    public function parse($tmpFile, $customerId)
    {
        $file = file_get_contents($tmpFile, FILE_IGNORE_NEW_LINES);
        $csv = Reader::createFromString($file);
        $csv->setDelimiter(';');

        $line = 0;

        foreach ($csv->getRecords() as $row) {
            if ($line == 0 || empty($row[0])) {
                if($line == 0){
                    $line++;
                }
                continue;
            }

            $cpf = preg_replace('/\D/', '', $row[0]);
            $cnpj = trim($row[1]);

            $existingUser = $this->CustomerUser->find('first', [
                'conditions' => [
                    "REPLACE(REPLACE(CustomerUser.cpf, '-', ''), '.', '')" => $cpf,
                    'CustomerUser.customer_id' => $customerId,
                ],
                'recursive' => -1
            ]);

            $existingEconomicGroup = $this->EconomicGroup->find('first', [
                'conditions' => [
                    "EconomicGroup.document" => $cnpj,
                    'EconomicGroup.customer_id' => $customerId,
                ],
                'recursive' => -1
            ]);

            if(empty($existingUser) || empty($existingEconomicGroup)){
                $line++;
                continue;
            }

            $this->CustomerUser->save([
                'CustomerUser' => [
                    'id' => $existingUser['CustomerUser']['id']
                ],
                'EconomicGroup' => [
                    'EconomicGroup' => [
                        $existingEconomicGroup['EconomicGroup']['id']
                    ]
                ]
            ]);

            $line++;
        }
    }
}
