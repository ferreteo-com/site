<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-cataloglabel
 * @version   1.1.21
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CatalogLabel\Api\Service;

/**
 * M2.2. compatibility
 */
interface CompatibilityServiceInterface
{
    /**
     * Prepare Rule Data For Save ('conditions_serialized', 'actions_serialized')
     * @param string $value
     * @return \Magento\Framework\Serialize\Serializer\Json|false
     */
    public function prepareRuleDataForSave($value);

    /**
     * @return \Magento\Framework\Serialize\Serializer\Json|false
     */
    public function getSerializer();
}