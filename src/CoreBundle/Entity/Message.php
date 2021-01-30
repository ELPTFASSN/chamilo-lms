<?php

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message.
 *
 * @ORM\Table(name="message", indexes={
 *     @ORM\Index(name="idx_message_user_sender", columns={"user_sender_id"}),
 *     @ORM\Index(name="idx_message_user_receiver", columns={"user_receiver_id"}),
 *     @ORM\Index(name="idx_message_user_sender_user_receiver", columns={"user_sender_id", "user_receiver_id"}),
 *     @ORM\Index(name="idx_message_user_receiver_status", columns={"user_receiver_id", "msg_status"}),
 *     @ORM\Index(name="idx_message_receiver_status_send_date", columns={"user_receiver_id", "msg_status", "send_date"}),
 *     @ORM\Index(name="idx_message_group", columns={"group_id"}),
 *     @ORM\Index(name="idx_message_parent", columns={"parent_id"}),
 *     @ORM\Index(name="idx_message_status", columns={"msg_status"})
 * })
 * @ORM\Entity(repositoryClass="Chamilo\CoreBundle\Repository\MessageRepository")
 */
class Message
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\User", inversedBy="sentMessages")
     * @ORM\JoinColumn(name="user_sender_id", referencedColumnName="id", nullable=false)
     */
    protected User $userSender;

    /**
     * @ORM\ManyToOne(targetEntity="Chamilo\CoreBundle\Entity\User", inversedBy="receivedMessages")
     * @ORM\JoinColumn(name="user_receiver_id", referencedColumnName="id", nullable=true)
     */
    protected User $userReceiver;

    /**
     * @ORM\Column(name="msg_status", type="smallint", nullable=false)
     */
    protected int $msgStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="send_date", type="datetime", nullable=false)
     */
    protected $sendDate;

    /**
     * @Assert\NotBlank
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected string $title;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    protected string $content;

    /**
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer", nullable=false)
     */
    protected $groupId;

    /**
     * @var int
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=false)
     */
    protected $parentId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    protected $updateDate;

    /**
     * @var int
     *
     * @ORM\Column(name="votes", type="integer", nullable=true)
     */
    protected $votes;

    /**
     * @var ArrayCollection|MessageAttachment[]
     *
     * @ORM\OneToMany(targetEntity="MessageAttachment", mappedBy="message")
     */
    protected $attachments;

    /**
     * @var ArrayCollection|MessageFeedback[]
     *
     * @ORM\OneToMany(targetEntity="MessageFeedback", mappedBy="message", orphanRemoval=true)
     */
    protected $likes;

    /**
     * Message constructor.
     */
    public function __construct()
    {
        $this->content = '';
        $this->attachments = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    /**
     * Set userSender.
     */
    public function setUserSender(User $userSender): self
    {
        $this->userSender = $userSender;

        return $this;
    }

    /**
     * Get userSender.
     */
    public function getUserSender(): User
    {
        return $this->userSender;
    }

    /**
     * Set userReceiver.
     */
    public function setUserReceiver(User $userReceiver): self
    {
        $this->userReceiver = $userReceiver;

        return $this;
    }

    /**
     * Get userReceiver.
     *
     * @return User
     */
    public function getUserReceiver()
    {
        return $this->userReceiver;
    }

    /**
     * Set msgStatus.
     */
    public function setMsgStatus(int $msgStatus): self
    {
        $this->msgStatus = $msgStatus;

        return $this;
    }

    /**
     * Get msgStatus.
     */
    public function getMsgStatus(): int
    {
        return $this->msgStatus;
    }

    /**
     * Set sendDate.
     *
     * @param \DateTime $sendDate
     *
     * @return Message
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    /**
     * Get sendDate.
     *
     * @return \DateTime
     */
    public function getSendDate()
    {
        return $this->sendDate;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Message
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set groupId.
     *
     * @param int $groupId
     *
     * @return Message
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Get groupId.
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set parentId.
     *
     * @param int $parentId
     *
     * @return Message
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set updateDate.
     *
     * @param \DateTime $updateDate
     *
     * @return Message
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set votes.
     *
     * @param int $votes
     *
     * @return Message
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Get votes.
     *
     * @return int
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Get attachments.
     *
     * @return ArrayCollection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Get an excerpt from the content.
     *
     * @param int $length Optional. Length of the excerpt.
     *
     * @return string
     */
    public function getExcerpt($length = 50)
    {
        $striped = strip_tags($this->content);
        $replaced = str_replace(["\r\n", "\n"], ' ', $striped);
        $trimmed = trim($replaced);

        return api_trunc_str($trimmed, $length);
    }
}
