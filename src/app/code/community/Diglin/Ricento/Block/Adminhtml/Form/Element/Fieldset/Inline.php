<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author Sylvain Rayé <sylvain.raye at diglin.com>
 * @category    Diglin
 * @package     Diglin_Ricento
 * @copyright   Copyright (c) 2011-2014 Diglin (http://www.diglin.com)
 */

/**
 * Class Diglin_Ricento_Block_Adminhtml_Form_Element_Fieldset_Inline
 *
 * A group of elements, to be rendered inline like a single element
 */
class Diglin_Ricento_Block_Adminhtml_Form_Element_Fieldset_Inline extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('fieldset_inline');
    }
    public function getElementHtml()
    {
        return $this->getChildrenHtml();
    }
    public function getChildrenHtml()
    {
        $html = '';
        foreach ($this->getElements() as $element) {
            if ($element->getType() != 'fieldset') {
                $html.= $element->getElementHtml();
            }
        }
        return $html;
    }
}