<?php

use Symfony\Component\Security\Core\Security;

class Auth
{
    private $security;

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }

    public function someMethod()
    {
        // returns User object or null if not authenticated
        $user = $this->security->getUser();

        return $user;
    }
}
/*
// usually you'll want to make sure the user is authenticated first,
// see "Authorization" below
$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

// returns your User object, or null if the user is not authenticated
// use inline documentation to tell your editor your exact User class
/** @var \App\Entity\User $user */
//$user = $this->getUser();