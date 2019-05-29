<?php

use Webmozart\Assert\Assert;

/**
 * Hook to add link to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 * @return void
 */
function oauth_hook_frontpage(&$links)
{
    Assert::isArray($links);
    Assert::keyExists($links, 'links');

    $links['federation']['oauthregistry'] = [
        'href' => SimpleSAML\Module::getModuleURL('oauth/registry.php'),
        'text' => '{core:frontpage:link_oauth}',
    ];
}
