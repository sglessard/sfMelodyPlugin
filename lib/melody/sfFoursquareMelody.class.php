<?php
class sfFoursquareMelody extends sfMelody2
{
  protected function initialize($config)
  {
    $this->setRequestAuthUrl('https://foursquare.com/oauth2/authorize');
    $this->setAccessTokenUrl('https://foursquare.com/oauth2/access_token');

    $this->setNamespaces(array('default' => 'https://api.foursquare.com/v2'));

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
   * @param v : versioning
   * @see https://developer.foursquare.com/docs/overview.html#versioning
   * @see http://groups.google.com/group/foursquare-api/browse_thread/thread/3605b214c2b3a1dd/5b034efa284b3818
   */
  public function initializeFromToken($token)
  {
    if($token && $token->getStatus() == Token::STATUS_ACCESS)
    {
      // We use user culture
      $this->setCallParameter('locale', $this->getContext()->getUser()->getCulture());

      $this->setAlias('me','users/self');
    }
  }

  public function getIdentifier()
  {
    return null; // No param is returned from access token request
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
