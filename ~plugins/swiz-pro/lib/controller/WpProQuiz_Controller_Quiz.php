<?php

class WpProQuiz_Controller_Quiz extends WpProQuiz_Controller_Controller
{
    public function route()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'show';

        switch ($action) {
            case 'show':
                $this->showAction();
                break;
            case 'addEdit':
                $this->addEditQuiz();
                break;
            case 'delete':
                if (isset($_GET['id'])) {
                    $this->deleteAction($_GET['id']);
                }
                break;
            case 'deleteMulti':
                $this->deleteMultiAction();
                break;
            default:
                $this->showAction();
                break;
        }
    }

    public function routeAction()
    {
        $action = isset($_GET['action']) ? $_GET['action'] : 'show';

        switch ($action) {
            default:
                $this->showActionHook();
                break;
        }
    }

    private function showActionHook()
    {
        if (!empty($_REQUEST['_wp_http_referer'])) {
            wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI'])));
            exit;
        }

        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }

        $quizId = isset($_GET['quizId']) ? (int)$_GET['quizId'] : 0;

        if($quizId){
            add_filter('manage_' . get_current_screen()->id . '_columns',
                array('WpProQuiz_View_QuestionOverallTable', 'getColumnDefs'));
        }else{
            add_filter('manage_' . get_current_screen()->id . '_columns',
                array('WpProQuiz_View_QuizOverallTable', 'getColumnDefs'));
        }

        add_screen_option('per_page', array(
            'label' => __('Quiz', 'wp-pro-quiz'),
            'default' => 20,
            'option' => 'wp_pro_quiz_quiz_overview_per_page'
        ));
    }

    private function addEditQuiz()
    {
        $quizId = isset($_GET['quizId']) ? (int)$_GET['quizId'] : 0;

        if ($quizId) {
            if (!current_user_can('wpProQuiz_edit_quiz')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
        } else {
            if (!current_user_can('wpProQuiz_add_quiz')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
        }

        $prerequisiteMapper = new WpProQuiz_Model_PrerequisiteMapper();
        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $formMapper = new WpProQuiz_Model_FormMapper();
        $templateMapper = new WpProQuiz_Model_TemplateMapper();
        $cateoryMapper = new WpProQuiz_Model_CategoryMapper();

        if ($quizId && $quizMapper->exists($quizId) == 0) {
            WpProQuiz_View_View::admin_notices(__('Quiz not found', 'wp-pro-quiz'), 'error');
            return;
        }

        $arrEmail = array('html' => true); //Set default html format true
        $email['adminEmail'] = $email['userEmail'] = new WpProQuiz_Model_Email($arrEmail);
        $quiz = new WpProQuiz_Model_Quiz($email);

        //Set default values 
        $quiz->setFacebookShare(true);
        $quiz->setTwitterShare(true);
        $quiz->setLinkedinShare(true);
        $quiz->setEmailShare(true);
        $quiz->setHideResultQuizTime(true);
        $quiz->setStatisticsOn(true);
        $quiz->setIsPrivacyPolicy(true);
        $quiz->setIsTermsConditions(true);
        $quiz->setIsContestRules(false);
        $quiz->setCustomFormHeader(DEFAULT_CUSTOM_FORM_HEADER);
        $quiz->setPrivacyPolicy(get_option('wpProQuiz_privacy_policy'));
        $quiz->setPrivacyPolicyText(DEFAULT_PRIVACY_POLICY_TEXT);
        $quiz->setTermsConditions(get_option('wpProQuiz_terms_conditions'));
        $quiz->setTermsConditionsText(DEFAULT_TERMS_CONDITIONS_TEXT);
        $quiz->setContestRules(get_option('wpProQuiz_content_rules'));
        $quiz->setContestRulesText(DEFAULT_CONTEST_RULES_TEXT);
        $quiz->setPcLeftHeaderTitle(DEFAULT_PC_LEFT_HEADER_TITLE);
        $quiz->setPcCenterHeaderTitle(DEFAULT_PC_CENTER_HEADER_TITLE);
        $quiz->setPcRightHeaderTitle(DEFAULT_PC_RIGHT_HEADER_TITLE);

        $forms = null;
        $prerequisiteQuizList = array();

        if (isset($this->_post['template']) || (isset($this->_post['templateLoad']) && isset($this->_post['templateLoadId']))) {
            if (isset($this->_post['template'])) {
                $template = $this->saveTemplate();
            } else {
                $template = $templateMapper->fetchById($this->_post['templateLoadId']);
            }

            $data = $template->getData();

            if ($data !== null) {
                /** @var WpProQuiz_Model_Quiz $quiz */
                $quiz = $data['quiz'];
                $quiz->setId($quizId);

                $forms = $data['forms'];
                $prerequisiteQuizList = $data['prerequisiteQuizList'];
            }
        } else {
            if (isset($this->_post['submit'])) {
                if (isset($this->_post['resultGradeEnabled'])) {
                    $this->_post['result_text'] = $this->filterResultTextGrade($this->_post);
                }

                $this->_post['categoryId'] = $this->_post['category'] > 0 ? $this->_post['category'] : 0;

                $this->_post['adminEmail'] = new WpProQuiz_Model_Email($this->_post['adminEmail']);
                $this->_post['userEmail'] = new WpProQuiz_Model_Email($this->_post['userEmail']);

                $arrCategory = $cateoryMapper->fetchByCatId($this->_post['categoryId']);

                $this->_post['showCategoryScore'] = false;
                $this->_post['showCategory'] = false;

                if(isset($arrCategory['category_value']) && ($arrCategory['category_value'] == PHOTO_CONTEST_VALUE || $arrCategory['category_value'] == GIVEAWAY_CONTEST_VALUE || $arrCategory['category_value'] == THIS_OR_THAT_QUIZ_VALUE)){
                    $this->_post['btnViewQuestionHidden'] = true;
                    $this->_post['questionRandom'] = false;
                    $this->_post['answerRandom'] = false;
                    $this->_post['showAverageResult'] = false;
                    $this->_post['hideResultCorrectQuestion'] = true;
                    $this->_post['hideResultPoints'] = true;
                    $this->_post['btnRestartQuizHidden'] = true;
                    $this->_post['quizModus'] = 0; // Paginate between questions sequentially
                    
                    if($arrCategory['category_value'] == PHOTO_CONTEST_VALUE || $arrCategory['category_value'] == GIVEAWAY_CONTEST_VALUE){
                        $this->_post['titleHidden'] = true;
                        $this->_post['timeLimit'] = 0;
                        $this->_post['hideResultQuizTime'] = true;
                        $this->_post['autostart'] = false;
                        $this->_post['formShowPosition'] = 1;
                    }
                    if($arrCategory['category_value'] == THIS_OR_THAT_QUIZ_VALUE){
                        $this->_post['showPoints'] = false;
                        $this->_post['hideAnswerMessageBox'] = true;
                        $this->_post['disabledAnswerMark'] = true;
                        $this->_post['forcingQuestionSolve'] = true;
                    }
                }
                
                $quiz = new WpProQuiz_Model_Quiz($this->_post);
                $quiz->setId($quizId);

                if (isset($this->_post['plugin'])) {
                    $quiz->getPluginContainer()->set($this->_post['plugin']);
                }

                if ($this->checkValidit($this->_post)) {
                    if ($quizId) {
                        WpProQuiz_View_View::admin_notices(__('Quiz edited', 'wp-pro-quiz'), 'info');
                    } else {
                        WpProQuiz_View_View::admin_notices(__('Quiz created', 'wp-pro-quiz'), 'info');
                    }

                    $quizMapper->save($quiz);

                    $quizId = $quiz->getId();

                    $_embedCode = '<script id="SwizproIframe" data-name-quizid="' .$quiz->getId() .'" data-name-url="' . DEFAULT_OG_TAG_URL . '" src="' . DEFAULT_OG_TAG_URL . '/wp-content/plugins/swiz-pro/js/swizPro_iframe.js"></script>';
                    $quiz->setEmbedCode($_embedCode);
                    $quizMapper->updateEmbedCode($quiz);


                    $prerequisiteMapper->delete($quizId);

                    if ($quiz->isPrerequisite() && !empty($this->_post['prerequisiteList'])) {
                        $prerequisiteMapper->save($quizId, $this->_post['prerequisiteList']);
                        $quizMapper->activateStatitic($this->_post['prerequisiteList'], 1440);
                    }

                    if (!$this->formHandler($quiz->getId(), $this->_post)) {
                        $quiz->setFormActivated(false);
                        $quizMapper->save($quiz);
                    }

                    $forms = $formMapper->fetch($quizId);
                    $prerequisiteQuizList = $prerequisiteMapper->fetchQuizIds($quizId);

                    if(isset($this->_post['stylesheet_name']) && !empty($this->_post['stylesheet_name'])){
                        $strStyleName = $quiz->getQuizStyle();
                        $strCustomCssBox = $this->_post['custom_quiz_box'];
                        
                        // Create new stylesheet at custom style path
                        $fp = fopen(WPPROQUIZ_CUSTOM_STYLE_PATH .'/'. $strStyleName,"wb");
                        if($fp){
                            fwrite($fp,$strCustomCssBox);
                            fclose($fp);
                        }else{
                            WpProQuiz_View_View::admin_notices(__('Failed to create file on server, please check permissions.', 'wp-pro-quiz'), 'error');
                        }
                    }
                    $quiz = $quizMapper->fetch($quizId);

                } else {
                    WpProQuiz_View_View::admin_notices(__('Quiz title or quiz description are not filled',
                        'wp-pro-quiz'));
                }
            } else {
                if ($quizId) {
                    $quiz = $quizMapper->fetch($quizId);
                    $forms = $formMapper->fetch($quizId);
                    $prerequisiteQuizList = $prerequisiteMapper->fetchQuizIds($quizId);
                }
            }
        }

        $arrCustomFiles = array_diff(scandir(WPPROQUIZ_CUSTOM_STYLE_PATH), array('..', '.'));

        if (($key = array_search(CUSTOM_CSS_FILENAME, $arrCustomFiles)) !== false) {
            unset($arrCustomFiles[$key]);
        }

        $strQuizStyleName = $quiz->getQuizStyle();
        $strPath = WPPROQUIZ_CUSTOM_STYLE_PATH.'/'.$quiz->getQuizStyle();
        if(empty($strQuizStyleName) || !file_exists($strPath)){
            //if quiz style doesn't found or file does not exists on server then default css will be applied
            $strPath = WPPROQUIZ_CUSTOM_STYLE_URL.'/'.SMALL_CHECKBOX_CSS;
            $strDefaultFileContent = file_get_contents($strPath);

            $quiz->setQuizStyle(SMALL_CHECKBOX_CSS);
            $quiz->setCustomQuizBox($strDefaultFileContent);
        }

        $view = new WpProQuiz_View_QuizEdit();
        $view->arrCustomFiles = $arrCustomFiles;
        $view->quiz = $quiz;
        $view->forms = $forms;
        $view->prerequisiteQuizList = $prerequisiteQuizList;
        $view->templates = $templateMapper->fetchAll(WpProQuiz_Model_Template::TEMPLATE_TYPE_QUIZ, false);
        $view->quizList = $quizMapper->fetchAllAsArray(array('id', 'name'), $quizId ? array($quizId) : array());
        $view->captchaIsInstalled = class_exists('ReallySimpleCaptcha');
        $view->categories = $cateoryMapper->fetchAll(WpProQuiz_Model_Category::CATEGORY_TYPE_QUIZ);

        $view->header = $quizId ? __('Edit quiz', 'wp-pro-quiz') : __('Create Quiz', 'wp-pro-quiz');

        /*Questions overview in question tab*/
        $mm = new WpProQuiz_Model_QuestionMapper();

        $per_page = (int)get_user_option('wp_pro_quiz_question_overview_per_page');
        if (empty($per_page) || $per_page < 1) {
            $per_page = 20;
        }

        $current_page = $this->getCurrentPage();
        $search = isset($_GET['s']) ? trim($_GET['s']) : '';
        $orderBy = isset($_GET['orderby']) ? trim($_GET['orderby']) : '';
        $order = isset($_GET['order']) ? trim($_GET['order']) : '';
        $offset = ($current_page - 1) * $per_page;
        $limit = $per_page;
        $filter = array();

        if (isset($_GET['cat'])) {
            $filter['cat'] = $_GET['cat'];
        }

        $result = $mm->fetchTable($quizId, $orderBy, $order, $search, $limit, $offset, $filter);

        $view->questionItems = $result['questions'];
        $view->questionCount = $result['count'];
        $view->categoryItems = $cateoryMapper->fetchAll(WpProQuiz_Model_Category::CATEGORY_TYPE_QUESTION);
        $view->perPage = $per_page;
        
        $view->show();
    }

    public function isLockQuiz()
    {
        $quizId = (int)$this->_post['quizId'];
        $userId = get_current_user_id();
        $data = array();

        $lockMapper = new WpProQuiz_Model_LockMapper();
        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $prerequisiteMapper = new WpProQuiz_Model_PrerequisiteMapper();

        $quiz = $quizMapper->fetch($this->_post['quizId']);

        if ($quiz === null || $quiz->getId() <= 0) {
            return null;
        }

        if ($this->isPreLockQuiz($quiz)) {
            $lockIp = $lockMapper->isLock($this->_post['quizId'], $this->getIp(), $userId,
                WpProQuiz_Model_Lock::TYPE_QUIZ);
            $lockCookie = false;
            $cookieTime = $quiz->getQuizRunOnceTime();

            if (isset($this->_cookie['wpProQuiz_lock']) && $userId == 0 && $quiz->isQuizRunOnceCookie()) {
                $cookieJson = json_decode($this->_cookie['wpProQuiz_lock'], true);

                if ($cookieJson !== false) {
                    if (isset($cookieJson[$this->_post['quizId']]) && $cookieJson[$this->_post['quizId']] == $cookieTime) {
                        $lockCookie = true;
                    }
                }
            }

            $data['lock'] = array(
                'is' => ($lockIp || $lockCookie),
                'pre' => true
            );
        }

        if ($quiz->isPrerequisite()) {
            $quizIds = array();

            if ($userId > 0) {
                $quizIds = $prerequisiteMapper->getNoPrerequisite($quizId, $userId);
            } else {
                $checkIds = $prerequisiteMapper->fetchQuizIds($quizId);

                if (isset($this->_cookie['wpProQuiz_result'])) {
                    $r = json_decode($this->_cookie['wpProQuiz_result'], true);

                    if ($r !== null && is_array($r)) {
                        foreach ($checkIds as $id) {
                            if (!isset($r[$id]) || !$r[$id]) {
                                $quizIds[] = $id;
                            }
                        }
                    }
                } else {
                    $quizIds = $checkIds;
                }
            }

            if (!empty($quizIds)) {
                $names = $quizMapper->fetchCol($quizIds, 'name');

                if (!empty($names)) {
                    $data['prerequisite'] = implode(', ', $names);
                }
            }

        }

        if ($quiz->isStartOnlyRegisteredUser()) {
            $data['startUserLock'] = (int)!is_user_logged_in();
        }

        return $data;
    }

    private function getCurrentPage()
    {
        $pagenum = isset($_REQUEST['paged']) ? absint($_REQUEST['paged']) : 0;

        return max(1, $pagenum);
    }

    private function showAction()
    {
        if (!current_user_can('wpProQuiz_show')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $view = new WpProQuiz_View_QuizOverall();

        $m = new WpProQuiz_Model_QuizMapper();
        $categoryMapper = new WpProQuiz_Model_CategoryMapper();

        $per_page = (int)get_user_option('wp_pro_quiz_quiz_overview_per_page');
        if (empty($per_page) || $per_page < 1) {
            $per_page = 20;
        }

        $current_page = $this->getCurrentPage();
        $search = isset($_GET['s']) ? trim($_GET['s']) : '';
        $orderBy = isset($_GET['orderby']) ? trim($_GET['orderby']) : '';
        $order = isset($_GET['order']) ? trim($_GET['order']) : '';
        $offset = ($current_page - 1) * $per_page;
        $limit = $per_page;
        $filter = array();

        if (isset($_GET['cat'])) {
            $filter['cat'] = $_GET['cat'];
        }

        $result = $m->fetchTable($orderBy, $order, $search, $limit, $offset, $filter);

        $view->quizItems = $result['quiz'];
        $view->quizCount = $result['count'];
        $view->categoryItems = $categoryMapper->fetchAll(WpProQuiz_Model_Category::CATEGORY_TYPE_QUIZ);;
        $view->perPage = $per_page;

        $view->show();
    }

    private function saveTemplate()
    {
        $templateMapper = new WpProQuiz_Model_TemplateMapper();

        if (isset($this->_post['resultGradeEnabled'])) {
            $this->_post['result_text'] = $this->filterResultTextGrade($this->_post);
        }

        $this->_post['categoryId'] = $this->_post['category'] > 0 ? $this->_post['category'] : 0;

        $this->_post['adminEmail'] = new WpProQuiz_Model_Email($this->_post['adminEmail']);
        $this->_post['userEmail'] = new WpProQuiz_Model_Email($this->_post['userEmail']);

        $quiz = new WpProQuiz_Model_Quiz($this->_post);

        if ($quiz->isPrerequisite() && !empty($this->_post['prerequisiteList']) && !$quiz->isStatisticsOn()) {
            $quiz->setStatisticsOn(true);
            $quiz->setStatisticsIpLock(1440);
        }

        $form = $this->_post['form'];

        unset($form[0]);

        $forms = array();

        foreach ($form as $f) {
            $f['fieldname'] = trim($f['fieldname']);

            if (empty($f['fieldname'])) {
                continue;
            }

            if ((int)$f['form_id'] && (int)$f['form_delete']) {
                continue;
            }

            if ($f['type'] == WpProQuiz_Model_Form::FORM_TYPE_SELECT || $f['type'] == WpProQuiz_Model_Form::FORM_TYPE_RADIO) {
                if (!empty($f['data'])) {
                    $items = explode("\n", $f['data']);
                    $f['data'] = array();

                    foreach ($items as $item) {
                        $item = trim($item);

                        if (!empty($item)) {
                            $f['data'][] = $item;
                        }
                    }
                }
            }

            if (empty($f['data']) || !is_array($f['data'])) {
                $f['data'] = null;
            }

            $forms[] = new WpProQuiz_Model_Form($f);
        }

        WpProQuiz_View_View::admin_notices(__('Template stored', 'wp-pro-quiz'), 'info');

        $data = array(
            'quiz' => $quiz,
            'forms' => $forms,
            'prerequisiteQuizList' => isset($this->_post['prerequisiteList']) ? $this->_post['prerequisiteList'] : array()
        );

        $template = new WpProQuiz_Model_Template();

        if ($this->_post['templateSaveList'] == '0') {
            $template->setName(trim($this->_post['templateName']));
        } else {
            $template = $templateMapper->fetchById($this->_post['templateSaveList'], false);
        }

        $template->setType(WpProQuiz_Model_Template::TEMPLATE_TYPE_QUIZ);
        $template->setData($data);

        $templateMapper->save($template);

        return $template;
    }

    private function formHandler($quizId, $post)
    {
        if (!isset($post['form'])) {
            return false;
        }

        $form = $post['form'];

        $fileTypeKey = array_search(WpProQuiz_Model_Form::FORM_TYPE_FILE, $this->custom_array_column($form, 'type')); //Fetch the key of input type file
        $approveTypeKey = array_search(WpProQuiz_Model_Form::FORM_TYPE_APPROVE, $this->custom_array_column($form, 'type')); //Fetch the key of input type approve


        unset($form[0]);

        if (empty($form)) {
            return false;
        }

        $formMapper = new WpProQuiz_Model_FormMapper();

        $deleteIds = array();
        $forms = array();
        $sort = 0;

        if(!empty($fileTypeKey) && empty($approveTypeKey)){
            // if file type is in dropdown but approve type is not in dropdown
            $formId = $formMapper->ApproveFieldId($quizId);

            $arrApproveFormData['fieldname'] = "Approve";
            $arrApproveFormData['type'] = WpProQuiz_Model_Form::FORM_TYPE_APPROVE; 
            $arrApproveFormData['data'] = '';
            $arrApproveFormData['required'] = '0';
            $arrApproveFormData['show_in_statistic'] = '0';
            $arrApproveFormData['form_id'] = $formId;
            $arrApproveFormData['form_delete'] = '0';

            $form[]=$arrApproveFormData;
        }

        foreach ($form as $f) {
            $f['fieldname'] = trim($f['fieldname']);

            if (empty($f['fieldname'])) {
                continue;
            }

            if ((int)$f['form_id'] && (int)$f['form_delete']) {
                $deleteIds[] = (int)$f['form_id'];
                continue;
            }

            $f['sort'] = $sort++;
            $f['quizId'] = $quizId;

            if ($f['type'] == WpProQuiz_Model_Form::FORM_TYPE_SELECT || $f['type'] == WpProQuiz_Model_Form::FORM_TYPE_RADIO) {
                if (!empty($f['data'])) {
                    $items = explode("\n", $f['data']);
                    $f['data'] = array();

                    foreach ($items as $item) {
                        $item = trim($item);

                        if (!empty($item)) {
                            $f['data'][] = $item;
                        }
                    }
                }
            }

            if (empty($f['data']) || !is_array($f['data'])) {
                $f['data'] = null;
            }

            $forms[] = new WpProQuiz_Model_Form($f);
        }

        if (!empty($deleteIds)) {
            $formMapper->deleteForm($deleteIds, $quizId);
        }
		print_r($forms);
       
        $formMapper->update($forms);
        
        

        return !empty($forms);
    }

    private function deleteAction($id)
    {
        if (!current_user_can('wpProQuiz_delete_quiz')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $m = new WpProQuiz_Model_QuizMapper();

        $m->deleteAll($id);

        WpProQuiz_View_View::admin_notices(__('Quiz deleted', 'wp-pro-quiz'), 'info');

        $this->showAction();
    }

    private function deleteMultiAction()
    {
        if (!current_user_can('wpProQuiz_delete_quiz')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $m = new WpProQuiz_Model_QuizMapper();

        if (!empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $id) {
                $m->deleteAll($id);
            }
        }

        WpProQuiz_View_View::admin_notices(__('Quiz deleted', 'wp-pro-quiz'), 'info');

        $this->showAction();
    }

    private function checkValidit($post)
    {
        if(isset($post['autostart']) && $post['autostart']=='1'){
            //If autostart is checked then don't validate quiz description
            return (isset($post['name']) && !empty($post['name']));
        }else{
            return (isset($post['name']) && !empty($post['name']) && isset($post['text']) && !empty($post['text']));
        }
    }

    private function filterResultTextGrade($post)
    {
        $activ = array_keys($post['resultTextGrade']['activ'], '1');
        $result = array();

        foreach ($activ as $k) {
            $result['text'][] = $post['resultTextGrade']['text'][$k];
            $result['prozent'][] = (float)str_replace(',', '.', $post['resultTextGrade']['prozent'][$k]);
        }

        return $result;
    }

    private function setResultCookie(WpProQuiz_Model_Quiz $quiz)
    {
        $prerequisite = new WpProQuiz_Model_PrerequisiteMapper();

        if (get_current_user_id() == 0 && $prerequisite->isQuizId($quiz->getId())) {
            $cookieData = array();

            if (isset($this->_cookie['wpProQuiz_result'])) {
                $d = json_decode($this->_cookie['wpProQuiz_result'], true);

                if ($d !== null && is_array($d)) {
                    $cookieData = $d;
                }
            }

            $cookieData[$quiz->getId()] = 1;

            $url = parse_url(get_bloginfo('url'));

            setcookie('wpProQuiz_result', json_encode($cookieData), time() + 60 * 60 * 24 * 300,
                empty($url['path']) ? '/' : $url['path']);
        }
    }

    public function isPreLockQuiz(WpProQuiz_Model_Quiz $quiz)
    {
        $userId = get_current_user_id();

        if ($quiz->isQuizRunOnce()) {
            switch ($quiz->getQuizRunOnceType()) {
                case WpProQuiz_Model_Quiz::QUIZ_RUN_ONCE_TYPE_ALL:
                    return true;
                case WpProQuiz_Model_Quiz::QUIZ_RUN_ONCE_TYPE_ONLY_USER:
                    return $userId > 0;
                case WpProQuiz_Model_Quiz::QUIZ_RUN_ONCE_TYPE_ONLY_ANONYM:
                    return $userId == 0;
            }
        }

        return false;
    }

    private function getIp()
    {
        if (get_current_user_id() > 0) {
            return '0';
        } else {
            return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
        }
    }

    /**
     * @param WpProQuiz_Model_Quiz $quiz
     * @param $result
     * @param WpProQuiz_Model_Category[] $categories
     * @param WpProQuiz_Model_Form[] $forms
     * @param $inputForms
     */
    private function emailNote(WpProQuiz_Model_Quiz $quiz, $result, $categories, $forms, $inputForms)
    {
        $user = wp_get_current_user();

        $r = array(
            '$userId' => $user->ID,
            '$username' => $user->display_name,
            '$quizname' => $quiz->getName(),
            '$result' => $result['result'] . '%',
            '$points' => $result['points'],
            '$ip' => filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP),
            '$categories' => empty($result['cats']) ? '' : $this->setCategoryOverview($result['cats'], $categories)
        );

        if ($quiz->isFormActivated() && $forms !== null) {
            foreach ($forms as $form) {
                $value = '';

                if ($form->getType() == WpProQuiz_Model_Form::FORM_TYPE_DATE) {
                    if (isset($inputForms[$form->getFormId()])) {
                        $value = $inputForms[$form->getFormId()]['day'] . '-' . $inputForms[$form->getFormId()]['month']
                            . '-' . $inputForms[$form->getFormId()]['year'];
                    }
                } else {
                    $value = isset($inputForms[$form->getFormId()]) ? $inputForms[$form->getFormId()] : '';
                }

                $r['$form{' . $form->getSort() . '}'] = esc_html($value);
            }
        }

        if ($user->ID == 0) {
            $r['$username'] = $r['$ip'];
        }

        if ($quiz->isUserEmailNotification()) {
            $userEmail = $quiz->getUserEmail();

            $userAdress = null;

            if ($userEmail->isToUser() && get_current_user_id() > 0) {
                $userAdress = $user->user_email;
            } else {
                if ($userEmail->isToForm() && $quiz->isFormActivated()) {
                    foreach ($forms as $form) {
                        if ($form->getSort() == $userEmail->getTo()) {
                            if (isset($inputForms[$form->getFormId()])) {
                                $userAdress = $inputForms[$form->getFormId()];
                            }

                            break;
                        }
                    }
                }
            }

            if (!empty($userAdress) && filter_var($userAdress, FILTER_VALIDATE_EMAIL) !== false) {
                $msg = str_replace(array_keys($r), $r, $userEmail->getMessage());

                $headers = '';
                $email = $userEmail->getFrom();

                if (!empty($email)) {
                    $headers = 'From: ' . $userEmail->getFrom();
                }

                if ($userEmail->isHtml()) {
                    add_filter('wp_mail_content_type', array($this, 'htmlEmailContent'));
                }

                wp_mail($userAdress, $userEmail->getSubject(), $msg, $headers);

                if ($userEmail->isHtml()) {
                    remove_filter('wp_mail_content_type', array($this, 'htmlEmailContent'));
                }
            }
        }

        if ($quiz->getEmailNotification() == WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL
            || (get_current_user_id() > 0 && $quiz->getEmailNotification() == WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER)
        ) {

            $adminEmail = $quiz->getAdminEmail();

            $msg = str_replace(array_keys($r), $r, $adminEmail->getMessage());

            $headers = '';
            $email = $adminEmail->getFrom();

            if (!empty($email)) {
                $headers = 'From: ' . $adminEmail->getFrom();
            }

            if ($adminEmail->isHtml()) {
                add_filter('wp_mail_content_type', array($this, 'htmlEmailContent'));
            }

            wp_mail($adminEmail->getTo(), $adminEmail->getSubject(), $msg, $headers);

            if ($adminEmail->isHtml()) {
                remove_filter('wp_mail_content_type', array($this, 'htmlEmailContent'));
            }
        }
    }

    public function htmlEmailContent()
    {
        return 'text/html';
    }

    private function setCategoryOverview($catArray, $categories)
    {
        $cats = array();

        foreach ($categories as $cat) {
            /* @var $cat WpProQuiz_Model_Category */

            if (!$cat->getCategoryId()) {
                $cat->setCategoryName(__('Not categorized', 'wp-pro-quiz'));
            }

            $cats[$cat->getCategoryId()] = $cat->getCategoryName();
        }

        $a = __('Categories', 'wp-pro-quiz') . ":\n";

        foreach ($catArray as $id => $value) {
            if (!isset($cats[$id])) {
                continue;
            }

            $a .= '* ' . str_pad($cats[$id], 35, '.') . ((float)$value) . "%\n";
        }

        return $a;
    }

    public static function ajaxSetQuizMultipleCategories($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            return json_encode(array());
        }

        $quizMapper = new WpProQuiz_Model_QuizMapper();

        $quizMapper->setMultipeCategories($data['quizIds'], $data['categoryId']);

        return json_encode(array());
    }

    public static function ajaxLoadQuizData($data)
    {
        $quizId = (int)$data['quizId'];

        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $toplistController = new WpProQuiz_Controller_Toplist();
        $statisticController = new WpProQuiz_Controller_Statistics();

        $quiz = $quizMapper->fetch($quizId);
        $data = array();

        if ($quiz === null || $quiz->getId() <= 0) {
            return json_encode(array());
        }

        $data['toplist'] = $toplistController->getAddToplist($quiz);
        $data['averageResult'] = $statisticController->getAverageResult($quizId);

        return json_encode($data);
    }

    public static function ajaxQuizCheckLock()
    {
        // workaround ...
        $_POST = $_POST['data'];

        $quizController = new WpProQuiz_Controller_Quiz();

        return json_encode($quizController->isLockQuiz());
    }

    public static function ajaxResetLock($data)
    {
        if (!current_user_can('wpProQuiz_edit_quiz')) {
            return json_encode(array());
        }

        $quizId = (int)$data['quizId'];

        $lm = new WpProQuiz_Model_LockMapper();
        $qm = new WpProQuiz_Model_QuizMapper();

        $q = $qm->fetch($quizId);

        if ($q->getId() > 0) {
            $q->setQuizRunOnceTime(time());

            $qm->save($q);

            $lm->deleteByQuizId($quizId, WpProQuiz_Model_Lock::TYPE_QUIZ);
        }

        return json_encode(array());
    }

    public static function ajaxCompletedQuiz($data)
    {
        // workaround ...
        $_POST = $_POST['data'];
        error_log( 'Made it into the Ajax function safe and sound!' );
        $ctr = new WpProQuiz_Controller_Quiz();

        $lockMapper = new WpProQuiz_Model_LockMapper();
        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $categoryMapper = new WpProQuiz_Model_CategoryMapper();
        $formMapper = new WpProQuiz_Model_FormMapper();

        $is100P = $data['results']['comp']['result'] == 100;

        $quiz = $quizMapper->fetch($data['quizId']);

        if ($quiz === null || $quiz->getId() <= 0) {
            return json_encode(array());
        }

        $categories = $categoryMapper->fetchByQuiz($quiz->getId());
        $forms = $formMapper->fetch($quiz->getId());

        $ctr->setResultCookie($quiz);

        $ctr->emailNote($quiz, $data['results']['comp'], $categories, $forms,
            isset($data['forms']) ? $data['forms'] : array());

        if (!$ctr->isPreLockQuiz($quiz)) {
            $statistics = new WpProQuiz_Controller_Statistics();
            $statistics->save($quiz);

            do_action('wp_pro_quiz_completed_quiz');

            if ($is100P) {
                do_action('wp_pro_quiz_completed_quiz_100_percent');
            }

            return json_encode(array());
        }

        $lockMapper->deleteOldLock(60 * 60 * 24 * 7, $data['quizId'], time(), WpProQuiz_Model_Lock::TYPE_QUIZ,
            0);

        $lockIp = $lockMapper->isLock($data['quizId'], $ctr->getIp(), get_current_user_id(),
            WpProQuiz_Model_Lock::TYPE_QUIZ);
        $lockCookie = false;
        $cookieTime = $quiz->getQuizRunOnceTime();
        $cookieJson = null;

        if (isset($ctr->_cookie['wpProQuiz_lock']) && get_current_user_id() == 0 && $quiz->isQuizRunOnceCookie()) {
            $cookieJson = json_decode($ctr->_cookie['wpProQuiz_lock'], true);

            if ($cookieJson !== false) {
                if (isset($cookieJson[$data['quizId']]) && $cookieJson[$data['quizId']] == $cookieTime) {
                    $lockCookie = true;
                }
            }
        }

        if (!$lockIp && !$lockCookie) {
            $statistics = new WpProQuiz_Controller_Statistics();
            $statistics->save($quiz);

            do_action('wp_pro_quiz_completed_quiz');

            if ($is100P) {
                do_action('wp_pro_quiz_completed_quiz_100_percent');
            }

            if (get_current_user_id() == 0 && $quiz->isQuizRunOnceCookie()) {
                $cookieData = array();

                if ($cookieJson !== null || $cookieJson !== false) {
                    $cookieData = $cookieJson;
                }

                $cookieData[$data['quizId']] = $quiz->getQuizRunOnceTime();
                $url = parse_url(get_bloginfo('url'));

                setcookie('wpProQuiz_lock', json_encode($cookieData), time() + 60 * 60 * 24 * 60,
                    empty($url['path']) ? '/' : $url['path']);
            }

            $lock = new WpProQuiz_Model_Lock();

            $lock->setUserId(get_current_user_id());
            $lock->setQuizId($data['quizId']);
            $lock->setLockDate(time());
            $lock->setLockIp($ctr->getIp());
            $lock->setLockType(WpProQuiz_Model_Lock::TYPE_QUIZ);

            $lockMapper->insert($lock);
        }

        return json_encode(array());
    }

    public static function getQuizStyleSheet($arrData)
    {
        $iQuizId=$arrData['quiz_id'];
        $strQuizCss=""; $strQuizStyleName="";
        if($iQuizId){
            $objQuizMapper = new WpProQuiz_Model_QuizMapper();
            $objQuiz = $objQuizMapper->fetch($iQuizId);
            $strQuizCss=$objQuiz->getCustomQuizBox();
            $strQuizStyleName=$objQuiz->getQuizStyle();
        }

        return json_encode(array(
            'strQuizCss'        => $strQuizCss,
            'strQuizStyleName'  => $strQuizStyleName,
        ));
    }

    public static function checkUniqueStyleSheetName($arrData)
    {
        $strQuizStyleName = $arrData['strQuizStyleName'];
        $arrCustomFiles = array_diff(scandir(WPPROQUIZ_CUSTOM_STYLE_PATH), array('..', '.'));
        $bFileExistsStatus = false;
        
        foreach($arrCustomFiles as $strFile)
        {
            $strPlainStyleName = str_replace('.css', '', $strFile); //remove .css and check only name
            if($strFile == $strQuizStyleName || $strPlainStyleName == $strQuizStyleName){
                //Given file name is already exists on custom style path
                $bFileExistsStatus = true;
                break;
            }
        }

        return json_encode(array(
            'bFileExistsStatus' => $bFileExistsStatus
        ));
    }

    public static function deleteStyleSheet($arrData)
    {
        $delete_file_status=false;
        if($arrData['current_stylesheet']){
            $current_stylesheet_path=WPPROQUIZ_CUSTOM_STYLE_PATH.'/'.$arrData['current_stylesheet'];
            if (unlink($current_stylesheet_path)){
                $delete_file_status=true;
            }
        }

        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $quizData = $quizMapper->fetch($arrData['quiz_id']);

        $strQuizStyleName = $quizData->getQuizStyle();
        $strQuizCustomQuizBox = $quizData->getCustomQuizBox();

        $strPath = WPPROQUIZ_CUSTOM_STYLE_PATH.'/'.$quizData->getQuizStyle();
        if(empty($strQuizStyleName) || !file_exists($strPath)){
            //if applied file is deleted then fetch default file code and update to db
            $strPath = WPPROQUIZ_CUSTOM_STYLE_URL.'/'.SMALL_CHECKBOX_CSS;
            $strDefaultFileContent = file_get_contents($strPath);

            $strQuizStyleName = SMALL_CHECKBOX_CSS;
            $strQuizCustomQuizBox = $strDefaultFileContent;

            //Update code in db
            $arrUpdateData=array(
                'quizStyle' => $strQuizStyleName,
                'custom_quiz_box' => $strQuizCustomQuizBox,
            );
            $quiz = new WpProQuiz_Model_Quiz($arrUpdateData);
            $quiz->setId($arrData['quiz_id']);
            $quizMapper->updateCustomStyle($quiz);
        }

        return json_encode(array(
            'delete_file_status'  => $delete_file_status,
            'strQuizStyleName'  => $strQuizStyleName,
            'strQuizCustomQuizBox'  => $strQuizCustomQuizBox,
        ));
    }

    /**
     * Custom array column function to use array_colum function if php version <5.5
     *
     * @param array $arrData
     * @param $column_name
     * @return void
     */
    private function custom_array_column($arrData,$column_name)
    {
        if(!function_exists("array_column"))
        {
            return array_map(function($element) use($column_name){return $element[$column_name];}, $arrData);
        }
        return array_column($arrData, 'type');
    }

    /**
     * Fetch photo or giveaway contest email template content
     * @return json
     */
    public static function setPCEmailTemplate()
    {
        $arrData = $_POST;

        if(isset($arrData['data']['pcStatus']) && $arrData['data']['pcStatus'] == 'true'){
            //Fetch contest email content
            $userEmailTemplate = WpProQuiz_Model_Email::getDefaultPC(false);
            $adminEmailTemplate = WpProQuiz_Model_Email::getDefaultPC(true);
        }else{
            //Fetch quiz email content
            $userEmailTemplate = WpProQuiz_Model_Email::getDefault(false);
            $adminEmailTemplate = WpProQuiz_Model_Email::getDefault(true);
        }
        
        return json_encode(array(
            'adminEmailTemplate'  => $adminEmailTemplate->getMessage(),
            'adminEmailSubject'  => $adminEmailTemplate->getSubject(),
            'userEmailTemplate'  => $userEmailTemplate->getMessage(),
            'userEmailSubject'  => $userEmailTemplate->getSubject()
        ));
    }
}
