<?php

/**
 * 
 */
class KSession {

    public static function resetMain() {
        unset($_SESSION['Usuario'], $_SESSION['Condominio'], $_SESSION['Cuenta']);
    }

    public function pMain($usuario = true, $condominio = true, $cuenta = true) {
        Dbg::data(self::printSession($usuario, $condominio, $cuenta), 3);
    }

    public static function pdMain($usuario = true, $condominio = true, $cuenta = true) {

        Dbg::pd(self::printSession($usuario, $condominio, $cuenta), 3);
    }

    private static function printSession($usuario = true, $condominio = true, $cuenta = true) {

        $data = array();
        if ($usuario && isset($_SESSION['Usuario'])) {
            $data['Usuario'] = $_SESSION['Usuario'];
        }

        if ($condominio && isset($_SESSION['Condominio'])) {
            $data['Condominio'] = $_SESSION['Condominio'];
        }

        if ($cuenta && isset($_SESSION['Cuenta'])) {
            $data['Cuenta'] = $_SESSION['Cuenta'];
        }

        return $data;
    }

}
