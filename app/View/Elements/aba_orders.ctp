<div class="tracking-wrapper">
    <div class="tracking">
        <div id="progress" class="progress-<?php echo $progress; ?>">
            <div class="empty-bar"></div>
            <div class="color-bar"></div>
            <ul>
                <li class="bullet-1">
                    <div class="el"><i><img src="<?php echo $this->base."/img/icones/inicio.ico" ?>" alt="Início" style="width: 15px; height: 15px;"></i></div>
                    <div class="txt">Início <br><br><?php echo $this->request->data['Order']['created']; ?></div>
                </li>
                <li class="bullet-2">
                    <div class="el"><i><img src="<?php echo $this->base."/img/icones/aguardando_pagamento.ico" ?>" alt="Aguardando Pagamento" style="width: 15px; height: 15px;"></i></div>
                    <div class="txt">Aguardando <br>Pagamento <br><br><?php echo $this->request->data['Order']['validation_date']; ?></div>
                </li>
                <li class="bullet-3">
                    <div class="el"><i><img src="<?php echo $this->base."/img/icones/pagamento_confirmado.ico" ?>" alt="Pagamento Confirmado" style="width: 15px; height: 15px;"></i></div>
                    <div class="txt">Pagamento <br>Confirmado <br><br><?php echo $this->request->data['Order']['payment_date']; ?></div>
                </li>
                <li class="bullet-4">
                    <div class="el"><i><img src="<?php echo $this->base."/img/icones/em_processamento.ico" ?>" alt="Em Processamento" style="width: 15px; height: 15px;"></i></div>
                    <div class="txt">Em <br>Processamento <br><br><?php echo $this->request->data['Order']['issuing_date']; ?></div>
                </li>
                <li class="bullet-5">
                    <div class="el"><i><img src="<?php echo $this->base."/img/icones/aguardando_liberacao.ico" ?>" alt="Liberação Créditos" style="width: 15px; height: 15px;"></i></div>
                    <div class="txt">Aguardando <br>Liberação <br>de Crédito</div>
                </li>
                <?php if ($this->request->data['Order']['condicao_pagamento'] == 2) { ?>
                    <li class="bullet-6">
                        <div class="el"><i><img src="<?php echo $this->base."/img/icones/em_processamento.ico" ?>" alt="Em Faturamento" style="width: 15px; height: 15px;"></i></div>
                        <div class="txt">Em <br>Faturamento</div>
                    </li>
                <?php } ?>
                <li class="bullet-7">
                    <div class="el"><i><img src="<?php echo $this->base."/img/icones/finalizado.ico" ?>" alt="Finalizado" style="width: 15px; height: 15px;"></i></div>
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
        width: 480px;
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
        width: 8%; /* Ajustado para 7 itens */
    }

    .tracking ul>li .el {
        position: relative;
        margin-top: 100%;
    }

    .tracking ul>li .el i {
        position: absolute;
        bottom: 100%;
        left: 5%;
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
        background-color: transparent;
        color: var(--blue);
        font-size: 10px;
        padding: 3px;
        cursor: pointer;
        z-index: 2;
        transition: background-color 0.3s, transform 0.2s ease-in-out;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .arrow-button i {
        font-size: 15px;
        color: var(--blue);
    }

    .arrow-button.left {
        left: -200px;
    }

    .arrow-button.right {
        right: -200px;
    }

    .arrow-button:hover {
        background-color: #fff;
        transform: translateY(-50%) rotate(360deg);
    }

    .arrow-button:hover i {
        color: #ED0677;
    }

    .arrow-button.loading {
        pointer-events: none;
    }

    .arrow-button.loading i {
        display: none;
    }

    .arrow-button.loading::after {
        content: '';
        border: 3px solid var(--blue);
        border-top: 3px solid transparent;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    /* Progress bar widths - ajustados para 7 etapas */
    .tracking .progress-0 .color-bar {
        width: 0%;
    }

    .tracking .progress-1 .color-bar {
        width: 10%;
    }

    .tracking .progress-2 .color-bar {
        width: 25%;
    }

    .tracking .progress-3 .color-bar {
        width: 25%;
    }

    .tracking .progress-4 .color-bar {
        width: 40%;
    }

    .tracking .progress-5 .color-bar {
        width: 40%;
    }

    .tracking .progress-6 .color-bar {
        width: 55%;
    }

    .tracking .progress-7 .color-bar {
        width: 55%;
    }

    .tracking .progress-8 .color-bar {
        width: 70%;
    }

    .tracking .progress-9 .color-bar {
        width: 70%;
    }

    .tracking .progress-10 .color-bar {
        width: 85%;
    }

    .tracking .progress-11 .color-bar {
        width: 85%;
    }

    .tracking .progress-12 .color-bar {
        width: 90%;
    }

    /* Background colors for bullets - Bullet 1 (Início) */
    .tracking .progress-0>ul>li.bullet-1,
    .tracking .progress-1>ul>li.bullet-1,
    .tracking .progress-2>ul>li.bullet-1,
    .tracking .progress-3>ul>li.bullet-1,
    .tracking .progress-4>ul>li.bullet-1,
    .tracking .progress-5>ul>li.bullet-1,
    .tracking .progress-6>ul>li.bullet-1,
    .tracking .progress-7>ul>li.bullet-1,
    .tracking .progress-8>ul>li.bullet-1,
    .tracking .progress-9>ul>li.bullet-1,
    .tracking .progress-10>ul>li.bullet-1,
    .tracking .progress-11>ul>li.bullet-1,
    .tracking .progress-12>ul>li.bullet-1 {
        background-color: var(--blue);
    }

    /* Bullet 2 (Aguardando Pagamento) */
    .tracking .progress-2>ul>li.bullet-2,
    .tracking .progress-3>ul>li.bullet-2,
    .tracking .progress-4>ul>li.bullet-2,
    .tracking .progress-5>ul>li.bullet-2,
    .tracking .progress-6>ul>li.bullet-2,
    .tracking .progress-7>ul>li.bullet-2,
    .tracking .progress-8>ul>li.bullet-2,
    .tracking .progress-9>ul>li.bullet-2,
    .tracking .progress-10>ul>li.bullet-2,
    .tracking .progress-11>ul>li.bullet-2,
    .tracking .progress-12>ul>li.bullet-2 {
        background-color: var(--blue);
    }

    /* Bullet 3 (Pagamento Confirmado) */
    .tracking .progress-4>ul>li.bullet-3,
    .tracking .progress-5>ul>li.bullet-3,
    .tracking .progress-6>ul>li.bullet-3,
    .tracking .progress-7>ul>li.bullet-3,
    .tracking .progress-8>ul>li.bullet-3,
    .tracking .progress-9>ul>li.bullet-3,
    .tracking .progress-10>ul>li.bullet-3,
    .tracking .progress-11>ul>li.bullet-3,
    .tracking .progress-12>ul>li.bullet-3 {
        background-color: var(--blue);
    }

    /* Bullet 4 (Em Processamento) */
    .tracking .progress-6>ul>li.bullet-4,
    .tracking .progress-7>ul>li.bullet-4,
    .tracking .progress-8>ul>li.bullet-4,
    .tracking .progress-9>ul>li.bullet-4,
    .tracking .progress-10>ul>li.bullet-4,
    .tracking .progress-11>ul>li.bullet-4,
    .tracking .progress-12>ul>li.bullet-4 {
        background-color: var(--blue);
    }

    /* Bullet 5 (Aguardando Liberação) */
    .tracking .progress-8>ul>li.bullet-5,
    .tracking .progress-9>ul>li.bullet-5,
    .tracking .progress-10>ul>li.bullet-5,
    .tracking .progress-11>ul>li.bullet-5,
    .tracking .progress-12>ul>li.bullet-5 {
        background-color: var(--blue);
    }

    /* Bullet 6 (Nova Etapa - Em Entrega) */
    .tracking .progress-10>ul>li.bullet-6,
    .tracking .progress-11>ul>li.bullet-6,
    .tracking .progress-12>ul>li.bullet-6 {
        background-color: var(--blue);
    }

    /* Bullet 7 (Finalizado) */
    .tracking .progress-12>ul>li.bullet-7 {
        background-color: var(--green);
    }

    /* Icon visibility - Bullet 1 */
    .tracking .progress-1>ul>li.bullet-1 .el i,
    .tracking .progress-2>ul>li.bullet-1 .el i,
    .tracking .progress-3>ul>li.bullet-1 .el i,
    .tracking .progress-4>ul>li.bullet-1 .el i,
    .tracking .progress-5>ul>li.bullet-1 .el i,
    .tracking .progress-6>ul>li.bullet-1 .el i,
    .tracking .progress-7>ul>li.bullet-1 .el i,
    .tracking .progress-8>ul>li.bullet-1 .el i,
    .tracking .progress-9>ul>li.bullet-1 .el i,
    .tracking .progress-10>ul>li.bullet-1 .el i,
    .tracking .progress-11>ul>li.bullet-1 .el i,
    .tracking .progress-12>ul>li.bullet-1 .el i {
        display: block;
    }

    /* Icon visibility - Bullet 2 */
    .tracking .progress-3>ul>li.bullet-2 .el i,
    .tracking .progress-4>ul>li.bullet-2 .el i,
    .tracking .progress-5>ul>li.bullet-2 .el i,
    .tracking .progress-6>ul>li.bullet-2 .el i,
    .tracking .progress-7>ul>li.bullet-2 .el i,
    .tracking .progress-8>ul>li.bullet-2 .el i,
    .tracking .progress-9>ul>li.bullet-2 .el i,
    .tracking .progress-10>ul>li.bullet-2 .el i,
    .tracking .progress-11>ul>li.bullet-2 .el i,
    .tracking .progress-12>ul>li.bullet-2 .el i {
        display: block;
    }

    /* Icon visibility - Bullet 3 */
    .tracking .progress-5>ul>li.bullet-3 .el i,
    .tracking .progress-6>ul>li.bullet-3 .el i,
    .tracking .progress-7>ul>li.bullet-3 .el i,
    .tracking .progress-8>ul>li.bullet-3 .el i,
    .tracking .progress-9>ul>li.bullet-3 .el i,
    .tracking .progress-10>ul>li.bullet-3 .el i,
    .tracking .progress-11>ul>li.bullet-3 .el i,
    .tracking .progress-12>ul>li.bullet-3 .el i {
        display: block;
    }

    /* Icon visibility - Bullet 4 */
    .tracking .progress-7>ul>li.bullet-4 .el i,
    .tracking .progress-8>ul>li.bullet-4 .el i,
    .tracking .progress-9>ul>li.bullet-4 .el i,
    .tracking .progress-10>ul>li.bullet-4 .el i,
    .tracking .progress-11>ul>li.bullet-4 .el i,
    .tracking .progress-12>ul>li.bullet-4 .el i {
        display: block;
    }

    /* Icon visibility - Bullet 5 */
    .tracking .progress-9>ul>li.bullet-5 .el i,
    .tracking .progress-10>ul>li.bullet-5 .el i,
    .tracking .progress-11>ul>li.bullet-5 .el i,
    .tracking .progress-12>ul>li.bullet-5 .el i {
        display: block;
    }

    /* Icon visibility - Bullet 6 */
    .tracking .progress-11>ul>li.bullet-6 .el i,
    .tracking .progress-12>ul>li.bullet-6 .el i {
        display: block;
    }

    /* Icon visibility - Bullet 7 */
    .tracking .progress-12>ul>li.bullet-7 .el i {
        display: block;
    }

    /* Text colors - Bullet 1 */
    .tracking .progress-1>ul>li.bullet-1 .txt,
    .tracking .progress-2>ul>li.bullet-1 .txt,
    .tracking .progress-3>ul>li.bullet-1 .txt,
    .tracking .progress-4>ul>li.bullet-1 .txt,
    .tracking .progress-5>ul>li.bullet-1 .txt,
    .tracking .progress-6>ul>li.bullet-1 .txt,
    .tracking .progress-7>ul>li.bullet-1 .txt,
    .tracking .progress-8>ul>li.bullet-1 .txt,
    .tracking .progress-9>ul>li.bullet-1 .txt,
    .tracking .progress-10>ul>li.bullet-1 .txt,
    .tracking .progress-11>ul>li.bullet-1 .txt,
    .tracking .progress-12>ul>li.bullet-1 .txt {
        color: var(--blue);
    }

    /* Text colors - Bullet 2 */
    .tracking .progress-3>ul>li.bullet-2 .txt,
    .tracking .progress-4>ul>li.bullet-2 .txt,
    .tracking .progress-5>ul>li.bullet-2 .txt,
    .tracking .progress-6>ul>li.bullet-2 .txt,
    .tracking .progress-7>ul>li.bullet-2 .txt,
    .tracking .progress-8>ul>li.bullet-2 .txt,
    .tracking .progress-9>ul>li.bullet-2 .txt,
    .tracking .progress-10>ul>li.bullet-2 .txt,
    .tracking .progress-11>ul>li.bullet-2 .txt,
    .tracking .progress-12>ul>li.bullet-2 .txt {
        color: var(--blue);
    }

    /* Text colors - Bullet 3 */
    .tracking .progress-5>ul>li.bullet-3 .txt,
    .tracking .progress-6>ul>li.bullet-3 .txt,
    .tracking .progress-7>ul>li.bullet-3 .txt,
    .tracking .progress-8>ul>li.bullet-3 .txt,
    .tracking .progress-9>ul>li.bullet-3 .txt,
    .tracking .progress-10>ul>li.bullet-3 .txt,
    .tracking .progress-11>ul>li.bullet-3 .txt,
    .tracking .progress-12>ul>li.bullet-3 .txt {
        color: var(--blue);
    }

    /* Text colors - Bullet 4 */
    .tracking .progress-7>ul>li.bullet-4 .txt,
    .tracking .progress-8>ul>li.bullet-4 .txt,
    .tracking .progress-9>ul>li.bullet-4 .txt,
    .tracking .progress-10>ul>li.bullet-4 .txt,
    .tracking .progress-11>ul>li.bullet-4 .txt,
    .tracking .progress-12>ul>li.bullet-4 .txt {
        color: var(--blue);
    }

    /* Text colors - Bullet 5 */
    .tracking .progress-9>ul>li.bullet-5 .txt,
    .tracking .progress-10>ul>li.bullet-5 .txt,
    .tracking .progress-11>ul>li.bullet-5 .txt,
    .tracking .progress-12>ul>li.bullet-5 .txt {
        color: var(--blue);
    }

    /* Text colors - Bullet 6 */
    .tracking .progress-11>ul>li.bullet-6 .txt,
    .tracking .progress-12>ul>li.bullet-6 .txt {
        color: var(--blue);
    }

    /* Text colors - Bullet 7 */
    .tracking .progress-12>ul>li.bullet-7 .txt {
        color: var(--blue);
    }

    /* Responsividade das setas */
    @media (max-width: 768px) {
        .tracking {
            max-width: 94%;
        }

        .tracking ul {
            flex-wrap: wrap;
        }

        .tracking ul li {
            flex: none;
            width: 12%; /* Ajustado para 7 itens */
            background-size: 50%; 
            background-position: center center;
        }
        
        .tracking ul>li .el i {
            font-size: 1.2rem; 
            margin-left: 8px;
        }
        
        .arrow-button {
            font-size: 16px;
            top: 40%;
        }

        .arrow-button.left {
            left: 5px;
        }

        .arrow-button.right {
            right: 5px;
        }
    }

    @media (max-width: 480px) {
        .tracking ul li {
            width: 10%; /* Ainda mais compacto para telas pequenas */
        }
        
        .arrow-button {
            font-size: 10px;
            padding: 3px;
            top: 65%;
        }

        .arrow-button.left {
            left: -25px;
        }

        .arrow-button.right {
            right: -25px;
        }
    }
</style>

<script>
    document.querySelectorAll('.arrow-button').forEach(button => {
        button.addEventListener('click', function() {
            this.classList.add('loading');
            setTimeout(() => {
                window.location.href = this.querySelector('a').href;
            }, 1000);
        });
    });
</script>