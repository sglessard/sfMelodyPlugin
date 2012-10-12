<?php
class sfInstagramMelody extends sfMelody2
{
  protected function initialize($config)
  {
    $this->setRequestAuthUrl('https://instagram.com/oauth/authorize');
    $this->setAccessTokenUrl('https://api.instagram.com/oauth/access_token');
    $this->setAccessTokenMethod('POST');

    $this->setNamespaces(array('default' => 'https://api.instagram.com/v1'));

    if(isset($config['scope']))
    {
      $this->setAuthParameter('scope', implode(',', $config['scope']));
    }
  }

  public function initializeFromToken($token)
  {
    if($token && $token->getStatus() == Token::STATUS_ACCESS)
    {
      $this->setAlias('me','users/self');
    }
  }

  public function getIdentifier()
  {
    return $this->getToken()->getParam('id');
  }

  protected function setExpire(&$token)
  {
    if($token->getParam('expires'))
    {
      $token->setExpire(time() + $token->getParam('expires'));
    }
  }
}
