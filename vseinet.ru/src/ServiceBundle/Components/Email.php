<?php

namespace ServiceBundle\Components;


use ServiceBundle\Services\AbstractSender;

class Email
{
    const SENDGRID_API_KEY = '';
    const QUEUE_STATUS_COMMERCIAL_OFFER = 'commercial_offer';

    private $_senderType = '';
    private $_data = [];

    /**
     * Email constructor.
     *
     * @param string $senderType
     * @param array  $data
     */
    public function __construct(string $senderType, array $data)
    {
        $this->_senderType = $senderType;
        $this->_data = $data;
    }

    /**
     * @return object
     */
    public function run()
    {
        $result = null;

        if (!empty($this->_data['body']) && !empty($this->_data['subject']) && !empty($this->_data['addresses'])) {
            $fromName = $fromEmail = $toName = $toEmail = '';
            $personalizations = [];

            $body = $this->_data['body']['content'];
            $subject = $this->_data['subject'];

            if (!empty($this->_data['from'])) {
                if (!empty($this->_data['from']['name'])) {
                    $fromName = $this->_data['from']['name'];
                    $fromEmail = $this->_data['from']['addresses'];
                } else {
                    $fromEmail = $this->_data['from']['addresses'];
                }
            } else {
                $fromEmail = AbstractSender::DEFAULT_FROM;
            }

            $from = new \SendGrid\Email($fromName, $fromEmail);

            if (is_array($this->_data['addresses'])) {
                foreach ($this->_data['addresses'] as $email => $emailData) {
                    $personalization = new \SendGrid\Personalization();
                    $personalization->addTo($email);
                    $personalization->addSubstitution('-url-', $this->_generateUnsubscribeUrl($emailData['id'], $email));
                    $personalization->addSubstitution('-name-', $emailData['name']);

                    $personalizations[] = $personalization;
                }

                //Добавление ссылки на отписку от коммерч. предложений
                if ($this->_data['status'] === self::QUEUE_STATUS_COMMERCIAL_OFFER) {
                    if ($this->_data['body']['contentType'] === AbstractSender::CONTENT_TYPE_HTML) {
                        $link = '<br><br><a href="-url-">Отписаться от рассылки</a>';
                    } else {
                        $link = PHP_EOL.PHP_EOL.'Отписаться от рассылки: -url-';
                    }

                    $body .= $link;
                }
            } else {
                $toEmail = $this->_data['addresses'];
            }

            $to = new \SendGrid\Email($toName, $toEmail);

            if (!empty($this->_data['file']) && file_exists($this->_data['file'])) {
                $file = $this->_data['file'];
                $file_encoded = base64_encode(file_get_contents($file));
                $attachment = new \SendGrid\Attachment();
                $attachment->setContent($file_encoded);
                $attachment->setType(mime_content_type($this->_data['file']));
                $attachment->setDisposition("attachment");
                $attachment->setFilename(basename($this->_data['file']));
            }

            $content = new \SendGrid\Content($this->_data['body']['contentType'], $body);

            $mail = new \SendGrid\Mail($from, $subject, $to, $content);
            if (isset($attachment)) {
                $mail->addAttachment($attachment);
            }
            if (!empty($personalizations)) {
                foreach ($personalizations  as $personalization) {
                    $mail->addPersonalization($personalization);
                }
            }

            $apiKey = getenv(self::SENDGRID_API_KEY);
            $sg = new \SendGrid($apiKey);

            $result = $sg->client->mail()->send()->post($mail);

            echo 'email: '.$toEmail.', statusCode = ' . $result->statusCode().PHP_EOL;

        } else {
            echo 'Error: wrong mail content'.PHP_EOL;
        }

        return $result;
    }

    /**
     * @param $counteragentID
     * @param $email
     *
     * @return string
     */
    private function _generateUnsubscribeUrl($counteragentID, $email)
    {
        return 'http://'.AbstractSender::SHOP_ADDRESS.'/service/subscription/unsubscribe?'.http_build_query(['id' => $counteragentID,'email' => $email,]);
    }
}