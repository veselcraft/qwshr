<?php declare(strict_types=1);
namespace qwshr\Web\Models\Repositories;
use qwshr\Web\Models\Entities\User;
use Nette\Database\Table\ActiveRow;
use Chandler\Database\DatabaseConnection;
use Chandler\Security\User as ChandlerUser;

class Users
{
    private $context;
    private $users;

    function __construct()
    {
        $this->context = DatabaseConnection::i()->getContext();
        $this->users   = $this->context->table("profiles");
    }
    
    private function toUser(?ActiveRow $ar): ?User
    {
        return is_null($ar) ? NULL : new User($ar);
    }
    
    function get(int $id): ?User
    {
        return $this->toUser($this->users->get($id));
    }

    function getByChandlerUser(ChandlerUser $user): ?User
    {
        return $this->toUser($this->users->where("user", $user->getId())->fetch());
    }

    function getByNickname(string $nickname): ?User
    {
        return $this->toUser($this->users->where("nickname", $nickname)->fetch());
    }
    
    use \Nette\SmartObject;
}