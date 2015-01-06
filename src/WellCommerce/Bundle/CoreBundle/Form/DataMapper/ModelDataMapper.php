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

namespace WellCommerce\Bundle\CoreBundle\Form\DataMapper;

use WellCommerce\Bundle\CoreBundle\Form\Elements\ElementCollection;
use WellCommerce\Bundle\CoreBundle\Form\Elements\ElementInterface;
use WellCommerce\Bundle\CoreBundle\Form\Elements\FormInterface;

/**
 * Class ModelDataMapper
 *
 * @author Adam Piotrowski <adam@wellcommerce.org>
 */
class ModelDataMapper extends AbstractDataMapper
{
    /**
     * Maps model data to form elements
     *
     * @param FormInterface $form
     */
    public function mapDataToForm(FormInterface $form)
    {
        $this->mapModelDataToElementCollection($form->getChildren());
    }

    /**
     * Maps all submitted values to model data representation
     *
     * @param FormInterface $form
     */
    public function mapFormToData(FormInterface $form)
    {
        $this->mapElementCollectionToModelData($form->getChildren());
    }

    /**
     * @param ElementCollection $children
     */
    protected function mapElementCollectionToModelData(ElementCollection $children)
    {
        $children->forAll(function (ElementInterface $child) {
            $this->mapElementToModelData($child);
        });
    }

    protected function mapElementToModelData(ElementInterface $child)
    {
        $this->setModelValueFromElement($child);

        $children = $child->getChildren();
        if ($children->count()) {
            $this->mapElementCollectionToModelData($children);
        }

    }

    /**
     * Maps data to single element
     *
     * @param ElementInterface $child
     */
    protected function mapModelDataToElement(ElementInterface $child)
    {
        $this->setDefaultElementValue($child);

        $children = $child->getChildren();

        if ($children->count()) {
            $this->mapModelDataToElementCollection($children);
        }
    }

    /**
     * Maps data using recursion to all children
     *
     * @param ElementCollection $children
     */
    protected function mapModelDataToElementCollection(ElementCollection $children)
    {
        foreach ($children->all() as $child) {
            $this->mapModelDataToElement($child);
        }
    }

    /**
     * Sets value for element
     *
     * @param ElementInterface $element
     */
    protected function setDefaultElementValue(ElementInterface $element)
    {
        if ($element->hasPropertyPath()) {
            $propertyPath = $element->getPropertyPath();
            if ($this->propertyAccessor->isReadable($this->data, $propertyPath)) {
                $value = $this->propertyAccessor->getValue($this->data, $propertyPath);
                if (null === $value) {
                    $value = $element->getDefaultValue();
                }

                if ($element->hasTransformer()) {
                    $transformer = $element->getTransformer();
                    $value       = $transformer->transform($value, $propertyPath);
                }

                $element->setValue($value);
            }
        }
    }

    /**
     * Transforms value if needed or directly changes model property
     *
     * @param ElementInterface $child
     */
    protected function setModelValueFromElement(ElementInterface $child)
    {
        if ($child->hasPropertyPath()) {
            $propertyPath = $child->getPropertyPath();
            if ($this->propertyAccessor->isWritable($this->data, $propertyPath)) {
                if ($child->hasTransformer()) {
                    $transformer = $child->getTransformer();
                    $transformer->reverseTransform($this->data, $propertyPath, $child->getValue());
                } else {
                    $this->propertyAccessor->setValue($this->data, $propertyPath, $child->getValue());
                }
            }
        }
    }
}