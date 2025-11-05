<?php
    parse_str($_SERVER['QUERY_STRING'], $params);
    unset($params['aba']);    
    $newQueryString = http_build_query($params);
?>

<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'liberacao_credito') ? 'active' : '' ?>" href="<?php echo $this->base.'/reports/compras?aba=liberacao_credito&'.(isset($newQueryString) ? $newQueryString : ''); ?>">Aguardando liberação de crédito</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'cartao_novo') ? 'active' : '' ?>" href="<?php echo $this->base.'/reports/compras?aba=cartao_novo&'.(isset($newQueryString) ? $newQueryString : ''); ?>">Cartão novo</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'inconsistencias') ? 'active' : '' ?>" href="<?php echo $this->base.'/reports/compras?aba=inconsistencias&'.(isset($newQueryString) ? $newQueryString : ''); ?>">Inconsistências</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'papel') ? 'active' : '' ?>" href="<?php echo $this->base.'/reports/compras?aba=papel&'.(isset($newQueryString) ? $newQueryString : ''); ?>">Papel</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'financeiro') ? 'active' : '' ?>" href="<?php echo $this->base.'/reports/compras?aba=financeiro&'.(isset($newQueryString) ? $newQueryString : ''); ?>">Financeiro</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'finalizado') ? 'active' : '' ?>" href="<?php echo $this->base.'/reports/compras?aba=finalizado&'.(isset($newQueryString) ? $newQueryString : ''); ?>">Pago</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'todos') ? 'active' : '' ?>" href="<?php echo $this->base.'/reports/compras?aba=todos&'.(isset($newQueryString) ? $newQueryString : ''); ?>">Todos</a>
    </li>
</ul>
