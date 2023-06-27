<?php 
class NovaVidaLogConsultaItem extends AppModel {
    public $name = 'NovaVidaLogConsultaItem';
    public $useTable = 'nova_vida_log_consulta_itens';

    public function beforeFind($queryData) {

        $queryData['conditions'][] = array('NovaVidaLogConsultaItem.data_cancel' => '1901-01-01 00:00:00');
        
        return $queryData;
    }
}