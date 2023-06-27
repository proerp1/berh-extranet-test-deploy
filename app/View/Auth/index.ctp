<div class="container">
  <form action="<?php echo $this->base; ?>/users/login" id="UserLoginForm" method="post" class="form-signin" accept-charset="utf-8">
    <h2 class="form-signin-heading">Bem-vindo!</h2>
    <input type="email" class="form-control" placeholder="e-mail" required="" autofocus="">
    <input type="password" class="form-control" placeholder="senha" required="">
    <label class="checkbox">
      <input type="checkbox" value="remember-me"> Lembrar?
    </label>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Entrar</button>
    <?php echo $this->Form->end(__('Login'));?>
  </form>
</div>