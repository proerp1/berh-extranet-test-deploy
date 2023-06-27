<?php
class LerConsumoDiarioComponent extends Component
{
    public function ler($id, $arquivo)
    {
        $ConsumoDiarioItem = ClassRegistry::init('ConsumoDiarioItem');
        $LoginConsulta = ClassRegistry::init('LoginConsulta');

        $handle = fopen($arquivo, "r");

        fgetcsv($handle); // pula primeira linha
        $items = [];
        while ($data = fgetcsv($handle, 1000, ";", "'")) {
            $login = $LoginConsulta->find('first', ['conditions' => ["LoginConsulta.login" => $data[2], "LoginConsulta.data_cancel" => '1901-01-01'], 'recursive' => 2]);

            $items[] = [
                'consumo_diario_id' => $id,
                'customer_id' => $login['LoginConsulta']['customer_id'],
                'data' => $this->dateFormatBeforeSave($data[0]),
                'hora' => $data[1],
                'logon' => $data[2],
                'usuario' => $data[3],
                'documento' => $data[4],
                'produto' => $data[7],
            ];
        }

        $ConsumoDiarioItem->saveMany($items);
    }

    public function dateFormatBeforeSave($dateString)
    {
        return date('Y-m-d', strtotime($this->date_converter($dateString)));
    }

    public function date_converter($_date = null)
    {
        $format = '/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/';
        if ($_date != null && preg_match($format, $_date, $partes)) {
            return $partes[3].'-'.$partes[2].'-'.$partes[1];
        }
        
        return false;
    }
}
