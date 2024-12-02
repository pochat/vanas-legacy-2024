<?php

require_once PATH_ADM_VENDOR . '/autoload.php';

//
date_default_timezone_set('UTC');
define('CLI', (PHP_SAPI == 'cli') ? true : false);
define('EOL', CLI ? PHP_EOL : '<br />');
define('SCRIPT_FILENAME', basename($_SERVER['SCRIPT_FILENAME'], '.php'));
define('IS_INDEX', SCRIPT_FILENAME == 'index');

//die;
//Settings::loadConfig();
//use Philo\Blade\Blade;
class PHPWord {

    public $phpWord;
    public $writers = array(
        'Word2007' => 'docx',
        //'ODText' => 'odt', 
        // 'RTF' => 'rtf', 
        'HTML' => 'html',
        'PDF' => 'pdf'
    );

    function __construct() {
        \PhpOffice\PhpWord\Settings::loadConfig();
        \PhpOffice\PhpWord\Settings::setPdfRendererPath(PATH_ADM_LIB . '/tcpdf/tcpdf.php');
        \PhpOffice\PhpWord\Settings::setPdfRendererName('TCPDF');
    }

    public function readTemplate() {
 \PhpOffice\PhpWord\Settings::loadConfig();
        \PhpOffice\PhpWord\Settings::setPdfRendererPath(PATH_ADM_LIB . '/tcpdf/');
        \PhpOffice\PhpWord\Settings::setPdfRendererName('TCPDF');
        
       Dbg::data(\PhpOffice\PhpWord\Settings::getPdfRendererPath());
        
        $this->phpWord = \PhpOffice\PhpWord\IOFactory::load(PATH_ADM_TEMPLATES . DS . 'doc' . DS . 'test.docx');

// Save file
        echo $this->write($this->phpWord, basename(__FILE__, '.php'), $this->writers);
    }

    /**
     * Write documents
     *
     * @param \PhpOffice\PhpWord\PhpWord $phpWord
     * @param string $filename
     * @param array $writers
     *
     * @return string
     */
    private function write($phpWord, $filename, $writers) {
        $result = '';


        // Write documents
        foreach ($writers as $format => $extension) {
            $result .= date('H:i:s') . " Write to {$format} format";
            if (null !== $extension) {
                $targetFile = __DIR__ . "/{$filename}.{$extension}";
                $phpWord->save($targetFile, $format);
            } else {
                $result .= ' ... NOT DONE!';
            }
            $result .= EOL;
        }

        $result .= $this->getEndingNotes($writers);

        return $result;
    }

    /**
     * Get ending notes
     *
     * @param array $writers
     *
     * @return string
     */
    private function getEndingNotes($writers) {
        $result = '';

        // Do not show execution time for index
        if (!IS_INDEX) {
            $result .= date('H:i:s') . " Done writing file(s)" . EOL;
            $result .= date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB" . EOL;
        }

        // Return
        if (CLI) {
            $result .= 'The results are stored in the "results" subdirectory.' . EOL;
        } else {
            if (!IS_INDEX) {
                $types = array_values($writers);
                $result .= '<p>&nbsp;</p>';
                $result .= '<p>Results: ';
                foreach ($types as $type) {
                    if (!is_null($type)) {
                        $resultFile = 'results/' . SCRIPT_FILENAME . '.' . $type;
                        if (file_exists($resultFile)) {
                            $result .= "<a href='{$resultFile}' class='btn btn-primary'>{$type}</a> ";
                        }
                    }
                }
                $result .= '</p>';
            }
        }

        return $result;
    }

}
