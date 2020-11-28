<?php declare(strict_types=1);
namespace qwshr\API\Handlers;
use qwshr\API\Exceptions\APIErrorException;
use qwshr\Web\Models\Entities\User;

abstract class APIRequestHandler
{
    protected $user;
    
    function __construct(?User $user = NULL)
    {
        $this->user = $user;
    }
    
    protected function fail(int $code, string $message): void
    {
        throw new APIErrorException($message, $code);
    }
    
    protected function getUser(): ?User
    {
        return $this->user;
    }
    
    protected function userAuthorized(): bool
    {
        return !is_null($this->getUser());
    }
    
    protected function requireUser(): void
    {
        if(!$this->userAuthorized())
            $this->fail(5, "User authorization failed: no access_token passed.");
    }
}