<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<script type=\"text/javascript\" src=\"js/debtor.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"js/group.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"js/mailing.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<script type=\"text/javascript\" src=\"3rdparty/ckeditor/ckeditor.js?v=";
echo JSFILE_NOCACHE;
echo "\"></script>\n<div id=\"submenu\">\n\t<ul>\n\t\t<li ";
if(strpos($page, "group") === false && strpos($page, "mailing") === false && strpos($page, "push") === false && !isset($sidebar_show_email_sub)) {
    echo "class=\"active\"";
}
echo "><a href=\"debtors.php\">";
echo __("debtors");
echo "</a></li>\n\t\t<li ";
if(strpos($page, "group") !== false) {
    echo "class=\"active\"";
}
echo "><a href=\"debtors.php?page=groups\">";
echo __("debtorgroups");
echo "</a></li>\n\t\t";
if(U_DEBTOR_EDIT) {
    echo "<li ";
    if(strpos($page, "push") !== false) {
        echo "class=\"active\"";
    }
    echo "><a href=\"debtors.php?page=push\">";
    echo __("push debtors");
    echo "</a></li>";
}
echo "\t</ul>\n</div>\n";
if(U_DEBTOR_EDIT) {
    echo "<div id=\"submenu\">\n\t<ul>\n\t\t<li ";
    if(strpos($page, "mailing") !== false) {
        echo "class=\"active\"";
    }
    echo "><a href=\"debtors.php?page=mailing\">";
    echo __("mailing");
    echo "</a></li>\n\t</ul>\n</div>\n";
}
echo "\n";
if(strpos($page, "mailing") !== false || isset($sidebar_show_email_sub)) {
    echo "<div id=\"submenu\">\n\t<ul>\n\t\t<li ";
    if(isset($sidebar_show_email_sub) && $sidebar_show_email_sub && (int) $selectgroup === 0) {
        echo "class=\"active\"";
    }
    echo "><a href=\"emails.php\">";
    echo __("not sent emails");
    echo "</a></li>\n\t\t<li ";
    if(isset($sidebar_show_email_sub) && $sidebar_show_email_sub && $selectgroup == 1) {
        echo "class=\"active\"";
    }
    echo "><a href=\"emails.php?selectgroup=1\">";
    echo __("sent emails");
    echo "</a></li>\n\t\t<li ";
    if(isset($sidebar_show_email_sub) && $sidebar_show_email_sub && $selectgroup == 8) {
        echo "class=\"active\"";
    }
    echo "><a href=\"emails.php?selectgroup=8\">";
    echo __("emails with errors");
    echo "</a></li>\n\t</ul>\n</div>\n";
}
echo "\n\n";
if(isset($interactions["CountRows"])) {
    echo "\t\n\t<div class=\"hr\" id=\"interaction_divider\"></div>\n\n\t<div class=\"interactioninfo_sidebar\">\n\t\t<h3>";
    echo __("interactions");
    echo "</h3>\n\n\t\t";
    if(0 < $interactions["CountRows"]) {
        $i = 0;
        echo "\t\t\t";
        foreach ($interactions as $interactionID => $interactionitem) {
            if(is_numeric($interactionID)) {
                echo "\t\t\t<a onclick=\"\$('#all_interactions').show(); \$('#new_interaction').hide(); \$('#dialog_interactions').dialog('open');\" class=\"interaction\">\n\t\t\t\t<div class=\"type_";
                echo $interactionitem["Type"];
                echo "\">";
                echo $interaction_type[$interactionitem["Type"]];
                echo "</div>\n\t\t\t\t<div class=\"date\">";
                echo sprintf(date("j %\\s Y", strtotime($interactionitem["Date"])), strtolower($array_months[date("m", strtotime($interactionitem["Date"]))]));
                echo "</div>\n\t\t\t\t<div class=\"message\">";
                echo substr($interactionitem["Message"], 0, 60);
                echo "</div>\n\t\t\t\t<br clear=\"both\" />\n\t\t\t</a>\n\t\t\t";
                if(DEBTOR_INTERACTIONS_SMALL_LIMIT - 1 <= $i) {
                    echo "            <p class=\"more_interactions\">\n                <a class=\"view_more_interactions c5\">\n                    ";
                    echo sprintf(__("show all interactions"), $interactions["CountRows"]);
                    echo "                </a>\n            </p>\n\t\t";
                } else {
                    $i++;
                }
            }
            echo "\t\t\t";
        }
    } else {
        echo "\t\t\t<span style=\"padding-left:19px;\">";
        echo __("no results found");
        echo "</span>\n\t\t";
    }
    echo "\t</div>\n\n\n";
}
echo "\n\n";
if(isset($interactions["CountRows"])) {
    echo "<div id=\"dialog_interactions\" class=\"hide actiondialog\" title=\"";
    echo __("interactions");
    echo " (";
    echo $interactions["CountRows"];
    echo ")\">\n\n\t<div id=\"all_interactions\">\n\n    ";
    if(U_DEBTOR_EDIT) {
        echo "            <a class=\"button1 alt1 pointer\" style=\"margin:10px;\" id=\"new_interaction_btn_dialog\"><span>";
        echo __("add interaction");
        echo "</span></a>\n            ";
    }
    $i = 0;
    $interaction_block_open = false;
    foreach ($interactions as $interactionID => $interactionitem) {
        if(is_numeric($interactionID)) {
            $emp = new employee();
            $emp->show($interactionitem["Author"]);
            if($i % DEBTOR_INTERACTIONS_DIALOG_LIMIT === 0) {
                $interaction_block_open = true;
                echo "<div class=\"interaction_block ";
                if(0 < $i) {
                    echo "hide";
                }
                echo "\">";
            }
            echo "\t\t\t<div class=\"interaction_container ";
            echo $i % 2 === 0 ? "odd" : "";
            echo "\">\n\t\t\t\t<div class=\"interaction_subcontainer\">\n\t\t\t\t\t<div class=\"date\">\n\t\t\t\t\t\t<span class=\"date_day\">";
            echo date("j", strtotime($interactionitem["Date"]));
            echo "</span><br />\n\t\t\t\t\t\t<span class=\"date_month\">";
            echo $array_months_short[date("n", strtotime($interactionitem["Date"]))];
            echo "</span><br />\n\t\t\t\t\t\t<div class=\"interaction_actions\">\n\t\t\t\t\t\t    ";
            if(U_DEBTOR_EDIT) {
                echo "                                    <a class=\"edit_interaction_btn\" data-id=\"";
                echo $interactionID;
                echo "\" title=\"";
                echo __("edit interaction");
                echo "\">\n                                        <span class=\"ico actionblock edit\">&nbsp;</span>\n                                    </a>\n                                    ";
            }
            if(U_DEBTOR_DELETE) {
                echo "                                    <a class=\"delete_interaction_btn\" title=\"";
                echo __("delete interaction");
                echo "\" data-confirm=\"";
                echo __("are you sure you want to delete interaction");
                echo "\" data-id=\"";
                echo $interactionID;
                echo "\">\n                                        <span class=\"ico actionblock trash\">&nbsp;</span>\n                                    </a>\n                                    ";
            }
            echo "\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"details\">\n\t\t\t\t\t\t<div class=\"message\"><div class=\"type_";
            echo $interactionitem["Type"];
            echo "\">";
            echo $interaction_type[$interactionitem["Type"]];
            echo "</div> ";
            echo $interactionitem["Message"];
            echo "</div>\n\t\t\t\t\t\t<div class=\"extended_message\"><div class=\"type_";
            echo $interactionitem["Type"];
            echo "\">";
            echo $interaction_type[$interactionitem["Type"]];
            echo "</div> ";
            echo nl2br($interactionitem["Message"]);
            echo "</div>\n\t\t\t\t\t\t<div class=\"extra\">";
            echo $emp->Name;
            echo " - ";
            echo sprintf(date("j %\\s Y", strtotime($interactionitem["Date"])), strtolower($array_months[date("m", strtotime($interactionitem["Date"]))]));
            echo " ";
            echo __("at");
            echo " ";
            echo date("H:i", strtotime($interactionitem["Date"]));
            echo "\t\t\t\t\t\t\t<span class=\"ico set2 arrowdown float_right\" >";
            echo __("slideDown");
            echo "</span>\n\t\t\t\t\t\t\t<span class=\"ico set2 arrowup float_right\" style=\"display:none;\">";
            echo __("slideUp");
            echo "</span>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t\t";
            $i++;
            if($i % DEBTOR_INTERACTIONS_DIALOG_LIMIT === 0) {
                $interaction_block_open = false;
                echo "</div>";
            }
        }
    }
    if($interaction_block_open) {
        $interaction_block_open = false;
        echo "</div>";
    }
    echo "\n\t\t";
    if(DEBTOR_INTERACTIONS_DIALOG_LIMIT < $i) {
        echo "\t\t<div id=\"dialog_interaction_more\" class=\"interaction_container\">\n\t\t\t<a class=\"a1 c1\" onclick=\"loadMoreInteractions();\">";
        echo __("load more");
        echo "</a>\n\t\t</div>\n\t\t";
    }
    echo "\n\t</div>\n\n\t<div id=\"new_interaction\" class=\"hide\" style=\"margin:10px; padding: 0px 10px;\">\n\n\t\t<div class=\"mark alt3 hide\" id=\"interaction_error\">\n\t\t\t<a class=\"close pointer\">";
    echo __("close");
    echo "</a>\n\t\t\t<strong>\n\t\t\t\t<em style=\"color:#c02e19\">";
    echo __("messagetype error");
    echo "</em>\n\t\t\t</strong>\n\t\t\t<br />\n\t\t\t<ul>\n\t\t\t\t<li></li>\n\t\t\t</ul>\n\t\t</div>\n\n\t\t<form action=\"\" method=\"post\" name=\"InteractionForm\">\n\t\t\t<input type=\"hidden\" name=\"Debtor\" value=\"";
    echo $debtor->Identifier;
    echo "\" />\n\t\t\t<input type=\"hidden\" name=\"InteractionID\" value=\"\" />\n\n\t\t\t<strong class=\"title2\" style=\"line-height: 28px;\">";
    echo __("category");
    echo "</strong>\n\t\t\t<span class=\"title2_value\">\n\t\t\t\t<select class=\"select1 size12\" name=\"Category\">\n\t\t\t\t\t";
    foreach ($interaction_category as $key => $value) {
        if(is_numeric($key)) {
            echo "\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\">";
            echo $value;
            echo "</option>\n\t\t\t\t    ";
        }
    }
    echo "\t\t\t\t</select>\n\t\t\t</span>\n\n\t\t\t<strong class=\"title2\" style=\"line-height: 28px;\">";
    echo __("communication by");
    echo "</strong>\n\t\t\t<span class=\"title2_value\">\n\t\t\t\t<select class=\"select1 size12\" name=\"Type\">\n\t\t\t\t\t";
    foreach ($interaction_type as $k => $v) {
        echo "\t\t\t\t\t<option value=\"";
        echo $k;
        echo "\">";
        echo $v;
        echo "</option>\n\t\t\t\t\t";
    }
    echo "\t\t\t\t</select>\n\t\t\t</span>\n\n\t\t\t<strong class=\"title2\" style=\"line-height: 28px;\">";
    echo __("date and time");
    echo "</strong>\n\t\t\t<span class=\"title2_value\">\n\t\t\t\t<input type=\"text\" class=\"text1 size6 datepicker\" name=\"Date\" value=\"";
    echo rewrite_date_db2site(date("Y-m-d"));
    echo "\"/>\n\t\t\t\t<input type=\"text\" class=\"text1 size3 timechecker\" name=\"Time\" value=\"";
    echo date("H:i");
    echo "\"/>\n\t\t\t</span>\n\n\t\t\t<strong class=\"title2\" style=\"line-height: 28px;\">";
    echo __("author");
    echo "</strong>\n\t\t\t<span class=\"title2_value\">\n\t\t\t\t<select class=\"select1 size12\" name=\"Author\" data-default-author=\"";
    echo $interaction->Author;
    echo "\">\n\t\t\t\t\t";
    foreach ($employees as $key => $value) {
        if(is_numeric($key)) {
            echo "\t\t\t\t\t<option value=\"";
            echo $key;
            echo "\">";
            echo $value["Name"];
            echo "</option>\n\t\t\t\t\t";
        }
    }
    echo "\t\t\t\t</select>\n\t\t\t</span>\n\n\t\t\t<strong class=\"title2\" style=\"line-height: 28px;\">";
    echo __("note");
    echo "</strong>\n\t\t\t<span class=\"title2_value\">\n\t\t\t\t<textarea name=\"Comment\" style=\"width: 545px; height: 260px;\"></textarea>\n\t\t\t</span>\n\n\t\t</form>\n\t\t<br />\n\n\t\t<p id=\"interaction_submit_btn\">\n\t\t\t<a class=\"hide button1 alt1 pointer float_left edit\" id=\"save_interaction_btn\">\n\t\t\t\t<span>";
    echo __("edit interaction");
    echo "</span>\n\t\t\t</a>\n\t\t\t<a class=\"hide button1 alt1 pointer float_left add\" id=\"add_interaction_btn\">\n\t\t\t\t<span>";
    echo __("add interaction");
    echo "</span>\n\t\t\t</a>\n\t\t</p>\n\n\t\t<p style=\"margin-right:6px;line-height:25px;\">\n\t\t\t<a id=\"new_interaction_back\" class=\"a1 c1 float_right\">\n\t\t\t\t";
    echo __("cancel");
    echo "\t\t\t</a>\n\t\t</p>\n\t\t<br class=\"clear\" />\n\n\t</div>\n\n</div>\n";
}
echo "\n\n";
get_dashboard_statistics();

?>