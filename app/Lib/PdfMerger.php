<?php
require_once('pdf_merger/autoload.php');

use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;

class PdfMerger
{
    public function merge($files)
    {
        $m = new Merger();

        foreach ($files as $key => $file) {
            if (strlen($file) > 0) {
                $m->addRaw($file);
            }
        }

        return $m->merge();
    }
}
