<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Faqs
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Faqs\Controller\Form;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Result\Page;
use Mageplaza\Faqs\Helper\Data;
use Mageplaza\Faqs\Model\ArticleFactory;
use Mageplaza\Faqs\Model\Config\Source\Visibility;

/**
 * Class Submit
 * @package Mageplaza\Faqs\Controller\Form
 */
class Submit extends Action
{
    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var ArticleFactory
     */
    protected $_articleFactory;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param DateTime $dateTime
     * @param Data $helperData
     * @param ArticleFactory $articleFactory
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        Data $helperData,
        ArticleFactory $articleFactory
    ) {
        parent::__construct($context);

        $this->_date           = $dateTime;
        $this->_helperData     = $helperData;
        $this->_articleFactory = $articleFactory;
    }

    /**
     * @return ResponseInterface|Forward|ResultInterface|Page
     * @throws Exception
     */
    public function execute()
    {
        $params       = $this->getRequest()->getParams();
        $updatedAt    = $this->_date->date();
        $createdAt    = $updatedAt;
        $visibility   = ($this->_helperData->getConfigGeneral('question/need_approved'))
            ? Visibility::NEED_APPROVED : Visibility::HIDDEN;
        $articleData  = [
            'name'         => strip_tags($params['content']),
            'author_name'  => (strip_tags($params['name'])) ?: 'Guest',
            'author_email' => ($params['email']) ?: 'Guest@gmail.com',
            'visibility'   => $visibility,
            'position'     => 0,
            'store_ids'    => 0,
            'email_notify' => $params['is_notify'],
            'meta_robots'  => 0,
            'created_at'   => $createdAt,
            'updated_at'   => $updatedAt
        ];
        $articleModel = $this->_articleFactory->create();
        if (!empty($params['product_id'])) {
            $articleData ['product_id'] = (int) $params['product_id'];
        }
        $articleModel->setData($articleData)->save();
        $this->_helperData->sendEmailToAdmin($articleModel);
        $result = ['status' => true];

        return $this->getResponse()->representJson(Data::jsonEncode($result));
    }
}
