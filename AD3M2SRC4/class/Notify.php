<?php

class Notify {

    /**
     * EGMC
     * Imprime el mensaje guardado en sesión 
     */
    public static function sessionMessage() {
        if (isset($_SESSION['Notification']['Message']) && $_SESSION['Notification']['Message'] != '') {
            echo $_SESSION['Notification']['Message'];
        }
        $_SESSION['Notification']['Message'] = '';
    }

    /**
     * EGMC
     * Guarda en variable de sesión el mensaje pasado por la variable
     * @param string $message texto o html a visualizar
     */
    public static function setMessage($message = '') {
        $_SESSION['Notification']['Message'] = $message;
        //Dbg::data($_SESSION['Notification']['Message']);
    }

    /**
     * EGMC
     * Guarda en variable de sesión el mensaje pasado por la variable
     * @param string $message mensaje que se va a mostrar 
     * @param string $type tipo de mensage info, succes, warning o error
     * @param string $title título que se muestra
     */
    private static function _setMessage($message = '', $strong = '', $type = 'info-circle', $icon = 'info') {
        $_SESSION['Notification']['Message'] = '<div class="alert alert-' . $type . ' fade in">'
                . '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                . ($icon == null ? '' : '<i class="fa fa-' . $icon . ' fa-fw fa-lg"></i> ')
                . ($strong != '' ? '<strong>' . $strong . '</strong>&nbsp;' : '' )
                . $message
                . '</div>';
    }

    /**
     * EGMC
     * Asigna un mensaje informativo
     * @param string $message
     * @param string $strong
     * @param string $icon
     */
    public function info($message = '', $strong = '', $icon = 'info-circle') {
        self::_setMessage($message, $strong, 'info', $icon);
    }

    /**
     * EGMC
     * Asigna un mensaje éxito 
     * @param string $message 
     * @param string|fsla $strong
     * @param string $icon
     */
    public function success($message = '', $strong = '', $icon = 'check-circle') {

        if ($message == '') {
            $message = ObtenEtiqueta(223);
        }
        if ($strong == '' && $strong !== false) {
            $strong = ObtenEtiqueta(23);
        }

        self::_setMessage($message, $strong, 'success', $icon);
    }

    /**
     * EGMC
     * Asigna un mensaje de advertencia
     * @param string $message
     * @param string $strong
     * @param string $icon
     */
    public function warning($message = '', $strong = '', $icon = 'warning') {
        self::_setMessage($message, $strong, 'warning', $icon);
    }

    /**
     * EGMC
     * Asigna un mensaje de error
     * @param string $message
     * @param string $strong
     * @param string $icon
     */
    public function error($message = '', $strong = '', $icon = 'times-circle') {

        if ($message == '') {
            $message = ObtenEtiqueta(222);
        }
        if ($strong == '' && $strong !== false) {
            $strong = ObtenEtiqueta(22);
        }

        self::_setMessage($message, $strong, 'danger', $icon);
    }

}
