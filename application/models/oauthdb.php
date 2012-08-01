<?php

use Oauth2\Authentication\Database;

class OAuthdb extends CI_Model implements Database
{
    public function __construct()
    {
        parent::__construct();
    }

	public function validateClient($clientId, $clientSecret = null, $redirectUri = null)
    {
        $this->db
            ->select('clients.id')
            ->where('clients.id', $clientId);

        if ($clientSecret !== null)
        {
            $this->db->where('clients.secret', $clientId);
        }

        if ($redirectUri !== null)
        {
            $this->db
                ->join('client_endpoints', 'client_endpoints.client_id = clients.id', 'left')
                ->where('client_endpoints.redirect_uri', $redirectUri);
        }

        $result = $this->db->get('clients');

        if ($result->num_rows() === 0)
        {
            return FALSE;
        }

        else
        {
            return TRUE;
        }
    }

    public function newSession(
        $clientId,
        $redirectUri,
        $type = 'user',
        $typeId = null,
        $authCode = null,
        $accessToken = null,
        $accessTokenExpires = null,
        $stage = 'requested'
    ){
        $this->db->insert('oauth_sessions', array(
            'client_id' =>  $clientId,
            'redirect_uri'  =>  $redirectUri,
            'owner_type'    =>  $type,
            'owner_id'  =>  $typeId,
            'auth_code' =>  $authCode,
            'access_token'  =>  $accessToken,
            'access_token_expires'  =>  $accessTokenExpires,
            'stage' =>  $stage,
            'first_requested'   =>  time(),
            'last_updated'  =>  time()
        ));

        return $this->db->insert_id();
    }

    public function updateSession(
        $sessionId,
        $authCode = null,
        $accessToken = null,
        $accessTokenExpires = null,
        $stage = 'requested'
    ){
        $this->db
            ->where(array(
                'id' => $sessionId
            ))
            ->update('oauth_sessions', array(
                'auth_code' =>  $authCode,
                'access_token'  =>  $accessToken,
                'access_token_expires'  =>  $accessTokenExpires,
                'last_updated'  => time(),
                'stage' =>  $stage
            ));
    }

    public function deleteSession(
        $clientId,
        $type,
        $typeId
    ){
        $this->db->delete('oauth_sessions', array(
            'client_id' =>  $clientId,
            'owner_type'    =>  $type,
            'owner_id'  =>  $typeId
        ));
    }

    public function validateAuthCode(
        $clientId,
        $redirectUri,
        $authCode
    )
    {
        $query = $this->db->get_where('oauth_sessions', array(
            'client_id' =>  $clientId,
            'redirect_uri'  =>  $redirectUri,
            'auth_code'  =>  $authCode
        ));

        if ($query->num_rows() === 0)
        {
            return false;
        }

        else
        {
            return $query->row_array();
        }
    }

    public function hasSession(
        $type,
        $typeId,
        $clientId
    )
    {
        $session_query = $this->db
                ->where(array(
                    'owner_type'    =>  $type,
                    'owner_id'  =>  $typeId,
                    'client_id' =>  $clientId,
                ))
                ->get('oauth_sessions');

        if ($session_query->num_rows() === 0)
        {
            return false;
        }

        else
        {
            return $session_query->row_array();
        }
    }

    public function getAccessToken($sessionId)
    {
        exit('not implemented getAccessToken');
    }

    public function removeAuthCode($sessionId)
    {
        exit('not implemented removeAuthCode');
    }

    public function setAccessToken(
        $sessionId,
        $accessToken
    ){
        exit('not implemented setAccessToken');
    }

    public function addSessionScope($sessionId, $scope)
    {
        $this->db->insert('oauth_session_scopes', array(
            'session_id'    =>  $sessionId,
            'scope' =>  $scope
        ));
    }

    public function getScope($scope)
    {
        $scope_details = $this->db->get_where('scopes', array('scope' => $scope));

        if ($scope_details->num_rows() === 0)
        {
            return FALSE;
        }

        else
        {
            return $scope_details->row_array();
        }
   }

    public function updateSessionScopeAccessToken(
        $sessionId,
        $accessToken
    )
    {
        $this->db
            ->where('session_id', $sessionId)
            ->update('oauth_session_scopes', array(
                'access_token'  =>  $accessToken
            ));
    }

    public function accessTokenScopes($accessToken)
    {
        $this->db->get_where('oauth_session_scopes', array(
            'access_token'   =>  $accessToken
        ));
    }
}