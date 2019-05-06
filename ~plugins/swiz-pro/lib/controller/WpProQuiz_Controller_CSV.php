<?php

class WpProQuiz_Controller_CSV extends WpProQuiz_Controller_Controller
{
    /**
     * ajaxExportCsv
     *
     * Collects data for CSV and returns to js
     *
     * @param   array
     * @return  json
     */
    public static function ajaxExportCsv($data)
    {
        if (!current_user_can('wpProQuiz_show_statistics')) {
            return json_encode(array());
        }

        $statisticRefMapper = new WpProQuiz_Model_Csv();
        $formMapper = new WpProQuiz_Model_FormMapper();
        $questionMapper = new WpProQuiz_Model_QuestionMapper();
        $parentController = new WpProQuiz_Controller_Controller();

        $quizId = $data['quizId'];

        $forms = $formMapper->fetch($quizId);

        $page = (isset($data['page']) && $data['page'] > 0) ? $data['page'] : 1;
        $limit = $data['pageLimit'];
        $start = $limit * ($page - 1);

        $startTime = (int)$data['dateFrom'];
        $endTime = (int)$data['dateTo'] ? $data['dateTo'] + 86400 : 0;

        $statisticModel = $statisticRefMapper->fetchHistory($quizId, $start, $limit, $data['users'], $startTime,
            $endTime);
        $arrCSVData = array();

        foreach($statisticModel as $arrSM){
            if(!$arrSM['user_id']){
                $arrSM['display_name'] = "Anonymous";
            }else{
                if($arrSM['user_name'] == ''){
                    $arrSM['display_name'] = "Deleted user";
                }
            }

            $dDateTime = WpProQuiz_Helper_Until::convertTime($arrSM['create_time'],get_option('wpProQuiz_statisticTimeFormat', 'Y/m/d g:i A'));
            $arrSM['date_time'] = $dDateTime;

            $iCorrectCount = isset($arrSM['correct_count']) ? $arrSM['correct_count'] : 0;
            $iInCorrectCount = isset($arrSM['incorrect_count']) ? $arrSM['incorrect_count'] : 0;
            $iPoints = isset($arrSM['points']) ? $arrSM['points'] : 0;
            $iG_Points = isset($arrSM['g_points']) ? $arrSM['g_points'] : 0;
            $iSolvedCount = isset($arrSM['solved_count']) ? $arrSM['solved_count'] : 0;
            $iSum = $iCorrectCount + $iInCorrectCount;

            $strFormatCorrect = $strFormatInCorrect = $strResult = "0 %";

            if($iCorrectCount != 0 && $iInCorrectCount != 0 && $iSum != 0){
                $strFormatCorrect = ($iCorrectCount . ' (' . round(100 * $iCorrectCount / $iSum, 2) . '%)');
                $strFormatInCorrect = ($iInCorrectCount . ' (' . round(100 * $iInCorrectCount / $iSum, 2) . '%)');
            }

            if($iPoints != 0 && $iG_Points != 0){
                $strResult = round(100 * $iPoints / $iG_Points, 2) . '%';
            }

            $arrSM['correct_count'] = $strFormatCorrect;
            $arrSM['incorrect_count'] = $strFormatInCorrect;
            $arrSM['solved_count'] = $iSolvedCount." of ".$iSum;
            $arrSM['results'] = $strResult;

            if(isset($arrSM['question_data']['questions']) && !empty($arrSM['question_data']['questions'])){
                $arrQuestions = $arrSM['question_data']['questions'];
                foreach($arrQuestions as $arrQ){
                    if($arrQ['questionName'] && $arrQ['statistcAnswerData'] && $arrQ['questionAnswerData']){
                        $strQuestionName = $arrQ['questionName'];//Save question name as a key
                        $question= $questionMapper->fetch($arrQ['questionId']);
                        if ($question->getAnswerType() == 'free_answer'){
                            $strAnswerGivenByUser = $arrQ['statistcAnswerData'][0];
                        }
                        else {
                            $iKeyOfAnswer = array_keys($arrQ['statistcAnswerData'],'1'); //Save answer key array
                            $strAnswerGivenByUser = "";
                            $iLastElement = end($iKeyOfAnswer); //Finds last element of array (Use for insert ',')
                            $iNumberOfAnswerAttempted = count($iKeyOfAnswer); //Number of options selected by user

                            foreach($iKeyOfAnswer as $iKey){
                                $objAnswer = $arrQ['questionAnswerData'][$iKey]; //Finds answer object

                                $strAnswerGivenByUser.=$objAnswer->getAnswer(); //Fetch answer name given by user
                                if($iNumberOfAnswerAttempted>1 && $iKey!=$iLastElement){
                                    $strAnswerGivenByUser.=": ";
                                }
                            }
                        }

                        $strAnswerGivenByUser = trim(preg_replace('/(<)([img])(\w+)([^>]*>)/',"", $strAnswerGivenByUser));
                        $arrSM[$strQuestionName] = $strAnswerGivenByUser; //Save answer name as a value
                    }
                }
                
            }

            if(isset($arrSM['form_data']) && !empty($arrSM['form_data'])){
                $arrFormData = $arrSM['form_data'];

                foreach ($arrFormData as $key => $value) {
                    if(!$parentController->checkImageInString($value)){
                        $arrSM[$key] = $value;
                    }
                }
            }

            if($statisticRefMapper->checkPhotoOrGiveAwayContestQuizStats($quizId) || $questionMapper->checkThisThatQuiz($quizId)) {
                unset($arrSM['correct_count']);
                unset($arrSM['incorrect_count']);
                unset($arrSM['points']);
                unset($arrSM['solved_count']);
                unset($arrSM['results']);
            }

            unset($arrSM['create_time']);
            unset($arrSM['question_data']);
            unset($arrSM['form_data']);
            unset($arrSM['user_id']);
            unset($arrSM['statistic_ref_id']);
            unset($arrSM['quiz_id']);
            unset($arrSM['is_old']);
            unset($arrSM['g_points']);
            unset($arrSM['user_login']);
            unset($arrSM['user_name']);
            
            $arrCSVData[] = $arrSM;
        }

        return json_encode($arrCSVData);
    }
}
