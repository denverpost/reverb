<?php

class WpProQuiz_Model_Quiz extends WpProQuiz_Model_Model
{

    const QUIZ_RUN_ONCE_TYPE_ALL = 1;
    const QUIZ_RUN_ONCE_TYPE_ONLY_USER = 2;
    const QUIZ_RUN_ONCE_TYPE_ONLY_ANONYM = 3;

    const QUIZ_TOPLIST_TYPE_ALL = 1;
    const QUIZ_TOPLIST_TYPE_ONLY_USER = 2;
    const QUIZ_TOPLIST_TYPE_ONLY_ANONYM = 3;

    const QUIZ_TOPLIST_SORT_BEST = 1;
    const QUIZ_TOPLIST_SORT_NEW = 2;
    const QUIZ_TOPLIST_SORT_OLD = 3;

    const QUIZ_TOPLIST_SHOW_IN_NONE = 0;
    const QUIZ_TOPLIST_SHOW_IN_NORMAL = 1;
    const QUIZ_TOPLIST_SHOW_IN_BUTTON = 2;

    const QUIZ_MODUS_NORMAL = 0;
    const QUIZ_MODUS_BACK_BUTTON = 1;
    const QUIZ_MODUS_CHECK = 2;
    const QUIZ_MODUS_SINGLE = 3;

    const QUIZ_EMAIL_NOTE_NONE = 0;
    const QUIZ_EMAIL_NOTE_REG_USER = 1;
    const QUIZ_EMAIL_NOTE_ALL = 2;

    const QUIZ_FORM_POSITION_START = 0;
    const QUIZ_FORM_POSITION_END = 1;

    protected $_id = 0;
    protected $_name = '';
    protected $_text = '';
    protected $_resultText;
    protected $_titleHidden = false;
    protected $_btnRestartQuizHidden = false;
    protected $_btnViewQuestionHidden = false;
    protected $_questionRandom = false;
    protected $_answerRandom = false;
    protected $_timeLimit = 0;
    protected $_statisticsOn = false;
    protected $_statisticsIpLock = 1440;
    protected $_resultGradeEnabled = false;
    protected $_showPoints = false;
    protected $_quizRunOnce = false;
    protected $_quizRunOnceType = 0;
    protected $_quizRunOnceCookie = false;
    protected $_quizRunOnceTime = 0;
    protected $_numberedAnswer = false;
    protected $_hideAnswerMessageBox = false;
    protected $_disabledAnswerMark = false;
    protected $_showMaxQuestion = false;
    protected $_showMaxQuestionValue = 1;
    protected $_showMaxQuestionPercent = false;

    //0.19
    protected $_toplistActivated = false;
    protected $_toplistDataAddPermissions = 1;
    protected $_toplistDataSort = 1;
    protected $_toplistDataAddMultiple = false;
    protected $_toplistDataAddBlock = 1;
    protected $_toplistDataShowLimit = 1;
    protected $_toplistDataShowQuizResult = false;
    protected $_toplistDataShowIn = 0;
    protected $_toplistDataCaptcha = false;

    protected $_toplistData = array();

    protected $_showAverageResult = false;

    protected $_prerequisite = false;

    //0.22
    protected $_toplistDataAddAutomatic = false;
    protected $_quizModus = 0;
    protected $_showReviewQuestion = false;
    protected $_quizSummaryHide = false;
    protected $_skipQuestionDisabled = false;
    protected $_emailNotification = 0;

    //0.24
    protected $_userEmailNotification = false;
    protected $_showCategoryScore = false;
    protected $_hideResultCorrectQuestion = false;
    protected $_hideResultQuizTime = false;
    protected $_hideResultPoints = false;

    //0.25
    protected $_autostart = false;
    protected $_forcingQuestionSolve = false;
    protected $_hideQuestionPositionOverview = false;
    protected $_hideQuestionNumbering = false;

    //0.27
    protected $_formActivated = false;
    protected $_formShowPosition = 1;
    protected $_startOnlyRegisteredUser = false;
    protected $_questionsPerPage = 0;
    protected $_sortCategories = false;
    protected $_showCategory = false;

    //0.29
    protected $_categoryId = 0;
    protected $_categoryName = '';
    protected $_adminEmail = null;
    protected $_userEmail = null;

    //0.33
    protected $_pluginContainer = null;

    protected $_facebookShare = false;
    protected $_twShare = false;
    protected $_emailShare = false;
    protected $_linkedinShare = false;

    protected $_quizStyle = '';
    protected $_customQuizBox = '';

    protected $_privacyPolicy = '';
    protected $_termsConditions = '';
    protected $_contestRules = '';

    protected $_isPrivacyPolicy = false;
    protected $_isTermsConditions = false;
    protected $_isContestRules = false;

    protected $_privacyPolicyText = '';
    protected $_termsConditionsText = '';
    protected $_contestRulesText = '';

    protected $_categoryValue = '';

    protected $_customFormHeader = '';
    protected $_isCustomFormHeader = false;

    protected $_embedCode = '';

    protected $_pcLeftHeader = '';
    protected $_pcCenterHeader = '';
    protected $_pcRightHeader = '';
    protected $_pcDescription = '';

    protected $_pcLeftHeaderTitle = '';
    protected $_pcCenterHeaderTitle = '';
    protected $_pcRightHeaderTitle = '';

    protected $_quizSharingDescription = '';

    public function setId($_id)
    {
        $this->_id = (int)$_id;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setName($_name)
    {
        $this->_name = (string)$_name;

        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setText($_text)
    {
        $this->_text = (string)$_text;

        return $this;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function setResultText($_resultText)
    {
        $this->_resultText = $_resultText;

        return $this;
    }

    public function getResultText()
    {
        return $this->_resultText;
    }

    public function setTitleHidden($_titleHidden)
    {
        $this->_titleHidden = (bool)$_titleHidden;

        return $this;
    }

    public function isTitleHidden()
    {
        return $this->_titleHidden;
    }

    public function setQuestionRandom($_questionRandom)
    {
        $this->_questionRandom = (bool)$_questionRandom;

        return $this;
    }

    public function isQuestionRandom()
    {
        return $this->_questionRandom;
    }

    public function setAnswerRandom($_answerRandom)
    {
        $this->_answerRandom = (bool)$_answerRandom;

        return $this;
    }

    public function isAnswerRandom()
    {
        return $this->_answerRandom;
    }

    public function setTimeLimit($_timeLimit)
    {
        $this->_timeLimit = (int)$_timeLimit;

        return $this;
    }

    public function getTimeLimit()
    {
        return $this->_timeLimit;
    }

    public function setStatisticsOn($_statisticsOn)
    {
        $this->_statisticsOn = (bool)$_statisticsOn;

        return $this;
    }

    public function isStatisticsOn()
    {
        return $this->_statisticsOn;
    }

    public function setStatisticsIpLock($_statisticsIpLock)
    {
        $this->_statisticsIpLock = (int)$_statisticsIpLock;

        return $this;
    }

    public function getStatisticsIpLock()
    {
        return $this->_statisticsIpLock;
    }

    public function setResultGradeEnabled($_resultGradeEnabled)
    {
        $this->_resultGradeEnabled = (bool)$_resultGradeEnabled;

        return $this;
    }

    public function isResultGradeEnabled()
    {
        return $this->_resultGradeEnabled;
    }

    public function setShowPoints($_showPoints)
    {
        $this->_showPoints = (bool)$_showPoints;

        return $this;
    }

    public function isShowPoints()
    {
        return $this->_showPoints;
    }

    public function fetchSumQuestionPoints()
    {
        $m = new WpProQuiz_Model_QuizMapper();

        return $m->sumQuestionPoints($this->_id);
    }

    public function fetchCountQuestions()
    {
        $m = new WpProQuiz_Model_QuizMapper();

        return $m->countQuestion($this->_id);
    }

    public function setBtnRestartQuizHidden($_btnRestartQuizHidden)
    {
        $this->_btnRestartQuizHidden = (bool)$_btnRestartQuizHidden;

        return $this;
    }

    public function isBtnRestartQuizHidden()
    {
        return $this->_btnRestartQuizHidden;
    }

    public function setBtnViewQuestionHidden($_btnViewQuestionHidden)
    {
        $this->_btnViewQuestionHidden = (bool)$_btnViewQuestionHidden;

        return $this;
    }

    public function isBtnViewQuestionHidden()
    {
        return $this->_btnViewQuestionHidden;
    }

    public function setQuizRunOnce($_quizRunOnce)
    {
        $this->_quizRunOnce = (bool)$_quizRunOnce;

        return $this;
    }

    public function isQuizRunOnce()
    {
        return $this->_quizRunOnce;
    }

    public function setQuizRunOnceCookie($_quizRunOnceCookie)
    {
        $this->_quizRunOnceCookie = (bool)$_quizRunOnceCookie;

        return $this;
    }

    public function isQuizRunOnceCookie()
    {
        return $this->_quizRunOnceCookie;
    }

    public function setQuizRunOnceType($_quizRunOnceType)
    {
        $this->_quizRunOnceType = (int)$_quizRunOnceType;

        return $this;
    }

    public function getQuizRunOnceType()
    {
        return $this->_quizRunOnceType;
    }

    public function setQuizRunOnceTime($_quizRunOnceTime)
    {
        $this->_quizRunOnceTime = (int)$_quizRunOnceTime;

        return $this;
    }

    public function getQuizRunOnceTime()
    {
        return $this->_quizRunOnceTime;
    }

    public function setNumberedAnswer($_numberedAnswer)
    {
        $this->_numberedAnswer = (bool)$_numberedAnswer;

        return $this;
    }

    public function isNumberedAnswer()
    {
        return $this->_numberedAnswer;
    }

    public function setHideAnswerMessageBox($_hideAnswerMessageBox)
    {
        $this->_hideAnswerMessageBox = (bool)$_hideAnswerMessageBox;

        return $this;
    }

    public function isHideAnswerMessageBox()
    {
        return $this->_hideAnswerMessageBox;
    }

    public function setDisabledAnswerMark($_disabledAnswerMark)
    {
        $this->_disabledAnswerMark = (bool)$_disabledAnswerMark;

        return $this;
    }

    public function isDisabledAnswerMark()
    {
        return $this->_disabledAnswerMark;
    }

    public function setShowMaxQuestion($_showMaxQuestion)
    {
        $this->_showMaxQuestion = (bool)$_showMaxQuestion;

        return $this;
    }

    public function isShowMaxQuestion()
    {
        return $this->_showMaxQuestion;
    }

    public function setShowMaxQuestionValue($_showMaxQuestionValue)
    {
        $this->_showMaxQuestionValue = (int)$_showMaxQuestionValue;

        return $this;
    }

    public function getShowMaxQuestionValue()
    {
        return $this->_showMaxQuestionValue;
    }

    public function setShowMaxQuestionPercent($_showMaxQuestionPercent)
    {
        $this->_showMaxQuestionPercent = (bool)$_showMaxQuestionPercent;

        return $this;
    }

    public function isShowMaxQuestionPercent()
    {
        return $this->_showMaxQuestionPercent;
    }

    public function setToplistActivated($_toplistActivated)
    {
        $this->_toplistActivated = (bool)$_toplistActivated;

        return $this;
    }

    public function isToplistActivated()
    {
        return $this->_toplistActivated;
    }

    public function setToplistDataAddPermissions($_toplistDataAddPermissions)
    {
        $this->_toplistDataAddPermissions = (int)$_toplistDataAddPermissions;

        return $this;
    }

    public function getToplistDataAddPermissions()
    {
        return $this->_toplistDataAddPermissions;
    }

    public function setToplistDataSort($_toplistDataSort)
    {
        $this->_toplistDataSort = (int)$_toplistDataSort;

        return $this;
    }

    public function getToplistDataSort()
    {
        return $this->_toplistDataSort;
    }

    public function setToplistDataAddMultiple($_toplistDataAddMultiple)
    {
        $this->_toplistDataAddMultiple = (bool)$_toplistDataAddMultiple;

        return $this;
    }

    public function isToplistDataAddMultiple()
    {
        return $this->_toplistDataAddMultiple;
    }

    public function setToplistDataAddBlock($_toplistDataAddBlock)
    {
        $this->_toplistDataAddBlock = (int)$_toplistDataAddBlock;

        return $this;
    }

    public function getToplistDataAddBlock()
    {
        return $this->_toplistDataAddBlock;
    }

    public function setToplistDataShowLimit($_toplistDataShowLimit)
    {
        $this->_toplistDataShowLimit = (int)$_toplistDataShowLimit;

        return $this;
    }

    public function getToplistDataShowLimit()
    {
        return $this->_toplistDataShowLimit;
    }

    public function setToplistData($_toplistData)
    {
        if (!empty($_toplistData)) {
            $d = unserialize($_toplistData);

            if ($d !== false) {
                $this->setModelData($d);
            }
        }

        return $this;
    }

    public function getToplistData()
    {

        $a = array(
            'toplistDataAddPermissions' => $this->getToplistDataAddPermissions(),
            'toplistDataSort' => $this->getToplistDataSort(),
            'toplistDataAddMultiple' => $this->isToplistDataAddMultiple(),
            'toplistDataAddBlock' => $this->getToplistDataAddBlock(),
            'toplistDataShowLimit' => $this->getToplistDataShowLimit(),
            'toplistDataShowIn' => $this->getToplistDataShowIn(),
            'toplistDataCaptcha' => $this->isToplistDataCaptcha(),
            'toplistDataAddAutomatic' => $this->isToplistDataAddAutomatic()
        );

        return serialize($a);
    }

    public function setToplistDataShowIn($_toplistDataShowIn)
    {
        $this->_toplistDataShowIn = (int)$_toplistDataShowIn;

        return $this;
    }

    public function getToplistDataShowIn()
    {
        return $this->_toplistDataShowIn;
    }

    public function setToplistDataCaptcha($_toplistDataCaptcha)
    {
        $this->_toplistDataCaptcha = (bool)$_toplistDataCaptcha;

        return $this;
    }

    public function isToplistDataCaptcha()
    {
        return $this->_toplistDataCaptcha;
    }

    public function setShowAverageResult($_showAverageResult)
    {
        $this->_showAverageResult = (bool)$_showAverageResult;

        return $this;
    }

    public function isShowAverageResult()
    {
        return $this->_showAverageResult;
    }

    public function setPrerequisite($_prerequisite)
    {
        $this->_prerequisite = (bool)$_prerequisite;

        return $this;
    }

    public function isPrerequisite()
    {
        return $this->_prerequisite;
    }

    public function setToplistDataAddAutomatic($_toplistDataAddAutomatic)
    {
        $this->_toplistDataAddAutomatic = (bool)$_toplistDataAddAutomatic;

        return $this;
    }

    public function isToplistDataAddAutomatic()
    {
        return $this->_toplistDataAddAutomatic;
    }

    public function setQuizModus($_quizModus)
    {
        $this->_quizModus = (int)$_quizModus;

        return $this;
    }

    public function getQuizModus()
    {
        return $this->_quizModus;
    }

    public function setShowReviewQuestion($_showReviewQuestion)
    {
        $this->_showReviewQuestion = (bool)$_showReviewQuestion;

        return $this;
    }

    public function isShowReviewQuestion()
    {
        return $this->_showReviewQuestion;
    }

    public function setQuizSummaryHide($_quizSummaryHide)
    {
        $this->_quizSummaryHide = (bool)$_quizSummaryHide;

        return $this;
    }

    public function isQuizSummaryHide()
    {
        return $this->_quizSummaryHide;
    }

    public function setSkipQuestionDisabled($_skipQuestion)
    {
        $this->_skipQuestionDisabled = (bool)$_skipQuestion;

        return $this;
    }

    public function isSkipQuestionDisabled()
    {
        return $this->_skipQuestionDisabled;
    }

    public function setEmailNotification($_emailNotification)
    {
        $this->_emailNotification = (int)$_emailNotification;

        return $this;
    }

    public function getEmailNotification()
    {
        return $this->_emailNotification;
    }

    public function setUserEmailNotification($_userEmailNotification)
    {
        $this->_userEmailNotification = (bool)$_userEmailNotification;

        return $this;
    }

    public function isUserEmailNotification()
    {
        return $this->_userEmailNotification;
    }

    public function setShowCategoryScore($_showCategoryScore)
    {
        $this->_showCategoryScore = (bool)$_showCategoryScore;

        return $this;
    }

    public function isShowCategoryScore()
    {
        return $this->_showCategoryScore;
    }

    public function setHideResultCorrectQuestion($_hideResultCorrectQuestion)
    {
        $this->_hideResultCorrectQuestion = (bool)$_hideResultCorrectQuestion;

        return $this;
    }

    public function isHideResultCorrectQuestion()
    {
        return $this->_hideResultCorrectQuestion;
    }

    public function setHideResultQuizTime($_hideResultQuizTime)
    {
        $this->_hideResultQuizTime = (bool)$_hideResultQuizTime;

        return $this;
    }

    public function isHideResultQuizTime()
    {
        return $this->_hideResultQuizTime;
    }

    public function setHideResultPoints($_hideResultPoints)
    {
        $this->_hideResultPoints = (bool)$_hideResultPoints;

        return $this;
    }

    public function isHideResultPoints()
    {
        return $this->_hideResultPoints;
    }

    public function setAutostart($_autostart)
    {
        $this->_autostart = (bool)$_autostart;

        return $this;
    }

    public function isAutostart()
    {
        return $this->_autostart;
    }

    public function setFacebookShare($_facebookShare)
    {
        $this->_facebookShare = (bool)$_facebookShare;

        return $this;
    }

    public function isFacebookShare()
    {
        return $this->_facebookShare;
    }

    public function setTwitterShare($_twShare)
    {
        $this->_twShare = (bool)$_twShare;

        return $this;
    }

    public function isTwitterShare()
    {
        return $this->_twShare;
    }

    public function setEmailShare($_emailShare)
    {
        $this->_emailShare = (bool)$_emailShare;

        return $this;
    }

    public function isEmailShare()
    {
        return $this->_emailShare;
    }

    public function setLinkedinShare($_linkedinShare)
    {
        $this->_linkedinShare = (bool)$_linkedinShare;

        return $this;
    }

    public function isLinkedinShare()
    {
        return $this->_linkedinShare;
    }

    /**
     * Sets the privacy policy toggle value
     * @param bool $_isPrivacyPolicy
     * @return WpProQuiz_Model_Quiz
     */
    public function setIsPrivacyPolicy($_isPrivacyPolicy)
    {
        $this->_isPrivacyPolicy = (bool)$_isPrivacyPolicy;

        return $this;
    }

    /**
     * Gets the privacy policy toggle value.
     * @return bool
     */
    public function isPrivacyPolicy()
    {
        return $this->_isPrivacyPolicy;
    }

    /**
     * Sets the terms conditions toggle value
     * @param bool $_isTermsConditions
     * @return WpProQuiz_Model_Quiz
     */
    public function setIsTermsConditions($_isTermsConditions)
    {
        $this->_isTermsConditions = (bool)$_isTermsConditions;

        return $this;
    }

    /**
     * Gets the terms conditions toggle value.
     * @return bool
     */
    public function isTermsConditions()
    {
        return $this->_isTermsConditions;
    }

    /**
     * Sets the contest rules toggle value
     * @param bool $_isContestRules
     * @return WpProQuiz_Model_Quiz
     */
    public function setIsContestRules($_isContestRules)
    {
        $this->_isContestRules = (bool)$_isContestRules;

        return $this;
    }

    /**
     * Gets the contest rules toggle value.
     *
     * @return bool
     */
    public function isContestRules()
    {
        return $this->_isContestRules;
    }

    /**
     * Sets the privacy policy text.
     * @param string $_privacyPolicyText
     * @return WpProQuiz_Model_Quiz
     */
    public function setPrivacyPolicyText($_privacyPolicyText)
    {
        $_privacyPolicyText = !empty($_privacyPolicyText) ? $_privacyPolicyText : DEFAULT_PRIVACY_POLICY_TEXT;
        $this->_privacyPolicyText = (string)$_privacyPolicyText;

        return $this;
    }

    /**
     * Gets the privacy policy text.
     *
     * @return string privacy policy text
     */
    public function getPrivacyPolicyText()
    {
        return $this->_privacyPolicyText;
    }

    /**
     * Sets the terms conditions text.
     * @param string $_termsConditionsText
     * @return WpProQuiz_Model_Quiz
     */
    public function setTermsConditionsText($_termsConditionsText)
    {
        $_termsConditionsText = !empty($_termsConditionsText) ? $_termsConditionsText : DEFAULT_TERMS_CONDITIONS_TEXT;
        $this->_termsConditionsText = (string)$_termsConditionsText;

        return $this;
    }

    /**
     * Gets the terms conditions text.
     *
     * @return string terms conditions text
     */
    public function getTermsConditionsText()
    {
        return $this->_termsConditionsText;
    }

    /**
     * Sets the contest rules text.
     * @param string $_contestRulesText
     * @return WpProQuiz_Model_Quiz
     */
    public function setContestRulesText($_contestRulesText)
    {
        $_contestRulesText = !empty($_contestRulesText) ? $_contestRulesText : DEFAULT_CONTEST_RULES_TEXT;
        $this->_contestRulesText = (string)$_contestRulesText;

        return $this;
    }

    /**
     * Gets the contest rules text.
     *
     * @return string contest rules text
     */
    public function getContestRulesText()
    {
        return $this->_contestRulesText;
    }

    /**
     * Sets the custom form header.
     * @param string $_customFormHeader
     * @return WpProQuiz_Model_Quiz
     */
    public function setCustomFormHeader($_customFormHeader)
    {
        $_customFormHeader = !empty($_customFormHeader) ? $_customFormHeader : DEFAULT_CUSTOM_FORM_HEADER;
        $this->_customFormHeader = (string)$_customFormHeader;

        return $this;
    }

    /**
     * Gets the custom form header.
     *
     * @return string custom form header
     */
    public function getCustomFormHeader()
    {
        return $this->_customFormHeader;
    }

    /**
     * Sets the custom form header toggle value
     * @param bool $_isCustomFormHeader
     * @return WpProQuiz_Model_Quiz
     */
    public function setIsCustomFormHeader($_isCustomFormHeader)
    {
        $this->_isCustomFormHeader = (bool)$_isCustomFormHeader;

        return $this;
    }

    /**
     * Gets the custom form header toggle value.
     *
     * @return bool
     */
    public function isCustomFormHeader()
    {
        return $this->_isCustomFormHeader;
    }

    public function setQuizStyle( $_quizStyle)
    {
        $this->_quizStyle = (string)$_quizStyle;

        return $this;
    }

    public function getQuizStyle()
    {
        return $this->_quizStyle;
    }

    public function setCustomQuizBox($_customQuizBox)
    {
        $this->_customQuizBox = (string)$_customQuizBox;

        return $this;
    }

    public function getCustomQuizBox()
    {
        return $this->_customQuizBox;
    }

    /**
     * Sets the privacy policy content.
     * @param string $_privacyPolicy
     * @return WpProQuiz_Model_Quiz
     */
    public function setPrivacyPolicy($_privacyPolicy)
    {
        $_privacyPolicy = !empty($_privacyPolicy) ? $_privacyPolicy : get_option('wpProQuiz_privacy_policy');
        $this->_privacyPolicy = (string)$_privacyPolicy;

        return $this;
    }

    /**
     * Gets the privacy policy content.
     *
     * @return string privacy policy content
     */
    public function getPrivacyPolicy()
    {
        return $this->_privacyPolicy;
    }

    /**
     * Sets the terms conditions content.
     * @param string $_termsConditions
     * @return WpProQuiz_Model_Quiz
     */
    public function setTermsConditions($_termsConditions)
    {
        $_termsConditions = !empty($_termsConditions) ? $_termsConditions : get_option('wpProQuiz_terms_conditions');
        $this->_termsConditions = (string)$_termsConditions;

        return $this;
    }

    /**
     * Gets the terms conditions content.
     *
     * @return string terms conditions content
     */
    public function getTermsConditions()
    {
        return $this->_termsConditions;
    }

    /**
     * Sets the contest rules content.
     * @param string $_contestRules
     * @return WpProQuiz_Model_Quiz
     */
    public function setContestRules($_contestRules)
    {
        $_contestRules = !empty($_contestRules) ? $_contestRules : get_option('wpProQuiz_content_rules');
        $this->_contestRules = (string)$_contestRules;

        return $this;
    }

    /**
     * Gets the contest rules content.
     *
     * @return string contest rules content
     */
    public function getContestRules()
    {
        return $this->_contestRules;
    }

    public function setForcingQuestionSolve($_forcingQuestionSolve)
    {
        $this->_forcingQuestionSolve = (bool)$_forcingQuestionSolve;

        return $this;
    }

    public function isForcingQuestionSolve()
    {
        return $this->_forcingQuestionSolve;
    }

    public function setHideQuestionPositionOverview($_hideQuestionPositionOverview)
    {
        $this->_hideQuestionPositionOverview = (bool)$_hideQuestionPositionOverview;

        return $this;
    }

    public function isHideQuestionPositionOverview()
    {
        return $this->_hideQuestionPositionOverview;
    }

    public function setHideQuestionNumbering($_hideQuestionNumbering)
    {
        $this->_hideQuestionNumbering = (bool)$_hideQuestionNumbering;

        return $this;
    }

    public function isHideQuestionNumbering()
    {
        return $this->_hideQuestionNumbering;
    }

    public function setFormActivated($_formActivated)
    {
        $this->_formActivated = (bool)$_formActivated;

        return $this;
    }

    public function isFormActivated()
    {
        return $this->_formActivated;
    }

    public function setFormShowPosition($_formShowPosition)
    {
        $this->_formShowPosition = (int)$_formShowPosition;

        return $this;
    }

    public function getFormShowPosition()
    {
        return $this->_formShowPosition;
    }

    public function setStartOnlyRegisteredUser($_startOnlyRegisteredUser)
    {
        $this->_startOnlyRegisteredUser = (bool)$_startOnlyRegisteredUser;

        return $this;
    }

    public function isStartOnlyRegisteredUser()
    {
        return $this->_startOnlyRegisteredUser;
    }

    public function setQuestionsPerPage($_questionsPerPage)
    {
        $this->_questionsPerPage = (int)$_questionsPerPage;

        return $this;
    }

    public function getQuestionsPerPage()
    {
        return $this->_questionsPerPage;
    }

    public function setSortCategories($_sortCategories)
    {
        $this->_sortCategories = (bool)$_sortCategories;

        return $this;
    }

    public function isSortCategories()
    {
        return $this->_sortCategories;
    }

    public function setShowCategory($_showCategory)
    {
        $this->_showCategory = (bool)$_showCategory;

        return $this;
    }

    public function isShowCategory()
    {
        return $this->_showCategory;
    }

    public function setCategoryId($_categoryId)
    {
        $this->_categoryId = (int)$_categoryId;

        return $this;
    }

    public function getCategoryId()
    {
        return $this->_categoryId;
    }

    public function setCategoryName($_categoryName)
    {
        $this->_categoryName = (string)$_categoryName;

        return $this;
    }

    public function getCategoryName()
    {
        return $this->_categoryName;
    }

    /**
     * Sets the category value
     * @param string $_categoryValue
     * @return WpProQuiz_Model_Quiz
    */
    public function setCategoryValue($_categoryValue)
    {
        $this->_categoryValue = (string)$_categoryValue;

        return $this;
    }

    /**
     * Gets the category value
     *
     * @return string
    */
    public function getCategoryValue()
    {
        return $this->_categoryValue;
    }


    public function setEmbedCode($_embedCode)
    {
        $this->_embedCode = (string)$_embedCode;
        return $this;
    }

    public function getEmbedCode()
    {
        return $this->_embedCode;
    }

    /**
     * Sets the left header value of photo contest
     * @param string $_pcLeftHeader
     * @return WpProQuiz_Model_Quiz
    */
    public function setPcLeftHeader($_pcLeftHeader)
    {
        $this->_pcLeftHeader = (string)$_pcLeftHeader;
        return $this;
    }

    /**
     * Gets left header value of photo contest
     *
     * @return string
    */
    public function getPcLeftHeader()
    {
        return $this->_pcLeftHeader;
    }

    /**
     * Sets the center header value of photo contest
     * @param string $_pcCenterHeader
     * @return WpProQuiz_Model_Quiz
    */
    public function setPcCenterHeader($_pcCenterHeader)
    {
        $this->_pcCenterHeader = (string)$_pcCenterHeader;
        return $this;
    }

    /**
     * Gets center header value of photo contest
     *
     * @return string
    */
    public function getPcCenterHeader()
    {
        return $this->_pcCenterHeader;
    }

    /**
     * Sets the right header value of photo contest
     * @param string $_pcRightHeader
     * @return WpProQuiz_Model_Quiz
    */
    public function setPcRightHeader($_pcRightHeader)
    {
        $this->_pcRightHeader = (string)$_pcRightHeader;
        return $this;
    }

    /**
     * Gets right header value of photo contest
     *
     * @return string
    */
    public function getPcRightHeader()
    {
        return $this->_pcRightHeader;
    }

    /**
     * Sets the description of photo contest
     * @param string $_pcDescription
     * @return WpProQuiz_Model_Quiz
    */
    public function setPcDescription($_pcDescription)
    {
        $this->_pcDescription = (string)$_pcDescription;
        return $this;
    }

    /**
     * Gets the description of photo contest
     *
     * @return string
    */
    public function getPcDescription()
    {
        return $this->_pcDescription;
    }

    /**
     * Sets the left header title of photo contest
     * @param string $_pcLeftHeaderTitle
     * @return WpProQuiz_Model_Quiz
    */
    public function setPcLeftHeaderTitle($_pcLeftHeaderTitle)
    {
        $_pcLeftHeaderTitle = !empty($_pcLeftHeaderTitle) ? $_pcLeftHeaderTitle : DEFAULT_PC_LEFT_HEADER_TITLE;
        $this->_pcLeftHeaderTitle = (string)$_pcLeftHeaderTitle;
        return $this;
    }

    /**
     * Gets left header title of photo contest
     *
     * @return string
    */
    public function getPcLeftHeaderTitle()
    {
        return $this->_pcLeftHeaderTitle;
    }

    /**
     * Sets the center header title of photo contest
     * @param string $_pcCenterHeaderTitle
     * @return WpProQuiz_Model_Quiz
    */
    public function setPcCenterHeaderTitle($_pcCenterHeaderTitle)
    {
        $_pcCenterHeaderTitle = !empty($_pcCenterHeaderTitle) ? $_pcCenterHeaderTitle : DEFAULT_PC_CENTER_HEADER_TITLE;
        $this->_pcCenterHeaderTitle = (string)$_pcCenterHeaderTitle;
        return $this;
    }

    /**
     * Gets center header title of photo contest
     *
     * @return string
    */
    public function getPcCenterHeaderTitle()
    {
        return $this->_pcCenterHeaderTitle;
    }

    /**
     * Sets the right header title of photo contest
     * @param string $_pcRightHeaderTitle
     * @return WpProQuiz_Model_Quiz
    */
    public function setPcRightHeaderTitle($_pcRightHeaderTitle)
    {
        $_pcRightHeaderTitle = !empty($_pcRightHeaderTitle) ? $_pcRightHeaderTitle : DEFAULT_PC_RIGHT_HEADER_TITLE;
        $this->_pcRightHeaderTitle = (string)$_pcRightHeaderTitle;
        return $this;
    }

    /**
     * Gets right header title of photo contest
     *
     * @return string
    */
    public function getPcRightHeaderTitle()
    {
        return $this->_pcRightHeaderTitle;
    }

    /**
     * Sets the quiz sharing description
     * @param string $_quizSharingDescription
     * @return WpProQuiz_Model_Quiz
    */
    public function setQuizSharingDescription($_quizSharingDescription)
    {
        $this->_quizSharingDescription = (string)$_quizSharingDescription;
        return $this;
    }

    /**
     * Gets the quiz sharing description
     *
     * @return string
    */
    public function getQuizSharingDescription()
    {
        return $this->_quizSharingDescription;
    }

    public function setAdminEmail($_adminEmail)
    {
        $this->_adminEmail = $_adminEmail;

        return $this;
    }

    /**
     * @param bool|false $serialize
     * @return null|string|WpProQuiz_Model_Email
     */
    public function getAdminEmail($serialize = false)
    {
        if ($this->_adminEmail === null) {
            return null;
        }

        if (is_object($this->_adminEmail) || $this->_adminEmail instanceof WpProQuiz_Model_Email) {
            if ($serialize) {
                return @serialize($this->_adminEmail);
            }
        } else {
            if (!$serialize) {
                if (WpProQuiz_Helper_Until::saveUnserialize($this->_adminEmail, $into) === false) {
                    return null;
                }

                $this->_adminEmail = $into;
            }
        }

        return $this->_adminEmail;
    }

    public function setUserEmail($_userEmail)
    {
        $this->_userEmail = $_userEmail;

        return $this;
    }

    /**
     * @param bool|false $serialize
     * @return null|string|WpProQuiz_Model_Email
     */
    public function getUserEmail($serialize = false)
    {
        if ($this->_userEmail === null) {
            return null;
        }

        if (is_object($this->_userEmail) || $this->_userEmail instanceof WpProQuiz_Model_Email) {
            if ($serialize) {
                return @serialize($this->_userEmail);
            }
        } else {
            if (!$serialize) {
                if (WpProQuiz_Helper_Until::saveUnserialize($this->_userEmail, $into) === false) {
                    return null;
                }

                $this->_userEmail = $into;
            }
        }

        return $this->_userEmail;
    }

    public function setPluginContainer($_pluginContainer)
    {
        $this->_pluginContainer = $_pluginContainer;

        return $this;
    }

    public function getPluginContainer($serialize = false)
    {
        if ($this->_pluginContainer === null) {
            $this->_pluginContainer = new WpProQuiz_Model_PluginContainer();
        }

        if (is_object($this->_pluginContainer) || $this->_pluginContainer instanceof WpProQuiz_Model_PluginContainer) {
            if ($serialize) {
                return @serialize($this->_pluginContainer);
            }
        } else {
            if (!$serialize) {
                if (WpProQuiz_Helper_Until::saveUnserialize($this->_pluginContainer, $into) === false) {
                    return null;
                }

                $this->_pluginContainer = $into;
            }
        }

        return $this->_pluginContainer;
    }
}
