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

namespace WellCommerce\Bundle\ProductBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use WellCommerce\Bundle\CoreBundle\Form\DataTransformer\CollectionToArrayTransformer;
use WellCommerce\Bundle\CoreBundle\Form\Elements\ElementInterface;
use WellCommerce\Bundle\ProductBundle\Entity\ProductAttribute;

/**
 * Class ProductAttributeCollectionToArrayTransformer
 *
 * @author  Adam Piotrowski <adam@wellcommerce.org>
 */
class ProductAttributeCollectionToArrayTransformer extends CollectionToArrayTransformer
{
    /**
     * @var \WellCommerce\Bundle\ProductBundle\Repository\ProductAttributeRepositoryInterface
     */
    protected $repository;

    /**
     * {@inheritdoc}
     */
    public function transform($collection, ElementInterface $element)
    {
        $items = [];

        foreach ($collection as $item) {
            $items[] = $this->convertItemToArray($item);
        }

        return $items;
    }

    /**
     * Converts collection item to array representation
     *
     * @param ProductAttribute $item
     *
     * @return array
     */
    protected function convertItemToArray(ProductAttribute $item)
    {
        return [
            'id'           => $item->getId(),
            'suffix'       => $item->getModifierType(),
            'modifier'     => $item->getModifierValue(),
            'stock'        => $item->getStock(),
            'symbol'       => $item->getSymbol(),
            'weight'       => $item->getWeight(),
            'deletable'    => true,
            'availability' => $this->transformAvailability($item->getAvailability()),
            'attributes'   => $this->transformValues($item->getAttributeValues()),
        ];
    }

    /**
     * Transforms availability identifier into entity
     *
     * @param $entity
     *
     * @return int
     */
    private function transformAvailability($entity)
    {
        if (null == $entity) {
            return 0;
        }
        $meta       = $this->repository->getMetadata();
        $identifier = $meta->getSingleIdentifierFieldName();
        $accessor   = $this->repository->getPropertyAccessor();

        return $accessor->getValue($entity, $identifier);
    }

    /**
     * Transforms values collection to identifiers
     *
     * @param PersistentCollection $collection
     *
     * @return array
     */
    public function transformValues(PersistentCollection $collection)
    {
        if (null == $collection) {
            return [];
        }

        $values = [];
        foreach ($collection as $item) {
            $values[$item->getAttribute()->getId()] = $item->getId();
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform(ElementInterface $element, $data)
    {
        $values     = $element->getValue();
        $collection = new ArrayCollection();
        if (null == $data || empty($data)) {
            return $collection;
        }
        foreach ($data as $id => $values) {
            if (is_array($values)) {
                $item = $this->repository->findOrCreate($id, $values);
                $collection->add($item);
            }
        }

        return $collection;
    }
}