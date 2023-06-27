<?php
/*
@author Eric Martins @rockingeric July 2012
Uploader Class
|============================|
Class built and integrated to CakePHP to a uploader
*/
class UploaderComponent extends Component
{
    public function up($arq, $path)
    {
        $arq['name'] = time().'_'.$arq['name'];
        if (move_uploaded_file($arq['tmp_name'], $path.$arq['name'])) {
            return ['nome' => $arq['name']];
        }

        exit('Erro ao mover arquivo');
    }
}
