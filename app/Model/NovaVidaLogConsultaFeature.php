<?php
class NovaVidaLogConsultaFeature extends AppModel
{
    public $name = 'NovaVidaLogConsultaFeature';

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['NovaVidaLogConsultaFeature.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }
}
