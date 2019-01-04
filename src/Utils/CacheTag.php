<?php

namespace Imanghafoori\Widgets\Utils;

class CacheTag
{
    /**
     * @var string[]
     */
    private $tagTokens = [];

    /**
     * @param $tag string
     * @return string The old existing token or a brand new one.
     */
    public function getTagToken(string $tag) : string
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
    public function generateNewToken(string $tag) : string
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
    private function setTokenInMemory(string $tag, string $token): string
    {
        return $this->tagTokens[$tag] = $token;
    }

    /**
     * Save token to disk for later requests.
     * @param $tag string
     * @param $token string
     * @return void
     */
    private function persistToken(string $tag, string $token): void
    {
        \Cache::forever('9z10_o6cg_r'.$tag, $token);
    }

    /**
     * @param $tag string
     * @return string|null
     */
    private function getPersistedToken(string $tag): ?string
    {
        return \Cache::get('9z10_o6cg_r'.$tag, null);
    }
}
