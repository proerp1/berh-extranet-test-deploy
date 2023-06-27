<?php
require_once APP.'../vendor/autoload.php';
require_once(APP."Lib/dompdf/autoload.inc.php");
// reference the Dompdf namespace
use Dompdf\Dompdf;

class HtmltoPdfComponent extends Component
{
    public $components = ['Session'];

    public function convert_old($html, $name)
    {
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        // $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        //$dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream($name);
    }

    public function convert($html, $name, $return_type)
    {
        try {
            $mpdf = new \Mpdf\Mpdf();

            $mpdf->debug = true;
            $mpdf->pdf_version = '1.7';

            if ($html) {
                $mpdf->WriteHTML($html);
            }

            if ($return_type == 'string') {
                $string = $mpdf->Output($name, 'S');
                return $string;
            } elseif ($return_type == 'download') {
                $mpdf->Output($name, 'D');
            }
        } catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception
            //       name used for catch
            // Process the exception, log, print etc.
            echo $e->getMessage();
        }
    }
}
