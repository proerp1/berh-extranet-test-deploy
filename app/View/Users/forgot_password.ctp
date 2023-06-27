<!--begin::Form-->
<?php echo $this->Form->create('User', array("class" => "form w-100", "id" => "kt_sign_in_form", "novalidate" => "novalidate", "url" => "forgot_password/"));?>
    <!--begin::Heading-->
    <div class="text-center mb-10">
        <!--begin::Title-->
        <h1 class="text-dark mb-3">Recuperar senha</h1>
        <p>Entre seu email para recuperar a sua senha!</p>
        <!--end::Title-->
    </div>
    <?php echo $this->Session->flash(); ?>
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
    <!--begin::Actions-->
    <div class="text-center">
        <!--begin::Submit button-->
        <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-facebook w-100 mb-5" style="background-color: #e61c72;">
            <span class="indicator-label">Recuperar</span>
        </button>
        <!--end::Submit button-->
        <a href="<?php echo $this->Html->url(['controller' => 'users', 'action' => 'login']) ?>">Voltar</a>
    </div>
    <!--end::Actions-->
</form>
<!--end::Form-->