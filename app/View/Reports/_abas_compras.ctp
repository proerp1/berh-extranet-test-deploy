
<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['aba']) and $_GET['aba'] == 'todos') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=todos">Todos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['aba']) and $_GET['aba'] == 'liberacao_credito') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=liberacao_credito">Aguardando liberação de crédito</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['aba']) and $_GET['aba'] == 'cartao_novo') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=cartao_novo">Cartão novo</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['aba']) and $_GET['aba'] == 'inconsistencias') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=inconsistencias">Inconsistências</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['aba']) and $_GET['aba'] == 'financeiro') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=financeiro">Financeiro</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo (isset($_GET['aba']) and $_GET['aba'] == 'finalizado') ? 'active' : '' ?>" href="<?php echo $this->base; ?>/reports/compras?aba=finalizado">Finalizado</a>
    </li>
</ul>
