<?php

namespace Imanghafoori\Widgets\Utils\Normalizers;

use Imanghafoori\Widgets\Utils\NormalizerContract;

class PresenterNormalizer implements NormalizerContract
{
    /**
     * Figures out which method should be called as the presenter.
     * @param object $widget
     * @return void
     */
    public function normalize($widget) : void
    {
        $presenter = $this->figureOutPresenterClass($widget);

        if ($widget->presenter) {
            $this->checkPresentMethodExists($presenter);
            $widget->presenter = $presenter.'@present';
        }
 
    }

    /**
     * @param string $presenter
     */
    private function checkPresentMethodExists($presenter) : void
    {
        if (! method_exists($presenter, 'present')) {
            throw new \InvalidArgumentException("'present' method not found on : ".$presenter);
        }
    }

    /**
     * @param string $presenter
     */
    private function checkPresenterExists($presenter): void
    {
        if (! class_exists($presenter)) {
            throw new \InvalidArgumentException("Presenter Class [{$presenter}] not found.");
        }
    }

    /**
     * @param $widget
     * @return string
     */
    private function figureOutPresenterClass($widget): string
    {
        if (property_exists($widget, 'presenter')) {
            $presenter = $widget->presenter;
            $this->checkPresenterExists($presenter);
        } else {
            $presenter = get_class($widget).'Presenter';
            if (! class_exists($presenter)) {
                $widget->presenter = null;
            }
        }

        return $presenter;
    }
}
