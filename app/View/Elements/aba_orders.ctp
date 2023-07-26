<div class="tracking-wrapper">
    <div class="tracking">
        <div id="progress" class="progress-<?php echo $progress; ?>">
            <div class="empty-bar"></div>
            <div class="color-bar"></div>
            <ul>
                <li class="bullet-1">
                    <div class="el"><i class='fa fa-cog'></i></div>
                    <div class="txt">Configuração</div>
                </li>
                <li class="bullet-2">
                    <div class="el"><i class='fa fa-list'></i></div>
                    <div class="txt">Aguardando Validação</div>
                </li>
                <li class="bullet-3">
                    <div class="el"><i class='fa fa-credit-card'></i></div>
                    <div class="txt">Aguardando Pagamento</div>
                </li>
                <li class="bullet-4">
                    <div class="el"><i class='fa fa-check'></i></div>
                    <div class="txt">Finalizado</div>
                </li>
            </ul>
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

    .tracking .progress-0 .color-bar {
        width: 00%;
    }

    .tracking .progress-1 .color-bar {
        width: 15%;
    }

    .tracking .progress-2 .color-bar {
        width: 30%;
    }

    .tracking .progress-3 .color-bar {
        width: 45%;
    }

    .tracking .progress-4 .color-bar {
        width: 60%;
    }

    .tracking .progress-5 .color-bar {
        width: 75%;
    }

    .tracking .progress-6 .color-bar {
        width: 90%;
    }

    .tracking .progress-7 .color-bar {
        width: 90%;
    }

    .tracking .progress-0>ul>li.bullet-1,
    .tracking .progress-1>ul>li.bullet-1,
    .tracking .progress-2>ul>li.bullet-1,
    .tracking .progress-3>ul>li.bullet-1,
    .tracking .progress-4>ul>li.bullet-1,
    .tracking .progress-5>ul>li.bullet-1,
    .tracking .progress-6>ul>li.bullet-1,
    .tracking .progress-7>ul>li.bullet-1 {
        background-color: var(--blue);
    }

    .tracking .progress-2>ul>li.bullet-2,
    .tracking .progress-3>ul>li.bullet-2,
    .tracking .progress-4>ul>li.bullet-2,
    .tracking .progress-5>ul>li.bullet-2,
    .tracking .progress-6>ul>li.bullet-2,
    .tracking .progress-7>ul>li.bullet-2 {
        background-color: var(--blue);
    }

    .tracking .progress-4>ul>li.bullet-3,
    .tracking .progress-5>ul>li.bullet-3,
    .tracking .progress-6>ul>li.bullet-3,
    .tracking .progress-7>ul>li.bullet-3 {
        background-color: var(--blue);
    }

    .tracking .progress-6>ul>li.bullet-4,
    .tracking .progress-7>ul>li.bullet-4 {
        background-color: var(--blue);
    }

    .tracking .progress-7>ul>li.bullet-4 {
        background-color: var(--green);
    }

    .tracking .progress-1>ul>li.bullet-1 .el i,
    .tracking .progress-2>ul>li.bullet-1 .el i,
    .tracking .progress-3>ul>li.bullet-1 .el i,
    .tracking .progress-4>ul>li.bullet-1 .el i,
    .tracking .progress-5>ul>li.bullet-1 .el i,
    .tracking .progress-6>ul>li.bullet-1 .el i,
    .tracking .progress-7>ul>li.bullet-1 .el i {
        display: block;
    }

    .tracking .progress-3>ul>li.bullet-2 .el i,
    .tracking .progress-4>ul>li.bullet-2 .el i,
    .tracking .progress-5>ul>li.bullet-2 .el i,
    .tracking .progress-6>ul>li.bullet-2 .el i,
    .tracking .progress-7>ul>li.bullet-2 .el i {
        display: block;
    }

    .tracking .progress-5>ul>li.bullet-3 .el i,
    .tracking .progress-6>ul>li.bullet-3 .el i,
    .tracking .progress-7>ul>li.bullet-3 .el i {
        display: block;
    }

    .tracking .progress-7>ul>li.bullet-4 .el i {
        display: block;
    }

    .tracking .progress-1>ul>li.bullet-1 .txt,
    .tracking .progress-2>ul>li.bullet-1 .txt,
    .tracking .progress-3>ul>li.bullet-1 .txt,
    .tracking .progress-4>ul>li.bullet-1 .txt,
    .tracking .progress-5>ul>li.bullet-1 .txt,
    .tracking .progress-6>ul>li.bullet-1 .txt,
    .tracking .progress-7>ul>li.bullet-1 .txt {
        color: var(--blue);
    }

    .tracking .progress-3>ul>li.bullet-2 .txt,
    .tracking .progress-4>ul>li.bullet-2 .txt,
    .tracking .progress-5>ul>li.bullet-2 .txt,
    .tracking .progress-6>ul>li.bullet-2 .txt,
    .tracking .progress-7>ul>li.bullet-2 .txt {
        color: var(--blue);
    }

    .tracking .progress-5>ul>li.bullet-3 .txt,
    .tracking .progress-6>ul>li.bullet-3 .txt,
    .tracking .progress-7>ul>li.bullet-3 .txt {
        color: var(--blue);
    }

    .tracking .progress-7>ul>li.bullet-4 .txt {
        color: var(--blue);
    }
</style>