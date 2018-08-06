<?php 

namespace AppBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use AppBundle\Enum\UserRole;

class UserIdentity 
{
    /**
     * @var SessionInterface
     */
    protected $session;


    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function setUser(UserInterface $user)
    {
        $this->session->set('user', $user);
    }

    public function getUser()
    {
        return $this->session->get('user') ?? UserRole::ANONIMOUS;
    }

    public function logout()
    {
        $this->session->remove('user');
    }

    public function isAnonimous(): bool
    {
        return UserRole::ANONIMOUS === $this->getUser();
    }

    public function isAuthorized(): bool
    {
        return UserRole::ANONIMOUS !== $this->getUser();
    }

    public function isClient(): bool
    {
        return $this->isAnonimous() || $this->getUser()->hasRole(UserRole::CLIENT); 
    }

    public function isWholesaler(): bool
    {
        return $this->isAuthorized() && (
            $this->getUser()->hasRole(UserRole::FRANCHISER) 
            || $this->getUser()->hasRole(UserRole::WHOLESALER) 
        );
    }

    public function isEmployee(?string $role = null): bool
    {
        if ($this->isClient() || $this->isWholesaler()) {
            return false;
        }

        if (null === $role) {
            return true;
        }

        return $this->getUser()->hasRole($role); 
    }

    public function isAdmin(): bool
    {
        return $this->isEmployee(UserRole::ADMIN);
    }

    public function isBookkeeper(): bool
    {
        return $this->isEmployee(UserRole::BOOKKEEPER);
    }

    public function isPurchaser(): bool
    {
        return $this->isEmployee(UserRole::PURCHASER);    
    }

    public function isStorekeeper(): bool
    {
        return $this->isEmployee(UserRole::STOREKEEPER);    
    }

    public function isContenter(): bool
    {
        return $this->isEmployee(UserRole::CONTENTER);    
    }

    public function isProgrammer(): bool
    {
        return $this->isEmployee(UserRole::PROGRAMMER);    
    }

    public function isCashier(): bool
    {
        return $this->isEmployee(UserRole::CASHIER);    
    }

    public function isServicer(): bool
    {
        return $this->isEmployee(UserRole::SERVICER);    
    }

    public function hasRole(string $role): bool 
    {
        switch ($role) {
            case UserRole::ANONIMOUS:
                return $this->isAnonimous();

            case UserRole::AUTHORIZED:
                return $this->isAuthorized();

            case UserRole::CLIENT:
                return $this->isClient();

            case UserRole::FRANCHISER:
            case UserRole::WHOLESALER:
                return $this->isWholesaler();
        }

        return $this->isEmployee($role);
    }

    public function isGranted(string $rule): bool
    {
        return $this->isAuthorized() && $this->getUser()->hasRule($rule);
    }

    public function isGrantedAny(array $rules): bool
    {
        foreach ($rules as $rule) {
            if ($this->isGranted($rule)) {
                return true;
            }
        }

        return false;
    }

    public function isGrantedAll(array $rules): bool
    {
        foreach ($rules as $rule) {
            if (!$this->isGranted($rule)) {
                return false;
            }
        }

        return true;
    }
}
