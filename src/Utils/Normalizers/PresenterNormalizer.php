<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

use Imanghafoori\Widgets\Utils\NormalizerContract;

class PresenterNormalizer implements NormalizerContract
{
    /**
     * Figures out which method should be called as the presenter.
     * @param object $widget
     * @return null
     */
    public function normalize($widget)
    {
        if (property_exists($widget, 'presenter')) {
            $presenter = $widget->presenter;
            $this->checkPresenterExists($presenter);
        } else {
            $presenter = get_class($widget).'Presenter';
            if (! class_exists($presenter)) {
                return $widget->presenter = null;
            }
        }

        $this->checkPresentMethodExists($presenter);

        $widget->presenter = $presenter.'@present';
    }

    /**
     * @param string $presenter
     */
    private function checkPresentMethodExists($presenter)
    {
        if (! method_exists($presenter, 'present')) {
            throw new \InvalidArgumentException("'present' method not found on : ".$presenter);
        }
    }

    /**
     * @param string $presenter
     */
    private function checkPresenterExists($presenter)
    {
        if (! class_exists($presenter)) {
            throw new \InvalidArgumentException("Presenter Class [{$presenter}] not found.");
        }
    }
}
