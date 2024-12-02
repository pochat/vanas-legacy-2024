<?php

class DataBase {

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

    private function _considerations() {
        if (isset($_POST['iDisplayLength']) && $_POST['iDisplayLength'] == -1) {
            $_POST['iDisplayLength'] = 1300000000;
        }
    }

}
