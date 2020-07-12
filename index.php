<?php
//  ------------------------------------------------------------------------ //
//                        Contact Plus Module for                            //
//               XOOPS - PHP Content Management System 2.0                   //
//                            Versión 1.2                                    //
//                   Copyright (c) 2002 Mario Figge                          //
//                       http://www.zona84.com                               //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

include "header.php";
$table =  $xoopsDB->prefix("contactplus_elements");

if ( empty($HTTP_POST_VARS['submit']) ) {
// make form
    $xoopsOption['template_main'] = 'contact_contactusform.html';
    include XOOPS_ROOT_PATH."/header.php";
    include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

    $contact_form = new XoopsThemeForm(_CT_CONTACTFORM, "contactform", "index.php");

    $sql = "SELECT * FROM $table ORDER BY ord";
    $result = $xoopsDB->query($sql);
    $count = 0;

    while ( list($id_element, $type, $caption, $name, $value, $parameter1, $parameter2, $ord, $req) = $xoopsDB->fetchRow($result) ) {
            switch ($type) {
                    case 'textbox':
                          $element = new XoopsFormText($caption, $name, $parameter1, $parameter2, $value);
                          break;
                    case 'textarea':
                          $element = new XoopsFormTextArea($caption, $name, $value, $parameter1, $parameter2 );
                          break;
            }
            $req = ($req == 1) ? true : false;
            $count++;
            $contact_form->addElement($element, $req);
            unset($element);
    }
    $submit_button = new XoopsFormButton("", "submit", _CT_SUBMIT, "submit");
    $contact_form->addElement($submit_button);
    $contact_form->assign($xoopsTpl);
    include XOOPS_ROOT_PATH."/footer.php";
} else {
// send mail
    extract($HTTP_POST_VARS);
    $myts =& MyTextSanitizer::getInstance();

    $sql = "SELECT caption, name FROM $table ORDER BY ord";
    $result = $xoopsDB->query($sql);
    $count = 0;
    $adminMessage = "";

    while ( list($caption, $name) = $xoopsDB->fetchRow($result) ) {
            $elem = $myts->stripSlashesGPC($$name);
            $adminMessage .= $caption.":\n";
            $adminMessage .= $elem."\n";
    }
    $adminMessage .= "\n".$HTTP_SERVER_VARS['HTTP_USER_AGENT']."\n";
    $subject = $xoopsConfig['sitename']." - "._CT_CONTACTFORM;
    $xoopsMailer =& getMailer();
    $xoopsMailer->useMail();
    $xoopsMailer->setToEmails($xoopsConfig['adminmail']);
    $xoopsMailer->setFromEmail($usersEmail);
    $xoopsMailer->setFromName($xoopsConfig['sitename']);
    $xoopsMailer->setSubject($subject);
    $xoopsMailer->setBody($adminMessage);
    $xoopsMailer->send();
    $messagesent = sprintf(_CT_MESSAGESENT,$xoopsConfig['sitename'])."<br />"._CT_THANKYOU.
                   "<br />".$adminMessage;

    // uncomment the following lines if you want to send confirmation mail to the user
    /*
    $conf_subject = _CT_THANKYOU;
    $userMessage = sprintf(_CT_HELLO,$usersName);
    $userMessage .= "\n\n";
    $userMessage .= sprintf(_CT_THANKYOUCOMMENTS,$xoopsConfig['sitename']);
    $userMessage .= "\n";
    $userMessage .= sprintf(_CT_SENTTOWEBMASTER,$xoopsConfig['sitename']);
    $userMessage .= "\n";
    $userMessage .= _CT_YOURMESSAGE."\n";
    $userMessage .= "\n$usersComments\n\n";
    $userMessage .= "--------------\n";
    $userMessage .= "".$xoopsConfig['sitename']." "._CT_WEBMASTER."\n";
    $userMessage .= "".$xoopsConfig['adminmail']."";
    $xoopsMailer =& getMailer();
    $xoopsMailer->useMail();
    $xoopsMailer->setToEmails($usersEmail);
    $xoopsMailer->setFromEmail($usersEmail);
    $xoopsMailer->setFromName($xoopsConfig['sitename']);
    $xoopsMailer->setSubject($conf_subject);
    $xoopsMailer->setBody($userMessage);
    $xoopsMailer->send();
    $messagesent .= sprintf(_CT_SENTASCONFIRM,$usersEmail);
    */

    redirect_header(XOOPS_URL."/index.php",2,$messagesent);
}
?>