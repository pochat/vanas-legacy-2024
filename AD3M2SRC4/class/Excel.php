<?php

require_once PATH_ADM_LIB . DS . 'PHPExcel-1.8' . DS . 'PHPExcel.php';

/**
 * 
 */
class Excel {

    public $phpExcel = '';
    public $data = array();
    public $pathTemplates = '';

    /**
     * EGMC
     * @param array $data
     */
    public function __construct($data = array()) {

//        $this->phpExcel = new PHPExcel();
//        $this->setData($data);
        $this->initialize($data);
    }

    /**
     * EGMC
     * Inicializa los datos base
     */
    protected function initialize($data = array()) {
        /**
         * EGMC 
         * Se asigan la ruta de los templates por default
         */
        $this->pathTemplates = PATH_ADM_TEMPLATES . '/xls';

        /**
         * EGMC 
         * Se crea el objeto
         */
        $this->phpExcel = new PHPExcel();

        $this->data = $data + array(
            'Properties' => array(
                'Creator' => 'Loomtek',
                'LastModifiedBy' => 'Kondominea',
                'Title' => 'Carga por lotes',
                'Subject' => '',
                'Description' => "Template kondominia para carga masiva de datos",
                'Keywords' => 'kondominea, carga masiva de datos',
                'Category' => 'Carga masiva'
            )
        );


        /**
         * EGMC 
         * Se asignan las propiedades del documento
         */
        $this->setDocumentProperties($this->phpExcel, $this->data['Properties']);
    }

    /**
     * EGMC
     * Actualiza los Datos
     * @param array $data
     * @return object Excel
     */
    public function setData($data = null) {
        if ($data) {
            $this->data = $data + $this->data;
        }
        return $this;
    }

    /**
     * EGMC
     * Asignamos las propiedades del documento al objeto phpExcel
     * @param PHPExcel $phpExcel
     * @param array $documentProperties
     */
    public function setDocumentProperties(&$phpExcel, &$documentProperties) {
        $phpExcel->getProperties()
                ->setCreator($this->data['Properties']['Creator'])
                ->setLastModifiedBy($documentProperties['LastModifiedBy'])
                ->setTitle($documentProperties['Title'])
                ->setSubject($documentProperties['Subject'])
                ->setDescription($documentProperties['Description'])
                ->setKeywords($documentProperties['Keywords'])
                ->setCategory($documentProperties['Category']);
    }

    /**
     * 
     * @param type $pathTemplate ruta del archivo template a cargar en el objeto
     * @param type $readerType Tipo de lectura ejemplo:
     *      Excel2007 para archivos xlsx
     *      Excel5    para archivos xls
     * @return type
     */
    public function loadTemplate($pathTemplate) {
        /**
         * EGMC 
         * Se carga el archivo
         */
        $this->phpExcel = $this->load($pathTemplate);
        if ($this->phpExcel !== false) {
            /**
             * EGMC 
             * Se asignan las propiedades del documento
             */
            $this->setDocumentProperties($this->phpExcel, $this->data['Properties']);
        }
        return $this->phpExcel;
    }

    public function save($pathSave, $writeType = 'Excel2007', PHPExcel $phpExcel = null) {

        if ($phpExcel != null) {
            $this->phpExcel = $phpExcel;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpExcel, $writeType);
        $objWriter->save($pathSave);
    }

    public function load($pathFile) {
//        $inputFileType = PHPExcel_IOFactory::identify($pathTemplate);
//        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
//        $this->phpExcel = $objReader->load($pathTemplate);
//  Read your Excel workbook
        if (file_exists($pathFile)) {
            try {
                $inputFileType = PHPExcel_IOFactory::identify($pathFile);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $this->phpExcel = $objReader->load($pathFile);
            } catch (Exception $e) {
                return false;
//            die('Error loading file "' . pathinfo($pathFile, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            return $this->phpExcel;
        }
        return false;
    }

    public function getDataSheet(PHPExcel $phpExcel = null, $numberSheet = 0, $lowerColumn = 'A', $lowerRow = 1, $highestColumn = null, $highestRow = null) {

        $data = array();

        if ($phpExcel != null) {
            $this->phpExcel = $phpExcel;
        }

        $sheet = $this->phpExcel->getSheet($numberSheet);
        if ($highestRow == null) {
            $highestRow = $sheet->getHighestRow();
        }

        if ($highestColumn == null) {
            $highestColumn = $sheet->getHighestColumn();
        }

//  Loop through each row of the worksheet in turn
        for ($row = $lowerRow; $row <= $highestRow; $row++) {
            //  Read a row of data into an array
            $data[$row] = reset($sheet->rangeToArray($lowerColumn . $row . ':' . $highestColumn . $row, null, true, true, true));
        }

        return $data;
    }

    public function loadFileAndGetAllDataInArray($pathFile = 'cargar_unidades_en_lote_para_vivienda.xlsx', $sheet = 0) {
        $data = array();
        $this->phpExcel = $this->load($pathFile);
        if ($this->phpExcel !== false) {
            $this->phpExcel->getSheet($sheet);

// Get the active sheet as an array
            $data = $this->phpExcel->getActiveSheet()->toArray(null, true, true, true);
        }
//        Dbg::data($data);
        return $data;
    }

    /**
     * 
     * Example: http://stackoverflow.com/questions/9695695/how-to-use-phpexcel-to-read-data-and-insert-into-database
     * @param type $pathFile
     * @param type $numberSheet
     * @param type $lowerColumn
     * @param type $lowerRow
     * @param type $highestColumn
     * @param type $highestRow
     * @return type
     */
    public function loadFileAndGetDataInArray($pathFile = 'cargar_unidades_en_lote_para_vivienda.xlsx', $numberSheet = 0, $lowerColumn = 'A', $lowerRow = 1, $highestColumn = null, $highestRow = null) {

        $data = array();
        /**
         * EGMC 
         * Carga el archivo
         */
        $this->phpExcel = $this->load($pathFile);
        if ($this->phpExcel !== false) {
//  Get worksheet dimensions
            /**
             * EGMC 
             * Obtener los datos
             */
            $data = $this->getDataSheet(null, $numberSheet, $lowerColumn, $lowerRow, $highestColumn, $highestRow);
        }
//        Dbg::data($data);
        return $data;
    }

    /**
     * Agrega una validación
     * @param string $cell
     * @param string $type
     *      'none'
     *      'custom'
     *      'date' 
     *      'decimal'
     *      'list'
     *      'textLength'
     *      'time'
     *      'whole'
     * @param string $operator:
     *      'between'
     *      'equal'
     *      'greaterThan'
     *      'greaterThanOrEqual'
     *      'lessThan'
     *      'lessThanOrEqual'
     *      'notBetween'
     *      'notEqual'
     * @param string|array $formulas
     * @param array $options
     *      'errorStyle' => 'stop' || 'warning' || 'information'
     * @param PHPExcel $phpExcel
     * @param type $numberSheet
     */
    public function addValidation($cell, $type = "", $operator = 'between', $formulas = null, $options = array(), PHPExcel $phpExcel = null, $numberSheet = null) {
        
        if (!is_array($formulas)) {
            $formulas = (array) $formulas;
        }

        $options += array(
            'allowBlank' => true,
            'showInputMessage' => true,
            'showErrorMessage' => true,
            'showDropDown' => false,
            'errorStyle' => 'stop',
            'errorTitle' => 'Input error',
            'error' => 'Value is not valid.',
            'promptTitle' => '',
            'prompt' => ''
        );
        
        
        $data = array();

        if ($phpExcel != null) {
            $this->phpExcel = $phpExcel;
        }
        if ($numberSheet != null) {
            $this->phpExcel->setActiveSheetIndex($numberSheet);
        }
        $objValidation = $this->phpExcel->getActiveSheet()->getCell($cell)->getDataValidation();
        $objValidation->setType($type);

        $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
        $objValidation->setErrorStyle($options['errorStyle']);
        $objValidation->setAllowBlank($options['allowBlank']);
        $objValidation->setShowInputMessage($options['showInputMessage']);
        $objValidation->setShowErrorMessage($options['showErrorMessage']);
        $objValidation->setShowDropDown($options['showDropDown']);
        $objValidation->setErrorTitle($options['errorTitle']);
        $objValidation->setError($options['error']);
        $objValidation->setPromptTitle($options['promptTitle']);
        $objValidation->setPrompt($options['prompt']);

        $f = 1;
        foreach ($formulas as $formula) {
            $funtion = 'setFormula' . $f++;
            $objValidation->$funtion($formula);
            if ($f > 2) {
                break;
            }
        }
        return $this->phpExcel;
        
    }

    /**
     * INTEGRAR CON $this->addValidation
     * @param type $cell
     * @param type $formulas
     * @param type $options
     * @param PHPExcel $phpExcel
     * @param type $numberSheet
     * @return type
     */
    public function addValidationList($cell, $formulas = null, $options = array(), PHPExcel $phpExcel = null, $numberSheet = null) {

        if (!is_array($formulas)) {
            $formulas = (array) $formulas;
        }

        $options += array(
            'allowBlank' => false,
            'showInputMessage' => true,
            'showErrorMessage' => true,
            'showDropDown' => true,
            'errorTitle' => 'Input error',
            'error' => 'Value is not in list.',
            'promptTitle' => 'Pick from list',
            'prompt' => 'Please pick a value from the drop-down list.'
        );

        $data = array();

        if ($phpExcel != null) {
            $this->phpExcel = $phpExcel;
        }
        if ($numberSheet != null) {
            $this->phpExcel->setActiveSheetIndex($numberSheet);
        }
        $objValidation = $this->phpExcel->getActiveSheet()->getCell($cell)->getDataValidation();
        $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);

//        $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
        $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_STOP);
        $objValidation->setAllowBlank($options['allowBlank']);
        $objValidation->setShowInputMessage($options['showInputMessage']);
        $objValidation->setShowErrorMessage($options['showErrorMessage']);
        $objValidation->setShowDropDown($options['showDropDown']);
        $objValidation->setErrorTitle($options['errorTitle']);
        $objValidation->setError($options['error']);
        $objValidation->setPromptTitle($options['promptTitle']);
        $objValidation->setPrompt($options['prompt']);

        $f = 1;
        foreach ($formulas as $formula) {
            $funtion = 'setFormula' . $f++;
            $objValidation->$funtion($formula);
            if ($f > 2) {
                break;
            }
        }
        return $this->phpExcel;
    }

    /**
     * 
     * @param type $cell
     * @param string $operator:
     *      'between'
     *      'equal'
     *      'greaterThan'
     *      'greaterThanOrEqual'
     *      'lessThan'
     *      'lessThanOrEqual'
     *      'notBetween'
     *      'notEqual'
     * @param type $formulas
     * @param type $options
     * @param PHPExcel $phpExcel
     * @param type $numberSheet
     * @return type
     */
    public function addValidationDate($cell, $operator = 'between', $formulas = null, $options = array(), PHPExcel $phpExcel = null, $numberSheet = null) {
//Dbg::pd($operator);
        if (!is_array($formulas)) {
            $formulas = (array) $formulas;
        }

        $options += array(
            'allowBlank' => false,
            'showInputMessage' => true,
            'showErrorMessage' => true,
            'showDropDown' => true,
            'errorTitle' => 'Input error',
            'error' => 'Value is not a date.',
            'promptTitle' => 'Pick format date',
            'prompt' => 'Please pick a value with format date d/m/yyyy, example 13/5/2015'
        );
//        Dbg::pd($options);
        $data = array();

        if ($phpExcel != null) {
            $this->phpExcel = $phpExcel;
        }
        if ($numberSheet != null) {
            $this->phpExcel->setActiveSheetIndex($numberSheet);
        }
        $objValidation = $this->phpExcel->getActiveSheet()->getCell($cell)->getDataValidation();
        $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_DATE);
        $objValidation->setOperator($operator);
        $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_STOP);
//        $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
        $objValidation->setAllowBlank($options['allowBlank']);
        $objValidation->setShowInputMessage($options['showInputMessage']);
        $objValidation->setShowErrorMessage($options['showErrorMessage']);
        $objValidation->setShowDropDown($options['showDropDown']);
        $objValidation->setErrorTitle($options['errorTitle']);
        $objValidation->setError($options['error']);
        $objValidation->setPromptTitle($options['promptTitle']);
        $objValidation->setPrompt($options['prompt']);

        $f = 1;
        foreach ($formulas as $formula) {
            $funtion = 'setFormula' . $f++;
            $objValidation->$funtion($formula);
            if ($f > 2) {
                break;
            }
        }
        return $this->phpExcel;
    }

    /**
     * 
     * @param type $cell
     * @param string $operator:
     *      'between'
     *      'equal'
     *      'greaterThan'
     *      'greaterThanOrEqual'
     *      'lessThan'
     *      'lessThanOrEqual'
     *      'notBetween'
     *      'notEqual'
     * @param type $formulas
     * @param type $options
     * @param PHPExcel $phpExcel
     * @param type $numberSheet
     * @return type
     */
    public function addValidationNumber($cell, $operator = 'between', $formulas = null, $options = array(), PHPExcel $phpExcel = null, $numberSheet = null) {

        $options += array(
            'allowBlank' => true,
            'showInputMessage' => true,
            'showErrorMessage' => true,
            'showDropDown' => true,
            'errorTitle' => 'Input error',
            'error' => 'Value is not a number.',
            'promptTitle' => 'Pick number',
            'prompt' => 'Please pick a valid number'
        );

        return $this->addValidation($cell, 'decimal', $operator, $formulas, $options, $phpExcel, $numberSheet);
    }

}
