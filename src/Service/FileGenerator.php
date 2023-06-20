<?php
// src/Service/FileGenerator.php
namespace App\Service;

use Dompdf\Dompdf;

/**
 * Class Tools
 * @package App\Service
 */
class FileGenerator
{
    /**
     * @param string $filename
     * @param string $content
     * @param bool $letter
     * @return string
     */
    public function pdfGenerator(string $filename, string $content, bool $letter = true): string
    {
        $dompdf = new Dompdf();

        $options = $dompdf->getOptions();

        $options->setIsRemoteEnabled(true);

        $dompdf->setOptions($options);
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', $letter ? 'letter' : 'landscape');

        $dompdf->render();

        file_put_contents($filename, $dompdf->output());

        return $filename;
    }
}
