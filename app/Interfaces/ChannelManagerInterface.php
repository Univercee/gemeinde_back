<?php
namespace App\Interfaces;

use phpDocumentor\Reflection\Types\Boolean;

interface ChannelManagerInterface{
    public static function queueLength(): Int;
    public static function sentCount($interval = 0): Int;
    public static function consumeQueue();
    public static function send($identifier, $body, $subject = null, $template = null): Void;
    public static function getQueueTable(): String;
}
?>