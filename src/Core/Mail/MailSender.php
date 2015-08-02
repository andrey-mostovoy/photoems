<?php
namespace Core\Mail;

use RuntimeException;
use Core\Mail\Exception\MailException;

/**
 * Класс для работы с почтовой рассылкой.
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class MailSender {
    /**
     * Получатели письма. Должны соответствовать RFC 2822.
     * Примеры:
     * user@example.com
     * user@example.com, anotheruser@example.com
     * User <user@example.com>
     * User <user@example.com>, Another User <anotheruser@example.com>
     * @var array
     */
    private $receivers = array();

    /**
     * @var string Имя отправителя.
     */
    private $fromName;

    /**
     * @var string Email отправителя.
     */
    private $fromEmail;

    /**
     * @var string Тема письма. Должна соответствовать RFC 2047.
     */
    private $subject;

    /**
     * @var string Текст письма.
     */
    private $message;

    /**
     * @var array Список дополнительных заголовков письма.
     */
    private $headers = array();

    /**
     * @var MailerFastSmtp Объект быстрой рассылки через smtp.
     */
    private $MailerFastSmtp;

    /**
     * Отправляет письмо.
     * @return bool
     */
    public function send() {
        $this->addHeader('MIME-Version: 1.0');

        if ($this->fromEmail) {
            if ($this->fromName) {
                $from = $this->fromName . ' <' . $this->fromEmail . '>';
            } else {
                $from = $this->fromEmail;
            }

            $this
                ->addHeader('From: ' . $from)
                ->addHeader('Reply-To: ' . $from)
                ->addHeader('Return-Path: ' . $this->fromEmail)
                ->addHeader('X-Mailer: PHP/' . phpversion())
            ;
        }
        return mail(
            implode(', ', $this->receivers),
            $this->subject,
            $this->message,
            implode("\r\n", $this->headers),
            '-f' . $this->fromEmail
        );
    }

    /**
     * Отправляет html письмо.
     * @return bool
     */
    public function sendHtml() {
        $this->addHeader('Content-Type: text/html; charset=ISO-8859-1');
        return $this->send();
    }

    /**
     * Отправляет сообщения через smtp.
     * @return array
     * @throws MailException
     * @throws RuntimeException
     */
    public function sendFastSmtp() {
        $MailerFastSmtp = $this->getMailerFastSmtp();

        foreach ($this->receivers as $receiver) {
            $mailerData = array();
            $mailerData['subject']     = $this->subject;
            $mailerData['from']     = '=?UTF-8?B?'.base64_encode($this->fromName).'?= <'.$this->fromEmail.'>';
            $mailerData['Return-Path'] = $this->fromEmail;
            $mailerData['to']          = $receiver;
            $mailerData['body']        = $this->message;
            $mailerData['html']        = nl2br($this->message);

            $MailerFastSmtp->addEmail($receiver, $mailerData);
        }

        return $MailerFastSmtp->send('omx1.local', 25, '', '');
    }

    /**
     * Добавляет получателя. Должно быть RFC 2822.
     * Примеры:
     * user@example.com
     * user@example.com, anotheruser@example.com
     * User <user@example.com>
     * User <user@example.com>, Another User <anotheruser@example.com>
     *
     * @param string $receiver
     * @return MailSender
     */
    public function addReceiver($receiver) {
        $this->receivers[] = $receiver;

        return $this;
    }

    /**
     * Добавляет дополнительный заголовок для письма.
     * @param string $header
     * @return MailSender
     */
    public function addHeader($header) {
        $this->headers[] = $header;

        return $this;
    }

    /**
     * Устанавливает тему письма. Должна соответствовать RFC 2047.
     * @param string $subject
     * @return MailSender
     */
    public function setSubject($subject) {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Устанавливает текст письма.
     * @param string $message
     * @return MailSender
     */
    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * Устанавливает email отправителя.
     * @param string $fromEmail
     * @return MailSender
     */
    public function setFromEmail($fromEmail) {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * Устанавливает имя отправителя.
     * @param string $fromName
     * @return MailSender
     */
    public function setFromName($fromName) {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * Возвращает объект быстрой рассылки мыл через smtp.
     * @return MailerFastSmtp
     */
    private function getMailerFastSmtp() {
        if (!$this->MailerFastSmtp) {
            $this->MailerFastSmtp = new MailerFastSmtp();
        }
        return $this->MailerFastSmtp;
    }
}
