<?php declare(strict_types=1);
namespace qwshr\Web\Models\Entities;
use qwshr\Web\Util\DateTime;
use qwshr\Web\Models\RowModel;
use qwshr\Web\Models\Repositories\Users;
use qwshr\Web\Models\Repositories\Links;
use Nette\Database\Table\ActiveRow;
use Chandler\Database\DatabaseConnection;
use Chandler\Security\User as ChandlerUser;

class User extends RowModel
{
    protected $tableName = "profiles";
    
    function getId()
    {
        return $this->getRecord()->id;
    }

    function getChandlerGUID(): string
    {
        return $this->getRecord()->user;
    }
    
    function getChandlerUser(): ChandlerUser
    {
        return new ChandlerUser($this->getRecord()->ref("ChandlerUsers", "user"));
    }

    function getNickname()
    {
        return $this->getRecord()->nickname;
    }

    function getIRLName()
    {
        return $this->getRecord()->irl_name;
    }

    function getBio()
    {
        return $this->getRecord()->bio;
    }

    function getLinks()
    {
        return (new Links)->getByUser($this->getId());
    }
}
