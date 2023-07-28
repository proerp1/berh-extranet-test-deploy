<?php
    $class = "";
    if (CakeSession::read("Auth.User.primeiro_acesso") == 1) {
        $class = "desativado";
    }
?>

<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        <!--begin::Logo-->
        <a href="<?php echo $this->base ?>">
            <img alt="Logo" src="<?php echo $this->base."/img/berh_2.png" ?>" class="w-125px logo" />
        </a>
        <!--end::Logo-->
        <!--begin::Aside toggler-->
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
            <!--begin::Svg Icon | path: icons/duotune/arrows/arr079.svg-->
            <span class="svg-icon svg-icon-1 rotate-180">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor" />
                    <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor" />
                </svg>
            </span>
            <!--end::Svg Icon-->
        </div>
        <!--end::Aside toggler-->
    </div>
    <!--end::Brand-->
    <!--begin::Aside menu-->
    <div class="aside-menu flex-column-fluid">
        <!--begin::Aside Menu-->
        <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
            <!--begin::Menu-->
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true" data-kt-menu-expand="false">
                <div class="menu-item">
                    <a class="menu-link <?php echo $class.($this->request->params['controller'] == 'dashboard' ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'dashboard', 'action' => 'index']) ?>">
                        <span class="menu-icon">
                            <i class="fas fa-chart-pie"></i> 
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link <?php echo $class.($this->request->params['controller'] == 'atendimentos' ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'atendimentos', 'action' => 'index']) ?>">
                        <span class="menu-icon">
                            <i class="fas fa-bullhorn"></i> 
                        </span>
                        <span class="menu-title">
                            Atendimento <?php echo $pendentes > 0 ? '<span class="label label-warning js-count-alert">'.$pendentes.'</span>' : '' ?>
                        </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'customers' && !isset($_GET['logon'])) ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'customers', 'action' => 'index']) ?>">
                        <span class="menu-icon">
                            <i class="fas fa-users"></i> 
                        </span>
                        <span class="menu-title">
                            Clientes
                        </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'orders' && !isset($_GET['logon'])) ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'orders', 'action' => 'index']) ?>">
                        <span class="menu-icon">
                            <i class="fas fa-shopping-cart"></i> 
                        </span>
                        <span class="menu-title">
                            Pedidos
                        </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'resales') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'resales', 'action' => 'index']) ?>">
                        <span class="menu-icon">
                            <i class="fas fa-briefcase"></i> 
                        </span>
                        <span class="menu-title">
                            Revendas
                        </span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'prospects') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'prospects', 'action' => 'index']) ?>">
                        <span class="menu-icon">
                            <i class="fas fa-user-plus"></i> 
                        </span>
                        <span class="menu-title">
                            Prospects
                        </span>
                    </a>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fas fa-dollar-sign"></i> 
                        </span>
                        <span class="menu-title">Financeiro</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'outcomes') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'outcomes', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Contas a pagar</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'incomes') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'incomes', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Contas a receber</span>
                            </a>
                        </div>

                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Relatórios</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'baixa_manual') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'baixa_manual']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Baixa manual</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'despesas') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'despesas']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Despesas</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'fluxo_caixa') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'fluxo_caixa']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Fluxo de caixa</span>
                                    </a>
                                </div>

                            </div>
                        </div>

                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Boletos</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'boletos' && $this->request->params['action'] == 'index') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'boletos', 'action' => 'index']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Emitir Boletos</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'boletos' && in_array($this->request->params['action'], ['lotes', 'detalhes_lote'])) ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'boletos', 'action' => 'lotes']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Lotes Boleto</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Cnab</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'cnab' && $this->request->params['action'] == 'index') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'cnab', 'action' => 'index']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Gerar arquivo CNAB</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'cnab' && in_array($this->request->params['action'], ['lotes', 'detalhes_lote'])) ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'cnab', 'action' => 'lotes']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Lotes CNAB</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'retorno_cnabs' && in_array($this->request->params['action'], ['index', 'add', 'detalhes'])) ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'retorno_cnabs', 'action' => 'index']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Retorno Sicoob</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'transfers') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'transfers', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Transferência</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'billings') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'billings', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Faturamento</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'billing_sales') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'billing_sales', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Faturamento Vendas</span>
                            </a>
                        </div>

                        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                            <span class="menu-link">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Configurações</span>
                                <span class="menu-arrow"></span>
                            </span>
                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.((in_array($this->request->params['controller'], ['bank_accounts', 'bank_tickets'])) ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'bank_accounts', 'action' => 'index']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Contas e Boletos</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.((in_array($this->request->params['controller'], ['plano_contas'])) ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'plano_contas', 'action' => 'index1', '1']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Plano de Contas</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="menu-item">
                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'cobrancas') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'cobrancas', 'action' => 'index']) ?>">
                        <span class="menu-icon">
                            <i class="fas fa-dollar-sign"></i> 
                        </span>
                        <span class="menu-title">
                            Cobrança
                        </span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'emails_campanhas') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'emails_campanhas', 'action' => 'index']) ?>">
                        <span class="menu-icon">
                            <i class="fas fa-envelope"></i> 
                        </span>
                        <span class="menu-title">
                            Emails
                        </span>
                    </a>
                </div>
                <!--
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fas fa-list"></i> 
                        </span>
                        <span class="menu-title">Relatórios</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'bloqueio_diario') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'bloqueio_diario']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Bloqueio diário</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'clientes') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'clientes']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Clientes</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'clientes_desconto') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'clientes_desconto']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Clientes com desconto</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'inadimplentes') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'inadimplentes']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Clientes inadimplentes</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'clientes_bloquear') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'clientes_bloquear']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Clientes para bloquear</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'cobrancas') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'cobrancas']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Cobranças</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'contas_pagas' && $this->request->params['action'] == 'index') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'contas_pagas', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Contas Pagas</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'dados_comerciais' && $this->request->params['action'] == 'index') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'dados_comerciais', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Dados comerciais</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'movimentacao_status') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'movimentacao_status']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Movimentação status</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'produto_desconto') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'produto_desconto']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Produtos com desconto</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'receitas') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'receitas']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Receitas</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['action'] == 'status_clientes') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'status_clientes']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Status Clientes</span>
                            </a>
                        </div>

                    </div>
                </div>
                -->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fas fa-cog"></i>
                        </span>
                        <span class="menu-title">Cadastros</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'benefits') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'benefits', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Benefícios</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'cost_centers') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'cost_centers', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Centro de custo</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'expenses') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'expenses', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Despesas</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'suppliers') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'suppliers', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Fornecedores</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'plans') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'plans', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Planos</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(in_array($this->request->params['controller'], ['products', 'product_attributes']) ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'products', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Produtos</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'activity_areas') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'activity_areas', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Ramo de atividades</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'revenues') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'revenues', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Receitas</span>
                            </a>
                        </div>

                    </div>
                </div>

                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fas fa-cog"></i>
                        </span>
                        <span class="menu-title">Configuração</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'departments') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'departments', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Departamentos</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'cobrancas' && $this->request->params['action'] == 'divisao_cobradores') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'cobrancas', 'action' => 'divisao_cobradores']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Divisao de cobradores</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'groups') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'groups', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Grupos</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'price_tables') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'price_tables', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Tabela de preços</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(in_array($this->request->params['controller'], ['users', 'user_resales']) ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'users', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Usuários</span>
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
