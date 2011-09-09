<?php
class sfFlickrMelody extends sfMelody1
{
  protected function initialize($config)
  {
    $this->setRequestTokenUrl('http://www.flickr.com/services/oauth/request_token');
    $this->setRequestAuthUrl('http://www.flickr.com/services/oauth/authorize');
    $this->setAccessTokenUrl('http://www.flickr.com/services/oauth/access_token');

    $this->setNamespaces(array('default' => 'http://api.flickr.com/services/rest'));
  }

  public function initializeFromToken($token)
  {
    if($token && $token->getStatus() == Token::STATUS_ACCESS)
    {
      $this->setCallParameter('format', 'json');
      $this->setCallParameter('nojsoncallback', '1');
      $this->setAlias('me','?method=flickr.people.getInfo&user_id='.$this->getIdentifier());
    }
  }

  public function getIdentifier()
  {
    return $this->getToken()->getParam('user_nsid');
  }
}