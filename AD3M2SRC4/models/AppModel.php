<?php

/**
 * EGMC 20160525
 * Para palicar ORM de LARAVEL
  require_once PATH_ADM_CONFIG . DS . 'database.php';

  abstract class AppModel extends Illuminate\Database\Eloquent\Model {

  const CREATED_AT = 'fe_alta';
  const UPDATED_AT = 'fe_ultmod';
  public static $validateRules = array();
 */
class AppModel {

    /**
     * Regresa el objeto conección con la base de datos
     * 
     * @return object objeto mysqli conector
     */
    private static function _getConnection() {
//        $dbConn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $dbConn = mysqli_connect(DATABASE_SERVER, DATABASE_USER, DATABASE_PWD, DATABASE_NAME);

        $dbConn->set_charset("utf8");

        return $dbConn;
    }

    /**
     * Executes a simple query and returns the resultset
     * 
     * @param string $query contiene el query que se va a ejecutar
     * @return object mysqli_result
     * 
     */
    public static function simpleQuery($query) {
        $conn = self::_getConnection();

        $_SESSION['DB']['Execute'][] = $query;

        $results = $conn->query($query);

        $conn->close();

        return self::convertToArray($results);
    }

    /**
     * EGMC
     * Convierte los resultados del objeto mysql en arreglo de datos
     * @param object $results
     * @return array
     */
//    private function convertToArray($results) {
//
//        $data = array();
//        if (!empty($results)) {
//            if (!is_bool($results)) {
//                foreach ($results as $rslt) {
//                    $data[] = $rslt;
//                }
//            } else
//                return $results;
//        }
//        
////        Dbg::pd($data);
//        
//        return $data;
//    }
    private static function convertToArray($results) {

        $data = array();
        if (0 < $results->num_rows) {
            if (!is_bool($results)) {
//                foreach ($results as $rslt) {
                while ($rslt = mysqli_fetch_assoc($results)) {
                    $data[] = $rslt;
                }
            } else
                return $results;
        }

        return $data;
    }

    /**
     * Executes a multiquery and returns all the resultsets in an array
     * 
     * @param string $query contiene el query que se va a ejecutar
     * @return object mysqli_result
     */
    public static function multiQuery($query) {
        $conn = self::_getConnection();

        $_SESSION['DB']['Execute'][] = $query;

        $conn->multi_query($query);

        $result = array();

        do {
            array_push($result, self::convertToArray($conn->store_result()));
        } while ($conn->next_result());

        $conn->close();

        return $result;
    }

    public function getFields($nameTable = '', $empty = true) {

        if ($nameTable == '') {
            $model = get_called_class();
            $obtModel = new $model;
            $nameTable = $obtModel->getTable();
        }

        $data = array();
        $rslts = self::simpleQuery('SHOW COLUMNS FROM ' . $nameTable . ';');

        foreach ($rslts as $rslt) {
            $data[] = $rslt['Field'];
        }

        if ($empty) {
            $data = array_fill_keys($data, '');
        }
        return $data;
    }

    private function _considerations() {
        if (isset($_POST['iDisplayLength']) && $_POST['iDisplayLength'] == -1) {
            $_POST['iDisplayLength'] = 1300000000;
        }
    }

    public function __destruct() {
        
    }

    public function killProcess() {
        $data = self::simpleQuery('SHOW FULL PROCESSLIST');
        foreach ($data as $dt) {
            if ($dt['Time'] > 200) {
                Dbg::data($dt);
                self::simpleQuery('KILL ' . $dt['Id']);
            }
        }

        return true;
    }

}
