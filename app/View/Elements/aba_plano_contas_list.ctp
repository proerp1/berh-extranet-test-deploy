<?php
    if ($nivel == 1) {
        $aba0 = "Lista Nível";
        $aba1 = "Primeiro Nível";
        $aba2 = "Segundo Nível";
        $urlA = $this->base."/plano_contas/index1/1/";
    } elseif ($nivel == 2) {
        $aba0 = "Primeiro Nível";
        $aba1 = "Segundo Nível";
        $aba2 = "Terceiro Nível";
        $urlA = $this->base."/plano_contas/edit/".$pai_id;
    } elseif ($nivel == 3) {
        $aba0 = "Segundo Nível";
        $aba1 = "Terceiro Nível";
        $aba2 = "Quarto Nível";
        $urlA = $this->base."/plano_contas/edit/".$pai_id;
    } elseif ($nivel == 4) {
        $aba0 = "Terceiro Nível";
        $aba1 = "Quarto Nível";
        $aba2 = "Quinto Nível";
        $urlA = $this->base."/plano_contas/edit/".$pai_id;
    } elseif ($nivel == 5) {
        $aba0 = "Quarto Nível";
        $aba1 = "Quinto Nível";
        $aba2 = "";
        $urlA = $this->base."/plano_contas/edit/".$pai_id;
    }
?>

<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $urlA;?>"><?php echo $aba0;?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="#"><?php echo $aba1;?></a>
    </li>
</ul>