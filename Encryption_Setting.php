<?php
//Store the cipher method
	$ciphering ="AES-256-CTR";
    //Use openSSL Encryption method
    $iv_length=openssl_cipher_iv_length($ciphering);
	$options=0;
	//Non-Null intialization vector
	$encryption_iv='1234567891101112';
	$encryption_key="EgyptiansAreGreatAndAmericansAre";
?>