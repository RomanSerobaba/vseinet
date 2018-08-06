<?php 

namespace SiteBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

class UserLogin
{
    public $username;

    public $password;

    public $remember;
}