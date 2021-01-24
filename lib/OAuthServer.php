<?php

declare(strict_types=1);

namespace SimpleSAML\Module\oauth;

require_once(dirname(dirname(__FILE__)) . '/libextinc/OAuth.php');

/**
 * OAuth Provider implementation..
 *
 * @package SimpleSAMLphp
 */
class OAuthServer extends \OAuthServer
{
    /**
     * @param \OAuthDataStore $store
     */
    public function __construct(\OAuthDataStore $store)
    {
        parent::__construct($store);
    }


    /**
     * @return array
     */
    public function get_signature_methods(): array
    {
        return $this->signature_methods;
    }
}
