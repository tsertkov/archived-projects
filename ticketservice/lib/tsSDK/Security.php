<?php
/**
 * @category tsSDK
 * @package Security
 */

namespace tsSDK;

/**
 * Static security helpers
 */
class Security
{
    /**
     * Returns signature for request arguments
     * @param array $arguments
     * @param string $secret
     * @return string
     */
    static public function requestSignature(array $arguments, $secret)
    {
        ksort($arguments);
        $c = $secret;
        foreach ($arguments as $k => $v) {
            $c .= $k . $v;
        }

        return md5($c);
    }
}