<?php
namespace Core\Mail;

use Exception;
use Mail_RFC822;
use PEAR;
use RuntimeException;
use Core\Mail\Exception\MailException;
use stalk\Logger\LoggerFactory;

require_once PEAR_DIR . 'PEAR.php';

/**
 * Скрипт быстрой рассылки мыл через smtp, использует AUTH LOGIN/PLAIN
 * @author Andrey Mostovoy <stalk.4.me@gmail.com>
 */
class MailerFastSmtp {
    /**
     * Сепаратор заголовков запроса
     * @var string
     */
    protected $sep = "\n"; // "\r\n"

    /**
     * Список писем, которые отправятся одним запросом
     * @var array
     */
    protected $emails           = array();

    /**
     * Список отформатированныъх писем с заголовками
     * @var array
     */
    protected $emailsReady      = array();

    /**
     * Добавить письмо в список на отправку
     * @param  string $recipient
     * @param  array $data
     * @return MailerFastSmtp
     */
    public function addEmail($recipient, $data) {
        $this->emails[] = array(
            'recipient' => $recipient,
            'data'      => $data
        );
        return $this;
    }

    /**
     * Отправить все письма добавленные в список
     * Вернет массив с логом шагов отправки и временем выполнения (подключение, авторизация, отправка писем)
     * @param string $host
     * @param int $port
     * @param string $login
     * @param string $password
     * @throws RuntimeException
     * @throws MailException
     * @return array
     */
    public function send($host, $port = 25, $login = null, $password = null) {
        if (empty($port)) {
            $port = 25;
        }

        if (empty($this->emails)) {
            return array();
        }

        $resultLog = array();

        $resultLog['total'] = microtime(1);

        $this->prepareSendout();

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_option($socket, SOL_SOCKET, SO_SNDBUF,    128*1024);
        socket_set_option($socket, SOL_SOCKET, TCP_NODELAY,  0);

        if ($socket < 0) {
            throw new RuntimeException('socket_create() failed: ' . socket_strerror(socket_last_error()));
        }

        $resultLog['server_name'] = $host . ':' . $port;

        $resultLog['connecting'] = microtime(1);

        try {
            $result = socket_connect($socket, $host, $port);
        } catch (Exception $e) {
            throw new MailException('socket_connect() failed (host=' . $host . ', port=' . $port . ')');
        }

        if ($result === false) {
            throw new MailException('socket_connect() failed: ' . socket_strerror(socket_last_error()));
        }

        $resultLog['connecting'] = microtime(1) - $resultLog['connecting'];

        $this->readSmtpAnswer($socket);
        $this->writeSmtpResponse($socket, 'EHLO ' . (empty($login) ? 'web5.local' : gethostname()));
        $this->readSmtpAnswer($socket);

        if (!empty($login) && !empty($password)) {
            $resultLog['authentication'] = microtime(1);

            // @todo: разобраться почему не работает AUTH PLAIN
            //$this->writeSmtpResponse($socket, 'AUTH PLAIN '.base64_encode(chr(0).$login.chr(0).$password));

            $this->writeSmtpResponse($socket, 'AUTH LOGIN');
            $this->writeSmtpResponse($socket, base64_encode($login));
            $this->writeSmtpResponse($socket, base64_encode($password));
            $this->readSmtpAnswer($socket);

            $resultLog['authentication'] = microtime(1) - $resultLog['authentication'];
        }

        if (!empty($this->emailsReady)) {
            $resultLog['emails_sent'] = microtime(1);
            $resultLog['emails_count'] = 0;
            $resultLog['emails'] = array();

            foreach($this->emailsReady as $k => $email) {
                try {
                    if ($email['TO'] == 'z1@mail.ru' || $email['TO'] == 'z2@mail.ru') {
                        LoggerFactory::getRootLogger()->info('Mailer ERROR: ' . 'email=' . $email['TO'] . ', ' . 'host=' . $host);
                    }

                    //добавляем мыла
                    $this->writeSmtpResponse($socket, 'MAIL FROM:' . $email['FROM']);
                    $this->readSmtpAnswer($socket);
                    $this->writeSmtpResponse($socket, 'RCPT TO:' . $email['TO']);
                    $this->readSmtpAnswer($socket);
                    $this->writeSmtpResponse($socket, 'DATA');
                    $this->readSmtpAnswer($socket);
                    $this->writeSmtpResponse($socket, $email['DATA'] . $this->sep . '.');
                    $this->readSmtpAnswer($socket);

                    $resultLog['emails'][] = $email['TO'];

                    $resultLog['emails_count']++;
                } catch (Exception $Ex) {
                    LoggerFactory::getRootLogger()->error($Ex);
                }

                unset($this->emailsReady[$k]);
            }

            $resultLog['emails_sent'] = microtime(1) - $resultLog['emails_sent'];

            // для дебага достаточно первых несколько адресов
            $resultLog['emails'] = array_slice($resultLog['emails'], 0, 2);
        }

        $this->writeSmtpResponse($socket, 'QUIT');
        $this->readSmtpAnswer($socket);

        $this->emailsReady = array();

        socket_close($socket);

        // общее время выполнения запроса
        $resultLog['total'] = microtime(1) - $resultLog['total'];

        return $resultLog;
    }

    /**
     * @return MailerFastSmtp
     */
    protected function prepareSendout() {
        foreach($this->emails as $k => $email) {
            $build          = $this->buildHeadersAndBodyFromData($email['recipient'], $email['data']);
            $headerElements = $this->prepareHeadersWithRFC822($build['headers']);
            list($from, $textHeaders) = $headerElements;
            $from = $email['data']['Return-Path'];

            $this->emailsReady[  ] = array(
                'TO'        => $email['recipient'],
                'FROM'      => $email['data']['Return-Path'],
                'DATA'      => $textHeaders.$this->sep.$build['body'],
            );

            unset($this->emails[$k]);
        }

        $this->emails = array();
        return $this;
    }

    /**
     * Готовим массив с хидерами и текст письма (стадия #1)
     *
     * @param  string $recipient
     * @param  array $data
     * @return array
     */
    protected function buildHeadersAndBodyFromData($recipient, $data) {
        $headers = array(
            'To' => $recipient,
            'Subject' => $data['subject'],
            'Mime-Version' => '1.0',
            'From' => $data['from'],
            'Return-Path' => $data['Return-Path'],
        );

        if (isset($data['headers'])) {
            $headers = array_merge($headers, $data['headers']);
        }


        if (!empty($headers['List-Unsubscribe'])) {
            $headers['List-Unsubscribe'] = '<' . implode('>, <', $headers['List-Unsubscribe']) . '>';
        }

        $body = '';

        if (!isset($data['body'])) {
            $data['body'] = '';
        }

        if (isset($data['html'])) {
            $content_transfer_encoding = 'Content-Transfer-Encoding: base64';
            $content_disposition = 'Content-Disposition: inline';

            // HTML alternative provided - we must use multipart/alternative
            $rand4  = mt_rand(1000, 9999);
            $rand10 = mt_rand(1000000000, 9999999999);
            $boundary = "mailpart_${rand4}_${rand10}_mailpart";
            $headers['Content-Type'] = "multipart/alternative; boundary={$boundary}";

            // We need boundary to be like "--key" until the end
            $boundary = '--' . $boundary;

            // NOTE: all $eol are here for not forgetting
            // Text part
            $body .= $boundary . $this->sep;
            $body .= 'Content-Type: text/plain; charset=utf-8' . $this->sep;
            $body .= $content_transfer_encoding . $this->sep;
            $body .= $content_disposition . $this->sep . $this->sep;
            $body .= chunk_split(base64_encode($data['body']),60,"\n") . $this->sep . $this->sep;
            // HTML part
            $body .= $boundary . $this->sep;
            $body .= 'Content-Type: text/html; charset=utf-8' . $this->sep;
            $body .= $content_transfer_encoding . $this->sep;
            $body .= $content_disposition . $this->sep . $this->sep;
            $body .= chunk_split(base64_encode($data['html']),60,"\n") . $this->sep . $this->sep;
            // Closing boundary
            $body .= $boundary . '--' . $this->sep . $this->sep;
        } else {
            // or it is just a plain text
            $headers['Content-Type'] = 'text/plain; charset=utf-8';
            $headers['Content-Transfer-Encoding'] = 'base64';
            $body = chunk_split(base64_encode($data['body']),60,"\n");
        }

        return array(
            'headers' => $headers,
            'body'    => $body,
        );
    }

    /**
     * Форматируем хидеры в соответствии с RFC822 (стадия #2, финальная)
     *
     * @param  $headers
     * @return array|bool
     */
    protected function prepareHeadersWithRFC822($headers) {
        $lines = array();
        $from = null;

        foreach ($headers as $key => $value) {
            if (strcasecmp($key, 'From') === 0) {
                include_once PEAR_DIR . 'Mail/RFC822.php';
                $parser = new Mail_RFC822();
                $addresses = $parser->parseAddressList($value, 'localhost', false);
                if (PEAR::isError($addresses)) return $addresses;

                $from = $addresses[0]->mailbox . '@' . $addresses[0]->host;

                // Reject envelope From: addresses with spaces.
                if (strstr($from, ' ')) {
                    return false;
                }

                $lines[] = $key . ': ' . $value;
            } elseif (strcasecmp($key, 'Received') === 0) {
                $received = array();
                if (is_array($value)) {
                    foreach ($value as $line) {
                        $received[] = $key . ': ' . $line;
                    }
                }
                else {
                    $received[] = $key . ': ' . $value;
                }
                // Put Received: headers at the top.  Spam detectors often
                // flag messages with Received: headers after the Subject:
                // as spam.
                $lines = array_merge($received, $lines);
            } else {
                // If $value is an array (i.e., a list of addresses), convert
                // it to a comma-delimited string of its elements (addresses).
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                if (0 == strcasecmp($key, 'Subject')) {
                    $value = '=?utf-8?B?' . base64_encode($value) . '?=';
                }
                $lines[] = $key . ': ' . $value;
            }
        }

        return array($from, join($this->sep, $lines) . $this->sep);
    }

    /**
     * @throws MailException
     * @param  $socket
     * @return void
     */
    protected function readSmtpAnswer($socket) {
        $read = socket_read($socket, 1024);

        if ($read{0} != '2' && $read{0} != '3') {
            if (!empty($read)) {
                throw new MailException('SMTP failed: ' . $read);
            } else {
                throw new MailException('Unknown error');
            }
        }
    }

    /**
     * @param  $socket
     * @param  $msg
     * @return void
     */
    protected function writeSmtpResponse($socket, $msg) {
        $msg = $msg.$this->sep;

        try {
            socket_write($socket, $msg, strlen($msg));
        } catch (Exception $e) {
            throw new MailException('socket_write() failed: ' . $e->getMessage());
        }
    }
}
