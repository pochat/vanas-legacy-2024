<?php

class Log {

    /**
     * EGMC 20150915
     * Es la variable que se utiliza para saber si 
     * la clase ya fue instanciada que sirve para patrón singleton
     * @var instance de la clase
     */
    private static $instance;

    /**
     * EGMC 20150915
     * contructor privado para aplicar patrón singleton
     */
    private function __construct() {
        
    }

    /**
     * EGMC 20150915
     * Aplica patrón singleton
     * @return class regresa la instancia del objeto
     */
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    /**
     * EGMC
     * Agrega a un archivo .log la inexistencia de las etiquetas
     * @param string $name
     * @param string $text
     * @param string $nameLog
     * @param string $pathLog
     * @return boolean
     */
    public function errorLabels($name, $text, $nameLog = null, $pathLog = null) {

        if ($pathLog == null) {
            $pathLog = PATH_ADM_LOGS . DS;
        }

        if ($nameLog == null) {
            $nameLog = 'error_labels_' . date('Y-m-d') . '.log';
        }
        
        return $this->writeLog($name, $text, $nameLog, $pathLog);
    }
    
    private function writeLog($name, $text, $nameLog = null, $pathLog = null)
    {
        if ($pathLog == null) {
            $pathLog = PATH_ADM_LOGS . DS;
        }

        if ($nameLog == null) {
            $nameLog = 'data_' . date('Y-m-d') . '.log';
        }
        
        $backtace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $backtace = array_pop($backtace);

        $fileLog = fopen($pathLog . $nameLog, 'a');
        fwrite($fileLog, '[' . date('r') . '] ' . $backtace['file'] . ' (' . $backtace['line'] . ') => ' . $name . ': ' . $text . PHP_EOL);
        fclose($fileLog);
        return true;
    }

}
