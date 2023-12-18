<?php
    $class = "";
    if (CakeSession::read("Auth.User.primeiro_acesso") == 1) {
        $class = "desativado";
    }
?>

<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <!--begin::Brand-->
    <div class="aside-logo flex-column-auto" id="kt_aside_logo" style="background-color: #ED0677 !important;">
        <!--begin::Logo-->
        <a href="<?php echo $this->base ?>">
            <img alt="Logo" src="<?php echo $this->base."/img/BE_Logo_Horizontal_Branco.png" ?>" class="w-125px logo" />
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
    <div class="aside-menu flex-column-fluid" style="background-color: #472F92 !important;">
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
                        <span class="d-flex justify-content-between menu-title">
                            Atendimento <?php echo $pendentes > 0 ? '<span class="badge badge-warning js-count-alert">'.$pendentes.'</span>' : '' ?>
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

                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fas fa-table"></i> 
                        </span>
                        <span class="menu-title">Relatórios</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && !isset($_GET['logon'])) ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Itinerários</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'reports' && $this->request->params['controller'] == 'reports') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'reports', 'action' => 'pedidos']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Pedidos</span>
                            </a>
                        </div>
                    </div>
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
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'incomes') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'incomes', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Contas a receber</span>
                            </a>
                        </div>
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'outcomes') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'outcomes', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Contas a pagar</span>
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
                                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'despesas_report') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'despesas_report', 'action' => 'index']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Despesas</span>
                                    </a>
                                </div>

                                <div class="menu-item">
                                    <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'fluxo_caixa') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'fluxo_caixa', 'action' => 'index']) ?>">
                                        <span class="menu-bullet">
                                            <span class="bullet bullet-dot"></span>
                                        </span>
                                        <span class="menu-title">Fluxo de caixa</span>
                                    </a>
                                </div>

                            </div>
                        </div>
                        <!--
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
                        -->
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

                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'transfers') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'transfers', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Transferência</span>
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

                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="fas fa-minus-circle"></i>
                        </span>
                        <span class="menu-title">Cadastros</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion menu-active-bg">
                        
                        <div class="menu-item">
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'suppliers') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'suppliers', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Fornecedores</span>
                            </a>
                        </div>

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
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'departments') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'departments', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Departamentos</span>
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
                            <a class="menu-link <?php echo $class.(($this->request->params['controller'] == 'groups') ? ' active' : '') ?>" href="<?php echo $this->Html->url(['controller' => 'groups', 'action' => 'index']) ?>">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">Grupos</span>
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
