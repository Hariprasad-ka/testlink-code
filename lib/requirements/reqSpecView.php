<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * @filesource $RCSfile: reqSpecView.php,v $
 * @version $Revision: 1.8 $
 * @modified $Date: 2008/02/20 21:21:45 $ by $Author: schlundus $
 * @author Martin Havlat
 *
 * Screen to view existing requirements within a req. specification.
 *
 * rev: 20070415 - franciscom - custom field manager
 *      20070415 - franciscom - added reorder feature
 *
**/
require_once("../../config.inc.php");
require_once("common.php");
require_once("users.inc.php");
require_once('requirements.inc.php');
require_once('attachments.inc.php');
require_once('requirement_spec_mgr.class.php');
require_once('requirement_mgr.class.php');
require_once("configCheck.php");

testlinkInitPage($db);

$req_spec_mgr = new requirement_spec_mgr($db);
$req_mgr = new requirement_mgr($db);

$user_feedback='';
$template_dir="requirements/";
$template = 'reqSpecView.tpl';

$_REQUEST = strings_stripSlashes($_REQUEST);
$req_spec_id = isset($_REQUEST['req_spec_id']) ? $_REQUEST['req_spec_id'] : null;
$title = isset($_REQUEST['title']) ? trim($_REQUEST['title']) : null;

$scope = isset($_REQUEST['scope']) ? $_REQUEST['scope'] : null;
$reqStatus = isset($_REQUEST['reqStatus']) ? $_REQUEST['reqStatus'] : TL_REQ_STATUS_VALID;
$reqType = isset($_REQUEST['reqType']) ? $_REQUEST['reqType'] : TL_REQ_TYPE_1;
$countReq = isset($_REQUEST['countReq']) ? intval($_REQUEST['countReq']) : 0;

$tproject_id = isset($_SESSION['testprojectID']) ? $_SESSION['testprojectID'] : 0;
$tproject_name = isset($_SESSION['testprojectName']) ? $_SESSION['testprojectName'] : null;


$req_spec = $req_spec_mgr->get_by_id($req_spec_id);
//SCHLUNDUS: refactoring, moving to class needed, identical code to reqEdit.php, reqSpecEdit.php, reqSpecView.php
$user = tlUser::getByID($db,$req_spec['author_id']);
$req_spec['author'] = null;
if ($user)
	$req_spec['author'] = $user->getDisplayName();
$req_spec['modifier'] = null;
$user = tlUser::getByID($db,$req_spec['modifier_id']);
if ($user)
	$req_spec['modifier'] = $user->getDisplayName();

var_dump($tproject_id);
$cf_smarty = $req_spec_mgr->html_table_of_custom_field_values($req_spec_id,$tproject_id);
$attachments = getAttachmentInfosFrom($req_spec_mgr,$req_spec_id);

$smarty = new TLSmarty();
$smarty->assign('tproject_name',$tproject_name);
$smarty->assign('cf',$cf_smarty);
$smarty->assign('attachments',$attachments);
$smarty->assign('req_spec_id', $req_spec_id);
$smarty->assign('req_spec', $req_spec);
$smarty->assign('name',$title);
$smarty->assign('modify_req_rights', has_rights($db,"mgt_modify_req"));
$smarty->display($template_dir . $template);
?>