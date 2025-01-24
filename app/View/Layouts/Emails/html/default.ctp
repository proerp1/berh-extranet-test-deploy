<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            line-height: 1.5;
            color: #2F3044;
        }
        table {
            border-collapse: collapse;
            margin: 0 auto;
            width: 100%;
            max-width: 800px;
        }
        .header, .footer {
            background-color: #472F92;
            padding: 15px;
            text-align: center;
            color: #ffffff;
            border-radius: 6px 6px 0 0;
        }
        .footer {
            border-radius: 0 0 6px 6px;
            font-size: 13px;
        }
        .header img {
            width: 120px;
        }
        .content {
            background-color: #f7f7f7;
            border-radius: 6px;
            padding: 30px;
            margin: 10px 20px;
            text-align: left;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        a {
            color: #ffffff;
            text-decoration: none;
        }
        .footer a {
            color: #ffffff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div style="background-color:#ffffff; padding: 20px 0;">
        <table>
            <tr>
                <td class="header">
                    <a href="https://berh.com.br/" target="_blank" rel="noopener">
                        <img src="https://sig.berh.com.br/img/BE_Logo_Horizontal_Branco.png" alt="BeRH Logo">
                    </a>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="content">
                        <?php echo $this->fetch('content'); ?>
                        <p style="margin-top: 20px;">Atenciosamente,<br><strong>BeRH</strong></p>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="footer">
                    <p>Atendimento (11) 5043-0544 | 
                        <a href="mailto:atendimento@berh.com.br">atendimento@berh.com.br</a>
                    </p>
                    <p>Copyright © <a href="https://berh.com.br/" target="_blank" rel="noopener">BeRH</a>.</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
