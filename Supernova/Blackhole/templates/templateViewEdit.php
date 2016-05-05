<?php
echo '<?php

$title = inject(__("Edit %name%: %item%"), array(
    "name" => "'.self::$params['singular'].'",
    "item" => $'.self::$params['singular'].'
));
$form = \Supernova\Form::create("'.self::$params['singular'].'", $'.self::$params['singular'].');
$link = \Supernova\Helper::createLink(
    array(
        "href" => \Supernova\Route\Generate::url(
            array(
                "prefix" => \Supernova\Core::$elements["prefix"],
                "controller" => "'.self::$params['plural'].'",
                "action" => "index"
            )
        ),
        "text" => __("<< Back")
    )
);
?>
<h3><?php echo $title; ?></h3>
<?php echo $form->render(); ?>
<?php echo $link; ?>
';
