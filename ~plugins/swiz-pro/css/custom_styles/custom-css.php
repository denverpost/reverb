<?php
   
header("Content-type: text/css; charset: UTF-8");
header('Cache-control: must-revalidate');

$absolute_path = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $absolute_path[0] . 'wp-load.php';
require_once($wp_load);

$quizMapper = new WpProQuiz_Model_QuizMapper();

if(isset($_GET['quizId'])){
	$quiz = $quizMapper->fetch($_GET['quizId']);
	echo $quiz->getCustomQuizBox();
}
die;
