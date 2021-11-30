<?php
require_once(__DIR__ . '/class.ilExamOrgaMessage.php');
require_once(__DIR__ . '/class.ilExamOrgaMessageSent.php');
require_once(__DIR__ . '/class.ilExamOrgaMailTemplateContext.php');

class ilExamOrgaMessenger
{
    /** @var ilExamOrgaPlugin */
    protected $plugin;

    /**
     * @var int orga object ref_id
     */
    protected $ref_id;

    /**
     * @var ilExamOrgaMessage[] indexed by type
     */
    protected $messages = [];

    /**
     * Constructor
     * @param int $ref_id
     */
    public function __construct($ref_id)
    {
        $this->ref_id = $ref_id;
        $this->plugin = ilExamOrgaPlugin::getInstance();
        $this->messages = ilExamOrgaMessage::getForObject(ilObject::_lookupObjectId($this->ref_id));
    }


    /**
     * Send the message if it is not already sent
     * @param ilExamOrgaRecord  $record
     * @param string $type      see types in ilExamOrgaMessage
     * @param bool $force       force sending even if message is inactive or already sent
     * @param bool $remember    remember that the message is sent
     * @return bool
     */
    public function send($record, $type, $force = false, $remember = true)
    {
        global $DIC;

        if (!isset($this->messages[$type])) {
            return false;
        }

        if (!$this->messages[$type]->active && !$force) {
            return false;
        }

        if (ilExamOrgaMessageSent::isSent($record->id, $type) && !$force) {
            return false;
        }

        $message = $this->messages[$type];
        $user = new ilObjUser($record->owner_id);

        $context = new ilExamOrgaMailTemplateContext();
        $params = ['ref_id' => $this->ref_id, 'record' => $record];


        $resolver = new ilMailTemplatePlaceholderResolver($context, strip_tags($message->subject));
        $subject = $resolver->resolve($user, $params);

        $resolver = new ilMailTemplatePlaceholderResolver($context, strip_tags($message->content));
        $content = $resolver->resolve($user, $params);

        $to = "";
        $cc = "";
        if (!empty($user->getLogin())) {
            $to = $user->getLogin();
            if (!empty($record->mail_address && $record->mail_address != $user->getEmail())) {
                $cc = $record->mail_address;
            }
        }
        elseif (!empty($record->mail_address)) {
            $to = $record->mail_address;
        }

        $mail = new ilMail($DIC->user()->getId());
        $errors = $mail->sendMail($to, $cc, '', $subject, $content, [],['system'], false);

        if ($remember) {
            ilExamOrgaMessageSent::setSent($record->id, $type);
        }

        if (!empty($errors)) {
            return false;
        }

        return true;
    }


    /**
     * Reset the status of a message type for a record
     */
    public function reset($record, $type)
    {
        ilExamOrgaMessageSent::setUnsent($record->id, $type);
    }

}