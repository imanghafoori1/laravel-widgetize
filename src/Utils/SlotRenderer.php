<?php

namespace Imanghafoori\Widgets\Utils;

trait SlotRenderer
{
    protected $slotName;

    protected $slots = [];

    public function startSlot($name)
    {
        if (ob_start()) {
            $this->slotName = $name;
        }
    }

    public function renderSlot($data)
    {
        $this->slots[$this->slotName] = $data ?? "";
    }

    public function hasSlot()
    {
        return !empty($this->slots);
    }

    /**
     * Assign slots to $_viewData
     */
    private function assignSlots()
    {
        if ($this->hasSlot())
            $this->_viewData = array_merge($this->_viewData, $this->slots);

        $this->slots = [];
    }
}
