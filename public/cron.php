<?php
$token = "GlMbDq3D9kk1y3DskUVh-he_XmepfscUBBOg3A1fmCc";
$urls = [
    'https://www.pfestartup.com/events/update-status',
    'https://www.pfestartup.com/mail/account-activation',
    'https://www.pfestartup.com/mail/reminder-create-first-project',
    'https://www.pfestartup.com/mail/reminder-customize-project-visuals',
    'https://www.pfestartup.com/mail/reminder-customize-project-team',
    'https://www.pfestartup.com/cron/delete-docs-requested',
];

foreach($urls as $url) {
    $s = curl_init();
    curl_setopt($s,CURLOPT_URL, $url . "/" . $token);
    curl_exec($s);
    curl_close($s);
    unset($s);
}