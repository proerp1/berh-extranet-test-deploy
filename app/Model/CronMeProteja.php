<?php
App::uses('AuthComponent', 'Controller/Component');
class CronMeProteja extends AppModel
{
    public $name = 'CronMeProteja';
    public $useTable = 'cronMeProteja';
    public $primaryKey = 'cronMeProtejaID';

    public $belongsTo = [
        'Customer' => [
            'className' => 'Customer',
            'foreignKey' => 'clienteID'
        ]
    ];

    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['CronMeProteja.cronMeProtejaDataCancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function find_cliente_validade($clienteID)
    {
        $dql = "SELECT c.cronMeProtejaID, c.cronMeProtejaValidade, DATEDIFF(c.cronMeProtejaValidade, NOW()) AS dias, IF(c.cronMeProtejaValidade > NOW(), 1, 0) AS status
                FROM cronMeProteja c
                WHERE c.cronMeProtejaDataCancel = '1901-01-01' AND c.clienteID = ".$clienteID."";
        
        $result = $this->query($dql);
    
        return $result;
    }

    public function find_cron_clientes_validade()
    {
        $sql = "SELECT c.cronMeProtejaID, c.cronMeProtejaValidade, c.clienteID, DATEDIFF(c.cronMeProtejaValidade, NOW()) AS dias, IF(c.cronMeProtejaValidade > NOW(), 1, 0) AS status
                FROM cronMeProteja c                
                    INNER JOIN customers cl ON cl.id = c.clienteID AND cl.status_id IN (3,4) AND cl.data_cancel = '1901-01-01'
                WHERE c.cronMeProtejaDataCancel = '1901-01-01' AND c.cronMeProtejaValidade < NOW() 
                
                ";

        return $this->query($sql);
    }

    public function find_clientes_cron_expiracao()
    {
        $sql = "SELECT *
                FROM cronMeProteja cr
                       INNER JOIN customers c ON c.id = cr.clienteID
                WHERE cr.cronMeProtejaDataCancel = '1901-01-01'
                  AND c.data_cancel = '1901-01-01'
                  AND DATE_FORMAT(cr.cronMeProtejaValidade, '%Y-%m-%d') <= CURDATE()";
        
        return $this->query($sql);
    }

    public function find_clientes_socios_expiracao()
    {
        $sql = "SELECT *
                FROM customers c
                       INNER JOIN cronMeProteja cr ON cr.clienteID = c.id
                       INNER JOIN sociosMeProteja s on s.clienteID = c.id
                WHERE c.data_cancel = '1901-01-01'
                  AND cr.cronMeProtejaDataCancel = '1901-01-01'
                  AND s.socioMeProtejaDataCancel = '1901-01-01'
                  AND DATE_FORMAT(cr.cronMeProtejaValidade, '%Y-%m-%d') <= CURDATE()";
        
        return $this->query($sql);
    }

    public function find_cron_socios_cliente($clienteID)
    {
        $sql = "SELECT *
                FROM customers c
                    INNER JOIN cronMeProteja cr ON cr.clienteID = c.id
                    INNER JOIN sociosMeProteja s on s.clienteID = c.id
                WHERE c.data_cancel = '1901-01-01' 
                    AND cr.cronMeProtejaDataCancel = '1901-01-01' 
                    AND s.socioMeProtejaDataCancel = '1901-01-01' 
                    AND c.id = ".$clienteID;

        return $this->query($sql);
    }

    public function find_cron_cliente($clienteID)
    {
        $sql = "SELECT *
                FROM customers c
                    INNER JOIN cronMeProteja cr ON cr.clienteID = c.id
                WHERE c.data_cancel = '1901-01-01' 
                    -- AND cr.cronMeProtejaDataCancel = '1901-01-01' 
                    AND c.id = ".$clienteID;

        return $this->query($sql);
    }

    public function update_cancel_cron($id, $idCancel = 99999)
    {
        $sql = "UPDATE cronMeProteja 
                SET usuarioIDCancel = {$idCancel}, cronMeProtejaDataCancel = '".date("Y-m-d H:i:s")."'
                WHERE cronMeProtejaID = {$id}";
        
        $this->query($sql);
    }
}
