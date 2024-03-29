<?php declare(strict_types=1);
namespace qwshr\Web\Presenters;
use qwshr\Web\Models\Entities\User;
use qwshr\Web\Models\Repositories\Users;
use Chandler\Session\Session;
use Chandler\Security\User as ChandlerUser;
use Chandler\Security\Authenticator;
use Chandler\Database\DatabaseConnection;

final class LoginPresenter extends QWshrPresenter
{
    private $authenticator;
    private $db;
    private $users;
    private $restores;
    
    function __construct(Users $users)
    {
        $this->authenticator = Authenticator::i();
        $this->db = DatabaseConnection::i()->getContext();
        
        $this->users    = $users;
        
        parent::__construct();
    }

    private function emailValid(string $email): bool
    {
        if(empty($email)) return false;
        
        $email = trim($email);
        [$user, $domain] = explode("@", $email);
        $domain = idn_to_ascii($domain) . ".";
        
        return checkdnsrr($domain, "MX");
    }

    function renderRegistration(): void
    {
        if(!is_null($this->user))
            $this->redirect("/", static::REDIRECT_TEMPORARY);
        
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            if(!$this->emailValid($this->postParam("email")))
                $this->flashFail("danger", "Your Email are not correct.");

            $nickname = $this->postParam("nickname");
            
            if(!is_null($user == $this->users->getByNickname($nickname)))
                if(preg_match("/[a-zA-Z1-9]+/", $nickname) != 1)
                    $this->flashFail("danger", "Incorrect Nickname format.");
            else
                $this->flashFail("danger", "User with this Nickname are already exist.");
            
            $chUser = ChandlerUser::create($this->postParam("email"), $this->postParam("password"));
            if(!$chUser)
                $this->flashFail("danger", "User with this Email are already exist.");

            $user = new User;
            $user->setUser($chUser->getId());
            $user->setNickname($nickname);
            $user->setIrl_name($this->postParam("nickname"));
            $user->setSince(time());
            $user->save();
            
            $this->authenticator->authenticate($chUser->getId());
            $this->flash("success", "Registration is done!");
            $this->redirect("/", static::REDIRECT_TEMPORARY);
        }
    }
    
    function renderLogin(): void
    {
        if(!is_null($this->user))
            $this->redirect("/", static::REDIRECT_TEMPORARY);
        
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            
            $user = $this->db->table("ChandlerUsers")->where("login", $this->postParam("email"))->fetch();
            if(!$user)
                $this->flashFail("danger", "Incorrect Email or Password.");
            
            if(!$this->authenticator->login($user->id, $this->postParam("password")))
                $this->flashFail("danger", "Incorrect Email or Password.");
            
            $redirUrl = $_GET["jReturnTo"] ?? "/@" . $user->related("profiles.user")->fetch()->nickname;
            $this->flash("success", "You are logged in.");
            $this->redirect($redirUrl, static::REDIRECT_TEMPORARY);
            exit;
        }
    }
    
    function renderLogout(): void
    {
        $this->assertUserLoggedIn();
        $this->authenticator->logout();
        Session::i()->set("_su", NULL);
        
        $this->redirect("/", static::REDIRECT_TEMPORARY_PRESISTENT);
    }
}
