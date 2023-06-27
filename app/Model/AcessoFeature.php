<?php
class AcessoFeature extends AppModel
{
    public $useTable = "acessosFeature";
    public $name = 'AcessoFeature';

    public $belongsTo = [
        'Plan',
        'Customer',
        'Feature' => [
            'className' => 'Feature',
            'foreignKey' => 'feature_id'
        ],
        'Acesso' => [
            'className' => 'Acesso',
            'foreignKey' => 'access_id'
        ],
        'CustomerUser' => [
            'className' => 'CustomerUser',
            'foreignKey' => 'customer_user_id'
        ]
    ];

    public function find_acesso_feature($id)
    {
        $sql = "SELECT * FROM acessosFeature af LEFT JOIN clientes c ON c.id = af.customer_id WHERE c.id = ".$id." AND af.customer_user_id IS NULL";
               

        $exSql = $this->query($sql);

        return $exSql;
    }

    public function find_feature_permitidas_por_cliente($produtoID, $clienteID)
    {
        $sql = "SELECT f.id, f.name, f.destaque, f.descricao, f.valor
                FROM acessosFeature a
                INNER JOIN features f ON f.id = a.feature_id
                WHERE 
                 a.customer_id = $clienteID AND a.customer_user_id IS NULL AND
                 f.product_id = $produtoID AND f.status_id = 1 AND f.data_cancel = '1901-01-01'
                group by f.id ";

        $exSql = $this->query($sql);

        return $exSql;
    }

    public function find_feature_permitidas_por_cliente_api($produtoID, $clienteID)
    {
        $sql = "SELECT Feature.id, Feature.name
                FROM acessosFeature a
                INNER JOIN features Feature ON Feature.id = a.feature_id
                WHERE 
                 a.customer_id = $clienteID AND a.customer_user_id IS NULL AND
                 Feature.product_id = $produtoID AND Feature.status_id = 1 AND Feature.data_cancel = '1901-01-01'
                group by Feature.id ";

        $exSql = $this->query($sql);

        return $exSql;
    }

    public function find_id($id, $vendedorID)
    {
        $sql = "SELECT f.id, f.name, f.destaque, f.descricao, f.valor
                        FROM acessosFeature a
                        INNER JOIN features f ON f.id = a.feature_id
                        WHERE f.id in ($id) and a.vendedor_id = $vendedorID";

        $exSql = $this->query($sql);

        return $exSql;
    }

    public function find_qtde_consulta_vendedor($vendedorID, $usuarioID, $featureID)
    {
        $sql = "SELECT v.vendedorID, v.featureID, IF(ve.vendedorConsultaFeaturesExcedenteData > NOW(), (IFNULL(v.vendedorConsultaFeaturesQtde, 0) + ve.vendedorConsultaFeaturesExcedenteQtde), v.vendedorConsultaFeaturesQtde) AS qtdeConsultasVendedor,(
                            SELECT COUNT(l.logConsultaID)
                                FROM logConsultas l
                                    INNER JOIN logConsultasFeature lf ON lf.logConsultaID = l.logConsultaID
                                WHERE l.logConsultasDataCancel = '1901-01-01' AND lf.logConsultasFeatureDataCancel = '1901-01-01' AND l.usuarioID = ".$usuarioID." AND lf.featuresID = v.featureID AND DATE_FORMAT(l.logConsultasData,'%m-%Y') = DATE_FORMAT(NOW(),'%m-%Y')) AS qtdeConsultado
                        FROM vendedorConsultaFeatures v
                            LEFT JOIN vendedorConsultaFeaturesExcedente ve ON ve.vendedorID = v.vendedorID AND ve.featureID = v.featureID AND ve.vendedorConsultaFeaturesExcedenteDataCancel = '1901-01-01'
                        WHERE v.vendedorConsultaFeaturesDataCancel = '1901-01-01' AND v.vendedorID = ".$vendedorID." AND v.featureID = ".$featureID."
                            GROUP BY v.vendedorConsultaFeaturesID";
        $exSql = $this->query($sql);

        if ($exSql) {
            return $exSql;
        } else {
            return false;
        }
    }
}
