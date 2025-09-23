<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link <?php echo $tipo == null ? 'active' : '' ?>" href="<?php echo $this->base.'/reports/extrato/'; ?>">Movimentação</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $tipo == 'grupo_economico' ? 'active' : '' ?>" href="<?php echo $this->base.'/reports/extrato/grupo_economico'; ?>">Movimentação por Grupo Economico</a>
    </li>
</ul>