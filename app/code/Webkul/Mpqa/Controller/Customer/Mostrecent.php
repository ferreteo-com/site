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
namespace Webkul\Mpqa\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Stdlib\DateTime\Timezone;

class Mostrecent extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    protected $_review;
    protected $_answer;
    protected $_question;
    protected $_timezone;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Mpqa\Model\ReviewFactory $reviewFactory,
        \Webkul\Mpqa\Model\MpqaanswerFactory $answerFactory,
        \Webkul\Mpqa\Model\QuestionFactory $questionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Timezone $timezone
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_review=$reviewFactory;
        $this->_answer=$answerFactory;
        $this->_resource=$resource;
        $this->_question=$questionFactory;
        $this->_timezone = $timezone;
        $this->_resultJsonFactory=$resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $pid = $this->getRequest()->getParam('pid');
        $final_array=[];
        $questionCollection=$this->_question->create()->getCollection();
        $questionCollection->addFieldToFilter('product_id', $pid);
        $questionCollection->addFieldToFilter('status', 1);
        $questionCollection ->setPageSize(5);
        $questionCollection->setOrder('question_id', 'DESC');
        foreach ($questionCollection as $key) {
            $response=[];
            $answers=$this->getQuestionAnswers($key->getQuestionId());
            if (!empty($answers)) {
                foreach ($answers as $ans) {
                    $likes=0;
                    $dislikes=0;
                    $reviews=$this->getReview($ans->getAnswerId());
                    $like='like';
                    $dislike='dislike';
                    foreach ($reviews as $rev) {
                        $returnData = $this->
                        getForeachData($rev, $likes, $dislikes, $key, $like, $dislike);
                        $likes = $returnData[0];
                        $dislikes = $returnData[1];
                        $like = $returnData[2];
                        $dislike = $returnData[3];
                    }
                    $response['likes']=$likes;
                    $response['dislikes']=$dislikes;
                    $response['like_class']=$like;
                    $response['dislike_class']=$dislike;
                    $response['answer_id']=$ans->getAnswerId();
                    $response['answer']=$ans->getContent();
                    $response['nickname']=$ans->getRespondNickname();
                    $response['createdat'] = $this->_timezone->formatDate(
                        $ans->getCreatedAt(),
                        \IntlDateFormatter::MEDIUM
                    );
                }
            }
            $answer_count = $this->getAnswerCount($key->getQuestionId());
            $response['count']=$answer_count;
            $response['qa_nickname']=$key->getQaNickname();
            $response['qa_date'] = $this->_timezone->formatDate($key->getCreatedAt(), \IntlDateFormatter::MEDIUM);
            $response['question_id']=$key->getQuestionId();
            $response['content']=$key->getContent();
            $response['subject']=$key->getSubject();
            array_push($final_array, $response);
        }
        $result = $this->_resultJsonFactory->create();
        $result->setData($final_array);
        return $result;
    }

    public function getQuestionAnswers($qid)
    {
        return $this->_answer->create()->getCollection()
            ->addFieldToFilter('question_id', $qid)
            ->setPageSize(1);
    }

    public function getReview($id)
    {
        return $this->_review->create()->getCollection()->addFieldToFilter('answer_id', $id);
    }
    /**
     * get count of answer for each question
     * @return count
     */
    public function getAnswerCount($qid)
    {
        $collection = $this->_answer->create()->getCollection()
            ->addFieldToFilter('question_id', $qid);
        return $collection->getSize();
    }

    /**
     * [getForeachData description]
     *
     * @param $rev         [$rev description]
     * @param $likes       [$likes description]
     * @param $dislikes    [$dislikes description]
     * @param $customerId  [$customerId description]
     * @param $key         [$key description]
     * @param $like        [$like description]
     * @param $dislike     [$dislike description]
     *
     * @return             [$likes, $dislikes, $like, $dislike]
     */
    public function getForeachData($rev, $likes, $dislikes, $key, $like, $dislike)
    {
        if ($rev->getLikeDislike()==1) {
            $likes++;
        } else {
            $dislikes++;
        }
        if ($rev->getReviewFrom()==$this->getRequest()->getParam('custid')) {
            if ($key->getLikeDislike()==1) {
                $like='liked';
            } else {
                $dislike='disliked';
            }
        }
        return [$likes, $dislikes, $like, $dislike];
    }
}
