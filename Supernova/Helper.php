<?php

namespace Supernova;

class Helper extends \Supernova\View
{
    /**
     * Create HTML link in the view
     * @param  array  $args Arguments (are href, class, text)
     * @return string       Formed HTML link
     */
    public static function createLink($args = array())
    {
        if (!isset($args['class']) || empty($args['class'])) {
            $args['class'] = array("btn", "btn-default");
        }
        return "<a href='".$args["href"]."' class='".implode(" ", $args['class'])."' >".$args["text"]."</a>";
    }

    /**
     * Format date
     * @param  string $date Date-time
     * @param  array  $args "from" and "to" formats
     * @return string       Formated date-time
     */
    public static function formatDate($date, $args = array())
    {
        $args['from'] = (isset($args['from'])) ? $args['from'] : "Y-m-d h:i:s";
        $args['to'] = (isset($args['to'])) ? $args['to'] : __($args['from']);
        extract(date_parse_from_format($args['from'], $date));

        return date($args['to'], mktime($hour, $minute, $second, $month, $day, $year));
    }
}
