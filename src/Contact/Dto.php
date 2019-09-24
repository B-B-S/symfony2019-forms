<?php


namespace App\Contact;

use Symfony\Component\Validator\Constraints as Assert;

class Dto
{

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min=2)
     */
    public $name;

    /** @var string
     *
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $email;

    /** @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    public $subject;

    /** @var string
     * @Assert\NotBlank
     * @Assert\Length(min=10, max=300)
     */
    public $message;
}