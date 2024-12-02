<?php

/**
 * clase para subir y regresar datos para JQUERY FILE UPLOAD
 * 
 */
class FileUpload {

    public $options = array(
        'baseUrl' => '',
        'mkdirMode' => '',
        'name' => '',
        'fileDir' => '',
        'uploadUrl' => '',
        'deleteUrl' => '',
        'thumbnailUrl' => '',
    );
// PHP File Upload error message codes:
// http://php.net/manual/en/features.file-upload.errors.php
    protected $error_messages = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
        'post_max_size' => 'The uploaded file exceeds the post_max_size directive in php.ini',
        'max_file_size' => 'File is too big',
        'min_file_size' => 'File is too small',
        'accept_file_types' => 'Filetype not allowed',
        'max_number_of_files' => 'Maximum number of files exceeded',
        'max_width' => 'Image exceeds maximum width',
        'min_width' => 'Image requires a minimum width',
        'max_height' => 'Image exceeds maximum height',
        'min_height' => 'Image requires a minimum height',
        'abort' => 'File upload aborted',
        'image_resize' => 'Failed to resize image'
    );

    function __construct($options = null) {
        $this->options = array(
            //C:/xampp/htdocs
            'baseUrl' => BASE_PATH,
            'mkdirMode' => 0755,
            'name' => ''/* date('YmdHis') */,
            'extention' => '', //.cid',
            'type' => '',
            'size' => 0,
            //C:\xampp\htdocs\rootKondominea\AD3M2SRC4\media
            'fileDir' => ROOT . DS . ADM_DIRECTORY . DS . 'media',
            //C:/xampp/htdocs/rootKondominea/AD3M2SRC4/media
            'uploadUrl' => PATH_ADM_HOME . '/media',
            'deleteUrl' => PATH_ADM_HOME . '/media',
            'deleteType' => 'DELETE',
            'thumbnailUrl' => '',
            'error' => ''
        );
//        Dbg::pd($this->options);

        if ($options) {
            $this->options = $options + $this->options;
        }
    }

    /**
     * EGMC
     * @param $_FILE $file
     * @param array $options
     * @param bool $jsonResponse
     * @return bool | string | json
     */
    public function upload(&$file, $options = null, $jsonResponse = false) {

//        Dbg::data($file);
//        Dbg::pd($options);
        if ($options) {
            $this->options = $options + $this->options;
        }
        //      print_r($this->options);
        if ($this->options['name'] == '') {
            $this->options['name'] = $this->friendlyURL($file['name']);
        } else {
            $this->options['name'] = $this->friendlyURL($this->options['name']);
        }

        if ($this->options['extention'] != '') {
            $this->options['name'].=$this->options['extention'];
        }

        $this->options['size'] = $file['size'];
        $this->options['type'] = $file['type'];
//        Dbg::pd($this->options);
        //$this->options['size'] = $file['size'];
        //return $this->options;
        if (move_uploaded_file($file['tmp_name'], $this->options['fileDir'] . DS . $this->options['name'])) {
            if ($jsonResponse) {
                $this->getResponseUpload($this->options);
            }
            return $this->options['name'];
        } else {
            $this->options ['error'] = 'Ya valió ' . $file['error'];
            if ($jsonResponse) {
                return $this->getResponseErro($this->options);
            }
            return false;
        }
    }

    public function delete($nameFile, $options = null) {

        if ($options) {
            $this->options = $options + $this->options;
        }

        if (file_exists($this->options['fileDir'] . DS . $nameFile)) {
            if (unlink($this->options['fileDir'] . DS . $nameFile)) {
                return $this->getResponseDelete($nameFile);
            }
            return $this->getResponseError(array('name' => $nameFile,
                        'error' => 'No se pudo borrar el archivo'));
        }
        return $this->getResponseError(array('name' => $nameFile,
                    'error' => 'NO se encontró el archivo en la ruta: ' . $this->options['fileDir'] . DS . $nameFile));
    }

    /**
     * regresa un arreglo con los datos en dos formatos arreglo y string con json
     * de respuestas positiva de que se SUBIÓ el archivo correctamente
     *  
     * @param type $options
     * @return array() regresa arreglo respuesta y string con json
     *  
     * "array" => array(
     *                  "name" => "picture1.jpg", 
     *                  "size" => 902604,
     *                  "url" => "http:\/\/example.org\/files\/picture1.jpg",
     *                  "thumbnailUrl" => "http:\/\/example.org\/files\/thumbnail\/picture1.jpg",
     *                  "deleteUrl" => "http:\/\/example.org\/files\/picture1.jpg",
     *                 ),
     * 
     * "jason" => '{"files": [{
     *                         "name": "picture1.jpg", 
     *                         "size": 902604,
     *                         "url": "http:\/\/example.org\/files\/picture1.jpg",
     *                         "thumbnailUrl": "http:\/\/example.org\/files\/thumbnail\/picture1.jpg",
     *                         "deleteUrl": "http:\/\/example.org\/files\/picture1.jpg",
     *                       }]
     *             }'
     */
    private function getResponseUpload($options) {
        if ($this->options['extention'] == '') {
            $this->options['extention'] = pathinfo($options['name'], PATHINFO_EXTENSION);
        }

        $options ['url'] = $options['uploadUrl'] . '/' . $options['name'];

        if (!isset($options['thumbnailUrl']) || $options['thumbnailUrl'] == '') {

            if (in_array($options['extention'], array('jpg', 'jpeg', 'png'))) {
                $options['thumbnailUrl'] = $this->options['baseUrl'] . '/img/64/img.png';
            } elseif ($options['extention'] == 'pdf') {
                $options['thumbnailUrl'] = $this->options['baseUrl'] . '/img/64/pdf.png';
            } elseif (in_array($options['extention'], array('doc', 'docx'))) {
                $options['thumbnailUrl'] = $this->options['baseUrl'] . '/img/64/doc.png';
            } elseif (in_array($options['extention'], array('xls', 'xlsx'))) {
                $options['thumbnailUrl'] = $this->options['baseUrl'] . '/img/64/xls.png';
            } elseif ($options['extention'] == 'txt') {
                $options['thumbnailUrl'] = $this->options['baseUrl'] . '/img/64/txt.png';
            } else {
                $options['thumbnailUrl'] = $this->options['baseUrl'] . '/img/64/general.png';
            }
        }

        $response['array'] = $options;

        $response['json'] = json_encode(array('files' => array($options)));

        return $response;
    }

    /**
     * regresa un arreglo con los datos en dos formatos arreglo y string con json
     * de respuestas positiva de que se ELIMINÓ el archivo correctamente
     *  
     * @param type $options
     * @return array() regresa arreglo respuesta y string con json
     *  
     * "array" => array(
     *                  "picture1.jpg" => true 
     *                 ),
     * 
     * "jason" => '{"files": [{
     *                         "picture1.jpg": true
     *                       }]
     *             }'
     */
    private function getResponseDelete($fileName) {
        $rspns = array($fileName => true);
        $response['array'] = $rspns;
        $response['json'] = json_encode(array('files' => array($rspns)));

        return $response;
    }

    /**
     * regresa un arreglo con los datos en dos formatos arreglo y string con json
     * de respuestas de error
     *  
     * @param type $options
     * @return array() regresa arreglo respuesta y string con json
     *  
     * "array" => array(
     *                  "name" => "picture1.jpg", 
     *                  "size" => 902604,
     *                  "error" => "falló",
     *                 ),
     * 
     * "jason" => '{"files": [{
     *                         "name": "picture1.jpg", 
     *                         "size": 902604,
     *                         "error": "http:\/\/example.org\/files\/picture1.jpg",
     *                       }]
     *             }'
     */
    private function getResponseError($options) {

        $response['array'] = $options;
        json_encode(array('files' => array($options)));

        return $response;
    }

    /**
     * Create a web friendly URL slug from a string.
     *
     * Although supported, transliteration is discouraged because
     * 1) most web browsers support UTF-8 characters in URLs
     * 2) transliteration causes a loss of information
     *
     * @author Sean Murphy <sean@iamseanmurphy.com>
     * @copyright Copyright 2012 Sean Murphy. All rights reserved.
     * @license http://creativecommons.org/publicdomain/zero/1.0/
     *
     * @param string $str
     * @param array $options
     * @return string
     */
    public function getFriendlyUrl($str, $options = array()) {
// Make sure string is in UTF-8 and strip invalid UTF-8 characters
//        $str = mb_convert_encoding((string) $str, 'UTF-8', mb_list_encodings());
        $defaults = array(
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(),
            'transliterate' => true,
        );
// Merge options
        $options = array_merge($defaults, $options);
        $char_map = array(
// Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',
// Latin symbols
            '©' => '(c)',
// Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
// Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
// Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',
// Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
// Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z',
// Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',
// Latvian
            'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
            'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z'
        );
// Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
// Transliterate characters to ASCII
        if ($options['transliterate']) {

            $str = str_replace(array_keys($char_map), $char_map, $str);
        }



// Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
// Remove duplicate delimiters
        echo $str . '2<br>';

        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
// Truncate slug to max. characters
        echo $str . '3<br>';


        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
// Remove delimiter from ends
        $str = trim($str, $options['delimiter']);
        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }

    public function friendlyURL($str = '') {

        $friendlyURL = htmlentities($str, ENT_COMPAT, "UTF-8", false);
        $friendlyURL = preg_replace('/&([a-z]{1,2})(?:acute|lig|grave|ring|tilde|uml|cedil|caron);/i', '\1', $friendlyURL);
        $friendlyURL = html_entity_decode($friendlyURL, ENT_COMPAT, "UTF-8");
        $friendlyURL = preg_replace('/[^a-z0-9-\.]+/i', '-', $friendlyURL);
        $friendlyURL = preg_replace('/-+/', '-', $friendlyURL);
        $friendlyURL = trim($friendlyURL, '-');
        $friendlyURL = strtolower($friendlyURL);
        return $friendlyURL;
    }

    /**
     * 
     * @param type $maxWidth
     * @param type $maxHeight
     * @param type $path
     * @param type $outfile
     * @param type $outFileType
     * @param type $autoAdjust
     * @return null
     */
    private function resizeImage($maxWidth, $maxHeight, $path, $outfile = NULL, $outFileType = "image/jpeg", $autoAdjust = true) {
        if (!
                file_exists($path))
            return NULL;
        $size = @getimagesize($path);

        if (!$size)
            return NULL;
        $inFileType = $size['mime'];
//$onWidth = $size[0] > $size [1];
//$widthDelta = 600 / $size[0];

        if ($autoAdjust) {
            $delta = $maxWidth / $size [0];
            if ($delta * $size[1] > $maxHeight) {
                $delta = $maxHeight / $size[1];
            }
            $resizeWidth = $delta * $size[0];
            $resizeHeight = $delta * $size[1];
        } else {
            $resizeWidth = $maxWidth;
            $resizeHeight = $maxHeight;
        }

        $infile = $path;
        $filePath = split("/", $path);
        if
        ($outfile == NULL)
            $outfile = str_replace($filePath[count($filePath) - 1], $filePath[count($filePath) - 1], $path);

        switch ($inFileType) {
            case "image/jpeg":
            case "image/pjpeg":
                $source = imagecreatefromjpeg($infile);
                break;
            case "image/png":
            case "image/x-png":
                $source = imagecreatefrompng($infile);
                break;
            case "image/gif":
                $source = imagecreatefromgif($infile);
                break;
            default:
                return NULL;
        }
//$source = imagecreatefromjpeg($infile);
        $dest = imagecreatetruecolor($resizeWidth, $resizeHeight);
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $size[0], $size[1]);
        $fstream = fopen($outfile, "w");
        fclose($fstream);
        switch ($outFileType) {
            case "image/jpeg":
            case "image/pjpeg": imagejpeg($dest, $outfile, 100);
                break;
            case "image/png":
            case "image/x-png":
                $trans_colour = imagecolorallocatealpha($dest, 0, 0, 0, 127);
                imagesavealpha($dest, true);
                imagefill($dest, 0, 0, $trans_colour);
                imagepng($dest, $outfile, 0);
                break;
            case "image/gif": imagegif($dest, $outfile);
                break;
            default:
                return NULL;
        }

        return $outfile;
    }

    /**
     * Makes directory, returns TRUE if exists or made
     *
     * @param string $pathname The directory path.
     * @return boolean returns TRUE if exists or made or FALSE on failure.
     */
    private function mkdir_recursive($pathname, $mode) {
        is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }

}
