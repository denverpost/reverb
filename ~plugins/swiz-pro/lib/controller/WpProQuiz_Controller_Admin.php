<?php

class WpProQuiz_Controller_Admin
{

    protected $_ajax;

    public function __construct()
    {

        $this->_ajax = new WpProQuiz_Controller_Ajax();
        $this->_ajax->init();

        add_action('admin_menu', array($this, 'register_page'));

        add_filter('set-screen-option', array($this, 'setScreenOption'), 10, 3);
    }

    public function setScreenOption($status, $option, $value)
    {
        if (in_array($option, array('wp_pro_quiz_quiz_overview_per_page', 'wp_pro_quiz_question_overview_per_page'))) {
            return $value;
        }

        return $status;
    }

    private function localizeScript()
    {
        global $wp_locale;

        $isRtl = isset($wp_locale->is_rtl) ? $wp_locale->is_rtl : false;
        
        $quizId = 0;
        if(isset($_GET['quizId']) || isset($_GET['quiz_id'])){
            $quizId = isset($_GET['quizId']) ? (int)$_GET['quizId'] : (int)$_GET['quiz_id'];
        }

        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $quiz = $quizMapper->fetch($quizId);

        $translation_array = array(
            'delete_msg' => __('Do you really want to delete?', 'wp-pro-quiz'),
            'no_title_msg' => __('Please add a title', 'wp-pro-quiz'),
            'no_question_msg' => __('Please add a question in the top text editor', 'wp-pro-quiz'),
            'no_correct_msg' => __('Please identify the Correct answer above prior to saving', 'wp-pro-quiz'),
            'no_answer_msg' => __('Please add one or more answers', 'wp-pro-quiz'),
            'no_quiz_start_msg' => __('Please add a description', 'wp-pro-quiz'),
            'fail_grade_result' => __('The percentage value is invalid. Please enter a value between 0 and 100%', 'wp-pro-quiz'),
            'no_nummber_points' => __('Please add a value in the "Points" field = or > 1', 'wp-pro-quiz'),
            'no_nummber_points_new' => __('Please add a value in the "Points" field = or > 1', 'wp-pro-quiz'),
            'no_selected_quiz' => __('Please select a quiz', 'wp-pro-quiz'),
            'reset_statistics_msg' => __('Do you really want to delete?', 'wp-pro-quiz'),
            'approve_statistics_msg' => __('Approve this image?', 'wp-pro-quiz'),
            'disapprove_statistics_msg' => __('Disapprove this image?', 'wp-pro-quiz'),
            'no_data_available' => __('No data available', 'wp-pro-quiz'),
            'no_sort_element_criterion' => __('Cannot be sorted', 'wp-pro-quiz'),
            'dif_points' => __('"Different points for every answer" cannot be used with the "Free" choice question format', 'wp-pro-quiz'),
            'category_no_name' => __('Please add a category name', 'wp-pro-quiz'),
            'confirm_delete_entry' => __('Do you really want to delete?', 'wp-pro-quiz'),
            'not_all_fields_completed' => __('Please complete all fields', 'wp-pro-quiz'),
            'temploate_no_name' => __('Please add a template name', 'wp-pro-quiz'),
            'closeText' => __('Close', 'wp-pro-quiz'),
            'currentText' => __('Today', 'wp-pro-quiz'),
            'monthNames' => array_values($wp_locale->month),
            'monthNamesShort' => array_values($wp_locale->month_abbrev),
            'dayNames' => array_values($wp_locale->weekday),
            'dayNamesShort' => array_values($wp_locale->weekday_abbrev),
            'dayNamesMin' => array_values($wp_locale->weekday_initial),
            //'dateFormat' => WpProQuiz_Helper_Until::convertPHPDateFormatToJS(get_option('date_format', 'm/d/Y')),
            //e.g. "9 de setembro de 2014" -> change to "hard" dateformat
            'dateFormat' => 'mm/dd/yy',
            'firstDay' => get_option('start_of_week'),
            'isRTL' => $isRtl,
            'no_style_name_msg' => __('Please add a stylesheet name that ends with .css', 'wp-pro-quiz'),
            'unique_stylesheet_msg' => __('This filename already exists, please try another name', 'wp-pro-quiz'),
            'delete_default_stylesheet_msg' => __('Default CSS styles cannot be deleted', 'wp-pro-quiz'),
            'delete_stylesheet_msg' => __('Do you really want to delete this stylesheet?', 'wp-pro-quiz'),
            'delete_applied_stylesheet_msg' => __('Do you really want to delete this stylesheet?', 'wp-pro-quiz'),
            'delete_success_stylesheet_msg' => __('Stylesheet deleted !!', 'wp-pro-quiz'),
            'delete_error_stylesheet_msg' => __('Unable to delete stylesheet from server, please try again', 'wp-pro-quiz'),
            'smallCheckBoxCss' => SMALL_CHECKBOX_CSS,
            'mediumCheckBoxCss' => MEDIUM_CHECKBOX_CSS,
            'largeCheckBoxCss' => LARGE_CHECKBOX_CSS,
            'theKnowCss' => THEKNOW_CSS,
            'denverPostCss' => DENVERPOST_CSS,
            'giveawayContest' => GIVEAWAY_CONTEST_VALUE,
            'photoContest' => PHOTO_CONTEST_VALUE,
            'challengeQuiz' => CHALLENGE_QUIZ_VALUE,
            'thisThatQuiz' => THIS_OR_THAT_QUIZ_VALUE,
            'bucketList' => BUCKETLIST_VALUE,
            'selectedQuizType' => $quiz->getCategoryName(),
            'selectedQuizTypeValue' => $quiz->getCategoryValue(),
            'no_left_header_pc' => __('Please add text in the left details box (e.g. Time & Date)', 'wp-pro-quiz'), // Photo contest left header
            'no_center_header_pc' => __('Please add text in the middle details box (e.g. Prize)', 'wp-pro-quiz'), // Photo contest center header
            'no_right_header_pc' => __('Please add text in the right details box (e.g. Winner Selection)', 'wp-pro-quiz'), // Photo contest right header
            'questionTabMsg' => DEFAULT_QUESTION_TAB_MSG,
            'pcEntryTabMsg' => DEFAULT_PC_ENTIRY_TAB_MSG,
            'thisThatMinAnsMsg' => __('Please add two answers', 'wp-pro-quiz'),
            'thisThatFillAllAnsMsg' => __('Please add two answers', 'wp-pro-quiz'),
            'thisThatAnsCount' => __('Only two answers can be added', 'wp-pro-quiz'),
        );

        wp_localize_script('wpProQuiz_admin_javascript', 'wpProQuizLocalize', $translation_array);
    }

    public function enqueueScript()
    {

        wp_enqueue_script(
            'wpProQuiz_admin_javascript',
            //plugins_url('js/wpProQuiz_admin' . (WPPROQUIZ_DEV ? '' : '.min') . '.js', WPPROQUIZ_FILE),
            plugins_url('js/wpProQuiz_admin.js', WPPROQUIZ_FILE),
            array('jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker'),
            WPPROQUIZ_VERSION
        );

        wp_enqueue_script(
            'wpProQuiz_chosen_javascript',
            plugins_url('js/chosen.jquery.min.js', WPPROQUIZ_FILE)
        );
        
        wp_enqueue_style('wpProQuiz_chosen_css',
            'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.min.css');

        wp_enqueue_style('wpProQuiz_chosen_custom_css',plugins_url('css/chosen-custom.css', WPPROQUIZ_FILE));
        
        wp_enqueue_script(
            'wpProQuiz_jquery_ui','https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js'
        );

        wp_enqueue_style('quizme_font_awsome',
            plugins_url('css/font-awesome.min.css', WPPROQUIZ_FILE),
            array(),
            WPPROQUIZ_VERSION
        );

        $this->localizeScript();
    }

    public function register_page()
    {
        $pages = array();

        $pages[] = add_menu_page(
            'SWIZ Pro',
            'SWIZ Pro',
            'wpProQuiz_show',
            'wpProQuiz',
            array($this, 'route'));

        $pages[] = add_submenu_page(
            'wpProQuiz',
            __('Global settings', 'wp-pro-quiz'),
            __('Global settings', 'wp-pro-quiz'),
            'wpProQuiz_change_settings',
            'wpProQuiz_glSettings',
            array($this, 'route'));

        foreach ($pages as $p) {
            add_action('admin_print_scripts-' . $p, array($this, 'enqueueScript'));
            add_action('load-' . $p, array($this, 'routeLoadAction'));
        }
    }

    public function routeLoadAction()
    {
        $screen = get_current_screen();

        if (!empty($screen)) {
            // Workaround for wp_ajax_hidden_columns() with sanitize_key()
            $name = strtolower($screen->id);

            if (!empty($_GET['module'])) {
                $name .= '_' . strtolower($_GET['module']);
            }

            set_current_screen($name);

            $screen = get_current_screen();
        }

        $helperView = new WpProQuiz_View_GlobalHelperTabs();

        $screen->add_help_tab($helperView->getHelperTab());
        $screen->set_help_sidebar($helperView->getHelperSidebar());

        $this->_route(true);
    }

    public function route()
    {
        $this->_route();
    }

    private function _route($routeAction = false)
    {
        $module = isset($_GET['module']) ? $_GET['module'] : 'overallView';

        if (isset($_GET['page'])) {
            if (preg_match('#wpProQuiz_(.+)#', trim($_GET['page']), $matches)) {
                $module = $matches[1];
            }
        }

        $c = null;

        switch ($module) {
            case 'overallView':
                $c = new WpProQuiz_Controller_Quiz();
                break;
            case 'question':
                $c = new WpProQuiz_Controller_Question();
                break;
            case 'preview':
                $c = new WpProQuiz_Controller_Preview();
                break;
            case 'statistics':
                $c = new WpProQuiz_Controller_Statistics();
                break;
            case 'exportCSV':
                $c = new WpProQuiz_Controller_CSV();
                break;
            case 'importExport':
                $c = new WpProQuiz_Controller_ImportExport();
                break;
            case 'glSettings':
                $c = new WpProQuiz_Controller_GlobalSettings();
                break;
            case 'styleManager':
                $c = new WpProQuiz_Controller_StyleManager();
                break;
            case 'toplist':
                $c = new WpProQuiz_Controller_Toplist();
                break;
            case 'wpq_support':
                $c = new WpProQuiz_Controller_WpqSupport();
                break;
            case 'info_adaptation':
                $c = new WpProQuiz_Controller_InfoAdaptation();
                break;
        }

        if ($c !== null) {
            if ($routeAction) {
                if (method_exists($c, 'routeAction')) {
                    $c->routeAction();
                }
            } else {
                $c->route();
            }
        }
    }
}
