<?php declare(strict_types=1);
namespace qwshr\Web\Models\Repositories; 
use qwshr\Web\Models\Entities\APIToken;

class APITokens extends Repository
{
    protected $tableName = "apitokens";
    protected $modelName = "APIToken";
    
    function getByCode(string $code, bool $withRevoked = false): ?APIToken
    {
        $parts  = explode("-", $code);
        $id     = $parts[0];
        $secret = implode("", array_slice($parts, 1, 9));
        
        $token = $this->get((int) $id);
        if(!$token)
            return NULL;
        else if($token->getSecret() !== $secret)
            return NULL;
        else if($token->isRevoked() && !$withRevoked)
            return NULL;
        
        return $token;
    }
}