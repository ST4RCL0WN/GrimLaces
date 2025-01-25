<?php

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
|
| Miscellaneous helper functions, primarily used for formatting.
|
*/

/**
 * Returns class name if the current URL corresponds to the given path.
 *
 * @param string $path
 * @param string $class
 *
 * @return string
 */
function set_active($path, $class = 'active') {
    return call_user_func_array('Request::is', (array) $path) ? $class : '';
}

/**
 * Adds a help icon with a tooltip.
 *
 * @param string $text
 *
 * @return string
 */
function add_help($text) {
    return '<i class="fas fa-question-circle help-icon" data-toggle="tooltip" title="'.$text.'"></i>';
}

/**
 * Uses the given array to generate breadcrumb links.
 *
 * @param array $links
 *
 * @return string
 */
function breadcrumbs($links) {
    $ret = '<nav><ol class="breadcrumb">';
    $count = 0;
    $ret .= '<li class="breadcrumb-item"><a href="'.url('/').'">'.config('lorekeeper.settings.site_name', 'Lorekeeper').'</a></li>';
    foreach ($links as $key => $link) {
        $isLast = ($count == count($links) - 1);

        $ret .= '<li class="breadcrumb-item ';
        if ($isLast) {
            $ret .= 'active';
        }
        $ret .= '">';

        if (!$isLast) {
            $ret .= '<a href="'.url($link).'">';
        }
        $ret .= $key;
        if (!$isLast) {
            $ret .= '</a>';
        }

        $ret .= '</li>';

        $count++;
    }
    $ret .= '</ol></nav>';

    return $ret;
}

/**
 * Formats the timestamp to a standard format.
 *
 * @param Illuminate\Support\Carbon\Carbon $timestamp
 * @param mixed                            $showTime
 *
 * @return string
 */
function format_date($timestamp, $showTime = true) {
    return $timestamp->format('j F Y'.($showTime ? ', H:i:s' : '')).($showTime ? ' <abbr data-toggle="tooltip" title="UTC'.$timestamp->timezone->toOffsetName().'">'.strtoupper($timestamp->timezone->getAbbreviatedName($timestamp->isDST())).'</abbr>' : '');
}

function pretty_date($timestamp, $showTime = true) {
    return '<abbr data-toggle="tooltip" title="'.$timestamp->format('F j Y'.($showTime ? ', H:i:s' : '')).' '.strtoupper($timestamp->timezone->getAbbreviatedName($timestamp->isDST())).'">'.$timestamp->diffForHumans().'</abbr>';
}

/**
 * Formats a number to fit the number of digits given,
 * for generating masterlist numbers.
 *
 * @param mixed $number
 * @param mixed $digits
 *
 * @return string
 */
function format_masterlist_number($number, $digits) {
    return sprintf('%0'.$digits.'d', $number);
}

/**
 * Generates a string of random characters of the specified length.
 *
 * @param int $characters
 *
 * @return string
 */
function randomString($characters) {
    $src = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $code = '';
    for ($i = 0; $i < $characters; $i++) {
        $code .= $src[mt_rand(0, strlen($src) - 1)];
    }

    return $code;
}

/**
 * Check that a url is from a site used for authentication,
 * and if it belongs to a user.
 *
 * @param string $url
 * @param bool   $failOnError
 *
 * @return App\Models\User\User|string
 */
function checkAlias($url, $failOnError = true) {
    if ($url) {
        $recipient = null;
        $matches = [];
        // Check to see if url is 1. from a site used for auth
        foreach (config('lorekeeper.sites') as $key=> $site) {
            if (isset($site['auth']) && $site['auth']) {
                preg_match_all($site['regex'], $url, $matches, PREG_SET_ORDER, 0);
                if ($matches != []) {
                    $urlSite = $key;
                    break;
                }
            }
        }
        if ((!isset($matches[0]) || $matches[0] == []) && $failOnError) {
            throw new Exception('This URL is from an invalid site. Please provide a URL for a user profile from a site used for authentication.');
        }

        // and 2. if it contains an alias associated with a user on-site.

        if (isset($matches[0]) && $matches[0] != [] && isset($matches[0][1])) {
            if ($urlSite != 'discord') {
                $alias = App\Models\User\UserAlias::where('site', $urlSite)->where('alias', $matches[0][1])->first();
            } else {
                $alias = App\Models\User\UserAlias::where('site', $urlSite)->where('alias', $matches[0][0])->first();
            }
            if ($alias) {
                $recipient = $alias->user;
            } else {
                $recipient = $url;
            }
        }

        return $recipient;
    }
}

/**
 * Prettifies links to user profiles on various sites in a "user@site" format.
 *
 * @param string $url
 *
 * @return string
 */
function prettyProfileLink($url) {
    $matches = [];
    // Check different sites and return site if a match is made, plus username (retreived from the URL)
    foreach (config('lorekeeper.sites') as $siteName=> $siteInfo) {
        if (preg_match_all($siteInfo['regex'], $url, $matches)) {
            $site = $siteName;
            $name = $matches[1][0];
            $link = $matches[0][0];
            $icon = $siteInfo['icon'] ?? 'fas fa-globe';
            break;
        }
    }

    // Return formatted link if possible; failing that, an unformatted link
    if (isset($name) && isset($site) && isset($link)) {
        return '<a href="https://'.$link.'"><i class="'.$icon.' mr-1" style="opacity: 50%;"></i>'.$name.'@'.(config('lorekeeper.sites.'.$site.'.display_name') != null ? config('lorekeeper.sites.'.$site.'.display_name') : $site).'</a>';
    } else {
        return '<a href="'.$url.'"><i class="fas fa-globe mr-1" style="opacity: 50%;"></i>'.$url.'</a>';
    }
}

/**
 * Prettifies user profile names for use in various functions.
 *
 * @param string $url
 *
 * @return string
 */
function prettyProfileName($url) {
    $matches = [];
    // Check different sites and return site if a match is made, plus username (retreived from the URL)
    foreach (config('lorekeeper.sites') as $siteName=> $siteInfo) {
        if (preg_match_all($siteInfo['regex'], $url, $matches)) {
            $site = $siteName;
            $name = $matches[1][0];
            break;
        }
    }

    // Return formatted name if possible; failing that, an unformatted url
    if (isset($name) && isset($site)) {
        return $name.'@'.(config('lorekeeper.sites.'.$site.'.display_name') != null ? config('lorekeeper.sites.'.$site.'.display_name') : $site);
    } else {
        return $url;
    }
}

/**
 * Returns the given objects limits, if any.
 *
 * @param mixed $object
 *
 * @return bool
 */
function getLimits($object) {
    return App\Models\Limit\Limit::where('object_model', get_class($object))->where('object_id', $object->id)->get();
}

/**
 * Checks the site setting and returns the appropriate FontAwesome version.
 *
 * @return string
 */
function faVersion() {
    $setting = config('lorekeeper.settings.fa_version');
    $directory = 'css';

    switch ($setting) {
        case 0:
            $version = 'allv5';
            break;
        case 1:
            $version = 'allv6';
            break;
        case 2:
            $version = 'allvmix';
            break;
    }

    return asset($directory.'/'.$version.'.min.css');
}

/****************************************************************************************
 *
 * PARSING FUNCTIONS
 *
 ****************************************************************************************/

/**
 * Parses a piece of user-entered text for HTML output and optionally gets pings.
 *
 * @param string $text
 * @param array  $pings
 *
 * @return string
 */
function parse($text, &$pings = null) {
    if (!$text) {
        return null;
    }

    require_once base_path().'/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';

    $config = HTMLPurifier_Config::createDefault();
    $config->set('Attr.EnableID', true);
    $config->set('HTML.DefinitionID', 'include');
    $config->set('HTML.DefinitionRev', 2);
    if ($def = $config->maybeGetRawHTMLDefinition()) {
        $def->addElement('include', 'Block', 'Empty', 'Common', ['file*' => 'URI', 'height' => 'Text', 'width' => 'Text']);
        $def->addAttribute('a', 'data-toggle', 'Enum#collapse,tab');
        $def->addAttribute('a', 'aria-expanded', 'Enum#true,false');
        $def->addAttribute('a', 'data-target', 'Text');
        $def->addAttribute('div', 'data-parent', 'Text');

        // mentions
        $elements = ['a', 'div', 'span', 'p', 'img'];
        foreach ($elements as $element) {
            $def->addAttribute($element, 'data-mention-type', 'Text');
            $def->addAttribute($element, 'data-id', 'Number');
        }
    }

    $purifier = new HTMLPurifier($config);
    $text = $purifier->purify($text);

    $text = parseMentions($text, $pings);

    return $text;
}

/**
 * Parses a piece of user-entered text to match mentions,
 * We don't replace the text and instead modify it on display to allow for name, image, hash, etc. changes, without breaking links or mentions.
 *
 * @param string $text
 * @param array  $pings
 *
 * @return string
 */
function parseMentions($text, &$pings) {
    $matches = [];
    $count = preg_match_all(
        '/<([^ >]+)[^>]*data-mention-type="([^"]+)"[^>]*data-id="([^"]+)"[^>]*>(.*?)<\/\1>/s',
        $text,
        $matches,
        PREG_SET_ORDER
    );

    if ($count) {
        foreach ($matches as $match) {
            $parentElement = $match[0];
            $type = $match[2];
            $id = $match[3];

            $model = getAssetModelString($type);
            $object = $model::find($id);

            if (!$object) {
                continue;
            }

            $pings[$type][] = $object;
            $hasImage = preg_match('/<img[^>]+>/i', $parentElement);
            $text = str_replace($parentElement, $hasImage ? $object->mentionImage : $object->mentionDisplayName, $text);
        }
    }

    return $text;
}

/**
 * Sends a notification to users or character's owners.
 *
 * @param mixed $pings
 * @param mixed $user
 * @param mixed $mention
 */
function sendNotifications($pings, $user, $mention) {
    if ($pings) {
        foreach ($pings as $type => $objects) {
            foreach ($objects as $object) {
                if ($type == 'user' && $object->id != $user->id) {
                    App\Facades\Notifications::create('MENTIONED', $object, [
                        'sender_url'     => $user->url,
                        'sender_name'    => $user->name,
                        'mention_target' => 'you',
                        'mention_url'    => $mention->url,
                        'mention_type'   => $mention->mentionType,
                    ]);
                } elseif ($type == 'character' && $object->user->id != $user->id) {
                    App\Facades\Notifications::create('MENTIONED', $object->user, [
                        'sender_url'     => $user->url,
                        'sender_name'    => $user->name,
                        'mention_target' => 'your character '.$object->displayName,
                        'mention_url'    => $mention->url,
                        'mention_type'   => $mention->mentionType,
                    ]);
                }
            }
        }
    }
}
