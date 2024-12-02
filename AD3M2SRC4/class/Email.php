<?php

require_once PATH_ADM_LIB . DS . 'PHPMailer' . DS . 'PHPMailerAutoload.php';

/**
 * 
 */
class Email {

    public $data = array();
    public $phpMailer = '';

    /**
     * EGMC
     * @param array $data
     */
    public function __construct($data = null) {

        $this->initialize();
        $this->setData($data);
    }

    /**
     * EGMC
     * Inicializa los datos base
     */
    protected function initialize() {

        $this->data = array(
            /**
             * true  => Se envia el correo
             * false => Regresa html del correo       
             */
            'send' => true,
            /**
             * 
             */
            'html' => true,
            /**
             * 
             */
            'charSet' => 'UTF-8',
            /**
             * 
             */
            'language' => 'es',
            /**
             * 
             */
            'wordWrap' => 0,
            /**
             * 
             */
            'smtp' => true,
            /**
             * 
             */
            'smtpData' => array(
                /**
                 * 
                 */
                'auth' => true,
                /**
                 * SMTP Host
                 * smtp.live.com
                 * localhost
                 */
                'host' => MAIL_SERVER,
                /**
                 * STMP Conexión de seguridad 
                 * http://www.arclab.com/en/amlc/list-of-smtp-and-pop3-servers-mailserver-list.html
                 * default
                 *      tls => 25 || 587
                 *      ssl => 465
                 * gmail 
                 *       tls => 587
                 *       ssl => 465 
                 */
                'secure' => 'tls',
                /**
                 * SMTP Puerto
                 * http://www.arclab.com/en/amlc/list-of-smtp-and-pop3-servers-mailserver-list.html
                 * default
                 *      tls => 25 || 587
                 *      ssl => 465
                 * gmail 
                 *       tls => 587
                 *       ssl => 465 
                 */
                'port' => MAIL_PORT,
                /**
                 * Dirección de correo electrónico del cual se mandará el correo
                 * mail@hotmail.com 
                 */
                'username' => MAIL_FROM,
                /**
                 * Password del correo electrónico del cual se mandará el correo 
                 */
                'password' => MAIL_PASS,
                /**
                 * SMTP la conección la mantiene abierta durante un envío masivo
                 * reduce la sobre carga SMTP
                 */
                'keepAlive' => false
            ),
            /**
             * Objetivo del del correo 
             */
            'subject' => MAIL_NAME,
            /**
             * Dirección desde la que se enviará el correo eltrónico
             * array(
             *      'mail' => 'emedina@loomtek.com.mx',
             *      'name' => 'Eddin Gustavo Medina Cid'
             * )
             */
            'from' => array(
                'mail' => '',
                'name' => ''),
            /**
             * Direcciones de correo a las que se le enviará el correo 
             * array(
             *      array(
             *          'mail' => 'emedina@loomtek.com.mx',
             *          'name' => 'Eddin Gustavo Medina Cid'),
             *      ...
             * )
             */
            'to' => array(),
            /**
             * Direcciones de correo elctrónico con copia 
             * array(
             *      array(
             *          'mail' => 'emedina@loomtek.com.mx',
             *          'name' => 'Eddin Gustavo Medina Cid'),
             *      ...
             * )
             */
            'cc' => array(),
            /**
             * Direcciones de correo elctrónico con copia oculta 
             * array(
             *      array(
             *          'mail' => 'emedina@loomtek.com.mx',
             *          'name' => 'Eddin Gustavo Medina Cid'),
             *      ...
             * )
             */
            'bcc' => array(),
            /**
             * Direcciones de correo elctrónico para responder
             * array(
             *      array(
             *          'mail' => 'emedina@loomtek.com.mx',
             *          'name' => 'Eddin Gustavo Medina Cid'),
             *      ...
             * ) 
             */
            'replyTo' => array(),
            /**
             * Archivos adjuntos que tendrá el correo
             * array(
             *      array(
             *          'path' => $this->option['baseDir'].'instructions.pdf',
             *          'name' => 'instrucciones.pdf'),
             *      ...
             * )
             */
            'attachments' => array(),
            /**
             * Contenido que se insertará en el correo
             */
            'content' => '',
            'error' => '',
            'debug' => 0,
            /**
             * Url Base 
             * http://www.acitronmedia.com/public/
             */
            'baseUrl' => ADM_HOME,
            /**
             * Directorio Base
             * /home/loomtek/public_html/
             */
            'baseDir' => PATH_ADM_HOME,
            /**
             * 
             */
            'layout' => 'mails.admin_kondominea_1',
            /**
             * Variables extras para el layout
             * array( 
             *     array(name => value),
             *     ...
             * )
             */
            'variables' => array(),
            'decodeHTMLEntities' => true
        );

        $this->phpMailer = new PHPMailer();
    }

    /**
     * EGMC
     * Actualiza los Datos
     * @param array $data
     * @return boolean
     */
    public function setData($data = null) {
        if ($data) {
            $this->data = $data + $this->data;
        }
        return $this;
    }

    /**
     * EGMC
     * Asigan a nada los datos del correo
     * subject, from, to, replyTo, cc, bcc, attachments, content y variables
     * @return boolean
     */
    public function resetMailData($excepttions = null) {

        /**
         * Clear all ReplyTo recipients.
         * @return void
         */
        $this->phpMailer->clearReplyTos();
        /**
         * Clear all recipient types.
         * @return void
         */
        $this->phpMailer->clearAllRecipients();
        /**
         * Clear all filesystem, string, and binary attachments.
         * @return void
         */
        $this->phpMailer->clearAttachments();
        /**
         * Clear all custom headers.
         * @return void
         */
        $this->phpMailer->clearCustomHeaders();


        return $this->setData(array(
                    'subject' => '',
                    'from' => array(
                        'mail' => '',
                        'name' => ''),
                    'to' => array(),
                    'replyTo' => array(),
                    'cc' => array(),
                    'bcc' => array(),
                    'attachments' => array(),
                    'content' => '',
                    'variables' => array()));
    }

    /**
     * EGMC
     * Crea los datos a mandar
     * @param array $data
     * @return boolean
     */
    public function build($data = null) {

//        Dbg::data($data);
//        Dbg::data($this->data);
        $this->setData($data);
//        Dbg::data($this->data);
//        Dbg::pd();
        //Dbg::pd($this->data);

        $this->phpMailer->SMTPDebug = $this->data['debug'];

        $this->phpMailer->CharSet = $this->data['charSet'];

        $this->phpMailer->setLanguage($this->data['language']);


        if ($this->data['smtp']) {

            $this->phpMailer->isSMTP();
            if (!empty($this->data['smtpData'])) {
                $this->phpMailer->SMTPAuth = $this->data['smtpData']['auth'];
                $this->phpMailer->Host = $this->data['smtpData']['host'];
                $this->phpMailer->SMTPSecure = $this->data['smtpData']['secure'];
                $this->phpMailer->Port = $this->data['smtpData']['port'];
                $this->phpMailer->Username = $this->data['smtpData']['username'];
                $this->phpMailer->Password = $this->data['smtpData']['password'];
                $this->phpMailer->SMTPKeepAlive = (isset($this->data['smtpData']['keepAlive']) ? $this->data['smtpData']['keepAlive'] : false);
            }
        }
        /**
         * EGMC 20150914
         * Si está activado el decodeHTMLEnitites aplica la acción
         */
        if ($this->data['decodeHTMLEntities']) {
            $this->phpMailer->Subject = html_entity_decode($this->data['subject']);
        } else {

            $this->phpMailer->Subject = $this->data['subject'];
        }


        $this->phpMailer->From = $this->data['from']['mail'];

        if ($this->data['from']['name'] != '') {
            $this->phpMailer->FromName = $this->data['from']['name'];
        }

        if (!empty($this->data['to'])) {
            foreach ($this->data['to'] as $to) {
                $this->phpMailer->addAddress($to['mail'], $to['name']);
            }
        }

        if (!empty($this->data['cc'])) {
            foreach ($this->data['cc'] as $cc) {
                $this->phpMailer->addCC($cc['mail'], $cc['name']);
            }
        }

        if (!empty($this->data['bcc'])) {
            foreach ($this->data['bcc'] as $bcc) {
                $this->phpMailer->addBCC($bcc['mail'], $bcc['name']);
            }
        }

        if (!empty($this->data['replyTo'])) {

            foreach ($this->replyTo as $replyTo) {
                $this->phpMailer->AddReplyTo($replyTo['mail'], $replyTo['name']);
            }
        }

        if (!empty($this->data['attachments'])) {
            foreach ($this->data['attachments'] as $attachment) {
                $this->phpMailer->addAttachment( $attachment['path'], $attachment['name']);
            }
        }

        if ($this->data['html']) {
            $this->phpMailer->isHTML(true);
        }
        /**
         * EGMC
         * Se obtiene el contenido del correo y se asigna
         */
        if ($this->data['decodeHTMLEntities']) {
            $this->phpMailer->Body = html_entity_decode($this->getContentMail());
            $this->phpMailer->AltBody = html_entity_decode($this->data['content']);
        } else {
//           echo $this->getContentMail(); die;
            $this->phpMailer->Body = $this->getContentMail();
            $this->phpMailer->AltBody = $this->data['content'];
        }


        return $this;
    }

    /**
     * EGMC
     * Regresa HTML del correo a enviar
     * @param string $content
     * @param string $variables
     * @param string $layout nombre del layuot
     * @param string $element nombre del layout
     * @return string  
     */
    public function getContentMail($content = '', $variables = null, $layout = 'mails.basic') {

        if ($content == '') {
            $content = $this->data['content'];
        }

        if ($variables == null && $this->data['variables'] != '') {
            $variables = $this->data['variables'];
        }

        $variables['content'] = $content;

        if ($this->data['layout'] != '') {
            $layout = $this->data['layout'];
        }

        $blade = new BladeView();

        return $blade->view()->make($layout, $variables)->render();
    }

    public function send() {
        if ($this->data['send']) {
//            Dbg::data($this->phpMailer);
            $result = $this->phpMailer->Send();

            if ($result == false) {
                $this->data['error'] = $this->phpMailer->ErrorInfo;
            }
            return $result;
        }
        return array(
            'html' => $this->phpMailer->Body,
            'text' => $this->phpMailer->AltBody);
    }

}
