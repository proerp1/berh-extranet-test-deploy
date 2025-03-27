<div class="tracking-wrapper">
    <div class="tracking">
        <div id="progress" class="progress-<?php echo $progress; ?>">
            <div class="empty-bar"></div>
            <div class="color-bar"></div>
            <ul>
                <li class="bullet-1">
                    <div class="el"><i class='fa fa-cog'></i></div>
                    <div class="txt">Início <br><br><?php echo $this->request->data['Order']['created']; ?></div>
                </li>
                <li class="bullet-2">
                    <div class="el"><i class='fa fa-list'></i></div>
                    <div class="txt">Aguardando Pagamento <br><br><?php echo $this->request->data['Order']['validation_date']; ?></div>
                </li>
                <li class="bullet-3">
                    <div class="el"><i class='fa fa-credit-card'></i></div>
                    <div class="txt">Pagamento Confirmado <br><br><?php echo $this->request->data['Order']['issuing_date']; ?></div>
                </li>
                <li class="bullet-4">
                    <div class="el"><i class='fa fa-check'></i></div>
                    <div class="txt">Em Processamento <br><br><?php echo $this->request->data['Order']['payment_date']; ?></div>
                </li>
                <li class="bullet-5">
                    <div class="el"><i class='fa fa-check'></i></div>
                    <div class="txt">Finalizado</div>
                </li>
            </ul>
        </div>

        <div class="arrow-button left">
    <?php if (!empty($prev_order['Order']['id'])): ?>
        <a href="<?php echo $this->base . '/orders/edit/' . $prev_order['Order']['id']; ?>" class="arrow-link">
            <i class="fa fa-arrow-left"></i>
        </a>
    <?php endif; ?>
</div>

<div class="arrow-button right">
    <?php if (!empty($next_order['Order']['id'])): ?>
        <a href="<?php echo $this->base . '/orders/edit/' . $next_order['Order']['id']; ?>" class="arrow-link">
            <i class="fa fa-arrow-right"></i>
        </a>
    <?php endif; ?>
</div>


    </div>
</div>

<style>
    /* vars */
    :root {
        --back: #eeeeee;
        --blue: #0082d2;
        --green: #33DDAA;
        --gray: #777777;
        --size: 400px;
    }

    .tracking-wrapper {
        margin: 30px;
        padding: 0;
    }

    .tracking * {
        padding: 0;
        margin: 0;
    }

    .tracking {
        width: var(--size);
        max-width: 100%;
        position: relative;
        margin: 0 auto;
    }

    .tracking .empty-bar {
        background: #ddd;
        position: absolute;
        width: 90%;
        height: 20%;
        top: 40%;
        margin-left: 5%;
    }

    .tracking .color-bar {
        background: var(--blue);
        position: absolute;
        height: 20%;
        top: 40%;
        margin-left: 5%;
        transition: all 0.5s;
        -webkit-transition: all 0.5s;
        -moz-transition: all 0.5s;
        -ms-transition: all 0.5s;
        -o-transition: all 0.5s;
    }

    .tracking ul {
        display: flex;
        justify-content: space-between;
        list-style: none;
    }

    .tracking ul>li {
        background: #ddd;
        text-align: center;
        border-radius: 50%;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        -ms-border-radius: 50%;
        -o-border-radius: 50%;
        z-index: 1;
        background-size: 70%;
        background-repeat: no-repeat;
        background-position: center center;
        transition: all 0.5s;
        -webkit-transition: all 0.5s;
        -moz-transition: all 0.5s;
        -ms-transition: all 0.5s;
        -o-transition: all 0.5s;
        display: inline-block;
        position: relative;
        width: 10%;
    }

    .tracking ul>li .el {
        position: relative;
        margin-top: 100%;
    }

    .tracking ul>li .el i {
        position: absolute;
        bottom: 100%;
        left: 8%;
        margin: 12px 10px;
        color: #fff;
        font-size: 100%;
        display: none;
    }

    .tracking ul>li .txt {
        color: #999;
        position: absolute;
        top: 120%;
        left: -75%;
        text-align: center;
        width: 250%;
        font-size: .75rem;
    }

    .arrow-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: transparent; /* Remove o fundo */
    color: var(--blue); /* Cor da seta */
    font-size: 10px; /* Tamanho da seta */
    padding: 3px; /* Ajuste para a área clicável */
    cursor: pointer;
    z-index: 2;
    transition: background-color 0.3s, transform 0.2s ease-in-out;
}

.arrow-button i {
    display: block;
    font-size: 15px;
    color: var(--blue); /* Cor da seta */
}

.arrow-button.left {
    left: -200px;
}

.arrow-button.right {
    right: -200px;
}

.arrow-button:hover {
    background-color: #fff; /* Cor de fundo ao passar o mouse */
    transform: translateY(-50%) rotate(360deg); /* Efeito de rotação */
}

.arrow-button:hover i {
    color: #ED0677; /* Cor da seta no hover */
}

    .tracking .progress-0 .color-bar {
        width: 00%;
    }

    .tracking .progress-1 .color-bar {
        width: 12%;
    }

    .tracking .progress-2 .color-bar {
        width: 34%;
    }

    .tracking .progress-3 .color-bar {
        width: 34%;
    }

    .tracking .progress-4 .color-bar {
        width: 56%;
    }

    .tracking .progress-5 .color-bar {
        width: 56%;
    }

    .tracking .progress-6 .color-bar {
        width: 80%;
    }

    .tracking .progress-7 .color-bar {
        width: 80%;
    }

    .tracking .progress-8 .color-bar {
        width: 90%;
    }

    .tracking .progress-9 .color-bar {
        width: 90%;
    }

    .tracking .progress-0>ul>li.bullet-1,
    .tracking .progress-1>ul>li.bullet-1,
    .tracking .progress-2>ul>li.bullet-1,
    .tracking .progress-3>ul>li.bullet-1,
    .tracking .progress-4>ul>li.bullet-1,
    .tracking .progress-5>ul>li.bullet-1,
    .tracking .progress-6>ul>li.bullet-1,
    .tracking .progress-7>ul>li.bullet-1,
    .tracking .progress-8>ul>li.bullet-1,
    .tracking .progress-9>ul>li.bullet-1 {
        background-color: var(--blue);
    }

    .tracking .progress-2>ul>li.bullet-2,
    .tracking .progress-3>ul>li.bullet-2,
    .tracking .progress-4>ul>li.bullet-2,
    .tracking .progress-5>ul>li.bullet-2,
    .tracking .progress-6>ul>li.bullet-2,
    .tracking .progress-7>ul>li.bullet-2,
    .tracking .progress-8>ul>li.bullet-2,
    .tracking .progress-9>ul>li.bullet-2 {
        background-color: var(--blue);
    }

    .tracking .progress-4>ul>li.bullet-3,
    .tracking .progress-5>ul>li.bullet-3,
    .tracking .progress-6>ul>li.bullet-3,
    .tracking .progress-7>ul>li.bullet-3,
    .tracking .progress-8>ul>li.bullet-3,
    .tracking .progress-9>ul>li.bullet-3 {
        background-color: var(--blue);
    }

    .tracking .progress-6>ul>li.bullet-4,
    .tracking .progress-7>ul>li.bullet-4,
    .tracking .progress-8>ul>li.bullet-4,
    .tracking .progress-9>ul>li.bullet-4 {
        background-color: var(--blue);
    }

    .tracking .progress-8>ul>li.bullet-5,
    .tracking .progress-9>ul>li.bullet-5 {
        background-color: var(--blue);
    }

    .tracking .progress-9>ul>li.bullet-5 {
        background-color: var(--green);
    }

    .tracking .progress-1>ul>li.bullet-1 .el i,
    .tracking .progress-2>ul>li.bullet-1 .el i,
    .tracking .progress-3>ul>li.bullet-1 .el i,
    .tracking .progress-4>ul>li.bullet-1 .el i,
    .tracking .progress-5>ul>li.bullet-1 .el i,
    .tracking .progress-6>ul>li.bullet-1 .el i,
    .tracking .progress-7>ul>li.bullet-1 .el i,
    .tracking .progress-8>ul>li.bullet-1 .el i,
    .tracking .progress-9>ul>li.bullet-1 .el i {
        display: block;
    }

    .tracking .progress-3>ul>li.bullet-2 .el i,
    .tracking .progress-4>ul>li.bullet-2 .el i,
    .tracking .progress-5>ul>li.bullet-2 .el i,
    .tracking .progress-6>ul>li.bullet-2 .el i,
    .tracking .progress-7>ul>li.bullet-2 .el i,
    .tracking .progress-8>ul>li.bullet-2 .el i,
    .tracking .progress-9>ul>li.bullet-2 .el i {
        display: block;
    }

    .tracking .progress-5>ul>li.bullet-3 .el i,
    .tracking .progress-6>ul>li.bullet-3 .el i,
    .tracking .progress-7>ul>li.bullet-3 .el i,
    .tracking .progress-8>ul>li.bullet-3 .el i,
    .tracking .progress-9>ul>li.bullet-3 .el i {
        display: block;
    }

    .tracking .progress-7>ul>li.bullet-4 .el i,
    .tracking .progress-8>ul>li.bullet-4 .el i,
    .tracking .progress-9>ul>li.bullet-4 .el i {
        display: block;
    }

    .tracking .progress-9>ul>li.bullet-5 .el i {
        display: block;
    }

    .tracking .progress-1>ul>li.bullet-1 .txt,
    .tracking .progress-2>ul>li.bullet-1 .txt,
    .tracking .progress-3>ul>li.bullet-1 .txt,
    .tracking .progress-4>ul>li.bullet-1 .txt,
    .tracking .progress-5>ul>li.bullet-1 .txt,
    .tracking .progress-6>ul>li.bullet-1 .txt,
    .tracking .progress-7>ul>li.bullet-1 .txt,
    .tracking .progress-8>ul>li.bullet-1 .txt,
    .tracking .progress-9>ul>li.bullet-1 .txt {
        color: var(--blue);
    }

    .tracking .progress-3>ul>li.bullet-2 .txt,
    .tracking .progress-4>ul>li.bullet-2 .txt,
    .tracking .progress-5>ul>li.bullet-2 .txt,
    .tracking .progress-6>ul>li.bullet-2 .txt,
    .tracking .progress-7>ul>li.bullet-2 .txt,
    .tracking .progress-8>ul>li.bullet-2 .txt,
    .tracking .progress-9>ul>li.bullet-2 .txt {
        color: var(--blue);
    }

    .tracking .progress-5>ul>li.bullet-3 .txt,
    .tracking .progress-6>ul>li.bullet-3 .txt,
    .tracking .progress-7>ul>li.bullet-3 .txt,
    .tracking .progress-8>ul>li.bullet-3 .txt,
    .tracking .progress-9>ul>li.bullet-3 .txt {
        color: var(--blue);
    }

    .tracking .progress-7>ul>li.bullet-4 .txt,
    .tracking .progress-8>ul>li.bullet-4 .txt,
    .tracking .progress-9>ul>li.bullet-4 .txt {
        color: var(--blue);
    }

    .tracking .progress-9>ul>li.bullet-5 .txt {
        color: var(--blue);
    }
</style>