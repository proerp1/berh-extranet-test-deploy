<!--begin::Form-->
<?php echo $this->Form->create('User', array("class" => "form w-100", "id" => "kt_sign_in_form", "novalidate" => "novalidate"));?>
    <!--begin::Heading-->
    <div class="text-center mb-10">
        <!--begin::Title-->
        <img alt="Logo" src="<?php echo $this->base."/img/Terra_GIF_sig.gif" ?>" class="h-100px" />
        <img alt="Logo" src="<?php echo $this->base."/img/logo-berh-colorido.png" ?>" class="h-100px" />
        <!--end::Title-->
    </div>
    <?php echo $this->Flash->render() ?>
    <!--begin::Heading-->
    <!--begin::Input group-->
    <div class="fv-row mb-10">
        <!--begin::Label-->
        <label class="form-label fs-6 fw-bolder text-dark">Email</label>
        <!--end::Label-->
        <!--begin::Input-->
        <input required class="form-control form-control-lg form-control-solid" type="text" name="data[User][username]" autocomplete="off" />
        <!--end::Input-->
    </div>
    <!--end::Input group-->
    <!--begin::Input group-->
    <div class="fv-row mb-10">
        <!--begin::Wrapper-->
        <div class="d-flex flex-stack mb-2">
            <!--begin::Label-->
            <label class="form-label fw-bolder text-dark fs-6 mb-0">Senha</label>
            <!--end::Label-->
        </div>
        <!--end::Wrapper-->
        <!--begin::Input-->
        <input required class="form-control form-control-lg form-control-solid" type="password" name="data[User][password]" autocomplete="off" />
        <!--begin::Link-->
        <a href="<?php echo $this->Html->url(['controller' => 'users', 'action' => 'forgot_password']) ?>" class="link-primary fs-6 fw-bolder">Esqueceu a senha ?</a>
            <!--end::Link-->
        <!--end::Input-->
    </div>
    <!--end::Input group-->
    <!--begin::Actions-->
    <div class="text-center">
        <!--begin::Submit button-->
        <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-facebook w-100 mb-5" style="background-color: #ED0677;">
            <span class="indicator-label">Entrar</span>
            <span class="indicator-progress">Please wait...
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
        </button>
        <!--end::Submit button-->
    </div>
    <!--end::Actions-->
</form>
<!--end::Form-->
