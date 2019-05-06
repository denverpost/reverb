<?php

class WpProQuiz_Model_Csv extends WpProQuiz_Model_Mapper
{
    /**
     * @param $quizId
     * @param $page
     * @param $limit
     * @param int $users
     * @param int $startTime
     * @param int $endTime
     * @return WpProQuiz_Model_StatisticHistory[]
     */
    public function fetchHistory($quizId, $page, $limit, $users = -1, $startTime = 0, $endTime = 0)
    {
        $result = $this->fetchHistoryResult($quizId, $page, $limit, $users = -1, $startTime = 0, $endTime = 0);
        $r = array();
        $o = array();
        
        foreach ($result as $row) {
            if (!empty($row['user_login'])) {
                $row['user_name'] = $row['user_login'] . ' (' . $row['display_name'] . ')';
            }

            $row['form_data'] = $row['form_data'] === null ? null : @json_decode($row['form_data'], true);

            // Replace the form id with field name and prepare a new form data array
            $updatedFormData = array();
            if(!empty($row['form_data'])){
                $arrFormData = $row['form_data'];
                
                //Find the form id of this particular records seprated by comma
                $strFormId = "";
                foreach($arrFormData as $formId => $formData){
                    $strFormId .= $formId.','; 
                }
                $strFormId = rtrim($strFormId,',');

                //Find field name of each form id
                $fieldName = $this->_wpdb->get_results(
                    "SELECT fieldname,form_id
                        FROM
                            " . $this->_tableForm . "
                        WHERE
                            form_id IN(" . $strFormId . ")
                        ",
                    ARRAY_A
                );

                // With the help of field name and formdata we will create new array with field name
                foreach($arrFormData as $formId => $formData){
                    foreach($fieldName as $fn){
                        if($fn['form_id'] == $formId){
                            $updatedFormData[$fn['fieldname']] = $formData;
                        }
                    }
                }                
            }
            if(!empty($updatedFormData)) $row['form_data'] = $updatedFormData;

            $statisticUserMapper = new WpProQuiz_Model_StatisticUserMapper();
            $statisticUsers = $statisticUserMapper->fetchUserStatistic($row['statistic_ref_id'], $quizId,0);
            $output = array();

            foreach ($statisticUsers as $statistic) {
                /* @var $statistic WpProQuiz_Model_StatisticUser */

                if (!isset($output[$statistic->getCategoryId()])) {
                    $output[$statistic->getCategoryId()] = array(
                        'questions' => array(),
                    );
                }

                $o = &$output[$statistic->getCategoryId()];

                $o['questions'][] = array(
                    'questionId' => $statistic->getQuestionId(),
                    'statistcAnswerData' => $statistic->getStatisticAnswerData(),
                    'questionName' => $statistic->getQuestionName(),
                    'questionAnswerData' => $statistic->getQuestionAnswerData(),
                );
            }
            $row['question_data']= $o;
            $r[]=$row;
        }
        
        return $r;
    }
}
