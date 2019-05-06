jQuery(document).ready(function ($) {
    /**
     * @memberOf $.fn
     */
    var defaultTimeOut = 5000;
    
    jQuery('.wpProQuiz_demoBox a').click(function() {
        jQuery(this).next('div').dialog({
            modal: true,
            autoOpen: true,
            height: 500,
            width: 630,
            resizable: false,
            draggable: false,
            open: function () {
                jQuery('.ui-button-icon').addClass("fa fa-times");
            },
            close: function(){
                jQuery(this).dialog("destroy");
            }
        });
        return false;
   });

    // Extend jQuery Dialog widget.
    $.widget("ui.dialog", $.ui.dialog, {
        open: function () {
            var me = this;
            $(document).on('click', function (e) {
                if ($(e.target).closest($(me.uiDialog)).length === 0) {
                    // Dialog close function
                    me.close();
                }
            });
            // Invoke parent open method
            this._super();
        },
        close: function () {
            // Remove click handler for the current .ui-widget-overlay
            $(document).off("click");
            // Invoke parent close method
            this._super();
        }
    });
        
   jQuery('.showAccordion').each(function () {
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
   });

   jQuery(function() {
        jQuery('.detailWrappr textarea').on('keyup paste', function() {
            var textAreaHeight = jQuery('.detailWrappr textarea'),
            offset = textAreaHeight.innerHeight() - textAreaHeight.height();

            if (textAreaHeight.innerHeight < this.scrollHeight) {
                //Grow the field if scroll height is smaller
                textAreaHeight.height(this.scrollHeight - offset);
            } else {
                //Shrink the field and then re-set it to the scroll height in case it needs to shrink
                textAreaHeight.height(1);
                textAreaHeight.height(this.scrollHeight - offset);
            }
        });
    });

    $.fn.wpProQuiz_preview = function () {
        var methods = {
            openPreview: function (obj) {
                window.open($(obj).attr('href'), 'wpProQuizPreview', 'width=900,height=900');
            }
        };

        var init = function () {
            $('.wpProQuiz_prview').click(function (e) {
                methods.openPreview(this);
                e.preventDefault();
            });
        };

        init();
    };

    $('.social-share-chosen').chosen();
    $('.chosen-container,.chosen-choices .default').css('width','385px');
    $(".updated, .error").delay(defaultTimeOut).fadeOut(); // Hide admin UI notifications in 5 seconds

    $.fn.wpProQuiz_quizOverall = function () {};

    $.fn.wpProQuiz_quizEdit = function () {

        function ajaxPost(func, data, success) {
            var d = {
                action: 'wp_pro_quiz_admin_ajax',
                func: func,
                data: data
            };

            $.post(ajaxurl, d, success, 'json');
        };

        var methode = {
            addCategory: function () {
                var name = $.trim($('input[name="categoryAdd"]').val());

                if (isEmpty(name)) {
                    return;
                }

                var data = {
                    categoryName: name,
                    type: 'quiz'
                };

                ajaxPost('categoryAdd', data, function (json) {
                    if (json.err) {
                        $('#categoryMsgBox').text(json.err).show('fast').delay(2000).hide('fast');
                        return;
                    }

                    var $option = $(document.createElement('option'))
                        .val(json.categoryId)
                        .text(json.categoryName)
                        .attr('selected', 'selected');

                    $('select[name="category"]').append($option).change();

                });
            },

            addResult: function () {
                $('#resultList').children().each(function () {
                    if ($(this).css('display') == 'none') {
                        //TODO rework
                        var $this = $(this);
                        var $text = $this.find('textarea[name="resultTextGrade[text][]"]');
                        var id = $text.attr('id');
                        var hidden = true;

                        $this.find('input[name="resultTextGrade[prozent][]"]').val('0');
                        $this.find('input[name="resultTextGrade[activ][]"]').val('1').keyup();

                        if (tinymce.editors[id] != undefined && !tinymce.editors[id].isHidden()) {
                            hidden = false;
                        }

                        if (switchEditors != undefined && !hidden) {
                            switchEditors.go(id, 'toggle');
                            switchEditors.go(id, 'toggle');
                        }

                        if (tinymce.editors[id] != undefined) {
                            tinymce.editors[id].setContent('');
                        } else {
                            $text.val('');
                        }

                        if (tinymce.editors[id] != undefined && !hidden) {
                            tinyMCE.execCommand('mceRemoveControl', false, id);
                        }

                        // $this.parent().children(':visible').last().after($this);
                        // Commented above code because of visual mode wasn't enable after adding page

                        if (tinymce.editors[id] != undefined && !hidden) {
                            tinyMCE.execCommand('mceAddControl', false, id);
                        }

                        $(this).show();

                        if (switchEditors != undefined && !hidden) {
                            switchEditors.go(id, 'toggle');
                        }

                        return false;
                    }
                });
            },

            deleteResult: function (e) {
                $(e).parent().parent().hide();
                $(e).siblings('input[name="resultTextGrade[activ][]"]').val('0');
            },

            deleteStyleSheet: function (e,data) {
                var ajax_data = {
                    current_stylesheet: data['current_stylesheet'],
                    applied_stylesheet: data['applied_stylesheet'],
                    quiz_id: data['quiz_id'],
                };

                var current_stylesheet=data['current_stylesheet'];
                var applied_stylesheet=data['applied_stylesheet'];

                var d = {
                    action: 'wp_pro_quiz_admin_ajax',
                    func: 'deleteStyleSheet',
                    data: ajax_data
                };

                $.post(ajaxurl, d, function (json) {
                    if(json['delete_file_status']){
                        //if success then alert and display values on dropdown and custom box
                        alert(wpProQuizLocalize.delete_success_stylesheet_msg);
                    }else{
                        //if error
                        alert(wpProQuizLocalize.delete_error_stylesheet_msg);
                        return false;
                    }
                    $(".quizStyle option[value='"+current_stylesheet+"']").remove();
                    $(".quizStyle").val(json['strQuizStyleName']);
                    $(".custom_quiz_box").val(json['strQuizCustomQuizBox']);
                }
                , 'json');
            },

            changeResult: function (e) {
                var $this = $(e);

                if (methode.validResultInput($this.val())) {
                    $this.siblings('.resultProzent').text($this.val());
                    $this.removeAttr('style');
                    return true;
                }

                $this.css('background-color', '#FF9696');

                return false;
            },

            validResultInput: function (input) {

                if (isEmpty(input))
                    return false;

                input = input.replace(/\,/, '.');

                if (!isNaN(input) && Number(input) <= 100 && Number(input) >= 0) {
                    if (input.match(/\./) != null)
                        return input.split('.')[1].length < 3;

                    return true;
                }

                return false;
            },

            toggleDisplay: function (th,id) {
                th.checked ? $('#'+id).show() : $('#'+id).hide();
            },

            validInput: function () {
                if (isEmpty($('#wpProQuiz_title').val())) {
                    alert(wpProQuizLocalize.no_title_msg);
                    return false;
                }

                var text = '';

                if (tinymce.editors.text != undefined && !tinymce.editors.text.isHidden()) {
                    text = tinymce.editors.text.getContent();
                } else {
                    text = $('textarea[name="text"]').val();
                }

                if (isEmpty(text)) {
                    if(!$('#autostart').is(':checked')){ //if autostart is not checked then description is required
                        alert(wpProQuizLocalize.no_quiz_start_msg);
                        return false;
                    }
                }

                if ($('#wpProQuiz_resultGradeEnabled:checked').length) {
                    var rCheck = true;

                    $('#resultList').children().each(function () {
                        if ($(this).is(':visible')) {
                            if (!methode.validResultInput($(this).find('input[name="resultTextGrade[prozent][]"]').val())) {
                                rCheck = false;
                                return false;
                            }
                        }
                    });

                    if (!rCheck) {
                        alert(wpProQuizLocalize.fail_grade_result);
                        return false;
                    }
                }

                if($("#wpProQuiz_stylesheet_name").length){
                    if (isEmpty($('#wpProQuiz_stylesheet_name').val())) {
                        alert(wpProQuizLocalize.no_style_name_msg);
                        return false;
                    }else{
                        var strQuizStyleName=$('#wpProQuiz_stylesheet_name').val();

                        var validExtensions = ['css']; //valid file extensions name
                        var fileNameExt = strQuizStyleName.substr(strQuizStyleName.lastIndexOf('.') + 1);
                        if ($.inArray(fileNameExt, validExtensions) == -1){
                           alert("Invalid file type");
                           return false;
                        }

                        var result=true;
                        $(".quizStyle option").each(function(){
                            if($(this).val()==strQuizStyleName){
                                alert(wpProQuizLocalize.unique_stylesheet_msg);
                                return result=false;
                            }
                        });
                        return result;
                    }
                }

                if(checkPhotoGiveAwayQuiz()){
                    if (isEmpty($('#pc_left_header').val())) {
                        alert(wpProQuizLocalize.no_left_header_pc);
                        return false;
                    }
                    if (isEmpty($('#pc_center_header').val())) {
                        alert(wpProQuizLocalize.no_center_header_pc);
                        return false;
                    }
                    if (isEmpty($('#pc_right_header').val())) {
                        alert(wpProQuizLocalize.no_right_header_pc);
                        return false;
                    }
                }

                return true;
            },

            resetLock: function () {
                //var location = window.location.pathname + window.location.search;
                //var url = location.replace('admin.php', 'admin-ajax.php');
                //url = url.replace('action=edit', 'action=reset_lock');
                //
                //$.post(url, {
                //    action: 'wp_pro_quiz_reset_lock'
                //}, function (data) {
                //    $('#resetLockMsg').show('fast').delay(2000).hide('fast');
                //});

                ajaxPost('resetLock', {
                    quizId: $('input[name="ajax_quiz_id"]').val()
                }, function () {
                    $('#resetLockMsg').show('fast').delay(2000).hide('fast');
                });
            },

            generateFormIds: function () {
                var index = 0;

                $('#form_table tbody > tr').each(function () {
                    $(this).find('[name^="form[]"]').each(function () {
                        var newname = $(this).attr('name').substr(6);
                        $(this).attr('name', 'form[' + index + ']' + newname);
                    });

                    ++index;
                });
            },

            updateFormIds: function () {
                var index = -1;
                var selected = $('.emailFormVariables option:selected').val();
                var $formVariables = $('.formVariables').empty();
                var $emailFormVariables = $('.emailFormVariables').empty().append('<option value="-1"></option>');

                if ($('.emailFormVariables').data('default') > -1) {
                    selected = $('.emailFormVariables').data('default');
                    $('.emailFormVariables').data('default', -1);
                }

                $('#form_table tbody > tr').each(function () {
                    $(this).children().first().text(index);
                    var fieldName = $(this).find('.formFieldName').val();
                    var type = $(this).find('[name="form[][type]"] option:selected');
                    var name = $(this).find('[name="form[][fieldname]"]').val();

                    //is deleted?
                    if ($(this).find('input[name="form[][form_delete]"]').val() == 1)
                        return;

                    if (index >= 0 && !isEmpty(fieldName))
                        $formVariables.append($('<li><span>$form{' + index + '}</span> - ' + fieldName + '</li>'));

                    if (type.val() == 4)
                        $emailFormVariables.append($('<option value="' + index + '">' + name + '</option>'))

                    index++;
                });

                $('.emailFormVariables option[value="' + selected + '"]').prop('selected', true);
            }

        };

        var isEmpty = function (str) {
            str = $.trim(str);
            return (!str || 0 === str.length);
        };

        var init = function () {

            $('#statistics_on').change(function () {
                if (this.checked) {
                    $('#statistics_ip_lock_tr').show();
                } else {
                    $('#statistics_ip_lock_tr').hide();
                }
            });

            $('.addResult').click(function () {
                methode.addResult();
            });

            $('.deleteResult').click(function (e) {
                methode.deleteResult(this);
            });

            $('input[name="resultTextGrade[prozent][]"]').keyup(function (event) {
                methode.changeResult(this);
            }).keydown(function (event) {
                if (event.which == 13) {
                    event.preventDefault();
                }
            });

            $('#wpProQuiz_resultGradeEnabled').change(function () {
                if (this.checked) {
                    $('#resultGrade').show();
                    $('#resultNormal').hide();
                } else {
                    $('#resultGrade').hide();
                    $('#resultNormal').show();
                }
            });

           $('#is_privacy_policy').change(function () {
                methode.toggleDisplay(this,'privacy_policy_editor');
            });

           $('#is_terms_conditions').change(function () {
                methode.toggleDisplay(this,'terms_conditions_editor');
            });

           $('#is_contest_rules').change(function () {
                methode.toggleDisplay(this,'contest_rules_editor');
            });

           $('#is_custom_form_header').change(function () {
                methode.toggleDisplay(this,'custom_form_header_editor');
            });

            $('#wpProQuiz_save').click(function (e) {
                if (!methode.validInput())
                    e.preventDefault();
                else
                    methode.generateFormIds();

                $('select[name="prerequisiteList[]"] option').attr('selected', 'selected');
            });

            $('input[name="template"]').click(function (e) {
                if ($('select[name="templateSaveList"]').val() == '0') {
                    if (isEmpty($('input[name="templateName"]').val())) {
                        alert(wpProQuizLocalize.temploate_no_name);

                        e.preventDefault();
                        return false;
                    }
                }

                methode.generateFormIds();
                $('select[name="prerequisiteList[]"] option').attr('selected', 'selected');
            });

            $('select[name="templateSaveList"]').change(function () {
                var $templateName = $('input[name="templateName"]');

                if ($(this).val() == '0') {
                    $templateName.show();
                } else {
                    $templateName.hide();
                }
            }).change();

            $('input[name="quizRunOnce"]').change(function (e) {
                if (this.checked) {
                    $('#wpProQuiz_quiz_run_once_type').show();
                    $('input[name="quizRunOnceType"]:checked').change();
                } else {
                    $('#wpProQuiz_quiz_run_once_type').hide();
                }
            });

            $('input[name="quizRunOnceType"]').change(function (e) {
                if (this.checked && (this.value == "1" || this.value == "3")) {
                    $('#wpProQuiz_quiz_run_once_cookie').show();
                } else {
                    $('#wpProQuiz_quiz_run_once_cookie').hide();
                }
            });

            $('input[name="resetQuizLock"]').click(function (e) {
                methode.resetLock();

                return false;
            });

            $('input[name="showMaxQuestion"]').change(function () {
                if (this.checked) {
//                  $('input[name="statisticsOn"]').removeAttr('checked').attr('disabled', 'disabled').change();
                    $('#wpProQuiz_showMaxBox').show();
                } else {
//                  $('input[name="statisticsOn"]').removeAttr('disabled');
                    $('#wpProQuiz_showMaxBox').hide();
                }
            });

            $('#btnPrerequisiteAdd').click(function () {
                $('select[name="quizList"] option:selected').removeAttr('selected').appendTo('select[name="prerequisiteList[]"]');
            });

            $('#btnPrerequisiteDelete').click(function () {
                $('select[name="prerequisiteList[]"] option:selected').removeAttr('selected').appendTo('select[name="quizList"]');
            });

            $('input[name="prerequisite"]').change(function () {
                if (this.checked)
                    $('#prerequisiteBox').show();
                else
                    $('#prerequisiteBox').hide();

            }).change();

            $('input[name="toplistDataAddMultiple"]').change(function () {
                if (this.checked)
                    $('#toplistDataAddBlockBox').show();
                else
                    $('#toplistDataAddBlockBox').hide();

            }).change();

            $('input[name="toplistActivated"]').change(function () {
                if (this.checked)
                    $('#toplistBox > tr:gt(0)').show();
                else
                    $('#toplistBox > tr:gt(0)').hide();

            }).change();

            $('input[name="showReviewQuestion"]').change(function () {
                if (this.checked) {
                    $('.wpProQuiz_reviewQuestionOptions').show();
                } else {
                    $('.wpProQuiz_reviewQuestionOptions').hide();
                }
            }).change();

            $('#statistics_on').change();
            $('#is_privacy_policy').change();
            $('#is_custom_form_header').change();
            $('#is_terms_conditions').change();
            $('#is_contest_rules').change();
            $('#wpProQuiz_resultGradeEnabled').change();
            $('input[name="quizRunOnce"]').change();
            $('input[name="quizRunOnceType"]:checked').change();
            $('input[name="showMaxQuestion"]').change();

            $('#form_add').click(function () {
                autoEnableCustomFields();
                $('#form_table tbody > tr:eq(0)').clone(true).appendTo('#form_table tbody').show();
                methode.updateFormIds();
            });

            $('input[name="form_delete"]').click(function () {
                var con = $(this).parents('tr');

                if($('select[name="form[][type]"]').hasClass(imageTypeClass))
                    $('select[name="form[][type]"]').removeClass(imageTypeClass);

                if (con.find('input[name="form[][form_id]"]').val() != "0") {
                    con.find('input[name="form[][form_delete]"]').val(1);
                    con.hide();
                } else {
                    con.remove();
                }
                autoEnableCustomFields("delete");
                methode.updateFormIds();
            });

            $('#form_table tbody').sortable({
                handle: '.form_move',
                update: methode.updateFormIds
            });
            $('.form_move').click(function () {
                return false;
            });

            var imageTypeClass = 'imageSelected';
            $('select[name="form[][type]"]').change(function () {
                var element = $('select[name="form[][type]"]');
                var inputSelectValue = Number($(this).val());
                 if(element.hasClass(imageTypeClass) && inputSelectValue != 9) {
                     element.removeClass(imageTypeClass);
                 }
                 switch (inputSelectValue) {
                    case 7:
                    case 8:
                        $(this).siblings('.editDropDown').show();
                        break;
                    case 9:
                        if(!element.hasClass(imageTypeClass))
                            element.addClass(imageTypeClass);
                        else {
                            alert("Only one image type allowed in custom fields");
                            $(this).val($(".formFieldTypes option:first").val());
                        }
                        $(this).siblings('.editDropDown').hide();
                        break;
                    default:
                        $(this).siblings('.editDropDown, .dropDownEditBox').hide();
                        break;
                }

            }).change();

            $('.editDropDown').click(function () {
                $('.dropDownEditBox').not(
                    $(this).siblings('.dropDownEditBox').toggle())
                    .hide();

                return false;
            });

            $('.dropDownEditBox input').click(function () {
                $(this).parent().hide();
            });

            $('.formFieldName, select[name="form[][type]"]').change(function () {
                methode.updateFormIds();
            });

            $('select[name="category"]').change(function () {
                var thisCategory = $(this);
                var box = $('#categoryAddBox').hide();

                if(checkPhotoGiveAwayQuiz()){
                    setPCEmailTemplate(true);
                    enablePhotoContest();
                }else{
                    disablePhotoContest();
                    setPCEmailTemplate(false);
                    checkThisThatQuiz() ? enableThisThatQuiz() : disableThisThatQuiz();
                }
                if (thisCategory.val() == "-1") {
                    box.show();
                }
            }).change();

            //if selected quiz type is photo contest then call pc function
            checkPhotoGiveAwayQuiz() ? enablePhotoContest() : "";

            //if selected quiz type is This or That quiz then call thisThatQuiz function
            checkThisThatQuiz() ? enableThisThatQuiz() : "";
            
            $('#categoryAddBtn').click(function () {
                methode.addCategory();
            });

            $('input[name="emailNotification"]').change(function () {
                var $tr = $('#adminEmailSettings tr:gt(0)');

                if ($('input[name="emailNotification"]:checked').val() > 0) {
                    $tr.show();
                } else {
                    $tr.hide();
                }
            }).change();

            $('input[name="userEmailNotification"]').change(function () {
                var $tr = $('#userEmailSettings tr:gt(0)');

                if ($('input[name="userEmailNotification"]:checked').val() > 0) {
                    $tr.show();
                } else {
                    $tr.hide();
                }
            }).change();

            methode.updateFormIds();

            $('input[name="email[html]"]').change(function () {
                if (switchEditors == undefined)
                    return false;

                if (this.checked) {
                    switchEditors.go('adminEmailEditor', 'tmce');
                } else {
                    switchEditors.go('adminEmailEditor', 'html');
                }

            });

            $('input[name="adminEmail[html]"]').change(function () {
                if (switchEditors == undefined)
                    return false;

                if (this.checked) {
                    switchEditors.go('adminEmailEditor', 'tmce');
                } else {
                    switchEditors.go('adminEmailEditor', 'html');
                }

            });

            $('input[name="userEmail[html]"]').change(function () {
                if (switchEditors == undefined)
                    return false;

                if (this.checked) {
                    switchEditors.go('userEmailEditor', 'tmce');
                } else {
                    switchEditors.go('userEmailEditor', 'html');
                }

            });

            $('#save_as_new_stylesheet').change(function () {
                if (this.checked) {
                    var stylesheet_div="<input name='stylesheet_name' id='wpProQuiz_stylesheet_name' type='text' class='regular-text full-width' value=''>";
                    stylesheet_div+="<div><strong><i>Note : Please save file with .css extension</i></strong></div>";
                    $('#wpProQuiz_stylesheet_name_div').html(stylesheet_div).show();
                } else {
                    $('#wpProQuiz_stylesheet_name_div').html("").hide();
                }
            });

            $('.deleteStyleSheet').click(function (e) {
                var current_stylesheet=$('.quizStyle').val();
                var applied_stylesheet=$('#applied_stylesheet').html();
                var quiz_id=$('#quiz_id').html();
                
                var defaultFileNames = [
                    wpProQuizLocalize.smallCheckBoxCss,
                    wpProQuizLocalize.mediumCheckBoxCss,
                    wpProQuizLocalize.largeCheckBoxCss,
                    wpProQuizLocalize.denverPostCss,
                    wpProQuizLocalize.theKnowCss
                ]; //Default css files
                
                //Check if selected stylesheet is default css or not
                if ($.inArray(current_stylesheet, defaultFileNames) == -1){
                    //selected stylesheet is not default css
                    if(current_stylesheet==applied_stylesheet){
                        var msg=wpProQuizLocalize.delete_applied_stylesheet_msg;
                    }else{
                        var msg=wpProQuizLocalize.delete_stylesheet_msg;
                    }
                    var confirmResult=confirm(msg);
                    if(confirmResult){
                        var data = {
                            current_stylesheet: current_stylesheet,
                            applied_stylesheet: applied_stylesheet,
                            quiz_id : quiz_id
                        };
                        methode.deleteStyleSheet(this,data);
                    }else{
                        return false;
                    }
                }else{
                    //selected stylesheet is default css
                    alert(wpProQuizLocalize.delete_default_stylesheet_msg);
                    return false;
                }
            });

            setTimeout(function () {
                $('input[name="userEmail[html]"]').change();
                $('input[name="email[html]"]').change();
            }, 1000);

            /**
             * Photo contest header value validation manage
             * @return void
             */
            $('.pcHeaderValue').keyup(function () {
                var pcHeaderMaxLength = 64;
                if(this.value.length > pcHeaderMaxLength){
                    $('.swizpro_pc_header_msg').html('Maximum character limit exceeded.').css("color","red");
                    $('#wpProQuiz_save').prop("disabled", true);
                }else{
                    $('.swizpro_pc_header_msg').html("");
                    $('#wpProQuiz_save').prop("disabled", false);
                }
            });
        };

        init();

        var quizId = $('input[name="ajax_quiz_id"]').val();

        /**
         * Automatic ernable custom field option
         * @return void
         */
        function autoEnableCustomFields(source){
            var customFieldCount = $('#form_table tbody > tr:visible').length;
            var condition = customFieldCount >= 0;
            var checked = true;

            if("delete" == source){
                var condition = customFieldCount <= 0;
                var checked = false;
            }

            if(condition){
                $('#formActivated').prop('checked', checked);
            }
        }

        /**
         * Show or hide if photo contest is enable
         * @return void
         */
        function enablePhotoContest(){
            $('.questions > a').html("Entries");
            $('#quiz_description').html("Contest Header / Details");
            $('#quiz_results_page').html("Contest Results Page");
            $('#quiz_options').html("Contest Options");
            $('#quiz_results_page_options').html("Contest Results Page Options");
            $('.quizSharingDesc th').html("Contest Sharing Description");
            $('#photoContestDetailsBlock').show();
            $('.quizResultGrade').hide();
            $('#question_display_options').parent().hide();
            $('.pcHide').hide();
            $('.pcHeaderContent').show();
            $('.quizCoverTxt').hide();

            if(quizId == 0){
                //Display photo contest message
                $('#questions').html(wpProQuizLocalize.pcEntryTabMsg);
            }
        }

        /**
         * Show or hide if photo contest is disable
         * @return void
         */
        function disablePhotoContest(){
            $('.questions > a').html("Questions");
            $('#quiz_description').html("Quiz Description / Cover Page");
            $('#quiz_results_page').html("Quiz Results Page");
            $('#quiz_options').html("Quiz Options");
            $('#quiz_results_page_options').html("Quiz Results Page Options");
            $('.quizSharingDesc th').html("Quiz Sharing Description");
            $('#photoContestDetailsBlock').hide();
            $('.quizResultGrade').show();
            $('#question_display_options').parent().show();
            $('.pcHide').show();
            $('.pcHeaderContent').hide();
            $('.quizCoverTxt').show();

            if(quizId == 0){
                //Display question message
                $('#questions').html(wpProQuizLocalize.questionTabMsg);
            }
        }

        /**
         * Show or hide if This or That is enable
         * @return void
         */
        function enableThisThatQuiz(){
            $('.quizResultGrade').hide();
            $('.thisThatHide').hide();
        }

        /**
         * Show or hide if This or That is disable
         * @return void
         */
        function disableThisThatQuiz(){
            $('.quizResultGrade').show();
            $('.thisThatHide').show();
        }
    };

    $.fn.wpProQuiz_statistics = function () {
        var currectTab = 'wpProQuiz_typeAnonymeUser';
        var changePageNav = true;

        var methode = {
            loadStatistics: function (userId) {
                var location = window.location.pathname + window.location.search;
                var url = location.replace('admin.php', 'admin-ajax.php') + '&action=load_statistics';
                var data = {
                    action: 'wp_pro_quiz_load_statistics',
                    userId: userId
                };

                $('#wpProQuiz_loadData').show();
                $('#wpProQuiz_statistics_content, #wpProQuiz_statistics_overview').hide();

                $.post(
                    url,
                    data,
                    methode.setStatistics,
                    'json'
                );
            },

            setStatistics: function (json) {
                var $table = $('.wpProQuiz_statistics_table');
                var $tbody = $table.find('tbody');

                if (currectTab == 'wpProQuiz_typeOverview') {
                    return;
                }

                var setItem = function (i, j, r) {
                    i.find('.wpProQuiz_cCorrect').text(j.cCorrect + ' (' + j.pCorrect + '%)');
                    i.find('.wpProQuiz_cIncorrect').text(j.cIncorrect + ' (' + j.pIncorrect + '%)');
                    i.find('.wpProQuiz_cTip').text(j.cTip);
                    i.find('.wpProQuiz_cPoints').text(j.cPoints);

                    if (r == true) {
                        $table.find('.wpProQuiz_cResult').text(j.result + '%');
                    }
                };

                setItem($table, json.clear, false);

                $.each(json.items, function (i, v) {
                    setItem($tbody.find('#wpProQuiz_tr_' + v.id), v, false);
                });

                setItem($table.find('tfoot'), json.global, true);

                $('#wpProQuiz_loadData').hide();
                $('#wpProQuiz_statistics_content, .wpProQuiz_statistics_table').show();
            },

            loadOverview: function () {
                $('.wpProQuiz_statistics_table, #wpProQuiz_statistics_content, #wpProQuiz_statistics_overview').hide();
                $('#wpProQuiz_loadData').show();

                var location = window.location.pathname + window.location.search;
                var url = location.replace('admin.php', 'admin-ajax.php') + '&action=load_statistics';
                var data = {
                    action: 'wp_pro_quiz_load_statistics',
                    overview: true,
                    pageLimit: $('#wpProQuiz_pageLimit').val(),
                    onlyCompleted: Number($('#wpProQuiz_onlyCompleted').is(':checked')),
                    page: $('#wpProQuiz_currentPage').val(),
                    generatePageNav: Number(changePageNav)
                };

                $.post(
                    url,
                    data,
                    function (json) {
                        $('#wpProQuiz_statistics_overview_data').empty();

                        if (currectTab != 'wpProQuiz_typeOverview') {
                            return;
                        }

                        var item = $('<tr>'
                            + '<th><a href="#">---</a></th>'
                            + '<th class="wpProQuiz_points">---</th>'
                            + '<th class="wpProQuiz_cCorrect" style="color: green;">---</th>'
                            + '<th class="wpProQuiz_cIncorrect" style="color: red;">---</th>'
                            + '<th class="wpProQuiz_cTip">---</th>'
                            + '<th class="wpProQuiz_cResult" style="font-weight: bold;">---</th>'
                            + '</tr>'
                        );

                        $.each(json.items, function (i, v) {
                            var d = item.clone();

                            d.find('a').text(v.userName).data('userId', v.userId).click(function () {
                                $('#userSelect').val($(this).data('userId'));

                                $('#wpProQuiz_typeRegisteredUser').click();

                                return false;
                            });

                            if (v.completed) {
                                d.find('.wpProQuiz_points').text(v.cPoints);
                                d.find('.wpProQuiz_cCorrect').text(v.cCorrect + ' (' + v.pCorrect + '%)');
                                d.find('.wpProQuiz_cIncorrect').text(v.cIncorrect + ' (' + v.pIncorrect + '%)');
                                d.find('.wpProQuiz_cTip').text(v.cTip);
                                d.find('.wpProQuiz_cResult').text(v.result + '%');
                            } else {
                                d.find('th').removeAttr('style');
                            }

                            $('#wpProQuiz_statistics_overview_data').append(d);
                        });

                        if (json.page != undefined) {
                            methode.setPageNav(json.page);
                            changePageNav = false;
                        }

                        $('#wpProQuiz_loadData').hide();
                        $('#wpProQuiz_statistics_overview').show();
                    },
                    'json'
                );
            },

            loadFormOverview: function () {
                $('#wpProQuiz_tabFormOverview').show();
            },

            changeTab: function (id) {
                currectTab = id;

                if (id == 'wpProQuiz_typeRegisteredUser') {
                    methode.loadStatistics($('#userSelect').val());
                } else if (id == 'wpProQuiz_typeAnonymeUser') {
                    methode.loadStatistics(0);
                } else if (id == 'wpProQuiz_typeForm') {
                    methode.loadFormOverview();
                } else {
                    methode.loadOverview();
                }
            },

            resetStatistic: function (complete) {
                var userId = (currectTab == 'wpProQuiz_typeRegisteredUser') ? $('#userSelect').val() : 0;
                var location = window.location.pathname + window.location.search;
                var url = location.replace('admin.php', 'admin-ajax.php') + '&action=reset';
                var data = {
                    action: 'wp_pro_quiz_statistics',
                    userId: userId,
                    'complete': complete
                };

                $.post(url, data, function (e) {
                    methode.changeTab(currectTab);
                });
            },

            setPageNav: function (page) {
                page = Math.ceil(page / $('#wpProQuiz_pageLimit').val());
                $('#wpProQuiz_currentPage').empty();

                for (var i = 1; i <= page; i++) {
                    $(document.createElement('option'))
                        .val(i)
                        .text(i)
                        .appendTo($('#wpProQuiz_currentPage'));
                }

                $('#wpProQuiz_pageLeft, #wpProQuiz_pageRight').hide();

                if ($('#wpProQuiz_currentPage option').length > 1) {
                    $('#wpProQuiz_pageRight').show();

                }
            }
        };

        var init = function () {
            $('.wpProQuiz_tab').click(function (e) {
                var $this = $(this);

                if ($this.hasClass('button-primary')) {
                    return false;
                }

                if ($this.attr('id') == 'wpProQuiz_typeRegisteredUser') {
                    $('#wpProQuiz_userBox').show();
                } else {
                    $('#wpProQuiz_userBox').hide();
                }

                $('.wpProQuiz_tab').removeClass('button-primary').addClass('button-secondary');
                $this.removeClass('button-secondary').addClass('button-primary');

                methode.changeTab($this.attr('id'));

                return false;
            });

            $('#userSelect').change(function () {
                methode.changeTab('wpProQuiz_typeRegisteredUser');
            });

            $('.wpProQuiz_update').click(function () {
                methode.changeTab(currectTab);

                return false;
            });

            $('#wpProQuiz_reset').click(function () {

                var c = confirm(wpProQuizLocalize.reset_statistics_msg);

                if (c) {
                    methode.resetStatistic(false);
                }

                return false;
            });

            $('.wpProQuiz_resetComplete').click(function () {

                var c = confirm(wpProQuizLocalize.reset_statistics_msg);

                if (c) {
                    methode.resetStatistic(true);
                }

                return false;
            });

            $('#wpProQuiz_pageLimit, #wpProQuiz_onlyCompleted').change(function () {
                $('#wpProQuiz_currentPage').val(0);
                changePageNav = true;
                methode.changeTab(currectTab);

                return false;
            });

            $('#wpProQuiz_currentPage').change(function () {
                $('#wpProQuiz_pageLeft, #wpProQuiz_pageRight').hide();

                if ($('#wpProQuiz_currentPage option').length == 1) {

                } else if ($('#wpProQuiz_currentPage option:first-child:selected').length) {
                    $('#wpProQuiz_pageRight').show();
                } else if ($('#wpProQuiz_currentPage option:last-child:selected').length) {
                    $('#wpProQuiz_pageLeft').show();
                } else {
                    $('#wpProQuiz_pageLeft, #wpProQuiz_pageRight').show();
                }

                methode.changeTab(currectTab);
            });

            $('#wpProQuiz_pageRight').click(function () {
                $('#wpProQuiz_currentPage option:selected').next().attr('selected', 'selected');
                $('#wpProQuiz_currentPage').change();

                return false;
            });

            $('#wpProQuiz_pageLeft').click(function () {
                $('#wpProQuiz_currentPage option:selected').prev().attr('selected', 'selected');
                $('#wpProQuiz_currentPage').change();

                return false;
            });

            methode.changeTab('wpProQuiz_typeAnonymeUser');
        };

        init();
    };

    $.fn.wpProQuiz_toplist = function () {
        function ajaxPost(func, data, success) {
            var d = {
                action: 'wp_pro_quiz_admin_ajax',
                func: func,
                data: data
            };

            $.post(ajaxurl, d, success, 'json');
        }

        var elements = {
            sort: $('#wpProQuiz_sorting'),
            pageLimit: $('#wpProQuiz_pageLimit'),
            currentPage: $('#wpProQuiz_currentPage'),
            loadDataBox: $('#wpProQuiz_loadData'),
            pageLeft: $('#wpProQuiz_pageLeft'),
            pageRight: $('#wpProQuiz_pageRight'),
            dataBody: $('#wpProQuiz_toplistTable tbody'),
            rowClone: $('#wpProQuiz_toplistTable tbody tr:eq(0)').clone(),
            content: $('#wpProQuiz_content')
        };

        var methods = {
            loadData: function (action) {
                //var location = window.location.pathname + window.location.search;
                //var url = location.replace('admin.php', 'admin-ajax.php') + '&action=load_toplist';
                var th = this;
                var data = {
                    //action: 'wp_pro_quiz_load_toplist',
                    sort: elements.sort.val(),
                    limit: elements.pageLimit.val(),
                    page: elements.currentPage.val(),
                    quizId: $('input[name="ajax_quiz_id"]').val()
                };

                if (action != undefined) {
                    $.extend(data, action);
                }

                elements.loadDataBox.show();
                elements.content.hide();

                //$.post(url, data, function (json) {
                //    //methods.handleDataRequest(json.data);
                //    th.handleDataRequest(json.data);
                //
                //    if (json.nav != undefined) {
                //        //methods.handleNav(json.nav);
                //        th.handleNav(json.nav);
                //    }
                //
                //    elements.loadDataBox.hide();
                //    elements.content.show();
                //}, 'json');

                ajaxPost('adminToplist', data, function (json) {
                    th.handleDataRequest(json.data);

                    if (json.nav != undefined) {
                        th.handleNav(json.nav);
                    }

                    elements.loadDataBox.hide();
                    elements.content.show();
                });
            },

            handleNav: function (nav) {
                elements.currentPage.empty();

                for (var i = 1; i <= nav.pages; i++) {
                    $(document.createElement('option'))
                        .val(i).text(i)
                        .appendTo(elements.currentPage);
                }

                this.checkNav();
            },

            handleDataRequest: function (json) {
                var methods = this;

                elements.dataBody.empty();

                $.each(json, function (i, v) {
                    var data = elements.rowClone.clone().children();

                    data.eq(0).children().val(v.id);
                    data.eq(1).find('strong').text(v.name);
                    data.eq(1).find('.inline_editUsername').val(v.name);
                    data.eq(2).find('.wpProQuiz_email').text(v.email);
                    data.eq(2).find('input').val(v.email);
                    data.eq(3).text(v.type);
                    data.eq(4).text(v.date);
                    data.eq(5).text(v.points);
                    data.eq(6).text(v.result);

                    data.parent().show().appendTo(elements.dataBody);
                });

                if (!json.length) {
                    $(document.createElement('td'))
                        .attr('colspan', '7')
                        .text(wpProQuizLocalize.no_data_available)
                        .css({
                            'font-weight': 'bold',
                            'text-align': 'center',
                            'padding': '5px'
                        })
                        .appendTo(document.createElement('tr'))
                        .appendTo(elements.dataBody);
                }

                $('.wpProQuiz_delete').click(function () {
                    if (confirm(wpProQuizLocalize.confirm_delete_entry)) {
                        var id = new Array($(this).closest('tr').find('input[name="checkedData[]"]').val());

                        methods.loadData({
                            a: 'delete',
                            toplistIds: id
                        });
                    }

                    return false;
                });

                $('.wpProQuiz_edit').click(function () {
                    var $contain = $(this).closest('tr');

                    $contain.find('.row-actions').hide();
                    $contain.find('.inline-edit').show();

                    $contain.find('.wpProQuiz_username, .wpProQuiz_email').hide();
                    $contain.find('.inline_editUsername, .inline_editEmail').show();

                    return false;
                });

                $('.inline_editSave').click(function () {
                    var $contain = $(this).closest('tr');
                    var username = $contain.find('.inline_editUsername').val();
                    var email = $contain.find('.inline_editEmail').val();

                    if (methods.isEmpty(username) || methods.isEmpty(email)) {
                        alert(wpProQuizLocalize.not_all_fields_completed);

                        return false;
                    }

                    methods.loadData({
                        a: 'edit',
                        toplistId: $contain.find('input[name="checkedData[]"]').val(),
                        name: username,
                        email: email
                    });

                    return false;
                });

                $('.inline_editCancel').click(function () {
                    var $contain = $(this).closest('tr');

                    $contain.find('.row-actions').show();
                    $contain.find('.inline-edit').hide();

                    $contain.find('.wpProQuiz_username, .wpProQuiz_email').show();
                    $contain.find('.inline_editUsername, .inline_editEmail').hide();

                    $contain.find('.inline_editUsername').val($contain.find('.wpProQuiz_username').text());
                    $contain.find('.inline_editEmail').val($contain.find('.wpProQuiz_email').text());

                    return false;
                });
            },

            checkNav: function () {
                var n = elements.currentPage.val();

                if (n == 1) {
                    elements.pageLeft.hide();
                } else {
                    elements.pageLeft.show();
                }

                if (n == elements.currentPage.children().length) {
                    elements.pageRight.hide();
                } else {
                    elements.pageRight.show();
                }
            },

            isEmpty: function (text) {
                text = $.trim(text);

                return (!text || 0 === text.length);
            }
        };

        var init = function () {
            elements.sort.change(function () {
                methods.loadData();
            });

            elements.pageLimit.change(function () {
                methods.loadData({nav: 1});
            });

            elements.currentPage.change(function () {
                methods.checkNav();
                methods.loadData();
            });

            elements.pageLeft.click(function () {
                elements.currentPage.val(Number(elements.currentPage.val()) - 1);
                methods.checkNav();
                methods.loadData();
            });

            elements.pageRight.click(function () {
                elements.currentPage.val(Number(elements.currentPage.val()) + 1);
                methods.checkNav();
                methods.loadData();
            });

            $('#wpProQuiz_deleteAll').click(function () {
                methods.loadData({a: 'deleteAll'});
            });

            $('#wpProQuiz_action').click(function () {
                var name = $('#wpProQuiz_actionName').val();

                if (name != '0') {

                    var ids = $('input[name="checkedData[]"]:checked').map(function () {
                        return $(this).val();
                    }).get();

                    methods.loadData({
                        a: name,
                        toplistIds: ids
                    });
                }
            });

            $('#wpProQuiz_checkedAll').change(function () {
                if (this.checked)
                    $('input[name="checkedData[]"]').attr('checked', 'checked');
                else
                    $('input[name="checkedData[]"]').removeAttr('checked', 'checked');
            });

            methods.loadData({nav: 1});
        };

        init();
    };

    if ($('.wpProQuiz_quizOverall').length)
        $('.wpProQuiz_quizOverall').wpProQuiz_preview();

    if ($('.wpProQuiz_quizOverall').length) {
        $('.wpProQuiz_quizOverall').wpProQuiz_quizOverall();
    }

    if ($('.wpProQuiz_quizEdit').length)
        $('.wpProQuiz_quizEdit').wpProQuiz_quizEdit();

//  if($('.wpProQuiz_questionEdit').length)
//      $('.wpProQuiz_questionEdit').wpProQuiz_questionEdit();

    //if($('.wpProQuiz_questionOverall').length)
    //  $('.wpProQuiz_questionOverall').wpProQuiz_questionOverall();

//  if($('.wpProQuiz_statistics').length)
//      $('.wpProQuiz_statistics').wpProQuiz_statistics();

    if ($('.wpProQuiz_toplist').length)
        $('.wpProQuiz_toplist').wpProQuiz_toplist();

    /**
     * NEW
     */
    /**
     * @memberOf WpProQuiz_Admin
     */
    function WpProQuiz_Admin() {
        var global = this;

        global = {
            displayChecked: function (t, box, neg, disabled) {
                var c = neg ? !t.checked : t.checked;

                if (disabled)
                    c ? box.attr('disabled', 'disabled') : box.removeAttr('disabled');
                else
                    c ? box.show() : box.hide();
            },

            isEmpty: function (text) {
                text = $.trim(text);

                return (!text || 0 === text.length);
            },

            isNumber: function (number) {
                number = $.trim(number);
                return !global.isEmpty(number) && !isNaN(number);
            },

            getMceContent: function (id) {
                var editor = tinymce.editors[id];
                if (editor != undefined && !editor.isHidden()) {
                    return editor.getContent();
                }
                return $('#' + id).val();
            },

            ajaxPost: function (func, data, success) {
                var d = {
                    action: 'wp_pro_quiz_admin_ajax',
                    func: func,
                    data: data
                };

                $.post(ajaxurl, d, success, 'json');
            },

            ajaxPostCsv: function (func, data, success) {
               var d = {
                   action: 'wp_pro_quiz_admin_ajax',
                   func: func,
                   data: data
               };

               $.post(ajaxurl, d, function(response) {
                    var dataone = JSON.parse(response);
                    var csv = '';
                    dataone.forEach(function(row, index) {
                        var data = [];
                        var cols = [];
                        for (var key in row) {
                            if(index == 0) {
                                cols.push(escapeComma(key));
                            }
                            data.push(escapeComma(row[key]));
                        }
                        if(index == 0) {
                            csv += cols.join(',');
                            csv += "\n";
                        }
                        csv += data.join(',');
                        csv += "\n";
                    });
                    var hiddenElement = document.createElement('a');
                    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
                    hiddenElement.target = '_blank';
                    var quizName = data.quizName;
                    var fileName = quizName.replace(" ","_");
                    hiddenElement.download = (!checkUndefinedInput(fileName)) ? fileName+".csv" : "results.csv";
                    hiddenElement.click();
               });
           }
        };

        /**
         * Escapes comma from string for CSV
         * @return string
         */
        function escapeComma(input){
            return input.includes(",") ? "\"" + input + "\"" : input;
        }

        var tabWrapper = function () {
            $('.wpProQuiz_tab_wrapper a').click(function () {
                var $this = $(this);
                var tabId = $this.data('tab');
                var currentTab = $this.siblings('.button-primary').removeClass('button-primary').addClass('button-secondary');

                $this.removeClass('button-secondary').addClass('button-primary');

                $(currentTab.data('tab')).hide('fast');
                $(tabId).show('fast');

                $(document).trigger({
                    type: 'changeTab',
                    tabId: tabId
                });

                return false;
            });
        };

        var module = {
            /**
             * @memberOf WpProQuiz_admin.module
             */

            gobalSettings: function () {
                var methode = {
                    categoryDelete: function (id, type) {
                        var data = {
                            categoryId: id
                        };

                        global.ajaxPost('categoryDelete', data, function (json) {
                            if (json.err) {

                                return;
                            }

                            $('select[name="category' + type + '"] option[value="' + id + '"]').remove();
                            $('select[name="category' + type + '"]').change();
                        });
                    },

                    categoryEdit: function (id, name, type) {
                        var data = {
                            categoryId: id,
                            categoryName: $.trim(name)
                        };

                        if (global.isEmpty(name)) {
                            alert(wpProQuizLocalize.category_no_name);
                            return;
                        }

                        global.ajaxPost('categoryEdit', data, function (json) {
                            if (json.err) {

                                return;
                            }

                            $('select[name="category' + type + '"] option[value="' + id + '"]').text(data.categoryName);
                            $('select[name="category' + type + '"]').change();
                        });
                    },

                    changeTimeFormat: function (inputName, $select) {
                        if ($select.val() != "0")
                            $('input[name="' + inputName + '"]').val($select.val());
                    },

                    templateDelete: function (id, type) {
                        var data = {
                            templateId: id,
                            type: type
                        };

                        global.ajaxPost('templateDelete', data, function (json) {
                            if (json.err) {

                                return;
                            }

                            if (!type) {
                                $('select[name="templateQuiz"] option[value="' + id + '"]').remove();
                                $('select[name="templateQuiz"]').change();
                            } else {
                                $('select[name="templateQuestion"] option[value="' + id + '"]').remove();
                                $('select[name="templateQuestion"]').change();
                            }
                        });
                    },

                    templateEdit: function (id, name, type) {

                        if (global.isEmpty(name)) {
                            alert(wpProQuizLocalize.category_no_name);
                            return;
                        }

                        var data = {
                            templateId: id,
                            name: $.trim(name),
                            type: type
                        };

                        global.ajaxPost('templateEdit', data, function (json) {
                            if (json.err) {

                                return;
                            }

                            if (!type) {
                                $('select[name="templateQuiz"] option[value="' + id + '"]').text(data.name);
                                $('select[name="templateQuiz"]').change();
                            } else {
                                $('select[name="templateQuestion"] option[value="' + id + '"]').text(data.name);
                                $('select[name="templateQuestion"]').change();
                            }
                        });
                    }
                };

                var init = function () {
//                  $('.wpProQuiz_tab').click(function() {
//                      var $this = $(this);
//                      
//                      $('.wpProQuiz_tab').removeClass('button-primary').addClass('button-secondary');
//                      $this.removeClass('button-secondary').addClass('button-primary');
//                      
//                      $('#problemInfo, #problemContent, #globalContent').hide('fast');
//                      
//                      if($this.attr('id') == 'globalTab') {
//                          $('#globalContent').show('fast');
//                      } else {
//                          $('#problemInfo, #problemContent').show('fast');
//                      }
//                  });

                    $('select[name="category"]').change(function () {
                        $('input[name="categoryEditText"]').val($(this).find(':selected').text());
                    }).change();

                    $('input[name="categoryDelete"]').click(function () {
                        var id = $('select[name="category"] option:selected').val();

                        methode.categoryDelete(id, '');
                    });

                    $('input[name="categoryEdit"]').click(function () {
                        var id = $('select[name="category"] option:selected').val();
                        var text = $('input[name="categoryEditText"]').val();

                        methode.categoryEdit(id, text, '');
                    });

                    $('select[name="categoryQuiz"]').change(function () {
                        $('input[name="categoryQuizEditText"]').val($(this).find(':selected').text());
                    }).change();

                    $('input[name="categoryQuizDelete"]').click(function () {
                        var id = $('select[name="categoryQuiz"] option:selected').val();

                        methode.categoryDelete(id, 'Quiz');
                    });

                    $('input[name="categoryQuizEdit"]').click(function () {
                        var id = $('select[name="categoryQuiz"] option:selected').val();
                        var text = $('input[name="categoryQuizEditText"]').val();

                        methode.categoryEdit(id, text, 'Quiz');
                    });

                    $('#statistic_time_format_select').change(function () {
                        methode.changeTimeFormat('statisticTimeFormat', $(this));
                    });

                    $(document).bind('changeTab', function (data) {
                        $('#problemInfo').hide('fast');

                        switch (data.tabId) {
                            case '#problemContent':
                                $('#problemInfo').show('fast');
                                break;
                            case '#emailSettingsTab':
                                break;
                        }
                    });

                    $('input[name="email[html]"]').change(function () {
                        if (switchEditors == undefined)
                            return false;

                        if (this.checked) {
                            switchEditors.go('adminEmailEditor', 'tmce');
                        } else {
                            switchEditors.go('adminEmailEditor', 'html');
                        }

                    }).change();

                    $('input[name="userEmail[html]"]').change(function () {
                        if (switchEditors == undefined)
                            return false;

                        if (this.checked) {
                            switchEditors.go('userEmailEditor', 'tmce');
                        } else {
                            switchEditors.go('userEmailEditor', 'html');
                        }

                    }).change();

                    $('select[name="templateQuiz"]').change(function () {
                        $('input[name="templateQuizEditText"]').val($(this).find(':selected').text());
                    }).change();

                    $('select[name="templateQuestion"]').change(function () {
                        $('input[name="templateQuestionEditText"]').val($(this).find(':selected').text());
                    }).change();

                    $('input[name="templateQuizDelete"]').click(function () {
                        var id = $('select[name="templateQuiz"] option:selected').val();

                        methode.templateDelete(id, 0);
                    });

                    $('input[name="templateQuestionDelete"]').click(function () {
                        var id = $('select[name="templateQuestion"] option:selected').val();

                        methode.templateDelete(id, 1);
                    });

                    $('input[name="templateQuizEdit"]').click(function () {
                        var id = $('select[name="templateQuiz"] option:selected').val();
                        var text = $('input[name="templateQuizEditText"]').val();

                        methode.templateEdit(id, text, 0);
                    });

                    $('input[name="templateQuestionEdit"]').click(function () {
                        var id = $('select[name="templateQuestion"] option:selected').val();
                        var text = $('input[name="templateQuestionEditText"]').val();

                        methode.templateEdit(id, text, 1);
                    });
                };

                init();
            },

            questionEdit: function () {
                var methode = this;
                var filter = $.noop();
                $('.thisThatAnswerText').hide();

                var elements = {
                    answerChildren: $('.answer_felder > div'),
                    pointsModus: $('input[name="answerPointsActivated"]'),
                    gPoints: $('input[name="points"]')
                };

                if(checkThisThatQuiz()){
                    $('.thisThatHide').hide();
                    $('.thisThatAnswerText').show();
                }

                methode = {
                    generateArrayIndex: function () {
                        var type = $('input[name="answerType"]:checked').val();
                        type = (type == 'single' || type == 'multiple') ? 'classic_answer' : type;

                        $('.answerList').each(function () {
                            var currentType = $(this).parent().attr('class');

                            $(this).children().each(function (i, v) {
                                $(this).find('[name^="answerData"]').each(function () {
                                    var name = this.name;
                                    var x = name.search(/\](\[\w+\])+$/);
                                    var n = (type == currentType) ? i : 'none';

                                    if (x > 0) {
                                        this.name = 'answerData[' + n + name.substring(x, name.length);

                                    }
                                });
                            });
                        });
                    },

                    globalValidate: function () {
                        if (global.isEmpty(global.getMceContent('question'))) {
                            alert(wpProQuizLocalize.no_question_msg);
                            return false;
                        }

                        if (!elements.pointsModus.is(':checked')) {
                            var p = elements.gPoints.val();

                            if (!global.isNumber(p) || p < 1) {
                                alert(wpProQuizLocalize.no_nummber_points);

                                return false;
                            }
                        } else {
                            if ($('input[name="answerType"]:checked').val() == 'free_answer') {
                                alert(wpProQuizLocalize.dif_points);
                                return false;
                            }
                        }

                        if (filter() === false)
                            return false;

                        if(!checkThisThatAnswerCount(true)){
                            // Validation for exact two answers in question page
                            alert(wpProQuizLocalize.thisThatMinAnsMsg);
                            return false;
                        }

                        if(!checkThisThatAnswerText()){
                            // Validation to enter answer text for question
                            alert(wpProQuizLocalize.thisThatFillAllAnsMsg);
                            return false;
                        }


                        return true;
                    },

                    answerRemove: function () {
                        var li = $(this).parent();

                        if (li.parent().children().length < 2)
                            return false;

                        li.remove();

                        return false;
                    },

                    addCategory: function () {
                        var name = $.trim($('input[name="categoryAdd"]').val());

                        if (global.isEmpty(name)) {
                            return;
                        }

                        var data = {
                            categoryName: name
                        };

                        global.ajaxPost('categoryAdd', data, function (json) {
                            if (json.err) {
                                $('#categoryMsgBox').text(json.err).show('fast').delay(2000).hide('fast');
                                return;
                            }

                            var $option = $(document.createElement('option'))
                                .val(json.categoryId)
                                .text(json.categoryName)
                                .attr('selected', 'selected');

                            $('select[name="category"]').append($option).change();

                        });
                    },

                    addMediaClick: function () {
                        if (typeof tb_show != "function")
                            return false;

                        var closest = $(this).closest('li');
                        var htmlCheck = closest.find('input[name="answerData[][html]"]:eq(0)');
                        var field = closest.find('.wpProQuiz_text:eq(0)');

                        window.org_send_to_editor = window.send_to_editor;
                        var org_tb_remove = tb_remove;

                        window.send_to_editor = function (html) {
                            //var img = $('img', html)[0].outerHTML;
                            var img =html;
                            field.val(field.val() + img);
                            htmlCheck.attr('checked', true);

                            tb_remove();

                            window.send_to_editor = window.org_send_to_editor;
                        };

                        window.tb_remove = function () {
                            window.send_to_editor = window.org_send_to_editor;
                            tb_remove = org_tb_remove;

                            tb_remove();
                        };

                        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
                    }
                };

                var validate = {
                    classic_answer: function () {
                        var findText = 0;
                        var findCorrect = 0;
                        var findPoints = 0;

                        $('.classic_answer .answerList').children().each(function () {
                            var t = $(this);

                            if (!global.isEmpty(t.find('textarea[name="answerData[][answer]"]').val())) {
                                findText++;

                                if (t.find('input[name="answerData[][correct]"]:checked').length) {
                                    findCorrect++;
                                }

                                var p = t.find('input[name="answerData[][points]"]').val();

                                if (global.isNumber(p) && p >= 0) {
                                    findPoints++;
                                }
                            }
                        });

                        if (!findText) {
                            alert(wpProQuizLocalize.no_answer_msg);
                            return false;
                        }

                        if (!findCorrect && !($('input[name="disableCorrect"]').is(':checked')
                            && $('input[name="answerPointsDiffModusActivated"]').is(':checked')
                            && $('input[name="answerPointsActivated"]').is(':checked')
                            && $('input[name="answerType"]:checked').val() == 'single')) {
                            if(!checkThisThatQuiz()){
                                alert(wpProQuizLocalize.no_correct_msg);
                                return false;
                            }
                        }

                        if (findPoints != findText && elements.pointsModus.is(':checked')) {
                            alert(wpProQuizLocalize.no_nummber_points_new);
                            return false;
                        }

                        return true;
                    },

                    free_answer: function () {
                        if (global.isEmpty($('.free_answer textarea[name="answerData[][answer]"]').val())) {
                            alert(wpProQuizLocalize.no_answer_msg);
                            return false;
                        }

                        return true;
                    },

                    cloze_answer: function () {
                        if (global.isEmpty(global.getMceContent('cloze'))) {
                            alert(wpProQuizLocalize.no_answer_msg);
                            return false;
                        }

                        return true;
                    },

                    sort_answer: function () {
                        var findText = 0;
                        var findPoints = 0;

                        $('.sort_answer .answerList').children().each(function () {
                            var t = $(this);

                            if (!global.isEmpty(t.find('textarea[name="answerData[][answer]"]').val())) {
                                findText++;

                                var p = t.find('input[name="answerData[][points]"]').val();

                                if (global.isNumber(p) && p >= 0) {
                                    findPoints++;
                                }
                            }
                        });

                        if (!findText) {
                            alert(wpProQuizLocalize.no_answer_msg);
                            return false;
                        }

                        if (findPoints != findText && elements.pointsModus.is(':checked')) {
                            alert(wpProQuizLocalize.no_nummber_points_new);
                            return false;
                        }

                        return true;
                    },

                    matrix_sort_answer: function () {
                        var findText = 0;
                        var findPoints = 0;
                        var sortString = true;
                        var menge = 0;

                        $('.matrix_sort_answer .answerList').children().each(function () {
                            var t = $(this);
                            var p = t.find('input[name="answerData[][points]"]').val();

                            if (!global.isEmpty(t.find('textarea[name="answerData[][answer]"]').val())) {
                                findText++;
                                menge++;

                                if (global.isEmpty(t.find('textarea[name="answerData[][sort_string]"]').val())) {
                                    sortString = false;
                                }

                                if (global.isNumber(p) && p >= 0) {
                                    findPoints++;
                                }
                            } else {
                                if (!global.isEmpty(t.find('textarea[name="answerData[][sort_string]"]').val())) {
                                    menge++;

                                    if (global.isNumber(p) && p >= 0) {
                                        findPoints++;
                                    }
                                }
                            }
                        });

                        if (!findText) {
                            alert(wpProQuizLocalize.no_answer_msg);
                            return false;
                        }

                        if (!sortString) {
                            alert(wpProQuizLocalize.no_sort_element_criterion);
                            return false;
                        }

                        if (findPoints != menge && elements.pointsModus.is(':checked')) {
                            alert(wpProQuizLocalize.no_nummber_points_new);
                            return false;
                        }

                        return true;
                    },

                    assessment_answer: function () {
                        if (global.isEmpty(global.getMceContent('assessment'))) {
                            alert(wpProQuizLocalize.no_answer_msg);
                            return false;
                        }

                        return true;
                    }
                };

                var formListener = function () {
                    $('#wpProQuiz_tip').change(function () {
                        global.displayChecked(this, $('#wpProQuiz_tipBox'));
                    }).change();

                    $('#wpProQuiz_correctSameText').change(function () {
                        global.displayChecked(this, $('#wpProQuiz_incorrectMassageBox'), true);
                    }).change();

                    $('input[name="answerType"]').click(function () {
                        elements.answerChildren.hide();
                        var v = this.value;

                        if (v == 'single') {
                            $('#singleChoiceOptions').show();
                            $('input[name="disableCorrect"]').change();
                        } else {
                            $('#singleChoiceOptions').hide();
                            $('.classic_answer .wpProQuiz_classCorrect').parent().parent().show();
                        }

                        if (v == 'single' || v == 'multiple') {
                            var type = (v == 'single') ? 'radio' : 'checkbox';
                            v = 'classic_answer';

                            $('.wpProQuiz_classCorrect').each(function () {
                                $("<input type=" + type + " />")
                                    .attr({
                                        name: this.name,
                                        value: this.value,
                                        checked: this.checked
                                    })
                                    .addClass('wpProQuiz_classCorrect wpProQuiz_checkbox')
                                    .insertBefore(this);
                            }).remove();
                        }

                        filter = (validate[v] != undefined) ? validate[v] : $.noop();

                        $('.' + v).show();
                    });

                    $('input[name="answerType"]:checked').click();

                    $('.deleteAnswer').click(methode.answerRemove);

                    $('.addAnswer').click(function () {
                        if(!checkThisThatAnswerCount()){
                            //Validation to not allowed to add more than two answers.
                            alert(wpProQuizLocalize.thisThatAnsCount); 
                            return false;
                        }

                        var ul = $(this).siblings('ul');
                        var clone = ul.find('li:eq(0)').clone();

                        clone.find('.wpProQuiz_checkbox').removeAttr('checked');
                        clone.find('.wpProQuiz_text').val('');
                        clone.find('.wpProQuiz_points').val(1);
                        clone.find('.deleteAnswer').click(methode.answerRemove);
                        clone.find('.addMedia').click(methode.addMediaClick);

                        clone.appendTo(ul);

                        return false;
                    });

                    $('.sort_answer ul, .classic_answer ul, .matrix_sort_answer ul').sortable({
                        handle: '.wpProQuiz_move'
                    });

                    $('#saveQuestion').click(function () {
                        if (!methode.globalValidate()) {
                            return false;
                        }

                        methode.generateArrayIndex();

                        return true;
                    });

                    $(elements.pointsModus).change(function () {
                        global.displayChecked(this, $('.wpProQuiz_answerPoints'));
                        global.displayChecked(this, $('#wpProQuiz_showPointsBox'));
                        global.displayChecked(this, elements.gPoints, false, true);
                        global.displayChecked(this, $('input[name="answerPointsDiffModusActivated"]'), true, true);

                        if (this.checked) {
                            $('input[name="answerPointsDiffModusActivated"]').change();
                            $('input[name="disableCorrect"]').change();
                        } else {
                            $('.classic_answer .wpProQuiz_classCorrect').parent().parent().show();
                            $('input[name="disableCorrect"]').attr('disabled', 'disabled');
                        }
                    }).change();

                    $('select[name="category"]').change(function () {
                        var $this = $(this);
                        var box = $('#categoryAddBox').hide();

                        if ($this.val() == "-1") {
                            box.show();
                        }

                    }).change();

                    $('#categoryAddBtn').click(function () {
                        methode.addCategory();
                    });

                    $('.addMedia').click(methode.addMediaClick);

                    $('input[name="answerPointsDiffModusActivated"]').change(function () {
                        global.displayChecked(this, $('input[name="disableCorrect"]'), true, true);

                        if (this.checked)
                            $('input[name="disableCorrect"]').change();
                        else
                            $('.classic_answer .wpProQuiz_classCorrect').parent().parent().show();
                    }).change();

                    $('input[name="disableCorrect"]').change(function () {
                        global.displayChecked(this, $('.classic_answer .wpProQuiz_classCorrect').parent().parent(), true);
                    }).change();

                    $('#clickPointDia').click(function () {
                        $('.pointDia').toggle('fast');

                        return false;
                    });

                    $('input[name="template"]').click(function (e) {
                        if ($('select[name="templateSaveList"]').val() == '0') {
                            if (global.isEmpty($('input[name="templateName"]').val())) {
                                alert(wpProQuizLocalize.temploate_no_name);

                                e.preventDefault();
                                return false;
                            }
                        }

                        methode.generateArrayIndex();
                    });

                    $('select[name="templateSaveList"]').change(function () {
                        var $templateName = $('input[name="templateName"]');

                        if ($(this).val() == '0') {
                            $templateName.show();
                        } else {
                            $templateName.hide();
                        }
                    }).change();
                };

                var init = function () {
                    elements.answerChildren.hide();

                    formListener();
                };

                init();
            },

            statistic: function () {

                var methode = this;

                var quizId = $('#quizId').val();

                var currentTab = 'users';

                var elements = {
                    currentPage: $('#wpProQuiz_currentPage'),
                    pageLeft: $('#wpProQuiz_pageLeft'),
                    pageRight: $('#wpProQuiz_pageRight'),
                    testSelect: $('#testSelect')

                };

                methode = {
                    loadStatistic: function (userId, callback) {
                        var data = {
                            userId: userId
                        };

                        global.ajaxPost('statisticLoad', data, function (json) {

                        });
                    },

                    loadUsersStatistic: function () {
                        //var userId = $('#userSelect').val();
                        //
                        //var data = {
                        //    userId: userId,
                        //    quizId: quizId,
                        //    testId: $('#testSelect').val()
                        //};
                        //
                        //methode.toggleLoadBox(false);
                        //
                        //global.ajaxPost('statisticLoad', data, function (json) {
                        //    $.each(json.question, function () {
                        //        var $tr = $('#wpProQuiz_tr_' + this.questionId);
                        //
                        //        methode.setStatisticData($tr, this);
                        //    });
                        //
                        //    $.each(json.category, function (i, v) {
                        //        var $tr = $('#wpProQuiz_ctr_' + i);
                        //
                        //        methode.setStatisticData($tr, v);
                        //    });
                        //
                        //    $('#testSelect option:gt(0)').remove();
                        //    var $testSelect = $('#testSelect');
                        //
                        //    $.each(json.tests, function () {
                        //        var $option = $(document.createElement('option'));
                        //
                        //        $option.val(this.id);
                        //        $option.text(this.date);
                        //
                        //        if (json.testId == this.id)
                        //            $option.attr('selected', true);
                        //
                        //        $testSelect.append($option);
                        //    });
                        //
                        //    methode.parseFormData(json.formData);
                        //
                        //    methode.toggleLoadBox(true);
                        //});
                    },

                    loadUsersStatistic_: function (userId, testId) {

                        var data = {
                            userId: userId,
                            quizId: quizId,
                            testId: testId
                        };

                        methode.toggleLoadBox(false);

                        global.ajaxPost('statisticLoad', data, function (json) {
                            $.each(json.question, function () {
                                var $tr = $('#wpProQuiz_tr_' + this.questionId);

                                methode.setStatisticData($tr, this);
                            });

                            $.each(json.category, function (i, v) {
                                var $tr = $('#wpProQuiz_ctr_' + i);

                                methode.setStatisticData($tr, v);
                            });

                            $('#testSelect option:gt(0)').remove();
                            var $testSelect = $('#testSelect');

                            $.each(json.tests, function () {
                                var $option = $(document.createElement('option'));

                                $option.val(this.id);
                                $option.text(this.date);

                                if (json.testId == this.id)
                                    $option.attr('selected', true);

                                $testSelect.append($option);
                            });

                            methode.parseFormData(json.formData);

                            $('#userSelect').val(userId);
                            $('#testSelect').val(testId);

                            methode.toggleLoadBox(true);
                        });
                    },

                    parseFormData: function (data) {
                        var $formBox = $('#wpProQuiz_form_box');

                        if (data == null) {
                            $formBox.hide();
                            return;
                        }

                        $.each(data, function (i, v) {
                            $('#form_id_' + i).text(v);
                        });

                        $formBox.show();
                    },

                    setStatisticData: function ($o, v) {
                        $o.find('.wpProQuiz_cCorrect').text(v.correct);
                        $o.find('.wpProQuiz_cIncorrect').text(v.incorrect);
                        $o.find('.wpProQuiz_cTip').text(v.hint);
                        $o.find('.wpProQuiz_cPoints').text(v.points);
                        $o.find('.wpProQuiz_cResult').text(v.result);
                        $o.find('.wpProQuiz_cTime').text(v.questionTime);
                        $o.find('.wpProQuiz_cCreateTime').text(v.date);
                    },

                    toggleLoadBox: function (show) {
                        var $loadBox = $('#wpProQuiz_loadData');
                        var $content = $('#wpProQuiz_content');

                        if (show) {
                            $loadBox.hide();
                            $content.show();
                        } else {
                            $content.hide();
                            $loadBox.show();
                        }
                    },

                    reset: function (type) {
                        var userId = $('#userSelect').val();

                        if (!confirm(wpProQuizLocalize.reset_statistics_msg)) {
                            return;
                        }

                        var data = {
                            quizId: quizId,
                            userId: userId,
                            testId: elements.testSelect.val(),
                            type: type
                        };

                        methode.toggleLoadBox(false);

                        global.ajaxPost('statisticReset', data, function () {
                            methode.loadUsersStatistic();
                        });
                    },

                    loadStatisticOverview: function (nav) {

                        var data = {
                            quizId: quizId,
                            pageLimit: $('#wpProQuiz_pageLimit').val(),
                            onlyCompleted: Number($('#wpProQuiz_onlyCompleted').is(':checked')),
                            page: elements.currentPage.val(),
                            nav: Number(nav)
                        };

                        methode.toggleLoadBox(false);

                        global.ajaxPost('statisticLoadOverview', data, function (json) {
                            var $body = $('#wpProQuiz_statistics_overview_data');
                            var $tr = $body.children();
                            var $c = $tr.first().clone();

                            $tr.slice(1).remove();

                            $.each(json.items, function () {
                                var clone = $c.clone();

                                methode.setStatisticData(clone, this);

                                clone.find('a').text(this.userName).data('userId', this.userId).click(function () {
                                    $('#userSelect').val($(this).data('userId'));

                                    $('#wpProQuiz_typeUser').click();

                                    return false;
                                });

                                clone.show().appendTo($body);
                            });

                            $c.remove();

                            methode.toggleLoadBox(true);

                            if (json.page != undefined)
                                methode.handleNav(json.page);
                        });

                    },

                    handleNav: function (nav) {
                        var $p = $('#wpProQuiz_currentPage').empty();

                        for (var i = 1; i <= nav; i++) {
                            $(document.createElement('option'))
                                .val(i)
                                .text(i)
                                .appendTo($p);
                        }

                        methode.checkNavBar();
                    },

                    checkNavBar: function () {
                        var n = elements.currentPage.val();

                        if (n == 1) {
                            elements.pageLeft.hide();
                        } else {
                            elements.pageLeft.show();
                        }

                        if (n == elements.currentPage.children().length) {
                            elements.pageRight.hide();
                        } else {
                            elements.pageRight.show();
                        }
                    },

                    refresh: function () {
                        if (currentTab == 'users') {
                            methode.loadUsersStatistic();
                        } else if (currentTab == 'formOverview') {
                            methode.loadFormsOverview(true);
                        } else {
                            methode.loadStatisticOverview(true);
                        }
                    },

                    loadFormsOverview: function (nav) {
                        var data = {
                            quizId: quizId,
                            pageLimit: $('#wpProQuiz_fromPageLimit').val(),
                            onlyUser: $('#wpProQuiz_formUser').val(),
                            page: $('#wpProQuiz_formCurrentPage').val(),
                            nav: Number(nav)
                        };

                        methode.toggleLoadBox(false);

                        global.ajaxPost('statisticLoadFormOverview', data, function (json) {
                            var $body = $('#wpProQuiz_statistics_form_data');
                            var $tr = $body.children();
                            var $c = $tr.first().clone();

                            $tr.slice(1).remove();

                            $.each(json.items, function () {
                                var clone = $c.clone();

                                methode.setStatisticData(clone, this);

                                clone.find('a').text(this.userName).data('userId', this.userId).data('testId', this.testId).click(function () {
                                    methode.switchTabOnLoad('users');
                                    methode.loadUsersStatistic_($(this).data('userId'), $(this).data('testId'));

                                    return false;
                                });

                                clone.show().appendTo($body);
                            });

                            $c.remove();

                            methode.toggleLoadBox(true);

                            if (json.page != undefined)
                                methode.handleFormNav(json.page);
                        });
                    },

                    handleFormNav: function (nav) {
                        var $p = $('#wpProQuiz_formCurrentPage').empty();

                        for (var i = 1; i <= nav; i++) {
                            $(document.createElement('option'))
                                .val(i)
                                .text(i)
                                .appendTo($p);
                        }

                        methode.checkFormNavBar();
                    },

                    checkFormNavBar: function () {
                        var n = $('#wpProQuiz_formCurrentPage').val();

                        if (n == 1) {
                            $('#wpProQuiz_formPageLeft').hide();
                        } else {
                            $('#wpProQuiz_formPageLeft').show();
                        }

                        if (n == $('#wpProQuiz_formCurrentPage').children().length) {
                            $('#wpProQuiz_formPageRight').hide();
                        } else {
                            $('#wpProQuiz_formPageRight').show();
                        }
                    },

                    switchTabOnLoad: function (name) {
                        $('.wpProQuiz_tab').removeClass('button-primary').addClass('button-secondary');
                        $('.wpProQuiz_tabContent').hide();

                        var $this = $('#wpProQuiz_typeOverview');

                        if (name == 'users') {
                            currentTab = 'users';
                            $('#wpProQuiz_tabUsers').show();
                            $this = $('#wpProQuiz_typeUser');
                        } else if (name == 'formOverview') {
                            currentTab = 'formOverview';
                            $('#wpProQuiz_tabFormOverview').show();
                            $this = $('#wpProQuiz_typeForm');
                        } else {
                            currentTab = 'overview';
                            $('#wpProQuiz_tabOverview').show();
                        }

                        $this.removeClass('button-secondary').addClass('button-primary');
                    }
                };

                var init = function () {

                    $('#userSelect, #testSelect').change(function () {
                        methode.loadUsersStatistic();
                    });

                    $('.wpProQuiz_update').click(function () {
                        methode.refresh();
                    });

                    $('#wpProQuiz_reset').click(function () {
                        methode.reset(0);
                    });

                    $('#wpProQuiz_resetUser').click(function () {
                        methode.reset(1);
                    });

                    $('.wpProQuiz_resetComplete').click(function () {
                        methode.reset(2);
                    });

                    $('.wpProQuiz_tab').click(function () {
                        var $this = $(this);

                        $('.wpProQuiz_tab').removeClass('button-primary').addClass('button-secondary');
                        $this.removeClass('button-secondary').addClass('button-primary');
                        $('.wpProQuiz_tabContent').hide();

                        if ($this.attr('id') == 'wpProQuiz_typeUser') {
                            currentTab = 'users';
                            $('#wpProQuiz_tabUsers').show();
                            methode.loadUsersStatistic();
                        } else if ($this.attr('id') == 'wpProQuiz_typeForm') {
                            currentTab = 'formOverview';
                            $('#wpProQuiz_tabFormOverview').show();
                            methode.loadFormsOverview(true);
                        } else {
                            currentTab = 'overview';
                            $('#wpProQuiz_tabOverview').show();
                            methode.loadStatisticOverview(true);
                        }

                        return false;
                    });

                    $('#wpProQuiz_onlyCompleted').change(function () {
                        elements.currentPage.val(1);
                        methode.loadStatisticOverview(true);
                    });

                    $('#wpProQuiz_pageLimit').change(function () {
                        elements.currentPage.val(1);
                        methode.loadStatisticOverview(true);
                    });

                    elements.pageLeft.click(function () {
                        elements.currentPage.val(Number(elements.currentPage.val()) - 1);
                        methode.loadStatisticOverview(false);
                        methode.checkNavBar();
                    });

                    elements.pageRight.click(function () {
                        elements.currentPage.val(Number(elements.currentPage.val()) + 1);
                        methode.loadStatisticOverview(false);
                        methode.checkNavBar();
                    });

                    elements.currentPage.change(function () {
                        methode.loadStatisticOverview(false);
                        methode.checkNavBar();
                    });

                    $('#wpProQuiz_formUser, #wpProQuiz_fromPageLimit').change(function () {
                        $('#wpProQuiz_formCurrentPage').val(1);
                        methode.loadFormsOverview(true);
                    });

                    $('#wpProQuiz_formPageLeft').click(function () {
                        $('#wpProQuiz_formCurrentPage').val(Number(elements.currentPage.val()) - 1);
                        methode.loadFormsOverview(false);
                        methode.checkFormNavBar();
                    });

                    $('#wpProQuiz_formPageRight').click(function () {
                        $('#wpProQuiz_formCurrentPage').val(Number(elements.currentPage.val()) + 1);
                        methode.loadFormsOverview(false);
                        methode.checkFormNavBar();
                    });

                    $('#wpProQuiz_formCurrentPage').change(function () {
                        methode.loadFormsOverview(false);
                        methode.checkFormNavBar();
                    });

                    methode.loadUsersStatistic();
                };

                init();
            },

            statisticNew: function () {
                var quizId = $('#quizId').val();
                var quizName = $('#quizName').val();

                if(checkQuizEditPage()){
                    var quizId = $('input[name="ajax_quiz_id"]').val();
                    var quizName = wpProQuizLocalize.selectedQuizType;
                }
                var historyNavigator = null;
                var overviewNavigator = null;

                var historyFilter = {
                    data: {
                        quizId: quizId,
                        quizName: quizName,
                        users: -1,
                        pageLimit: 100,
                        dateFrom: 0,
                        dateTo: 0,
                        generateNav: 0
                    },

                    changeFilter: function () {
                        var getTime = function (p) {
                            var date = p.datepicker('getDate');

                            return (date === null || checkUndefinedInput()) ? 0 : date.getTime() / 1000;
                        };

                        $.extend(this.data, {
                            users: $('#wpProQuiz_historyUser').val(),
                            pageLimit: $('#wpProQuiz_historyPageLimit').val(),
                            dateFrom: getTime($('#datepickerFrom')),
                            dateTo: getTime($('#datepickerTo')),
                            generateNav: 1
                        });

                        return this.data;
                    }
                };

                var overviewFilter = {
                    data: {
                        pageLimit: 100,
                        onlyCompleted: 0,
                        generateNav: 0,
                        quizId: quizId
                    },

                    changeFilter: function () {
                        $.extend(this.data, {
                            pageLimit: $('#wpProQuiz_overviewPageLimit').val(),
                            onlyCompleted: Number($('#wpProQuiz_overviewOnlyCompleted').is(':checked')),
                            generateNav: 1
                        });
                    }
                };
                //approve code starts
                var approveMethode = {
                    approveUserStatistic: function (refId, userId,approveStatus) {
                        if(1 == approveStatus) {
                            if (!confirm(wpProQuizLocalize.disapprove_statistics_msg))
                                return false;
                        }
                        else {
                            if (!confirm(wpProQuizLocalize.approve_statistics_msg))
                                return false;
                        }
                        var data = {
                            refId: refId,
                            userId: userId,
                            quizId: quizId,
                            type: 0
                        };

                        global.ajaxPost('statisticResetApproveNew', data, function () {
                            $('#wpProQuiz_user_overlay').hide();

                            statisticsPageEnable(historyFilter,methode,overviewFilter);
                        });
                    }
                };
                //approve code ends

                var deleteMethode = {
                    deleteUserStatistic: function (refId, userId) {
                        if (!confirm(wpProQuizLocalize.reset_statistics_msg))
                            return false;

                        var data = {
                            refId: refId,
                            userId: userId,
                            quizId: quizId,
                            type: 0
                        };

                        global.ajaxPost('statisticResetNew', data, function () {
                            $('#wpProQuiz_user_overlay').hide();

                            statisticsPageEnable(historyFilter,methode,overviewFilter);
                        });
                    },

                    deleteAll: function () {
                        if (!confirm(wpProQuizLocalize.reset_statistics_msg))
                            return false;

                        var data = {
                            quizId: quizId,
                            type: 1
                        };

                        global.ajaxPost('statisticResetNew', data, function () {
                            historyFilter.changeFilter();
                            methode.loadHistoryAjax();

                            overviewFilter.changeFilter();
                            methode.loadOverviewAjax();
                        });
                    }
                };

                var methode = {

                    loadExportCsvAjax : function () {
                        var data = $.extend({
                            page: historyFilter.data.generateNav ? 1 : historyNavigator.getCurrentPage()
                        }, historyFilter.data);


                        global.ajaxPostCsv('statisticExportCsv', data,function (json) {

                        });

                    },

                    loadHistoryAjax: function () {
                        var data = $.extend({
                            page: historyFilter.data.generateNav ? 1 : historyNavigator.getCurrentPage()
                        }, historyFilter.data);

                        methode.loadBox(true);
                        var content = $('#wpProQuiz_historyLoadContext').hide();

                        global.ajaxPost('statisticLoadHistory', data, function (json) {
                            content.html(json.html).show();

                            if (json.navi)
                                historyNavigator.setNumPage(json.navi);

                            historyFilter.data.generateNav = 0;

                            content.find('.user_statistic').click(function () {
                                methode.loadUserAjax(0, $(this).data('ref_id'), false);

                                return false;
                            });

                            content.find('.wpProQuiz_delete').click(function () {
                                deleteMethode.deleteUserStatistic($(this).parents('tr').find('.user_statistic').data('ref_id'), 0);

                                return false;
                            });

                            content.find('.wpProQuiz_approve').click(function () {
                                approveMethode.approveUserStatistic( $(this).parents('tr').find('.user_statistic').data('ref_id'),0,$(this).attr('dataApproveStatus'));

                                return false;
                            });

                            methode.loadBox(false);
                        });

                    },

                    loadUserAjax: function (userId, refId, avg) {
                        $('#wpProQuiz_user_overlay, #wpProQuiz_loadUserData').show();

                        var content = $('#wpProQuiz_user_content').hide();

                        var data = {
                            quizId: quizId,
                            userId: userId,
                            refId: refId,
                            avg: Number(avg)
                        };

                        global.ajaxPost('statisticLoadUser', data, function (json) {
                            content.html(json.html);

                            content.find('.wpProQuiz_update').click(function () {
                                methode.loadUserAjax(userId, refId, avg);

                                return false;
                            });

                            content.find('#wpProQuiz_resetUserStatistic').click(function () {
                                deleteMethode.deleteUserStatistic(refId, userId);
                            });

                            content.find('.statistic_data').click(function () {
                                $(this).parents('tr').next().toggle('fast');

                                return false;
                            });

                            $('#wpProQuiz_loadUserData').hide();
                            content.show();
                        });

                    },

                    loadBox: function (show, contain) {
                        if (show)
                            $('#wpProQuiz_loadDataHistory').show();
                        else
                            $('#wpProQuiz_loadDataHistory').hide();

                    },

                    loadOverviewAjax: function () {
                        var data = $.extend({
                            page: overviewFilter.data.generateNav ? 1 : overviewNavigator.getCurrentPage()
                        }, overviewFilter.data);

                        $('#wpProQuiz_loadDataOverview').show();

                        var content = $('#wpProQuiz_overviewLoadContext').hide();

                        global.ajaxPost('statisticLoadOverviewNew', data, function (json) {
                            content.html(json.html).show();

                            if (json.navi)
                                overviewNavigator.setNumPage(json.navi);

                            overviewFilter.data.generateNav = 0;

                            content.find('.user_statistic').click(function () {
                                methode.loadUserAjax($(this).data('user_id'), 0, true);

                                return false;
                            });

                            content.find('.wpProQuiz_delete').click(function () {
                                deleteMethode.deleteUserStatistic(0, $(this).parents('tr').find('.user_statistic').data('user_id'));

                                return false;
                            });

                            content.find('.wpProQuiz_approve').click(function () {
                                approveMethode.approveUserStatistic(0, $(this).parents('tr').find('.user_statistic').data('user_id'));

                                return false;
                            });

                            $('#wpProQuiz_loadDataOverview').hide();
                        });
                    }
                };

                var init = function () {
                    historyNavigator = new Navigator($('#historyNavigation'), {
                        onChange: function () {
                            methode.loadHistoryAjax();
                        }
                    });

                    overviewNavigator = new Navigator($('#overviewNavigation'), {
                        onChange: function () {
                            methode.loadOverviewAjax();
                        }
                    });

                    $('#datepickerFrom').datepicker({
                        closeText: wpProQuizLocalize.closeText,
                        currentText: wpProQuizLocalize.currentText,
                        monthNames: wpProQuizLocalize.monthNames,
                        monthNamesShort: wpProQuizLocalize.monthNamesShort,
                        dayNames: wpProQuizLocalize.dayNames,
                        dayNamesShort: wpProQuizLocalize.dayNamesShort,
                        dayNamesMin: wpProQuizLocalize.dayNamesMin,
                        dateFormat: wpProQuizLocalize.dateFormat,
                        firstDay: wpProQuizLocalize.firstDay,

                        changeMonth: true,
                        onClose: function (selectedDate) {
                            $('#datepickerTo').datepicker('option', 'minDate', selectedDate);
                        }
                    });

                    $('#datepickerTo').datepicker({
                        closeText: wpProQuizLocalize.closeText,
                        currentText: wpProQuizLocalize.currentText,
                        monthNames: wpProQuizLocalize.monthNames,
                        monthNamesShort: wpProQuizLocalize.monthNamesShort,
                        dayNames: wpProQuizLocalize.dayNames,
                        dayNamesShort: wpProQuizLocalize.dayNamesShort,
                        dayNamesMin: wpProQuizLocalize.dayNamesMin,
                        dateFormat: wpProQuizLocalize.dateFormat,
                        firstDay: wpProQuizLocalize.firstDay,

                        changeMonth: true,
                        onClose: function (selectedDate) {
                            $('#datepickerFrom').datepicker('option', 'maxDate', selectedDate);
                        }
                    });

                    $('#filter').click(function () {
                        historyFilter.changeFilter();
                        methode.loadHistoryAjax();
                    });

                    $('#export_csv').click(function () {
                       methode.loadExportCsvAjax();
                    });

                    $('#wpProQuiz_overlay_close').click(function () {
                        $('#wpProQuiz_user_overlay').hide();
                    });

                    $('#wpProQuiz_tabHistory .wpProQuiz_update').click(function () {
                        historyFilter.changeFilter();
                        methode.loadHistoryAjax();

                        return false;
                    });

                    $('#wpProQuiz_tabOverview .wpProQuiz_update').click(function () {
                        overviewFilter.changeFilter();
                        methode.loadOverviewAjax();

                        return false;
                    });

                    $('.wpProQuiz_resetComplete').click(function () {
                        deleteMethode.deleteAll();

                        return false;
                    });

                    $('#overviewFilter').click(function () {
                        overviewFilter.changeFilter();
                        methode.loadOverviewAjax();
                    });

                    statisticsPageEnable(historyFilter,methode,overviewFilter);
                };

                init();
            }
        };

        var init = function () {
            tabWrapper();

            var m = $.noop;

            if ($('.wpProQuiz_questionEdit').length) {
                m = module.questionEdit;
            } else if ($('.wpProQuiz_globalSettings').length) {
                m = module.gobalSettings;
            } else if ($('.wpProQuiz_statistics').length) {
                m = module.statistic;
            } else if ($('.wpProQuiz_statisticsNew').length || checkQuizEditPage()) {
                m = module.statisticNew;
            }

            m();

            $('.wpProQuiz_demoImgBox a').mouseover(function (e) {
                var $this = $(this);
                var d = $(document).width();
                var img = $this.siblings().outerWidth(true);

                if (e.pageX + img > d) {
                    var v = d - (e.pageX + img + 30);
                    $(this).next().css('left', v + "px");
                }

                $(this).next().show();

            }).mouseout(function () {
                $(this).next().hide();
            }).click(function () {
                return false;
            });
        };

        init();
    }

    /**
     * Check if current page is quiz edit or not
     * @return bool
     */
    function checkQuizEditPage()
    {
        return $('.wpProQuiz_quizEdit').length;
    }

    /**
     * Applying statistics functions
     * @return void
     */
    function statisticsPageEnable(historyFilter,methode,overviewFilter)
    {
        if(!checkQuizEditPage()){
            historyFilter.changeFilter();
        }
            methode.loadHistoryAjax();

        if(!checkQuizEditPage()){
            overviewFilter.changeFilter();
            methode.loadOverviewAjax();
        }
    }

    WpProQuiz_Admin();

    function Navigator(obj, option) {
        var defaultOption = {
            onChange: null
        };

        var elements = {
            contain: null,
            pageLeft: null,
            pageRight: null,
            currentPage: null
        };

        var checkNavBar = function () {
            var num = elements.currentPage.children().length;
            var cur = Number(elements.currentPage.val());

            elements.pageLeft.hide();
            elements.pageRight.hide();

            if (cur > 1)
                elements.pageLeft.show();

            if ((cur + 1) <= num)
                elements.pageRight.show();
        };

        var init = function () {
            $.extend(elements, {
                contain: obj,
                pageLeft: obj.find('.navigationLeft'),
                pageRight: obj.find('.navigationRight'),
                currentPage: obj.find('.navigationCurrentPage')
            });

            $.extend(defaultOption, option);

            elements.pageLeft.click(function () {
                elements.currentPage.val(Number(elements.currentPage.val()) - 1);
                checkNavBar();

                if (defaultOption.onChange)
                    defaultOption.onChange(elements.currentPage.val());
            });

            elements.pageRight.click(function () {
                elements.currentPage.val(Number(elements.currentPage.val()) + 1);
                checkNavBar();

                if (defaultOption.onChange)
                    defaultOption.onChange(elements.currentPage.val());
            });

            elements.currentPage.change(function () {
                checkNavBar();

                if (defaultOption.onChange)
                    defaultOption.onChange(elements.currentPage.val());
            });
        };

        this.getCurrentPage = function () {
            return elements.currentPage.val();
        }

        this.setNumPage = function (num) {
            elements.currentPage.empty();

            for (var i = 1; i <= num; i++) {
                $(document.createElement('option'))
                    .val(i)
                    .text(i)
                    .appendTo(elements.currentPage);
            }

            checkNavBar();
        }

        init();
    }

    var quiz_id=$('#quiz_id').html();
    $('.quizStyle').change(function(){
        startQuizLoader();

        var this_css=$(this);
        var css_file_name=this_css.val();
        var custom_style_path=$('#custom_style_path').html()+'/'+css_file_name;
        var selectedStyle=$('.quizStyle option:selected').val();

        var data = {
            quiz_id: quiz_id
        };

        var d = {
            action: 'wp_pro_quiz_admin_ajax',
            func: 'getQuizStyleSheet',
            data: data
        };

        $.post(ajaxurl, d, function (json) {
            var strQuizCss=""; var strQuizStyleName="";

            if(json['strQuizCss'] && json['strQuizStyleName'] && json['strQuizStyleName']==selectedStyle){
                var strQuizCss=json['strQuizCss'];
                var strQuizStyleName=json['strQuizStyleName'];
            }

            stopQuizLoader();
            if(strQuizCss!=""){
                $('.custom_quiz_box').val(strQuizCss);
            }else{
                $.get( custom_style_path, function( strQuizCss ) {
                    $('.custom_quiz_box').val(strQuizCss);
                });
            }
        }
        , 'json');
    });



    /**
     * Starts loader and disable save button
     * @return void
     */
    function startQuizLoader(){
        $('.loaderWrapper').css('display','block');
        $('#wpProQuiz_save').prop("disabled", true);
    }

    /**
     * Stops loader and enable save button
     * @return void
     */
    function stopQuizLoader(){
        $('.loaderWrapper').css('display','none');
        $('#wpProQuiz_save').prop("disabled", false);
    }

    if(quiz_id > 0 && quiz_id != 'undefined'){
        //Following events perform in edit quiz page for question overview
        $('.tablenav').remove();
        $('#questions').find('#_wpnonce').remove();
        $('#questions').find('input[name="_wp_http_referer"]').remove();
        $('#questions').find('tfoot').remove();
    }

    function getURLParameter(sParam){
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        
        for (var iParamIndex = 0; iParamIndex < sURLVariables.length; iParamIndex++){
            var sParameterName = sURLVariables[iParamIndex].split('=');
            if (sParameterName[0] == sParam){
                return sParameterName[1];
            }
        }
    }

    var action = getURLParameter('action');
    if(action != 'addEdit'){
        localStorage.setItem('previousSelectedTab', '#content'); //Reset tab value
        localStorage.setItem('accordionId', 'quiz_description'); //Reset accordion value
    }else{
        $(document).ready(function() {
            var accordionId = localStorage.getItem('accordionId'); //Get accordion value
            if(accordionId != ''){
                //Open accordion 
                $('#'+accordionId).trigger('click');
            }
        });
    }

    function checkUndefinedInput(input){
        return typeof input == 'undefined';
    }

    /* Check whether current quiz type is photo contest or not
    *   return bool
    */
    function checkPhotoGiveAwayQuiz(){
        var catValue = $('#categoryValue').html();
        if(wpProQuizLocalize.selectedQuizTypeValue || catValue){
            var catValue = wpProQuizLocalize.selectedQuizTypeValue ? wpProQuizLocalize.selectedQuizTypeValue : catValue;
            return (catValue == wpProQuizLocalize.photoContest || catValue == wpProQuizLocalize.giveawayContest);
        }
        var catValue = $('.quizType option:selected').attr('catValue');
        var photoContest= (!checkUndefinedInput(catValue) && catValue == wpProQuizLocalize.photoContest) ? true : false;
        var giveAwayContest= (!checkUndefinedInput(catValue) && catValue == wpProQuizLocalize.giveawayContest) ? true : false;
        return(photoContest || giveAwayContest);
    }

    /* Check whether current quiz type is ThisThatQuiz or not
     *   return bool
     */
    function checkThisThatQuiz(){
        if(wpProQuizLocalize.selectedQuizTypeValue){
            return wpProQuizLocalize.selectedQuizTypeValue == wpProQuizLocalize.thisThatQuiz;
        }
        var catValue = $('.quizType option:selected').attr('catValue');
        return (!checkUndefinedInput(catValue) && catValue == wpProQuizLocalize.thisThatQuiz) ? true : false;
    }

    /* Check whether this or that quiz answer type
     *   return bool
     */
    function checkThisThatAnswerCount(checkOnSave){
        if(checkThisThatQuiz()){
            var currentRows = $('.answerList .singleChoiceRow').length;
            var newRowsAdded = 1;

            if(checkOnSave){
                // While saving question
                if(currentRows != 2)
                    return false;
            }else{
                // While adding answer
                if(currentRows + newRowsAdded > 2)
                    return false;
            }
        }
        return true;
    }

    /* Check whether any this or that quiz answer text is null or not
     *   return bool
     */
    function checkThisThatAnswerText(){
        if(checkThisThatQuiz()){
            var checkStatus = 0;
            $('.classic_answer .answerList').children().each(function () {
                var t = $(this);
                var ansText = (t.find('textarea[name="answerData[][answer]"]').val());

                if(!ansText)
                    checkStatus++;
            });
            if(checkStatus)
                return false;
        }
        return true;
    }

    /* Fetch contest email template
     * return void
     */
    function setPCEmailTemplate(pcStatus){
        
        var ajax_data = {
            pcStatus: pcStatus
        };
        var d = {
            action: 'wp_pro_quiz_admin_ajax',
            func: 'setPCEmailTemplate',
            data: ajax_data
        };
        $.post(ajaxurl, d, function (json) {
            // Check if system can update email editors
            if(!checkUndefinedInput(tinymce.get('adminEmailEditor').parser) && 
                !checkUndefinedInput(tinymce.get('userEmailEditor').parser)){
                if (json['adminEmailTemplate'] && json['userEmailTemplate']) {
                    // Update the new email content to editor
                    tinymce.get('adminEmailEditor').setContent(json['adminEmailTemplate']);
                    tinymce.get('userEmailEditor').setContent(json['userEmailTemplate']);
                }
            }
            if(json['adminEmailSubject'] && json['userEmailSubject']){
                // Update the new email subject to template
                $('.adminEmailSubject').val(json['adminEmailSubject']);
                $('.userEmailSubject').val(json['userEmailSubject']);
            }
        }
        , 'json');
    }
});
