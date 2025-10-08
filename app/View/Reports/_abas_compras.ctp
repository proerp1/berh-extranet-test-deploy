
<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <?php /* ?>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'todos') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=todos">Todos</a>
    </li>
    <?php */ ?>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'liberacao_credito') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=liberacao_credito">Aguardando liberação de crédito</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'cartao_novo') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=cartao_novo">Cartão novo</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'inconsistencias') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=inconsistencias">Inconsistências</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'financeiro') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=financeiro">Financeiro</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($aba) and $aba == 'finalizado') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=finalizado">Finalizado</a>
    </li>
</ul>
