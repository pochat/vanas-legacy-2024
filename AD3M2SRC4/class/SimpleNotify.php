<?php

class SimpleNotify {

    /**
     * EGMC
     * Regresa HTML de mensaje pasado por la variable
     * @param string $message mensaje que se va a mostrar 
     * @param string $type tipo de mensage info, succes, warning o error
     * @param string $title título que se muestra
     */
    private static function _setMessage($message = '', $strong = '', $type = 'info-circle', $icon = 'info') {
       return '<div class="alert alert-' . $type . ' fade in">'
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
       return self::_setMessage($message, $strong, 'info', $icon);
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

        return self::_setMessage($message, $strong, 'success', $icon);
    }

    /**
     * EGMC
     * Asigna un mensaje de advertencia
     * @param string $message
     * @param string $strong
     * @param string $icon
     */
    public function warning($message = '', $strong = '', $icon = 'warning') {
        return self::_setMessage($message, $strong, 'warning', $icon);
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

        return self::_setMessage($message, $strong, 'danger', $icon);
    }

}
