<?php
namespace App\Helpers;
use Mockery\Exception;

/**
 * Class Expression
 *
 * Generate regular expression helpers for social integration
 * @package App\Helpers
 */
class Expression
{
    /**
     * Expression to match a username.
     *
     * @var  string
     */
    const REGEX_USERNAME_MENTION = '/(^|[^a-z0-9_])[@＠]([a-z0-9_]{1,20})/iu';

    /**
     * Expression to match a hashtag.
     *
     * @var  string
     */
    const REGEX_HASHTAG = '/(^|[^0-9A-Z&\/\?]+)([#＃]+)([0-9A-Z_]*[A-Z_]+[a-z0-9_üÀ-ÖØ-öø-ÿ]*)/iu';

    /**
     * Expression available patterns
     *
     * @var  array
     */
    private static $available_patterns = ['mentions', 'hashtags'];

    /**
     * Parse text
     *
     * @param string $text
     * @param string $pattern
     * @return array
     */
    public static function parseText($text = null, $pattern)
    {
        if(!in_array($pattern, self::$available_patterns))
            throw new Exception("Invalid Expression pattern");

        return self::{'parse'.ucfirst($pattern)}($text);
    }

    /**
     * Search for mention pattern:  (at)+word
     *
     * @param string $text
     * @return array
     */
    private static function parseMentions($text = null)
    {
        if($text == null || empty($text)) return [];

        preg_match_all(self::REGEX_USERNAME_MENTION, trim($text), $result);
        list($_all, $_before, $username, $after) = array_pad($result, 4, '');

        unset($_all);
        unset($_before);

        $usernames = [];

        for ($i = 0; $i < count($username); $i ++)
        {
            # If $after is not empty, there is an invalid character.
            if (!empty($after[$i])) continue;
            array_push($usernames, $username[$i]);
        }

        return array_unique($usernames);
    }

    /**
     * Search for hashtags pattern: #word
     *
     * @param string $text
     * @return array
     */
    private static function parseHashtags($text = null)
    {
        if($text == null || empty($text)) return [];

        preg_match_all(self::REGEX_HASHTAG, trim($text), $result);

        if (!isset($result[3]) || count($result[3]) == 0 || count($result) < 3) return [];

        return array_unique($result[3]);
    }
}