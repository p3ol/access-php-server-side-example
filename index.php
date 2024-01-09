<?php

/**
 * Simple "api route" that returns an article content ONLY if the Token
 * header can be decrypted (using an RSA-OAEP SHA-1 private key) and the
 * time between the request encryption and the server decryption is less
 * than 500ms.
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    die();
}

header('Content-Type: text/plain; charset=utf-8');

// phpcs:disable Generic.Files.LineLength
$content = "Nullam vitae tellus vel justo iaculis semper sit amet vitae mauris. Vivamus tincidunt bibendum mi vel ullamcorper. Donec suscipit leo laoreet risus aliquam, vitae mattis felis hendrerit. Fusce tincidunt nunc mauris, ut malesuada sem placerat sed. Suspendisse tincidunt elit non elementum porta. Nam ut placerat ipsum, imperdiet vestibulum sem. Aliquam lectus tortor, consequat vitae varius in, iaculis sit amet risus. Sed accumsan ultricies est vel rhoncus. Curabitur eget mi in nisi suscipit efficitur sed sit amet nunc.

Phasellus ipsum lectus, bibendum ac lacus sed, condimentum interdum ipsum. In dictum sem vitae sapien rhoncus, sed mollis mi ultricies. In commodo metus ac condimentum vulputate. Pellentesque sollicitudin posuere mattis. Donec ut eros aliquam sem fringilla mollis et rhoncus lorem. Donec semper placerat pellentesque. Fusce et ullamcorper sem. Vivamus pharetra leo nec interdum semper. Suspendisse vel quam sit amet urna mollis dignissim quis in mi. Mauris pellentesque orci quis aliquam rutrum. Ut ac mollis ligula. Donec in aliquet arcu, non suscipit nulla. In eu lacus at sem finibus porttitor. Suspendisse vel eros ut magna imperdiet vestibulum. Aenean finibus sem eget risus sodales, pharetra pharetra ligula vulputate. Ut fermentum orci sapien, id dapibus nulla elementum ut.

Cras viverra, metus in hendrerit viverra, massa massa porttitor tellus, eget dignissim orci ante sed velit. Suspendisse et auctor turpis, iaculis tristique quam. Etiam mattis scelerisque neque, id vestibulum justo volutpat vel. Sed vel massa ornare, laoreet velit vel, lacinia eros. Nam malesuada, urna quis venenatis pharetra, ex sem porttitor urna, quis euismod risus lectus et leo. Aenean sagittis mi eget est semper placerat. Aenean a venenatis urna, ultrices congue orci. Proin maximus, nisl sed mollis gravida, metus mauris eleifend magna, vitae maximus urna nisi vel nulla. Ut hendrerit congue molestie. Nulla ultricies feugiat risus, quis posuere eros facilisis a. Proin fermentum sed ex id scelerisque. In eros tellus, tincidunt in maximus sed, venenatis eu est. Donec turpis ex, viverra et magna in, congue tincidunt lorem. In eget maximus erat. Pellentesque eu porta elit, eget scelerisque mi.

Praesent molestie suscipit ullamcorper. Duis eleifend cursus leo, quis ultricies dolor volutpat tempus. Maecenas non leo sit amet risus tempus laoreet. Sed lobortis ipsum eu convallis vestibulum. Vivamus nunc nisi, venenatis quis arcu non, pretium congue libero. Donec dapibus felis eget nisi lobortis, a accumsan quam interdum. In cursus malesuada neque, eget congue velit dignissim sed. Nulla quis dictum turpis, sit amet congue arcu. Donec faucibus eros felis, sed maximus massa gravida a. Donec ut arcu sem. Donec a augue quis orci vulputate maximus vitae at velit. Phasellus vehicula sem imperdiet, luctus risus ut, eleifend tortor. Integer porttitor tincidunt eros, at accumsan sem placerat ut.";
// phpcs:enable Generic.Files.LineLength

require "vendor/autoload.php";

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = base64_decode($_SERVER['HTTP_TOKEN']);
$key = "-----BEGIN ENCRYPTED PRIVATE KEY-----\n" .
    implode(PHP_EOL, str_split($_ENV['PRIVATE_KEY'], 64)) .
    "\n-----END ENCRYPTED PRIVATE KEY-----";

openssl_private_decrypt($token, $decrypted, $key, OPENSSL_PKCS1_OAEP_PADDING);

if (floor(microtime(true) * 1000) - $decrypted > 500) {
    http_response_code(401);
    die();
}

echo $content;
