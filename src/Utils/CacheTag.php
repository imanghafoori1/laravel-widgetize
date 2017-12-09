<?php

namespace Imanghafoori\Widgets\Utils;

class CacheTag
{
    private $tagTokens = [];

    /**
     * @param $tag string
     * @return string The old existing token or a brand new one.
     */
    public function getTagToken($tag)
    {
        if (isset($this->tagTokens[$tag])) {
            return $this->tagTokens[$tag];
        }

        $token = $this->getPersistedToken($tag);
        if ($token !== null) {
            $this->setTokenInMemory($tag, $token);

            return $token;
        }

        // In Case no token has been generated for tag yet.
        return $this->generateNewToken($tag);
    }

    /**
     * Generates a brand new token.
     * @param $tag string
     * @return string
     */
    public function generateNewToken($tag)
    {
        $token = str_random(7);
        $this->setTokenInMemory($tag, $token);
        $this->persistToken($tag, $token);

        return $token;
    }

    /**
     * Set token in Memory for fast access within the same request.
     * @param $tag string
     * @param $token string
     * @return string
     */
    private function setTokenInMemory($tag, $token)
    {
        return $this->tagTokens[$tag] = $token;
    }

    /**
     * Save token to disk for later requests.
     * @param $tag string
     * @param $token string
     * @return void
     */
    private function persistToken($tag, $token)
    {
        \Cache::forever('9z10_o6cg_r'.$tag, $token);
    }

    /**
     * @param $tag string
     * @return string|null
     */
    private function getPersistedToken($tag)
    {
        return \Cache::get('9z10_o6cg_r'.$tag, null);
    }
}
