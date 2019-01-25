<?php
/**
* @property WpProQuiz_Model_Form[] forms
* @property WpProQuiz_Model_Quiz quiz
* @property array prerequisiteQuizList
* @property WpProQuiz_Model_Template[] templates
* @property array quizList
* @property bool captchaIsInstalled
* @property WpProQuiz_Model_Category[] categories
* @property string header
*/
class WpProQuiz_View_QuizEdit extends WpProQuiz_View_View
{
public function show()
{
?>
<style>
.wpProQuiz_quizModus th, .wpProQuiz_quizModus td {
border-right: 1px solid #A0A0A0;
padding: 5px;
}
.wpProQuiz_demoBox {
position: relative;
}
.clearfix:after {
visibility: hidden;
display: block;
font-size: 0;
content: " ";
clear: both;
height: 0;
}
.clearfix { display: inline-block; }
* html .clearfix { height: 1%; }
.clearfix { display: block; }
.createQuizSectionWrap {
margin: 20px 0;
}
.checkboxToggleWrap input {
position: absolute;
left: -9999px;
}
.checkBoxDragger {
position: relative;
display: block;
cursor: pointer;
border-radius: 24px;
transition: background 250ms 100ms;
background: rgba(0,0,0,.05);
width: 65px;
height: 27px;
}
.checkBoxDragger::before {
text-transform: uppercase;
font-size: 10px;
line-height: 24px;
position: absolute;
right: 11px;
color:#9a9a9a;
}
.checkBoxDragger::after {
position: absolute;
content: '';
width: 27px;
height: 27px;
top: 0;
left: 2px;
border-radius: 50%;
transition: 150ms linear;
background: #fff;
box-shadow: 0 1px 2px 0 rgba(34,36,38,.15), 0 0 0 1px rgba(34,36,38,.15) inset;
}
.checkboxToggleWrap input:checked + .checkBoxDragger {
background:#2196F3!important
}
.checkboxToggleWrap input:checked + .checkBoxDragger::after {
left: 40px;
}
.checkboxToggleWrap input:checked + .checkBoxDragger::before {
right: auto;
left: 14px;
color:white;
}
.quizOptionSetting,
.quizOptionTitle {
display: inline-block;
float: left;
font-size: 14px;
font-weight: 500;
margin-top: 4px;
}
.quizOptionSetting {
float: right;
margin-top: 0;
}
.btn {
text-decoration: none;
border: none;
display: inline-block;
border-radius: 3px;
margin-top: 5px;
padding: 3px 14px;
}
.btn-large {
padding: 8px 14px;
font-size: 14px;
font-weight: 700;
text-transform: uppercase;
margin-bottom: 25px;
}
.btn-green {
background: #4CAF50;
color: #fff;
}
.btn-green:hover, .btn-green:focus {
background: #8BC34A;
color: #fff;
box-shadow: none;
}
.pull-right {
float: right;
}
.hndle.active i {
transform: rotate(180deg);
}
.downArrow {
font-size: 10px;
color: #666666;
}
.non-accordion-inside{
padding: 8px 12px;
}
.full-width{
width: 100%;
}
.form-table th{
padding: 0;
}
.grayBtns {
border: 0 !important;
background-color: #cecece !important;
border-radius: 0 !important;
}
.grayBtns:hover{
background-color: #999 !important;
border: 0 !important;
}
.stylesheet-list-width{
width: 88%;
}
</style>
<div class="wrap quizWrapper wpProQuiz_quizEdit">
  <div class="denvorLogWrap">
    <img src="<?php echo WPPROQUIZ_DP_COVER_IMAGE; ?>">
  </div>
  <h3 class="overviewTitle">
    <?php echo $this->header; ?>    
  </h3> 

  <form method="post"
    action="admin.php?page=wpProQuiz&action=addEdit&quizId=<?php echo $this->quiz->getId(); ?>">
    <input type="hidden" name="ajax_quiz_id" value="<?php echo $this->quiz->getId(); ?>">
    <div class="clearfix backQuizContainer">
      <div class="backQuizBtn leftWrap">
        <a class="backBtn" href="admin.php?page=wpProQuiz">
          <?php _e('Back to overview', 'wp-pro-quiz'); ?>
        </a>
      </div>
      <div class="rightWrap">
        <select name="templateLoadId">
          <?php
          foreach ($this->templates as $template) {
          echo '<option value="', $template->getTemplateId(), '">', esc_html($template->getName()), '</option>';
          }
          ?>
        </select>
        <input type="submit" name="templateLoad" value="<?php _e('Load template', 'wp-pro-quiz'); ?>"
        class="button-primary">
      </div>
    </div>
    
    <div id="poststuff">
      <?php do_action('wpProQuiz_action_plugin_quizEdit', $this); ?>
      <div class="postbox">
        <h3 class="hndle"><?php _e('Quiz Title', 'wp-pro-quiz'); ?> <?php _e('(required)','wp-pro-quiz'); ?>
        <!--<i class="pull-right downArrow">▼</i>-->
        </h3>
        <div class="insides non-accordion-inside">
          <input name="name" id="wpProQuiz_title" type="text" class="regular-text full-width" value="<?php echo htmlspecialchars($this->quiz->getName(), ENT_QUOTES); ?>">
        </div>
      </div>

      <div class="postbox">
        <h3 class="hndle"><?php _e((!$this->checkQuizId() ? 'Select Quiz Type' : 'Quiz Type'), 'wp-pro-quiz'); ?>
        </h3>
        <div class="insides non-accordion-inside">
          <?php
            if(!$this->checkQuizId()){
          ?>
          <p class="description">
            <?php _e('You can manage types in global settings.', 'wp-pro-quiz'); ?>
          </p>
          <div>
            <select name='category' class='full-width quizType'>
              <?php
              foreach ($this->categories as $category) {
                echo '<option ' . ($category->getCategoryValue() == CHALLENGE_QUIZ_VALUE ? 'selected="selected"' : '') . ' value="' . $category->getCategoryId() . '" catValue="'.$category->getCategoryValue().'">' . $category->getCategoryName() . '</option>';
              }
              ?>
            </select>
          </div>
          <?php } 
              else{
                echo $this->quiz->getCategoryName();
                echo "<input type='hidden' name='category' value='".$this->quiz->getCategoryId()."'>";
                echo "<div id='categoryValue' style='display:none;'>" . $this->quiz->getCategoryValue() . "</div>";
              }
          ?>
          <div id="categoryMsgBox"
            style="display:none; padding: 5px; border: 1px solid rgb(160, 160, 160); background-color: rgb(255, 255, 168); font-weight: bold; margin: 5px; ">
            Kategorie gespeichert
          </div>
        </div>
      </div>

      <div class="postbox">
        <div class="insides non-accordion-inside tabContainerWrapper <?php echo $this->checkPhotoOrGiveAwayQuiz() ? PHOTOCONTEST_CLASS : ''?>">
          <div class="form-table">
            <ul class="tabMenu">
              <li class="content"><a href="#content">Content</a></li>
              <li class="questions"><a href='#questions'>Questions</a></li>
              <li class="styling"><a href="#styling">Styling</a></li>
              <li class="settings"><a href="#settings">Settings</a></li>
              <li class="emailTemplate"><a href="#emailTemplate">Email Template</a></li>
            </ul>
            <div class="clearfix"></div>
            <div class="tabs" id="content">
              <div class="postbox">
                <h3 class="hndle" id="quiz_description"><?php _e('Quiz Description / Cover Page', 'wp-pro-quiz'); ?> </h3>
                <div class="inside">
                  <table class="form-table quiz-description-table">
                    <tbody>
                      <tr class="pcHeaderContent">
                        <td>
                          <div class="descriptionWrap">
                            <strong>Header Image (required)</strong>
                          </div>
                        </td>
                        <td colspan="2">
                          <div class="descriptionWrap">
                            <em>
                              Please add a header image, we recommend 920px (or wider) X 250px ; or simply add title text in the editor below
                            </em>
                          </div>
                        </td>
                      </tr>
                      <tr class="quizCoverTxt">
                        <td colspan="2">
                          <div class="descriptionWrap">
                            <em>
                              Any text or images you add in the description box will appear on a cover page shown before the quiz questions. To skip, select the Autostart toggle below.
                            </em>
                          </div>
                        </td>
                      </tr> 
                    </tbody>
                  </table> 
                  
                  <table class="form-table photo-contest-details-block">
                    <tbody>
                      <tr>
                        <td colspan="3">
                          <?php
                            wp_editor($this->quiz->getText(), "text", array('textarea_rows' => 6));
                          ?>
                        </td>  
                      </tr>
                      <tr class="quizAutoStart pcHide">
                        <th scope="row"><?php _e('Autostart', 'wp-pro-quiz'); ?></th>
                        <td>
                          <fieldset>
                              <legend class="screen-reader-text">
                                <span><?php _e('Autostart', 'wp-pro-quiz'); ?></span>
                              </legend>                            
                              <span class="quizOptionSetting">
                                <div class="checkboxToggleWrap">
                                  <input type="checkbox" id="autostart" value="1" name="autostart" <?php $this->checked($this->quiz->isAutostart()); ?> >
                                  <label class="checkBoxDragger" for="autostart"></label>
                                </div>
                              </span>
                            <p class="description">
                              <?php _e('If you enable this option, the quiz will start automatically after the page is loaded, and a quiz description is not required.',
                              'wp-pro-quiz'); ?>
                            </p>
                          </fieldset>
                        </td>
                      </tr>
                      <tr class="pcHeaderContent">
                        <th colspan="3">
                          <strong class="contestDetailWrap">Contest Details (e.g Date, Prize, Winner Selection)</strong>
                        </th>
                      </tr>
                      <tr class="pcHeaderContent detailInfoContainer">
                        <td>
                          <div class="titleWrapper">
                            <input type="text" name="pc_left_header_title" class="pcHeaderTitle" id="pc_left_header_title" value="<?php echo $this->quiz->getPcLeftHeaderTItle(); ?>">
                          </div>
                          <div class="detailWrappr">
                            <textarea name="pc_left_header" class="pcHeaderValue" id="pc_left_header" placeholder="Details about the Contest start and end dates"><?php echo $this->quiz->getPcLeftHeader(); ?></textarea>
                          </div>
                        </td>
                        <td>
                          <div class="titleWrapper">
                            <input type="text" name="pc_center_header_title" class="pcHeaderTitle" id="pc_center_header_title" value="<?php echo $this->quiz->getPcCenterHeaderTitle(); ?>"> 
                          </div>
                          <div class="detailWrappr">
                            <textarea name="pc_center_header" class="pcHeaderValue" id="pc_center_header" placeholder="Details about the Contest prizes"><?php echo $this->quiz->getPcCenterHeader(); ?></textarea>
                          </div>
                        </td>
                        <td>
                          <div class="titleWrapper">
                            <input type="text" name="pc_right_header_title" class="pcHeaderTitle" id="pc_right_header_title" value="<?php echo $this->quiz->getPcRightHeaderTitle(); ?>">
                          </div>
                          <div class="detailWrappr">
                            <textarea name="pc_right_header" class="pcHeaderValue" id="pc_right_header" placeholder="Details about the Contest winner selection"><?php echo $this->quiz->getPcRightHeader(); ?></textarea>
                          </div>
                        </td>
                      </tr>
                      <tr class="pcHeaderContent" style="display: none;">
                        <td colspan="3">
                          <div class="descriptionWrap">
                            <em class="swizpro_pc_header_msg"></em>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <?php $this->photoContestDetailsBlock(); ?>
              <?php $this->form(); ?>
              <?php $this->resultTextOption(); ?>
              <?php $this->footerLinks(); ?>
            </div>
            <div class="tabs stylingContainer" id="styling">
                <div style="display: none;" id="custom_style_path"><?php echo WPPROQUIZ_CUSTOM_STYLE_URL; ?></div>
                <div style="display: none;" id="quiz_id"><?php echo $this->quiz->getId(); ?></div>
                <div style="display: none;" id="applied_stylesheet"><?php echo $this->quiz->getQuizStyle(); ?></div>

                <h3>Quiz Styling</h3>
                <div class="quizeStylingWrap">
                    <select name="quizStyle" id="" class="stylesheet-list-width quizStyle">
                    <?php
                    echo '<option value="select" disabled selected>Select</option>';
                    foreach ($this->arrCustomFiles as $strCustomFile) {
                        if($this->checkQuizId()){
                            $strSelected=($this->quiz->getQuizStyle() == $strCustomFile) ? 'selected="selected"' : '';
                        }else{
                            $strSelected=($strCustomFile == SMALL_CHECKBOX_CSS) ? 'selected="selected"' : '';
                        }
                        echo '<option value="'.$strCustomFile.'" '.$strSelected.'>' . ($strCustomFile) . '</option>';
                    }
                    ?>
                    </select>
                    <input type="button" style="float: right;" class="button-primary deleteStyleSheet" value="Delete Stylesheet">
                </div>
                <h3>Custom CSS</h3>
                <textarea name="custom_quiz_box" id="" class="full-width custom_quiz_box" rows="10"><?php echo $this->quiz->getCustomQuizBox(); ?></textarea>
                <div class="clearfix">&nbsp;</div>

                <div class="addStyle addNewStyle">
                  <table class="form-table">
                    <tbody>
                      <tr>
                        <th scope="row">
                          <?php _e('Save as a new stylesheet', 'wp-pro-quiz'); ?>
                        </th>
                        <td>
                          <fieldset>
                            <legend class="screen-reader-text">
                              <span><?php _e('Save as a new stylesheet', 'wp-pro-quiz'); ?></span>
                            </legend>
                            <p class="description">This option saves the Custom CSS box as a new "Quiz Style" that will appear in the dropdown for future use</p>
                            <span class="quizOptionSetting">
                              <div class="checkboxToggleWrap">
                                  <input type="checkbox" id="save_as_new_stylesheet" value="1" name="save_as_new_stylesheet">
                                  <label class="checkBoxDragger" for="save_as_new_stylesheet"></label>
                              </div>
                            </span>
                          </fieldset>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2"><div id="wpProQuiz_stylesheet_name_div" class="saveFileWrap"></div></td>
                      </tr>
                    </tbody>
                  </table>
                  </div>                
                <div id="wpProQuiz_stylesheet_name_error"></div>
                <div class="loaderWrapper">
                  <div class="overLayBg"></div>
                  <div class="loaderContainer"></div>                  
                </div>
            </div>
            <div class="tabs" id="settings">
              <?php $this->quizMode(); ?>
              <?php $this->quizOptions(); ?>
              <?php $this->questionOptions(); ?>
              <?php $this->resultOptions(); ?>
              <?php //$this->leaderboardOptions(); ?>
            </div>
            <div class="tabs" id="emailTemplate">
              <?php $this->adminEmailOption(); ?>
              <?php $this->userEmailOption(); ?>
            </div>
            <div class="tabs questionWrap" id="questions">
            <?php
                if($this->checkQuizId()){
                    if(current_user_can('wpProQuiz_edit_quiz') && !$this->checkPhotoOrGiveAwayQuiz()) {
                        echo '<a class="button-primary" href="?page=wpProQuiz&module=question&action=addEdit&quiz_id='.$this->quiz->getId().'">Add question</a>';
                    } 
                    if(!class_exists('WP_List_Table')){
                      require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
                    }
                    if($this->checkPhotoOrGiveAwayQuiz()){ 
                      echo $this->displayLoader(); 
                    ?>
                      <div id='wpProQuiz_historyLoadContext'></div> 
                      <div id="wpProQuiz_tabHistory" style="margin-top: 10px;">

                          <div style="float: left;" id="historyNavigation">
                              <input style="font-weight: bold;" class="button-secondary navigationLeft" value="&lt;"
                                     type="button">
                              <select class="navigationCurrentPage">
                                  <option value="1">1</option>
                              </select>
                              <input style="font-weight: bold;" class="button-secondary navigationRight" value="&gt;"
                                     type="button">
                          </div>

                          <div style="float: right;">
                              <a class="button-secondary wpProQuiz_update" href="#"><?php _e('Refresh', 'wp-pro-quiz'); ?></a>
                              <?php if (current_user_can('wpProQuiz_reset_statistics')) { ?>
                                  <a class="button-secondary wpProQuiz_resetComplete" href="#"><?php _e('Reset entire statistic',
                                          'wp-pro-quiz'); ?></a>
                              <?php } ?>
                          </div>

                          <div style="clear: both;"></div>
                      </div>
                    <?php
                    }else{
                      $table = new WpProQuiz_View_QuestionOverallTable($this->questionItems, $this->questionCount, $this->categoryItems, $this->perPage);

                      $table->prepare_items();
                      $table->display();
                    }
                }else{
                    echo DEFAULT_QUESTION_TAB_MSG;
                }
            ?>
            </div>
          </div>
        </div>
      </div>

      <div class="clearfix createTemplateWrap">
        <div class="leftWrap">
          <input type="submit" name="submit" class="button-primary" id="wpProQuiz_save"
          value="<?php _e('Save', 'wp-pro-quiz'); ?>">
        </div>
        <div class="rightWrap">
          <input type="text" placeholder="<?php _e('template name', 'wp-pro-quiz'); ?>"
          class="regular-text" name="templateName">
          <select name="templateSaveList" class="createTemplate">
            <option value="0">=== <?php _e('Create new template', 'wp-pro-quiz'); ?> ===</option>
            <?php
            foreach ($this->templates as $template) {
            echo '<option value="', $template->getTemplateId(), '">', esc_html($template->getName()), '</option>';
            }
            ?>
          </select> 
          <input type="submit" name="template" class="button-primary" id="wpProQuiz_saveTemplate"
          value="<?php _e('Save as template', 'wp-pro-quiz'); ?>">
        </div>
      </div>
    </div>
  </form>
</div>
<?php
}
/**
 * Check whether quiz id is available or not
 * @return bool
 */
private function checkQuizId()
{
  return ($this->quiz->getId() > 0 ? true : false); 
}
private function resultOptions()
{
?>
<div class="postbox questionDisplayOptionWrap">
  <h3 class="hndle" id="quiz_results_page_options"><?php _e('Quiz Results Page Options', 'wp-pro-quiz'); ?></h3>
  <div class="inside">
    <table class="form-table">
      <tbody>
        <tr class="socialShareLinks">
          <th scope="row">
            <?php _e('Social sharing links', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Social sharing links', 'wp-pro-quiz'); ?></span>
              </legend>
              <span class="quizOptionSetting">
                <select class="form-control social-share-chosen" name="shareButtonsSettings[]" data-placeholder="Choose an option please" multiple>
                  <option value="facebookShare" <?php $this->selected($this->quiz->isFacebookShare()); ?>>Facebook</option>
                  <option value="twitterShare" <?php $this->selected($this->quiz->isTwitterShare()); ?>>Twitter</option>
                  <option value="linkedinShare" <?php $this->selected($this->quiz->isLinkedinShare()); ?>>LinkedIn</option>
                  <option value="emailShare" <?php $this->selected($this->quiz->isEmailShare()); ?>>Email</option>
                </select>
              </span>
            </fieldset>
          </td>
        </tr>
        <tr class="quizSharingDesc">
          <th scope="row">
            <?php _e('Quiz Sharing Description', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Quiz Sharing Description', 'wp-pro-quiz'); ?></span>
              </legend>
              <p class="description">Add text here to override the default sharing description.</p>
              <span class="quizOptionSetting">
                  <input type="text" class="regular-text" id="quizSharingDesc"  value="<?php echo $this->quiz->getQuizSharingDescription(); ?>" name="quiz_sharing_description">
              </span>
            </fieldset>
          </td>
        </tr>
        <tr class="showAvgPoints pcHide thisThatHide">
          <th scope="row">
            <?php _e('Show average points', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Show average points', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label>
                <input type="checkbox" value="1"
                name="showAverageResult" <?php $this->checked($this->quiz->isShowAverageResult()); ?>>
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="showAverageResult" value="1" name="showAverageResult" <?php $this->checked($this->quiz->isShowAverageResult()); ?> >
                  <label class="checkBoxDragger" for="showAverageResult"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('To show average points earned, Statistics must be enabled as a Quiz Option.', 'wp-pro-quiz'); ?>
              </p>
              <div class="wpProQuiz_demoBox">
                <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
                <div class="demoPopup">
                  <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/averagePoints.jpg'; ?> ">
                </div>
              </div>
            </fieldset>
          </td>
        </tr>
        <tr class="hideCorrectQuestions pcHide thisThatHide">
          <th scope="row">
            <?php _e('Hide correct questions - display', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Hide correct questions - display', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label>
                <input type="checkbox" name="hideResultCorrectQuestion"
                value="1" <?php $this->checked($this->quiz->isHideResultCorrectQuestion()); ?>>
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="hideResultCorrectQuestion" value="1" name="hideResultCorrectQuestion" <?php $this->checked($this->quiz->isHideResultCorrectQuestion()); ?> >
                  <label class="checkBoxDragger" for="hideResultCorrectQuestion"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('Hide the number of correctly answered questions from the quiz results page.',
                'wp-pro-quiz'); ?>
              </p>
            <div class="wpProQuiz_demoBox">
              <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
              <div class="demoPopup">
                <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/hideCorrectQuestion.jpg'; ?> ">
              </div>
            </div>
            </fieldset>
          </td>
        </tr>
        <tr class="hideQuizTime pcHide">
          <th scope="row">
            <?php _e('Hide quiz time - display', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Hide quiz time - display', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label>
                <input type="checkbox" name="hideResultQuizTime"
                value="1" <?php $this->checked($this->quiz->isHideResultQuizTime()); ?>>
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="hideResultQuizTime" value="1" name="hideResultQuizTime" <?php $this->checked($this->quiz->isHideResultQuizTime()); ?> >
                  <label class="checkBoxDragger" for="hideResultQuizTime"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('Hide the amount of time it takes a user to complete the quiz from the results page.',
                'wp-pro-quiz'); ?>
              </p>
            <div class="wpProQuiz_demoBox">
              <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
              <div class="demoPopup">
                <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/hideQuizTime.jpg'; ?> ">
              </div>
            </div>
            </fieldset>
          </td>
        </tr>
        <tr class="hideScore pcHide thisThatHide">
          <th scope="row">
            <?php _e('Hide score - display', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Hide score - display', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label>
                <input type="checkbox" name="hideResultPoints"
                value="1" <?php $this->checked($this->quiz->isHideResultPoints()); ?>>
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="hideResultPoints" value="1" name="hideResultPoints" <?php $this->checked($this->quiz->isHideResultPoints()); ?> >
                  <label class="checkBoxDragger" for="hideResultPoints"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('Hide the user’s score from the quiz results page.',
                'wp-pro-quiz'); ?>
              </p>
              <div class="wpProQuiz_demoBox">              
                <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
                <div class="demoPopup">
                  <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/hideQuizPoints.jpg'; ?> ">
                </div>
              </div>
            </fieldset>  
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?php
}
private function questionOptions()
{
?>
<div class="postbox questionDisplayOptionWrap">
  <h3 class="hndle" id="question_display_options"><?php _e('Question Display Options', 'wp-pro-quiz'); ?></h3>
  <div class="inside">
    <table class="form-table questinDisplayTable">
      <tbody>
        <tr class="thisThatHide">
          <th scope="row">
            <?php _e('Show points', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Show points', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label for="show_points">
                <input type="checkbox" id="show_points" value="1"
                name="showPoints" <?php echo $this->quiz->isShowPoints() ? 'checked="checked"' : '' ?> >
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="show_points" value="1" name="showPoints" <?php echo $this->quiz->isShowPoints() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="show_points"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('Enable this option to show how many points are reachable for each question.','wp-pro-quiz'); ?>
              </p>
            </fieldset>
          </td>
        </tr>       
        <tr class="thisThatHide">
          <th scope="row">
            <?php _e('Hide correct / incorrect message', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Hide correct / incorrect message', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label>
                <input type="checkbox" value="1"
                name="hideAnswerMessageBox" <?php echo $this->quiz->isHideAnswerMessageBox() ? 'checked="checked"' : '' ?>>
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="hideAnswerMessageBox" value="1" name="hideAnswerMessageBox" <?php echo $this->quiz->isHideAnswerMessageBox() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="hideAnswerMessageBox"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('Enable this option to hide the correct / incorrect messages from displaying under questions in results.',
                'wp-pro-quiz'); ?>
              </p>
              <div class="wpProQuiz_demoBox">
                <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
                <div class="demoPopup">
                  <img alt=""
                  src="<?php echo WPPROQUIZ_URL . '/img/hideAnswerMessageBox.jpg'; ?> ">
                </div>
              </div>
            </fieldset>
          </td>
        </tr>
        <tr class="thisThatHide">
          <th scope="row">
            <?php _e('Hide correct / incorrect highlighting', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Hide correct / incorrect highlighting', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label>
                <input type="checkbox" value="1" name="disabledAnswerMark" <?php echo $this->quiz->isDisabledAnswerMark() ? 'checked="checked"' : '' ?>>
                <?php _e('Deactivate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="disabledAnswerMark" value="1" name="disabledAnswerMark" <?php echo $this->quiz->isDisabledAnswerMark() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="disabledAnswerMark"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('Enable this option to hide the correct / incorrect answer highlighting from results.',
                'wp-pro-quiz'); ?>
              </p>
              <div class="wpProQuiz_demoBox">
                <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
                <div class="demoPopup">
                  <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/mark.jpg'; ?> ">
                </div>
              </div>
            </fieldset>
          </td>
        </tr>
        <tr class="thisThatHide">
          <th scope="row">
            <?php _e('Force user to answer each question', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Force user to answer each question', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label>
                <input type="checkbox" value="1" name="forcingQuestionSolve" <?php $this->checked($this->quiz->isForcingQuestionSolve()); ?>>
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="forcingQuestionSolve" value="1" name="forcingQuestionSolve" <?php echo $this->quiz->isForcingQuestionSolve() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="forcingQuestionSolve"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('If you enable this option, the user is forced to answer each question.',
                'wp-pro-quiz'); ?>
                
              </p>
            </fieldset>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e('Hide question position overview', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Hide question position overview', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label>
                <input type="checkbox" value="1"
                name="hideQuestionPositionOverview" <?php $this->checked($this->quiz->isHideQuestionPositionOverview()); ?>>
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="hideQuestionPositionOverview" value="1" name="hideQuestionPositionOverview" <?php $this->checked($this->quiz->isHideQuestionPositionOverview()); ?> >
                  <label class="checkBoxDragger" for="hideQuestionPositionOverview"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('If you enable this option, the question position overview is hidden.',
                'wp-pro-quiz'); ?>
              </p>
              <div class="wpProQuiz_demoBox">
                <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
                <div class="demoPopup">
                  <img alt=""
                  src="<?php echo WPPROQUIZ_URL . '/img/hideQuestionPositionOverview.jpg'; ?> ">
                </div>
              </div>
            </fieldset>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e('Hide question numbering', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Hide question numbering', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label>
                <input type="checkbox" value="1"
                name="hideQuestionNumbering" <?php $this->checked($this->quiz->isHideQuestionNumbering()); ?>>
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="hideQuestionNumbering" value="1" name="hideQuestionNumbering" <?php $this->checked($this->quiz->isHideQuestionNumbering()); ?>>
                  <label class="checkBoxDragger" for="hideQuestionNumbering"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('If you enable this option, the question numbering is hidden.',
                'wp-pro-quiz'); ?>
              </p>
              <div class="wpProQuiz_demoBox">
                <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
                <div class="demoPopup">
                  <img alt=""
                  src="<?php echo WPPROQUIZ_URL . '/img/hideQuestionNumbering.jpg'; ?> ">
                </div>
              </div>
            </fieldset>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?php
}
private function leaderboardOptions()
{
?>
<div class="postbox">
  <h3 class="hndle"><?php _e('Leaderboard', 'wp-pro-quiz'); ?> <?php _e('(optional)', 'wp-pro-quiz'); ?></h3>
  <div class="inside">
    <p>
      <?php _e('The leaderboard allows users to enter results in public list and to share the result this way.',
      'wp-pro-quiz'); ?>
    </p>
    <p>
      <?php _e('The leaderboard works independent from internal statistics function.', 'wp-pro-quiz'); ?>
    </p>
    <table class="form-table">
      <tbody id="toplistBox">
        <tr>
          <th scope="row">
            <?php _e('Leaderboard', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <!--
            <label>
              <input type="checkbox" name="toplistActivated"
              value="1" <?php echo $this->quiz->isToplistActivated() ? 'checked="checked"' : ''; ?>>
              <?php _e('Activate', 'wp-pro-quiz'); ?>
            </label>
            -->
            <span class="quizOptionSetting">
              <div class="checkboxToggleWrap">
                <input type="checkbox" id="toplistActivated" value="1" name="toplistActivated" <?php echo $this->quiz->isToplistActivated() ? 'checked="checked"' : ''; ?> >
                <label class="checkBoxDragger" for="toplistActivated"></label>
              </div>
            </span>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e('Who can sign up to the list', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <label>
              <input name="toplistDataAddPermissions" type="radio"
              value="1" <?php echo $this->quiz->getToplistDataAddPermissions() == 1 ? 'checked="checked"' : ''; ?>>
              <?php _e('all users', 'wp-pro-quiz'); ?>
            </label>
            <label>
              <input name="toplistDataAddPermissions" type="radio"
              value="2" <?php echo $this->quiz->getToplistDataAddPermissions() == 2 ? 'checked="checked"' : ''; ?>>
              <?php _e('registered useres only', 'wp-pro-quiz'); ?>
            </label>
            <label>
              <input name="toplistDataAddPermissions" type="radio"
              value="3" <?php echo $this->quiz->getToplistDataAddPermissions() == 3 ? 'checked="checked"' : ''; ?>>
              <?php _e('anonymous users only', 'wp-pro-quiz'); ?>
            </label>
            <p class="description">
              <?php _e('Not registered users have to enter name and e-mail (e-mail won\'t be displayed)',
              'wp-pro-quiz'); ?>
            </p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e('insert automatically', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <!--
            <label>
              <input name="toplistDataAddAutomatic" type="checkbox"
              value="1" <?php $this->checked($this->quiz->isToplistDataAddAutomatic()); ?>>
              <?php _e('Activate', 'wp-pro-quiz'); ?>
            </label>
            -->
            <span class="quizOptionSetting">
              <div class="checkboxToggleWrap">
                <input type="checkbox" id="toplistDataAddAutomatic" value="1" name="toplistDataAddAutomatic" <?php $this->checked($this->quiz->isToplistDataAddAutomatic()); ?> >
                <label class="checkBoxDragger" for="toplistDataAddAutomatic"></label>
              </div>
            </span>
            <p class="description">
              <?php _e('If you enable this option, logged in users will be automatically entered into leaderboard','wp-pro-quiz'); ?>
            </p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e('display captcha', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <!--
            <label>
              <input type="checkbox" name="toplistDataCaptcha"
              value="1" <?php echo $this->quiz->isToplistDataCaptcha() ? 'checked="checked"' : ''; ?> <?php echo $this->captchaIsInstalled ? '' : 'disabled="disabled"'; ?>>
              <?php _e('Activate', 'wp-pro-quiz'); ?>
            </label>
            -->
            <span class="quizOptionSetting">
              <div class="checkboxToggleWrap">
                <input type="checkbox" id="toplistDataCaptcha" value="1"
                name="toplistDataCaptcha" <?php echo $this->quiz->isToplistDataCaptcha() ? 'checked="checked"' : ''; ?> <?php echo $this->captchaIsInstalled ? '' : 'disabled="disabled"'; ?> >
                <label class="checkBoxDragger" for="toplistDataCaptcha"></label>
              </div>
            </span>
            <p class="description">
              <?php _e('If you enable this option, additional captcha will be displayed for users who are not registered.',
              'wp-pro-quiz'); ?>
            </p>
            <p class="description" style="color: red;">
              <?php _e('This option requires additional plugin:', 'wp-pro-quiz'); ?>
              <a href="http://wordpress.org/extend/plugins/really-simple-captcha/" target="_blank">Really
              Simple CAPTCHA</a>
            </p>
            <?php if ($this->captchaIsInstalled) { ?>
            <p class="description" style="color: green;">
              <?php _e('Plugin has been detected.', 'wp-pro-quiz'); ?>
            </p>
            <?php } else { ?>
            <p class="description" style="color: red;">
              <?php _e('Plugin is not installed.', 'wp-pro-quiz'); ?>
            </p>
            <?php } ?>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e('Sort list by', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <label>
              <input name="toplistDataSort" type="radio"
              value="1" <?php echo ($this->quiz->getToplistDataSort() == 1) ? 'checked="checked"' : ''; ?>>
              <?php _e('best user', 'wp-pro-quiz'); ?>
            </label>
            <label>
              <input name="toplistDataSort" type="radio"
              value="2" <?php echo ($this->quiz->getToplistDataSort() == 2) ? 'checked="checked"' : ''; ?>>
              <?php _e('newest entry', 'wp-pro-quiz'); ?>
            </label>
            <label>
              <input name="toplistDataSort" type="radio"
              value="3" <?php echo ($this->quiz->getToplistDataSort() == 3) ? 'checked="checked"' : ''; ?>>
              <?php _e('oldest entry', 'wp-pro-quiz'); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e('Users can apply multiple times', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <div>
              <!--
              <label>
                <input type="checkbox" name="toplistDataAddMultiple"
                value="1" <?php echo $this->quiz->isToplistDataAddMultiple() ? 'checked="checked"' : ''; ?>>
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="toplistDataAddMultiple" value="1"
                  name="toplistDataAddMultiple" <?php echo $this->quiz->isToplistDataAddMultiple() ? 'checked="checked"' : ''; ?> >
                  <label class="checkBoxDragger" for="toplistDataAddMultiple"></label>
                </div>
              </span>
            </div>
            <div id="toplistDataAddBlockBox" style="display: none;">
              <label>
                <?php _e('User can apply after:', 'wp-pro-quiz'); ?>
                <input type="number" min="0" class="small-text" name="toplistDataAddBlock"
                value="<?php echo $this->quiz->getToplistDataAddBlock(); ?>">
                <?php _e('minute', 'wp-pro-quiz'); ?>
              </label>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e('How many entries should be displayed', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <div>
              <label>
                <input type="number" min="0" class="small-text" name="toplistDataShowLimit"
                value="<?php echo $this->quiz->getToplistDataShowLimit(); ?>">
                <?php _e('Entries', 'wp-pro-quiz'); ?>
              </label>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <?php _e('Automatically display leaderboard in quiz result', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <div style="margin-top: 6px;">
              <?php _e('Where should leaderboard be displayed:', 'wp-pro-quiz'); ?>
              <label style="margin-right: 5px; margin-left: 5px;">
                <input type="radio" name="toplistDataShowIn"
                value="0" <?php echo ($this->quiz->getToplistDataShowIn() == 0) ? 'checked="checked"' : ''; ?>>
                <?php _e('don\'t display', 'wp-pro-quiz'); ?>
              </label>
              <label>
                <input type="radio" name="toplistDataShowIn"
                value="1" <?php echo ($this->quiz->getToplistDataShowIn() == 1) ? 'checked="checked"' : ''; ?>>
                <?php _e('below the "result text"', 'wp-pro-quiz'); ?>
              </label>
              <span class="wpProQuiz_demoBox" style="margin-right: 5px;">
                <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
                <div class="demoPopup">
                  <img alt=""
                  src="<?php echo WPPROQUIZ_URL . '/img/leaderboardInResultText.png'; ?> ">
                </div>
              </span>
              <label>
                <input type="radio" name="toplistDataShowIn"
                value="2" <?php echo ($this->quiz->getToplistDataShowIn() == 2) ? 'checked="checked"' : ''; ?>>
                <?php _e('in a button', 'wp-pro-quiz'); ?>
              </label>
              <span class="wpProQuiz_demoBox">
                <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
                <div class="demoPopup">
                  <img alt=""
                  src="<?php echo WPPROQUIZ_URL . '/img/leaderboardInButton.png'; ?> ">
                </div>
              </span>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?php
}
private function quizMode()
{
?>
<div class="postbox pcHide thisThatHide">
  <h3 class="hndle" id="pagination_longscroll"><?php _e('Pagination Vs. Long Scroll', 'wp-pro-quiz'); ?> <?php _e('(required)', 'wp-pro-quiz'); ?></h3>
  <div class="inside">
    <table style="width: 100%;" cellpadding="0" cellspacing="0" class="pagination-table">
        <tr>
          <td style="width: 25%; font-weight: bold;"><?php _e('Pagination', 'wp-pro-quiz'); ?></td>
          <td style="width: 25%; font-weight: bold;"><?php _e('Pagination with Back Button', 'wp-pro-quiz'); ?></td>
          <td style="width: 25%; font-weight: bold;"><?php _e('Pagination with Answers', 'wp-pro-quiz'); ?></td>
          <td style="width: 25%; font-weight: bold;"><?php _e('Long Scroll', 'wp-pro-quiz'); ?></th>
        </tr>
        <tr>
          <td><label><input type="radio" name="quizModus"
            value="0" <?php $this->checked($this->quiz->getQuizModus(),
            WpProQuiz_Model_Quiz::QUIZ_MODUS_NORMAL); ?>> <?php _e('Activate',
          'wp-pro-quiz'); ?></label></td>
          <td><label><input type="radio" name="quizModus"
            value="1" <?php $this->checked($this->quiz->getQuizModus(),
            WpProQuiz_Model_Quiz::QUIZ_MODUS_BACK_BUTTON); ?>> <?php _e('Activate',
          'wp-pro-quiz'); ?></label></td>
          <td><label><input type="radio" name="quizModus"
            value="2" <?php $this->checked($this->quiz->getQuizModus(),
            WpProQuiz_Model_Quiz::QUIZ_MODUS_CHECK); ?>> <?php _e('Activate', 'wp-pro-quiz'); ?>
          </label></td>
          <td><label><input type="radio" name="quizModus"
            value="3" <?php $this->checked($this->quiz->getQuizModus(),
            WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE); ?>> <?php _e('Activate',
          'wp-pro-quiz'); ?></label></td>
        </tr>
        <tr>
          <td>
            <?php _e('Paginate between questions sequentially',
            'wp-pro-quiz'); ?>
          </td>
          <td>
            <?php _e('Add a back button to return to prior question', 'wp-pro-quiz'); ?>
          </td>
          <td>
            <?php _e('Show users the correct answer after each question'); ?>
          </td>
          <td>
            <?php _e('List questions below each other on a single page'); ?>
          </td>
        </tr>
        <tr>
          <td>
            <div class="wpProQuiz_demoBox">
              <a href="javascript:void(0)"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
              <div class="demoPopup">
                <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/normal.jpg'; ?> ">
              </div>
            </div>
          </td>
          <td>
            <div class="wpProQuiz_demoBox">
              <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
              <div class="demoPopup">
                <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/backButton.jpg'; ?> ">
              </div>
            </div>
          </td>
          <td>
            <div class="wpProQuiz_demoBox" style="position: relative;">
              <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
              <div class="demoPopup">
                <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/checkCcontinue.jpg'; ?> ">
              </div>
            </div>
          </td>
          <td>
            <div class="wpProQuiz_demoBox" style="position: relative;">
              <a href="javascript:void(0);"><?php _e('Demo', 'wp-pro-quiz'); ?></a>
              <div class="demoPopup">
                <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/singlePage.jpg'; ?> ">
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td>
            <?php _e('Select the number of questions per page'); ?><br>
            <input type="number" name="questionsPerPage"
            value="<?php echo $this->quiz->getQuestionsPerPage(); ?>" min="0">
            <div class="description">
              <?php _e('(0 = All on one page)', 'wp-pro-quiz'); ?>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?php
}
private function form()
{
$forms = $this->forms;
$index = 0;
if (!count($forms)) {
$forms = array(new WpProQuiz_Model_Form(), new WpProQuiz_Model_Form());
} else {
array_unshift($forms, new WpProQuiz_Model_Form());
}
?>
<div class="postbox">
  <h3 class="hndle" id="custom_fields"><?php _e('Custom Fields', 'wp-pro-quiz'); ?></h3>
  <div class="inside">
    <table class="form-table">
      <tbody>
        <tr>
          <th scope="row">
            <?php _e('Enable Custom Fields ', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Custom fields enable', 'wp-pro-quiz'); ?></span>
              </legend>              
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="formActivated" value="1" name="formActivated" <?php $this->checked($this->quiz->isFormActivated()); ?> >
                  <label class="checkBoxDragger" for="formActivated"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('This option allows you to add custom fields, e.g. to request the name or e-mail of users', 'wp-pro-quiz'); ?>
              </p>
            </fieldset>
          </td>
        </tr>
        <tr class="pcHide">
          <th scope="row">
            <?php _e('Display Position', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Display Position', 'wp-pro-quiz'); ?></span>
              </legend>
              <?php // _e('Where should the fileds be displayed:', 'wp-pro-quiz'); ?>
              <label style="padding: 0 50px 0 0">
                <input type="radio"
                value="<?php echo WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START; ?>"
                name="formShowPosition" <?php $this->checked($this->quiz->getFormShowPosition(),
                WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_START); ?>>
                <?php _e('Before the quiz starts', 'wp-pro-quiz'); ?>
                <div style="display: inline-block;" class="wpProQuiz_demoBox">
                  <!-- <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> -->
                  <div
                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                    <img alt=""
                    src="<?php echo WPPROQUIZ_URL . '/img/customFieldsFront.jpg'; ?> ">
                  </div>
                </div>
              </label>
              <label>
                <input type="radio"
                value="<?php echo WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_END; ?>"
                name="formShowPosition" <?php $this->checked($this->quiz->getFormShowPosition(),
                WpProQuiz_Model_Quiz::QUIZ_FORM_POSITION_END); ?>  >
                <?php _e('At the end of the quiz (before the quiz results)', 'wp-pro-quiz'); ?>
                <div style="display: inline-block;" class="wpProQuiz_demoBox">
                  <!-- <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> -->
                  <div
                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                    <img alt=""
                    src="<?php echo WPPROQUIZ_URL . '/img/customFieldsEnd1.jpg'; ?> ">
                  </div>
                </div>
                <div style="display: inline-block;" class="wpProQuiz_demoBox">
                  <!-- <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> -->
                  <div
                    style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                    <img alt=""
                    src="<?php echo WPPROQUIZ_URL . '/img/customFieldsEnd2.jpg'; ?> ">
                  </div>
                </div>
              </label>
            </fieldset>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="fieldContainer">
      <table class="customFieldTable" id="form_table">
        <thead>
          <tr>
            <th>#ID</th>
            <th><?php _e('Field name', 'wp-pro-quiz'); ?></th>
            <th><?php _e('Type', 'wp-pro-quiz'); ?></th>
            <th class="checkboxContainer"><?php _e('Required?', 'wp-pro-quiz'); ?></th>
            <th class="checkboxContainer">
              <?php _e('Show in Statistics?', 'wp-pro-quiz'); ?>
              <div style="display: inline-block;" class="wpProQuiz_demoBox">
                <!-- <a href="#"><?php _e('Demo', 'wp-pro-quiz'); ?></a> -->
                <div style="z-index: 9999999; position: absolute; background-color: #E9E9E9; padding: 10px; box-shadow: 0px 0px 10px 4px rgb(44, 44, 44); display: none; ">
                  <img alt="" src="<?php echo WPPROQUIZ_URL . '/img/formStatisticOverview.jpg'; ?> ">
                </div>
              </div>
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($forms as $form) {
            if($form->getType() == WpProQuiz_Model_Form::FORM_TYPE_APPROVE) break;
            $checkType = $this->selectedArray($form->getType(), array(
              WpProQuiz_Model_Form::FORM_TYPE_TEXT,
              WpProQuiz_Model_Form::FORM_TYPE_TEXTAREA,
              WpProQuiz_Model_Form::FORM_TYPE_CHECKBOX,
              WpProQuiz_Model_Form::FORM_TYPE_SELECT,
              WpProQuiz_Model_Form::FORM_TYPE_RADIO,
              WpProQuiz_Model_Form::FORM_TYPE_NUMBER,
              WpProQuiz_Model_Form::FORM_TYPE_EMAIL,
              WpProQuiz_Model_Form::FORM_TYPE_YES_NO,
              WpProQuiz_Model_Form::FORM_TYPE_DATE,
              WpProQuiz_Model_Form::FORM_TYPE_FILE,
              WpProQuiz_Model_Form::FORM_TYPE_APPROVE
            ));
          ?>

          <tr <?php echo $index++ == 0 ? 'style="display: none;"' : '' ?>>
            <td>
              <?php echo $index - 2; ?>
            </td>
            <td>
              <input type="text" name="form[][fieldname]"
              value="<?php echo esc_attr($form->getFieldname()); ?>"
              class="regular-text formFieldName"/>
            </td>
            <td class="optionContainer">
              <select name="form[][type]" class="formFieldTypes">
                <option
                  value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_TEXT; ?>" <?php echo $checkType[0]; ?>><?php _e('Text',
                'wp-pro-quiz'); ?></option>
                <option
                  value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_TEXTAREA; ?>" <?php echo $checkType[1]; ?>><?php _e('Textarea',
                'wp-pro-quiz'); ?></option>
                <option
                  value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_CHECKBOX; ?>" <?php echo $checkType[2]; ?>><?php _e('Checkbox',
                'wp-pro-quiz'); ?></option>
                <option
                  value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_SELECT; ?>" <?php echo $checkType[3]; ?>><?php _e('Drop-Down menu',
                'wp-pro-quiz'); ?></option>
                <option
                  value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_RADIO; ?>" <?php echo $checkType[4]; ?>><?php _e('Radio',
                'wp-pro-quiz'); ?></option>
                <option
                  value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_NUMBER; ?>" <?php echo $checkType[5]; ?>><?php _e('Number',
                'wp-pro-quiz'); ?></option>
                <option
                  value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_EMAIL; ?>" <?php echo $checkType[6]; ?>><?php _e('Email',
                'wp-pro-quiz'); ?></option>
                <option
                  value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_YES_NO; ?>" <?php echo $checkType[7]; ?>><?php _e('Yes/No',
                'wp-pro-quiz'); ?></option>
                <option
                  value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_DATE; ?>" <?php echo $checkType[8]; ?>><?php _e('Date',
                'wp-pro-quiz'); ?></option>
                  <option value="<?php echo WpProQuiz_Model_Form::FORM_TYPE_FILE; ?>" <?php echo $checkType[9]; ?>><?php _e('Image','wp-pro-quiz'); ?></option>
              </select>
              <a href="#" class="editDropDown"><?php _e('Edit list', 'wp-pro-quiz'); ?></a>
              <div class="dropDownEditBox"
                style="position: absolute; border: 1px solid #AFAFAF; background: #EBEBEB; padding: 5px; bottom: 0;right: 0;box-shadow: 1px 1px 1px 1px #AFAFAF; display: none;">
                <h4><?php _e('One entry per line', 'wp-pro-quiz'); ?></h4>
                <div>
                  <textarea rows="5" cols="50"
                  name="form[][data]"><?php echo $form->getData() === null ? '' : esc_textarea(implode("\n",
                  $form->getData())); ?></textarea>
                </div>
                <input type="button" value="<?php _e('OK', 'wp-pro-quiz'); ?>"
                class="button-primary">
              </div>
            </td>
            <td class="checkboxContainer">
              <input type="checkbox" name="form[][required]" value="1" <?php $this->checked($form->isRequired()); ?>>
            </td>
            <td class="checkboxContainer">
              <input type="checkbox" name="form[][show_in_statistic]" value="1" <?php $this->checked($form->isShowInStatistic()); ?>>
            </td>
            <td>
              <input type="button" name="form_delete"
              value="<?php _e('Delete', 'wp-pro-quiz'); ?>" class="button-secondary grayBtns">
              <a class="form_move button-secondary grayBtns" href="#" style="cursor:move;"><?php _e('Move',
              'wp-pro-quiz'); ?></a>
              <input type="hidden" name="form[][form_id]"
              value="<?php echo $form->getFormId(); ?>">
              <input type="hidden" name="form[][form_delete]" value="0">
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <div style="margin-top: 10px;">
        <input type="button" name="form_add" id="form_add"
        value="<?php _e('Add field', 'wp-pro-quiz'); ?>" class="button-secondary">
      </div>
    </div>
    <div class="clearfix">&nbsp;</div>
    <table class="form-table quiz-description-table">
      <tbody>
      <tr>
          <th scope="row"><?php _e('Information Text', 'wp-pro-quiz'); ?></th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                  <span><?php _e('Information Text', 'wp-pro-quiz'); ?></span>
              </legend>
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="is_custom_form_header" value="1" name="is_custom_form_header" <?php $this->checked($this->quiz->isCustomFormHeader()); ?> >
                  <label class="checkBoxDragger" for="is_custom_form_header"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('Add text to appear before the custom fields, e.g. to explain how users&apos; information will be used','wp-pro-quiz'); ?>
              </p>
            </fieldset>
          </td>
      </tr>
      <tr>
        <td colspan="2">
        <div id="custom_form_header_editor">
          <?php
            wp_editor($this->quiz->getCustomFormHeader(), 'custom_form_header', array('textarea_rows' => ADMIN_TEXTAREA_ROWS));
          ?>
        </div>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</div>
<?php
}
private function footerLinks()
{
?>
<div class="postbox">
  <h3 class="hndle" id="footer_links"><?php _e('Footer Links', 'wp-pro-quiz'); ?></h3>
  <div class="inside">
    <?php
    $textarea_rows = array('textarea_rows' => ADMIN_TEXTAREA_ROWS);
    ?>
    <table class="form-table quiz-description-table">
      <tbody>
      <tr>
          <th scope="row"><?php _e('Privacy Policy', 'wp-pro-quiz'); ?></th>
          <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('Privacy Policy', 'wp-pro-quiz'); ?></span>
                </legend>
                <span class="quizOptionSetting">
                  <div class="checkboxToggleWrap">
                    <input type="checkbox" id="is_privacy_policy" value="1" name="is_privacy_policy" <?php $this->checked($this->quiz->isPrivacyPolicy()); ?> >
                    <label class="checkBoxDragger" for="is_privacy_policy"></label>
                  </div>
                </span>
              <p class="description">
                <?php _e('This text will be displayed in the footer of every page','wp-pro-quiz'); ?>
              </p>
            </fieldset>
          </td>
      </tr>
      <tr>
        <td colspan="3">
        <div id="privacy_policy_editor">
          <div style="margin-bottom: 5px;">
          <?php
            $privacy_policy = $this->quiz->getPrivacyPolicy();
            wp_editor($privacy_policy, 'privacy_policy', $textarea_rows);
          ?>
          </div>
          <div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
            <?php _e('Link title :', 'wp-pro-quiz'); ?>
            <input type="text" name="privacy_policy_text" class="" value="<?php echo $this->quiz->getPrivacyPolicyText() ?>">
            <div style="clear: right;"></div>
          </div>
        </div>
        </td>
      </tr>

      <tr>
          <th scope="row"><?php _e('Terms &amp; Conditions', 'wp-pro-quiz'); ?></th>
          <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('Terms &amp; Conditions', 'wp-pro-quiz'); ?></span>
                </legend>
                <span class="quizOptionSetting">
                  <div class="checkboxToggleWrap">
                    <input type="checkbox" id="is_terms_conditions" value="1" name="is_terms_conditions" <?php $this->checked($this->quiz->isTermsConditions()); ?> >
                    <label class="checkBoxDragger" for="is_terms_conditions"></label>
                  </div>
                </span>
              <p class="description">
                <?php _e('This text will be displayed in the footer of every page','wp-pro-quiz'); ?>
              </p>
            </fieldset>
          </td>
      </tr>
      <tr>
        <td colspan="3">
          <div id="terms_conditions_editor">
            <div style="margin-bottom: 5px;">
            <?php
              $terms_conditions = $this->quiz->getTermsConditions();
              wp_editor($terms_conditions, 'terms_conditions', $textarea_rows);
            ?>
            </div>
            <div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
              <?php _e('Link title :', 'wp-pro-quiz'); ?>
              <input type="text" name="terms_conditions_text" class="" value="<?php echo $this->quiz->getTermsConditionsText() ?>">
              <div style="clear: right;"></div>
            </div>
          </div>
        </td>
      </tr>

      <tr>
          <th scope="row"><?php _e('Contest Rules', 'wp-pro-quiz'); ?></th>
          <td>
            <fieldset>
                <legend class="screen-reader-text">
                    <span><?php _e('Contest Rules', 'wp-pro-quiz'); ?></span>
                </legend>
                <span class="quizOptionSetting">
                  <div class="checkboxToggleWrap">
                    <input type="checkbox" id="is_contest_rules" value="1" name="is_contest_rules" <?php $this->checked($this->quiz->isContestRules()); ?> >
                    <label class="checkBoxDragger" for="is_contest_rules"></label>
                  </div>
                </span>
              <p class="description">
                <?php _e('This text will be displayed in the footer of every page','wp-pro-quiz'); ?>
              </p>
            </fieldset>
          </td>
      </tr>
      <tr>
        <td colspan="3">
          <div id="contest_rules_editor">
            <div style="margin-bottom: 5px;">
            <?php
              $contest_rules = $this->quiz->getContestRules();
              wp_editor($contest_rules, 'contest_rules', $textarea_rows);
            ?>
            </div>
            <div style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
              <?php _e('Link title :', 'wp-pro-quiz'); ?>
              <input type="text" name="contest_rules_text" class="" value="<?php echo $this->quiz->getContestRulesText() ?>">
              <div style="clear: right;"></div>
            </div>
          </div>
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</div>
<?php
}
private function quizOptions()
{
?>
<div class="postbox">
  <h3 class="hndle" id="quiz_options"><?php _e('Quiz Options', 'wp-pro-quiz'); ?></h3>
  <div class="inside">
    <table class="form-table">
      <tbody>
        <tr class="hideTitle pcHide">
          <th scope="row">
            <?php _e('Hide quiz title', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Hide title', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label for="title_hidden">
                <input type="checkbox" id="title_hidden" value="1"
                name="titleHidden" <?php echo $this->quiz->isTitleHidden() ? 'checked="checked"' : '' ?> >
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <p class="description">
                <?php _e('Hide the Quiz Title from the heading of all pages.', 'wp-pro-quiz'); ?>
              </p>
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="title_hidden" value="1" name="titleHidden" <?php echo $this->quiz->isTitleHidden() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="title_hidden"></label>
                </div>
              </span>
            </fieldset>
          </td>
        </tr>
        <tr class="restartQuiz pcHide thisThatHide">
          <th scope="row">
            <?php _e('Hide "Restart Quiz" button', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Hide "Restart quiz" button', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label for="btn_restart_quiz_hidden">
                <input type="checkbox" id="btn_restart_quiz_hidden" value="1"
                name="btnRestartQuizHidden" <?php echo $this->quiz->isBtnRestartQuizHidden() ? 'checked="checked"' : '' ?> >
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <p class="description">
                <?php _e(' Hide the "Restart Quiz" button from the quiz results page.',
                'wp-pro-quiz'); ?>
              </p>
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="btn_restart_quiz_hidden" value="1" name="btnRestartQuizHidden" <?php echo $this->quiz->isBtnRestartQuizHidden() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="btn_restart_quiz_hidden"></label>
                </div>
              </span>
            </fieldset>
          </td>
        </tr>
        <tr class="hideViewQuestions pcHide thisThatHide">
          <th scope="row">
            <?php _e('Hide "View Questions" button', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Hide "View question" button', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label for="btn_view_question_hidden">
                <input type="checkbox" id="btn_view_question_hidden" value="1"
                name="btnViewQuestionHidden" <?php echo $this->quiz->isBtnViewQuestionHidden() ? 'checked="checked"' : '' ?> >
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <p class="description">
                <?php _e('Hide the "View Questions" button from the quiz results page',
                'wp-pro-quiz'); ?>
              </p>
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="btn_view_question_hidden" value="1" name="btnViewQuestionHidden" <?php echo $this->quiz->isBtnViewQuestionHidden() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="btn_view_question_hidden"></label>
                </div>
              </span>
            </fieldset>
          </td>
        </tr>
        <tr class="displayQuestionsRand pcHide thisThatHide">
          <th scope="row">
            <?php _e('Display Questions randomly', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Display Question randomly', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label for="question_random">
                <input type="checkbox" id="question_random" value="1"
                name="questionRandom" <?php echo $this->quiz->isQuestionRandom() ? 'checked="checked"' : '' ?> >
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="question_random" value="1" name="questionRandom" <?php echo $this->quiz->isQuestionRandom() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="question_random"></label>
                </div>
              </span>
            </fieldset>
          </td>
        </tr>
        <tr class="displayAnsRand pcHide thisThatHide">
          <th scope="row">
            <?php _e('Display answers randomly', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Display answers randomly', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label for="answer_random">
                <input type="checkbox" id="answer_random" value="1"
                name="answerRandom" <?php echo $this->quiz->isAnswerRandom() ? 'checked="checked"' : '' ?> >
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="answer_random" value="1" name="answerRandom" <?php echo $this->quiz->isAnswerRandom() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="answer_random"></label>
                </div>
              </span>
            </fieldset>
          </td>
        </tr>        
        </tr>
        <tr class="timeLimit pcHide">
          <th scope="row">
            <?php _e('Time limit', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Time limit', 'wp-pro-quiz'); ?></span>
              </legend>
              <span class="quizOptionSetting">
                <label for="time_limit">
                  <input type="number" min="0" class="small-text" id="time_limit"
                  value="<?php echo $this->quiz->getTimeLimit(); ?>"
                  name="timeLimit"> <?php _e('Seconds', 'wp-pro-quiz'); ?>
                </label>
                <p class="description">
                  <?php _e('0 = no limit', 'wp-pro-quiz'); ?>
                </p>
              </span>
            </fieldset>
          </td>
        </tr>
        <tr class="statisticsEnable">
          <th scope="row">
            <?php _e('Statistics', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Statistics', 'wp-pro-quiz'); ?></span>
              </legend>
              <!--
              <label for="statistics_on">
                <input type="checkbox" id="statistics_on" value="1"
                name="statisticsOn" <?php echo $this->quiz->isStatisticsOn() ? 'checked="checked"' : ''; ?>>
                <?php _e('Activate', 'wp-pro-quiz'); ?>
              </label>
              -->
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="statistics_on" value="1" name="statisticsOn" <?php echo $this->quiz->isStatisticsOn() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="statistics_on"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('Save statistics for all completed quizzes. Return to the Quiz Overview page and hover over the quiz name to view.','wp-pro-quiz'); ?>
              </p>              
            </fieldset>
          </td>
        </tr>
        <tr id="statistics_ip_lock_tr" style="display: none;" class="statisticsIpBlock">
          <th scope="row">
            <?php _e('Statistics IP-lock', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Statistics IP-lock', 'wp-pro-quiz'); ?></span>
              </legend>
              <label for="statistics_ip_lock">
                <input type="number" min="0" class="small-text" id="statistics_ip_lock"
                value="<?php echo ($this->quiz->getStatisticsIpLock() === null) ? 1440 : $this->quiz->getStatisticsIpLock(); ?>"
                name="statisticsIpLock">
                <?php _e('minutes (recommended 1440 minutes = 1 day)',
                'wp-pro-quiz'); ?>
              </label>
              <p class="description">
                <?php _e('Protect the statistics from spam. Results from the same IP address will only be saved every X minutes. (0 = deactivated)',
                'wp-pro-quiz'); ?>
              </p>
            </fieldset>
          </td>
        </tr>
        <tr class="execQuizOnce">
          <th scope="row">
            <?php _e('Execute quiz only once', 'wp-pro-quiz'); ?>
          </th>
          <td>
            <fieldset>
              <legend class="screen-reader-text">
                <span><?php _e('Execute quiz only once', 'wp-pro-quiz'); ?></span>
              </legend>              
              <span class="quizOptionSetting">
                <div class="checkboxToggleWrap">
                  <input type="checkbox" id="quizRunOnce" value="1" name="quizRunOnce" <?php echo $this->quiz->isQuizRunOnce() ? 'checked="checked"' : '' ?> >
                  <label class="checkBoxDragger" for="quizRunOnce"></label>
                </div>
              </span>
              <p class="description">
                <?php _e('Restrict users to a single quiz submission, and block subsequent attempts.',
                'wp-pro-quiz'); ?>
              </p>              
              <div id="wpProQuiz_quiz_run_once_type" style="margin-bottom: 5px; display: none;">
                <?php _e('This option applies to:', 'wp-pro-quiz');
                $quizRunOnceType = $this->quiz->getQuizRunOnceType();
                $quizRunOnceType = ($quizRunOnceType == 0) ? 1 : $quizRunOnceType;
                ?>
                <label>
                  <input name="quizRunOnceType" type="radio"
                  value="1" <?php echo ($quizRunOnceType == 1) ? 'checked="checked"' : ''; ?>>
                  <?php _e('all users', 'wp-pro-quiz'); ?>
                </label>
                <label>
                  <input name="quizRunOnceType" type="radio"
                  value="2" <?php echo ($quizRunOnceType == 2) ? 'checked="checked"' : ''; ?>>
                  <?php _e('registered useres only', 'wp-pro-quiz'); ?>
                </label>
                <label>
                  <input name="quizRunOnceType" type="radio"
                  value="3" <?php echo ($quizRunOnceType == 3) ? 'checked="checked"' : ''; ?>>
                  <?php _e('anonymous users only', 'wp-pro-quiz'); ?>
                </label>
                <div id="wpProQuiz_quiz_run_once_cookie" style="margin-top: 10px;">
                  <label>
                    <input type="checkbox" value="1"
                    name="quizRunOnceCookie" <?php echo $this->quiz->isQuizRunOnceCookie() ? 'checked="checked"' : '' ?>>
                    <?php _e('user identification by cookie', 'wp-pro-quiz'); ?>
                  </label>
                  <p class="description">
                    <?php _e('If you activate this option, a cookie is set additionally for unregistrated (anonymous) users. This ensures a longer assignment of the user than the simple assignment by the IP address.',
                    'wp-pro-quiz'); ?>
                  </p>
                </div>
                <div style="margin-top: 15px;">
                  <input class="button-secondary" type="button" name="resetQuizLock"
                  value="<?php _e('Reset the user identification',
                  'wp-pro-quiz'); ?>">
                  <span id="resetLockMsg"
                  style="display:none; background-color: rgb(255, 255, 173); border: 1px solid rgb(143, 143, 143); padding: 4px; margin-left: 5px; "><?php _e('User identification has been reset.'); ?></span>
                  <p class="description">
                    <?php _e('Resets user identification for all users.',
                    'wp-pro-quiz'); ?>
                  </p>
                </div>
              </div>
            </fieldset>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?php
}
private function photoContestDetailsBlock()
{
?>
<div class="postbox" id="photoContestDetailsBlock" style="display: none;">
  <h3 class="hndle" id="photo_contest_details_block"><?php _e('Contest Description', 'wp-pro-quiz'); ?> </h3>
  <div class="inside">
    <table class="form-table quiz-description-table">
      <tbody>               
        <tr>
          <td>
            <?php wp_editor($this->quiz->getPcDescription(), "pc_description"); ?>
          </td>  
        </tr>        
      </tbody>
    </table>           
  </div>
</div>
<?php
}
private function resultTextOption()
{
?>
<div class="postbox">
  <h3 class="hndle" id="quiz_results_page"><?php _e('Quiz Results Page', 'wp-pro-quiz'); ?></h3>
  <div class="inside">
    <p class="description pcHide">
      <?php _e('Any images or text you include in the box below will be displayed at the end of the quiz with the user’s score / results.',
      'wp-pro-quiz'); ?>
    </p>
    <p class="description pcHeaderContent" style="display: none;">
      <?php _e('Any images or text you include in the box below will be displayed to users at the end of the contest.',
      'wp-pro-quiz'); ?>
    </p>
    <div style="padding-top: 10px; padding-bottom: 10px;">
      <table class="form-table quiz-description-table quizResultGrade">
        <tbody>
          <tr>
            <th scope="row"><?php _e('Enable custom results pages', 'wp-pro-quiz'); ?></th>
            <td>
              <fieldset>
                <p class="description">
                  <?php _e('Create custom results pages, and determine the score (%) in which each should be shown', 'wp-pro-quiz'); ?>
                </p>
                <span class="quizOptionSetting">
                  <div class="checkboxToggleWrap">
                    <input type="checkbox" id="wpProQuiz_resultGradeEnabled" value="1" name="resultGradeEnabled" <?php echo $this->quiz->isResultGradeEnabled() ? 'checked="checked"' : ''; ?> >
                    <label class="checkBoxDragger" for="wpProQuiz_resultGradeEnabled"></label>
                  </div>
                </span>
              </fieldset>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div style="display: none;" id="resultGrade">
      <div>
        <strong><?php _e('Please Note:', 'wp-pro-quiz'); ?></strong>
        <ul style="list-style-type: none; padding: 5px; margin-left: 0; margin-top: 0;">
          <li><i><?php _e('A user’s total score (%) will determine which results page is shown to them after completing the quiz. <br>Any text or images you include on a results page will be added to social posts for sharing.', 'wp-pro-quiz'); ?></i></li>
        </ul>
      </div>
      <div>
        <ul id="resultList">
          <?php
          $resultText = $this->quiz->getResultText();
          for ($i = 0; $i < 15; $i++) {
          if ($this->quiz->isResultGradeEnabled() && isset($resultText['text'][$i])) {
          ?>
          <li style="padding: 5px; border: 1px dotted;">
            <div
              style="margin-bottom: 5px;"><?php wp_editor($resultText['text'][$i],
              'resultText_' . $i, array(
              'textarea_rows' => 3,
              'textarea_name' => 'resultTextGrade[text][]'
            )); ?></div>
            <div
              style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
              <?php _e('from:', 'wp-pro-quiz'); ?> <input type="text"
              name="resultTextGrade[prozent][]"
              class="small-text"
              value="<?php echo $resultText['prozent'][$i] ?>"> <?php _e('%',
              'wp-pro-quiz'); ?> <?php printf(__('(Will be displayed, when user scores >= <span class="resultProzent">%s</span>%%)',
              'wp-pro-quiz'), $resultText['prozent'][$i]); ?>
              <input type="button" style="float: right;"
              class="button-primary deleteResult"
              value="<?php _e('Delete page', 'wp-pro-quiz'); ?>">
              <div style="clear: right;"></div>
              <input type="hidden" value="1" name="resultTextGrade[activ][]">
            </div>
          </li>
          <?php } else { ?>
          <li style="padding: 5px; border: 1px dotted; <?php echo $i ? 'display:none;' : '' ?>">
            <div style="margin-bottom: 5px;"><?php wp_editor('',
              'resultText_' . $i, array(
              'textarea_rows' => 3,
              'textarea_name' => 'resultTextGrade[text][]'
            )); ?></div>
            <div
              style="margin-bottom: 5px;background-color: rgb(207, 207, 207);padding: 10px;">
              <?php _e('from:', 'wp-pro-quiz'); ?> <input type="text"
              name="resultTextGrade[prozent][]"
              class="small-text"
              value="0"> <?php _e('%',
              'wp-pro-quiz'); ?> <?php printf(__('(<i>This page will be displayed when a user scores</i> >= <span class="resultProzent">%s</span><span class="boldPercent">%%</span>)',
              'wp-pro-quiz'), '0'); ?>
              <input type="button" style="float: right;"
              class="button-primary deleteResult"
              value="<?php _e('Delete Page', 'wp-pro-quiz'); ?>">
              <div style="clear: right;"></div>
              <input type="hidden" value="<?php echo $i ? '0' : '1' ?>"
              name="resultTextGrade[activ][]">
            </div>
          </li>
          <?php }
          } ?>
        </ul>
        <input type="button" class="button-primary addResult"
        value="<?php _e('Add Page', 'wp-pro-quiz'); ?>">
      </div>
    </div>
    <div id="resultNormal">
      <?php
      $resultText = is_array($resultText) ? '' : $resultText;
      wp_editor($resultText, 'resultText', array('textarea_rows' => 10));
      ?>
    </div>
    <h4><?php _e('Custom fields - Variables', 'wp-pro-quiz'); ?></h4>
    <ul class="formVariables"></ul>
</div>
</div>
<?php
}
private function adminEmailOption()
{
/** @var WpProQuiz_Model_Email * */
$email = $this->quiz->getAdminEmail();
if($this->checkPhotoOrGiveAwayQuiz()) {
    $email = WpProQuiz_Model_Email::getDefaultPC(true); //fetch admin template for contest
} else {
    $email = $email === null ? WpProQuiz_Model_Email::getDefault(true) : $email; //fetch admin template for quiz
}
?>
<div class="postbox" id="adminEmailSettings">
<h3 class="hndle" id="admin_email_settings"><?php _e('Admin Email Settings', 'wp-pro-quiz'); ?></h3>
<div class="inside">
  <table class="form-table">
    <tbody>
      <tr>
        <th scope="row">
          <?php _e('Admin E-Mail Notification', 'wp-pro-quiz'); ?>
        </th>
        <td>
          <fieldset>
            <legend class="screen-reader-text">
              <span><?php _e('Admin E-Mail Notification', 'wp-pro-quiz'); ?></span>
            </legend>
            <label>
              <input type="radio" name="emailNotification"
              value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
              WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_NONE); ?>>
              <?php _e('Deactivate', 'wp-pro-quiz'); ?>
            </label>
            <label>
              <input type="radio" name="emailNotification"
              value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
              WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_REG_USER); ?>>
              <?php _e('for registered users only', 'wp-pro-quiz'); ?>
            </label>
            <label>
              <input type="radio" name="emailNotification"
              value="<?php echo WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL; ?>" <?php $this->checked($this->quiz->getEmailNotification(),
              WpProQuiz_Model_Quiz::QUIZ_EMAIL_NOTE_ALL); ?>>
              <?php _e('for all users', 'wp-pro-quiz'); ?>
            </label>
            <p class="description">
              <?php _e('If you enable this option, you will be informed if a user completes this quiz.',
              'wp-pro-quiz'); ?>
            </p>
          </fieldset>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php _e('To:', 'wp-pro-quiz'); ?>
        </th>
        <td>
          <label>
            <input type="text" name="adminEmail[to]" value="<?php echo $email->getTo(); ?>"
            class="regular-text">
          </label>
          <p class="description">
            <?php _e('Separate multiple email addresses with a comma, e.g. wp@test.com, test@test.com',
            'wp-pro-quiz'); ?>
          </p>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php _e('From:', 'wp-pro-quiz'); ?>
        </th>
        <td>
          <label>
            <input type="text" name="adminEmail[from]" value="<?php echo $email->getFrom(); ?>"
            class="regular-text">
          </label>
          <!--                                <p class="description"> -->
          <?php //_e('Server-Adresse empfohlen, z.B. info@YOUR-PAGE.com', 'wp-pro-quiz');
          ?>
          <!--                                </p> -->
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php _e('Subject:', 'wp-pro-quiz'); ?>
        </th>
        <td>
          <label>
            <input type="text" name="adminEmail[subject]"
            value="<?php echo $email->getSubject(); ?>" class="regular-text adminEmailSubject">
          </label>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php _e('HTML', 'wp-pro-quiz'); ?>
        </th>
        <td>
          <label>
            <input type="checkbox" name="adminEmail[html]"
            value="1" <?php $this->checked($email->isHtml()); ?>> <?php _e('Activate',
            'wp-pro-quiz'); ?>
          </label>
        </td>
      </tr>
      <tr>
        <th scope="row">
          <?php _e('Message body:', 'wp-pro-quiz'); ?>
        </th>
        <td>
          <?php
          wp_editor($email->getMessage(), 'adminEmailEditor',
          array('textarea_rows' => 20, 'textarea_name' => 'adminEmail[message]'));
          ?>
          <div style="padding-top: 10px;">
            <table style="width: 100%;">
              <thead>
                <tr>
                  <th style="padding: 0;">
                    <?php _e('Allowed variables', 'wp-pro-quiz'); ?>
                  </th>
                  <th style="padding: 0;">
                    <?php _e('Custom fields - Variables', 'wp-pro-quiz'); ?>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td style="vertical-align: top;">
                    <ul>
                      <li><span>$userId</span> - <?php _e('User-ID', 'wp-pro-quiz'); ?></li>
                      <li><span>$username</span> - <?php _e('Username', 'wp-pro-quiz'); ?>
                    </li>
                    <li><span>$quizname</span> - <?php _e('Quiz-Name', 'wp-pro-quiz'); ?>
                  </li>
                  <li><span>$result</span> - <?php _e('Result in precent',
                'wp-pro-quiz'); ?></li>
                <li><span>$points</span> - <?php _e('Reached points', 'wp-pro-quiz'); ?>
              </li>
              <li><span>$ip</span> - <?php _e('IP-address of the user',
            'wp-pro-quiz'); ?></li>
            <li><span>$categories</span> - <?php _e('Category-Overview',
          'wp-pro-quiz'); ?></li>
        </ul>
      </td>
      <td style="vertical-align: top;">
      <ul class="formVariables"></ul>
    </td>
  </tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<?php
}
private function userEmailOption()
{
/** @var WpProQuiz_Model_Email * */
$email = $this->quiz->getUserEmail();
if($this->checkPhotoOrGiveAwayQuiz()) {
    $email = WpProQuiz_Model_Email::getDefaultPC(false); //fetch user template for contest
} else {
    $email = $email === null ? WpProQuiz_Model_Email::getDefault(false) : $email; // fetch user template for quiz
}
$to = $email->getTo();
?>
<div class="postbox" id="userEmailSettings">
<h3 class="hndle" id="user_email_settings"><?php _e('User Email Settings', 'wp-pro-quiz'); ?></h3>
<div class="inside">
<table class="form-table">
<tbody>
<tr>
<th scope="row">
<?php _e('Enable Email Notifications', 'wp-pro-quiz'); ?>
</th>
<td>
<fieldset>
<legend class="screen-reader-text">
<span><?php _e('User e-mail notification', 'wp-pro-quiz'); ?></span>
</legend>
<!--
<label>
<input type="checkbox" name="userEmailNotification"
value="1" <?php $this->checked($this->quiz->isUserEmailNotification()); ?>>
<?php _e('Activate', 'wp-pro-quiz'); ?>
</label>
-->
<span class="quizOptionSetting">
<div class="checkboxToggleWrap">
  <input type="checkbox" id="userEmailNotification" value="1" name="userEmailNotification" <?php $this->checked($this->quiz->isUserEmailNotification()); ?> >
  <label class="checkBoxDragger" for="userEmailNotification"></label>
</div>
</span>
<p class="description">
<?php _e('If you enable this option, users will be sent an email with their quiz results.',
'wp-pro-quiz'); ?>
</p>
</fieldset>
</td>
</tr>
<tr>
<th scope="row">
<?php _e('To:', 'wp-pro-quiz'); ?>
</th>
<td>
<label>
<input type="checkbox" name="userEmail[toUser]"
value="1" <?php $this->checked($email->isToUser()); ?>>
<?php _e('User Email-Address (only registered users)', 'wp-pro-quiz'); ?>
</label><br>
<label>
<input type="checkbox" name="userEmail[toForm]"
value="1" <?php $this->checked($email->isToForm()); ?>>
<?php _e('Custom fields', 'wp-pro-quiz'); ?> :
<select name="userEmail[to]" class="emailFormVariables"
data-default="<?php echo empty($to) && $to != 0 ? -1 : $email->getTo(); ?>"></select>
<?php _e('(Type Email)', 'wp-pro-quiz'); ?>
</label>
</td>
</tr>
<tr>
<th scope="row">
<?php _e('From:', 'wp-pro-quiz'); ?>
</th>
<td>
<label>
<input type="text" name="userEmail[from]" value="<?php echo $email->getFrom(); ?>"
class="regular-text">
</label>
</td>
</tr>
<tr>
<th scope="row">
<?php _e('Subject:', 'wp-pro-quiz'); ?>
</th>
<td>
<label>
<input type="text" name="userEmail[subject]" value="<?php echo $email->getSubject(); ?>" class="regular-text userEmailSubject">
<!-- <input type="text" name="userEmail[subject]" value="Your quiz results from Adtaxi Quiz Pro" class="regular-text"> -->
</label>
</td>
</tr>
<tr>
<th scope="row">
<?php _e('HTML', 'wp-pro-quiz'); ?>
</th>
<td>
<label>
<input type="checkbox" name="userEmail[html]"
value="1" <?php $this->checked($email->isHtml()); ?>> <?php _e('Activate',
'wp-pro-quiz'); ?>
</label>
</td>
</tr>
<tr>
<th scope="row">
<?php _e('Message body:', 'wp-pro-quiz'); ?>
</th>
<td>
<?php
wp_editor($email->getMessage(), 'userEmailEditor',
array('textarea_rows' => 20, 'textarea_name' => 'userEmail[message]'));
?>
<div style="padding-top: 10px;">
<table style="width: 100%;">
<thead>
  <tr>
    <th style="padding: 0;">
      <?php _e('Allowed variables', 'wp-pro-quiz'); ?>
    </th>
    <th style="padding: 0;">
      <?php _e('Custom fields - Variables', 'wp-pro-quiz'); ?>
    </th>
  </tr>
</thead>
<tbody>
  <tr>
    <td style="vertical-align: top;">
      <ul>
        <li><span>$userId</span> - <?php _e('User-ID', 'wp-pro-quiz'); ?></li>
        <li><span>$username</span> - <?php _e('Username', 'wp-pro-quiz'); ?>
      </li>
      <li><span>$quizname</span> - <?php _e('Quiz-Name', 'wp-pro-quiz'); ?>
    </li>
    <li><span>$result</span> - <?php _e('Result in precent',
  'wp-pro-quiz'); ?></li>
  <li><span>$points</span> - <?php _e('Reached points', 'wp-pro-quiz'); ?>
</li>
<li><span>$ip</span> - <?php _e('IP-address of the user',
'wp-pro-quiz'); ?></li>
<li><span>$categories</span> - <?php _e('Category-Overview',
'wp-pro-quiz'); ?></li>
</ul>
</td>
<td style="vertical-align: top;">
<ul class="formVariables"></ul>
</td>
</tr>
</tbody>
</table>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.inside').hide();
        jQuery('.postbox .hndle').click(function(){
            jQuery(this).toggleClass('expanded')
            jQuery(this).next('.inside').stop().slideToggle();
            jQuery(this).parents('.postbox').siblings().find('.hndle').removeClass('expanded');
            jQuery(this).parents('.postbox').siblings().find('.inside').slideUp();

            var accordionId = jQuery(this).attr('id'); //Get the accordionId which will use to open accordion on page load
            localStorage.setItem('accordionId', accordionId); //Set accordionId to localstorage
            return false;
        });
        jQuery(".tabs").hide();
        jQuery(".tabs:first").show();
        jQuery(".tabMenu li a").click(function () {
            var tablink = jQuery(this).attr('href');
            jQuery(".tabs").hide();
            jQuery(tablink).show();
            jQuery(this).parents(".tabMenu").find('li').removeClass("active");
            jQuery(this).parents(".tabMenu li").addClass("active");
            localStorage.setItem('previousSelectedTab', tablink); //Set tabname to localstorage
            return false;
        });

        /*Get tab name from localstorage and add active class to enable it */
        var previousSelectedTab = localStorage.getItem('previousSelectedTab');
        if(previousSelectedTab != ''){
            var tab = previousSelectedTab;
            var tab = tab.replace("#", "");
            var tab_this = '.tabMenu .'+tab+' a';

            jQuery(".tabs").hide();
            jQuery(previousSelectedTab).show();
            jQuery(tab_this).parents(".tabMenu").find('li').removeClass("active");
            jQuery(tab_this).parents(".tabMenu li").addClass("active");
        }
    });
</script>
<?php
}
}
