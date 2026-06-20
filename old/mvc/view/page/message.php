<?
$message = $data['message'];
// dump($message);
?>

<img src="<?=baseUrl() . $message['source'] ?>" alt="<?= $message['brief'] ?>" class="hero-image">
<br>
<p><?= $message['brief'] ?></p>
<br>
<a href="<?= baseUrl() . $message['url'] ?>"><?= $message['title'] ?></a>