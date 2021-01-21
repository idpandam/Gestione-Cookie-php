<?php
include('includes/class-cookie.php');


$cookie=new PaCookie('pandam');
$cookie->SetValue('helloworld');
$cookie->SetExpire('18:00');
$cookie->SetSecure(1);
$cookie->CookieSave();

echo $cookie->GetValue();

echo '<hr>';

echo $cookie;

echo '<hr>';

//$cookie->CookieDelete();
?>