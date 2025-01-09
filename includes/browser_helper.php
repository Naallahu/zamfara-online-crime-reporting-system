<?php
function getBrowserInfo($userAgent) {
    // Initialize browser info array
    $browserInfo = [
        'browser' => 'Unknown Browser',
        'version' => '',
        'os' => detectOS($userAgent),
        'device' => detectDevice($userAgent)
    ];
    
    // Enhanced browser detection with version
    if (preg_match('/Firefox\/([0-9.]+)/', $userAgent, $matches)) {
        $browserInfo['browser'] = 'Firefox';
        $browserInfo['version'] = $matches[1];
    } elseif (preg_match('/Chrome\/([0-9.]+)/', $userAgent, $matches)) {
        $browserInfo['browser'] = 'Chrome';
        $browserInfo['version'] = $matches[1];
    } elseif (preg_match('/Safari\/([0-9.]+)/', $userAgent, $matches)) {
        $browserInfo['browser'] = 'Safari';
        $browserInfo['version'] = $matches[1];
    } elseif (preg_match('/Edge\/([0-9.]+)/', $userAgent, $matches)) {
        $browserInfo['browser'] = 'Edge';
        $browserInfo['version'] = $matches[1];
    } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident/7')) {
        $browserInfo['browser'] = 'Internet Explorer';
        if (preg_match('/MSIE ([0-9.]+)/', $userAgent, $matches)) {
            $browserInfo['version'] = $matches[1];
        }
    }

    return formatBrowserInfo($browserInfo);
}

function detectOS($userAgent) {
    if (strpos($userAgent, 'Windows') !== false) {
        return 'Windows';
    } elseif (strpos($userAgent, 'Mac OS X') !== false) {
        return 'macOS';
    } elseif (strpos($userAgent, 'Linux') !== false) {
        return 'Linux';
    } elseif (strpos($userAgent, 'Android') !== false) {
        return 'Android';
    } elseif (strpos($userAgent, 'iOS') !== false) {
        return 'iOS';
    }
    return 'Unknown OS';
}

function detectDevice($userAgent) {
    if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $userAgent)) {
        return 'Tablet';
    } elseif (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $userAgent)) {
        return 'Mobile';
    }
    return 'Desktop';
}

function formatBrowserInfo($info) {
    $version = $info['version'] ? " {$info['version']}" : '';
    return "{$info['browser']}{$version} on {$info['os']} ({$info['device']})";
}
