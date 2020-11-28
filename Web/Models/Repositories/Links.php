<?php declare(strict_types=1);
namespace qwshr\Web\Models\Repositories;
use qwshr\Web\Models\Entities\Link;
use Nette\Database\Table\ActiveRow;
use Chandler\Database\DatabaseConnection;

class Links
{
    private $context;
    private $links;

    function __construct()
    {
        $this->context = DatabaseConnection::i()->getContext();
        $this->links   = $this->context->table("links");
    }
    
    private function toLink(?ActiveRow $ar): ?Link
    {
        return is_null($ar) ? NULL : new Link($ar);
    }
    
    function getByUser(int $id): \Traversable
    {
        $result = $this->links->where("user = ?", $id);
        return new Util\EntityStream("Link", $result);
    }

    function getById(int $id): ?Link
    {
        return $this->toLink($this->links->where("id", $id)->fetch());
    }
    
    use \Nette\SmartObject;
}