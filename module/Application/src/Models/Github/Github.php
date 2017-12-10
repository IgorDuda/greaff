<?php namespace Application\Models\Github;

use Zend\Http\Client;
use Zend\Http\Request;
use Application\Models\Github\Urls;

class Github
{
    protected $clientId;
    protected $clientSecret;
    protected $code;
    protected $accessToken;

    public function __construct($clientId, $clientSecret)
    {
        if(empty($clientId)) {
            throw new \InvalidArgumentException("Argument 'client_id' can't be empty."); 
        }
        
        if(empty($clientSecret)) {
            throw new \InvalidArgumentException("Argument 'client_secret' can't be empty."); 
        }
        
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function setClientId($clientId)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException("Argument 'client_id' can't be empty.");
        }

        $this->clientId = $clientId;
        return $this;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setCode($code)
    {
        if (empty($code)) {
            throw new \InvalidArgumentException("Argument 'code' can't be empty.");
        }
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setAccessToken($accessToken)
    {
        if (empty($accessToken)) {
            throw new \InvalidArgumentException("Argument 'access_token' can't be empty.");
        }
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function createAccessToken()
    {
        $client = new Client();
        $client->setUri(Urls::access_token);
        $client->setMethod(Request::METHOD_POST);
        $client->setParameterPost(array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $this->code
        ));

        $response = $client->send()->getBody();
        return $this->parseCallback($response);
    }

    public function authorize()
    {
        return Urls::authorize . "?client_id=" . $this->clientId;
    }

    public function me($acessToken)
    {
        $client = new Client(Urls::current_user);
        $client->setMethod(Request::METHOD_GET);
        $client->setParameterGet(array('access_token' => $acessToken));
        $response = $client->send()->getBody();
        return json_decode($response);
    }

    public function searchUser($query)
    {
        $client = new Client(Urls::user_search);
        $client->setMethod(Request::METHOD_GET);
        $client->setParameterGet(array(
            'q' => $query,
        ));
        $response = $client->send()->getBody();
        return json_decode($response);
    }

    private function parseCallback($string)
    {
        if (strlen($string) > 0) {
            parse_str($string, $output);

            if (!$output['access_token']) {
                throw new \InvalidArgumentException("Current returned string does not consist acces_token key.");
            }

            return $output['access_token'];
        }

        throw new \InvalidArgumentException("Returned string can't be empty.");
    }
}
