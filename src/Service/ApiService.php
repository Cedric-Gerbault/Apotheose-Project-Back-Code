<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ApiService{

    private $accessTokenIgdb;
    private $tokenUrl = 'https://id.twitch.tv/oauth2/token?client_id=xg8ndhd4c1gk9bi9a88lw06kxcizi6&client_secret=wgeksfty9kw4m74o2jznmgny5aof27&grant_type=client_credentials';



    private $session;

    // setting the session at any call of the apiService
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;

    }




    /**
     * Get the value of accessTokenIgdb
     */ 
    public function getAccessTokenIgdb()
    {
        return $this->session->get('AccessToken');
    }

    /**
     * Set the value of accessTokenIgdb
     *
     * @return  self
     */ 
    public function setAccessTokenIgdb($accessTokenIgdb)
    {
        $this->accessTokenIgdb = $accessTokenIgdb;
        $this->session->set('AccessToken',$accessTokenIgdb);

        return $this;
    }

    /**
     * Get the value of tokenUrl
     */ 
    public function getTokenUrl()
    {
        return $this->tokenUrl;
    }
}