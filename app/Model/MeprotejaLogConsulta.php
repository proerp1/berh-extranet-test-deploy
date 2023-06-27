<?php 
class MeprotejaLogConsulta extends AppModel {
    public $useTable = 'meproteja_consultas';

    public $belongsTo = ['Customer'];
}