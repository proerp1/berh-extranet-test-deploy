<?php 

App::uses('AuthComponent', 'Controller/Component');
class ApontamentoMeProteja extends AppModel {
  public $name = 'ApontamentoMeProteja';
  public $useTable = 'apontamentoMeProteja';
  public $primaryKey = 'apontamentoMeProtejaID';

  public $belongsTo = array(
    'Customer' => array(
      'foreignKey' => 'clienteID'
    ),
    'SocioMeProteja' => array(
      'foreignKey' => 'socioMeProtejaID'
    )
  );

  public function beforeFind($queryData) {

    $queryData['conditions'][] = array('ApontamentoMeProteja.apontamentoMeProtejaDataCancel' => '1901-01-01 00:00:00');
    
    return $queryData;
  }

  public function cron_clientes_nenhuma_restricao(){
    $sql = "SELECT 1                                                AS tipo,
                   cr1.cronMeProtejaID                              AS id,
                   c1.nome_primario                                 AS nome,
                   group_concat(c1.email)                           AS email,
                   c1.tipo_pessoa                                   AS tipo_documento,
                   ''                                               as celular
            FROM cronMeProteja cr1
                     INNER JOIN customers c1 ON c1.id = cr1.clienteID
            WHERE cr1.cronMeProtejaDataCancel = '1901-01-01'
              AND c1.data_cancel = '1901-01-01'
              AND (DATE_FORMAT(cr1.cronMeProtejaApontamento, '%Y-%m-%d') <= DATE_SUB(CURDATE(), INTERVAL 7 DAY) OR
                   cr1.cronMeProtejaApontamento IS null)
              AND cr1.cronMeProtejaValidade > NOW()
            GROUP BY cr1.clienteID
            UNION
            SELECT 2                         AS tipo,
                   sc1.socioMeProtejaID      AS id,
                   sc1.socioMeProtejaNome    AS nome,
                   sc1.socioMeProtejaEmail   AS email,
                   sc1.socioMeProtejaTipoDoc AS tipo_documento,
                   sc1.socioMeProtejaCelular
            FROM sociosMeProteja sc1
            WHERE sc1.socioMeProtejaDataCancel = '1901-01-01'
              AND (DATE_FORMAT(sc1.socioMeProtejaApontamento, '%Y-%m-%d') <= DATE_SUB(CURDATE(), INTERVAL 7 DAY) OR
                   sc1.socioMeProtejaApontamento IS null)";

    return $this->query($sql);
  }

  public function update_apontamento_socios($socioMeProtejaID){
    $sql = "UPDATE sociosMeProteja s SET s.socioMeProtejaApontamento = NOW() WHERE s.socioMeProtejaID = ".$socioMeProtejaID." ";

    $this->query($sql);
  }

  public function update_apontamento_cron($cronMeProtejaID){
    $sql = "UPDATE cronMeProteja c SET c.cronMeProtejaApontamento = NOW() WHERE c.cronMeProtejaID = ".$cronMeProtejaID." ";
    
    $this->query($sql);
  }

}