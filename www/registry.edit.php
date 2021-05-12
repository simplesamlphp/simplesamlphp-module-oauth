<?php

// Load SimpleSAMLphp, configuration and metadata
$config = \SimpleSAML\Configuration::getInstance();
$session = \SimpleSAML\Session::getSessionFromRequest();
$oauthconfig = \SimpleSAML\Configuration::getOptionalConfig('module_oauth.php');

$store = new \SimpleSAML\Module\core\Storage\SQLPermanentStorage('oauth');

$authsource = "admin"; // force admin to authenticate as registry maintainer
$useridattr = $oauthconfig->getValue('useridattr', 'user');

if ($session->isValid($authsource)) {
    $attributes = $session->getAuthData($authsource, 'Attributes');
    // Check if userid exists
    if (!isset($attributes[$useridattr])) {
        throw new \Exception('User ID is missing');
    }
    $userid = $attributes[$useridattr][0];
} else {
    $as = \SimpleSAML\Auth\Source::getById($authsource);
    if (!is_null($as)) {
        $httpUtils = new \SimpleSAML\Utils\HTTP();
        $as->initLogin($httpUtils->getSelfURL());
    }
    throw new \Exception('Invalid authentication source: ' . $authsource);
}

if (array_key_exists('editkey', $_REQUEST)) {
    $entryc = $store->get('consumers', $_REQUEST['editkey'], '');
    $entry = $entryc['value'];
    \SimpleSAML\Module\oauth\Registry::requireOwnership($entry, $userid);
} else {
    $randomUtils = new \SimpleSAML\Utils\Random();
    $entry = [
        'owner' => $userid,
        'key' => $randomUtils->generateID(),
        'secret' => $randomUtils->generateID(),
    ];
}

$editor = new \SimpleSAML\Module\oauth\Registry();

if (isset($_POST['submit'])) {
    $editor->checkForm($_POST);

    $entry = $editor->formToMeta($_POST, [], ['owner' => $userid]);

    \SimpleSAML\Module\oauth\Registry::requireOwnership($entry, $userid);

    $store->set('consumers', $entry['key'], '', $entry);

    $template = new \SimpleSAML\XHTML\Template($config, 'oauth:registry.saved.twig');
    $template->data['entry'] = $entry;
    $template->send();
    exit;
}

$form = $editor->metaToForm($entry);

$template = new \SimpleSAML\XHTML\Template($config, 'oauth:registry.edit.twig');
$template->data['form'] = $form;
$template->send();
