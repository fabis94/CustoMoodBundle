<?php

namespace CustoMood\Bundle\AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ChangeEmail
{
    /**
     * @Assert\Email()
     */
    protected $newEmail;

    /**
     * @return mixed
     */
    public function getNewEmail()
    {
        return $this->newEmail;
    }

    /**
     * @param mixed $newEmail
     * @return ChangeEmail
     */
    public function setNewEmail($newEmail)
    {
        $this->newEmail = $newEmail;
        return $this;
    }

}