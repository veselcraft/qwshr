<?php declare(strict_types=1);
namespace qwshr\API\Handlers;
use qwshr\Web\Models\Entities\User;
use qwshr\Web\Models\Entities\Link;
use qwshr\Web\Models\Repositories\Users as UsersRepo;
use qwshr\Web\Models\Repositories\Links;

final class Users extends APIRequestHandler
{
    function get(string $nickname): array
    {
        $users = new UsersRepo;

        $user = $users->getByNickname($nickname);
        if(is_null($user))
            fail(113, "Invalid user id");
        else {
            $links;
            
            $i = 0;
            foreach ($user->getLinks() as $link)
            {
                $links[$i] = [
                    "type" => $link->getType(),
                    "url" => $link->getURL(),
                    "caption" => $link->getCaption()
                ];
                $i++;
            }

            return [
                "id" => $user->getId(),
                "nickname" => $user->getNickname(),
                "name" => $user->getIRLName(),
                "bio" => $user->getBio(),
                "links" => (object)$links
            ];
        }
    }
}