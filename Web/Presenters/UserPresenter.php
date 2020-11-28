<?php declare(strict_types=1);
namespace qwshr\Web\Presenters;
use qwshr\Web\Models\Entities\Link;
use qwshr\Web\Models\Repositories\Users;
use qwshr\Web\Models\Repositories\Links;

final class UserPresenter extends QWshrPresenter
{
    private $users;

    function __construct(Users $users)
    {
        $this->users = $users;

        parent::__construct();
    }

    function renderView(string $nickname): void
    {
        $user = $this->users->getByNickname($nickname);

        if(!$user)
            $this->notFound();
        else 
            $this->template->user = $user;
        
    }

    private function checkURL(string $url): ?string
    {
        if(preg_match("@^(https?)://[^\s/$.?#].[^\s]*$@i", $url) == 1) 
        {
            $services = json_decode(file_get_contents(dirname(__FILE__) . "/../../services.json"));
            
            foreach ($services->services as $service) 
            {
                if(in_array(parse_url($url)['host'], $service->url)) 
                {
                    return $service->type;
                }
            }
        }
        return null;
    }

    function renderSettings(): void
    {
        $this->assertUserLoggedIn();

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            /*
             *  Links
             */

            switch ($this->postParam("pt")) {
                case "edit_link":
                    $link = (new Links)->getById(intval($this->postParam("link_id")));
                    if(is_null($link))
                        $this->flashFail("danger", "This link does not exist or was been deleted");
                    
                    if($link->getOwnerId() == $this->user->identity->getId())
                    {
                        $urlCheckResult = $this->checkURL($this->postParam("link_url"));
                        
                        if(!is_null($urlCheckResult)){
                            $link->setUrl($this->postParam("link_url"));
                            $link->setType($urlCheckResult);
                        }else{
                            $this->flashFail("danger", "URL is incorrect or that service is not supported");
                        }
                            
                        $link->setCaption($this->postParam("link_caption"));
                        $link->save();
                        $this->flashFail("success", "Link was been updated");
                    } else {
                        $this->flashFail("danger", "This link is not yours");
                    }
                    break;

                case "add_link":
                    $link = new Link;
                    
                    $urlCheckResult = $this->checkURL($this->postParam("link_url"));
                    
                    if(!is_null($urlCheckResult)){
                        $link->setUrl($this->postParam("link_url"));
                        $link->setType($urlCheckResult);
                    }else{
                        $this->flashFail("danger", "URL is incorrect or that service is not supported");
                    }
                    
                    $link->setCaption($this->postParam("link_caption"));
                    $link->setUser($this->user->identity->getId());
                    $link->save();
                    $this->flashFail("success", "New Link was been added");
                    break;
                
                case "delete_link":
                    $link = (new Links)->getById(intval($this->postParam("link_id")));
                    if(is_null($link))
                        $this->flashFail("danger", "This link does not exist or was been already deleted");
                    
                    if($link->getOwnerId() == $this->user->identity->getId())
                    {
                        $link->delete(false);
                        $this->flashFail("success", "Link was been deleted");
                    }
                    break;
            }

            $user = $this->user->identity;
            
            /*
             *  Main info
             */

            if(!empty($this->postParam("irl_name")))
                $user->setIrl_name($this->postParam("irl_name"));
            else
                $this->flashFail("danger", "Name cannot be empty.");
            $user->setBio($this->postParam("bio"));
            
            /*
             *  Password changing
             */

            if($this->postParam("old_password") && $this->postParam("new_password") && $this->postParam("repeat_password")) {
                if($this->postParam("new_password") === $this->postParam("repeat_password")) {
                    if(!$this->user->identity->getChandlerUser()->updatePassword($this->postParam("new_password"), $this->postParam("old_password")))
                        $this->flashFail("danger", "Old password does not match");
                } else {
                    $this->flashFail("danger", "New password does not match");
                }   
            }

            $user->save();

            $this->flashFail("success", "Your settings was been saved.");
        }
    }
}