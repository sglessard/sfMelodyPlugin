<?php
class sfFoursquareMelody extends sfMelody2
{
  protected function initialize($config)
  {
    $this->setRequestAuthUrl('https://foursquare.com/oauth2/authorize');
    $this->setAccessTokenUrl('https://foursquare.com/oauth2/access_token');

    $this->setNamespace('default','https://api.foursquare.com/v2');

    // Params requirement 
    // @see https://developer.foursquare.com/docs/oauth.html
    // Auth step
    $this->setAuthParameter('response_type', 'code');

    // Access step
    $this->setAccessParameter('grant_type', 'authorization_code');
    
    if(isset($config['scope']))
    {
      $this->setAuthParameter('scope', implode(',', $config['scope']));
    }
  }


  /**
   * (non-PHPdoc)
   *
   * @param locale : Foursquare internationalization (i18n)
   * @see https://developer.foursquare.com/docs/overview.html#internationalization
   *
   */
  public function initializeFromToken($token)
  {
    if($token && $token->getStatus() == Token::STATUS_ACCESS)
    {
      // i18n from user culture
      $this->setCallParameter('locale', $this->getContext()->getUser()->getCulture());

      $this->setAlias('me','users/self');
    }
  }

  public function getIdentifier()
  {
    //
   // return $this->getToken()->getParam('user_id');
  }

  protected function setExpire(&$token)
  {
    if($token->getParam('expires'))
    {
      $token->setExpire(time() + $token->getParam('expires'));
    }
  }

 /**
   * (non-PHPdoc)
   * Since Foursquare uses 'oauth_token' name for the access token parameter
   * We override the sfOAuth2::prepareCall method
   *
   * @see plugins/sfDoctrineOAuthPlugin/lib/sfOAuth2::prepareCall()
   */
  protected function prepareCall($action, $aliases = null, $params = array(), $method = 'GET')
  {
    if(is_null($this->getToken()))
    {
      throw new sfException(sprintf('no access token available for "%s"', $this->getName()));
    }

    $this->setCallParameter('oauth_token', $this->getToken()->getTokenKey());

    if(in_array($method, array('GET', 'POST')))
    {
      $this->addCallParameters($params);
    }

    return $this->formatUrl($action, $aliases);
  }

}
