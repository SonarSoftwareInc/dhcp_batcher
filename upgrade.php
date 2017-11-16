<?php
require(dirname(__FILE__) . "/dhcp_batcher/vendor/autoload.php");

$currentVersion = (string)file_get_contents(dirname(__FILE__) . "/dhcp_batcher/resources/version");
echo "Checking for new upgrades newer than $currentVersion!\n";

$client = new GuzzleHttp\Client();
$res = $client->get("https://api.github.com/repos/sonarsoftware/dhcp_batcher/tags");
$body = json_decode($res->getBody()->getContents());
$latestVersion = $body[0]->name;

if (version_compare($currentVersion, $latestVersion) === -1)
{
    echo "There is a newer version, $latestVersion.\n";
    exec("(cd " . dirname(__FILE__) . "; git reset --hard origin/master)", $output, $returnVal);
    if ($returnVal !== 0) {
        echo "There was an error updating to master.\n";
        return;
    }
    exec("(cd " . dirname(__FILE__) . "; git pull https://github.com/sonarsoftware/dhcp_batcher master)", $output,
        $returnVal);
    if ($returnVal !== 0) {
        echo "There was an error updating to master.\n";
        return;
    }
    exec("(cd " . dirname(__FILE__) . "; git fetch --tags)", $output, $returnVal);
    if ($returnVal !== 0) {
        echo "There was an error updating to master.\n";
        return;
    }
    exec("(cd " . dirname(__FILE__) . "; git checkout tags/$latestVersion)", $output, $returnVal);
    if ($returnVal !== 0) {
        echo "There was an error checking out $latestVersion.\n";
        return;
    }

    exec("/usr/bin/php " . dirname(__FILE__) . "/dhcp_batcher/artisan migrate --force");
    exec("/usr/bin/php " . dirname(__FILE__) . "/dhcp_batcher/artisan config:cache");
    exec("/usr/bin/php " . dirname(__FILE__) . "/dhcp_batcher/artisan route:cache");
    exec("(cd " . dirname(__FILE__) . "/dhcp_batcher; composer install)");
}

echo "You are on the latest version.\n";