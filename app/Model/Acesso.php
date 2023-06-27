<?php
class Acesso extends AppModel
{
    public $name = 'Acesso';

    public $belongsTo = [
        'Customer',
        'Plan',
        'Product'
    ];


    public function beforeFind($queryData)
    {
        $queryData['conditions'][] = ['Acesso.data_cancel' => '1901-01-01 00:00:00'];
        
        return $queryData;
    }

    public function dateFormatBeforeSave($date)
    {
        $date = date('Y-m-d', strtotime($date));

        return $date;
    }

    public function find_acessos_cliente_by_tipo($clienteID, $tipo)
    {
        $sql = "SELECT *
                        FROM acessos a
                        LEFT JOIN produtos p on p.id = a.product_id
                        WHERE a.customer_id = ".$clienteID." AND p.tipo = 2 AND p.id != 25 AND (p.tipo_pessoa = 0 or p.tipo_pessoa = ".$tipo.") AND a.data_cancel = '1901-01-01' AND p.status_id = 1
                        group by p.id
                        order by p.valor_produto asc";

        $res = $this->query($sql);

        return $res;
    }

    public function find_acessos_revenda_by_tipo($revendaID, $vendedorID, $tipo)
    {
        $sql = "SELECT *
                        FROM acessos a
                        LEFT JOIN produtos p on p.id = a.product_id
                        WHERE a.revenda_id = ".$revendaID." AND a.vendedor_id = ".$vendedorID." AND p.tipo = 2 AND p.id != 25 AND (p.tipo_pessoa = 0 or p.tipo_pessoa = ".$tipo.") AND a.data_cancel = '1901-01-01' AND p.status_id = 1
                        order by p.valor_produto asc";

        $res = $this->query($sql);

        return $res;
    }

    public function find_acesso_by_produto($produtoID, $where)
    {
        $sql = "SELECT * from acessos a where $where and a.product_id = $produtoID and a.data_cancel = '1901-01-01' ";

        $rsSql = $this->query($sql);

        return $rsSql;
    }

    public function find_sub_usuario_acesso_by_produto($produtoID, $clienteID, $usuarioID)
    {
        $sql = "SELECT *
                FROM permissoescliente p
                WHERE p.clienteID = $clienteID and p.usuarioclienteID = $usuarioID AND p.produtoID = $produtoID and p.permissoesclienteDataCancel = '1901-01-01' ";

        $rsSql = $this->query($sql);

        return $rsSql;
    }
}
