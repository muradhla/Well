<?php
/*
 * WellCommerce Open-Source E-Commerce Platform
 *
 * This file is part of the WellCommerce package.
 *
 * (c) Adam Piotrowski <adam@wellcommerce.org>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */

namespace WellCommerce\Bundle\CoreBundle\Form\Filters;

use WellCommerce\Bundle\CoreBundle\Form\AbstractFilter;

/**
 * Class NoCode
 *
 * @package WellCommerce\Bundle\CoreBundle\Form\Filters
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
class NoCode extends AbstractFilter implements FilterInterface
{
    /**
     * Strips all html code from value
     *
     * @param $value
     *
     * @return mixed|string
     */
    public function filterValue($value)
    {
        return strip_tags($value);
    }

}
