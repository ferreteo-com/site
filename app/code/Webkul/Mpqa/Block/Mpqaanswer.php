<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mpqa
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Mpqa\Block;

use Magento\Customer\Model\Customer;

/**
 * Webkul Mpqa Mpqaanswer Block
 */
class Mpqaanswer extends \Magento\Framework\View\Element\Template
{

    protected $_urlinterface;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    protected $_question;

    protected $_answer;

    protected $_imageHelper;

    /**
     * @var ObjectManagerInterface
     */
    protected $_productloader;
    protected $_customer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context               $context
     * @param \Magento\Customer\Model\Session                                $customerSession
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Webkul\Mpqa\Model\QuestionFactory $questionFactory,
        \Webkul\Mpqa\Model\MpqaanswerFactory $answerFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Customer\Model\CustomerFactory $customer,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->_urlinterface = $context->getUrlBuilder();
        $this->_customerSession = $customerSession;
        $this->_question=$questionFactory;
        $this->_answer=$answerFactory;
        $this->_imageHelper = $context->getImageHelper();
        $this->_productloader=$_productloader;
        $this->_customer=$customer;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    /**
     * get parameters
     * @return array
     */
    public function getParams()
    {
        $data = $this->getRequest()->getParams();
        return $data;
    }

    /**
     * get seller coupons
     * @return object
     */
    public function getQuestion()
    {
        $data = $this->getParams();
        if ($data['id']) {
            $collection = $this->_question
                ->create()->load($data['id']);

            return $collection;
        }
    }

    /**
     * Get Product Id
     *
     * @return object
     */
    public function getProduct()
    {
        if ($this->getQuestion()->getProductId()) {
            return $this->_productloader->create()->load($this->getQuestion()->getProductId());
        }
    }

    /**
     * Get Image Helper
     */
    public function imageHelperObj()
    {
        return $this->_imageHelper;
    }
    
    public function getAnswers()
    {
        return $this->_answer->create()->getCollection()
            ->addFieldToFilter('question_id', $this->getRequest()->getParam('id'))->setOrder('created_at', 'DESC');
    }

    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    public function getCustomerById($id)
    {
        return $this->_customer->create()->load($id);
    }

    /**
     * Get IsSecure
     */
    public function getIsSecure()
    {
        return $this->getRequest()->isSecure();
    }

    /**
     * Get Json Helper Data
     */
    public function getJsonHelper()
    {
        return $this->jsonHelper;
    }
}
