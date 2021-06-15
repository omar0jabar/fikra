<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    /**
     *
     */
    private $currentPassword;

    /**
     * @Assert\Length(
     *     min=8,
     *     max="50",
     *
     * )
     */
    private $newPassword;

    /**
     * @Assert\EqualTo(
     *     propertyPath="newPassword",
     *     message="The password fields must match."
     * )
     */
    private $confirmPassword;

    public function getCurrentPassword(): ?string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(string $currentPassword): self
    {
        $this->currentPassword = $currentPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
