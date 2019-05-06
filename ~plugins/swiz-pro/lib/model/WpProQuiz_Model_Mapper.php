<?php

class WpProQuiz_Model_Mapper
{
    /**
     * Wordpress Datenbank Object
     *
     * @var wpdb
     */
    protected $_wpdb;

    /**
     * @var string
     */
    protected $_prefix;

    /**
     * @var string
     */
    protected $_tableQuestion;
    protected $_tableMaster;
    protected $_tableLock;
    protected $_tableStatistic;
    protected $_tableToplist;
    protected $_tablePrerequisite;
    protected $_tableCategory;
    protected $_tableStatisticRef;
    protected $_tableForm;
    protected $_tableTemplate;


    function __construct()
    {
        global $wpdb;

        $this->_wpdb = $wpdb;
        $this->_prefix = $wpdb->prefix . 'wp_pro_quiz_';

        $this->_tableQuestion = $this->_prefix . 'question';
        $this->_tableMaster = $this->_prefix . 'master';
        $this->_tableLock = $this->_prefix . 'lock';
        $this->_tableStatistic = $this->_prefix . 'statistic';
        $this->_tableToplist = $this->_prefix . 'toplist';
        $this->_tablePrerequisite = $this->_prefix . 'prerequisite';
        $this->_tableCategory = $this->_prefix . 'category';
        $this->_tableStatisticRef = $this->_prefix . 'statistic_ref';
        $this->_tableForm = $this->_prefix . 'form';
        $this->_tableTemplate = $this->_prefix . 'template';
    }

    public function getInsertId()
    {
        return $this->_wpdb->insert_id;
    }

    /**
     * Check whether current quiz type is photocontest
     * @return bool
     */

    public function checkPhotoOrGiveAwayContestQuizStats($quizId = 0)
    {
        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $quiz = $quizMapper->fetch($quizId);
        $quiz->getCategoryValue();

        return ($quiz->getCategoryValue() == PHOTO_CONTEST_VALUE || $quiz->getCategoryValue() == GIVEAWAY_CONTEST_VALUE);
    }

    /*Common function written
    *called in statictics -> fetchHistory()
    */
    public function fetchHistoryResult($quizId, $page, $limit, $users = -1, $startTime = 0, $endTime = 0)
    {
        $timeWhere = '';

        switch ($users) {
            case -3: //only anonym
                $where = 'AND user_id = 0';
                break;
            case -2: //only reg user
                $where = 'AND user_id > 0';
                break;
            case -1: //all
                $where = '';
                break;
            default:
                $where = 'AND user_id = ' . (int)$users;
                break;
        }

        if ($startTime) {
            $timeWhere = 'AND create_time >= ' . (int)$startTime;
        }

        if ($endTime) {
            $timeWhere .= ' AND create_time <= ' . (int)$endTime;
        }

        $questionQuery = !$this->checkPhotoOrGiveAwayContestQuizStats($quizId) ? 'INNER JOIN ' . $this->_tableQuestion . ' AS q ON(q.id = s.question_id)' : "";
        $selectQuePoints = !$this->checkPhotoOrGiveAwayContestQuizStats($quizId) ? ',SUM(q.points) AS g_points'  : "";

        $result = $this->_wpdb->get_results(
            $this->_wpdb->prepare('
                SELECT
                    u.`user_login`, u.`display_name`, u.ID AS user_id,
                    sf.*,
                    SUM(s.correct_count) AS correct_count,
                    SUM(s.incorrect_count) AS incorrect_count,
                    SUM(s.solved_count) as solved_count,
  					SUM(s.points) AS points ' . $selectQuePoints . '
                FROM
                    ' . $this->_tableStatisticRef . ' AS sf
                    INNER JOIN ' . $this->_tableStatistic . ' AS s ON(s.statistic_ref_id = sf.statistic_ref_id)
                    LEFT JOIN ' . $this->_wpdb->users . ' AS u ON(u.ID = sf.user_id)' . $questionQuery . '
                WHERE
                    sf.quiz_id = %d AND sf.is_old = 0 ' . $where . ' ' . $timeWhere . '
                GROUP BY
                    sf.statistic_ref_id
                ORDER BY
                    sf.create_time DESC
                LIMIT
                    %d, %d
            ', $quizId, $page, $limit),
            ARRAY_A
        );
        return $result;
    }
}
