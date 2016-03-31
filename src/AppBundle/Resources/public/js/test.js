var previous_width = 0;
var mobile_state = 0;
var nav_state = 0;
var global_units = 1;
var $user_units = 1;
var $si_units = 1;
var $us_units = 0;
var $user_units_weight = 1;


function set_unit(choice) {
    var previous_setting = $(".unit").val();
    if (choice != 'si' && choice != 'imperial') {
        if (previous_setting == 'si' || previous_setting == 'imperial') {
            choice = previous_setting;
        } else {
            choice = 'imperial';
        }
    }
    $(".unit").val(choice);
    //if (previous_setting != choice) {
    //    $.ajax({url: "/calculate/unit/" + choice, success: function (response) {
    //    }});
    //}
    if (global_units == 1) {
        if (choice == 'si') {
            $(".unit_si").removeClass("unit_off").addClass("unit_on");
            $(".unit_imperial").removeClass("unit_on").addClass("unit_off");
        } else {
            $(".unit_si").removeClass("unit_on").addClass("unit_off");
            $(".unit_imperial").removeClass("unit_off").addClass("unit_on");
        }
        if (choice == 'si') {
            $(".unit_type_primary").each(function() {
                var question_number = $(this).attr('id');
                var unit_factor = $(this).val();
                question_number = question_number.replace("qutp_", "");
                $(".question_" + question_number + "_units").val(unit_factor);
            });
            $(".is_mc").each(function() {
                var question_number = $(this).val();
                $(".question_" + question_number + "_mc_primary").removeClass("hide").addClass("show");
                $(".question_" + question_number + "_mc_secondary").removeClass("show").addClass("hide");
            });
            $user_units = 1;
            $user_units_weight = 1;
        } else {
            $(".unit_type_secondary").each(function() {
                var question_number = $(this).attr('id');
                var unit_factor = $(this).val();
                question_number = question_number.replace("quts_", "");
                $(".question_" + question_number + "_units").val(unit_factor);
            });
            $(".is_mc").each(function() {
                var question_number = $(this).val();
                var check_inner_html = '';
                $(".question_" + question_number + "_mc_secondary").each(function() {
                    var check_html = $(this).html();
                    if (check_html != '') {
                        check_inner_html = check_html;
                    }
                });
                if (check_inner_html != '') {
                    $(".question_" + question_number + "_mc_primary").removeClass("show").addClass("hide");
                }
                $(".question_" + question_number + "_mc_secondary").removeClass("hide").addClass("show");
            });
            $user_units = 0;
            $user_units_weight = 0;
        }
    }
}
function try_compute() {
    var check = $(".loaded").html();
    if (check == '1') {
        compute();
    }
}
function validate_entry(position, value, original_value) {
    var min_value = $(".question_" + position + "_min_value").val();
    var max_value = $(".question_" + position + "_max_value").val();
    var min_value_msg = $(".question_" + position + "_min_value_msg").val();
    var max_value_msg = $(".question_" + position + "_max_value_msg").val();
    if (original_value != undefined && original_value != '') {
        if (min_value) {
            if (Number(min_value) > Number(value)) {
                $(".question_" + position + "_error").removeClass("hide").addClass("show").html(min_value_msg);
                return;
            } else {
                $(".question_" + position + "_error").removeClass("show").addClass("hide").html("");
            }
        }
        if (max_value) {
            if (Number(max_value) < Number(value)) {
                $(".question_" + position + "_error").removeClass("hide").addClass("show").html(max_value_msg);
                return;
            } else {
                $(".question_" + position + "_error").removeClass("show").addClass("hide").html("");
            }
        }
    }
}
function clean_up_result_group(position) {
    var group = $(".result_" + position + "_group").html();
    var title_area = $(".result_" + position + "_title_area").html();
    var mini_title_area = $(".result_" + position + "_mini_title_area").html();
    var sub_title_area = $(".result_" + position + "_sub_title_area").html();
    var answer_area = $(".result_" + position + "_answer_area").html();
    var note_area = $(".result_" + position + "_note_area").html();
    if (title_area == '') {
        hide("result_" + position + "_title_area");
    }
    if (mini_title_area == '') {
        hide("result_" + position + "_mini_title_area");
    }
    if (sub_title_area == '') {
        hide("result_" + position + "_sub_title_area");
    }
    if (answer_area == '') {
        hide("result_" + position + "_answer_area");
    }
    if (note_area == '') {
        hide("result_" + position + "_note_area");
    }
}
function set_selection(object, position, answer_factor) {
    $(".question_" + position).val(answer_factor);
    $(".question_" + position + "_mc").removeClass("mcon").addClass("mcoff");
    $(object).removeClass("mcoff").addClass("mcon");
}
function append_to_debug(submission) {
    var texts = $(".dump").html();
    texts = texts + submission + "<br/>";
    $(".dump").html(texts);
}
function append_to_results(submission) {
    var texts = $(".results_content").html();
    texts = texts + submission;
    $(".results_content").html(texts);
}
function append_to_div_if(target_div, content, condition) {
    if (condition === true) {
        $("." + target_div).html(content);
    }
}

function remove_non_numbers(data) {
    data = String(data);
    data.replace(/[^\d.-]/g, '');
    data = Number(data);
    return data;
}

function populate_elements(area, preferred, alternative) {
    if (preferred == '') {
        if (alternative == '') {
            $("." + area).addClass("hide");
        } else {
            $("." + area).html(alternative);
        }
    } else {
        $("." + area).html(preferred);
    }
}