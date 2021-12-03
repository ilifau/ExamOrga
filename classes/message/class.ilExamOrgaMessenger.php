<?php
require_once(__DIR__ . '/class.ilExamOrgaMessage.php');
require_once(__DIR__ . '/class.ilExamOrgaMessageSent.php');
require_once(__DIR__ . '/class.ilExamOrgaMailTemplateContext.php');

class ilExamOrgaMessenger
{
    /** @var ilExamOrgaPlugin */
    protected $plugin;

    /** @var ilObjExamOrga */
    protected $object;

    /**
     * @var ilExamOrgaMessage[] indexed by type
     */
    protected $messages = [];

    /**
     * Constructor
     * @param ilObjExamOrga $object
     */
    public function __construct($object)
    {
        $this->object = $object;
        $this->plugin = ilExamOrgaPlugin::getInstance();
        $this->messages = ilExamOrgaMessage::getForObject($this->object->getId());
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
        $params = ['ref_id' => $this->getRefId(), 'record' => $record];


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

        $mail = new ilMail(ANONYMOUS_USER_ID);
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


    /**
     * Get a ref_id for the current object
     * In a cron job the object may not be created by ref_id
     * @retirn int|null
     */
    protected Function getRefId()
    {
        if (!empty($this->object->getRefId())) {
            return $this->object->getRefId();
        }

        foreach(ilObject::_getAllReferences($this->object->getId()) as $ref_id) {
            if (!ilObject::_isInTrash($ref_id)) {
                return $ref_id;
            }
        }

        return null;
    }
}