<div id="kt_header" style="" class="header align-items-stretch" >
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show aside menu">
            <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle">
                <span class="svg-icon svg-icon-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="currentColor" />
                        <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="currentColor" />
                    </svg>
                </span>
            </div>
        </div>
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
            <a href="<?php echo $this->base ?>" class="d-lg-none">
                <img alt="Logo" src="<?php echo $this->base."/img/logo-berh-colorido.png" ?>" class="w-100px"/>
            </a>
        </div>
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <div class="d-flex align-items-stretch" id="kt_header_nav">
                <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch" id="#kt_header_menu" data-kt-menu="true">
                    <a href="<?php echo $this->Html->url(['controller' => 'pages', 'action' => 'comunicados']) ?>" class="menu-item here show menu-lg-down-accordion me-lg-1">
                        <span class="menu-link py-3">
                            <span class="menu-title">Comunicados</span>
                            <span class="menu-arrow d-lg-none"></span>
                        </span>
                    </a>
                    <a href="<?php echo $this->Html->url(['controller' => 'pages', 'action' => 'documentacao']) ?>" class="menu-item here show menu-lg-down-accordion me-lg-1">
                        <span class="menu-link py-3">
                            <span class="menu-title">Documentação</span>
                            <span class="menu-arrow d-lg-none"></span>
                        </span>
                    </a>
                    <a href="<?php echo $this->Html->url(['controller' => 'pages', 'action' => 'layout']) ?>" class="menu-item here show menu-lg-down-accordion me-lg-1">
                        <span class="menu-link py-3">
                            <span class="menu-title">Layout</span>
                            <span class="menu-arrow d-lg-none"></span>
                        </span>
                    </a>
                    <a href="<?php echo $this->Html->url(['controller' => 'pages', 'action' => 'ajuda']) ?>" class="menu-item here show menu-lg-down-accordion me-lg-1">
                        <span class="menu-link py-3">
                            <span class="menu-title">Ajuda</span>
                            <span class="menu-arrow d-lg-none"></span>
                        </span>
                    </a>
                    <a href="<?php echo $this->Html->url(['controller' => 'pages', 'action' => 'biblioteca']) ?>" class="menu-item here show menu-lg-down-accordion me-lg-1">
                        <span class="menu-link py-3">
                            <span class="menu-title">Biblioteca</span>
                            <span class="menu-arrow d-lg-none"></span>
                        </span>
                    </a>
                    <a href="<?php echo $this->Html->url(['controller' => 'dashboard', 'action' => 'index']) ?>" class="menu-item here show menu-lg-down-accordion me-lg-1">
                        <span class="menu-link py-3">
                            <span class="menu-title">FAQS</span>
                            <span class="menu-arrow d-lg-none"></span>
                        </span>
                    </a>
                </div>
            </div>
            <div class="d-flex align-items-stretch flex-shrink-0">
                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                        <?php
                            if (CakeSession::read("Auth.User.img_profile") == "") {
                                $img = $this->base.'/img/profiles/avatar.png';
                            } else {
                                $img = $this->base."/files/user/img_profile/".CakeSession::read("Auth.User.id")."/".CakeSession::read("Auth.User.img_profile");
                            }
                        ?>
                        <img style="object-fit: cover" src="<?php echo $img; ?>" alt="<?php echo CakeSession::read("Auth.User.name");?>" />
                    </div>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content d-flex align-items-center px-3">
                                <div class="symbol symbol-50px me-5">
                                    <img style="object-fit: cover" class="cursor-pointer upload-profile-image" alt="<?php echo CakeSession::read("Auth.User.name");?>" src="<?php echo $img; ?>" />
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bolder d-flex align-items-center fs-6">
                                        <?php echo CakeSession::read("Auth.User.name");?>
                                    </div>
                                    <div class="d-flex flex-column fs-8" style="color: #666">
                                        <?php echo CakeSession::read("Auth.User.Customer.nome_primario");?>
                                        <span id="doc_text"><?php echo CakeSession::read("Auth.User.Customer.documento");  ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-5">
                            <a href="javascript:" class="upload-profile-image menu-link px-5">Alterar imagem de perfil</a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="<?php echo $this->base.'/users/change_password/'; ?>" class="menu-link px-5">Alterar senha</a>
                        </div>
                        <div class="menu-item px-5">
                            <a href="<?php echo $this->base.'/users/logout/'; ?>" class="menu-link px-5">Sair</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
