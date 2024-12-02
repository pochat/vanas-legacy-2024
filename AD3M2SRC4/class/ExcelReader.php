<?php
require_once PATH_ADM_LIB . DS . 'Excel/spreadsheet.excel.reader.php';

class ExcelReader {

    function readXls($path = null) {
        $spreadsheetExcelReader = new Spreadsheet_Excel_Reader();

        $spreadsheetExcelReader->setOutputEncoding('CP1251');
        ini_set('memory_limit', '512M');
        //ini_set('post_max_size', '128M');
        //ini_set('upload_max_filesize', '128M');
        $spreadsheetExcelReader->read($path);
        $data = $spreadsheetExcelReader->sheets;

        return $data[0];
    }

}

?>