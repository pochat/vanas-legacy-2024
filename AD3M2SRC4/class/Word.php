<?php

require_once PATH_ADM_LIB . DS . 'PHPWord-0.6.3' . DS . 'PHPWord.php';

/**
 * 
 */
class Word {

    public $phpWord = '';
    public $data = array();
    public $pathTemplates = '';

    /**
     * EGMC
     * @param array $data
     */
    public function __construct($data = array()) {

//        $this->phpWord = new PHPWord();
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
        $this->pathTemplates = PATH_ADM_TEMPLATES . '/doc';

        /**
         * EGMC 
         * Se crea el objeto
         */
        $this->phpWord = new PHPWord();

        $this->data = $data + array(
            'Properties' => array(
                'Creator' => 'Loomtek',
                'LastModifiedBy' => 'Kondominea',
                'Title' => 'Templete Kondominea',
                'Subject' => '',
                'Description' => "Template kondominea",
                'Keywords' => 'kondominea, templete, word',
                'Category' => 'Templete',
                'Company' => 'Loomtek'
            )
        );


        /**
         * EGMC 
         * Se asignan las propiedades del documento
         */
        $this->setDocumentProperties($this->phpWord, $this->data['Properties']);
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
     * Asignamos las propiedades del documento al objeto phpWord
     * @param PHPWord $phpWord
     * @param array $documentProperties
     */
    public function setDocumentProperties(&$phpWord, &$documentProperties) {
        $phpWord->getProperties()
                ->setCreator($this->data['Properties']['Creator'])
                ->setLastModifiedBy($documentProperties['LastModifiedBy'])
                ->setTitle($documentProperties['Title'])
                ->setSubject($documentProperties['Subject'])
                ->setDescription($documentProperties['Description'])
                ->setKeywords($documentProperties['Keywords'])
                ->setCategory($documentProperties['Category'])
                ->setCompany($documentProperties['Company']);
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
        $this->phpWord = $this->load($pathTemplate);
        if ($this->phpWord !== false) {
            /**
             * EGMC 
             * Se asignan las propiedades del documento
             */
            $this->setDocumentProperties($this->phpWord, $this->data['Properties']);
        }
        return $this->phpWord;
    }

    public function save($pathSave, $writeType = 'Excel2007', PHPWord $phpWord = null) {

        if ($phpWord != null) {
            $this->phpWord = $phpWord;
        }

        $objWriter = PHPWord_IOFactory::createWriter($this->phpWord, $writeType);
        $objWriter->save($pathSave);
    }

    public function loadTempleteReplaceSave($pathTemplate, $dataToReplace, $pathFileToSave = false, $download = false) {

        if (file_exists($pathTemplate)) {
            try {
                /**
                 * EGMC 20160120
                 * Carga el template
                 */
                $documentWord = $this->phpWord->loadTemplate($pathTemplate);

                /**
                 * EGMC 20160120
                 * Reemplaza los datos
                 */
                foreach ($dataToReplace as $search => $replace) {
                    $documentWord->setValue13($search, $replace);
                }

                /**
                 * EGMC 20160120
                 * Guarda el archivo
                 */
                if ($pathFileToSave !== false) {
                    
                    $documentWord->save($pathFileToSave);
//                    Dbg::pd($documentWord);

                    /**
                     * EGMC 20160120
                     * descarga de archivo
                     */
                    if ($download) {
                        header('Location: /' . str_replace($_SERVER['DOCUMENT_ROOT'],'', $pathFileToSave));

                        exit;
                    }
                    return $pathFileToSave;
                }
                return $documentWord;
            } catch (Exception $e) {
                throw new Exception('Internal error PHPWord :(');
            }
            return $this->phpWord;
        }
        return false;
    }

}
