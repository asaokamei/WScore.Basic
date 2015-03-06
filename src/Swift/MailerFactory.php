<?php
namespace WScore\Basic\Swift;

use Swift;
use Swift_DependencyContainer;
use Swift_FileSpool;
use Swift_Mailer;
use Swift_MailTransport;
use Swift_NullTransport;
use Swift_Plugins_AntiFloodPlugin;
use Swift_Plugins_ThrottlerPlugin;
use Swift_Preferences;
use Swift_SmtpTransport;
use Swift_SpoolTransport;

class MailerFactory
{
    /**
     * creates a mailer instance that will NOT send.
     *
     * @return static
     */
    public static function forgeNull()
    {
        $transport = Swift_NullTransport::newInstance();
        $mailer    = Swift_Mailer::newInstance($transport);
        return new Mailer($mailer);
    }

    /**
     * creates a mailer instance that will save mail to a file.
     *
     * @param string $path
     * @return static
     */
    public static function forgeFileSpool($path)
    {
        $spool     = new Swift_FileSpool($path);
        $transport = Swift_SpoolTransport::newInstance($spool);
        $mailer    = Swift_Mailer::newInstance($transport);
        return new Mailer($mailer);
    }

    /**
     * creates a mailer instance that will send mail using PHP's mail() function.
     *
     * @return static
     */
    public static function forgePhpMailer()
    {
        $transport = Swift_MailTransport::newInstance();
        $mailer    = Swift_Mailer::newInstance($transport);
        return new Mailer($mailer);
    }

    /**
     * creates a mailer instance that will send mail via SMTP.
     * $security maybe 'ssl', 'tls' ?
     *
     * @param string $host
     * @param int    $port
     * @param string $security
     * @param string $user
     * @param string $pass
     * @return static
     */
    public static function forgeSmtp($host='localhost', $port=25, $security = null, $user=null, $pass=null)
    {
        $transport = Swift_SmtpTransport::newInstance($host, $port, $security);
        if($user) {
            $transport->setUsername($user);
            $transport->setPassword($pass);
            $transport->start();
            if(!$transport->isStarted()) {
                throw new \RuntimeException('cannot start SMPT transport.');
            }
        }
        $mailer    = Swift_Mailer::newInstance($transport);
        return new Mailer($mailer);
    }

    /**
     * call this method to use mails in ISO2022
     * (this was the Japanese traditional mail encoding).
     */
    public static function goJapaneseIso2022()
    {
        Swift::init(function () {
            Swift_DependencyContainer::getInstance()
                ->register('mime.qpheaderencoder')
                ->asAliasOf('mime.base64headerencoder');
            Swift_Preferences::getInstance()->setCharset('iso-2022-jp');
        });
    }

    /**
     * @param Mailer $message
     * @param int    $threshold
     * @param int    $sleep
     */
    public static function antiFlood($message, $threshold=99, $sleep=0)
    {
        $plugIn = new Swift_Plugins_AntiFloodPlugin($threshold, $sleep);
        $message->getMailer()->registerPlugin($plugIn);
    }

    /**
     * @param Mailer $message
     * @param int    $rate
     * @param int    $mode
     */
    public static function throttle($message, $rate=10, $mode=Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE)
    {
        $plugIn = new Swift_Plugins_ThrottlerPlugin($rate, $mode);
        $message->getMailer()->registerPlugin($plugIn);
    }
}