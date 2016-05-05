<?php
if (isset(self::$params['primaryKey'])) {
    echo '<?php
$title = inject(__("List from %name%"), array( "name" => "'.self::$params['plural'].'" ));

$table = \Supernova\Table::draw(
    array(
        "values" => $'.self::$params['plural'].',
        "use" => array(
            "created" => "\Supernova\Helper::formatDate",
            "updated" => "\Supernova\Helper::formatDate"
        )
    )
);

$link = \Supernova\Helper::createLink(
    array(
        "href" => \Supernova\Route\Generate::url(
            array(
                "prefix" => \Supernova\Core::$elements["prefix"],
                "controller" => "'.self::$params['plural'].'",
                "action" => "Add"
            )
        ),
        "text" => inject(__("Add %name%"), array( "name" => "'.self::$params['singular'].'" ))
    )
);

?>
<div class="panel panel-default" id="buttons">
    <div class="panel-heading"><?php echo $title; ?></div>
    <div class="panel-body">
        <?php echo $table; ?>
        <?php echo $link; ?>
    </div>
</div>
';
} else {
    echo "<?php
//Fill your code here
";
}
