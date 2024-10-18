<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.4
 * @ Decoder version: 1.0.2
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<tr class=\"match_tr\" data-amount-matched=\"";
if(isset($_candidate->AmountMatched)) {
    echo $_candidate->AmountMatched;
}
echo "\" data-amount-payable=\"";
if(isset($_candidate->AmountPayable)) {
    echo $_candidate->AmountPayable;
}
echo "\" data-paymenttype=\"";
if(isset($_candidate->PaymentType)) {
    echo $_candidate->PaymentType;
}
echo "\" data-reference-type=\"";
if(isset($_candidate->Type)) {
    echo $_candidate->Type;
}
echo "\" data-reference-id=\"";
if(isset($_candidate->ReferenceID)) {
    echo $_candidate->ReferenceID;
}
echo "\" data-reference-status=\"";
if(isset($_candidate->ReferenceStatus)) {
    echo $_candidate->ReferenceStatus;
}
echo "\" style=\"\">\n\n\t<td class=\"match_td_relation\" style=\"text-align:left;border-bottom:  1px solid #eee;padding-left:5px;\" class=\"c4\">\n\t\t";
if(isset($_candidate->Type)) {
    echo "<a href=\"" . ($_candidate->Type == "invoice" ? "debtors.php" : "creditors.php") . "?page=show&amp;id=" . $_candidate->RelationID . "\" class=\"a1\" target=\"_blank\">" . $_candidate->RelationCode . " &nbsp; " . $_candidate->RelationName . "</a>";
}
echo "\t</td>\n\n\t<td class=\"match_td_reference\" style=\"border-bottom: 1px solid #eee;padding-left:50px;\" align=\"right\">\n\t\t";
if(isset($_candidate->ReferencePrefix) && $_candidate->ReferencePrefix) {
    echo "<span class=\"c4 smallfont\">";
    echo $_candidate->ReferencePrefix;
    echo " &nbsp; - &nbsp; </span>";
}
echo "\t\t";
if(isset($_candidate->Type)) {
    echo "<a href=\"" . ($_candidate->Type == "invoice" ? "invoices.php?page=show" : ($_candidate->Type == "batch" ? "directdebit.php?page=show" : "creditors.php?page=show_invoice")) . "&amp;id=" . $_candidate->ReferenceID . "\" class=\"a1\" target=\"_blank\">" . $_candidate->ReferenceCode . "</a>";
}
echo "\t</td>\n\n\n\t<td style=\"border-bottom:  1px solid #eee;\" width=\"20\">&nbsp;</td>\n\t<td style=\"border-bottom:  1px solid #eee;\" width=\"25\">";
echo currency_sign_td(CURRENCY_SIGN_LEFT);
echo "</td>\n\t<td style=\"border-bottom:  1px solid #eee;\" width=\"90\" class=\"letterspacing\" align=\"right\">\n\t\t<input type=\"text\" name=\"\" value=\"";
echo money(isset($_candidate) ? $_candidate->AmountMatched : 0, false);
echo "\" class=\"text1 size6 amount_matched\" style=\"text-align:right;\"/>\n\t\t";
if(CURRENCY_SIGN_RIGHT) {
    echo " " . CURRENCY_SIGN_RIGHT;
}
echo "\t\t<a class=\"remove_match\"><img src=\"images/ico_close.png\" class=\"pointer\"></a></td>\n\t</td>\n\n</tr>";

?>