<?php

namespace MediaAlchemyst\Transmuter;

use Imagine\Image\Box;
use MediaAlchemyst\Specification\Image;
use MediaAlchemyst\Specification\SpecificationInterface;
use MediaAlchemyst\Exception\InvalidArgumentException;
use MediaVorus\Media\MediaInterface;
use Pimple;

abstract class AbstractTransmuter
{
    /**
     *
     * @var Pimple
     */
    protected $container;

    public function __construct(Pimple $container)
    {
        $this->container = $container;
    }

    public function __destruct()
    {
        $this->container = null;
    }

    /**
     * Return the box for a spec
     *
     * @param  Specification\Image $spec
     * @param  MediaInterface               $source
     * @return \Image\Box
     */
    protected function boxFromImageSpec(Image $spec, MediaInterface $source)
    {
        if ( ! $spec->getWidth() && ! $spec->getHeight()) {
            throw new InvalidArgumentException('The specification you provide must have width nad height');
        }

        if ($spec->getResizeMode() == Image::RESIZE_MODE_INBOUND_FIXEDRATIO) {

            $ratioOut = $spec->getWidth() / $spec->getHeight();
            $ratioIn = $source->getWidth() / $source->getHeight();

            if ($ratioOut > $ratioIn) {

                $outHeight = round($spec->getHeight());
                $outWidth = round($ratioIn * $outHeight);
            } else {

                $outWidth = round($spec->getWidth());
                $outHeight = round($outWidth / $ratioIn);
            }

            return new Box($outWidth, $outHeight);
        }

        return new Box($spec->getWidth(), $spec->getHeight());
    }

    abstract public function execute(SpecificationInterface $spec, MediaInterface $source, $dest);
}