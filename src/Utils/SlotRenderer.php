<?php

namespace Imanghafoori\Widgets\Utils;

trait SlotRenderer
{
    protected $slotName;

    protected $slots = [];

    /**
     * Start output buffer to get content of slot and set slot name
     * 
     * @param String $name
     */
    public function startSlot($name)
    {
        if (ob_start()) {
            $this->slotName = $name;
        }
    }

    /**
     * get slot content from widget block
     * 
     * @param String $data
     */
    public function renderSlot($data)
    {
        $this->slots[$this->slotName] = $data ?? "";
    }

    /**
     * check if widget has any slots
     * 
     * @return Boolean
     */
    public function hasSlots()
    {
        return !empty($this->slots);
    }

    /**
     * get and clean current slots
     * 
     * @return Array $slots
     */
    public function getSlots()
    {
        $slots = $this->slots;

        $this->slots = [];

        return $slots;
    }
}
