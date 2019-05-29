<?php

use Webmozart\Assert\Assert;

/**
 * Hook to run a cron job.
 *
 * @param array &$croninfo  Output
 * @return void
 */
function oauth_hook_cron(&$croninfo)
{
    Assert::isArray($croninfo);
    Assert::keyExists($croninfo, 'summary');
    Assert::keyExists($croninfo, 'tag');

    $oauthconfig = \SimpleSAML\Configuration::getOptionalConfig('module_statistics.php');

    if (is_null($oauthconfig->getValue('cron_tag', 'hourly'))) {
        return;
    }
    if ($oauthconfig->getValue('cron_tag', null) !== $croninfo['tag']) {
        return;
    }

    try {
        $store = new \SimpleSAML\Module\core\Storage\SQLPermanentStorage('oauth');
        $cleaned = $store->removeExpired();
        $croninfo['summary'][] = 'OAuth clean up. Removed '.$cleaned.' expired entries from OAuth storage.';
    } catch (\Exception $e) {
        $message = 'OAuth clean up cron script failed: '.$e->getMessage();
        \SimpleSAML\Logger::warning($message);
        $croninfo['summary'][] = $message;
    }
}
