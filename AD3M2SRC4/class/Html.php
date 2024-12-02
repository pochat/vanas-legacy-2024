<?php

/**
 * 
 */
class Html {

    /**
     * EGMC 20150702
     * Es la variable que se utiliza para saber si 
     * la clase ya fue instanciada sirve para patrón singleton
     * @var instance de la clase Html
     */
    protected static $instance;

    /**
     * EGMC 20150702
     * Lista de elementos HTML
     * que no tienen contenido
     * @var array 
     */
    private $voidElements = array(
        // html4
        'area' => 'area',
        'base' => 'base',
        'br' => 'br',
        'col' => 'col',
        'hr' => 'hr',
        'img' => 'img',
        'input' => 'input',
        'link' => 'link',
        'meta' => 'meta',
        'param' => 'param',
        // html5
        'command' => 'command',
        'embed' => 'embed',
        'keygen' => 'keygen',
        'source' => 'source',
        'track' => 'track',
        'wbr' => 'wbr',
        // html5.1
        'menuitem' => 'menuitem',
    );
    public $attrsBase = array('class' => '');
    public $baseTagParams = array(
        'content' => '',
        'attributes' => array(),
        'options' => array()
    );
    public $attrsInfoPopover = array(
        'title' => '',
        'data-original-title' => '',
        'data-content' => '',
        'data-container' => 'body',
        'class' => 'fa fa-info-circle iconInfoPopover',
        'data-toggle' => 'popover',
        'data-placement' => 'top'
    );
    public $attrsInfoTootip = array(
        'title' => '',
        'data-original-title' => '',
        'class' => 'fa fa-info-circle iconInfoTootip',
        'data-toggle' => 'popover',
        'data-placement' => 'top'
    );
    public $attrsButton = array(
        'class' => 'btn btn-default'
    );

    /**
     * EGMC 20150703
     * @var array elementos de visibilidad para dar formato alos grids de bootstrap     
     */
    protected $gridVH = array('visible' => 'visible', 'hidden' => 'hidden');

    /**
     * EGMC 20150702
     * opciones extra para elementos
     *      array grid 
     *               array('xs' => 12, 'sm' => 6, 'md' => 3, 'lg' => 3)
     *      string before cadena que se inserta antes del elemento
     *      string after datos que se inertan después del elemento
     * @var array 
     */
    protected $extraOptions = array(
        'grids' => array(),
        'before' => '',
        'after' => '',
        'content' => array(
            'before' => '',
            'after' => '')
    );

    /**
     * EGMC 20150702
     * contructor privado para aplicar patrón singleton
     */
    private function __construct() {
//        Dbg::data('Se creó class HTML');
    }

    /**
     * EGMC 20150702
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
     * Regresa un tag de HTML
     * @param string $name nombre del tag html
     * @param array $attributes atributos que contendrá el tag
     * @param string $content contenido que tendrá el tag
     * @param array $options datos extra
     *              before => html que se inserta antes
     *              after => html que se inerta después
     * @return string tag formado
     */
    public function tag($name, $attributes = array(), $content = '', $options = array()) {

        $options += $this->extraOptions;

        $name = strtolower($name);

        $tag = '<' . $name;

        if (!empty($attributes)) {
            $tag .= ' ' . General::arrayToAttrs($attributes);
        }

        if (isset($this->voidElements[$name])) {
            return $tag . ' />' . "\r\n";
        }

        return $options['before'] . $tag . '>' . $options['content']['before'] . $content . $options['content']['after'] . '</' . $name . '>' . "\r\n" . $options['after'];
        return $options['before'] . $tag . '>' . "\r\n" . $options['content']['before'] . $content . $options['content']['after'] . "\r\n" . '</' . $name . '>' . "\r\n" . $options['after'];
    }

    /**
     * EGMC 20150702
     * Regresa cadena con metatags
     * @param string|array multiple inputs or name/http-equiv value
     * @param string content value
     * @param string name or http-equiv
     * @return string
     */
    public function meta($name = '', $content = '', $type = 'name') {
        if (!is_array($name)) {
            $result = $this->tag('meta', array($type => $name, 'content' => $content));
        } elseif (is_array($name)) {
            $result = "";
            foreach ($name as $array) {
                $meta = $array;
                $result .= $this->tag('meta', $meta);
            }
        }
        return $result;
    }

    /**
     * Imprime los links de las hojas estilo <link type="text/css" rel="stylesheet" href="urlFiles" /> 
     * que se encuentran en public/css
     * @param mix $pathFiles nombre(s) de archivo(s)
     */
//    public function css($pathFiles = null) {
//        $data = '';
//        if ($pathFiles != null) {
//            if (is_array($pathFiles)) {
//                foreach ($pathFiles as $name) {
//                    $parseUrl = parse_url($name, PHP_URL_HOST);
//                    if ($parseUrl == null) {
//                        $data .= '<link type="text/css" rel="stylesheet" href="' . PATH_CSS . '/' . $name . '" />';
//                    } elseif (is_string($parseUrl)) {
//                        $data .= '<link type="text/css" rel="stylesheet" href="' . $name . '" />';
//                    }
//                }
//            } else {
//                $parseUrl = parse_url($pathFiles, PHP_URL_HOST);
//                if ($parseUrl == null) {
//                    $data .= '<link type="text/css" rel="stylesheet" href="' . PATH_CSS . '/' . $pathFiles . '" />';
//                } elseif (is_string($parseUrl)) {
//                    $data .= '<link type="text/css" rel="stylesheet" href="' . $pathFiles . '" />';
//                }
//            }
//        }
//        return $data;
//    }

    /**
     * Imprime los links de las hojas estilo <link type="text/css" rel="stylesheet" href="urlFiles" /> 
     * que se encuentran en public/css
     * @param mix $pathFiles ruta y nombre(s) de archivo(s)
     */
//    public function font($pathFiles = null) {
//        $data = '';
//        if ($pathFiles != null) {
//            if (is_array($pathFiles)) {
//                foreach ($pathFiles as $name) {
//                    $parseUrl = parse_url($name, PHP_URL_HOST);
//                    if ($parseUrl == null) {
//                        $data .= '<link type="text/css" rel="stylesheet" href="' . PATH_FONT . '/' . $name . '" />';
//                    } elseif (is_string($parseUrl)) {
//                        $data .= '<link type="text/css" rel="stylesheet" href="' . $name . '" />';
//                    }
//                }
//            } else {
//                $parseUrl = parse_url($pathFiles, PHP_URL_HOST);
//                    if ($parseUrl == null) {
//                    $data .= '<link type="text/css" rel="stylesheet" href="' . PATH_FONT . '/' . $pathFiles . '" />';
//                } elseif (is_string($parseUrl)) {
//                    $data .= '<link type="text/css" rel="stylesheet" href="' . $pathFiles . '" />';
//                }
//            }
//        }
//        return $data;
//    }

    /**
     * Imprime los links de los javascripts <script type="text/javascript" src="urlFiles"></script>
     * que se encuentran en public/js
     * @param string|array $urlFiles nombre(s) de archivo(s)
     */
//    public function js($urlFiles = null) {
//        $data = '';
//        if ($urlFiles != null) {
//            if (is_array($urlFiles)) {
//                foreach ($urlFiles as $name) {
//                    $parseUrl = parse_url($name, PHP_URL_HOST);
//                    if ($parseUrl == null) {
//                        $data .= '<script type="text/javascript" src="' . PATH_JS . '/' . $name . '"></script>';
//                    } elseif (is_string($parseUrl)) {
//                        $data .= '<script type="text/javascript" src="' . $name . '"></script>';
//                    }
//                }
//            } else {
//                $parseUrl = parse_url($urlFiles, PHP_URL_HOST);
//                if ($parseUrl == null) {
//                    $data .= '<script type="text/javascript" src="' . PATH_JS . '/' . $urlFiles . '"></script>';
//                } elseif (is_string($parseUrl)) {
//                    $data .= '<script type="text/javascript" src="' . $urlFiles . '"></script>';
//                }
//            }
//        }
//        return $data;
//    }

    /**
     * EGMC 20150703 
     * Regresa un tag HTML div
     * @param string $content contenido del div
     * @param array $attributes atributos que tendrá el div
     * @param array $options opciones extra before, after y grids
     * @return string cadena con div formado
     */
    public function div($content, $attributes = array(), $options = array()) {

        $attributes += $this->attrsBase;
        $options +=$this->extraOptions;
        /**
         * EGMC 20150707
         * si hay grids en options damos agregamos al class
         */
        if (!empty($options['grids'])) {
            $attributes['class'].=$this->formatGrids($options['grids']);
        }
        return $this->tag('div', $attributes, $content, $options);
    }

    /**
     * EGMC 20150703
     * Regresa un tag HTML label (etiqueta)
     * @param type $content contenido de la etiqueta
     * @param type $attributes atributos que tendrá la etiqueta
     * @param type $icon ícono que contendrá la etiqueta
     * @param type $infoPopover
     * @param type $options
     * @return type
     */
    public function label($content, $attributes = array(), $icon = array(), $infoPopover = array(), $options = array()) {

        $htmlLabelIcon = $htmlInfoPopover = '';
        //INICIA CONSTRUCCIÓN DE ICONO
        if (!empty($icon)) {
            /**
             * Sin clase más eficiente
             */
            $htmlLabelIcon = '<i class="fa fa-' . $icon . '"></i>&nbsp;' . "\r\n";
            /*
             * Con clase menos eficiente
             */
            //$htmlLabelIcon = $this->tag('i', array('class' => 'fa fa-' . $icon)) . '&nbsp;';
        }
        //FIN CONSTRUCCIÓN DE ICONO
        //INICIA CONSTRUCCIÓN DE POPOVER INFORMATIVO
        if (!empty($infoPopover)) {

            if (is_string($infoPopover)) {

                $infoPopover = (array) $infoPopover;
                $infoPopover['data-content'] = $infoPopover[0];
                unset($infoPopover[0]);
//                $infoPopover['data-original-title'] = $content;
            }
            $infoPopover+=$this->attrsInfoPopover;
            $htmlInfoPopover = '&nbsp;&nbsp;' . $this->tag('i', $infoPopover);
        }
        //FIN CONSTRUCCIÓN DE POPOVER INFORMATIVO

        $attributes+=$this->attrsBase;
        $options+=$this->extraOptions;

        /**
         * EGMC 20150707
         * si hay grids en options damos agregamos al class
         */
        if (!empty($options['grids'])) {
            $attributes['class'].=$this->formatGrids($options['grids']);
        }

        return $this->tag('label', $attributes, $options['before'] . $htmlLabelIcon . $content . $htmlInfoPopover . $options['after']);
    }

    /**
     * EGMC 20150702
     * Regresa la cadena formada para grids de Bootstrap
     * 
     * Entrada: 
     *      array(
     *          'xs' => 12, 
     *          'sm' => array(6, 'offset-3'),
     *          'md' => 4,
     *          'lg' => 'hidden'));
     * Salida:
     *      "col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 hidden-lg"
     * 
     * @param array $grids arreglo con los datos
     * @return string grid formado para agregar al class del tag de html
     */
    public function formatGrids($grids = array()) {
        if (empty($grids)) {
            return '';
        }
        $grid = array();
        foreach ($grids as $type => $value) {
            foreach ((array) $value as $subValue) {
                if (!isset($this->gridVH[$subValue])) {
                    $grid[] = 'col-' . $type . '-' . $subValue;
                } else {
                    $grid[] = $subValue . '-' . $type;
                }
            }
        }

        return ' ' . implode(' ', $grid) . ' ';
    }

    /**
     * EGMC 20150706
     * Regresa un tag HTML button
     * @param type $content
     * @param type $attributes
     * @param type $options
     * @return type
     */
    public function tagButton($content, $attributes = array(), $options = array()) {

        $attributes += $this->attrsButton;
        $options +=$this->extraOptions;

        return $this->tag('button', $attributes, $content, $options);
    }

    /**
     * EGMC 20150706
     * Regresa un tag button con icon dentro del botón 
     * 
     * @param string $label Etiqueta del botón
     * @param string|array|false $icon Icon que contendrá el botón
     * @param array $attributes atributos extra del botón
     * @param array $options opciones extra del botón
     * @return string tag HTML de botón
     * <!-- HTML de $options['before'] ->
     * <button class="btn btn-default">
     *     <!-- Icono -->
     *     <span class="fa fa-icon"></span>
     *     Label Button
     * </button>
     * <!-- HTML de $options['after'] ->
     */
    public function button($label = 'Label', $icon = '', $attributes = array(), $options = array()) {

        $htmlIcon = '';

        if (!empty($icon)) {
            if (is_array($icon)) {

                $icon += $this->baseTagParams; //array('content' => '', 'atrributes' => array(), 'options' => array());
                $htmlIcon = $this->tag('span', $icon['content'], $icon['attributes'], $icon['options']);
            } else {
                $htmlIcon = '<span class="fa fa-' . $icon . '">&nbsp;</span>' . "\r\n";
            }
        }

        $options+= $this->extraOptions;

        return $this->tagButton($htmlIcon . $label, $attributes, $options);
    }

}
