<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

class PresenterNormalizer
{
    /**
     * Figures out which method should be called as the presenter
     * @return null
     */
    public function normalizePresenterName($widget)
    {
        if ($widget->presenter !== 'default') {
            $presenter = $widget->presenter;
            $this->checkPresenterExists($presenter);
        } else {
            $presenter = get_class($widget) . 'Presenter';
            if (!class_exists($presenter)) {
                return $widget->presenter = null;
            }
        }

        $this->checkPresentMethodExists($presenter);

        $widget->presenter = $presenter . '@present';
    }

    /**
     * @param $presenter
     */
    private function checkPresentMethodExists($presenter)
    {
        if (!method_exists($presenter, 'present')) {
            throw new \InvalidArgumentException("'present' method not found on : " . $presenter);
        }
    }

    /**
     * @param $presenter
     */
    private function checkPresenterExists($presenter)
    {
        if (!class_exists($presenter)) {
            throw new \InvalidArgumentException("Presenter Class [{$presenter}] not found.");
        }
    }
}
