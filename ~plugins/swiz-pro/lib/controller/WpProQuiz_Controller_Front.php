<?php

class WpProQuiz_Controller_Front
{

    /**
     * @var WpProQuiz_Model_GlobalSettings
     */
    private $_settings = null;

    public function __construct()
    {
        $this->loadSettings();

        add_action('wp_enqueue_scripts', array($this, 'loadDefaultScripts'),100);
        add_shortcode('WpProQuiz', array($this, 'shortcode'));
        add_shortcode('WpProQuiz_toplist', array($this, 'shortcodeToplist'));
        //add_action('wp_head', array($this, 'addMetaData'));
    }

    public function loadQuizStyles($id)
    {
        $quizMapper       = new WpProQuiz_Model_QuizMapper();
        $quiz             = $quizMapper->fetch($id);
        $strQuizStyleName = $quiz->getQuizStyle();
        $strFileName      = WPPROQUIZ_CUSTOM_STYLE_PATH.'/'.$strQuizStyleName;

        /*if(file_exists($strFileName)){
            wp_enqueue_style('front-custom-style',
                plugins_url('css/custom_styles/'.$strQuizStyleName, WPPROQUIZ_FILE),
                array(),
                WPPROQUIZ_VERSION
            );
        }*/

        // Showing database css to external file
        $url = plugins_url('css/custom_styles/'.CUSTOM_CSS_FILENAME, WPPROQUIZ_FILE).'?quizId='.$id;
        echo '<link rel="stylesheet" href="'.$url.'" type="text/css" media="screen" />';
    }

    public function loadDefaultScripts()
    {
        global $post;

        $content = "";
        if(!empty($post) && isset($post->post_content)){
            $content = $post->post_content;
        }

        if(!has_shortcode($content,'WpProQuiz')) return false;

        //Loading scripts from here

        wp_enqueue_script('jquery');

        $data = array(
            'src' => plugins_url('css/wpProQuiz_front.css', WPPROQUIZ_FILE),
            'deps' => array(),
            'ver' => WPPROQUIZ_VERSION,
        );

        $data = apply_filters('wpProQuiz_front_style', $data);

        wp_enqueue_style('wpProQuiz_front_style', $data['src'], $data['deps'], $data['ver']);

        wp_enqueue_style('jquery-ui','https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');

        wp_enqueue_style('wpProQuiz-lightbox-css','https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.10.0/css/lightbox.min.css');

        if ($this->_settings->isJsLoadInHead()) {
            $this->loadJsScripts(false, true, true);
        }

        wp_enqueue_style('quizme_front_style',
            plugins_url('css/quizme.css', WPPROQUIZ_FILE),
            array(),
            WPPROQUIZ_VERSION
        );

        wp_enqueue_style('quizme_font_awsome',
            plugins_url('css/font-awesome.min.css', WPPROQUIZ_FILE),
            array(),
            WPPROQUIZ_VERSION
        );
    }

    private function loadJsScripts($footer = true, $quiz = true, $toplist = false)
    {
        if ($quiz) {
            wp_enqueue_script(
                'wpProQuiz_front_javascript',
                //plugins_url('js/wpProQuiz_front' . (WPPROQUIZ_DEV ? '' : '.min') . '.js', WPPROQUIZ_FILE),
                plugins_url('js/wpProQuiz_front.js', WPPROQUIZ_FILE),
                array('jquery-ui-sortable'),
                WPPROQUIZ_VERSION,
                $footer
            );

            wp_enqueue_script(
                'wpProQuiz_jquery_ui','https://code.jquery.com/ui/1.10.3/jquery-ui.js'
            );

            //This includes the script needed at child side to set dynamic iframe height
            wp_enqueue_script(
                'wpProQuiz_iframe_child_javascript',
                plugins_url('js/iframeResizer.contentWindow.min.js', WPPROQUIZ_FILE),
                '',
                WPPROQUIZ_VERSION,
                $footer
            );

            //This includes the script needed for image light box
            wp_enqueue_script(
                'wpProQuiz-jquery-lightbox','https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.10.0/js/lightbox.min.js
'
            );

            $parentIframeUrl = "";
            if(isset($_SERVER['HTTP_REFERER'])){
                //if page is iframe then change the current url
                $arrUrlParts = parse_url($_SERVER['HTTP_REFERER']);
                if(isset($arrUrlParts['query'])){
                    parse_str($arrUrlParts['query'], $query);
                    if(isset($query['parentLocationURL'])){
                        $parentIframeUrl = $query['parentLocationURL'];
                    }
                }
            }

            wp_localize_script('wpProQuiz_front_javascript', 'WpProQuizGlobal', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'loadData' => __('Loading', 'wp-pro-quiz'),
                'questionNotSolved' => __('You must answer this question', 'wp-pro-quiz'),
                'questionsNotSolved' => __('You must answer all questions in order to complete the quiz',
                    'wp-pro-quiz'),
                'fieldsNotFilled' => __('Please complete all required fields', 'wp-pro-quiz'),
                'quizDpImage' => WPPROQUIZ_DP_IMAGE,
                'fbAppId' => get_option('wpProQuiz_facebookAppId'),
                'twitterShareURL' => TWITTER_SHARE_URL,
                'linkedinShareURL' => LINKEDIN_SHARE_URL,
                'giveawayContest' => GIVEAWAY_CONTEST_VALUE,
                'photoContest' => PHOTO_CONTEST_VALUE,
                'challengeQuiz' => CHALLENGE_QUIZ_VALUE,
                'thisThatQuiz' => THIS_OR_THAT_QUIZ_VALUE,
                'bucketList' => BUCKETLIST_VALUE,
                'bucketListTags' => BUCKETLIST_TAGS,
                'domainName' => DEFAULT_OG_TAG_URL,
                'iframepagetitle' => IFRAME_PAGE_TITLE,
                'validImageExt' => VALID_IMAGE_EXT,
                'parentIframeUrl' => $parentIframeUrl
            ));
        }

        if ($toplist) {
            wp_enqueue_script(
                'wpProQuiz_front_javascript_toplist',
                plugins_url('js/wpProQuiz_toplist' . (WPPROQUIZ_DEV ? '' : '.min') . '.js', WPPROQUIZ_FILE),
                array('jquery-ui-sortable'),
                WPPROQUIZ_VERSION,
                $footer
            );

            if (!wp_script_is('wpProQuiz_front_javascript')) {
                wp_localize_script('wpProQuiz_front_javascript_toplist', 'WpProQuizGlobal', array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'loadData' => __('Loading', 'wp-pro-quiz'),
                    'questionNotSolved' => __('You must answer this question.', 'wp-pro-quiz'),
                    'questionsNotSolved' => __('You must answer all questions before you can completed the quiz.',
                        'wp-pro-quiz'),
                    'fieldsNotFilled' => __('All fields have to be filled.', 'wp-pro-quiz')
                ));
            }
        }

        if (!$this->_settings->isTouchLibraryDeactivate()) {
            wp_enqueue_script(
                'jquery-ui-touch-punch',
                plugins_url('js/jquery.ui.touch-punch.min.js', WPPROQUIZ_FILE),
                array('jquery-ui-sortable'),
                '0.2.2',
                $footer
            );
        }
    }

    public function shortcode($attr)
    {
        $id = $attr[0];
        $content = '';

        if (!$this->_settings->isJsLoadInHead()) {
            $this->loadJsScripts();
        }

        $this->loadQuizStyles($id);

        if (is_numeric($id)) {
            ob_start();

            $this->handleShortCode($id);

            $content = ob_get_contents();

            ob_end_clean();
        }

        if ($this->_settings->isAddRawShortcode()) {
            return '[raw]' . $content . '[/raw]';
        }

        return $content;
    }

    /**
     * Add open graph and twitter meta data into header
     * Function is used for both static and dynamic values
     *
     * @return void
     */
    public function addMetaData()
    {
        global $post;
        $result = array();
        $pattern = get_shortcode_regex(); //get shortcode regex pattern wordpress function
        $id = 0;
        if (preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches)){
            $keys = array();
            $result = array();
            
            foreach( $matches[0] as $key => $value) {
                // $matches[3] return the shortcode attribute as string
                // replace space with '&' for parse_str() function
                $get = str_replace(" ", "&" , $matches[3][$key] );
                parse_str($get, $output);

                //get all shortcode attribute keys
                $keys = array_unique( array_merge(  $keys, array_keys($output)) );
                $result[] = $output;
            }

            if($keys && $result){
                // Loop the result array and add the missing shortcode attribute key
                foreach ($result as $key => $value) {
                    // Loop the shortcode attribute key
                    foreach ($keys as $attr_key) {
                        $result[$key][$attr_key] = isset( $result[$key][$attr_key] ) ? $result[$key][$attr_key] : NULL;
                    }
                    //sort the array key
                    ksort( $result[$key]);              
                }
            }

            $attr=array_keys($result[0]);
            $id=$attr[0];
        }

        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $quiz = $quizMapper->fetch($id);
        $textQuizDescription = $quiz->getText();
        preg_match( '@src="([^"]+)"@' , $textQuizDescription, $match );
        $imgSrc = array_pop($match); // It will return image src
        $textDescription = strip_tags(preg_replace_callback('/<img\s[^>]+>/i', function ($matches) {  // return empty str or original text
            return !strpos($matches[0], "keyword") ? "" : $matches[0];
        }, $textQuizDescription));

        $strQuizName = $quiz->getName();
        $title = !empty($strQuizName) ? $strQuizName : DEFAULT_OG_TAG_TITLE;
        $description = !empty($textDescription) ? $textDescription : DEFAULT_OG_TAG_DESC;
        $url = DEFAULT_OG_TAG_URL;
        $image = !empty($imgSrc) ? $imgSrc : WPPROQUIZ_DP_IMAGE;

        echo '<meta property="og:type" content="'.OG_TAG_TYPE.'">';
        echo '<meta property="og:title" content="'.$title.'">';
        echo '<meta property="og:description" content="'.$description.'">';
        echo '<meta property="og:url" content="'.$url.'">';
        echo '<meta property="og:image" content="'.$image.'">';
        
        echo '<meta property="twitter:card" content="'.TWITTER_CARD.'">';
        echo '<meta property="twitter:title" content="'.$title.'">';
        echo '<meta property="twitter:description" content="'.$description.'">';
        echo '<meta property="twitter:image" content="'.$image.'">';
    }

    public function handleShortCode($id)
    {
        $view = new WpProQuiz_View_FrontQuiz();

        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $questionMapper = new WpProQuiz_Model_QuestionMapper();
        $categoryMapper = new WpProQuiz_Model_CategoryMapper();
        $formMapper = new WpProQuiz_Model_FormMapper();

        $quiz = $quizMapper->fetch($id);

        $maxQuestion = false;

        if ($quiz->isShowMaxQuestion() && $quiz->getShowMaxQuestionValue() > 0) {

            $value = $quiz->getShowMaxQuestionValue();

            if ($quiz->isShowMaxQuestionPercent()) {
                $count = $questionMapper->count($id);

                $value = ceil($count * $value / 100);
            }

            $question = $questionMapper->fetchAll($id, true, $value);
            $maxQuestion = true;

        } else {
            $question = $questionMapper->fetchAll($id);
        }

        if (empty($quiz) || empty($question) && $quiz->getCategoryValue() != PHOTO_CONTEST_VALUE && $quiz->getCategoryValue() != GIVEAWAY_CONTEST_VALUE) {
            echo '';

            return;
        }

        wp_localize_script('wpProQuiz_front_javascript', 'WpProSelectedQuizData', array(
            'quizType' => $quiz->getCategoryName(),
            'quizTypeValue' => $quiz->getCategoryValue(),
            'quizSharingDescription' => $quiz->getQuizSharingDescription(),
        ));

        $forms = $formMapper->fetch($quiz->getId());
        $view->quiz = $quiz;
        $view->question = $question;
        $view->category = $categoryMapper->fetchByQuiz($quiz->getId());
        $view->forms = $forms;
        $pcImages = array();
        $totalPcRows = 0;

        // Fetching photo contest images
        if($quiz->getCategoryValue() == PHOTO_CONTEST_VALUE){
            $arrResponse = $this->fetchPcGalleryImages($id, 0, DEFAULT_PC_GALLERY_IMG);
            $pcImages = $arrResponse['pcImages'];
            $totalPcRows = $arrResponse['totalPcRows'];
        }

        $view->pcImages = $pcImages;
        $view->totalPcRows = $totalPcRows;

        if ($maxQuestion) {
            $view->showMaxQuestion();
        } else {
            $view->show();
        }
    }

    public function shortcodeToplist($attr)
    {
        $id = $attr[0];
        $content = '';

        if (!$this->_settings->isJsLoadInHead()) {
            $this->loadJsScripts(true, false, true);
        }

        if (is_numeric($id)) {
            ob_start();

            $this->handleShortCodeToplist($id, isset($attr['q']));

            $content = ob_get_contents();

            ob_end_clean();
        }

        if ($this->_settings->isAddRawShortcode() && !isset($attr['q'])) {
            return '[raw]' . $content . '[/raw]';
        }

        return $content;
    }

    private function handleShortCodeToplist($quizId, $inQuiz = false)
    {
        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $view = new WpProQuiz_View_FrontToplist();

        $quiz = $quizMapper->fetch($quizId);

        if ($quiz->getId() <= 0 || !$quiz->isToplistActivated()) {
            echo '';

            return;
        }

        $view->quiz = $quiz;
        $view->points = $quizMapper->sumQuestionPoints($quizId);
        $view->inQuiz = $inQuiz;
        $view->show();
    }

    private function loadSettings()
    {
        $mapper = new WpProQuiz_Model_GlobalSettingsMapper();

        $this->_settings = $mapper->fetchAll();
    }

    public static function ajaxQuizLoadData($data)
    {
        $id = $data['quizId'];

        $view = new WpProQuiz_View_FrontQuiz();

        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $questionMapper = new WpProQuiz_Model_QuestionMapper();
        $categoryMapper = new WpProQuiz_Model_CategoryMapper();
        $formMapper = new WpProQuiz_Model_FormMapper();

        $quiz = $quizMapper->fetch($id);

        if ($quiz->isShowMaxQuestion() && $quiz->getShowMaxQuestionValue() > 0) {

            $value = $quiz->getShowMaxQuestionValue();

            if ($quiz->isShowMaxQuestionPercent()) {
                $count = $questionMapper->count($id);

                $value = ceil($count * $value / 100);
            }

            $question = $questionMapper->fetchAll($id, true, $value);

        } else {
            $question = $questionMapper->fetchAll($id);
        }

        if (empty($quiz) || empty($question)) {
            return null;
        }

        $view->quiz = $quiz;
        $view->question = $question;
        $view->category = $categoryMapper->fetchByQuiz($quiz->getId());
        $view->forms = $formMapper->fetch($quiz->getId());

        return json_encode($view->getQuizData());
    }

    /**
     * Fetch more photo contest gallery images
     * @return json
     */
    public static function loadPcImages($data)
    {
        $id = $data['quizId'];
        $startPage = $data['startPage'];

        $endStatus = false;
        $frontController = new WpProQuiz_Controller_Front();
        $arrResponse = $frontController->fetchPcGalleryImages($id, $startPage, DEFAULT_PC_GALLERY_IMG, true);
        $pcImages = $arrResponse['pcImages'];
        $totalPcRows = $arrResponse['totalPcRows'];
        $newStartPage = $startPage + DEFAULT_PC_GALLERY_IMG;

        $existingImages = count($pcImages) + $startPage;

        if($totalPcRows <= $existingImages) {
            //Count if more images is left after this execution
            $endStatus = true;
        }

        $arrData['pcImages'] = $pcImages;
        $arrData['newStartPage'] = $newStartPage;
        $arrData['endStatus'] = $endStatus;

        return json_encode($arrData);
    }

    /**
     * Common function fetch photo contest gallery images
     * @return array
     */
    public function fetchPcGalleryImages($quizId, $startPage, $limit, $responseHTML = false)
    {
        $formMapper = new WpProQuiz_Model_FormMapper();
        $fieldId = $formMapper->ApproveFieldId($quizId);
        $forms = $formMapper->fetch($quizId);
        $thumbnailWidth = get_option("wpProQuiz_thumbnail_size_w");
        $thumbnailHeight = get_option("wpProQuiz_thumbnail_size_h");

        $statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();
        $arrResponse = $statisticRefMapper->fetchPcImages($quizId, $fieldId, $startPage, $limit);
        $statisticModel = $arrResponse['results'];
        $totalPcRows = $arrResponse['totalPcRows'];
        $pcImages = array();

        foreach ($statisticModel as $model) {
            /* @var $model WpProQuiz_Model_StatisticHistory */
            $formData = $model->getFormData();

            if(!empty($fieldId) && "1" == $formData[$fieldId]) {
                foreach ($forms as $form) {
                    /* @var $form WpProQuiz_Model_Form */
                    if ($form->getType() == WpProQuiz_Model_Form::FORM_TYPE_FILE) {
                        $originalImgURL = $formData != null && isset($formData[$form->getFormId()])
                            ? WpProQuiz_Helper_Form::formToString($form, $formData[$form->getFormId()], false)
                            : '----';

                        $arrImgInfo = pathinfo($originalImgURL); //fetch the information of image

                        if(isset($arrImgInfo['dirname']) && isset($arrImgInfo['filename']) && isset($arrImgInfo['extension'])){
                            $postImageName = "-".$thumbnailWidth."x".$thumbnailHeight; // prepare post image name
                            $thumbnailImage = $arrImgInfo['dirname']."/".$arrImgInfo['filename'].$postImageName.".".strtolower($arrImgInfo['extension']); // prepare thumbnail image name
                        }
                        if(!@getimagesize($thumbnailImage)){
                            // if thumbnail image is not exists then take the original one
                            $thumbnailImage = $originalImgURL;
                        }

                        $pcImages[] = "<li><a href='".$originalImgURL."' data-lightbox='photo-contest-gallery'><img src='".$thumbnailImage."'></a></li>";
                    }
                }
            }
        }

        return array('pcImages' => $pcImages,'totalPcRows' => $totalPcRows);
    }
}
