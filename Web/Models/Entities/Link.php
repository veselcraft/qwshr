<?php declare(strict_types=1);
namespace qwshr\Web\Models\Entities;
use qwshr\Web\Util\DateTime;
use qwshr\Web\Models\RowModel;
use qwshr\Web\Models\Repositories\Links;
use Nette\Database\Table\ActiveRow;
use Chandler\Database\DatabaseConnection;
use Chandler\Security\User as ChandlerUser;

class Link extends RowModel
{
    protected $tableName = "links";
    
    function getId()
    {
        return $this->getRecord()->id;
    }

    function getOwnerId()
    {
        return $this->getRecord()->user;
    }

    function getType()
    {
        return $this->getRecord()->type;
    }

    function getUrl()
    {
        return $this->getRecord()->url;
    }

    function getCaption()
    {
        return $this->getRecord()->caption;
    }
}
