<?php
namespace WScore\Basic\Swift;

use Swift_Mailer;
use Swift_Message;

class Mailer
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Closure
     */
    private $default_call;

    /**
     * @param Swift_Mailer $mailer
     */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param callable $default
     */
    public function setDefault($default)
    {
        $this->default_call = $default;
    }

    /**
     * @return Swift_Message
     */
    public function message()
    {
        return Swift_Message::newInstance();
    }

    /**
     * @return Swift_Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @param string   $text
     * @param callable $callable
     * @return int
     */
    public function sendText($text, $callable)
    {
        return $this->send($text, null, $callable);
    }

    /**
     * @param string   $html
     * @param callable $callable
     * @return int
     */
    public function sendHtml($html, $callable)
    {
        return $this->send($html, 'text/html', $callable);
    }

    /**
     * @param string   $text
     * @param callable $callable
     * @return int
     */
    public function sendJIS($text, $callable)
    {
        return $this->send($text, null, $callable, function($message) {
            /** @var Swift_Message $message */
            $message->setCharset('iso-2022-jp');
            $message->setEncoder( new \Swift_Mime_ContentEncoder_PlainContentEncoder( '7bit' ) );
        });
    }

    /**
     * @param string      $text
     * @param null|string $type
     * @param callable    $callable
     * @param callable    $mailerCallable
     * @return int
     */
    private function send($text, $type, $callable, $mailerCallable=null)
    {
        $message = Swift_Message::newInstance();
        if($mailerCallable) {
            $mailerCallable($message);
        }
        $message->setBody($text, $type);
        if($this->default_call) {
            $default = $this->default_call;
            $default($message);
        }
        $callable($message);
        return $this->mailer->send($message);
    }
}