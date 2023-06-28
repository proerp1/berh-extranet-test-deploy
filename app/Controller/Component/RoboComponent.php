<?php

/**
 * Classe da NOVA Integração usando Cypress
 * Essa classe interliga os controllers à classe do cliente soap do me proteja
 */
class RoboComponent extends Component
{
    public $components = ['Session'];
    public $controller;

    public function gerar_logon($params)
    {
        $params['cnpj_indireto'] = $this->get_indireto_create($params['cnpj_indireto']);
        $params['cnpj_completo'] = $this->limpar_doc($params['cnpj_completo']);
        $params['cpf'] = $this->limpar_doc($params['cpf']);
        $params['tel'] = $this->limpar_doc($params['tel']);
        $params['cep'] = $this->limpar_doc($params['cep']);

        return trim($this->run_command2('create_logon', $params));
    }

    public function excluir_logon($params)
    {
        return trim($this->run_command2('delete_logon', $params));
    }

    public function activate_deactivate_logon($params)
    {
        return trim($this->run_command('activate_deactivate_logon', $params));
    }

    public function reset_password($params)
    {
        $params['cnpj_indireto'] = $this->get_indireto_create($params['cnpj_indireto']);
        $params['cnpj_completo'] = $this->limpar_doc($params['cnpj_completo']);

        return trim($this->run_command2('reset_password', $params));
    }

    public function add_access_logon($params)
    {
        return trim($this->run_command('include_access', $params));
    }

    public function remove_access_logon($params)
    {
        return trim($this->run_command('delete_access', $params));
    }

    public function simulate_filter($params)
    {
        $params['cnpj_indireto'] = $this->get_indireto($params['cnpj_indireto']);

        return trim($this->run_command2('simulate_filter', $params));
    }

    public function reset_shield($params)
    {
        return trim($this->run_command('reset_shield', $params));
    }

    public function analytical_statement($params)
    {   
        $file_name = trim($this->run_command2('analytical_statement', $params));

        if($file_name == ''){
            echo 'Serasa temporariamente fora do ar. Tente novamente em instantes. <a href="javascript:history.back()">Voltar</a>';die;
        }

        $full_name = dirname(__FILE__).'/berh-scrapper/tmp/'.$file_name;

        $file = file_get_contents($full_name);

        unlink($full_name);

        header('Content-type: text/csv');
        header("Content-Disposition: attachment; filename=".$file_name);

        echo $file;

        die;
    }

    private function run_command($method, $params)
    {
        $default = ["logon" => "70042582", "pwd" => "@Cred100"];

        if (isset($params['filial'])) {
            $params['filial'] = str_pad($params['filial'], 4, '0', STR_PAD_LEFT);
        }

        $params = array_merge($params, $default);

        $param_json = json_encode($params);

        $path = dirname(__FILE__) . "/robo-logon";

        return shell_exec("cd " . $path . " && ./robo.sh " . $method . " DATA='" . $param_json . "' ");
    }

    private function run_command2($method, $params)
    {
        $default = ["logon" => "70042582", "pwd" => "@Cred100"];

        if (isset($params['filial'])) {
            $params['filial'] = str_pad($params['filial'], 4, '0', STR_PAD_LEFT);
        }

        $params = array_merge($params, $default);

        $param_json = json_encode($params);

        $path = dirname(__FILE__) . "/berh-scrapper";

        return shell_exec("cd " . $path . " && ./robo.sh " . $method . " DATA='" . $param_json . "' ");
    }

    private function limpar_doc($cnpj)
    {
        return str_replace(['.', '/', '-'], '', $cnpj);
    }

    private function get_indireto($cnpj)
    {
        return str_pad(substr($this->limpar_doc($cnpj), 0, -6), 8, '0', STR_PAD_LEFT);
    }

    private function get_indireto_create($cnpj)
    {
        return str_pad(substr($this->limpar_doc($cnpj), 0, -6), 9, '0', STR_PAD_LEFT);
    }
}
