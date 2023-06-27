<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body style="font-family:\'Helvetica Neue\', Helvetica, Arial, sans-serif; color: #333; border: 0; background-color: #eee; padding: 20px 0;">
    <table align="center" cellpadding="0" cellspacing="0" border="0" width="670" style="border: 1px solid #ccc; background:#fff;">
      <!-- header -->
      <thead>
        <tr style="background:#1d4f91; table-layout:fixed; height: 60px;">
          <td width="50"></td>
          <td>
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
              <tbody>
                <tr>
                  <td>
                    <a href="https://credcheck.com.br" target="_blank" style="text-decoration: none;color: #fff;font-weight: bold;font-size: 22px;" title="clique aqui para acessar o nosso site">
                      <img src="<?php echo "https://credcheck.com.br/extranet/img/logo-credcheckb-branco-alta.png" ?>" width="175" style="">                      
                    </a>
                  </td>                  
                </tr>
              </tbody>
            </table>
          </td>
          <td width="50"></td>
        </tr>
      </thead>
      <!-- /header -->

      <!-- body -->
      <tbody>
        <tr>
          <td style="background: #fff; height: 17px;">&nbsp;</td>
        </tr>
        <tr>
          <td width="50">&nbsp;</td>
          <td>
            <table border="0" cellspacing="0" cellpadding="0" width="568" style="-webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; border:1px solid #dddddd;">
              <tbody>
                <tr>
                  <td height="10"></td>
                </tr>
                <tr>
                  <td valign="top" style="padding-left: 20px;">
                    <?php echo $this->fetch('content');?>

                  </td>
                  <td width="20"></td>
                </tr>
                <tr>
                  <td style="text-align: right;" colspan="3"><img src="https://credcheck.com.br/cliente/img/logo-serasa-novo.png" width="120"></td>

                </tr>
              </tbody>
            </table>
          </td>
          <td width="50">&nbsp;</td>
        </tr>
        <tr>
          <td style="background: #fff; height: 17px;">&nbsp;</td>
        </tr>
      </tbody>
      <!-- /body -->

      <!-- footer -->
      <tfoot>
          <tr style="height: 40px; background: #eee ; border-top: 1px solid #ddd;">
            <td width="50">&nbsp;</td>
            <td style="color:#999999; font-size:11px;">
                Atendimento: 0800 084 0996 | <a href="mailto:berh@berh.com.br" style="border:none; color:#0084b4; text-decoration:none">berh@berh.com.br </a>
            </td>
            <td width="50"></td>
          </tr>
      </tfoot>
      <!-- /footer -->
    </table>
  </body>
</html>