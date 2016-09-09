<?php
namespace Portrino\PxHybridAuth\ViewHelpers\Format;

/**
 * Class UppercaseFirstViewHelper
 *
 * @package Portrino\PxHybridAuth\ViewHelpers\Format
 *
 * Wrapper for PHPs ucfirst function.
 * @see http://www.php.net/manual/en/ucfirst
 *
 * = Examples =
 *
 * <code title="Example">
 * <h:format.uppercaseFirst>{textWithMixedCase}</h:format.uppercaseFirst>
 * </code>
 *
 * Output:
 * TextWithMixedCase
 *
 */
class UppercaseFirstViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * Uppercase first character
     *
     * @return string The altered string.
     */
    public function render()
    {
        $content = $this->renderChildren();
        return ucfirst($content);
    }
}