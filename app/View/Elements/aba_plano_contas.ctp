<?php 
        if ($nivel == 1){
                $aba0 = "Lista Nível";
                $aba1 = "Primeiro Nível";
                $aba2 = "Segundo Nível";
                $urlA = $this->base."/plano_contas/index1/1/";
                $urlP = $this->base."/plano_contas/index2/2/".$id;
        } else if ($nivel == 2){
                $aba0 = "Primeiro Nível";
                $aba1 = "Segundo Nível";
                $aba2 = "Terceiro Nível";
                $urlA = $this->base."/plano_contas/edit/".$pai_id;
                $urlP = $this->base."/plano_contas/index2/3/".$id;
        } else if ($nivel == 3){
                $aba0 = "Segundo Nível";
                $aba1 = "Terceiro Nível";
                $aba2 = "Quarto Nível";
                $urlA = $this->base."/plano_contas/edit/".$pai_id;
                $urlP = $this->base."/plano_contas/index2/4/".$id;
        } else if ($nivel == 4){
                $aba0 = "Terceiro Nível";
                $aba1 = "Quarto Nível";
                $aba2 = "Quinto Nível";
                $urlA = $this->base."/plano_contas/edit/".$pai_id;
                $urlP = $this->base."/plano_contas/index2/5/".$id;
        } else if ($nivel == 5){
                $aba0 = "Quarto Nível";
                $aba1 = "Quinto Nível";
                $aba2 = "";
                $urlA = $this->base."/plano_contas/edit/".$pai_id;
                $urlP = $this->base."/plano_contas/index2/5/".$id;
        }
?>

<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $urlA;?>"><?php echo $aba0;?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="#"><?php echo $aba1;?></a>
    </li>
    <?php if($aba2 != ""){?>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $urlP;?>"><?php echo $aba2;?></a>
        </li>
    <?php } ?>
</ul>