<?php
include("config.php");
// initialize session
session_start();
if(!isset($_SESSION['user'])) {
	// user is not logged in, do something like redirect to login.php
	header("Location: login.php");
	die();
}else {
  $launch_data = array(
    "user_id" => $_SESSION['user'],
    "roles" => "Instructor",
    "resource_link_id" => "120988f929-274612",
    "resource_link_title" => "Gestión de salas",
    "resource_link_description" => "Gestión de salas de Blackboard Collaborate.",
    "lis_person_name_full" => $_SESSION['cn'],
    "lis_person_name_family" => $_SESSION['sn'],
    "lis_person_name_given" => $_SESSION['givenName'],
    "lis_person_contact_email_primary" => $_SESSION['mail'],
    "lis_person_sourcedid" => "school.edu:user",
    "context_id" => $_SESSION['user']."EXTERNAL",
    "context_title" => "Gestión de salas de Blackboard Collaborate",
    "context_label" => "EXTERNAL",
    "tool_consumer_instance_guid" => "school.edu",
    "tool_consumer_instance_description" => "School Name"
  );
  $now = new DateTime();
  $launch_data["lti_version"] = "LTI-1p0";
  $launch_data["lti_message_type"] = "basic-lti-launch-request";
  # Basic LTI uses OAuth to sign requests
  # OAuth Core 1.0 spec: http://oauth.net/core/1.0/
  $launch_data["oauth_callback"] = "about:blank";
  $launch_data["oauth_consumer_key"] = $key;
  $launch_data["oauth_version"] = "1.0";
  $launch_data["oauth_nonce"] = uniqid('', true);
  $launch_data["oauth_timestamp"] = $now->getTimestamp();
  $launch_data["oauth_signature_method"] = "HMAC-SHA1";
  # In OAuth, request parameters must be sorted by name
  $launch_data_keys = array_keys($launch_data);
  sort($launch_data_keys);
  $launch_params = array();
  foreach ($launch_data_keys as $key) {
    array_push($launch_params, $key . "=" . rawurlencode($launch_data[$key]));
  }
  $base_string = "POST&" . urlencode($launch_url) . "&" . rawurlencode(implode("&", $launch_params));
  $secret = urlencode($secret) . "&";
  $signature = base64_encode(hash_hmac("sha1", $base_string, $secret, true));
}
?>

<html>
<head></head>
<body onload="document.ltiLaunchForm.submit();">
  <p>Redirigiendo a Blackboard Collaborate...</p>
<form id="ltiLaunchForm" name="ltiLaunchForm" method="POST" action="<?php printf($launch_url); ?>">
<?php foreach ($launch_data as $k => $v ) { ?>
	<input type="hidden" name="<?php echo $k ?>" value="<?php echo $v ?>">
<?php } ?>
	<input type="hidden" name="oauth_signature" value="<?php echo $signature ?>">
	<button type="submit">Acceder</button>
</form>
<body>
</html>
