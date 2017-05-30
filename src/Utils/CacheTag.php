<?php

namespace Imanghafoori\Widgets\Utils;

class CacheTag
{
    private $tagTokens = [];

    /**
     * @param $tag string
     * @return array
     */
    public function getTagToken($tag)
    {
        if (isset($this->tagTokens[$tag])) {
            return $this->tagTokens[$tag];
        }

        $token = $this->getPersistedToken($tag);
        if ($token) {
            $this->setTokenInMemory($tag, $token);
            return $token;
        }

        // In Case no token has been generated for tag yet.
        return $this->generateNewToken($tag);
    }


    /**
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

    private function setTokenInMemory($tag, $token)
    {
        return $this->tagTokens[$tag] = $token;
    }

    /**
     * @param $tag
     * @param $token
     */
    private function persistToken($tag, $token)
    {
        \Cache::forever('9z10_o6cg_r'.$tag, $token);
    }

    /**
     * @param $tag
     * @return mixed
     */
    private function getPersistedToken($tag)
    {
        return \Cache::get('9z10_o6cg_r'.$tag, null);
    }
}
