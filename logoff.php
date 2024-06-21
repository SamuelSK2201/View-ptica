<?
session_start();
session_destroy();
print("<script language='JavaScript'>self.location.href=\"login.php\";</script>");
?>